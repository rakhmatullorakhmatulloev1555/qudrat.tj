<template>
  <AdminLayout title="Сделки — Канбан">
    <Head title="Сделки — Канбан" />
    <div class="flex flex-col h-full">

      <!-- ── Top bar ───────────────────────────────────────────────────── -->
      <div class="flex-shrink-0 px-6 pt-5 pb-4 border-b border-white/5 space-y-3">

        <!-- Title + actions -->
        <div class="flex items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <h1 class="text-xl font-bold text-white">Сделки</h1>
            <span class="text-xs text-slate-400 bg-white/5 px-2 py-0.5 rounded-full">{{ pipeline.name }}</span>
          </div>
          <div class="flex items-center gap-2">
            <Link :href="route('admin.deals.index')"
              class="text-xs px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-slate-400 hover:text-white transition-colors">
              ☰ Список
            </Link>
            <button @click="openDealModal(null, null)"
              class="flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-lg bg-gold hover:bg-[#b8945a] text-[#0A0E1A] font-semibold transition-colors">
              + Новая сделка
            </button>
          </div>
        </div>

        <!-- KPI row -->
        <div class="flex items-center gap-6 text-sm">
          <div>
            <span class="text-slate-500 text-xs">Всего сделок</span>
            <div class="text-white font-bold">{{ stats.total_deals }}</div>
          </div>
          <div class="w-px h-8 bg-white/10"></div>
          <div>
            <span class="text-slate-500 text-xs">В работе</span>
            <div class="text-gold font-bold">${{ formatMoney(stats.open_value) }}</div>
          </div>
          <div class="w-px h-8 bg-white/10"></div>
          <div>
            <span class="text-slate-500 text-xs">Выиграно</span>
            <div class="text-green-400 font-bold">${{ formatMoney(stats.won_value) }}</div>
          </div>
          <div class="w-px h-8 bg-white/10"></div>
          <div>
            <span class="text-slate-500 text-xs">Закрытых сделок</span>
            <div class="text-green-400 font-bold">{{ stats.won_count }}</div>
          </div>

          <!-- Filters -->
          <div class="ml-auto flex flex-wrap items-center gap-2">
            <!-- Pipeline selector -->
            <select v-if="pipelines.length > 1" v-model="filterPipeline"
              @change="applyFilters"
              class="filter-select w-full sm:w-auto">
              <option v-for="p in pipelines" :key="p.id" :value="p.id">{{ p.name }}</option>
            </select>
            <!-- Status filter -->
            <select v-model="filterStatus" @change="applyFilters" class="filter-select w-full sm:w-auto">
              <option value="">Все статусы</option>
              <option value="open">Открытые</option>
              <option value="won">Выиграны</option>
              <option value="lost">Проиграны</option>
              <option value="frozen">Заморожены</option>
            </select>
            <!-- Search -->
            <input v-model="filterSearch" @input="debouncedApply" placeholder="Поиск..."
              class="filter-select w-full sm:w-40" />
          </div>
        </div>
      </div>

      <!-- ── Kanban Board ───────────────────────────────────────────────── -->
      <div class="flex-1 overflow-x-auto overflow-y-hidden px-6 pt-4 pb-6">
        <div class="flex gap-4 h-full" style="min-width: max-content;">

          <div
            v-for="col in columns" :key="col.id"
            class="kanban-col"
            :style="`--col-color:${col.color}`"
            @dragover.prevent="dragOverCol = col.id"
            @dragleave="dragOverCol = null"
            @drop="onDrop(col)"
            :class="{ 'ring-1 ring-white/15': dragOverCol === col.id }"
          >
            <!-- Column header -->
            <div class="flex items-center justify-between mb-2 px-1">
              <div class="flex items-center gap-2 min-w-0">
                <span class="text-sm font-semibold text-white truncate">{{ col.name }}</span>
                <span class="text-[11px] font-bold px-1.5 py-0.5 rounded-full flex-shrink-0"
                  :style="`background:${col.color}22; color:${col.color}`">{{ col.deals.length }}</span>
                <span v-if="col.is_won"  class="text-[10px] text-green-400 bg-green-400/10 px-1.5 py-0.5 rounded-full flex-shrink-0">Победа</span>
                <span v-if="col.is_lost" class="text-[10px] text-red-400 bg-red-400/10 px-1.5 py-0.5 rounded-full flex-shrink-0">Отказ</span>
              </div>
              <button @click="openDealModal(null, col)"
                class="w-6 h-6 rounded flex items-center justify-center text-slate-500 hover:text-white hover:bg-white/10 transition-colors text-lg leading-none flex-shrink-0 ml-1">
                +
              </button>
            </div>

            <!-- Column amount -->
            <div class="text-xs text-slate-400 mb-3 px-1 font-mono font-semibold">
              ${{ formatMoney(col.total_amount) }}
            </div>

            <!-- Cards -->
            <div class="space-y-2 overflow-y-auto flex-1 pr-0.5" style="max-height: calc(100vh - 230px)">
              <div
                v-for="deal in col.deals" :key="deal.id"
                class="deal-card group"
                draggable="true"
                @dragstart="onDragStart(deal, col)"
                @dragend="draggedDeal = null"
                :class="{
                  'ring-1 ring-green-400/40 bg-green-400/5': deal.status === 'won',
                  'ring-1 ring-red-400/40 bg-red-400/5':    deal.status === 'lost',
                  'opacity-50':                               draggedDeal?.id === deal.id,
                }"
              >
                <!-- Status badge -->
                <div v-if="deal.status !== 'open'" class="flex mb-2">
                  <span :class="deal.status === 'won' ? 'badge-won' : deal.status === 'lost' ? 'badge-lost' : 'badge-frozen'">
                    {{ statusLabel(deal.status) }}
                  </span>
                </div>

                <!-- Title -->
                <div class="font-semibold text-white text-sm mb-1.5 leading-snug pr-4">{{ deal.title }}</div>

                <!-- Amount -->
                <div class="text-gold font-bold text-base mb-2">
                  ${{ formatMoney(deal.amount) }}
                  <span class="text-slate-500 text-xs font-normal ml-1">{{ deal.currency }}</span>
                </div>

                <!-- Lead -->
                <div v-if="deal.lead" class="flex items-center gap-1.5 text-xs text-slate-400 mb-1.5">
                  <span class="text-slate-600">👤</span>
                  <span>{{ deal.lead.name }}</span>
                </div>

                <!-- Probability bar -->
                <div class="mb-2">
                  <div class="flex justify-between text-[10px] text-slate-600 mb-1">
                    <span>Вероятность</span><span>{{ deal.probability }}%</span>
                  </div>
                  <div class="h-1 bg-white/5 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all"
                      :style="{ width: deal.probability + '%', background: col.color }"></div>
                  </div>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-1.5">
                    <!-- Assignee avatar -->
                    <div v-if="deal.assignee"
                      class="w-5 h-5 rounded-full bg-indigo-500/20 flex items-center justify-center text-[9px] font-bold text-indigo-300"
                      :title="deal.assignee.name">
                      {{ deal.assignee.name[0] }}
                    </div>
                    <!-- Close date -->
                    <span v-if="deal.expected_close_date"
                      :class="['text-[10px]', isOverdue(deal) ? 'text-red-400' : 'text-slate-500']">
                      📅 {{ deal.expected_close_date }}
                    </span>
                  </div>

                  <!-- Actions -->
                  <div class="flex gap-1 transition-opacity opacity-100 md:opacity-0 md:group-hover:opacity-100">
                    <button @click.stop="openDealModal(deal, col)"
                      class="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/10 transition-colors text-xs">
                      ✏️
                    </button>
                    <button @click.stop="deleteDeal(deal)"
                      class="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:text-red-400 hover:bg-red-400/10 transition-colors text-xs">
                      🗑️
                    </button>
                  </div>
                </div>
              </div>

              <!-- Empty state -->
              <div v-if="col.deals.length === 0"
                class="border-2 border-dashed border-white/5 rounded-xl p-4 text-center text-slate-600 text-xs">
                Нет сделок
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════
         DEAL MODAL (create / edit)
    ══════════════════════════════════════════════════════════ -->
    <Teleport to="body">
      <div v-if="showModal" class="modal-backdrop" @click.self="showModal = false">
        <div class="modal-box w-full max-w-lg">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-gray-900">
              {{ form.id ? 'Редактировать сделку' : 'Новая сделка' }}
            </h2>
            <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">×</button>
          </div>

          <form @submit.prevent="saveDeal" class="space-y-4">
            <!-- Title -->
            <div>
              <label class="form-label">Название сделки *</label>
              <input v-model="form.title" required class="form-input" placeholder="Квартира 3-комн. — Иванов А." />
            </div>

            <!-- Amount + Currency -->
            <div class="grid grid-cols-3 gap-3">
              <div class="col-span-2">
                <label class="form-label">Сумма</label>
                <input v-model.number="form.amount" type="number" min="0" class="form-input" placeholder="50000" />
              </div>
              <div>
                <label class="form-label">Валюта</label>
                <select v-model="form.currency" class="form-input">
                  <option>USD</option><option>TJS</option><option>RUB</option>
                </select>
              </div>
            </div>

            <!-- Stage -->
            <div>
              <label class="form-label">Стадия</label>
              <select v-model="form.stage_id" class="form-input">
                <option :value="null">— не выбрана —</option>
                <option v-for="col in columns" :key="col.id" :value="col.id">
                  {{ col.name }}
                </option>
              </select>
            </div>

            <!-- Lead -->
            <div>
              <label class="form-label">Лид / Клиент</label>
              <select v-model="form.lead_id" class="form-input">
                <option :value="null">— не привязан —</option>
                <option v-for="l in leads" :key="l.id" :value="l.id">{{ l.name }}</option>
              </select>
            </div>

            <!-- Probability + Expected close -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="form-label">
                  Вероятность
                  <span class="font-bold text-indigo-600 ml-1">{{ form.probability }}%</span>
                </label>
                <input v-model.number="form.probability" type="range" min="0" max="100" step="5"
                  class="w-full accent-indigo-600" />
              </div>
              <div>
                <label class="form-label">Ожидаемое закрытие</label>
                <input v-model="form.expected_close_date" type="date" class="form-input" />
              </div>
            </div>

            <!-- Status + Assignee -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="form-label">Статус</label>
                <select v-model="form.status" class="form-input">
                  <option value="open">Открыта</option>
                  <option value="won">Выиграна</option>
                  <option value="lost">Проиграна</option>
                  <option value="frozen">Заморожена</option>
                </select>
              </div>
              <div>
                <label class="form-label">Менеджер</label>
                <select v-model="form.assigned_to" class="form-input">
                  <option :value="null">— не назначен —</option>
                  <option v-for="m in managers" :key="m.id" :value="m.id">{{ m.name }}</option>
                </select>
              </div>
            </div>

            <!-- Notes -->
            <div>
              <label class="form-label">Комментарий</label>
              <textarea v-model="form.notes" rows="2" class="form-input resize-none"
                placeholder="Дополнительная информация..."></textarea>
            </div>

            <div class="flex gap-3 pt-2">
              <button type="submit" :disabled="saving" class="btn-primary flex-1">
                {{ saving ? 'Сохранение...' : (form.id ? 'Сохранить' : 'Создать сделку') }}
              </button>
              <button type="button" @click="showModal = false" class="btn-secondary flex-1">Отмена</button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, router, Link, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  pipeline:  { type: Object, default: () => ({}) },
  pipelines: { type: Array,  default: () => [] },
  columns:   { type: Array,  default: () => [] },
  managers:  { type: Array,  default: () => [] },
  leads:     { type: Array,  default: () => [] },
  filters:   { type: Object, default: () => ({}) },
  stats:     { type: Object, default: () => ({}) },
})

