<template>
  <div ref="wrap" class="relative select-none" @mousemove="onMove" @mouseleave="hovered = null">
    <!-- Base image -->
    <img :src="asset(image)" :alt="alt" class="w-full h-auto block rounded-2xl" draggable="false"/>

    <!-- SVG overlay: полигоны в нормализованных координатах 0-1000 × 0-1000 -->
    <svg class="absolute inset-0 w-full h-full" viewBox="0 0 1000 1000" preserveAspectRatio="none">
      <!-- Декоративные зоны (POI) — не кликабельны -->
      <polygon v-for="(poi, i) in pois" :key="'poi' + i"
        :points="pts(poi.polygon)"
        :fill="rgba(poiColor(poi.type), hovered === `poi${i}` ? 0.35 : 0.16)"
        :stroke="rgba(poiColor(poi.type), 0.55)"
        stroke-width="1.2"
        style="transition:fill .3s ease"
        @mouseenter="hovered = `poi${i}`; tipShape = { label: poi.label, poiType: poi.type }"
      />

      <!-- Интерактивные объекты -->
      <polygon v-for="s in shapes" :key="s.id"
        :points="pts(s.polygon)"
        :fill="rgba(shapeColor(s), hovered === s.id ? hoverOpacity : baseOpacity)"
        :stroke="rgba(shapeColor(s), hovered === s.id ? 1 : 0.6)"
        :stroke-width="hovered === s.id ? 2.5 : 1.4"
        class="cursor-pointer"
        :style="`transition: fill .3s ease, stroke .3s ease; ${hovered === s.id ? `filter: drop-shadow(0 0 14px ${rgba(shapeColor(s), 0.85)})` : ''}`"
        @mouseenter="hovered = s.id; tipShape = s"
        @click="$emit('select', s)"
      />
    </svg>

    <!-- Tooltip -->
    <Transition name="tip">
      <div v-if="hovered !== null && tipShape"
        class="absolute z-20 pointer-events-none rounded-xl px-4 py-3 shadow-2xl border"
        :style="`left:${tip.x}px; top:${tip.y}px; background:rgba(7,11,22,0.94); border-color:rgba(201,169,110,0.25); backdrop-filter:blur(8px); min-width:150px; transform:translate(${tip.flipX ? '-100%' : '12px'}, ${tip.flipY ? '-100%' : '14px'})`">
        <slot name="tooltip" :shape="tipShape">
          <div class="text-white text-sm font-bold">{{ tipShape.label || tipShape.name }}</div>
        </slot>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useAsset } from '@/composables/useAsset'

const props = defineProps({
  image:  { type: String, required: true },
  alt:    { type: String, default: '' },
  // [{id, polygon: [[x,y],...], status?, color?, ...}] — координаты 0-1000
  shapes: { type: Array, default: () => [] },
  // Декоративные зоны: [{type, label, polygon}]
  pois:   { type: Array, default: () => [] },
  baseOpacity:  { type: Number, default: 0.14 },
  hoverOpacity: { type: Number, default: 0.42 },
  // Цвет по умолчанию (когда у shape нет status/color)
  defaultColor: { type: String, default: '#C9A96E' },
})

defineEmits(['select'])

const { asset } = useAsset()

const wrap    = ref(null)
const hovered = ref(null)
const tipShape = ref(null)
const tip = reactive({ x: 0, y: 0, flipX: false, flipY: false })

function onMove(e) {
  const rect = wrap.value?.getBoundingClientRect()
  if (!rect) return
  tip.x = e.clientX - rect.left
  tip.y = e.clientY - rect.top
  tip.flipX = tip.x > rect.width * 0.62
  tip.flipY = tip.y > rect.height * 0.7
}

function pts(polygon) {
  return (polygon || []).map(p => `${p[0]},${p[1]}`).join(' ')
}

// ── Цвета ──

const STATUS_COLORS = {
  available: '#22c55e', // Свободна — зелёный
  reserved:  '#eab308', // Забронирована — жёлтый
  sold:      '#ef4444', // Продана — красный
  soon:      '#64748b', // Скоро — серый
}

const POI_COLORS = {
  road:       '#475569',
  parking:    '#64748b',
  playground: '#f59e0b',
  pool:       '#06b6d4',
  leisure:    '#ec4899',
  commercial: '#8b5cf6',
  green:      '#16a34a',
}

function shapeColor(s) {
  return s.color || STATUS_COLORS[s.status] || props.defaultColor
}

function poiColor(type) {
  return POI_COLORS[type] || '#64748b'
}

function rgba(hex, alpha) {
  const h = (hex || '#C9A96E').replace('#', '')
  const full = h.length === 3 ? h.split('').map(c => c + c).join('') : h
  const n = parseInt(full, 16)
  if (Number.isNaN(n)) return `rgba(201,169,110,${alpha})`
  return `rgba(${(n >> 16) & 255},${(n >> 8) & 255},${n & 255},${alpha})`
}
</script>

<style scoped>
.tip-enter-active, .tip-leave-active { transition: opacity .2s ease, transform .2s ease; }
.tip-enter-from, .tip-leave-to { opacity: 0; }
</style>
