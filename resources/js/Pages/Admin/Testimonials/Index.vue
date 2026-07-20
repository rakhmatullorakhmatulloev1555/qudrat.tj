<template>
  <AdminLayout page-title="Отзывы" page-subtitle="CMS — управление отзывами клиентов">
    <Head title="Отзывы" />

    <!-- Toolbar -->
    <div class="flex items-center justify-between gap-4 mb-6">
      <p class="text-slate-400 text-sm">Всего: <span class="text-white font-semibold">{{ testimonials.length }}</span></p>
      <button @click="openModal()" class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm text-[#0F172A] transition-all"
        style="background:#C9A96E">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Добавить отзыв
      </button>
    </div>

    <!-- Cards grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
      <div v-for="t in testimonials" :key="t.id"
        class="rounded-xl border p-5 flex flex-col gap-3 transition-all"
        :style="t.is_active ? 'background:#1E293B; border-color:rgba(255,255,255,0.06)' : 'background:#1a2234; border-color:rgba(255,255,255,0.03); opacity:0.6'">

        <!-- Header -->
        <div class="flex items-start justify-between gap-2">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0"
              style="background:#C9A96E20; color:#C9A96E; border:1.5px solid #C9A96E40">
              {{ initials(t.name) }}
            </div>
            <div>
              <div class="text-white font-semibold text-sm">{{ t.name }}</div>
              <div class="text-slate-500 text-xs">{{ t.role }}<span v-if="t.company"> · {{ t.company }}</span></div>
            </div>
          </div>
          <!-- Stars -->
          <div class="flex gap-0.5 mt-0.5">
            <svg v-for="i in 5" :key="i" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"
              :class="i <= t.rating ? 'text-gold' : 'text-slate-700'">
              <path d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/>
            </svg>
          </div>
        </div>

        <!-- Text -->
        <p class="text-slate-400 text-sm leading-relaxed line-clamp-3">{{ t.text }}</p>

        <!-- Footer -->
        <div class="flex items-center justify-between mt-auto pt-2 border-t border-white/4">
          <span class="text-xs px-2 py-0.5 rounded-full"
            :class="t.is_active ? 'text-emerald-400 bg-emerald-400/10' : 'text-slate-500 bg-slate-700/30'">
            {{ t.is_active ? 'Активен' : 'Скрыт' }}
          </span>
          <div class="flex items-center gap-1">
            <button @click="openModal(t)" class="p-1.5 rounded-lg text-slate-400 hover:text-gold hover:bg-gold/10 transition-all">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
              </svg>
            </button>
            <button @click="deleteItem(t)" class="p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-all">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <div v-if="!testimonials.length" class="col-span-3 py-16 text-center text-slate-500 text-sm">
        Отзывов нет. Добавьте первый.
      </div>
    </div>

    <!-- Modal -->
    <Teleport to="body">
      <div v-if="modal.open" class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="background:rgba(0,0,0,0.7); backdrop-filter:blur(4px)" @click.self="closeModal">
        <div class="w-full max-w-lg rounded-2xl border border-white/8 shadow-2xl overflow-hidden" style="background:#1E293B">
          <!-- Header -->
          <div class="flex items-center justify-between px-6 py-4 border-b border-white/6">
            <h3 class="text-white font-semibold">{{ modal.editing ? 'Редактировать отзыв' : 'Добавить отзыв' }}</h3>
            <button @click="closeModal" class="text-slate-400 hover:text-white transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Body -->
          <form @submit.prevent="submitForm" class="p-6 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs text-slate-400 mb-1">Имя *</label>
                <input v-model="form.name" required class="field w-full" placeholder="Имя клиента"/>
                <p v-if="errors.name" class="text-red-400 text-xs mt-1">{{ errors.name }}</p>
              </div>
              <div>
                <label class="block text-xs text-slate-400 mb-1">Должность</label>
                <input v-model="form.role" class="field w-full" placeholder="Покупатель, инвестор..."/>
              </div>
            </div>
            <div>
              <label class="block text-xs text-slate-400 mb-1">Компания</label>
              <input v-model="form.company" class="field w-full" placeholder="Название компании (опционально)"/>
            </div>
            <div>
              <label class="block text-xs text-slate-400 mb-1">Текст отзыва *</label>
              <textarea v-model="form.text" required rows="4" class="field w-full resize-none"
                placeholder="Текст отзыва..."/>
              <p v-if="errors.text" class="text-red-400 text-xs mt-1">{{ errors.text }}</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs text-slate-400 mb-1">Рейтинг (1–5)</label>
                <div class="flex gap-1">
                  <button v-for="i in 5" :key="i" type="button" @click="form.rating = i"
                    class="text-2xl transition-transform hover:scale-110"
                    :class="i <= form.rating ? 'text-gold' : 'text-slate-700'">★</button>
                </div>
              </div>
              <div>
                <label class="block text-xs text-slate-400 mb-1">Порядок</label>
                <input v-model.number="form.sort_order" type="number" min="0" class="field w-full" placeholder="0"/>
              </div>
            </div>
            <label class="flex items-center gap-3 cursor-pointer">
              <div class="relative w-10 h-5 rounded-full transition-colors"
                :style="form.is_active ? 'background:#C9A96E' : 'background:#334155'"
                @click="form.is_active = !form.is_active">
                <div class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform"
                  :class="form.is_active ? 'translate-x-5' : 'translate-x-0.5'"/>
              </div>
              <span class="text-sm text-slate-300">Показывать на сайте</span>
            </label>

            <div class="flex gap-3 pt-2">
              <button type="button" @click="closeModal"
                class="flex-1 py-2.5 rounded-xl text-sm text-slate-300 hover:text-white border border-white/10 hover:border-white/20 transition-all">
                Отмена
              </button>
              <button type="submit" :disabled="form.processing"
                class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-[#0F172A] transition-all disabled:opacity-60"
                style="background:#C9A96E">
                {{ form.processing ? 'Сохраняем...' : (modal.editing ? 'Сохранить' : 'Добавить') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  testimonials: { type: Array, default: () => [] },
});

const modal = reactive({ open: false, editing: null });
const errors = ref({});

const form = useForm({
  name: '', role: '', company: '', text: '',
  rating: 5, is_active: true, sort_order: 0,
});

function initials(name) {
  return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
}

function openModal(item = null) {
  modal.editing = item;
  modal.open = true;
  errors.value = {};
  if (item) {
    form.name       = item.name;
    form.role       = item.role || '';
    form.company    = item.company || '';
    form.text       = item.text;
    form.rating     = item.rating;
    form.is_active  = item.is_active;
    form.sort_order = item.sort_order;
  } else {
    form.reset();
    form.rating = 5;
    form.is_active = true;
    form.sort_order = 0;
  }
}

function closeModal() {
  modal.open = false;
  modal.editing = null;
}

function submitForm() {
  if (modal.editing) {
    form.put(route('admin.testimonials.update', modal.editing.id), {
      preserveScroll: true,
      onSuccess: closeModal,
      onError: e => { errors.value = e; },
    });
  } else {
    form.post(route('admin.testimonials.store'), {
      preserveScroll: true,
      onSuccess: closeModal,
      onError: e => { errors.value = e; },
    });
  }
}

function deleteItem(item) {
  if (!confirm(`Удалить отзыв от "${item.name}"?`)) return;
  router.delete(route('admin.testimonials.destroy', item.id), { preserveScroll: true });
}
</script>

<style scoped>
.field {
  background: #0F172A;
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 0.75rem;
  padding: 0.6rem 1rem;
  font-size: 0.875rem;
  color: #E2E8F0;
  outline: none;
  transition: border-color 0.15s;
}
.field:focus { border-color: rgba(201,169,110,0.5); }
</style>