// ── Helpers ────────────────────────────────────────────────────────────────
function formatMoney(v) {
  if (!v) return '0'
  return Number(v).toLocaleString('ru-RU')
}
function statusLabel(s) {
  return { open: 'Открыта', won: 'Выиграна', lost: 'Проиграна', frozen: 'Заморожена' }[s] ?? s
}
function isOverdue(deal) {
  if (!deal.expected_close_date || deal.status !== 'open') return false
  const [d, m, y] = deal.expected_close_date.split('.')
  return new Date(`${y}-${m}-${d}`) < new Date()
}

// ── Filters ────────────────────────────────────────────────────────────────
const filterPipeline = ref(props.filters.pipeline_id ?? props.pipeline.id)
const filterStatus   = ref(props.filters.status ?? '')
const filterSearch   = ref(props.filters.search ?? '')
let   debounceTimer  = null

function applyFilters() {
  router.get(route('admin.deals.kanban'), {
    pipeline_id: filterPipeline.value || undefined,
    status:      filterStatus.value   || undefined,
    search:      filterSearch.value   || undefined,
  }, { preserveScroll: true, preserveState: true })
}
function debouncedApply() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(applyFilters, 400)
}

// ── Drag & Drop ────────────────────────────────────────────────────────────
const draggedDeal = ref(null)
const draggedCol  = ref(null)
const dragOverCol = ref(null)

