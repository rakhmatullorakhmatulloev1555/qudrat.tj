<template>
  <AdminLayout page-title="Горнодобыча" page-subtitle="Учёт партий угля и поставок">
    <Head title="Горнодобыча" />

    <!-- Stat cards -->
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
      <div v-for="s in statCards" :key="s.label"
        class="rounded-xl p-5 border border-white/6" style="background:#1E293B">
        <div class="text-slate-400 text-[10px] uppercase tracking-widest mb-1">{{ s.label }}</div>
        <div class="text-white font-bold text-2xl">{{ s.value }}</div>
        <div v-if="s.sub" class="text-slate-500 text-xs mt-1">{{ s.sub }}</div>
      </div>
    </div>

    <!-- Toolbar -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
      <div class="flex flex-wrap items-center gap-3 flex-1">
        <select v-model="statusFilter" @change="doFilter"
          class="px-4 py-2.5 text-sm rounded-xl text-slate-300 outline-none w-full sm:w-auto"
          style="background:#1E293B; border:1px solid rgba(255,255,255,0.08)">
          <option value="">Все статусы</option>
          <option value="planned">Запланировано</option>
          <option value="loading">Погрузка</option>
          <option value="shipped">Отгружено</option>
          <option value="delivered">Доставлено</option>
          <option value="paid">Оплачено</option>
        </select>
        <select v-model="typeFilter" @change="doFilter"
          class="px-4 py-2.5 text-sm rounded-xl text-slate-300 outline-none w-full sm:w-auto"
          style="background:#1E293B; border:1px solid rgba(255,255,255,0.08)">
          <option value="">Все типы</option>
          <option value="energy">Энергетический</option>
          <option value="coking">Коксующийся</option>
          <option value="anthracite">Антрацит</option>
        </select>
      </div>
      <button @click="openModal()"
        class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm text-[#0F172A]"
        style="background:#C9A96E">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Добавить партию
      </button>
    </div>

    <!-- Mobile cards (md:hidden) -->
    <div class="md:hidden space-y-3 mb-5">
      <div v-if="shipments.data.length === 0" class="px-5 py-10 text-center text-slate-500 text-sm rounded-xl border border-white/6" style="background:#1E293B">Партий пока нет</div>
      <div v-for="s in shipments.data" :key="s.id"
        class="rounded-xl border border-white/6 p-4 space-y-2" style="background:#1E293B">
        <!-- Row 1: date + coal type badge + site -->
        <div class="flex items-start justify-between gap-2">
          <div>
            <div class="text-slate-400 text-xs mb-0.5">{{ formatDate(s.date) }}</div>
            <div class="text-white text-sm font-medium">{{ s.site }}</div>
            <div v-if="s.destination" class="text-slate-500 text-xs">→ {{ s.destination }}</div>
          </div>
          <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold flex-shrink-0" :class="coalTypeClass(s.coal_type)">
            {{ coalTypeLabel(s.coal_type) }}
          </span>
        </div>
        <!-- Row 2: volume + price -->
        <div class="flex items-center gap-4">
          <div>
            <div class="text-white font-bold text-sm">{{ Number(s.volume).toLocaleString() }} т</div>
            <div v-if="s.quality_kcal" class="text-slate-500 text-xs">{{ s.quality_kcal }} ккал/кг</div>
          </div>
          <div v-if="s.price_per_ton" class="ml-auto text-right">
            <div class="text-gold font-semibold text-sm">{{ Number(s.volume * s.price_per_ton).toLocaleString() }} {{ s.currency }}</div>
            <div class="text-slate-500 text-xs">{{ s.price_per_ton }}/т</div>
          </div>
        </div>
        <!-- Row 3: buyer + status + actions -->
        <div class="flex items-center justify-between gap-2 pt-1">
          <div class="flex items-center gap-2">
            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold" :class="statusClass(s.status)">
              {{ statusLabel(s.status) }}
            </span>
            <span v-if="s.buyer" class="text-slate-400 text-xs truncate max-w-[120px]">{{ s.buyer }}</span>
          </div>
          <div class="flex items-center gap-1">
            <button @click="openModal(s)" class="p-1.5 rounded-lg text-slate-400 hover:text-gold hover:bg-gold/10 transition-all">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
            </button>
            <button @click="deleteShipment(s)" class="p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-all">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Table (hidden on mobile) -->
    <div class="hidden md:block rounded-xl border border-white/6 overflow-hidden" style="background:#1E293B">
      <table class="w-full">
        <thead>
          <tr class="border-b border-white/6">
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Дата</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Шахта / Источник</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Тип угля</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Объём (т)</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Стоимость</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500 hidden lg:table-cell">Покупатель</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Статус</th>
            <th class="text-right px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="shipments.data.length === 0">
            <td colspan="8" class="px-5 py-12 text-center text-slate-500 text-sm">Партий пока нет</td>
          </tr>
          <tr v-for="s in shipments.data" :key="s.id"
            class="border-b border-white/4 hover:bg-white/3 transition-colors">
            <td class="px-5 py-3 text-slate-400 text-sm">{{ formatDate(s.date) }}</td>
            <td class="px-5 py-3">
              <div class="text-white text-sm font-medium">{{ s.site }}</div>
              <div v-if="s.destination" class="text-slate-500 text-xs">→ {{ s.destination }}</div>
            </td>
            <td class="px-5 py-3">
              <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold" :class="coalTypeClass(s.coal_type)">
                {{ coalTypeLabel(s.coal_type) }}
              </span>
            </td>
            <td class="px-5 py-3">
              <div class="text-white font-bold text-sm">{{ Number(s.volume).toLocaleString() }} т</div>
              <div v-if="s.quality_kcal" class="text-slate-500 text-xs">{{ s.quality_kcal }} ккал/кг</div>
            </td>
            <td class="px-5 py-3 text-sm">
              <div v-if="s.price_per_ton" class="text-gold font-semibold">
                {{ Number(s.volume * s.price_per_ton).toLocaleString() }} {{ s.currency }}
              </div>
              <div v-if="s.price_per_ton" class="text-slate-500 text-xs">{{ s.price_per_ton }}/т</div>
              <div v-else class="text-slate-600">—</div>
            </td>
            <td class="px-5 py-3 text-slate-300 text-sm hidden lg:table-cell">{{ s.buyer || '—' }}</td>
            <td class="px-5 py-3">
              <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold" :class="statusClass(s.status)">
                {{ statusLabel(s.status) }}
              </span>
            </td>
            <td class="px-5 py-3 text-right">
              <div class="flex items-center justify-end gap-2">
                <button @click="openModal(s)" class="p-1.5 rounded-lg text-slate-400 hover:text-gold hover:bg-gold/10 transition-all">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                </button>
                <button @click="deleteShipment(s)" class="p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-all">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div v-if="shipments.last_page > 1" class="flex items-center justify-between px-5 py-3 border-t border-white/6">
        <span class="text-slate-500 text-xs">Всего: {{ shipments.total }}</span>
        <div class="flex gap-1">
          <Link v-for="link in shipments.links" :key="link.label"
            :href="link.url || '#'"
            :class="['px-3 py-1.5 text-xs rounded-lg transition-colors',
              link.active ? 'bg-gold text-[#0F172A] font-bold' : 'text-slate-400 hover:text-white hover:bg-white/5',
              !link.url ? 'opacity-30 pointer-events-none' : '']"
            >{{ link.label.replace('&laquo;','«').replace('&raquo;','»') }}</Link>
        </div>
      </div>
    </div><!-- end table wrapper -->

    <!-- Modal -->
    <Teleport to="body">
      <Transition enter-active-class="transition-all duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100"
        leave-active-class="transition-all duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" style="background:rgba(0,0,0,0.7)">
          <div class="w-full max-w-xl rounded-2xl p-6 shadow-2xl my-auto" style="background:#1E293B; border:1px solid rgba(255,255,255,0.1)">
            <div class="flex items-center justify-between mb-5">
              <h3 class="font-bold text-white text-lg">{{ editing ? 'Редактировать партию' : 'Новая партия' }}</h3>
              <button @click="modal = false" class="text-slate-400 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
              </button>
            </div>
            <form @submit.prevent="save" class="space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="form-label">Дата *</label>
                  <input v-model="form.date" type="date" class="form-input" required />
                </div>
                <div>
                  <label class="form-label">Статус</label>
                  <select v-model="form.status" class="form-input">
                    <option value="planned">Запланировано</option>
                    <option value="loading">Погрузка</option>
                    <option value="shipped">Отгружено</option>
                    <option value="delivered">Доставлено</option>
                    <option value="paid">Оплачено</option>
                  </select>
                </div>
                <div class="col-span-1 sm:col-span-2">
                  <label class="form-label">Шахта / Источник *</label>
                  <input v-model="form.site" type="text" class="form-input" required placeholder="Шахта №1, Согдийская обл." />
                </div>
                <div>
                  <label class="form-label">Тип угля</label>
                  <select v-model="form.coal_type" class="form-input">
                    <option value="energy">Энергетический</option>
                    <option value="coking">Коксующийся</option>
                    <option value="anthracite">Антрацит</option>
                  </select>
                </div>
                <div>
                  <label class="form-label">Объём (тонн) *</label>
                  <input v-model="form.volume" type="number" step="0.01" class="form-input" required />
                </div>
                <div>
                  <label class="form-label">Цена за тонну</label>
                  <input v-model="form.price_per_ton" type="number" step="0.01" class="form-input" />
                </div>
                <div>
                  <label class="form-label">Валюта</label>
                  <select v-model="form.currency" class="form-input">
                    <option value="USD">USD</option>
                    <option value="TJS">TJS</option>
                    <option value="RUB">RUB</option>
                  </select>
                </div>
                <div>
                  <label class="form-label">Теплота (ккал/кг)</label>
                  <input v-model="form.quality_kcal" type="number" class="form-input" placeholder="5500" />
                </div>
                <div>
                  <label class="form-label">Покупатель</label>
                  <input v-model="form.buyer" type="text" class="form-input" />
                </div>
                <div class="col-span-1 sm:col-span-2">
                  <label class="form-label">Пункт назначения</label>
                  <input v-model="form.destination" type="text" class="form-input" />
                </div>
                <div class="col-span-1 sm:col-span-2">
                  <label class="form-label">Примечания</label>
                  <textarea v-model="form.notes" rows="2" class="form-input"></textarea>
                </div>
              </div>
              <!-- Total preview -->
              <div v-if="form.volume && form.price_per_ton"
                class="flex items-center justify-between px-4 py-3 rounded-xl border border-gold/20"
                style="background:rgba(201,169,110,0.05)">
                <span class="text-slate-400 text-sm">Итого стоимость:</span>
                <span class="text-gold font-bold">
                  {{ Number(form.volume * form.price_per_ton).toLocaleString() }} {{ form.currency }}
                </span>
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

