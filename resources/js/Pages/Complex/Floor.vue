<template>
  <MainLayout>
    <SeoHead
      :title="`${project.name} — ${block.name}, ${t('complex.floor_word')} ${floor.number}`"
      :description="`${t('complex.floor_meta')} ${floor.number}, ${block.name}, ${project.name}`"
      :path="`/complex/${project.slug}/${block.slug}/floor-${floor.number}`"
    />

    <div class="pt-28 pb-24" style="background:#070B16">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-[11px] uppercase tracking-widest text-white/35 mb-8 flex-wrap">
          <Link :href="route('objects')" class="hover:text-gold transition-colors">{{ t('complex.crumb_projects') }}</Link>
          <span class="text-white/20">/</span>
          <Link :href="route('complex.master', project.slug)" class="hover:text-gold transition-colors">{{ project.name }}</Link>
          <span class="text-white/20">/</span>
          <Link :href="route('complex.block', [project.slug, block.slug])" class="hover:text-gold transition-colors">{{ block.name }}</Link>
          <span class="text-white/20">/</span>
          <span class="text-gold">{{ t('complex.floor_word') }} {{ floor.number }}</span>
        </nav>

        <!-- Header + floor switcher -->
        <div class="flex flex-wrap items-end justify-between gap-6 mb-10">
          <div>
            <div class="inline-flex items-center gap-3 mb-4">
              <span class="block h-px w-8 bg-gold"></span>
              <span class="text-gold/70 text-[9px] font-bold uppercase tracking-[0.55em]">{{ t('complex.floor_badge') }}</span>
            </div>
            <h1 class="font-black text-white leading-none" style="font-size:clamp(30px,4vw,56px);letter-spacing:-0.02em">
              {{ t('complex.floor_word') }} {{ floor.number }}
            </h1>
          </div>

          <div class="flex items-center gap-2">
            <Link v-if="floor.number > 1"
              :href="route('complex.floor', [project.slug, block.slug, floor.number - 1])"
              class="px-4 py-2.5 rounded-xl border border-white/10 text-white/50 hover:text-gold hover:border-gold/40 text-xs font-bold transition-all">
              ↓ {{ floor.number - 1 }}
            </Link>
            <span class="px-5 py-2.5 rounded-xl bg-gold text-dark text-xs font-black">{{ floor.number }}</span>
            <Link v-if="floor.number < block.floorsTotal"
              :href="route('complex.floor', [project.slug, block.slug, floor.number + 1])"
              class="px-4 py-2.5 rounded-xl border border-white/10 text-white/50 hover:text-gold hover:border-gold/40 text-xs font-bold transition-all">
              ↑ {{ floor.number + 1 }}
            </Link>
          </div>
        </div>

        <div class="grid lg:grid-cols-[1fr_360px] gap-8 items-start">

          <!-- Floor plan -->
          <div>
            <div v-if="floor.plan" class="rounded-2xl overflow-hidden border border-white/8" style="background:#0A0E1A">
              <InteractiveMap
                :image="floor.plan"
                :alt="`${block.name} — ${t('complex.floor_word')} ${floor.number}`"
                :shapes="aptShapes"
                :baseOpacity="0.22"
                :hoverOpacity="0.5"
                @select="goApartment">
                <template #tooltip="{ shape }">
                  <div class="text-white font-black text-base mb-1.5">№ {{ shape.number }}</div>
                  <div class="space-y-0.5 text-xs text-white/60">
                    <div>{{ shape.rooms }} {{ t('complex.rooms_word') }} · {{ shape.area }} м²</div>
                    <div class="pt-1.5 mt-1.5 border-t border-white/10 flex items-center justify-between gap-4">
                      <span class="font-bold" :style="`color:${STATUS_COLORS[shape.status]}`">{{ statusLabel(shape.status) }}</span>
                      <span v-if="shape.status === 'available'" class="text-gold font-bold">${{ fmt(shape.price) }}</span>
                    </div>
                  </div>
                </template>
              </InteractiveMap>
            </div>
            <div v-else class="rounded-2xl border border-white/8 flex items-center justify-center py-32 text-white/30 text-sm" style="background:#0A0E1A">
              {{ t('complex.no_plan') }}
            </div>

            <!-- Legend -->
            <div class="flex flex-wrap gap-5 mt-5 px-1">
              <div v-for="(color, st) in STATUS_COLORS" :key="st" class="flex items-center gap-2 text-[10px] uppercase tracking-wider text-white/45">
                <span class="w-2.5 h-2.5 rounded-full" :style="`background:${color}`"></span>
                {{ statusLabel(st) }}
              </div>
            </div>
          </div>

          <!-- Apartments list -->
          <aside class="rounded-2xl border border-white/8 overflow-hidden lg:sticky lg:top-24" style="background:#0C1220">
            <div class="px-5 py-4 border-b border-white/6 flex items-center justify-between">
              <span class="text-white text-xs font-bold uppercase tracking-[0.25em]">{{ t('complex.apts_on_floor') }}</span>
              <span class="text-white/35 text-xs">{{ apartments.length }}</span>
            </div>
            <div class="max-h-[600px] overflow-y-auto obj-tabs-scroll">
              <Link v-for="a in apartments" :key="a.id"
                :href="route('apartments.show', [project.slug, a.id])"
                class="flex items-center justify-between px-5 py-3.5 border-b border-white/4 hover:bg-gold/8 transition-all group">
                <div class="flex items-center gap-3.5">
                  <span class="w-2 h-2 rounded-full flex-shrink-0" :style="`background:${STATUS_COLORS[a.status]}`"></span>
                  <div>
                    <div class="text-white text-sm font-bold group-hover:text-gold transition-colors">№ {{ a.number }}</div>
                    <div class="text-white/35 text-[11px]">{{ a.rooms }} {{ t('complex.rooms_word') }} · {{ a.area }} м²{{ a.balcony ? ` · ${t('complex.balcony_word')}` : '' }}</div>
                  </div>
                </div>
                <div class="text-right">
                  <div v-if="a.status === 'available'" class="text-gold font-bold text-sm">${{ fmt(a.price) }}</div>
                  <div v-else class="text-[10px] font-bold uppercase tracking-wider" :style="`color:${STATUS_COLORS[a.status]}`">{{ statusLabel(a.status) }}</div>
                </div>
              </Link>
              <div v-if="!apartments.length" class="py-14 text-center text-white/30 text-sm">{{ t('complex.floor_empty') }}</div>
            </div>
          </aside>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'
import InteractiveMap from '@/Components/Complex/InteractiveMap.vue'
import { useTrans } from '@/composables/useTrans'

const props = defineProps({
  project:    { type: Object, required: true },
  block:      { type: Object, required: true },
  floor:      { type: Object, required: true },
  apartments: { type: Array, default: () => [] },
})

const { t } = useTrans()

const STATUS_COLORS = {
  available: '#22c55e',
  reserved:  '#eab308',
  sold:      '#ef4444',
  soon:      '#64748b',
}

const aptShapes = computed(() =>
  props.apartments.filter(a => (a.polygon || []).length >= 3).map(a => ({ ...a })))

function statusLabel(st) {
  return t(`complex.status_${st}`)
}

function fmt(n) {
  return new Intl.NumberFormat('ru-RU').format(Math.round(n))
}

function goApartment(shape) {
  router.visit(route('apartments.show', [props.project.slug, shape.id]))
}
</script>
