<template>
  <MainLayout>
    <SeoHead
      :title="`${project.name} — ${t('complex.master_title')}`"
      :description="`${t('complex.master_meta')} ${project.name}, ${project.city || ''}`"
      :path="`/complex/${project.slug}`"
    />

    <div class="pt-28 pb-24" style="background:#070B16">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-[11px] uppercase tracking-widest text-white/35 mb-8">
          <Link :href="route('objects')" class="hover:text-gold transition-colors">{{ t('complex.crumb_projects') }}</Link>
          <span class="text-white/20">/</span>
          <span class="text-gold">{{ project.name }}</span>
        </nav>

        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-10">
          <div>
            <div class="inline-flex items-center gap-3 mb-4">
              <span class="block h-px w-8 bg-gold"></span>
              <span class="text-gold/70 text-[9px] font-bold uppercase tracking-[0.55em]">{{ t('complex.master_badge') }}</span>
            </div>
            <h1 class="font-black text-white leading-none" style="font-size:clamp(30px,4vw,56px);letter-spacing:-0.02em">
              {{ project.name }}
            </h1>
            <p v-if="project.address" class="text-white/40 text-sm mt-3 flex items-center gap-2">
              <Icon name="map-pin" class="w-4 h-4 text-gold/70" :stroke-width="2"/>{{ project.address }}
            </p>
          </div>

          <!-- Stats -->
          <div class="flex gap-8">
            <div v-for="s in headStats" :key="s.l" class="text-right">
              <div class="text-gold font-black text-2xl leading-none">{{ s.v }}</div>
              <div class="text-white/35 text-[10px] uppercase tracking-widest mt-1.5">{{ s.l }}</div>
            </div>
          </div>
        </div>

        <!-- Masterplan -->
        <div v-if="project.masterplan" class="rounded-2xl overflow-hidden border border-white/8 relative" style="background:#0A0E1A">
          <InteractiveMap
            :image="project.masterplan"
            :alt="`${project.name} — ${t('complex.master_title')}`"
            :shapes="blockShapes"
            :pois="project.pois"
            @select="goBlock">
            <template #tooltip="{ shape }">
              <template v-if="shape.slug">
                <div class="text-white font-black text-base mb-1.5">{{ shape.name }}</div>
                <div class="space-y-0.5 text-xs text-white/60">
                  <div>{{ shape.floors }} {{ t('complex.floors_word') }}</div>
                  <div>{{ shape.apartments }} {{ t('complex.apts_word') }}</div>
                  <div class="pt-1.5 mt-1.5 border-t border-white/10">
                    <span class="text-emerald-400 font-bold">{{ shape.available }}</span> {{ t('complex.available_word') }}
                    <span class="text-white/30 mx-1">·</span>
                    <span class="text-gold font-bold">{{ shape.sold_percent }}%</span> {{ t('complex.sold_word') }}
                  </div>
                </div>
              </template>
              <template v-else>
                <div class="text-white text-sm font-bold">{{ shape.label }}</div>
                <div class="text-white/40 text-[10px] uppercase tracking-wider mt-0.5">{{ t(`complex.poi_${shape.poiType}`) }}</div>
              </template>
            </template>
          </InteractiveMap>

          <!-- POI legend -->
          <div v-if="poiTypes.length" class="flex flex-wrap gap-4 px-6 py-4 border-t border-white/6">
            <div v-for="pt in poiTypes" :key="pt" class="flex items-center gap-2 text-[10px] uppercase tracking-wider text-white/45">
              <span class="w-2.5 h-2.5 rounded-sm" :style="`background:${POI_COLORS[pt]}`"></span>
              {{ t(`complex.poi_${pt}`) }}
            </div>
          </div>
        </div>

        <!-- Blocks grid (навигация-дублёр + mobile) -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5 mt-10">
          <Link v-for="b in blocks" :key="b.id"
            :href="route('complex.block', [project.slug, b.slug])"
            class="group rounded-2xl p-6 border border-white/6 hover:border-gold/40 transition-all duration-300 hover:-translate-y-0.5"
            style="background:#0C1220">
            <div class="flex items-start justify-between mb-5">
              <div class="text-white font-black text-xl group-hover:text-gold transition-colors">{{ b.name }}</div>
              <div class="w-9 h-9 rounded-full border border-gold/25 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                <Icon name="arrow-right" class="w-4 h-4 text-gold" :stroke-width="2"/>
              </div>
            </div>
            <div class="space-y-1.5 text-xs text-white/45">
              <div class="flex justify-between"><span>{{ t('complex.floors_label') }}</span><span class="text-white font-semibold">{{ b.floors }}</span></div>
              <div class="flex justify-between"><span>{{ t('complex.apts_label') }}</span><span class="text-white font-semibold">{{ b.apartments }}</span></div>
              <div class="flex justify-between"><span>{{ t('complex.available_label') }}</span><span class="text-emerald-400 font-semibold">{{ b.available }}</span></div>
            </div>
            <!-- Sold progress -->
            <div class="mt-4 pt-4 border-t border-white/6">
              <div class="flex justify-between text-[10px] uppercase tracking-wider text-white/35 mb-1.5">
                <span>{{ t('complex.sold_label') }}</span><span class="text-gold font-bold">{{ b.sold_percent }}%</span>
              </div>
              <div class="h-1 rounded-full bg-white/6 overflow-hidden">
                <div class="h-full rounded-full transition-all duration-700" :style="`width:${b.sold_percent}%;background:linear-gradient(90deg,#C9A96E,#D6BA7A)`"></div>
              </div>
            </div>
          </Link>
        </div>

        <div v-if="!blocks.length" class="text-center py-20 text-white/35 text-sm">{{ t('complex.no_blocks') }}</div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'
import Icon from '@/Components/Icon.vue'
import InteractiveMap from '@/Components/Complex/InteractiveMap.vue'
import { useTrans } from '@/composables/useTrans'

const props = defineProps({
  project: { type: Object, required: true },
  blocks:  { type: Array, default: () => [] },
  stats:   { type: Object, default: () => ({}) },
})

const { t } = useTrans()

const POI_COLORS = {
  road: '#475569', parking: '#64748b', playground: '#f59e0b', pool: '#06b6d4',
  leisure: '#ec4899', commercial: '#8b5cf6', green: '#16a34a',
}

const blockShapes = computed(() =>
  props.blocks.filter(b => (b.polygon || []).length >= 3).map(b => ({ ...b, id: b.id })))

const poiTypes = computed(() => [...new Set((props.project.pois || []).map(p => p.type))])

const headStats = computed(() => [
  { v: props.stats.blocks,     l: t('complex.stat_blocks') },
  { v: props.stats.apartments, l: t('complex.stat_apts') },
  { v: props.stats.available,  l: t('complex.stat_available') },
])

function goBlock(shape) {
  if (shape.slug) router.visit(route('complex.block', [props.project.slug, shape.slug]))
}
</script>