function onDragStart(deal, col) {
  draggedDeal.value = deal
  draggedCol.value  = col
}
function onDrop(targetCol) {
  dragOverCol.value = null
  if (!draggedDeal.value || draggedCol.value?.id === targetCol.id) return

  router.patch(
    route('admin.deals.move-stage', draggedDeal.value.id),
    { stage_id: targetCol.id },
    { preserveScroll: true, preserveState: false }
  )
  draggedDeal.value = null
  draggedCol.value  = null
}

// ── Deal Modal ─────────────────────────────────────────────────────────────
const showModal = ref(false)
const saving    = ref(false)
const form = ref({
  id: null, title: '', amount: null, currency: 'USD',
  stage_id: null, lead_id: null, probability: 20,
  expected_close_date: '', status: 'open', assigned_to: null, notes: '',
})

function openDealModal(deal, col) {
  if (deal) {
    form.value = {
      id:                   deal.id,
      title:                deal.title,
      amount:               deal.amount,
      currency:             deal.currency ?? 'USD',
      stage_id:             deal.stage?.id ?? col?.id ?? null,
      lead_id:              deal.lead?.id ?? null,
      probability:          deal.probability ?? 20,
      expected_close_date:  deal.expected_close_date
        ? deal.expected_close_date.split('.').reverse().join('-')
        : '',
      status:               deal.status ?? 'open',
      assigned_to:          deal.assignee?.id ?? null,
      notes:                deal.notes ?? '',
    }
  } else {
    form.value = {
      id: null, title: '', amount: null, currency: 'USD',
      stage_id: col?.id ?? null, lead_id: null, probability: col?.probability ?? 20,
      expected_close_date: '', status: 'open', assigned_to: null, notes: '',
    }
  }
  showModal.value = true
}

