<template>
  <Head :title="pageTitle" />

  <div ref="stage" class="tour-stage">
    <!-- Мини-навбар -->
    <div class="tour-nav">
      <Link :href="backUrl" class="tour-back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
        </svg>
        Назад
      </Link>
      <div class="tour-title">
        <span class="tour-badge">3D · Digital Twin</span>
        <span v-if="apartment" class="tour-title-txt">{{ roomLabel(apartment.rooms) }}, {{ apartment.area }} м² · {{ t('objects.apt_abbr') }} {{ apartment.number }}</span>
      </div>
    </div>

    <!-- WebGL не поддерживается -->
    <div v-if="!webglOk" class="tour-fallback">
      <div class="tour-fallback-card">
        <div class="text-3xl mb-3">🖥️</div>
        <h2>3D-просмотр недоступен</h2>
        <p>Ваш браузер или устройство не поддерживает WebGL. Откройте в современном браузере на устройстве с 3D-графикой.</p>
        <Link :href="backUrl" class="tour-fallback-btn">Вернуться к квартире</Link>
      </div>
    </div>

    <template v-else>
      <!-- Премиальный загрузчик -->
      <transition name="fade">
        <div v-if="!state.ready" class="tour-loader">
          <div class="tour-loader-logo">QUDRAT</div>
          <div class="tour-loader-bar"><div class="tour-loader-fill" :style="`width:${displayProgress}%`"></div></div>
          <div class="tour-loader-stage">{{ loaderStage }} · {{ displayProgress }}%</div>
        </div>
      </transition>

      <!-- Сцена -->
      <Suspense>
        <ApartmentScene />
        <template #fallback><div class="tour-noop"></div></template>
      </Suspense>

      <!-- Оверлеи (после готовности) -->
      <transition name="fade">
        <div v-show="state.ready">
          <div class="tour-left"><RoomPanel /><Minimap /></div>
          <div class="tour-right"><InfoPanel :apartment="apartment" @book="reserveOpen = true" /></div>
          <TourControls @toggle-fullscreen="toggleFullscreen" />
          <FloorPlanOverlay />

          <!-- Мобильная кнопка бронирования -->
          <button v-if="apartment.status === 'available'" class="tour-mobile-cta" @click="reserveOpen = true">
            Забронировать
          </button>
        </div>
      </transition>

      <!-- Подсказка облёта -->
      <transition name="fade">
        <div v-if="state.ready && state.introPlaying" class="tour-intro-hint">
          <span class="tour-intro-dot"></span> Кинематографический облёт · коснитесь, чтобы взять управление
        </div>
      </transition>
    </template>

    <!-- Бронирование (общий компонент) -->
    <ReserveModal v-model="reserveOpen" :apartment="apartment" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, defineAsyncComponent } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import TourControls from '@/three/TourControls.vue'
import RoomPanel from '@/three/RoomPanel.vue'
import Minimap from '@/three/Minimap.vue'
import InfoPanel from '@/three/InfoPanel.vue'
import FloorPlanOverlay from '@/three/FloorPlanOverlay.vue'
import ReserveModal from '@/Components/ReserveModal.vue'
import { useTour, resetTourState } from '@/three/useTour'
import { useApartmentFormat } from '@/composables/useApartmentFormat'
import { useTrans } from '@/composables/useTrans'

const ApartmentScene = defineAsyncComponent(() => import('@/three/ApartmentScene.vue'))

const props = defineProps({
  apartment: { type: Object, default: () => ({ rooms: 1, area: 0, number: '', price: 0, currency: 'USD', status: 'available', floor: 1, ceiling_height: 2.8 }) },
  project:   { type: Object, default: null },
})

const { state } = useTour()
const { roomLabel } = useApartmentFormat()
const { t } = useTrans()

const stage = ref(null)
const webglOk = ref(true)
const reserveOpen = ref(false)

// Загрузчик: этапы + анимация прогресса до готовности сцены
const displayProgress = ref(0)
const loaderStages = ['Загрузка окружения (HDRI)', 'Загрузка текстур', 'Загрузка мебели', 'Настройка освещения', 'Запуск сцены']
const loaderStage = ref(loaderStages[0])
let progTimer = null

const pageTitle = computed(() => props.apartment
  ? `3D · ${roomLabel(props.apartment.rooms)}, ${props.apartment.area} м² — QUDRAT` : '3D-просмотр — QUDRAT')
const backUrl = computed(() => props.apartment && props.project
  ? `/objects/${props.project.slug}/${props.apartment.id}` : '/objects')

function checkWebGL() {
  try {
    const c = document.createElement('canvas')
    return !!(window.WebGLRenderingContext && (c.getContext('webgl2') || c.getContext('webgl')))
  } catch (e) { return false }
}

function toggleFullscreen() {
  const el = stage.value
  if (!document.fullscreenElement) el?.requestFullscreen?.().catch(() => {})
  else document.exitFullscreen?.().catch(() => {})
}

function animateLoader() {
  let stageIdx = 0
  progTimer = setInterval(() => {
    if (state.ready) { displayProgress.value = 100; clearInterval(progTimer); return }
    if (displayProgress.value < 92) displayProgress.value += Math.random() * 7
    if (displayProgress.value > 92) displayProgress.value = 92
    const idx = Math.min(loaderStages.length - 1, Math.floor(displayProgress.value / 20))
    if (idx !== stageIdx) { stageIdx = idx; loaderStage.value = loaderStages[idx] }
  }, 260)
}

