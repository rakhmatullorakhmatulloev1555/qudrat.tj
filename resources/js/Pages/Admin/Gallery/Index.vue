<template>
  <AdminLayout page-title="Галерея" page-subtitle="CMS — управление фотографиями">
    <Head title="Галерея" />

    <!-- Toolbar -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
      <div class="flex items-center gap-3">
        <select v-model="catFilter"
          class="px-4 py-2.5 text-sm rounded-xl text-slate-300 outline-none transition-all"
          style="background:#1E293B; border:1px solid rgba(255,255,255,0.08)">
          <option value="">Все категории</option>
          <option v-for="(label, val) in categories" :key="val" :value="val">{{ label }}</option>
        </select>
      </div>
      <button @click="openModal()"
        class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm text-[#0F172A] transition-all"
        style="background:#C9A96E">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Добавить фото
      </button>
    </div>

    <!-- Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
      <div v-for="img in filteredImages" :key="img.id"
        class="rounded-xl overflow-hidden border border-white/6 relative group"
        style="background:#1E293B">
        <div class="aspect-video bg-slate-900 overflow-hidden">
          <img :src="img.image_path" :alt="img.alt || img.title || 'Gallery'" loading="lazy"
            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
            @error="e => e.target.src = 'https://placehold.co/400x225/0f172a/475569?text=No+Image'"/>
        </div>
        <!-- Overlay on hover -->
        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
          <button @click="openModal(img)" class="p-2 rounded-xl bg-gold text-[#0F172A] transition-all hover:scale-110">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
            </svg>
          </button>
          <button @click="deleteItem(img)" class="p-2 rounded-xl bg-red-500/20 text-red-400 hover:bg-red-500/40 transition-all hover:scale-110">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
            </svg>
          </button>
        </div>
        <!-- Info -->
        <div class="p-3">
          <div class="text-white text-xs font-medium truncate">{{ img.title || 'Без названия' }}</div>
          <div class="flex items-center justify-between mt-1">
            <span class="text-slate-500 text-[11px]">{{ categories[img.category] || img.category }}</span>
            <span class="text-[11px] px-1.5 py-0.5 rounded-full"
              :class="img.is_active ? 'text-emerald-400 bg-emerald-400/10' : 'text-slate-500 bg-slate-700/30'">
              {{ img.is_active ? '●' : '○' }}
            </span>
          </div>
        </div>
        <!-- Mobile action buttons (touch-friendly) -->
        <div class="flex gap-1 px-3 pb-3 md:hidden">
          <button @click="openModal(img)"
            class="flex-1 py-1.5 rounded-lg text-xs font-medium text-gold border border-gold/30 hover:bg-gold/10 transition-all">
            Изменить
          </button>
          <button @click="deleteItem(img)"
            class="px-3 py-1.5 rounded-lg text-xs font-medium text-red-400 border border-red-500/20 hover:bg-red-500/10 transition-all">
            Удалить
          </button>
        </div>
      </div>

      <div v-if="!filteredImages.length" class="col-span-4 py-16 text-center text-slate-500 text-sm">
        Изображений нет.
      </div>
    </div>

    <!-- Modal -->
    <Teleport to="body">
      <div v-if="modal.open" class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="background:rgba(0,0,0,0.7); backdrop-filter:blur(4px)" @click.self="closeModal">
        <div class="w-full max-w-md rounded-2xl border border-white/8 shadow-2xl" style="background:#1E293B">
          <div class="flex items-center justify-between px-6 py-4 border-b border-white/6">
            <h3 class="text-white font-semibold">{{ modal.editing ? 'Редактировать' : 'Добавить фото' }}</h3>
            <button @click="closeModal" class="text-slate-400 hover:text-white transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <form @submit.prevent="submitForm" enctype="multipart/form-data" class="p-6 space-y-4">
            <!-- Image preview + upload -->
            <div>
              <label class="block text-xs text-slate-400 mb-2">{{ modal.editing ? 'Новое изображение (оставьте пустым, чтобы не менять)' : 'Изображение *' }}</label>
              <div class="relative border-2 border-dashed rounded-xl p-4 text-center cursor-pointer transition-colors"
                :style="dragOver ? 'border-color:#C9A96E; background:rgba(201,169,110,0.05)' : 'border-color:rgba(255,255,255,0.12)'"
                @dragover.prevent="dragOver=true" @dragleave="dragOver=false" @drop.prevent="onDrop"
                @click="$refs.fileInput.click()">
                <img v-if="imagePreview" :src="imagePreview" class="h-32 mx-auto object-contain rounded-lg mb-2"/>
                <div v-else class="py-4">
                  <svg class="w-8 h-8 mx-auto text-slate-600 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                  </svg>
                  <p class="text-slate-500 text-xs">Перетащите или нажмите для выбора</p>
                  <p class="text-slate-600 text-[11px]">JPEG, PNG, WebP — макс. 5 МБ</p>
                </div>
                <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="onFileChange"/>
              </div>
              <p v-if="errors.image" class="text-red-400 text-xs mt-1">{{ errors.image }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs text-slate-400 mb-1">Название</label>
                <input v-model="form.title" class="field w-full" placeholder="Необязательно"/>
              </div>
              <div>
                <label class="block text-xs text-slate-400 mb-1">Alt-текст (SEO)</label>
                <input v-model="form.alt" class="field w-full" placeholder="Описание для SEO"/>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs text-slate-400 mb-1">Категория *</label>
                <select v-model="form.category" class="field w-full">
                  <option v-for="(label, val) in categories" :key="val" :value="val">{{ label }}</option>
                </select>
              </div>
              <div>
                <label class="block text-xs text-slate-400 mb-1">Порядок</label>
                <input v-model.number="form.sort_order" type="number" min="0" class="field w-full"/>
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
                class="flex-1 py-2.5 rounded-xl text-sm text-slate-300 border border-white/10 hover:border-white/20 transition-all">
                Отмена
              </button>
              <button type="submit" :disabled="processing"
                class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-[#0F172A] transition-all disabled:opacity-60"
                style="background:#C9A96E">
                {{ processing ? 'Загружаем...' : (modal.editing ? 'Сохранить' : 'Загрузить') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  images:     { type: Array, default: () => [] },
  categories: { type: Object, default: () => ({}) },
});

const catFilter = ref('');
const modal = reactive({ open: false, editing: null });
const errors = ref({});
const dragOver = ref(false);
const imagePreview = ref(null);
const selectedFile = ref(null);
const processing = ref(false);

const form = reactive({
  title: '', alt: '', category: 'building', is_active: true, sort_order: 0,
});

const filteredImages = computed(() =>
  catFilter.value ? props.images.filter(i => i.category === catFilter.value) : props.images
);

function openModal(item = null) {
  modal.editing = item;
  modal.open = true;
  errors.value = {};
  imagePreview.value = item?.image_path || null;
  selectedFile.value = null;
  Object.assign(form, {
    title:      item?.title ?? '',
    alt:        item?.alt ?? '',
    category:   item?.category ?? 'building',
    is_active:  item?.is_active ?? true,
    sort_order: item?.sort_order ?? 0,
  });
}

function closeModal() { modal.open = false; modal.editing = null; }

function onFileChange(e) {
  const f = e.target.files[0];
  if (!f) return;
  selectedFile.value = f;
  imagePreview.value = URL.createObjectURL(f);
}

function onDrop(e) {
  dragOver.value = false;
  const f = e.dataTransfer.files[0];
  if (f && f.type.startsWith('image/')) {
    selectedFile.value = f;
    imagePreview.value = URL.createObjectURL(f);
  }
}

function submitForm() {
  if (!modal.editing && !selectedFile.value) {
    errors.value = { image: 'Выберите изображение' };
    return;
  }

  const data = new FormData();
  if (selectedFile.value) data.append('image', selectedFile.value);
  data.append('title', form.title);
  data.append('alt', form.alt);
  data.append('category', form.category);
  data.append('is_active', form.is_active ? '1' : '0');
  data.append('sort_order', String(form.sort_order));

  processing.value = true;
  errors.value = {};

  if (modal.editing) {
    data.append('_method', 'PUT');
    router.post(route('admin.gallery.update', modal.editing.id), data, {
      preserveScroll: true,
      forceFormData: true,
      onSuccess: closeModal,
      onError: e => { errors.value = e; },
      onFinish: () => { processing.value = false; },
    });
  } else {
    router.post(route('admin.gallery.store'), data, {
      preserveScroll: true,
      forceFormData: true,
      onSuccess: closeModal,
      onError: e => { errors.value = e; },
      onFinish: () => { processing.value = false; },
    });
  }
}

function deleteItem(item) {
  if (!confirm('Удалить изображение?')) return;
  router.delete(route('admin.gallery.destroy', item.id), { preserveScroll: true });
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