function saveDeal() {
  saving.value = true
  const payload = {
    title:                form.value.title,
    amount:               form.value.amount,
    currency:             form.value.currency,
    pipeline_id:          props.pipeline.id,
    stage_id:             form.value.stage_id,
    lead_id:              form.value.lead_id,
    probability:          form.value.probability,
    expected_close_date:  form.value.expected_close_date || null,
    status:               form.value.status,
    assigned_to:          form.value.assigned_to,
    notes:                form.value.notes,
  }

  const isEdit = !!form.value.id
  const url    = isEdit
    ? route('admin.deals.update', form.value.id)
    : route('admin.deals.store')

  router[isEdit ? 'put' : 'post'](url, payload, {
    preserveScroll: true,
    preserveState:  false,
    onSuccess: () => { showModal.value = false },
    onFinish:  () => { saving.value = false },
  })
}

function deleteDeal(deal) {
  if (!confirm(`Удалить сделку «${deal.title}»?`)) return
  router.delete(route('admin.deals.destroy', deal.id), { preserveScroll: true, preserveState: false })
}
</script>

<style scoped>
@reference "tailwindcss";

/* Board */
.kanban-col {
  @apply flex flex-col w-72 flex-shrink-0 rounded-2xl p-3 transition-all;
  background: #141C2B;
  border-top: 3px solid var(--col-color, #C9A96E);
  box-shadow: 0 2px 12px rgba(0,0,0,0.25);
}

/* Card */
.deal-card {
  @apply border rounded-xl p-3 cursor-grab transition-all duration-150 select-none;
  background: #1E293B;
  border-color: rgba(255,255,255,0.08);
}
.deal-card:hover {
  background: #243349;
  border-color: rgba(255,255,255,0.16);
  transform: translateY(-1px);
}

/* Filters */
.filter-select {
  @apply bg-white/5 border border-white/10 rounded-lg px-3 py-1.5 text-sm text-slate-300
         focus:outline-none focus:ring-1 focus:ring-white/20 transition-colors;
}
.filter-select option { background: #1e293b; color: #e2e8f0; }

/* Status badges */
.badge-won    { @apply text-[10px] font-semibold px-2 py-0.5 rounded-full bg-green-400/15 text-green-400; }
.badge-lost   { @apply text-[10px] font-semibold px-2 py-0.5 rounded-full bg-red-400/15 text-red-400; }
.badge-frozen { @apply text-[10px] font-semibold px-2 py-0.5 rounded-full bg-blue-400/15 text-blue-400; }

/* Modal */
.modal-backdrop {
  @apply fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4;
}
.modal-box {
  @apply bg-white rounded-2xl shadow-2xl p-6 max-h-[90vh] overflow-y-auto;
  color: #111827;
}
.form-label { @apply block text-sm font-medium mb-1.5; color: #374151; }
.form-input {
  @apply w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm
         focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow;
  background: #ffffff;
  color: #111827;
}
.form-input::placeholder { color: #9CA3AF; }
.form-input option { color: #111827; background: #ffffff; }
.btn-primary   { @apply bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition-colors disabled:opacity-50; }
.btn-secondary { @apply bg-gray-100 hover:bg-gray-200 text-sm font-medium px-4 py-2.5 rounded-xl transition-colors; color: #374151; }
</style>
