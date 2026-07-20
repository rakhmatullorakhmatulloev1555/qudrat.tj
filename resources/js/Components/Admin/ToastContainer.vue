<template>
  <Teleport to="body">
    <div class="toast-container" aria-live="polite" aria-atomic="true">
      <TransitionGroup name="toast" tag="div" class="flex flex-col gap-2">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          class="toast-item"
          :class="`toast-${toast.type}`"
          @click="remove(toast.id)"
        >
          <span class="toast-icon">{{ icons[toast.type] }}</span>
          <span class="toast-msg">{{ toast.message }}</span>
          <button class="toast-close" @click.stop="remove(toast.id)" aria-label="Закрыть">×</button>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup>
import { useToast } from '@/composables/useToast';

const { toasts, remove } = useToast();

const icons = {
  success: '✓',
  error:   '✕',
  warning: '⚠',
  info:    'ℹ',
};
</script>

<style scoped>
.toast-container {
  position: fixed;
  top: 1.25rem;
  right: 1.25rem;
  z-index: 9999;
  min-width: 280px;
  max-width: 380px;
}
.toast-item {
  display: flex;
  align-items: center;
  gap: 0.625rem;
  padding: 0.875rem 1rem;
  border-radius: 0.625rem;
  border-left: 4px solid transparent;
  cursor: pointer;
  box-shadow: 0 8px 32px rgba(0,0,0,0.4);
  backdrop-filter: blur(8px);
  font-size: 0.875rem;
  font-weight: 500;
}
.toast-success { background: #0f2a1f; border-color: #10B981; color: #34D399; }
.toast-error   { background: #2a0f0f; border-color: #EF4444; color: #F87171; }
.toast-warning { background: #2a1f0f; border-color: #F59E0B; color: #FCD34D; }
.toast-info    { background: #0f1a2a; border-color: #6366F1; color: #818CF8; }
.toast-icon    { font-weight: 700; font-size: 1rem; flex-shrink: 0; }
.toast-msg     { flex: 1; }
.toast-close   { background: none; border: none; color: inherit; opacity: 0.6; cursor: pointer; font-size: 1.1rem; padding: 0; line-height: 1; flex-shrink: 0; }
.toast-close:hover { opacity: 1; }

/* Анимации */
.toast-enter-active { transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
.toast-leave-active { transition: all 0.2s ease-in; }
.toast-enter-from   { opacity: 0; transform: translateX(60px) scale(0.9); }
.toast-leave-to     { opacity: 0; transform: translateX(60px) scale(0.9); }
.toast-move         { transition: transform 0.3s ease; }

@media (prefers-reduced-motion: reduce) {
  .toast-enter-active, .toast-leave-active, .toast-move { transition: opacity 0.2s; }
  .toast-enter-from, .toast-leave-to { transform: none; }
}
</style>
