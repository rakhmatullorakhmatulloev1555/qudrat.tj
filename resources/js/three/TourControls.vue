<template>
  <div class="tour-ui">
    <!-- ── Верх-право: действия ── -->
    <div class="tour-top">
      <button class="tour-icon" title="Повторить облёт" @click="replay">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" width="18" height="18">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 1 1 2.2 5.3M4.5 12V7.5M4.5 12H9"/>
        </svg>
      </button>
      <button class="tour-icon" title="Планировка" :class="{ on: state.planVisible }" @click="togglePlan">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" width="18" height="18">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
        </svg>
      </button>
      <button class="tour-icon" title="Сбросить вид" @click="reset">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" width="18" height="18">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9m11.25-5.25h-4.5m4.5 0v4.5m0-4.5L15 9M3.75 20.25h4.5m-4.5 0v-4.5m0 4.5L9 15m11.25 5.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/>
        </svg>
      </button>
      <button class="tour-icon" title="Во весь экран" @click="$emit('toggle-fullscreen')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" width="18" height="18">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 8.25V4.5h3.75m9 0h3.75v3.75M20.25 15.75v3.75H16.5m-9 0H3.75v-3.75"/>
        </svg>
      </button>
    </div>

    <!-- ── Низ-центр: стили + время суток ── -->
    <div class="tour-bottom">
      <div class="tour-group">
        <span class="tour-group-label">Стиль</span>
        <div class="tour-pills">
          <button v-for="s in styles" :key="s.id"
            class="tour-pill" :class="{ active: state.styleId === s.id }" @click="setStyle(s.id)">
            <span class="tour-dot" :style="`background:${s.accentHex}`"></span>{{ s.name }}
          </button>
        </div>
      </div>

      <span class="tour-sep"></span>

      <div class="tour-group">
        <span class="tour-group-label">Время суток</span>
        <div class="tour-pills">
          <button v-for="ti in times" :key="ti.id"
            class="tour-pill" :class="{ active: state.timeId === ti.id }" @click="setTime(ti.id)">
            <span class="tour-emoji">{{ ti.icon }}</span>{{ ti.name }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { styleList } from './config/interiorStyles'
import { timeList } from './config/timeOfDay'
import { useTour } from './useTour'

defineEmits(['toggle-fullscreen'])

const { state, setStyle, setTime, reset, replay, togglePlan } = useTour()
const styles = styleList()
const times = timeList()
</script>

<style scoped>
.tour-ui { position: absolute; inset: 0; pointer-events: none; z-index: 30; }
.tour-top { position: absolute; top: 88px; right: 20px; display: flex; gap: 10px; pointer-events: auto; }
.tour-icon {
  width: 42px; height: 42px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  color: rgba(255,255,255,0.75);
  background: rgba(12,18,32,0.72); backdrop-filter: blur(12px);
  border: 1px solid rgba(255,255,255,0.1); transition: all .2s; cursor: pointer;
}
.tour-icon:hover { color: #C9A96E; border-color: rgba(201,169,110,0.4); background: rgba(12,18,32,0.9); }
.tour-icon.on { color: #C9A96E; border-color: rgba(201,169,110,0.5); background: rgba(201,169,110,0.12); }

.tour-bottom {
  position: absolute; left: 50%; bottom: 26px; transform: translateX(-50%);
  display: flex; align-items: center; gap: 18px; flex-wrap: wrap; justify-content: center;
  max-width: calc(100vw - 32px); padding: 14px 20px; border-radius: 18px;
  background: rgba(10,14,26,0.78); backdrop-filter: blur(18px);
  border: 1px solid rgba(255,255,255,0.08); box-shadow: 0 12px 44px rgba(0,0,0,0.5); pointer-events: auto;
}
.tour-group { display: flex; flex-direction: column; gap: 7px; }
.tour-group-label {
  font-size: 9px; font-weight: 700; letter-spacing: .28em; text-transform: uppercase;
  color: rgba(201,169,110,0.75); padding-left: 4px;
}
.tour-pills { display: flex; gap: 7px; flex-wrap: wrap; }
.tour-pill {
  display: inline-flex; align-items: center; gap: 7px; padding: 8px 13px; border-radius: 11px; cursor: pointer;
  font-size: 12px; font-weight: 600; color: rgba(255,255,255,0.55);
  background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); transition: all .22s; white-space: nowrap;
}
.tour-pill:hover { color: #fff; border-color: rgba(255,255,255,0.2); }
.tour-pill.active { color: #0C1220; background: #C9A96E; border-color: #C9A96E; }
.tour-dot { width: 9px; height: 9px; border-radius: 50%; box-shadow: 0 0 0 2px rgba(255,255,255,0.15); }
.tour-emoji { font-size: 14px; line-height: 1; }
.tour-sep { width: 1px; height: 40px; background: rgba(255,255,255,0.09); }

@media (max-width: 900px) {
  .tour-bottom { gap: 12px; padding: 12px 14px; bottom: 14px; }
  .tour-sep { display: none; }
  .tour-pill { padding: 7px 10px; font-size: 11px; }
  .tour-top { top: 74px; right: 12px; }
}
</style>
