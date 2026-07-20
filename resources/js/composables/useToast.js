/**
 * useToast — глобальная система уведомлений для admin панели
 * Использование: const toast = useToast(); toast.success('Сохранено!')
 */
import { reactive } from 'vue';

const state = reactive({
  toasts: [],
  nextId: 1,
});

function add(message, type = 'success', duration = 3500) {
  const id = state.nextId++;
  state.toasts.push({ id, message, type });
  setTimeout(() => remove(id), duration);
  return id;
}

function remove(id) {
  const idx = state.toasts.findIndex(t => t.id === id);
  if (idx !== -1) state.toasts.splice(idx, 1);
}

export function useToast() {
  return {
    toasts: state.toasts,
    success: (msg) => add(msg, 'success'),
    error:   (msg) => add(msg, 'error', 5000),
    info:    (msg) => add(msg, 'info'),
    warning: (msg) => add(msg, 'warning', 4000),
    remove,
  };
}
