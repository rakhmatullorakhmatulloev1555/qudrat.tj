<template>
  <div ref="mount" class="scene-mount"></div>
</template>

<script setup>
/**
 * ApartmentScene — процедурная 3D-сцена квартиры на «голом» Three.js.
 * (TresJS в связке Inertia+Vite не отдавал контекст дочерним компонентам,
 *  поэтому используем прямой Three.js — надёжно и полностью под контролем.)
 *
 * Данные берутся из config/* — при появлении реального GLB достаточно
 * заменить buildApartment() на загрузку модели через modelLoader.
 */
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import * as THREE from 'three'
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js'
import { RoomEnvironment } from 'three/examples/jsm/environments/RoomEnvironment.js'
import { gsap } from 'gsap'
import { interiorStyles } from './config/interiorStyles'
import { timesOfDay } from './config/timeOfDay'
import { layout, groundConfig, introSequence, rooms } from './config/roomsConfig'
import { useTour } from './useTour'

const { state, setReady, setCam } = useTour()
const mount = ref(null)

let renderer, scene, camera, controls, raf, ro
let lights = {}
const mats = {}          // materialKey → THREE.MeshStandardMaterial (общие)
let tl = null
let frame = 0
let disposed = false

/* ── Материалы (общие, обновляются при смене стиля) ── */
function styleObj() { return interiorStyles[state.styleId] || Object.values(interiorStyles)[0] }
function applyMaterials() {
  const s = styleObj()
  for (const key in s.materials) {
    const m = s.materials[key]
    let mat = mats[key]
    if (!mat) {
      mat = new THREE.MeshStandardMaterial()
      mats[key] = mat
    }
    mat.color.set(m.color)
    mat.roughness = m.roughness ?? 0.8
    mat.metalness = m.metalness ?? 0.0
    mat.emissive.set(m.emissive || '#000000')
    mat.emissiveIntensity = m.emissiveIntensity ?? 0
    mat.transparent = !!m.transparent
    mat.opacity = m.transparent ? (m.opacity ?? 1) : 1
    mat.needsUpdate = true
  }
}
function mat(key) { return mats[key] || mats.wall }

/* ── Геометрия квартиры (демо-модель) ── */
function buildApartment() {
  const group = new THREE.Group()

  // Пол
  const floor = new THREE.Mesh(new THREE.PlaneGeometry(...groundConfig.floor.size), mat('floor'))
  floor.rotation.x = -Math.PI / 2
  floor.position.y = groundConfig.floor.y
  floor.receiveShadow = true
  group.add(floor)

  // Потолок
  const ceil = new THREE.Mesh(new THREE.PlaneGeometry(...groundConfig.ceiling.size), mat('ceiling'))
  ceil.rotation.x = Math.PI / 2
  ceil.position.y = groundConfig.ceiling.y
  group.add(ceil)

  // Стены
  layout.walls.forEach((w) => {
    const mesh = new THREE.Mesh(new THREE.BoxGeometry(...w.size), mat(w.material))
    mesh.position.set(...w.position)
    mesh.castShadow = true; mesh.receiveShadow = true
    group.add(mesh)
  })
  // Мебель
  layout.furniture.forEach((f) => {
    const mesh = new THREE.Mesh(new THREE.BoxGeometry(...f.size), mat(f.material))
    mesh.position.set(...f.position)
    if (f.rot) mesh.rotation.y = f.rot
    mesh.castShadow = true; mesh.receiveShadow = true
    group.add(mesh)
  })
  return group
}

/* ── Свет (стиль × время суток) ── */
function applyLighting() {
  const l = styleObj().lighting
  const t = timesOfDay[state.timeId] || timesOfDay.day
  lights.ambient.intensity = l.ambient * t.ambientMul
  lights.hemi.intensity = l.hemi * t.hemiMul
  lights.dir.intensity = l.dir * t.dirMul
  lights.dir.color.set(t.dirColor || '#ffffff')
  lights.p1.color.set(l.accentColor); lights.p1.intensity = l.accentIntensity * t.accentMul
  lights.p2.color.set(l.accentColor); lights.p2.intensity = l.accentIntensity * 0.5 * t.accentMul
  if (renderer) renderer.toneMappingExposure = l.exposure * t.exposureMul
  scene.background = new THREE.Color(t.bg || styleObj().background)
}

/* ── Камера: интро / комнаты / сброс ── */
function killTl() { if (tl) { tl.kill(); tl = null } }
function onUserInteract() { killTl(); if (state.introPlaying) state.introPlaying = false }
function playIntro() {
  killTl(); state.introPlaying = true
  tl = gsap.timeline({ onComplete: () => { tl = null; state.introPlaying = false } })
  introSequence.forEach((wp, i) => {
    if (i === 0) { camera.position.set(...wp.pos); controls.target.set(...wp.target); controls.update(); return }
    tl.to(camera.position, { x: wp.pos[0], y: wp.pos[1], z: wp.pos[2], duration: wp.duration, ease: 'power2.inOut' }, i === 1 ? 0 : '>')
      .to(controls.target, { x: wp.target[0], y: wp.target[1], z: wp.target[2], duration: wp.duration, ease: 'power2.inOut', onUpdate: () => controls.update() }, '<')
  })
}
function goToRoom(id) {
  const r = rooms.find(x => x.id === id); if (!r) return
  killTl(); state.introPlaying = false
  gsap.to(camera.position, { x: r.camera.pos[0], y: r.camera.pos[1], z: r.camera.pos[2], duration: 1.5, ease: 'power3.inOut' })
  gsap.to(controls.target, { x: r.camera.target[0], y: r.camera.target[1], z: r.camera.target[2], duration: 1.5, ease: 'power3.inOut', onUpdate: () => controls.update() })
}
function resetView() {
  killTl(); state.introPlaying = false
  gsap.to(camera.position, { x: 8.5, y: 5.5, z: 8.5, duration: 1.3, ease: 'power3.inOut' })
  gsap.to(controls.target, { x: 0, y: 1, z: 0, duration: 1.3, ease: 'power3.inOut', onUpdate: () => controls.update() })
}