let readyFallback = null
onMounted(() => {
  resetTourState()
  webglOk.value = checkWebGL()
  if (webglOk.value) {
    animateLoader()
    // Защита от «вечного загрузчика»: если сцена не сообщила о готовности
    // за 6 сек (напр. не зарегистрировались OrbitControls) — снимаем загрузчик,
    // чтобы пользователь увидел сцену/интерфейс, а не бесконечную загрузку.
    readyFallback = setTimeout(() => { if (!state.ready) state.ready = true }, 6000)
  }
})
onBeforeUnmount(() => {
  if (progTimer) clearInterval(progTimer)
  if (readyFallback) clearTimeout(readyFallback)
  if (document.fullscreenElement) document.exitFullscreen?.().catch(() => {})
})
</script>

<style scoped>
.tour-stage { position: relative; width: 100%; height: 100vh; height: 100dvh; background: #0B0F17; overflow: hidden; }
.tour-stage :deep(canvas) { display: block; width: 100% !important; height: 100% !important; }
.tour-noop { display: none; }

.tour-nav {
  position: absolute; top: 0; left: 0; right: 0; z-index: 40;
  display: flex; align-items: center; gap: 18px; padding: 18px 22px;
  background: linear-gradient(to bottom, rgba(7,11,22,0.85), transparent); pointer-events: none;
}
.tour-back {
  pointer-events: auto; display: inline-flex; align-items: center; gap: 7px;
  color: rgba(255,255,255,0.8); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .12em;
  padding: 9px 14px; border-radius: 11px; background: rgba(12,18,32,0.6); backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.1); transition: all .2s;
}
.tour-back:hover { color: #C9A96E; border-color: rgba(201,169,110,0.4); }
.tour-title { display: flex; align-items: center; gap: 10px; color: rgba(255,255,255,0.55); font-size: 12px; min-width: 0; }
.tour-badge {
  font-size: 9px; font-weight: 800; letter-spacing: .22em; text-transform: uppercase; color: #C9A96E;
  padding: 4px 9px; border-radius: 6px; border: 1px solid rgba(201,169,110,0.3); background: rgba(201,169,110,0.08); white-space: nowrap;
}
.tour-title-txt { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

/* Панели */
.tour-left { position: absolute; left: 20px; top: 88px; z-index: 30; display: flex; flex-direction: column; gap: 12px; pointer-events: none; }
.tour-right { position: absolute; right: 20px; top: 88px; z-index: 30; pointer-events: none; }

/* Загрузчик */
.tour-loader {
  position: absolute; inset: 0; z-index: 50;
  display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 20px;
  background: radial-gradient(ellipse at center, #0d1424, #060810);
}
.tour-loader-logo {
  font-family: 'Cormorant Garamond', Georgia, serif; font-size: 42px; font-weight: 700;
  letter-spacing: .3em; color: #C9A96E; padding-left: .3em;
}
.tour-loader-bar { width: 240px; height: 3px; border-radius: 3px; background: rgba(255,255,255,0.08); overflow: hidden; }
.tour-loader-fill { height: 100%; background: linear-gradient(90deg, #B8935A, #E8C888); transition: width .3s ease; }
.tour-loader-stage { color: rgba(255,255,255,0.45); font-size: 11px; letter-spacing: .05em; }

.tour-fallback { position: absolute; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; }
.tour-fallback-card { max-width: 420px; text-align: center; padding: 34px; border-radius: 20px; background: rgba(12,18,32,0.85); border: 1px solid rgba(255,255,255,0.08); color: #fff; }
.tour-fallback-card h2 { font-size: 20px; font-weight: 700; margin-bottom: 10px; }
.tour-fallback-card p { color: rgba(255,255,255,0.5); font-size: 14px; line-height: 1.6; margin-bottom: 20px; }
.tour-fallback-btn { display: inline-block; padding: 11px 22px; border-radius: 12px; background: #C9A96E; color: #0C1220; font-weight: 700; font-size: 13px; text-transform: uppercase; letter-spacing: .1em; }

.tour-intro-hint {
  position: absolute; top: 74px; left: 50%; transform: translateX(-50%); z-index: 35;
  display: inline-flex; align-items: center; gap: 9px; padding: 9px 16px; border-radius: 999px;
  background: rgba(10,14,26,0.7); backdrop-filter: blur(10px); border: 1px solid rgba(201,169,110,0.25);
  color: rgba(255,255,255,0.7); font-size: 11px; pointer-events: none; white-space: nowrap;
}
.tour-intro-dot { width: 7px; height: 7px; border-radius: 50%; background: #C9A96E; animation: pulse 1.4s ease-in-out infinite; }
@keyframes pulse { 0%,100%{opacity:.4;transform:scale(0.8)} 50%{opacity:1;transform:scale(1.2)} }

.tour-mobile-cta {
  display: none; position: absolute; z-index: 36; right: 16px; bottom: 96px;
  background: #C9A96E; color: #0C1220; font-weight: 700; font-size: 12px; text-transform: uppercase; letter-spacing: .08em;
  padding: 12px 18px; border-radius: 12px; box-shadow: 0 8px 26px rgba(201,169,110,0.35); pointer-events: auto;
}

.fade-enter-active, .fade-leave-active { transition: opacity .5s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

/* Планшет/мобайл: боковые панели убираем, оставляем нижние контролы + мобильную CTA */
@media (max-width: 1024px) {
  .tour-left, .tour-right { display: none; }
  .tour-mobile-cta { display: block; }
}
</style>
