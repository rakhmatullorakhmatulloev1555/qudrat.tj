<template>
  <AdminLayout page-title="Документооборот" page-subtitle="Управление договорами, актами и сертификатами">
    <Head title="Документы" />

    <!-- Stat cards -->
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
      <div class="rounded-xl p-5 border border-white/6" style="background:#1E293B">
        <div class="text-slate-400 text-[10px] uppercase tracking-widest mb-1">Всего документов</div>
        <div class="text-white font-bold text-2xl">{{ stats?.total || 0 }}</div>
      </div>
      <div class="rounded-xl p-5 border border-white/6" style="background:#1E293B">
        <div class="text-slate-400 text-[10px] uppercase tracking-widest mb-1">Активных</div>
        <div class="text-emerald-400 font-bold text-2xl">{{ stats?.active || 0 }}</div>
      </div>
      <div class="rounded-xl p-5 border border-white/6" style="background:#1E293B">
        <div class="text-slate-400 text-[10px] uppercase tracking-widest mb-1">Истекают (30 дн.)</div>
        <div class="font-bold text-2xl" :class="(stats?.expiring || 0) > 0 ? 'text-red-400' : 'text-slate-400'">
          {{ stats?.expiring || 0 }}
        </div>
      </div>
      <div class="rounded-xl p-5 border border-white/6" style="background:#1E293B">
        <div class="text-slate-400 text-[10px] uppercase tracking-widest mb-1">Черновики</div>
        <div class="text-slate-300 font-bold text-2xl">{{ stats?.draft || 0 }}</div>
      </div>
    </div>

    <!-- Expiry warning -->
    <div v-if="(stats?.expiring || 0) > 0"
      class="flex items-center gap-3 mb-5 px-4 py-3 rounded-xl border border-red-500/30 bg-red-500/8 text-red-400 text-sm">
      <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
      </svg>
      <span>Внимание: {{ stats.expiring }} {{ stats.expiring === 1 ? 'документ истекает' : 'документа истекают' }} в ближайшие 30 дней</span>
    </div>

    <!-- Toolbar -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
      <div class="flex flex-wrap items-center gap-3 flex-1">
        <div class="relative flex-1 min-w-[160px]">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
          </svg>
          <input v-model="search" @input="doSearch" type="text" placeholder="Поиск по названию..."
            class="pl-9 pr-4 py-2.5 text-sm rounded-xl text-white placeholder-slate-500 outline-none w-full"
            style="background:#1E293B; border:1px solid rgba(255,255,255,0.08)"/>
        </div>
        <select v-model="typeFilter" @change="doFilter"
          class="px-4 py-2.5 text-sm rounded-xl text-slate-300 outline-none w-full sm:w-auto"
          style="background:#1E293B; border:1px solid rgba(255,255,255,0.08)">
          <option value="">Все типы</option>
          <option value="contract">Договор</option>
          <option value="invoice">Счёт</option>
          <option value="certificate">Сертификат</option>
          <option value="permit">Разрешение</option>
          <option value="act">Акт</option>
          <option value="report">Отчёт</option>
          <option value="power_of_attorney">Доверенность</option>
          <option value="other">Другое</option>
        </select>
        <select v-model="statusFilter" @change="doFilter"
          class="px-4 py-2.5 text-sm rounded-xl text-slate-300 outline-none w-full sm:w-auto"
          style="background:#1E293B; border:1px solid rgba(255,255,255,0.08)">
          <option value="">Все статусы</option>
          <option value="draft">Черновик</option>
          <option value="review">На согласовании</option>
          <option value="signed">Подписан</option>
          <option value="active">Действующий</option>
          <option value="expired">Истёк</option>
          <option value="archived">Архив</option>
        </select>
        <select v-model="relatedFilter" @change="doFilter"
          class="px-4 py-2.5 text-sm rounded-xl text-slate-300 outline-none w-full sm:w-auto"
          style="background:#1E293B; border:1px solid rgba(255,255,255,0.08)">
          <option value="">Все разделы</option>
          <option value="client">Клиент</option>
          <option value="partner">Партнёр</option>
          <option value="project">Проект</option>
          <option value="apartment">Квартира</option>
          <option value="mining">Горнодобыча</option>
        </select>
      </div>
      <button @click="openModal()"
        class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm text-[#0F172A] flex-shrink-0"
        style="background:#C9A96E">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Добавить документ
      </button>
    </div>

    <!-- Mobile cards -->
    <div class="md:hidden space-y-2 mb-4">
      <div v-if="documents.data.length === 0" class="rounded-xl p-6 text-center text-slate-500 text-sm" style="background:#1E293B; border:1px solid rgba(255,255,255,0.06)">
        Документов пока нет
      </div>
      <div v-for="d in documents.data" :key="d.id"
        class="rounded-xl p-4 border border-white/6"
        :class="isExpiring(d) ? 'border-red-500/30 bg-red-500/3' : ''"
        style="background:#1E293B">
        <div class="flex items-start justify-between gap-3 mb-2">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" :class="typeIconBg(d.type)">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z"/>
              </svg>
            </div>
            <div>
              <div class="text-white text-sm font-semibold">{{ d.title }}</div>
              <div v-if="d.notes" class="text-slate-500 text-xs truncate max-w-[200px]">{{ d.notes }}</div>
            </div>
          </div>
          <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold flex-shrink-0" :class="docStatusClass(d.status)">
            {{ docStatusLabel(d.status) }}
          </span>
        </div>
        <div class="flex flex-wrap gap-2 mb-2">
          <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold" :class="typeClass(d.type)">{{ typeLabel(d.type) }}</span>
          <span v-if="d.related_name" class="text-slate-400 text-xs">{{ d.related_name }}</span>
        </div>
        <div v-if="d.issued_at || d.expires_at" class="text-xs mb-3">
          <span v-if="d.issued_at" class="text-slate-500">Выдан: {{ formatDate(d.issued_at) }}</span>
          <span v-if="d.expires_at" class="ml-2" :class="isExpiring(d) ? 'text-red-400 font-semibold' : 'text-slate-500'">
            до {{ formatDate(d.expires_at) }}{{ isExpiring(d) ? ' ⚠️' : '' }}
          </span>
        </div>
        <div class="flex gap-2">
          <button @click="openModal(d)"
            class="flex-1 py-2 rounded-lg text-xs font-medium text-gold border border-gold/30 hover:bg-gold/10 transition-all">
            Изменить
          </button>
          <button @click="deleteDocument(d)"
            class="px-4 py-2 rounded-lg text-xs font-medium text-red-400 border border-red-500/20 hover:bg-red-500/10 transition-all">
            Удалить
          </button>
        </div>
      </div>
      <!-- Mobile pagination -->
      <div v-if="documents.last_page > 1" class="flex justify-center gap-1 pt-2">
        <Link v-for="link in documents.links" :key="link.label"
          :href="link.url || '#'"
          :class="['px-3 py-1.5 text-xs rounded-lg transition-colors',
            link.active ? 'bg-gold text-[#0F172A] font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5',
            !link.url ? 'opacity-30 pointer-events-none' : '']"
          >{{ link.label.replace('&laquo;','«').replace('&raquo;','»') }}</Link>
      </div>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block rounded-xl border border-white/6 overflow-hidden" style="background:#1E293B">
      <table class="w-full">
        <thead>
          <tr class="border-b border-white/6">
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Документ</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500 hidden lg:table-cell">Тип</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500 hidden lg:table-cell">Связан с</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500 hidden xl:table-cell">Подписан</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500 hidden xl:table-cell">Выдан / Истекает</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Статус</th>
            <th class="text-right px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="documents.data.length === 0">
            <td colspan="7" class="px-5 py-12 text-center text-slate-500 text-sm">Документов пока нет</td>
          </tr>
          <tr v-for="d in documents.data" :key="d.id"
            class="border-b border-white/4 hover:bg-white/3 transition-colors"
            :class="isExpiring(d) ? 'bg-red-500/3' : ''">
            <td class="px-5 py-3">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                  :class="typeIconBg(d.type)">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z"/>
                  </svg>
                </div>
                <div>
                  <div class="text-white text-sm font-medium">{{ d.title }}</div>
                  <div v-if="d.notes" class="text-slate-500 text-xs truncate max-w-[200px]">{{ d.notes }}</div>
                </div>
              </div>
            </td>
            <td class="px-5 py-3 hidden lg:table-cell">
              <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold" :class="typeClass(d.type)">
                {{ typeLabel(d.type) }}
              </span>
            </td>
            <td class="px-5 py-3 text-sm hidden lg:table-cell">
              <div v-if="d.related_name" class="text-slate-300">{{ d.related_name }}</div>
              <div v-if="d.related_type" class="text-slate-500 text-xs">{{ relatedTypeLabel(d.related_type) }}</div>
              <div v-else class="text-slate-600">—</div>
            </td>
            <td class="px-5 py-3 text-slate-400 text-sm hidden xl:table-cell">{{ d.signed_by || '—' }}</td>
            <td class="px-5 py-3 text-xs hidden xl:table-cell">
              <div class="text-slate-400">{{ d.issued_at ? formatDate(d.issued_at) : '—' }}</div>
              <div v-if="d.expires_at" :class="isExpiring(d) ? 'text-red-400 font-semibold' : 'text-slate-500'">
                до {{ formatDate(d.expires_at) }}
                <span v-if="isExpiring(d)" class="ml-1">⚠️</span>
              </div>
            </td>
            <td class="px-5 py-3">
              <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold" :class="docStatusClass(d.status)">
                {{ docStatusLabel(d.status) }}
              </span>
            </td>
            <td class="px-5 py-3 text-right">
              <div class="flex items-center justify-end gap-2">
                <button @click="openModal(d)" class="p-1.5 rounded-lg text-slate-400 hover:text-gold hover:bg-gold/10 transition-all">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                </button>
                <button @click="deleteDocument(d)" class="p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-all">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="documents.last_page > 1" class="flex items-center justify-between px-5 py-3 border-t border-white/6">
        <span class="text-slate-500 text-xs">Всего: {{ documents.total }}</span>
        <div class="flex gap-1">
          <Link v-for="link in documents.links" :key="link.label"
            :href="link.url || '#'"
            :class="['px-3 py-1.5 text-xs rounded-lg transition-colors',
              link.active ? 'bg-gold text-[#0F172A] font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5',
              !link.url ? 'opacity-30 pointer-events-none' : '']"
            >{{ link.label.replace('&laquo;','«').replace('&raquo;','»') }}</Link>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <Teleport to="body">
      <Transition enter-active-class="transition-all duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100"
        leave-active-class="transition-all duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" style="background:rgba(0,0,0,0.7)">
          <div class="w-full max-w-xl rounded-2xl p-6 shadow-2xl my-auto" style="background:#1E293B; border:1px solid rgba(255,255,255,0.1)">
            <div class="flex items-center justify-between mb-5">
              <h3 class="font-bold text-white text-lg">{{ editing ? 'Редактировать документ' : 'Новый документ' }}</h3>
              <button @click="modal = false" class="text-slate-400 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
              </button>
            </div>
            <form @submit.prevent="save" class="space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="col-span-1 sm:col-span-2">
                  <label class="form-label">Название документа *</label>
                  <input v-model="form.title" type="text" class="form-input" required placeholder="Договор поставки угля №..." />
                </div>
                <div>
                  <label class="form-label">Тип документа</label>
                  <select v-model="form.type" class="form-input">
                    <option value="contract">Договор</option>
                    <option value="invoice">Счёт</option>
                    <option value="certificate">Сертификат</option>
                    <option value="permit">Разрешение</option>
                    <option value="act">Акт</option>
                    <option value="report">Отчёт</option>
                    <option value="power_of_attorney">Доверенность</option>
                    <option value="other">Другое</option>
                  </select>
                </div>
                <div>
                  <label class="form-label">Статус</label>
                  <select v-model="form.status" class="form-input">
                    <option value="draft">Черновик</option>
                    <option value="review">На согласовании</option>
                    <option value="signed">Подписан</option>
                    <option value="active">Действующий</option>
                    <option value="expired">Истёк</option>
                    <option value="archived">Архив</option>
                  </select>
                </div>
                <div>
                  <label class="form-label">Связан с (раздел)</label>
                  <select v-model="form.related_type" class="form-input">
                    <option value="">— Не указано —</option>
                    <option value="client">Клиент</option>
                    <option value="partner">Партнёр</option>
                    <option value="project">Проект</option>
                    <option value="apartment">Квартира</option>
                    <option value="mining">Горнодобыча</option>
                  </select>
                </div>
                <div>
                  <label class="form-label">Название объекта</label>
                  <input v-model="form.related_name" type="text" class="form-input" placeholder="Иванов И.И. / Проект X" />
                </div>
                <div>
                  <label class="form-label">Дата выдачи</label>
                  <input v-model="form.issued_at" type="date" class="form-input" />
                </div>
                <div>
                  <label class="form-label">Срок действия</label>
                  <input v-model="form.expires_at" type="date" class="form-input" />
                </div>
                <div class="col-span-1 sm:col-span-2">
                  <label class="form-label">Подписан кем</label>
                  <input v-model="form.signed_by" type="text" class="form-input" placeholder="Директор Рахмонов А.А." />
                </div>
                <div class="col-span-1 sm:col-span-2">
                  <label class="form-label">Примечания</label>
                  <textarea v-model="form.notes" rows="2" class="form-input"></textarea>
                </div>
              </div>
              <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="modal = false" class="px-5 py-2.5 rounded-xl text-sm text-slate-400 hover:text-white" style="background:rgba(255,255,255,0.05)">Отмена</button>
                <button type="submit" :disabled="form.processing" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-[#0F172A] disabled:opacity-60" style="background:#C9A96E">
                  {{ editing ? 'Сохранить' : 'Добавить' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ documents: Object, stats: Object, filters: Object });

const modal = ref(false);
const editing = ref(null);
const search = ref(props.filters?.search || '');
const typeFilter = ref(props.filters?.type || '');
const statusFilter = ref(props.filters?.status || '');
const relatedFilter = ref(props.filters?.related_type || '');

const form = useForm({
  title:'', type:'contract', related_type:'', related_id:'', related_name:'',
  status:'draft', issued_at:'', expires_at:'', signed_by:'', notes:'',
});

function openModal(d = null) {
  editing.value = d;
  if (d) { Object.keys(form).forEach(k => { if (k in d) form[k] = d[k] ?? ''; }); }
  else { form.reset(); form.type = 'contract'; form.status = 'draft'; }
  modal.value = true;
}
function save() {
  const opts = { onSuccess: () => { modal.value = false; } };
  editing.value ? form.put(route('admin.documents.update', editing.value.id), opts)
                : form.post(route('admin.documents.store'), opts);
}
function deleteDocument(d) {
  if (confirm(`Удалить документ "${d.title}"?`)) router.delete(route('admin.documents.destroy', d.id));
}
let t;
function doSearch() { clearTimeout(t); t = setTimeout(doFilter, 400); }
function doFilter() {
  router.get(route('admin.documents.index'), {
    search: search.value, type: typeFilter.value,
    status: statusFilter.value, related_type: relatedFilter.value,
  }, { preserveState: true });
}

function isExpiring(d) {
  if (!d.expires_at) return false;
  const diff = (new Date(d.expires_at) - new Date()) / (1000 * 60 * 60 * 24);
  return diff >= 0 && diff <= 30;
}
function formatDate(d) { return d ? new Date(d).toLocaleDateString('ru-RU') : '—'; }
function typeLabel(t) {
  return { contract:'Договор', invoice:'Счёт', certificate:'Сертификат', permit:'Разрешение', act:'Акт', report:'Отчёт', power_of_attorney:'Доверенность', other:'Другое' }[t] || t;
}
function typeClass(t) {
  return { contract:'bg-blue-500/15 text-blue-400', invoice:'bg-emerald-500/15 text-emerald-400', certificate:'bg-gold/15 text-gold', permit:'bg-purple-500/15 text-purple-400', act:'bg-orange-500/15 text-orange-400', report:'bg-cyan-500/15 text-cyan-400', power_of_attorney:'bg-pink-500/15 text-pink-400', other:'bg-slate-500/15 text-slate-400' }[t] || '';
}
function typeIconBg(t) {
  return { contract:'bg-blue-500/10 text-blue-400', invoice:'bg-emerald-500/10 text-emerald-400', certificate:'bg-gold/10 text-gold', permit:'bg-purple-500/10 text-purple-400' }[t] || 'bg-slate-500/10 text-slate-400';
}
function docStatusLabel(s) {
  return { draft:'Черновик', review:'Согласование', signed:'Подписан', active:'Действующий', expired:'Истёк', archived:'Архив' }[s] || s;
}
function docStatusClass(s) {
  return { draft:'bg-slate-500/15 text-slate-400', review:'bg-amber-500/15 text-amber-400', signed:'bg-blue-500/15 text-blue-400', active:'bg-emerald-500/15 text-emerald-400', expired:'bg-red-500/15 text-red-400', archived:'bg-slate-600/15 text-slate-500' }[s] || '';
}
function relatedTypeLabel(t) {
  return { client:'Клиент', partner:'Партнёр', project:'Проект', apartment:'Квартира', mining:'Горнодобыча' }[t] || t;
}
</script>

<style scoped>
.form-label { display:block; font-size:0.75rem; font-weight:500; color:#94A3B8; margin-bottom:0.375rem; }
.form-input { display:block; width:100%; padding:0.625rem 0.75rem; border-radius:0.75rem; font-size:0.875rem; color:#fff; background:#0F172A; border:1px solid rgba(255,255,255,0.08); outline:none; transition:border-color 0.2s; }
.form-input:focus { border-color:rgba(201,169,110,0.5); }
</style>
