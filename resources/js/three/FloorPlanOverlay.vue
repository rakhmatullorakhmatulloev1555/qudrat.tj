<template>
  <transition name="plan">
    <div v-if="state.planVisible" class="plan" @click.self="togglePlan">
      <div class="plan-card">
        <div class="plan-head">
          <span>Планировка квартиры</span>
          <button class="plan-close" @click="togglePlan" aria-label="Закрыть">✕</button>
        </div>
        <svg :viewBox="`0 0 ${W} ${H}`" class="plan-svg">
          <rect x="0" y="0" :width="W" :height="H" fill="rgba(12,18,32,0.4)" />
          <g v-for="z in minimap.zones" :key="z.slug">
            <rect :x="fx(z.x1)" :y="fz(z.z1)" :width="fx(z.x2)-fx(z.x1)" :height="fz(z.z2)-fz(z.z1)"
              fill="rgba(201,169,110,0.05)" stroke="rgba(201,169,110,0.55)" stroke-width="1.2" />
            <text :x="(fx(z.x1)+fx(z.x2))/2" :y="(fz(z.z1)+fz(z.z2))/2" class="plan-label">{{ z.name }}</text>
            <text :x="(fx(z.x1)+fx(z.x2))/2" :y="(fz(z.z1)+fz(z.z2))/2 + 12" class="plan-area">{{ areaBySlug[z.slug] }} м²</text>
          </g>
          <rect :x="fx(minimap.door[0])-9" :y="fz(minimap.door[1])-2.5" width="18" height="5" rx="2" fill="#C9A96E" />
          <text :x="fx(minimap.door[0])" :y="fz(minimap.door[1])-8" class="plan-door">Вход</text>
        </svg>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { minimap, hotspots } from './config/roomsConfig'
import { useTour } from './useTour'

const { state, togglePlan } = useTour()

const b = minimap.bounds
const SCALE = 34
const W = (b.x2 - b.x1) * SCALE
const H = (b.z2 - b.z1) * SCALE
const fx = (x) => (x - b.x1) * SCALE
const fz = (z) => (z - b.z1) * SCALE
const areaBySlug = Object.fromEntries(hotspots.map(h => [h.slug, h.area]))
</script>

<style scoped>
.plan {
  position: absolute; inset: 0; z-index: 60;
  display: flex; align-items: center; justify-content: center; padding: 24px;
  background: rgba(5,7,13,0.72); backdrop-filter: blur(6px); pointer-events: auto;
}
.plan-card {
  background: rgba(10,14,26,0.92); border: 1px solid rgba(255,255,255,0.1);
  border-radius: 20px; padding: 20px; max-width: 560px; width: 100%;
  box-shadow: 0 24px 70px rgba(0,0,0,0.6);
}
.plan-head {
  display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;
  color: #fff; font-weight: 700; font-size: 14px;
}
.plan-close { color: rgba(255,255,255,0.5); font-size: 16px; cursor: pointer; }
.plan-close:hover { color: #fff; }
.plan-svg { width: 100%; height: auto; border-radius: 12px; }
.plan-label { fill: #fff; font-size: 15px; text-anchor: middle; font-weight: 600; }
.plan-area { fill: rgba(201,169,110,0.9); font-size: 12px; text-anchor: middle; }
.plan-door { fill: #C9A96E; font-size: 12px; text-anchor: middle; font-weight: 700; }

.plan-enter-active, .plan-leave-active { transition: opacity .3s ease; }
.plan-enter-from, .plan-leave-to { opacity: 0; }
</style>
