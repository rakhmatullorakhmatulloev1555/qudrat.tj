<template><TresGroup /></template>

<script setup>
/**
 * CameraRig — управление камерой:
 *  • кинематографический пролёт при открытии (GSAP);
 *  • передача управления пользователю (handoff) при первом действии;
 *  • переходы по комнатам и «Сбросить вид».
 * Работает с инстансом OrbitControls, который cientos регистрирует в контексте.
 */
import { watch, onBeforeUnmount } from 'vue'
import { useLoop, useTresContext } from '@tresjs/core'
import { gsap } from 'gsap'
import { introSequence, rooms } from './config/roomsConfig'
import { useTour } from './useTour'

const { state, setIntroPlaying, setCam, setReady } = useTour()
const { controls } = useTresContext()
const { onBeforeRender } = useLoop()

let tl = null
let ready = false
let frame = 0

function killTl() { if (tl) { tl.kill(); tl = null } }

function onUserInteract() {
  killTl()
  if (state.introPlaying) setIntroPlaying(false)
}

function cfg(c) {
  c.enableDamping = true
  c.dampingFactor = 0.075
  c.minDistance = 1.6
  c.maxDistance = 18
  c.maxPolarAngle = Math.PI * 0.49   // не проваливаться под пол
  c.enablePan = true
  c.target.set(0, 1, 0)
  c.addEventListener('start', onUserInteract)
}

function playIntro() {
  const c = controls.value; if (!c) return
  killTl(); setIntroPlaying(true)
  const cam = c.object
  tl = gsap.timeline({ onComplete: () => { tl = null; setIntroPlaying(false) } })
  introSequence.forEach((wp, i) => {
    if (i === 0) { cam.position.set(...wp.pos); c.target.set(...wp.target); c.update(); return }
    tl.to(cam.position, { x: wp.pos[0], y: wp.pos[1], z: wp.pos[2], duration: wp.duration, ease: 'power2.inOut' }, i === 1 ? 0 : '>')
      .to(c.target, { x: wp.target[0], y: wp.target[1], z: wp.target[2], duration: wp.duration, ease: 'power2.inOut', onUpdate: () => c.update() }, '<')
  })
}

function goToRoom(id) {
  const c = controls.value; if (!c) return
  const r = rooms.find(x => x.id === id); if (!r) return
  killTl(); setIntroPlaying(false)
  const cam = c.object
  gsap.to(cam.position, { x: r.camera.pos[0], y: r.camera.pos[1], z: r.camera.pos[2], duration: 1.5, ease: 'power3.inOut' })
  gsap.to(c.target,   { x: r.camera.target[0], y: r.camera.target[1], z: r.camera.target[2], duration: 1.5, ease: 'power3.inOut', onUpdate: () => c.update() })
}

function resetView() {
  const c = controls.value; if (!c) return
  killTl(); setIntroPlaying(false)
  const cam = c.object
  gsap.to(cam.position, { x: 8.5, y: 5.5, z: 8.5, duration: 1.3, ease: 'power3.inOut' })
  gsap.to(c.target,   { x: 0, y: 1, z: 0, duration: 1.3, ease: 'power3.inOut', onUpdate: () => c.update() })
}

// Ждём регистрации OrbitControls
const stop = watch(controls, (c) => {
  if (c && !ready) { ready = true; cfg(c); setReady(true); playIntro(); stop() }
}, { immediate: true })

watch(() => state.activeRoom, (id) => { if (id) goToRoom(id) })
watch(() => state.cmd, () => {
  if (state.cmdName === 'reset') resetView()
  else if (state.cmdName === 'intro') playIntro()
})

// damping + публикация позы камеры для миникарты (throttle ~15 fps)
onBeforeRender(() => {
  const c = controls.value; if (!c) return
  if (c.enableDamping) c.update()
  if ((frame++ & 3) === 0) {
    const p = c.object.position
    const yaw = typeof c.getAzimuthalAngle === 'function' ? c.getAzimuthalAngle() : Math.atan2(p.x - c.target.x, p.z - c.target.z)
    setCam(p.x, p.z, yaw)
  }
})

onBeforeUnmount(() => {
  killTl()
  try { controls.value?.removeEventListener('start', onUserInteract) } catch (e) {}
})
</script>
