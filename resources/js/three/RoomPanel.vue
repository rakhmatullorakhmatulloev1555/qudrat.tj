<template>
  <div class="rp">
    <div class="rp-title">Комнаты</div>
    <button v-for="r in items" :key="r.id"
      class="rp-item" :class="{ active: state.activeRoom === r.id }"
      @click="goRoom(r.id)">
      <span class="rp-icon">{{ r.icon }}</span>
      <span class="rp-name">{{ r.name }}</span>
      <span v-if="r.area" class="rp-area">{{ r.area }} м²</span>
    </button>
  </div>
</template>

<script setup>
import { rooms, hotspots } from './config/roomsConfig'
import { useTour } from './useTour'

const { state, goRoom } = useTour()

const areaBySlug = Object.fromEntries(hotspots.map(h => [h.slug, h.area]))
const items = rooms.map(r => ({ ...r, area: areaBySlug[r.id] }))
</script>

<style scoped>
.rp {
  width: 190px;
  padding: 12px;
  border-radius: 16px;
  background: rgba(10,14,26,0.72);
  backdrop-filter: blur(14px);
  border: 1px solid rgba(255,255,255,0.08);
  box-shadow: 0 10px 34px rgba(0,0,0,0.45);
  pointer-events: auto;
}
.rp-title {
  font-size: 9px; font-weight: 700; letter-spacing: .22em; text-transform: uppercase;
  color: rgba(201,169,110,0.75); margin-bottom: 9px; padding-left: 4px;
}
.rp-item {
  display: flex; align-items: center; gap: 10px; width: 100%;
  padding: 9px 11px; border-radius: 11px; cursor: pointer; margin-bottom: 3px;
  color: rgba(255,255,255,0.6); font-size: 13px; font-weight: 500;
  background: transparent; border: 1px solid transparent; transition: all .2s;
}
.rp-item:hover { color: #fff; background: rgba(255,255,255,0.04); }
.rp-item.active { color: #0C1220; background: #C9A96E; }
.rp-icon { font-size: 15px; line-height: 1; }
.rp-name { flex: 1; text-align: left; }
.rp-area { font-size: 10px; opacity: 0.7; }
</style>
