<template>
  <AdminLayout page-title="Новости" page-subtitle="CMS — управление публикациями">
    <Head title="Новости" />

    <!-- Toolbar -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
      <div class="flex flex-wrap items-center gap-3 flex-1">
        <div class="relative flex-1 min-w-[160px]">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
          </svg>
          <input v-model="localSearch" @input="doSearch" type="text" placeholder="Поиск по заголовку..."
            class="pl-9 pr-4 py-2.5 text-sm rounded-xl text-white placeholder-slate-500 outline-none w-full transition-all"
            style="background:#1E293B; border:1px solid rgba(255,255,255,0.08)"/>
        </div>
        <select v-model="localCat" @change="doFilter"
          class="px-4 py-2.5 text-sm rounded-xl text-slate-300 outline-none w-full sm:w-auto"
          style="background:#1E293B; border:1px solid rgba(255,255,255,0.08)">
          <option value="">Все категории</option>
          <option v-for="(label, val) in categories" :key="val" :value="val">{{ label }}</option>
        </select>
      </div>
      <button @click="openModal()"
        class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm text-[#0F172A] transition-all flex-shrink-0"
        style="background:#C9A96E">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Написать новость
      </button>
    </div>

    <!-- Mobile cards -->
    <div class="md:hidden space-y-2 mb-4">
      <div v-if="!posts.data.length" class="rounded-xl p-6 text-center text-slate-500 text-sm" style="background:#1E293B; border:1px solid rgba(255,255,255,0.06)">
        Новостей нет
      </div>
      <div v-for="post in posts.data" :key="post.id"
        class="rounded-xl p-4 border border-white/6" style="background:#1E293B">
        <div class="flex items-start justify-between gap-3 mb-2">
          <div class="text-white text-sm font-semibold line-clamp-2">{{ post.title }}</div>
          <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold flex-shrink-0"
            :class="post.is_published ? 'bg-emerald-400/10 text-emerald-400' : 'bg-slate-700/40 text-slate-400'">
            {{ post.is_published ? 'Опубл.' : 'Черновик' }}
          </span>
        </div>
        <div v-if="post.excerpt" class="text-slate-500 text-xs mb-2 line-clamp-2">{{ post.excerpt }}</div>
        <div class="flex items-center gap-2 mb-3">
          <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-500/10 text-blue-400">
            {{ categories[post.category] || post.category }}
          </span>
          <span class="text-slate-500 text-xs">{{ post.published_at ? formatDate(post.published_at) : formatDate(post.created_at) }}</span>
        </div>
        <div class="flex gap-2">
          <button @click="openModal(post)"
            class="flex-1 py-2 rounded-lg text-xs font-medium text-gold border border-gold/30 hover:bg-gold/10 transition-all">
            Редактировать
          </button>
          <button @click="deletePost(post)"
            class="px-4 py-2 rounded-lg text-xs font-medium text-red-400 border border-red-500/20 hover:bg-red-500/10 transition-all">
            Удалить
          </button>
        </div>
      </div>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block rounded-xl border border-white/6 overflow-hidden" style="background:#1E293B">
      <table class="w-full">
        <thead>
          <tr class="border-b border-white/6">
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Заголовок</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500 hidden lg:table-cell">Категория</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Статус</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500 hidden lg:table-cell">Автор</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500 hidden lg:table-cell">Дата</th>
            <th class="text-right px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="!posts.data.length">
            <td colspan="6" class="px-5 py-12 text-center text-slate-500 text-sm">Новостей нет</td>
          </tr>
          <tr v-for="post in posts.data" :key="post.id"
            class="border-b border-white/4 hover:bg-white/3 transition-colors">
            <td class="px-5 py-3 max-w-xs">
              <div class="text-white text-sm font-medium truncate">{{ post.title }}</div>
              <div v-if="post.excerpt" class="text-slate-500 text-xs truncate mt-0.5">{{ post.excerpt }}</div>
            </td>
            <td class="px-5 py-3 hidden lg:table-cell">
              <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-500/10 text-blue-400">
                {{ categories[post.category] || post.category }}
              </span>
            </td>
            <td class="px-5 py-3">
              <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold"
                :class="post.is_published ? 'bg-emerald-400/10 text-emerald-400' : 'bg-slate-700/40 text-slate-400'">
                {{ post.is_published ? 'Опубликовано' : 'Черновик' }}
              </span>
            </td>
            <td class="px-5 py-3 text-slate-400 text-sm hidden lg:table-cell">{{ post.author?.name || '—' }}</td>
            <td class="px-5 py-3 text-slate-500 text-xs hidden lg:table-cell">
              {{ post.published_at ? formatDate(post.published_at) : formatDate(post.created_at) }}
            </td>
            <td class="px-5 py-3 text-right">
              <div class="flex items-center justify-end gap-2">
                <button @click="openModal(post)" class="p-1.5 rounded-lg text-slate-400 hover:text-gold hover:bg-gold/10 transition-all">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                  </svg>
                </button>
                <button @click="deletePost(post)" class="p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-all">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="posts.last_page > 1" class="flex items-center justify-center gap-2 mt-6">
      <Link v-for="p in posts.links" :key="p.label"
        :href="p.url || '#'"
        :class="[
          'px-3 py-1.5 rounded-lg text-sm transition-all',
          p.active ? 'text-[#0F172A] font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/5',
          !p.url ? 'opacity-30 pointer-events-none' : ''
        ]"
        :style="p.active ? 'background:#C9A96E' : ''"
        >{{ p.label.replace('&laquo;','«').replace('&raquo;','»') }}</Link>
    </div>

    <!-- Modal — full post editor -->
    <Teleport to="body">
      <div v-if="modal.open" class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="background:rgba(0,0,0,0.75); backdrop-filter:blur(6px)" @click.self="closeModal">
        <div class="w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl border border-white/8 shadow-2xl" style="background:#1E293B">
          <div class="flex items-center justify-between px-6 py-4 border-b border-white/6 sticky top-0 z-10" style="background:#1E293B">
            <h3 class="text-white font-semibold">{{ modal.editing ? 'Редактировать новость' : 'Новая публикация' }}</h3>
            <button @click="closeModal" class="text-slate-400 hover:text-white transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <form @submit.prevent="submitForm" class="p-6 space-y-5">
            <div>
              <label class="block text-xs text-slate-400 mb-1">Заголовок *</label>
              <input v-model="form.title" required class="field w-full text-base" placeholder="Заголовок новости"/>
              <p v-if="errors.title" class="text-red-400 text-xs mt-1">{{ errors.title }}</p>
            </div>

            <div>
              <label class="block text-xs text-slate-400 mb-1">Краткое описание (анонс)</label>
              <textarea v-model="form.excerpt" rows="2" class="field w-full resize-none"
                placeholder="Краткое описание для списка новостей..."/>
            </div>

            <div>
              <label class="block text-xs text-slate-400 mb-1">Текст статьи *</label>
              <textarea v-model="form.body" required rows="10" class="field w-full resize-y"
                placeholder="Полный текст публикации..."/>
              <p v-if="errors.body" class="text-red-400 text-xs mt-1">{{ errors.body }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs text-slate-400 mb-1">Категория</label>
                <select v-model="form.category" class="field w-full">
                  <option v-for="(label, val) in categories" :key="val" :value="val">{{ label }}</option>
                </select>
              </div>
              <div>
                <label class="block text-xs text-slate-400 mb-1">Дата публикации</label>
                <input v-model="form.published_at" type="datetime-local" class="field w-full"/>
              </div>
            </div>

            <label class="flex items-center gap-3 cursor-pointer">
              <div class="relative w-10 h-5 rounded-full transition-colors"
                :style="form.is_published ? 'background:#C9A96E' : 'background:#334155'"
                @click="form.is_published = !form.is_published">
                <div class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform"
                  :class="form.is_published ? 'translate-x-5' : 'translate-x-0.5'"/>
              </div>
              <span class="text-sm text-slate-300">Опубликовать сразу</span>
            </label>

            <div class="flex gap-3 pt-2">
              <button type="button" @click="closeModal"
                class="flex-1 py-2.5 rounded-xl text-sm text-slate-300 border border-white/10 hover:border-white/20 transition-all">
                Отмена
              </button>
              <button type="submit" :disabled="form.processing"
                class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-[#0F172A] transition-all disabled:opacity-60"
                style="background:#C9A96E">
                {{ form.processing ? 'Сохраняем...' : (modal.editing ? 'Сохранить' : 'Создать') }}
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
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  posts:      { type: Object, default: () => ({ data: [], last_page: 1, links: [] }) },
  categories: { type: Object, default: () => ({}) },
  filters:    { type: Object, default: () => ({}) },
});

