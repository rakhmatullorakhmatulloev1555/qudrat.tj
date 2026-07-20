<template>
  <div class="rounded-2xl overflow-hidden animate-pulse" :style="containerStyle">
    <!-- Image placeholder -->
    <div v-if="!noImage" class="bg-white/5" :style="{ height: imageHeight }"></div>
    <!-- Content lines -->
    <div class="p-4 space-y-3">
      <div class="h-4 bg-white/5 rounded-full w-3/4"></div>
      <div class="h-3 bg-white/5 rounded-full w-full"></div>
      <div v-if="lines >= 3" class="h-3 bg-white/5 rounded-full w-5/6"></div>
      <div v-if="lines >= 4" class="h-3 bg-white/5 rounded-full w-4/6"></div>
      <div v-if="showBadge" class="flex gap-2 mt-4">
        <div class="h-6 w-16 bg-white/5 rounded-full"></div>
        <div class="h-6 w-20 bg-white/5 rounded-full"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  imageHeight:    { type: String, default: '200px' },
  lines:          { type: Number, default: 2 },
  noImage:        { type: Boolean, default: false },
  showBadge:      { type: Boolean, default: false },
  containerStyle: { type: String, default: 'background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06)' },
})
</script>
