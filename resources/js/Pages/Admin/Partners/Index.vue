<template>
  <AdminLayout page-title="Партнёры" page-subtitle="Управление деловыми партнёрами">
    <Head title="Партнёры" />

    <!-- Stat cards -->
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
      <div v-for="s in statCards" :key="s.label"
        class="rounded-xl p-5 border border-white/6" style="background:#1E293B">
        <div class="text-slate-400 text-[10px] uppercase tracking-widest mb-1">{{ s.label }}</div>
        <div class="text-white font-bold text-2xl">{{ s.value }}</div>
      </div>
    </div>

    <!-- Toolbar -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
      <div class="flex flex-wrap items-center gap-3 flex-1">
        <div class="relative flex-1 min-w-[160px]">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
          </svg>
          <input v-model="search" @input="doSearch" type="text" placeholder="Поиск по названию, стране..."
            class="pl-9 pr-4 py-2.5 text-sm rounded-xl text-white placeholder-slate-500 outline-none w-full"
            style="background:#1E293B; border:1px solid rgba(255,255,255,0.08)"/>
        </div>
        <select v-model="typeFilter" @change="doFilter"
          class="px-4 py-2.5 text-sm rounded-xl text-slate-300 outline-none w-full sm:w-auto"
          style="background:#1E293B; border:1px solid rgba(255,255,255,0.08)">
          <option value="">Все типы</option>
          <option value="buyer">Покупатель</option>
          <option value="supplier">Поставщик</option>
          <option value="investor">Инвестор</option>
          <option value="contractor">Подрядчик</option>
          <option value="government">Гос. структура</option>
        </select>
        <select v-model="statusFilter" @change="doFilter"
          class="px-4 py-2.5 text-sm rounded-xl text-slate-300 outline-none w-full sm:w-auto"
          style="background:#1E293B; border:1px solid rgba(255,255,255,0.08)">
          <option value="">Все статусы</option>
          <option value="active">Активный</option>
          <option value="negotiation">Переговоры</option>
          <option value="expired">Истёк</option>
          <option value="terminated">Расторгнут</option>
        </select>
      </div>
      <button @click="openModal()"
        class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm text-[#0F172A] flex-shrink-0"
        style="background:#C9A96E">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Добавить партнёра
      </button>
    </div>

    <!-- Mobile cards -->
    <div class="md:hidden space-y-2 mb-4">
      <div v-if="partners.data.length === 0" class="rounded-xl p-6 text-center text-slate-500 text-sm" style="background:#1E293B; border:1px solid rgba(255,255,255,0.06)">
        Партнёров пока нет
      </div>
      <div v-for="p in partners.data" :key="p.id"
        class="rounded-xl p-4 border border-white/6" style="background:#1E293B">
        <div class="flex items-start justify-between gap-3 mb-3">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg overflow-hidden flex items-center justify-center flex-shrink-0"
              style="background:#0F172A; border:1px solid rgba(255,255,255,0.08)">
              <img v-if="p.logo" :src="p.logo" :alt="p.name" class="w-full h-full object-contain p-1" />
              <span v-else class="text-gold font-bold text-sm">{{ p.name.charAt(0).toUpperCase() }}</span>
            </div>
            <div>
              <div class="text-white text-sm font-semibold">{{ p.name }}</div>
              <div v-if="p.website" class="text-slate-500 text-xs">{{ p.website }}</div>
            </div>
          </div>
          <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold flex-shrink-0" :class="contractStatusClass(p.contract_status)">
            {{ contractStatusLabel(p.contract_status) }}
          </span>
        </div>
        <div class="flex flex-wrap items-center gap-2 mb-3">
          <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold" :class="typeClass(p.type)">{{ typeLabel(p.type) }}</span>
          <span v-if="p.country" class="text-slate-400 text-xs">🌍 {{ p.country }}</span>
          <span v-if="p.annual_volume" class="text-gold text-xs font-semibold">
            {{ Number(p.annual_volume).toLocaleString() }} {{ p.currency }}/год
          </span>
        </div>
        <div v-if="p.contact_person || p.phone" class="text-slate-400 text-xs mb-3">
          <span v-if="p.contact_person">{{ p.contact_person }}</span>
          <span v-if="p.phone" class="ml-2">{{ p.phone }}</span>
        </div>
        <div class="flex gap-2">
          <button @click="openModal(p)"
            class="flex-1 py-2 rounded-lg text-xs font-medium text-gold border border-gold/30 hover:bg-gold/10 transition-all">
            Изменить
          </button>
          <button @click="deletePartner(p)"
            class="px-4 py-2 rounded-lg text-xs font-medium text-red-400 border border-red-500/20 hover:bg-red-500/10 transition-all">
            Удалить
          </button>
        </div>
      </div>
      <!-- Mobile pagination -->
      <div v-if="partners.last_page > 1" class="flex justify-center gap-1 pt-2">
        <Link v-for="link in partners.links" :key="link.label"
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
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500 w-10"></th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Компания</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500 hidden lg:table-cell">Страна</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Тип</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500 hidden lg:table-cell">Контакт</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500 hidden xl:table-cell">Объём/год</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500 hidden xl:table-cell">Договор</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Статус</th>
            <th class="text-right px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="partners.data.length === 0">
            <td colspan="8" class="px-5 py-12 text-center text-slate-500 text-sm">Партнёров пока нет</td>
          </tr>
          <tr v-for="p in partners.data" :key="p.id"
            class="border-b border-white/4 hover:bg-white/3 transition-colors">
            <td class="px-3 py-3">
              <div class="w-9 h-9 rounded-lg overflow-hidden flex-shrink-0 flex items-center justify-center"
                style="background:#0F172A; border:1px solid rgba(255,255,255,0.08)">
                <img v-if="p.logo" :src="p.logo" :alt="p.name" class="w-full h-full object-contain p-1" />
                <span v-else class="text-gold font-bold text-xs">{{ p.name.charAt(0).toUpperCase() }}</span>
              </div>
            </td>
            <td class="px-5 py-3">
              <div>
                <div class="text-white text-sm font-medium">{{ p.name }}</div>
                <div v-if="p.website" class="text-slate-500 text-xs">{{ p.website }}</div>
              </div>
            </td>
            <td class="px-5 py-3 hidden lg:table-cell">
              <span class="text-slate-300 text-sm">{{ p.country }}</span>
            </td>
            <td class="px-5 py-3">
              <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold" :class="typeClass(p.type)">
                {{ typeLabel(p.type) }}
              </span>
            </td>
            <td class="px-5 py-3 hidden lg:table-cell">
              <div class="text-slate-300 text-sm">{{ p.contact_person || '—' }}</div>
              <div v-if="p.phone" class="text-slate-500 text-xs">{{ p.phone }}</div>
            </td>
            <td class="px-5 py-3 text-sm hidden xl:table-cell">
              <div v-if="p.annual_volume" class="text-gold font-semibold">
                {{ Number(p.annual_volume).toLocaleString() }} {{ p.currency }}
              </div>
              <div v-else class="text-slate-600">—</div>
            </td>
            <td class="px-5 py-3 text-slate-500 text-xs hidden xl:table-cell">
              {{ p.partnership_since ? new Date(p.partnership_since).getFullYear() + ' г.' : '—' }}
            </td>
            <td class="px-5 py-3">
              <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold" :class="contractStatusClass(p.contract_status)">
                {{ contractStatusLabel(p.contract_status) }}
              </span>
            </td>
            <td class="px-5 py-3 text-right">
              <div class="flex items-center justify-end gap-2">
                <button @click="openModal(p)" class="p-1.5 rounded-lg text-slate-400 hover:text-gold hover:bg-gold/10 transition-all">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                </button>
                <button @click="deletePartner(p)" class="p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-all">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="partners.last_page > 1" class="flex items-center justify-between px-5 py-3 border-t border-white/6">
        <span class="text-slate-500 text-xs">Всего: {{ partners.total }}</span>
        <div class="flex gap-1">
          <Link v-for="link in partners.links" :key="link.label"
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
              <h3 class="font-bold text-white text-lg">{{ editing ? 'Редактировать партнёра' : 'Новый партнёр' }}</h3>
              <button @click="modal = false" class="text-slate-400 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
              </button>
            </div>
            <form @submit.prevent="save" class="space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="col-span-1 sm:col-span-2">
                  <label class="form-label">Название компании *</label>
                  <input v-model="form.name" type="text" class="form-input" required />
                </div>
                <div>
                  <label class="form-label">Страна *</label>
                  <input v-model="form.country" type="text" class="form-input" required placeholder="Таджикистан" />
                </div>
                <div>
                  <label class="form-label">Тип партнёра</label>
                  <select v-model="form.type" class="form-input">
                    <option value="buyer">Покупатель</option>
                    <option value="supplier">Поставщик</option>
                    <option value="investor">Инвестор</option>
                    <option value="contractor">Подрядчик</option>
                    <option value="government">Гос. структура</option>
                  </select>
                </div>
                <div>
                  <label class="form-label">Контактное лицо</label>
                  <input v-model="form.contact_person" type="text" class="form-input" />
                </div>
                <div>
                  <label class="form-label">Телефон</label>
                  <input v-model="form.phone" type="text" class="form-input" />
                </div>
                <div>
                  <label class="form-label">Email</label>
                  <input v-model="form.email" type="email" class="form-input" />
                </div>
                <div>
                  <label class="form-label">Сайт</label>
                  <input v-model="form.website" type="text" class="form-input" />
                </div>
                <!-- Logo picker -->
                <div class="col-span-1 sm:col-span-2">
                  <label class="form-label">Логотип</label>
                  <div v-if="availableLogos.length" class="grid grid-cols-4 sm:grid-cols-7 gap-2 mb-2">
                    <button
                      v-for="logo in availableLogos" :key="logo.filename"
                      type="button"
                      @click="form.logo = form.logo === logo.url ? '' : logo.url"
                      class="rounded-lg p-1.5 transition-all flex items-center justify-center aspect-square"
                      :style="form.logo === logo.url
                        ? 'background:#C9A96E22; border:2px solid #C9A96E'
                        : 'background:#0F172A; border:2px solid rgba(255,255,255,0.08)'"
                      :title="logo.filename"
                    >
                      <img :src="logo.url" :alt="logo.filename" class="w-full h-full object-contain" />
                    </button>
                    <button
                      type="button"
                      @click="form.logo = ''"
                      class="rounded-lg p-1.5 transition-all flex items-center justify-center aspect-square text-slate-500 text-xs"
                      :style="!form.logo ? 'background:#C9A96E22; border:2px solid #C9A96E; color:#C9A96E' : 'background:#0F172A; border:2px solid rgba(255,255,255,0.08)'"
                      title="Без логотипа"
                    >✕</button>
                  </div>
                  <div v-if="form.logo" class="flex items-center gap-2 mt-1">
                    <img :src="form.logo" class="w-8 h-8 object-contain rounded" />
                    <span class="text-slate-400 text-xs">{{ form.logo }}</span>
                  </div>
                </div>
                <div>
                  <label class="form-label">Статус договора</label>
                  <select v-model="form.contract_status" class="form-input">
                    <option value="active">Активный</option>
                    <option value="negotiation">Переговоры</option>
                    <option value="expired">Истёк</option>
                    <option value="terminated">Расторгнут</option>
                  </select>
                </div>
                <div>
                  <label class="form-label">Партнёрство с</label>
                  <input v-model="form.partnership_since" type="date" class="form-input" />
                </div>
                <div>
                  <label class="form-label">Годовой объём</label>
                  <input v-model="form.annual_volume" type="number" step="0.01" class="form-input" />
                </div>
                <div>
                  <label class="form-label">Валюта</label>
                  <select v-model="form.currency" class="form-input">
                    <option value="USD">USD</option>
                    <option value="TJS">TJS</option>
                    <option value="RUB">RUB</option>
                  </select>
                </div>
                <div class="col-span-1 sm:col-span-2">
                  <label class="form-label">Примечания</label>
                  <textarea v-model="form.notes" rows="2" class="form-input"></textarea>
                </div>
                <div class="col-span-1 sm:col-span-2 flex items-center gap-3">
                  <input id="is_active" v-model="form.is_active" type="checkbox" class="w-4 h-4 rounded" />
                  <label for="is_active" class="text-sm text-slate-300 cursor-pointer">Активный партнёр</label>
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
import { ref, computed } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ partners: Object, stats: Object, filters: Object, availableLogos: Array });

