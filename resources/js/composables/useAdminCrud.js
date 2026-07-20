/**
 * useAdminCrud — универсальный composable для CRUD операций в admin панели
 * Устраняет дублирование кода между Mining, Partners, Documents, Leads и др.
 */
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

export function useAdminCrud(defaultFields = {}) {
  const showModal = ref(false);
  const editing = ref(null);
  const form = useForm({ ...defaultFields });

  /**
   * Открыть модал для создания новой записи
   */
  function openCreate() {
    editing.value = null;
    form.reset();
    Object.keys(defaultFields).forEach(key => {
      form[key] = defaultFields[key];
    });
    showModal.value = true;
  }

  /**
   * Открыть модал для редактирования существующей записи
   * @param {Object} item — запись из БД
   */
  function openEdit(item) {
    editing.value = item;
    Object.keys(defaultFields).forEach(key => {
      form[key] = item[key] !== undefined ? item[key] : defaultFields[key];
    });
    showModal.value = true;
  }

  /**
   * Закрыть модал и сбросить форму
   */
  function closeModal() {
    showModal.value = false;
    editing.value = null;
    form.reset();
    form.clearErrors();
  }

  /**
   * Отправить форму (store или update)
   * @param {string} storeRoute  — имя Inertia route для создания
   * @param {string} updateRoute — имя Inertia route для обновления
   * @param {*} id               — идентификатор записи при обновлении
   */
  function submit(storeRoute, updateRoute, id = null) {
    if (editing.value) {
      form.put(route(updateRoute, id ?? editing.value.id), {
        onSuccess: closeModal,
        preserveScroll: true,
      });
    } else {
      form.post(route(storeRoute), {
        onSuccess: closeModal,
        preserveScroll: true,
      });
    }
  }

  /**
   * Удалить запись с подтверждением
   * @param {string} destroyRoute — имя Inertia route для удаления
   * @param {*} id                — идентификатор записи
   * @param {string} confirmMsg   — текст подтверждения
   */
  function destroy(destroyRoute, id, confirmMsg = 'Вы уверены? Это действие нельзя отменить.') {
    if (!confirm(confirmMsg)) return;
    form.delete(route(destroyRoute, id), {
      preserveScroll: true,
    });
  }

  return {
    form,
    showModal,
    editing,
    openCreate,
    openEdit,
    closeModal,
    submit,
    destroy,
  };
}
