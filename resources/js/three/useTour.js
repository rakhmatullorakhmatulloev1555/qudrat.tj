/**
 * useTour — общее реактивное состояние 3D-тура.
 * Связывает DOM-панели (вне canvas: панель комнат, миникарта, инфо-панель)
 * со сценой (внутри canvas) без прямой связи компонентов.
 */
import { reactive } from 'vue'
import { defaultStyleId } from './config/interiorStyles'
import { defaultTimeId } from './config/timeOfDay'

const state = reactive({
  styleId: defaultStyleId,
  timeId: defaultTimeId,
  activeRoom: '',
  introPlaying: true,
  planVisible: false,
  // прогресс инициализации сцены (для загрузчика) 0..100
  progress: 0,
  ready: false,
  // живое положение камеры для миникарты: мировые X,Z и азимут (рад)
  cam: { x: 8, z: 8, yaw: 0 },
  // одноразовые команды камере (reset / intro)
  cmd: 0,
  cmdName: '',
})

export function resetTourState() {
  state.styleId = defaultStyleId
  state.timeId = defaultTimeId
  state.activeRoom = ''
  state.introPlaying = true
  state.planVisible = false
  state.progress = 0
  state.ready = false
  state.cam = { x: 8, z: 8, yaw: 0 }
  state.cmd = 0
  state.cmdName = ''
}

export function useTour() {
  return {
    state,
    setStyle: (id) => { state.styleId = id },
    setTime:  (id) => { state.timeId = id },
    goRoom:   (id) => { state.activeRoom = id },
    togglePlan: () => { state.planVisible = !state.planVisible },
    reset:    () => { state.activeRoom = ''; state.cmdName = 'reset'; state.cmd++ },
    replay:   () => { state.cmdName = 'intro'; state.cmd++ },
    setIntroPlaying: (v) => { state.introPlaying = v },
    setProgress: (v) => { state.progress = Math.round(v) },
    setReady: (v) => { state.ready = v },
    setCam:   (x, z, yaw) => { state.cam.x = x; state.cam.z = z; state.cam.yaw = yaw },
  }
}
