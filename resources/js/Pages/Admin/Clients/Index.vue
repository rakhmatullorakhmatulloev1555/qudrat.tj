<template>
  <AdminLayout page-title="Клиенты" page-subtitle="CRM — база клиентов">
    <Head title="Клиенты" />

    <!-- Toolbar -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
      <div class="flex flex-wrap items-center gap-2 flex-1">
        <div class="relative flex-1 min-w-[160px] sm:flex-none sm:w-64">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
          </svg>
          <input v-model="search" @input="doSearch" type="text" placeholder="Поиск клиента..."
            class="pl-9 pr-4 py-2.5 text-sm rounded-xl text-white placeholder-slate-500 outline-none w-full"
            style="background:#1E293B; border:1px solid rgba(255,255,255,0.08)"/>
        </div>
        <select v-model="statusFilter" @change="doFilter"
          class="px-4 py-2.5 text-sm rounded-xl text-slate-300 outline-none w-full sm:w-auto"
          style="background:#1E293B; border:1px solid rgba(255,255,255,0.08)">
          <option value="">Все статусы</option>
          <option value="lead">Лид</option>
          <option value="active">Активный</option>
          <option value="vip">VIP</option>
          <option value="inactive">Неактивный</option>
        </select>
      </div>
      <button @click="openModal()" class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm text-[#0F172A] flex-shrink-0"
        style="background:#C9A96E">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Добавить клиента
      </button>
    </div>

    <!-- Mobile cards (< md) -->
    <div class="md:hidden space-y-2 mb-4">
      <div v-if="clients.data.length === 0" class="rounded-xl p-8 text-center text-slate-500 text-sm" style="background:#1E293B; border:1px solid rgba(255,255,255,0.06)">
        Клиентов пока нет
      </div>
      <div v-for="client in clients.data" :key="client.id"
        class="rounded-xl p-4" style="background:#1E293B; border:1px solid rgba(255,255,255,0.06)">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 rounded-full bg-gold/20 flex items-center justify-center text-gold text-sm font-bold flex-shrink-0">
            {{ client.name.charAt(0).toUpperCase() }}
          </div>
          <div class="min-w-0 flex-1">
            <div class="text-white text-sm font-medium truncate">{{ client.name }}</div>
            <div v-if="client.assignee" class="text-slate-500 text-xs">→ {{ client.assignee.name }}</div>
          </div>
          <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold flex-shrink-0" :class="clientStatusClass(client.status)">
            {{ clientStatusLabel(client.status) }}
          </span>
        </div>
        <div class="text-slate-300 text-sm mb-1">{{ client.phone }}</div>
        <div v-if="client.email" class="text-slate-500 text-xs mb-2 truncate">{{ client.email }}</div>
        <div class="flex items-center gap-2 text-xs text-slate-500 mb-3">
          <span>{{ client.type === 'individual' ? 'Физлицо' : 'Компания' }}</span>
          <span>·</span>
          <span>{{ sourceLabel(client.source) }}</span>
        </div>
        <div class="flex items-center gap-2 pt-3 border-t border-white/5">
          <button @click="openModal(client)"
            class="flex-1 py-1.5 text-xs rounded-lg text-slate-300 hover:text-white hover:bg-white/10 transition-colors text-center">
            ✏️ Изменить
          </button>
          <button @click="deleteClient(client)"
            class="flex-1 py-1.5 text-xs rounded-lg text-red-400 hover:bg-red-500/10 transition-colors text-center">
            🗑️ Удалить
          </button>
        </div>
      </div>
    </div>

    <!-- Desktop table (md+) -->
    <div class="hidden md:block rounded-xl border border-white/6 overflow-hidden" style="background:#1E293B">
      <table class="w-full">
        <thead>
          <tr class="border-b border-white/6">
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Клиент</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Контакты</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500 hidden lg:table-cell">Тип</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Статус</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500 hidden lg:table-cell">Источник</th>
            <th class="text-right px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="clients.data.length === 0">
            <td colspan="6" class="px-5 py-12 text-center text-slate-500 text-sm">Клиентов пока нет</td>
          </tr>
          <tr v-for="client in clients.data" :key="client.id"
            class="border-b border-white/4 hover:bg-white/3 transition-colors">
            <td class="px-5 py-3">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-gold/20 flex items-center justify-center text-gold text-xs font-bold flex-shrink-0">
                  {{ client.name.charAt(0).toUpperCase() }}
                </div>
                <div>
                  <div class="text-white text-sm font-medium">{{ client.name }}</div>
                  <div v-if="client.assignee" class="text-slate-500 text-xs">→ {{ client.assignee.name }}</div>
                </div>
              </div>
            </td>
            <td class="px-5 py-3">
              <div class="text-slate-300 text-sm">{{ client.phone }}</div>
              <div v-if="client.email" class="text-slate-500 text-xs">{{ client.email }}</div>
            </td>
            <td class="px-5 py-3 text-slate-400 text-sm hidden lg:table-cell">{{ client.type === 'individual' ? 'Физлицо' : 'Компания' }}</td>
            <td class="px-5 py-3">
              <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold" :class="clientStatusClass(client.status)">
                {{ clientStatusLabel(client.status) }}
              </span>
            </td>
            <td class="px-5 py-3 text-slate-400 text-sm hidden lg:table-cell">{{ sourceLabel(client.source) }}</td>
            <td class="px-5 py-3 text-right">
              <div class="flex items-center justify-end gap-2">
                <button @click="openModal(client)" class="p-1.5 rounded-lg text-slate-400 hover:text-gold hover:bg-gold/10 transition-all">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                </button>
                <button @click="deleteClient(client)" class="p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-all">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <Teleport to="body">
      <Transition enter-active-class="transition-all duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100"
        leave-active-class="transition-all duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="modal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.7)">
          <div class="w-full max-w-lg rounded-2xl p-6 shadow-2xl" style="background:#1E293B; border:1px solid rgba(255,255,255,0.1)">
            <div class="flex items-center justify-between mb-5">
              <h3 class="font-bold text-white text-lg">{{ editingClient ? 'Редактировать клиента' : 'Новый клиент' }}</h3>
              <button @click="modal = false" class="text-slate-400 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
              </button>
            </div>
            <form @submit.prevent="save" class="space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="col-span-2">
                  <label class="form-label">Имя *</label>
                  <input v-model="form.name" type="text" class="form-input" required />
                </div>
                <div>
                  <label class="form-label">Телефон *</label>
                  <input v-model="form.phone" type="text" class="form-input" required />
                </div>
                <div>
                  <label class="form-label">Email</label>
                  <input v-model="form.email" type="email" class="form-input" />
                </div>
                <div>
                  <label class="form-label">Тип</label>
                  <select v-model="form.type" class="form-input">
                    <option value="individual">Физлицо</option>
                    <option value="company">Компания</option>
                  </select>
                </div>
                <div>
                  <label class="form-label">Статус</label>
                  <select v-model="form.status" class="form-input">
                    <option value="lead">Лид</option>
                    <option value="active">Активный</option>
                    <option value="vip">VIP</option>
                    <option value="inactive">Неактивный</option>
                  </select>
                </div>
                <div>
                  <label class="form-label">Источник</label>
                  <select v-model="form.source" class="form-input">
                    <option value="website">Сайт</option>
                    <option value="phone">Телефон</option>
                    <option value="referral">Рекомендация</option>
                    <option value="social">Соцсети</option>
                    <option value="other">Другое</option>
                  </select>
                </div>
                <div>
                  <label class="form-label">Дата рождения</label>
                  <input v-model="form.birth_date" type="date" class="form-input" />
                </div>
                <div class="col-span-2">
                  <label class="form-label">Адрес</label>
                  <input v-model="form.address" type="text" class="form-input" />
                </div>
                <div class="col-span-2">
                  <label class="form-label">Заметки</label>
                  <textarea v-model="form.notes" rows="2" class="form-input"></textarea>
                </div>
              </div>
              <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="modal = false"
                  class="px-5 py-2.5 rounded-xl text-sm text-slate-400 hover:text-white" style="background:rgba(255,255,255,0.05)">
                  Отмена
                </button>
                <button type="submit" :disabled="form.processing"
                  class="px-6 py-2.5 rounded-xl text-sm font-semibold text-[#0F172A] disabled:opacity-60"
                  style="background:#C9A96E">
                  {{ editingClient ? 'Сохранить' : 'Добавить' }}
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

