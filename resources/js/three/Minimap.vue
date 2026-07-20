<template>
  <div class="mm">
    <div class="mm-title">План · вид сверху</div>
    <svg :viewBox="`0 0 ${W} ${H}`" class="mm-svg">
      <!-- Комнаты -->
      <g v-for="z in minimap.zones" :key="z.slug">
        <rect
          :x="fx(z.x1)" :y="fz(z.z1)" :width="fx(z.x2) - fx(z.x1)" :height="fz(z.z2) - fz(z.z1)"
          rx="2"
          class="mm-room" :class="{ active: state.activeRoom === z.slug }"
          @click="goRoom(z.slug)"
        />
        <text :x="(fx(z.x1) + fx(z.x2)) / 2" :y="(fz(z.z1) + fz(z.z2)) / 2 + 3" class="mm-label">{{ z.name }}</text>
      </g>

      <!-- Дверь -->
      <rect :x="fx(minimap.door[0]) - 5" :y="fz(minimap.door[1]) - 1.5" width="10" height="3" rx="1.5" class="mm-door" />

      <!-- Пользователь: позиция + направление взгляда -->
      <g :transform="`translate(${fx(state.cam.x)}, ${fz(state.cam.z)})`">
        <path d="M0,-6 L4,4 L0,1.5 L-4,4 Z" class="mm-user" :transform="`rotate(${userDeg})`" />
      </g>
    </svg>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { minimap } from './config/roomsConfig'
import { useTour } from './useTour'

const { state, goRoom } = useTour()

const b = minimap.bounds
const SCALE = 8
const W = computed(() => (b.x2 - b.x1) * SCALE)
const H = computed(() => (b.z2 - b.z1) * SCALE)

const fx = (x) => (x - b.x1) * SCALE
const fz = (z) => (z - b.z1) * SCALE

// азимут камеры (рад) → градусы для стрелки (схематично)
const userDeg = computed(() => (-state.cam.yaw * 180 / Math.PI) + 180)
</script>

<style scoped>
.mm {
  width: 190px;
  padding: 12px;
  border-radius: 16px;
  background: rgba(10,14,26,0.72);
  backdrop-filter: blur(14px);
  border: 1px solid rgba(255,255,255,0.08);
  box-shadow: 0 10px 34px rgba(0,0,0,0.45);
  pointer-events: auto;
}
.mm-title {
  font-size: 9px; font-weight: 700; letter-spacing: .22em; text-transform: uppercase;
  color: rgba(201,169,110,0.75); margin-bottom: 9px;
}
.mm-svg { width: 100%; height: auto; display: block; }
.mm-room {
  fill: rgba(255,255,255,0.04);
  stroke: rgba(255,255,255,0.14);
  stroke-width: 0.6;
  cursor: pointer;
  transition: fill .2s, stroke .2s;
}
.mm-room:hover { fill: rgba(201,169,110,0.14); stroke: rgba(201,169,110,0.5); }
.mm-room.active { fill: rgba(201,169,110,0.22); stroke: #C9A96E; }
.mm-label {
  fill: rgba(255,255,255,0.55); font-size: 5.5px; text-anchor: middle;
  pointer-events: none; font-weight: 600;
}
.mm-door { fill: #C9A96E; }
.mm-user { fill: #C9A96E; stroke: #0C1220; stroke-width: 0.6; }
</style>
