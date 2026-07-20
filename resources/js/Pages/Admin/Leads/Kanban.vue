<template>
  <AdminLayout title="Kanban — Заявки">
    <Head title="Заявки — Kanban" />

    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
      <div>
        <h1 class="text-xl font-bold text-white">Kanban-доска</h1>
        <p class="text-sm text-gray-400 mt-0.5">{{ pipeline?.name ?? 'Воронка продаж' }}</p>
      </div>
      <div class="flex items-center gap-2">
        <Link :href="route('admin.leads.index')" class="btn-secondary text-sm px-3 py-1.5">
          ☰ Таблица
        </Link>
        <button @click="openCreate = true" class="btn-primary text-sm px-3 py-1.5">
          + Новая заявка
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-2 mb-5">
      <input v-model="filters.search" @input="applyFilters" type="text"
        placeholder="Поиск по имени / телефону…"
        class="input text-sm flex-1 min-w-[160px]" />
      <select v-model="filters.temperature" @change="applyFilters" class="input text-sm w-full sm:w-36">
        <option value="">Все температуры</option>
        <option value="cold">❄️ Низкий интерес</option>
        <option value="warm">🌤 Средний интерес</option>
        <option value="hot">🔥 Высокий интерес</option>
      </select>
      <select v-model="filters.assigned_to" @change="applyFilters" class="input text-sm w-full sm:w-44">
        <option value="">Все менеджеры</option>
        <option v-for="m in managers" :key="m.id" :value="m.id">{{ m.name }}</option>
      </select>
    </div>

    <!-- Board -->
    <div v-if="columns.length" class="flex gap-3 overflow-x-auto pb-6" style="min-height:calc(100vh - 290px)">
      <div
        v-for="col in columns" :key="col.id"
        class="kanban-column"
        :style="`--col-color:${col.color}`"
      >
        <!-- Column header -->
        <div class="kanban-col-header">
          <div class="flex items-center gap-2 min-w-0">
            <span class="text-sm font-semibold text-white truncate">{{ col.name }}</span>
            <span class="kanban-count flex-shrink-0"
              :style="`background:${col.color}22; color:${col.color}`">
              {{ col.leads.length }}
            </span>
          </div>
          <span class="text-[11px] text-gray-500 font-medium flex-shrink-0 ml-2">{{ col.probability }}%</span>
        </div>

        <!-- Drop zone -->
        <draggable
          :list="col.leads"
          group="leads"
          item-key="id"
          class="kanban-dropzone"
          :class="dragOverCol === col.id ? 'drag-over' : ''"
          @start="onDragStart"
          @end="(e) => onDragEnd(e, col)"
          @dragenter="dragOverCol = col.id"
          @dragleave="dragOverCol = null"
        >
          <template #item="{ element: lead }">
            <div
              class="kanban-card"
              :style="`border-left-color:${tempColor(lead.temperature)}`"
              @click="openLead(lead)"
            >
              <!-- Name + temp icon -->
              <div class="flex items-start justify-between gap-1 mb-1.5">
                <span class="text-sm font-semibold text-white leading-tight">{{ lead.name }}</span>
                <span class="text-base leading-none flex-shrink-0" :title="tempLabel(lead.temperature)">
                  {{ tempIcon(lead.temperature) }}
                </span>
              </div>
              <!-- Phone -->
              <div class="text-xs text-gray-400 mb-2">{{ lead.phone }}</div>
              <!-- Tags -->
              <div class="flex flex-wrap gap-1 mb-2">
                <span v-if="lead.interest" class="badge badge-blue text-[10px]">{{ lead.interest }}</span>
                <span v-if="lead.budget_range" class="badge badge-gold text-[10px]">{{ lead.budget_range }}</span>
                <span v-if="lead.source" class="badge text-[10px]">{{ lead.source }}</span>
              </div>
              <!-- Footer: assignee + score -->
              <div class="flex items-center justify-between">
                <div v-if="lead.assignee" class="flex items-center gap-1.5">
                  <div class="w-5 h-5 rounded-full bg-gold/20 flex items-center justify-center text-gold text-[9px] font-bold">
                    {{ lead.assignee.name.charAt(0) }}
                  </div>
                  <span class="text-[10px] text-gray-500 truncate max-w-[80px]">{{ lead.assignee.name }}</span>
                </div>
                <div v-else></div>
                <div class="flex items-center gap-1.5">
                  <div class="w-14 h-1 bg-white/10 rounded-full overflow-hidden">
                    <div class="h-full rounded-full" :class="scoreColor(lead.score)" :style="{width: lead.score + '%'}"></div>
                  </div>
                  <span class="text-[10px] text-gray-500">{{ lead.score }}</span>
                </div>
              </div>
              <!-- Follow-up -->
              <div v-if="lead.next_follow_up_at" class="mt-2 text-[10px] flex items-center gap-1"
                :class="isOverdue(lead.next_follow_up_at) ? 'text-red-400' : 'text-gray-500'">
                🕐 {{ fmtDate(lead.next_follow_up_at) }}
              </div>
            </div>
          </template>
        </draggable>

        <!-- Add button -->
        <button @click="quickCreate(col)" class="kanban-add-btn">
          + Добавить заявку
        </button>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else class="flex flex-col items-center justify-center py-24 text-gray-500">
      <div class="text-5xl mb-4">📋</div>
      <p class="text-lg font-semibold text-gray-400">Воронка не настроена</p>
      <p class="text-sm mt-1">Создайте pipeline в настройках CRM</p>
    </div>

    <!-- Create Lead Modal -->
    <Teleport to="body">
      <div v-if="openCreate" class="modal-overlay" @click.self="closeCreate">
        <div class="modal-box w-full max-w-lg">
          <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-white">Новая заявка</h2>
            <button @click="closeCreate" class="text-gray-400 hover:text-white text-xl">×</button>
          </div>
          <form @submit.prevent="submitCreate" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-xs text-gray-400 mb-1">Имя *</label>
                <input v-model="form.name" type="text" class="input w-full" required />
              </div>
              <div>
                <label class="block text-xs text-gray-400 mb-1">Телефон *</label>
                <input v-model="form.phone" type="text" class="input w-full" required />
              </div>
            </div>
            <div>
              <label class="block text-xs text-gray-400 mb-1">Email</label>
              <input v-model="form.email" type="email" class="input w-full" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-xs text-gray-400 mb-1">Источник</label>
                <select v-model="form.source" class="input w-full">
                  <option value="website">Сайт</option>
                  <option value="phone">Телефон</option>
                  <option value="referral">Реферал</option>
                  <option value="social">Соц. сети</option>
                  <option value="other">Другое</option>
                </select>
              </div>
              <div>
                <label class="block text-xs text-gray-400 mb-1">Температура</label>
                <select v-model="form.temperature" class="input w-full">
                  <option value="cold">❄️ Низкий интерес</option>
                  <option value="warm">🌤 Средний интерес</option>
                  <option value="hot">🔥 Высокий интерес</option>
                </select>
              </div>
            </div>
            <div>
              <label class="block text-xs text-gray-400 mb-1">Менеджер</label>
              <select v-model="form.assigned_to" class="input w-full">
                <option :value="null">Не назначен</option>
                <option v-for="m in managers" :key="m.id" :value="m.id">{{ m.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-xs text-gray-400 mb-1">Бюджет</label>
              <input v-model="form.budget_range" type="text" placeholder="например: $50 000 – $80 000" class="input w-full" />
            </div>
            <div>
              <label class="block text-xs text-gray-400 mb-1">Сообщение</label>
              <textarea v-model="form.message" rows="2" class="input w-full resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
              <button type="button" @click="closeCreate" class="btn-secondary">Отмена</button>
              <button type="submit" class="btn-primary" :disabled="processing">
                {{ processing ? 'Сохранение…' : 'Создать' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import draggable from 'vuedraggable'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  pipeline: Object,
  columns: Array,
  managers: Array,
  filters: Object,
})

const filters = reactive({
  search:      props.filters?.search      ?? '',
  temperature: props.filters?.temperature ?? '',
  assigned_to: props.filters?.assigned_to ?? '',
})

const openCreate   = ref(false)
const processing   = ref(false)
const dragOverCol  = ref(null)

const form = reactive({
  name: '', phone: '', email: '', message: '',
  status: 'new', source: 'website', temperature: 'cold',
  assigned_to: null, budget_range: '',
  pipeline_stage: props.columns?.[0]?.key ?? 'new',
})

function applyFilters() {
  router.get(route('admin.leads.kanban'), filters, { preserveState: true, replace: true })
}

function openLead(lead) {
  router.visit(route('admin.leads.show', lead.id))
}

function quickCreate(col) {
  form.pipeline_stage = col.key
  openCreate.value = true
}

function closeCreate() {
  openCreate.value = false
  Object.assign(form, {
    name:'', phone:'', email:'', message:'',
    status:'new', source:'website', temperature:'cold',
    assigned_to: null, budget_range:'',
    pipeline_stage: props.columns?.[0]?.key ?? 'new',
  })
}

function submitCreate() {
  processing.value = true
  router.post(route('admin.leads.store'), form, {
    onSuccess: () => { closeCreate(); processing.value = false },
    onError:   () => { processing.value = false },
  })
}

function onDragStart() {
  dragOverCol.value = null
}

function onDragEnd(evt, targetCol) {
  dragOverCol.value = null
  if (evt.item) {
    const movedLead = targetCol.leads[evt.newIndex]
    if (movedLead && movedLead.id) {
      router.patch(route('admin.leads.move-stage', movedLead.id), { stage: targetCol.key }, {
        preserveState: true,
        preserveScroll: true,
      })
    }
  }
}

// Helpers
function tempIcon(t)  { return { cold:'❄️', warm:'🌤', hot:'🔥' }[t] ?? '❄️' }
function tempLabel(t) { return { cold:'Низкий интерес', warm:'Средний интерес', hot:'Высокий интерес' }[t] ?? t }
function tempColor(t) { return { cold:'#818CF8', warm:'#F59E0B', hot:'#EF4444' }[t] ?? '#818CF8' }
function scoreColor(s) {
  if (s >= 70) return 'bg-green-400'
  if (s >= 40) return 'bg-yellow-400'
  return 'bg-red-400'
}
function isOverdue(dt) { return dt && new Date(dt) < new Date() }
function fmtDate(dt) {
  if (!dt) return ''
  return new Date(dt).toLocaleDateString('ru-RU', { day:'2-digit', month:'short' })
}
</script>

<style scoped>
@reference "tailwindcss";

/* ── Column ─────────────────────────────────────────────────── */
.kanban-column {
  flex-shrink: 0;
  width: 272px;
  display: flex;
  flex-direction: column;
  border-radius: 14px;
  background: #141C2B;
  border-top: 3px solid var(--col-color, #C9A96E);
  box-shadow: 0 2px 12px rgba(0,0,0,0.25);
}

.kanban-col-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 14px 10px;
  border-bottom: 1px solid rgba(255,255,255,0.05);
}

.kanban-count {
  font-size: 11px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 9999px;
}

/* ── Drop zone ──────────────────────────────────────────────── */
.kanban-dropzone {
  display: flex;
  flex-direction: column;
  gap: 8px;
  flex: 1;
  padding: 10px 8px 6px;
  min-height: 120px;
  transition: background 0.15s;
  border-radius: 0 0 4px 4px;
}

.kanban-dropzone.drag-over {
  background: rgba(255,255,255,0.04);
}

/* ── Card ───────────────────────────────────────────────────── */
.kanban-card {
  background: #1E293B;
  border: 1px solid rgba(255,255,255,0.07);
  border-left: 3px solid #818CF8;
  border-radius: 10px;
  padding: 12px;
  cursor: pointer;
  transition: background 0.15s, border-color 0.15s, transform 0.1s;
  user-select: none;
}

.kanban-card:hover {
  background: #243349;
  border-color: rgba(255,255,255,0.14);
  transform: translateY(-1px);
}

.kanban-card:active {
  transform: translateY(0);
}

/* ── Add button ─────────────────────────────────────────────── */
.kanban-add-btn {
  width: 100%;
  text-align: left;
  padding: 9px 14px;
  font-size: 12px;
  color: #6b7280;
  border-top: 1px solid rgba(255,255,255,0.05);
  border-radius: 0 0 14px 14px;
  transition: color 0.15s, background 0.15s;
}

.kanban-add-btn:hover {
  color: #e2e8f0;
  background: rgba(255,255,255,0.04);
}
</style>