const props = defineProps({ clients: Object, managers: Array, filters: Object });

const modal = ref(false);
const editingClient = ref(null);
const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');

const form = useForm({ name:'', phone:'', email:'', address:'', birth_date:'', type:'individual', status:'active', source:'website', notes:'' });

function openModal(client = null) {
  editingClient.value = client;
  if (client) { Object.keys(form).forEach(k => { if (k in client) form[k] = client[k] ?? ''; }); }
  else { form.reset(); form.type='individual'; form.status='active'; form.source='website'; }
  modal.value = true;
}
function save() {
  const opts = { onSuccess: () => { modal.value = false; } };
  editingClient.value ? form.put(route('admin.clients.update', editingClient.value.id), opts) : form.post(route('admin.clients.store'), opts);
}
function deleteClient(c) {
  if (confirm(`Удалить клиента "${c.name}"?`)) router.delete(route('admin.clients.destroy', c.id));
}
let t; function doSearch() { clearTimeout(t); t = setTimeout(doFilter, 400); }
function doFilter() { router.get(route('admin.clients.index'), { search: search.value, status: statusFilter.value }, { preserveState: true }); }
function clientStatusLabel(s) { return { lead:'Лид', active:'Активный', vip:'VIP', inactive:'Неактивный' }[s]||s; }
function clientStatusClass(s) { return { lead:'bg-blue-500/15 text-blue-400', active:'bg-emerald-500/15 text-emerald-400', vip:'bg-gold/15 text-gold', inactive:'bg-slate-500/15 text-slate-400' }[s]||''; }
function sourceLabel(s) { return { website:'Сайт', phone:'Телефон', referral:'Рекомендация', social:'Соцсети', other:'Другое' }[s]||s; }
</script>

<style scoped>
.form-label { display:block; font-size:0.75rem; font-weight:500; color:#94A3B8; margin-bottom:0.375rem; }
.form-input { display:block; width:100%; padding:0.625rem 0.75rem; border-radius:0.75rem; font-size:0.875rem; color:#fff; background:#0F172A; border:1px solid rgba(255,255,255,0.08); outline:none; transition:border-color 0.2s; }
.form-input:focus { border-color:rgba(201,169,110,0.5); }
</style>
