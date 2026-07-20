<template>
  <MainLayout>
    <SeoHead
      :title="`${project.name} — ${block.name}`"
      :description="`${block.name}, ${project.name}: ${block.floorsTotal} ${t('complex.floors_word')}. ${t('complex.block_meta')}`"
      :path="`/complex/${project.slug}/${block.slug}`"
    />

    <div class="pt-28 pb-24" style="background:#070B16">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-[11px] uppercase tracking-widest text-white/35 mb-8 flex-wrap">
          <Link :href="route('objects')" class="hover:text-gold transition-colors">{{ t('complex.crumb_projects') }}</Link>
          <span class="text-white/20">/</span>
          <Link :href="route('complex.master', project.slug)" class="hover:text-gold transition-colors">{{ project.name }}</Link>
          <span class="text-white/20">/</span>
          <span class="text-gold">{{ block.name }}</span>
        </nav>

        <!-- Header -->
        <div class="mb-10">
          <div class="inline-flex items-center gap-3 mb-4">
            <span class="block h-px w-8 bg-gold"></span>
            <span class="text-gold/70 text-[9px] font-bold uppercase tracking-[0.55em]">{{ t('complex.block_badge') }}</span>
          </div>
          <h1 class="font-black text-white leading-none" style="font-size:clamp(30px,4vw,56px);letter-spacing:-0.02em">
            {{ block.name }}
          </h1>
          <p v-if="block.description" class="text-white/45 text-sm mt-4 max-w-2xl leading-relaxed">{{ block.description }}</p>
        </div>

        <div class="grid lg:grid-cols-[1fr_340px] gap-8 items-start">

          <!-- Facade with clickable floors -->
          <div v-if="block.facade" class="rounded-2xl overflow-hidden border border-white/8" style="background:#0A0E1A">
            <InteractiveMap
              :image="block.facade"
              :alt="`${block.name} — ${t('complex.facade_alt')}`"
              :shapes="floorShapes"
              :baseOpacity="0.05"
              :hoverOpacity="0.35"
              @select="goFloor">
              <template #tooltip="{ shape }">
                <div class="text-white font-black text-base mb-1">{{ t('complex.floor_word') }} {{ shape.number }}</div>
                <div class="text-xs" :class="shape.available > 0 ? 'text-emerald-400' : 'text-white/40'">
                  {{ shape.available > 0 ? `${shape.available} ${t('complex.available_word')}` : t('complex.floor_empty') }}
                </div>
              </template>
            </InteractiveMap>
          </div>
          <div v-else class="rounded-2xl border border-white/8 flex items-center justify-center py-32 text-white/30 text-sm" style="background:#0A0E1A">
            {{ t('complex.no_facade') }}
          </div>

          <!-- Floor list (n..1) -->
          <aside class="rounded-2xl border border-white/8 overflow-hidden sticky top-24" style="background:#0C1220">
            <div class="px-5 py-4 border-b border-white/6">
              <span class="text-white text-xs font-bold uppercase tracking-[0.25em]">{{ t('complex.choose_floor') }}</span>
            </div>
            <div class="max-h-[560px] overflow-y-auto obj-tabs-scroll">
              <component v-for="f in floors" :key="f.number"
                :is="f.total > 0 ? Link : 'div'"
                :href="f.total > 0 ? route('complex.floor', [project.slug, block.slug, f.number]) : undefined"
                class="flex items-center justify-between px-5 py-3 border-b border-white/4 transition-all"
                :class="f.total > 0
                  ? 'hover:bg-gold/8 cursor-pointer group'
                  : 'opacity-40 cursor-default'">
                <div class="flex items-center gap-4">
                  <span class="w-9 text-gold font-black text-lg leading-none">{{ f.number }}</span>
                  <span class="text-white/50 text-xs group-hover:text-white transition-colors">
                    {{ t('complex.floor_word') }}
                  </span>
                </div>
                <div class="text-right">
                  <span v-if="f.total > 0" class="text-[11px]" :class="f.available > 0 ? 'text-emerald-400 font-semibold' : 'text-white/30'">
                    {{ f.available > 0 ? `${f.available} ${t('complex.available_short')}` : t('complex.sold_out') }}
                  </span>
                  <span v-else class="text-[11px] text-white/25">—</span>
                </div>
              </component>
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
  project: { type: Object, required: true },
  block:   { type: Object, required: true },
  floors:  { type: Array, default: () => [] }, // сверху вниз (n..1)
})

const { t } = useTrans()

const floorShapes = computed(() =>
  props.floors
    .filter(f => (f.polygon || []).length >= 3 && f.total > 0)
    .map(f => ({ ...f, id: f.number, color: f.available > 0 ? '#C9A96E' : '#64748b' })))

function goFloor(shape) {
  router.visit(route('complex.floor', [props.project.slug, props.block.slug, shape.number]))
}
</script>
