<template>
  <AdminLayout title="Сделки">
    <Head title="Сделки" />
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
      <div>
        <h1 class="text-xl font-bold text-white">Сделки</h1>
        <p class="text-sm text-gray-400 mt-0.5">Управление сделками и воронкой продаж</p>
      </div>
      <button @click="openCreate = true" class="btn-primary">+ Новая сделка</button>
    </div>

    <!-- Stats row -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
      <div class="card p-4">
        <div class="text-2xl font-black text-white">{{ stats.total }}</div>
        <div class="text-xs text-gray-400 mt-1">Всего</div>
      </div>
      <div class="card p-4">
        <div class="text-2xl font-black text-blue-400">{{ stats.open }}</div>
        <div class="text-xs text-gray-400 mt-1">Открыто</div>
      </div>
      <div class="card p-4">
        <div class="text-2xl font-black text-green-400">{{ stats.won }}</div>
        <div class="text-xs text-gray-400 mt-1">Выиграно</div>
      </div>
      <div class="card p-4">
        <div class="text-2xl font-black text-red-400">{{ stats.lost }}</div>
        <div class="text-xs text-gray-400 mt-1">Проиграно</div>
      </div>
      <div class="card p-4">
        <div class="text-lg font-black text-gold leading-tight">{{ formatMoney(stats.total_value) }}</div>
        <div class="text-xs text-gray-400 mt-1">Сумма открытых</div>
      </div>
    </div>

    <!-- Filters -->
    <div class="card mb-5 p-3">
      <div class="flex flex-wrap gap-2">
        <input v-model="filters.search" @input="applyFilters" type="text"
          placeholder="Поиск по названию…"
          class="input text-sm flex-1 min-w-[160px]" />
        <select v-model="filters.status" @change="applyFilters" class="input text-sm w-full sm:w-36">
          <option value="">Все статусы</option>
          <option value="open">Открыто</option>
          <option value="won">Выиграно</option>
          <option value="lost">Проиграно</option>
          <option value="frozen">Заморожено</option>
        </select>
        <select v-model="filters.pipeline_id" @change="applyFilters" class="input text-sm w-full sm:w-44">
          <option value="">Все воронки</option>
          <option v-for="p in pipelines" :key="p.id" :value="p.id">{{ p.name }}</option>
        </select>
        <select v-model="filters.assigned_to" @change="applyFilters" class="input text-sm w-full sm:w-44">
          <option value="">Все менеджеры</option>
          <option v-for="m in managers" :key="m.id" :value="m.id">{{ m.name }}</option>
        </select>
      </div>
    </div>

    <!-- Mobile cards (< md) -->
    <div class="md:hidden space-y-2 mb-4">
      <div v-if="!deals.data.length" class="card p-8 text-center text-gray-500">
        <div class="text-3xl mb-2">💼</div>
        <p>Сделок не найдено</p>
      </div>
      <div v-for="deal in deals.data" :key="deal.id" class="card p-4">
        <div class="flex items-start justify-between gap-2 mb-2">
          <div class="min-w-0">
            <div class="font-semibold text-white truncate">{{ deal.title }}</div>
            <div v-if="deal.pipeline" class="text-xs text-gray-500">{{ deal.pipeline.name }}</div>
          </div>
          <span class="badge flex-shrink-0" :class="statusBadge(deal.status)">{{ statusLabel(deal.status) }}</span>
        </div>
        <div v-if="deal.stage" class="flex items-center gap-1.5 mb-2">
          <span class="w-2 h-2 rounded-full flex-shrink-0" :style="{background: deal.stage.color}"></span>
          <span class="text-xs text-gray-400">{{ deal.stage.name }}</span>
        </div>
        <div class="flex items-center justify-between mb-3">
          <span v-if="deal.amount" class="text-lg font-bold text-gold">{{ formatMoney(deal.amount, deal.currency) }}</span>
          <span v-else class="text-gray-600 text-sm">Сумма не указана</span>
          <span v-if="deal.expected_close_date" class="text-xs"
            :class="isOverdue(deal.expected_close_date, deal.status) ? 'text-red-400' : 'text-gray-500'">
            До {{ fmtDate(deal.expected_close_date) }}
          </span>
        </div>
        <div class="flex items-center gap-2 pt-3 border-t border-white/5" @click.stop>
          <button @click="startEdit(deal)"
            class="flex-1 py-1.5 text-xs rounded-lg text-gray-300 hover:text-white hover:bg-white/10 transition-colors text-center">
            ✏️ Изменить
          </button>
          <button @click="confirmDelete(deal)"
            class="flex-1 py-1.5 text-xs rounded-lg text-red-400 hover:bg-red-500/10 transition-colors text-center">
            🗑️ Удалить
          </button>
        </div>
      </div>
    </div>

    <!-- Desktop table (md+) -->
    <div class="hidden md:block card overflow-hidden p-0">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-white/5">
              <th class="text-left px-4 py-3 text-xs text-gray-400 font-semibold uppercase tracking-wide">Сделка</th>
              <th class="text-left px-4 py-3 text-xs text-gray-400 font-semibold uppercase tracking-wide hidden lg:table-cell">Этап</th>
              <th class="text-left px-4 py-3 text-xs text-gray-400 font-semibold uppercase tracking-wide hidden lg:table-cell">Лид/Клиент</th>
              <th class="text-right px-4 py-3 text-xs text-gray-400 font-semibold uppercase tracking-wide">Сумма</th>
              <th class="text-left px-4 py-3 text-xs text-gray-400 font-semibold uppercase tracking-wide hidden xl:table-cell">Менеджер</th>
              <th class="text-left px-4 py-3 text-xs text-gray-400 font-semibold uppercase tracking-wide hidden lg:table-cell">Закрытие</th>
              <th class="text-left px-4 py-3 text-xs text-gray-400 font-semibold uppercase tracking-wide">Статус</th>
              <th class="px-4 py-3"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!deals.data.length">
              <td colspan="8" class="text-center py-16 text-gray-500">
                <div class="text-3xl mb-2">💼</div>
                <p>Сделок не найдено</p>
              </td>
            </tr>
            <tr v-for="deal in deals.data" :key="deal.id"
              class="border-b border-white/5 hover:bg-white/3 transition-colors group">
              <td class="px-4 py-3">
                <div class="font-semibold text-white">{{ deal.title }}</div>
                <div v-if="deal.pipeline" class="text-xs text-gray-500 mt-0.5">{{ deal.pipeline.name }}</div>
              </td>
              <td class="px-4 py-3 hidden lg:table-cell">
                <span v-if="deal.stage" class="flex items-center gap-1.5">
                  <span class="w-2 h-2 rounded-full" :style="{background: deal.stage.color}"></span>
                  <span class="text-gray-300 text-xs">{{ deal.stage.name }}</span>
                </span>
                <span v-else class="text-gray-600 text-xs">—</span>
              </td>
              <td class="px-4 py-3 hidden lg:table-cell">
                <div v-if="deal.lead" class="text-gray-300 text-xs">
                  <div class="font-medium text-white">{{ deal.lead.name }}</div>
                  <div class="text-gray-500">{{ deal.lead.phone }}</div>
                </div>
                <span v-else class="text-gray-600 text-xs">—</span>
              </td>
              <td class="px-4 py-3 text-right">
                <span v-if="deal.amount" class="font-bold text-gold">{{ formatMoney(deal.amount, deal.currency) }}</span>
                <span v-else class="text-gray-600">—</span>
              </td>
              <td class="px-4 py-3 hidden xl:table-cell">
                <div v-if="deal.assignee" class="flex items-center gap-1.5">
                  <div class="w-6 h-6 rounded-full bg-gold/20 flex items-center justify-center text-gold text-xs font-bold">
                    {{ deal.assignee.name.charAt(0) }}
                  </div>
                  <span class="text-xs text-gray-300">{{ deal.assignee.name }}</span>
                </div>
              </td>
              <td class="px-4 py-3 hidden lg:table-cell">
                <span v-if="deal.expected_close_date" class="text-xs"
                  :class="isOverdue(deal.expected_close_date, deal.status) ? 'text-red-400' : 'text-gray-400'">
                  {{ fmtDate(deal.expected_close_date) }}
                </span>
                <span v-else class="text-gray-600 text-xs">—</span>
              </td>
              <td class="px-4 py-3">
                <span class="badge" :class="statusBadge(deal.status)">{{ statusLabel(deal.status) }}</span>
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                  <button @click="startEdit(deal)" class="p-1.5 hover:bg-white/10 rounded-lg text-gray-400 hover:text-white transition-colors" title="Редактировать">✏️</button>
                  <button @click="confirmDelete(deal)" class="p-1.5 hover:bg-red-500/10 rounded-lg text-gray-400 hover:text-red-400 transition-colors" title="Удалить">🗑️</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination (desktop) -->
      <div v-if="deals.last_page > 1" class="flex items-center justify-between px-4 py-3 border-t border-white/5">
        <span class="text-sm text-gray-400">Показано {{ deals.from }}–{{ deals.to }} из {{ deals.total }}</span>
        <div class="flex gap-1">
          <Link v-for="link in deals.links" :key="link.label"
            :href="link.url ?? '#'"
            class="px-2.5 py-1 rounded text-xs transition-colors"
            :class="link.active ? 'bg-gold text-dark font-bold' : 'text-gray-400 hover:bg-white/5'"
            >{{ link.label.replace('&laquo;','«').replace('&raquo;','»') }}</Link>
        </div>
      </div>
    </div>

    <!-- Pagination (mobile) -->
    <div v-if="deals.last_page > 1" class="md:hidden flex items-center justify-between mt-3">
      <span class="text-sm text-gray-400">{{ deals.from }}–{{ deals.to }} из {{ deals.total }}</span>
      <div class="flex gap-1">
        <Link v-for="link in deals.links" :key="link.label"
          :href="link.url ?? '#'"
          class="px-2.5 py-1.5 rounded text-xs transition-colors"
          :class="link.active ? 'bg-gold text-dark font-bold' : 'text-gray-400 hover:bg-white/5'"
          >{{ link.label.replace('&laquo;','«').replace('&raquo;','»') }}</Link>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <Teleport to="body">
      <div v-if="modal.open" class="modal-overlay" @click.self="closeModal">
        <div class="modal-box w-full max-w-lg">
          <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-white">{{ modal.deal ? 'Редактировать сделку' : 'Новая сделка' }}</h2>
            <button @click="closeModal" class="text-gray-400 hover:text-white text-xl">×</button>
          </div>
          <form @submit.prevent="submitModal" class="space-y-3">
            <div>
              <label class="block text-xs text-gray-400 mb-1">Название *</label>
              <input v-model="form.title" type="text" class="input w-full" required />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-xs text-gray-400 mb-1">Воронка</label>
                <select v-model="form.pipeline_id" class="input w-full" @change="form.stage_id = null">
                  <option :value="null">Не выбрана</option>
                  <option v-for="p in pipelines" :key="p.id" :value="p.id">{{ p.name }}</option>
                </select>
              </div>
              <div>
                <label class="block text-xs text-gray-400 mb-1">Этап</label>
                <select v-model="form.stage_id" class="input w-full">
                  <option :value="null">Не выбран</option>
                  <option v-for="s in selectedPipelineStages" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>
              </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-xs text-gray-400 mb-1">Сумма</label>
                <input v-model.number="form.amount" type="number" min="0" step="0.01" class="input w-full" placeholder="0.00" />
              </div>
              <div>
                <label class="block text-xs text-gray-400 mb-1">Валюта</label>
                <select v-model="form.currency" class="input w-full">
                  <option value="USD">USD</option>
                  <option value="EUR">EUR</option>
                  <option value="TJS">TJS</option>
                  <option value="RUB">RUB</option>
                </select>
              </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-xs text-gray-400 mb-1">Статус</label>
                <select v-model="form.status" class="input w-full">
                  <option value="open">Открыто</option>
                  <option value="won">Выиграно</option>
                  <option value="lost">Проиграно</option>
                  <option value="frozen">Заморожено</option>
                </select>
              </div>
              <div>
                <label class="block text-xs text-gray-400 mb-1">Дата закрытия</label>
                <input v-model="form.expected_close_date" type="date" class="input w-full" />
              </div>
            </div>
            <div>
              <label class="block text-xs text-gray-400 mb-1">Вероятность (%)</label>
              <input v-model.number="form.probability" type="range" min="0" max="100" class="w-full" />
              <div class="text-xs text-gray-400 text-right">{{ form.probability }}%</div>
            </div>
            <div>
              <label class="block text-xs text-gray-400 mb-1">Менеджер</label>
              <select v-model="form.assigned_to" class="input w-full">
                <option :value="null">Не назначен</option>
                <option v-for="m in managers" :key="m.id" :value="m.id">{{ m.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-xs text-gray-400 mb-1">Заметки</label>
              <textarea v-model="form.notes" rows="2" class="input w-full resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
              <button type="button" @click="closeModal" class="btn-secondary">Отмена</button>
              <button type="submit" class="btn-primary" :disabled="processing">
                {{ processing ? 'Сохранение…' : (modal.deal ? 'Сохранить' : 'Создать') }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Delete confirm -->
      <div v-if="deleteModal.open" class="modal-overlay" @click.self="deleteModal.open = false">
        <div class="modal-box w-full max-w-sm text-center">
          <div class="text-4xl mb-3">🗑️</div>
          <h3 class="text-lg font-bold text-white mb-2">Удалить сделку?</h3>
          <p class="text-sm text-gray-400 mb-5">«{{ deleteModal.deal?.title }}»</p>
          <div class="flex gap-3 justify-center">
            <button @click="deleteModal.open = false" class="btn-secondary">Отмена</button>
            <button @click="doDelete" class="btn-danger" :disabled="processing">Удалить</button>
          </div>
        </div>
      </div>
    </Teleport>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  deals:     Object,
  pipelines: Array,
  managers:  Array,
  filters:   Object,
  stats:     Object,
})

const filters = reactive({
  search:      props.filters?.search      ?? '',
  status:      props.filters?.status      ?? '',
  pipeline_id: props.filters?.pipeline_id ?? '',
  assigned_to: props.filters?.assigned_to ?? '',
})

const openCreate = ref(false)
const processing = ref(false)

const modal = reactive({ open: false, deal: null })
const deleteModal = reactive({ open: false, deal: null })

const form = reactive({
  title: '', pipeline_id: null, stage_id: null,
  amount: null, currency: 'USD', probability: 50,
  status: 'open', expected_close_date: '',
  assigned_to: null, notes: '',
})

const selectedPipelineStages = computed(() => {
  if (!form.pipeline_id) return []
  return props.pipelines?.find(p => p.id === form.pipeline_id)?.stages ?? []
})

function applyFilters() {
  router.get(route('admin.deals.index'), filters, { preserveState: true, replace: true })
}

function startEdit(deal) {
  modal.deal = deal
  Object.assign(form, {
    title:               deal.title,
    pipeline_id:         deal.pipeline_id,
    stage_id:            deal.stage_id,
    amount:              deal.amount,
    currency:            deal.currency || 'USD',
    probability:         deal.probability || 50,
    status:              deal.status,
    expected_close_date: deal.expected_close_date ?? '',
    assigned_to:         deal.assigned_to,
    notes:               deal.notes ?? '',
  })
  modal.open = true
}

function closeModal() {
  modal.open = false
  modal.deal = null
  openCreate.value = false
  Object.assign(form, { title:'', pipeline_id:null, stage_id:null, amount:null, currency:'USD', probability:50, status:'open', expected_close_date:'', assigned_to:null, notes:'' })
}

function submitModal() {
  processing.value = true
  if (modal.deal) {
    router.put(route('admin.deals.update', modal.deal.id), form, {
      onSuccess: () => { closeModal(); processing.value = false },
      onError:   () => { processing.value = false },
    })
  } else {
    router.post(route('admin.deals.store'), form, {
      onSuccess: () => { closeModal(); processing.value = false },
      onError:   () => { processing.value = false },
    })
  }
}

function confirmDelete(deal) {
  deleteModal.deal = deal
  deleteModal.open = true
}

function doDelete() {
  processing.value = true
  router.delete(route('admin.deals.destroy', deleteModal.deal.id), {
    onSuccess: () => { deleteModal.open = false; processing.value = false },
    onError:   () => { processing.value = false },
  })
}

// Helpers
function statusLabel(s) {
  return { open:'Открыто', won:'Выиграно', lost:'Проиграно', frozen:'Заморожено' }[s] ?? s
}
function statusBadge(s) {
  return { open:'badge-blue', won:'badge-green', lost:'badge-red', frozen:'' }[s] ?? ''
}
function formatMoney(amount, currency = 'USD') {
  if (!amount) return '—'
  return new Intl.NumberFormat('ru-RU', { style:'currency', currency, maximumFractionDigits:0 }).format(amount)
}
function fmtDate(dt) {
  if (!dt) return ''
  return new Date(dt).toLocaleDateString('ru-RU', { day:'2-digit', month:'short', year:'numeric' })
}
function isOverdue(dt, status) {
  return dt && status === 'open' && new Date(dt) < new Date()
}
</script>
