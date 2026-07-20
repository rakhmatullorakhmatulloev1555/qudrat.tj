<template>
  <TresGroup>
    <!-- Пол -->
    <TresMesh :position="[0, groundConfig.floor.y, 0]" :rotation="[-Math.PI/2, 0, 0]" receive-shadow>
      <TresPlaneGeometry :args="groundConfig.floor.size" />
      <TresMeshStandardMaterial v-bind="mat('floor')" />
    </TresMesh>

    <!-- Потолок -->
    <TresMesh :position="[0, groundConfig.ceiling.y, 0]" :rotation="[Math.PI/2, 0, 0]">
      <TresPlaneGeometry :args="groundConfig.ceiling.size" />
      <TresMeshStandardMaterial v-bind="mat('ceiling')" />
    </TresMesh>

    <!-- Стены / двери / остекление -->
    <TresMesh v-for="(w, i) in layout.walls" :key="'w'+i" :position="w.position" cast-shadow receive-shadow>
      <TresBoxGeometry :args="w.size" />
      <TresMeshStandardMaterial v-bind="mat(w.material)" />
    </TresMesh>

    <!-- Мебель -->
    <TresMesh v-for="(f, i) in layout.furniture" :key="'f'+i"
      :position="f.position" :rotation="[0, f.rot || 0, 0]" cast-shadow receive-shadow>
      <TresBoxGeometry :args="f.size" />
      <TresMeshStandardMaterial v-bind="mat(f.material)" />
    </TresMesh>
  </TresGroup>
</template>

<script setup>
import { computed } from 'vue'
import { layout, groundConfig } from './config/roomsConfig'
import { interiorStyles } from './config/interiorStyles'

const props = defineProps({
  styleId: { type: String, required: true },
})

const style = computed(() => interiorStyles[props.styleId] || Object.values(interiorStyles)[0])

// Преобразуем конфиг материала в props для TresMeshStandardMaterial.
// При смене стиля computed пересчитывается → материалы обновляются без перезагрузки.
function mat(key) {
  const m = (style.value.materials[key]) || style.value.materials.wall
  const out = {
    color: m.color,
    roughness: m.roughness ?? 0.8,
    metalness: m.metalness ?? 0.0,
  }
  if (m.emissive) { out.emissive = m.emissive; out['emissive-intensity'] = m.emissiveIntensity ?? 0 }
  if (m.transparent) { out.transparent = true; out.opacity = m.opacity ?? 1 }
  return out
}
</script>