const modal = ref(false);
const editing = ref(null);
const search = ref(props.filters?.search || '');
const typeFilter = ref(props.filters?.type || '');
const statusFilter = ref(props.filters?.status || '');

const form = useForm({
  name:'', country:'', type:'buyer', contact_person:'', phone:'', email:'',
  website:'', logo:'', contract_status:'negotiation', partnership_since:'',
  annual_volume:'', currency:'USD', notes:'', is_active:true,
});

const statCards = computed(() => [
  { label: 'Всего партнёров',   value: props.stats?.total || 0 },
  { label: 'Активные договоры', value: props.stats?.active || 0 },
  { label: 'Покупатели',        value: props.stats?.buyers || 0 },
  { label: 'Оборот (USD/год)',  value: '$' + Number(props.stats?.total_volume || 0).toLocaleString() },
]);

function openModal(p = null) {
  editing.value = p;
  if (p) { Object.keys(form).forEach(k => { if (k in p) form[k] = p[k] ?? ''; }); form.is_active = !!p.is_active; }
  else { form.reset(); form.type = 'buyer'; form.contract_status = 'negotiation'; form.currency = 'USD'; form.is_active = true; }
  modal.value = true;
}
function save() {
  const opts = { onSuccess: () => { modal.value = false; } };
  editing.value ? form.put(route('admin.partners.update', editing.value.id), opts)
                : form.post(route('admin.partners.store'), opts);
}
function deletePartner(p) {
  if (confirm(`Удалить партнёра "${p.name}"?`)) router.delete(route('admin.partners.destroy', p.id));
}
let t;
function doSearch() { clearTimeout(t); t = setTimeout(doFilter, 400); }
function doFilter() {
  router.get(route('admin.partners.index'), { search: search.value, type: typeFilter.value, status: statusFilter.value }, { preserveState: true });
}
function typeLabel(t) { return { buyer:'Покупатель', supplier:'Поставщик', investor:'Инвестор', contractor:'Подрядчик', government:'Гос. структура' }[t] || t; }
function typeClass(t) { return { buyer:'bg-blue-500/15 text-blue-400', supplier:'bg-purple-500/15 text-purple-400', investor:'bg-gold/15 text-gold', contractor:'bg-orange-500/15 text-orange-400', government:'bg-emerald-500/15 text-emerald-400' }[t] || ''; }
function contractStatusLabel(s) { return { active:'Активный', negotiation:'Переговоры', expired:'Истёк', terminated:'Расторгнут' }[s] || s; }
function contractStatusClass(s) { return { active:'bg-emerald-500/15 text-emerald-400', negotiation:'bg-amber-500/15 text-amber-400', expired:'bg-red-500/15 text-red-400', terminated:'bg-slate-500/15 text-slate-400' }[s] || ''; }
</script>

<style scoped>
.form-label { display:block; font-size:0.75rem; font-weight:500; color:#94A3B8; margin-bottom:0.375rem; }
.form-input { display:block; width:100%; padding:0.625rem 0.75rem; border-radius:0.75rem; font-size:0.875rem; color:#fff; background:#0F172A; border:1px solid rgba(255,255,255,0.08); outline:none; transition:border-color 0.2s; }
.form-input:focus { border-color:rgba(201,169,110,0.5); }
</style>
