<template>
  <div>
    <!-- Canvas -->
    <div ref="wrap" class="relative select-none rounded-xl overflow-hidden border border-white/10 cursor-crosshair"
      style="background:#0F172A" @click="addPoint">
      <img :src="image" alt="" class="w-full h-auto block pointer-events-none" draggable="false"/>

      <svg class="absolute inset-0 w-full h-full pointer-events-none" viewBox="0 0 1000 1000" preserveAspectRatio="none">
        <!-- Контекст: другие контуры (для ориентира) -->
        <polygon v-for="(ctx, i) in context" :key="'ctx' + i"
          :points="pts(ctx.polygon)"
          fill="rgba(100,116,139,0.14)" stroke="rgba(100,116,139,0.5)" stroke-width="1"/>
        <!-- Редактируемый полигон -->
        <polygon v-if="points.length >= 3" :points="pts(points)"
          fill="rgba(201,169,110,0.25)" stroke="#C9A96E" stroke-width="2"/>
        <polyline v-else-if="points.length >= 2" :points="pts(points)"
          fill="none" stroke="#C9A96E" stroke-width="2" stroke-dasharray="6 4"/>
        <circle v-for="(p, i) in points" :key="'pt' + i"
          :cx="p[0]" :cy="p[1]" :r="i === 0 ? 9 : 6"
          :fill="i === 0 ? '#C9A96E' : '#0F172A'" stroke="#C9A96E" stroke-width="2"/>
      </svg>

      <!-- Подписи контекста -->
      <div v-for="(ctx, i) in context" :key="'lbl' + i"
        v-show="ctx.label && (ctx.polygon || []).length"
        class="absolute text-[9px] font-bold text-slate-400 pointer-events-none px-1 py-0.5 rounded bg-black/50"
        :style="ctxLabelStyle(ctx)">
        {{ ctx.label }}
      </div>
    </div>

    <!-- Toolbar -->
    <div class="flex items-center gap-2 mt-3 flex-wrap">
      <span class="text-slate-500 text-xs mr-auto">
        Кликайте по изображению, чтобы добавить точки контура ({{ points.length }})
      </span>
      <button type="button" @click.stop="undo" :disabled="!points.length"
        class="px-3 py-1.5 rounded-lg text-xs text-slate-300 border border-white/10 hover:border-white/25 disabled:opacity-30 transition-all">
        ← Отменить точку
      </button>
      <button type="button" @click.stop="points = []" :disabled="!points.length"
        class="px-3 py-1.5 rounded-lg text-xs text-red-400/80 border border-red-500/20 hover:bg-red-500/10 disabled:opacity-30 transition-all">
        Очистить
      </button>
      <button type="button" @click.stop="save" :disabled="points.length < 3"
        class="px-4 py-1.5 rounded-lg text-xs font-semibold text-[#0F172A] disabled:opacity-40 transition-all"
        style="background:#C9A96E">
        Сохранить контур
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  image:      { type: String, required: true },
  modelValue: { type: Array, default: () => [] },   // [[x,y],...] в координатах 0-1000
  context:    { type: Array, default: () => [] },   // [{label, polygon}] — другие контуры
})

const emit = defineEmits(['update:modelValue', 'save'])

const wrap   = ref(null)
const points = ref([...(props.modelValue || [])])

watch(() => props.modelValue, v => { points.value = [...(v || [])] })

function addPoint(e) {
  const rect = wrap.value.getBoundingClientRect()
  const x = Math.round(((e.clientX - rect.left) / rect.width) * 1000)
  const y = Math.round(((e.clientY - rect.top) / rect.height) * 1000)
  points.value.push([Math.max(0, Math.min(1000, x)), Math.max(0, Math.min(1000, y))])
  emit('update:modelValue', points.value)
}

function undo() {
  points.value.pop()
  emit('update:modelValue', points.value)
}

function save() {
  emit('save', points.value)
}

function pts(polygon) {
  return (polygon || []).map(p => `${p[0]},${p[1]}`).join(' ')
}

function ctxLabelStyle(ctx) {
  const poly = ctx.polygon || []
  if (!poly.length) return ''
  const cx = poly.reduce((s, p) => s + p[0], 0) / poly.length / 10
  const cy = poly.reduce((s, p) => s + p[1], 0) / poly.length / 10
  return `left:${cx}%;top:${cy}%;transform:translate(-50%,-50%)`
}
</script>