const localSearch = ref(props.filters.search || '');
const localCat    = ref(props.filters.category || '');
const modal = reactive({ open: false, editing: null });
const errors = ref({});

const form = useForm({
  title: '', excerpt: '', body: '',
  category: 'news', is_published: false, published_at: '',
});

let searchTimer = null;
function doSearch() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => router.get(route('admin.news.index'), { search: localSearch.value, category: localCat.value }, { preserveState: true, replace: true }), 400);
}
function doFilter() {
  router.get(route('admin.news.index'), { search: localSearch.value, category: localCat.value }, { preserveState: true, replace: true });
}

function openModal(post = null) {
  modal.editing = post;
  modal.open = true;
  errors.value = {};
  if (post) {
    form.title        = post.title;
    form.excerpt      = post.excerpt || '';
    form.body         = post.body;
    form.category     = post.category;
    form.is_published = post.is_published;
    form.published_at = post.published_at ? post.published_at.slice(0, 16) : '';
  } else {
    form.reset();
    form.category = 'news';
  }
}
function closeModal() { modal.open = false; modal.editing = null; }

function submitForm() {
  if (modal.editing) {
    form.put(route('admin.news.update', modal.editing.id), {
      preserveScroll: true,
      onSuccess: closeModal,
      onError: e => { errors.value = e; },
    });
  } else {
    form.post(route('admin.news.store'), {
      preserveScroll: true,
      onSuccess: closeModal,
      onError: e => { errors.value = e; },
    });
  }
}

function deletePost(post) {
  if (!confirm(`Удалить "${post.title}"?`)) return;
  router.delete(route('admin.news.destroy', post.id), { preserveScroll: true });
}

function formatDate(d) {
  if (!d) return '—';
  return new Date(d).toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit', year: 'numeric' });
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
