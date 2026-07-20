<template><TresGroup /></template>

<script setup>
/**
 * SceneEnvironment — процедурная HDRI-подобная среда (RoomEnvironment + PMREM).
 * Даёт реалистичные отражения на металле/стекле БЕЗ внешних файлов (CSP-safe).
 * Написано защитно: если рендерер недоступен — сцена продолжит работать на ручном свете.
 */
import { onBeforeUnmount, watch } from 'vue'
import { useLoop, useTresContext } from '@tresjs/core'
import { PMREMGenerator } from 'three'
import { RoomEnvironment } from 'three/examples/jsm/environments/RoomEnvironment.js'

const props = defineProps({ exposure: { type: Number, default: 1 } })

const { scene } = useTresContext()
const { onBeforeRender } = useLoop()

let envRT = null
let pmrem = null
let gl = null
let done = false

onBeforeRender((ctx) => {
  if (done) return
  try {
    gl = ctx?.renderer?.instance || ctx?.renderer || null
    const sc = scene?.value
    if (gl && gl.capabilities && sc) {
      pmrem = new PMREMGenerator(gl)
      envRT = pmrem.fromScene(new RoomEnvironment(), 0.04)
      sc.environment = envRT.texture
      if ('toneMappingExposure' in gl) gl.toneMappingExposure = props.exposure
      done = true
    }
  } catch (e) {
    done = true
  }
})

watch(() => props.exposure, (v) => {
  try { if (gl && 'toneMappingExposure' in gl) gl.toneMappingExposure = v } catch (e) {}
})

onBeforeUnmount(() => {
  try { envRT?.dispose(); pmrem?.dispose() } catch (e) {}
})
</script>
