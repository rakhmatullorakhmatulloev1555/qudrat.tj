<template>
  <div class="animate-pulse space-y-2">
    <!-- Header -->
    <div class="flex gap-4 px-4 py-3 border-b border-white/5">
      <div v-for="w in headerWidths" :key="w" class="h-3 bg-white/5 rounded-full" :style="{ width: w }"></div>
    </div>
    <!-- Rows -->
    <div v-for="i in rows" :key="i" class="flex gap-4 px-4 py-3 border-b border-white/5">
      <div v-for="w in headerWidths" :key="w" class="h-3 bg-white/5 rounded-full" :style="{ width: w }"></div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  rows:         { type: Number, default: 5 },
  headerWidths: { type: Array, default: () => ['15%', '25%', '20%', '15%', '10%'] },
})
</script>