/* ── Цикл рендера ── */
function animate() {
  if (disposed) return
  raf = requestAnimationFrame(animate)
  controls.update()
  if ((frame++ & 3) === 0) {
    const p = camera.position
    setCam(p.x, p.z, controls.getAzimuthalAngle())
  }
  renderer.render(scene, camera)
}

function onResize() {
  if (!mount.value || !renderer) return
  const { clientWidth: w, clientHeight: h } = mount.value
  if (!w || !h) return
  camera.aspect = w / h
  camera.updateProjectionMatrix()
  renderer.setSize(w, h)
}

onMounted(() => {
  const el = mount.value
  const w = el.clientWidth || window.innerWidth
  const h = el.clientHeight || window.innerHeight

  renderer = new THREE.WebGLRenderer({ antialias: true, powerPreference: 'high-performance' })
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2))
  renderer.setSize(w, h)
  renderer.shadowMap.enabled = true
  renderer.shadowMap.type = THREE.PCFSoftShadowMap
  renderer.toneMapping = THREE.ACESFilmicToneMapping
  renderer.outputColorSpace = THREE.SRGBColorSpace
  el.appendChild(renderer.domElement)

  scene = new THREE.Scene()

  // Процедурная среда (PBR-отражения без внешних файлов)
  try {
    const pmrem = new THREE.PMREMGenerator(renderer)
    scene.environment = pmrem.fromScene(new RoomEnvironment(), 0.04).texture
  } catch (e) {}

  camera = new THREE.PerspectiveCamera(52, w / h, 0.1, 120)
  camera.position.set(10.5, 6.5, 10.5)

  controls = new OrbitControls(camera, renderer.domElement)
  controls.enableDamping = true
  controls.dampingFactor = 0.075
  controls.minDistance = 1.6
  controls.maxDistance = 18
  controls.maxPolarAngle = Math.PI * 0.49
  controls.target.set(0, 1, 0)
  controls.addEventListener('start', onUserInteract)

  // Свет
  lights.ambient = new THREE.AmbientLight(0xffffff, 0.5)
  lights.hemi = new THREE.HemisphereLight(0xffffff, 0x3a3f47, 0.4)
  lights.hemi.position.set(0, 6, 0)
  lights.dir = new THREE.DirectionalLight(0xffffff, 1.1)
  lights.dir.position.set(6, 9.5, 5)
  lights.dir.castShadow = true
  lights.dir.shadow.mapSize.set(2048, 2048)
  Object.assign(lights.dir.shadow.camera, { near: 0.5, far: 40, left: -12, right: 12, top: 12, bottom: -12 })
  lights.dir.shadow.bias = -0.0004
  lights.p1 = new THREE.PointLight(0xffffff, 12, 14, 2); lights.p1.position.set(0, 2.55, 0)
  lights.p2 = new THREE.PointLight(0xffffff, 6, 8, 2); lights.p2.position.set(-3.5, 2.4, -2)
  scene.add(lights.ambient, lights.hemi, lights.dir, lights.p1, lights.p2)

  applyMaterials()
  applyLighting()
  scene.add(buildApartment())

  animate()
  setReady(true)
  playIntro()

  ro = new ResizeObserver(onResize)
  ro.observe(el)
  window.addEventListener('resize', onResize)
})

// Реактивность на состояние тура
watch(() => state.styleId, () => { applyMaterials(); applyLighting() })
watch(() => state.timeId, () => applyLighting())
watch(() => state.activeRoom, (id) => { if (id && controls) goToRoom(id) })
watch(() => state.cmd, () => {
  if (!controls) return
  if (state.cmdName === 'reset') resetView()
  else if (state.cmdName === 'intro') playIntro()
})

onBeforeUnmount(() => {
  disposed = true
  killTl()
  if (raf) cancelAnimationFrame(raf)
  if (ro) ro.disconnect()
  window.removeEventListener('resize', onResize)
  try { controls?.dispose() } catch (e) {}
  try {
    scene?.traverse((o) => { o.geometry?.dispose?.(); if (o.material) (Array.isArray(o.material) ? o.material : [o.material]).forEach(m => m.dispose?.()) })
    renderer?.dispose()
    renderer?.domElement?.remove()
  } catch (e) {}
})
</script>

<style scoped>
.scene-mount { position: absolute; inset: 0; width: 100%; height: 100%; }
.scene-mount :deep(canvas) { display: block; width: 100% !important; height: 100% !important; }
</style>