const props = defineProps({ shipments: Object, stats: Object, filters: Object });

const modal = ref(false);
const editing = ref(null);
const statusFilter = ref(props.filters?.status || '');
const typeFilter   = ref(props.filters?.coal_type || '');

const form = useForm({
  date:'', site:'', coal_type:'energy', volume:'', price_per_ton:'',
  currency:'USD', buyer:'', destination:'', status:'planned', quality_kcal:'', notes:'',
});

const statCards = computed(() => [
  { label: 'Всего добыто (т)',    value: Number(props.stats?.total_volume || 0).toLocaleString(), sub: 'за все время' },
  { label: 'Оборот (USD)',        value: '$' + Number(props.stats?.total_value || 0).toLocaleString(), sub: 'сумма сделок' },
  { label: 'В этом месяце (т)',   value: Number(props.stats?.this_month || 0).toLocaleString(), sub: '' },
  { label: 'Доставлено партий',   value: props.stats?.delivered || 0, sub: 'выполнено' },
]);

function openModal(s = null) {
  editing.value = s;
  if (s) { Object.keys(form).forEach(k => { if (k in s) form[k] = s[k] ?? ''; }); }
  else { form.reset(); form.coal_type = 'energy'; form.status = 'planned'; form.currency = 'USD'; }
  modal.value = true;
}
function save() {
  const opts = { onSuccess: () => { modal.value = false; } };
  editing.value ? form.put(route('admin.mining.update', editing.value.id), opts)
                : form.post(route('admin.mining.store'), opts);
}
function deleteShipment(s) {
  if (confirm(`Удалить партию от ${formatDate(s.date)}?`)) router.delete(route('admin.mining.destroy', s.id));
}
function doFilter() {
  router.get(route('admin.mining.index'), { status: statusFilter.value, coal_type: typeFilter.value }, { preserveState: true });
}
function formatDate(d) { return d ? new Date(d).toLocaleDateString('ru-RU') : '—'; }
function coalTypeLabel(t) { return { energy:'Энергетический', coking:'Коксующийся', anthracite:'Антрацит' }[t] || t; }
function coalTypeClass(t) { return { energy:'bg-amber-500/15 text-amber-400', coking:'bg-blue-500/15 text-blue-400', anthracite:'bg-slate-500/15 text-slate-300' }[t] || ''; }
function statusLabel(s) { return { planned:'Запланировано', loading:'Погрузка', shipped:'Отгружено', delivered:'Доставлено', paid:'Оплачено' }[s] || s; }
function statusClass(s) { return { planned:'bg-slate-500/15 text-slate-400', loading:'bg-yellow-500/15 text-yellow-400', shipped:'bg-blue-500/15 text-blue-400', delivered:'bg-emerald-500/15 text-emerald-400', paid:'bg-gold/15 text-gold' }[s] || ''; }
</script>

<style scoped>
.form-label { display:block; font-size:0.75rem; font-weight:500; color:#94A3B8; margin-bottom:0.375rem; }
.form-input { display:block; width:100%; padding:0.625rem 0.75rem; border-radius:0.75rem; font-size:0.875rem; color:#fff; background:#0F172A; border:1px solid rgba(255,255,255,0.08); outline:none; transition:border-color 0.2s; }
.form-input:focus { border-color:rgba(201,169,110,0.5); }
</style>
