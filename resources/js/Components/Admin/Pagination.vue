<template>
  <div v-if="links && links.length > 3" class="flex items-center justify-between px-1 py-3">

    <!-- Left: info -->
    <p class="text-slate-500 text-xs">
      Показано <span class="text-white font-semibold">{{ from }}–{{ to }}</span>
      из <span class="text-white font-semibold">{{ total }}</span>
    </p>

    <!-- Right: page buttons -->
    <div class="flex items-center gap-1">

      <!-- Prev -->
      <button
        v-if="prevUrl"
        @click="$emit('navigate', prevUrl)"
        class="page-btn"
        aria-label="Предыдущая страница"
      >
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
        </svg>
      </button>
      <span v-else class="page-btn opacity-30 cursor-not-allowed">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
        </svg>
      </span>

      <!-- Page numbers -->
      <template v-for="link in pageLinks" :key="link.label">
        <span v-if="link.label === '...'" class="px-2 text-slate-600 text-xs">…</span>
        <button
          v-else
          @click="link.url && $emit('navigate', link.url)"
          class="page-btn"
          :class="link.active
            ? 'bg-gold text-[#0F172A] font-bold border-gold'
            : link.url ? '' : 'opacity-30 cursor-not-allowed'"
          :disabled="!link.url"
        >
          {{ link.label }}
        </button>
      </template>

      <!-- Next -->
      <button
        v-if="nextUrl"
        @click="$emit('navigate', nextUrl)"
        class="page-btn"
        aria-label="Следующая страница"
      >
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
        </svg>
      </button>
      <span v-else class="page-btn opacity-30 cursor-not-allowed">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
        </svg>
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  links: { type: Array, default: () => [] },
  from:  { type: Number, default: 0 },
  to:    { type: Number, default: 0 },
  total: { type: Number, default: 0 },
});

const emit = defineEmits(['navigate']);

const prevUrl = computed(() => props.links.find(l => l.label === '&laquo; Previous' || l.label === '« Previous')?.url ?? null);
const nextUrl = computed(() => props.links.find(l => l.label === 'Next &raquo;' || l.label === 'Next »')?.url ?? null);

// Только страницы (без Prev/Next)
const pageLinks = computed(() => {
  const pages = props.links.filter(l =>
    l.label !== '&laquo; Previous' && l.label !== 'Next &raquo;' &&
    l.label !== '« Previous' && l.label !== 'Next »'
  );

  // Умное сжатие: показываем первую, последнюю, текущую ±2
  if (pages.length <= 7) return pages;

  const current = pages.findIndex(p => p.active);
  const result = [];
  pages.forEach((p, i) => {
    if (i === 0 || i === pages.length - 1 || Math.abs(i - current) <= 1) {
      result.push(p);
    } else if (result[result.length - 1]?.label !== '...') {
      result.push({ label: '...', url: null, active: false });
    }
  });
  return result;
});

// Навигация через Inertia (сохраняет scroll)
function navigate(url) {
  if (!url) return;
  router.visit(url, { preserveScroll: true, preserveState: true });
}
</script>

<style scoped>
.page-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 2rem;
  height: 2rem;
  padding: 0 0.375rem;
  border-radius: 0.375rem;
  font-size: 0.75rem;
  font-weight: 500;
  border: 1px solid rgba(255,255,255,0.1);
  background: transparent;
  color: #94A3B8;
  cursor: pointer;
  transition: all 0.15s;
}
.page-btn:hover:not(:disabled) {
  border-color: rgba(201,169,110,0.4);
  color: #fff;
  background: rgba(201,169,110,0.08);
}
</style>
