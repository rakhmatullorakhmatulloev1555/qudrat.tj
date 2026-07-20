<template>
  <AdminLayout :title="lead.name">
    <Head :title="lead.name + ' — Заявка'" />
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-400 mb-5">
      <Link :href="route('admin.leads.index')" class="hover:text-white transition-colors">Заявки</Link>
      <span>/</span>
      <Link :href="route('admin.leads.kanban')" class="hover:text-white transition-colors">Kanban</Link>
      <span>/</span>
      <span class="text-white">{{ lead.name }}</span>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
      <!-- ── LEFT: Profile card ── -->
      <div class="xl:col-span-1 space-y-4">
        <!-- Identity card -->
        <div class="card">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div class="w-14 h-14 rounded-2xl bg-gold/20 flex items-center justify-center text-2xl font-bold text-gold">
                {{ lead.name.charAt(0).toUpperCase() }}
              </div>
              <div>
                <h2 class="text-lg font-bold text-white">{{ lead.name }}</h2>
                <div class="flex items-center gap-1.5 mt-1">
                  <span class="badge" :class="tempBadge(lead.temperature)">
                    {{ tempIcon(lead.temperature) }} {{ tempLabel(lead.temperature) }}
                  </span>
                  <span class="badge" :class="statusBadge(lead.status)">{{ statusLabel(lead.status) }}</span>
                </div>
              </div>
            </div>
            <button @click="editMode = !editMode" class="btn-secondary text-xs px-2 py-1">
              {{ editMode ? '✕ Отмена' : '✏️ Ред.' }}
            </button>
          </div>

          <!-- Score bar -->
          <div class="mb-4">
            <div class="flex justify-between text-xs text-gray-400 mb-1">
              <span>Рейтинг лида</span>
              <span class="font-bold" :class="scoreText(lead.score)">{{ lead.score ?? 0 }}/100</span>
            </div>
            <div class="h-2 bg-white/10 rounded-full overflow-hidden">
              <div class="h-full rounded-full transition-all duration-500"
                :class="scoreBg(lead.score)"
                :style="{width: (lead.score ?? 0) + '%'}"></div>
            </div>
          </div>

          <!-- Contact info -->
          <div class="space-y-2 text-sm">
            <div class="flex items-center gap-2">
              <span class="text-gray-500 w-5">📞</span>
              <a :href="`tel:${lead.phone}`" class="text-white hover:text-gold transition-colors">{{ lead.phone }}</a>
            </div>
            <div v-if="lead.email" class="flex items-center gap-2">
              <span class="text-gray-500 w-5">✉️</span>
              <a :href="`mailto:${lead.email}`" class="text-gray-300 hover:text-white transition-colors truncate">{{ lead.email }}</a>
            </div>
            <div v-if="lead.source" class="flex items-center gap-2">
              <span class="text-gray-500 w-5">📡</span>
              <span class="text-gray-300">{{ lead.source }}</span>
            </div>
            <div v-if="lead.budget_range" class="flex items-center gap-2">
              <span class="text-gray-500 w-5">💰</span>
              <span class="text-gold font-semibold">{{ lead.budget_range }}</span>
            </div>
            <div v-if="lead.interest" class="flex items-center gap-2">
              <span class="text-gray-500 w-5">🏢</span>
              <span class="text-gray-300">{{ lead.interest }}</span>
            </div>
          </div>
        </div>

        <!-- Edit form (inline) -->
        <div v-if="editMode" class="card">
          <h3 class="text-sm font-semibold text-white mb-3">Редактировать</h3>
          <form @submit.prevent="saveEdit" class="space-y-2">
            <input v-model="editForm.name" type="text" class="input w-full text-sm" placeholder="Имя" required />
            <input v-model="editForm.phone" type="text" class="input w-full text-sm" placeholder="Телефон" required />
            <input v-model="editForm.email" type="email" class="input w-full text-sm" placeholder="Email" />
            <input v-model="editForm.budget_range" type="text" class="input w-full text-sm" placeholder="Бюджет" />
            <select v-model="editForm.temperature" class="input w-full text-sm">
              <option value="cold">❄️ Низкий интерес</option>
              <option value="warm">🌤 Средний интерес</option>
              <option value="hot">🔥 Высокий интерес</option>
            </select>
            <select v-model="editForm.assigned_to" class="input w-full text-sm">
              <option :value="null">Не назначен</option>
              <option v-for="m in managers" :key="m.id" :value="m.id">{{ m.name }}</option>
            </select>
            <textarea v-model="editForm.notes" rows="2" class="input w-full text-sm resize-none" placeholder="Заметки"></textarea>
            <button type="submit" class="btn-primary w-full text-sm" :disabled="saving">
              {{ saving ? 'Сохранение…' : 'Сохранить' }}
            </button>
          </form>
        </div>

        <!-- Pipeline stage selector -->
        <div class="card">
          <h3 class="text-sm font-semibold text-white mb-3">Этап воронки</h3>
          <div class="space-y-1">
            <button
              v-for="stage in stages" :key="stage.key"
              @click="moveToStage(stage.key)"
              class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all"
              :class="lead.pipeline_stage === stage.key
                ? 'bg-white/10 text-white font-semibold'
                : 'text-gray-400 hover:bg-white/5 hover:text-gray-200'"
            >
              <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" :style="{background: stage.color}"></span>
              <span class="flex-1 text-left">{{ stage.name }}</span>
              <span v-if="lead.pipeline_stage === stage.key" class="text-gold text-xs">✓</span>
              <span class="text-xs text-gray-600">{{ stage.probability }}%</span>
            </button>
          </div>
        </div>

        <!-- UTM / Source info -->
        <div v-if="lead.utm_source || lead.referrer_url || lead.landing_page" class="card">
          <h3 class="text-sm font-semibold text-white mb-3">Источник трафика</h3>
          <div class="space-y-1.5 text-xs text-gray-400">
            <div v-if="lead.utm_source">
              <span class="text-gray-500">utm_source:</span>
              <span class="text-gray-300 ml-1">{{ lead.utm_source }}</span>
            </div>
            <div v-if="lead.utm_medium">
              <span class="text-gray-500">utm_medium:</span>
              <span class="text-gray-300 ml-1">{{ lead.utm_medium }}</span>
            </div>
            <div v-if="lead.utm_campaign">
              <span class="text-gray-500">utm_campaign:</span>
              <span class="text-gray-300 ml-1">{{ lead.utm_campaign }}</span>
            </div>
            <div v-if="lead.landing_page" class="truncate">
              <span class="text-gray-500">Страница входа:</span>
              <span class="text-gray-300 ml-1">{{ lead.landing_page }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- ── RIGHT: Timeline & Deals ── -->
      <div class="xl:col-span-2 space-y-5">

        <!-- Add activity bar -->
        <div class="card p-3">
          <div class="flex flex-wrap gap-2">
            <button v-for="type in activityTypes" :key="type"
              @click="openActivity(type)"
              class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-sm text-gray-300 hover:text-white transition-all">
              <span>{{ typeIcon(type) }}</span>
              <span class="capitalize">{{ typeLabel(type) }}</span>
            </button>
          </div>
        </div>

        <!-- Deals section -->
        <div v-if="lead.deals && lead.deals.length" class="card">
          <h3 class="text-sm font-semibold text-white mb-3 flex items-center gap-2">
            💼 Сделки
            <span class="badge">{{ lead.deals.length }}</span>
          </h3>
          <div class="space-y-2">
            <div v-for="deal in lead.deals" :key="deal.id"
              class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
              <div>
                <div class="text-sm font-semibold text-white">{{ deal.title }}</div>
                <div class="text-xs text-gray-400 mt-0.5">
                  <span v-if="deal.stage" class="mr-2">{{ deal.stage.name }}</span>
                  <span v-if="deal.pipeline">{{ deal.pipeline.name }}</span>
                </div>
              </div>
              <div class="text-right">
                <div class="text-sm font-bold text-gold">
                  {{ deal.amount ? formatMoney(deal.amount, deal.currency) : '—' }}
                </div>
                <span class="badge text-[10px]" :class="dealStatusBadge(deal.status)">{{ deal.status }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Activity Timeline -->
        <div class="card">
          <h3 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
            📋 История активностей
            <span class="badge">{{ lead.activities?.length ?? 0 }}</span>
          </h3>

          <div v-if="lead.activities && lead.activities.length" class="relative">
            <!-- Timeline line -->
            <div class="absolute left-5 top-0 bottom-0 w-px bg-white/5"></div>

            <div class="space-y-1">
              <div v-for="act in lead.activities" :key="act.id"
                class="flex gap-3 group"
                :class="act.is_done ? '' : 'opacity-80'"
              >
                <!-- Icon -->
                <div class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center text-base z-10 relative"
                  :class="act.is_done ? 'bg-[#1E293B]' : 'bg-gold/10'">
                  {{ typeIcon(act.type) }}
                </div>
                <!-- Content -->
                <div class="flex-1 bg-white/3 hover:bg-white/5 rounded-xl p-3 transition-colors min-w-0">
                  <div class="flex items-start justify-between gap-2 mb-1">
                    <div class="flex items-center gap-2 flex-wrap">
                      <span class="text-sm font-semibold text-white">{{ act.subject || typeLabel(act.type) }}</span>
                      <span v-if="!act.is_done" class="badge badge-gold text-[10px]">запланировано</span>
                      <span v-if="act.outcome" class="badge badge-blue text-[10px]">{{ act.outcome }}</span>
                      <span v-if="act.direction === 'in'" class="badge text-[10px]">входящий</span>
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                      <button v-if="!act.is_done" @click="markDone(act)"
                        class="text-xs text-gray-500 hover:text-green-400 transition-colors px-1.5 py-0.5 rounded hover:bg-green-400/10">
                        ✓ Выполнено
                      </button>
                      <button @click="deleteActivity(act)"
                        class="text-xs text-gray-600 hover:text-red-400 transition-colors opacity-0 group-hover:opacity-100 px-1 py-0.5">
                        ✕
                      </button>
                    </div>
                  </div>
                  <p v-if="act.body" class="text-sm text-gray-400 mb-2 whitespace-pre-line">{{ act.body }}</p>
                  <div class="flex items-center gap-3 text-[11px] text-gray-500">
                    <span v-if="act.user">👤 {{ act.user.name }}</span>
                    <span>{{ fmtDatetime(act.created_at) }}</span>
                    <span v-if="act.duration_seconds">⏱ {{ fmtDuration(act.duration_seconds) }}</span>
                    <span v-if="act.completed_at && act.is_done">✓ {{ fmtDatetime(act.completed_at) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div v-else class="text-center py-10 text-gray-500">
            <div class="text-3xl mb-2">📭</div>
            <p class="text-sm">Активностей пока нет</p>
            <p class="text-xs mt-1">Добавьте звонок, письмо или задачу выше</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Activity Modal -->
    <Teleport to="body">
      <div v-if="actModal.open" class="modal-overlay" @click.self="actModal.open = false">
        <div class="modal-box w-full max-w-md">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
              <span>{{ typeIcon(actModal.type) }}</span>
              Добавить: {{ typeLabel(actModal.type) }}
            </h2>
            <button @click="actModal.open = false" class="text-gray-400 hover:text-white text-xl">×</button>
          </div>
          <form @submit.prevent="submitActivity" class="space-y-3">
            <div>
              <label class="block text-xs text-gray-400 mb-1">Тема</label>
              <input v-model="actModal.form.subject" type="text" class="input w-full text-sm" :placeholder="typeLabel(actModal.type)" />
            </div>
            <div>
              <label class="block text-xs text-gray-400 mb-1">Описание</label>
              <textarea v-model="actModal.form.body" rows="3" class="input w-full text-sm resize-none"></textarea>
            </div>
            <div v-if="['call','whatsapp','telegram'].includes(actModal.type)">
              <label class="block text-xs text-gray-400 mb-1">Результат</label>
              <select v-model="actModal.form.outcome" class="input w-full text-sm">
                <option value="">Не указан</option>
                <option value="answered">Ответил</option>
                <option value="no_answer">Не ответил</option>
                <option value="callback">Перезвонить</option>
                <option value="interested">Заинтересован</option>
                <option value="not_interested">Не заинтересован</option>
              </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs text-gray-400 mb-1">Дата/время</label>
                <input v-model="actModal.form.scheduled_at" type="datetime-local" class="input w-full text-sm" />
              </div>
              <div v-if="['call','meeting'].includes(actModal.type)">
                <label class="block text-xs text-gray-400 mb-1">Длительность (мин)</label>
                <input v-model.number="actModal.form.duration_min" type="number" min="0" class="input w-full text-sm" />
              </div>
            </div>
            <div class="flex items-center gap-2">
              <input v-model="actModal.form.is_done" type="checkbox" id="isDone" class="toggle" />
              <label for="isDone" class="text-sm text-gray-300 cursor-pointer">Уже выполнено</label>
            </div>
            <div class="flex justify-end gap-2 pt-1">
              <button type="button" @click="actModal.open = false" class="btn-secondary text-sm">Отмена</button>
              <button type="submit" class="btn-primary text-sm" :disabled="actModal.saving">
                {{ actModal.saving ? 'Сохранение…' : 'Добавить' }}
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
import { Head, Link, router } from '@inertiajs/vue3'
import axios from 'axios'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  lead:          Object,
  pipeline:      Object,
  stages:        Array,
  managers:      Array,
  activityTypes: Array,
})

const editMode = ref(false)
const saving   = ref(false)

const editForm = reactive({
  name:        props.lead.name,
  phone:       props.lead.phone,
  email:       props.lead.email ?? '',
  budget_range:props.lead.budget_range ?? '',
  temperature: props.lead.temperature ?? 'cold',
  assigned_to: props.lead.assigned_to ?? null,
  notes:       props.lead.notes ?? '',
  status:      props.lead.status,
  source:      props.lead.source ?? 'other',
  interest:    props.lead.interest ?? '',
})

function saveEdit() {
  saving.value = true
  router.put(route('admin.leads.update', props.lead.id), editForm, {
    onSuccess: () => { saving.value = false; editMode.value = false },
    onError:   () => { saving.value = false },
  })
}

function moveToStage(key) {
  router.patch(route('admin.leads.move-stage', props.lead.id), { stage: key }, {
    preserveScroll: true,
  })
}

// Activity modal
const actModal = reactive({
  open: false,
  type: 'note',
  saving: false,
  form: {
    subject: '', body: '', outcome: '',
    scheduled_at: '', is_done: false, duration_min: null,
  },
})

function openActivity(type) {
  actModal.type = type
  Object.assign(actModal.form, { subject:'', body:'', outcome:'', scheduled_at:'', is_done: type !== 'task', duration_min: null })
  actModal.open = true
}

async function submitActivity() {
  actModal.saving = true
  try {
    await axios.post(route('admin.activities.store'), {
      related_type:     'lead',
      related_id:       props.lead.id,
      type:             actModal.type,
      subject:          actModal.form.subject || null,
      body:             actModal.form.body || null,
      outcome:          actModal.form.outcome || null,
      scheduled_at:     actModal.form.scheduled_at || null,
      is_done:          actModal.form.is_done,
      duration_seconds: actModal.form.duration_min ? actModal.form.duration_min * 60 : null,
    })
    actModal.open = false
    router.reload({ only: ['lead'] })
  } finally {
    actModal.saving = false
  }
}

async function markDone(act) {
  await axios.put(route('admin.activities.update', act.id), { is_done: true })
  router.reload({ only: ['lead'] })
}

async function deleteActivity(act) {
  if (!confirm('Удалить активность?')) return
  await axios.delete(route('admin.activities.destroy', act.id))
  router.reload({ only: ['lead'] })
}

// Helpers
function typeIcon(t) {
  return { call:'📞', email:'📧', note:'📝', task:'✅', meeting:'🤝', whatsapp:'💬', telegram:'✈️', status_change:'🔄' }[t] ?? '📌'
}
function typeLabel(t) {
  return { call:'Звонок', email:'Email', note:'Заметка', task:'Задача', meeting:'Встреча', whatsapp:'WhatsApp', telegram:'Telegram', status_change:'Изменение этапа' }[t] ?? t
}
function tempIcon(t)  { return { cold:'❄️', warm:'🌤', hot:'🔥' }[t] ?? '❄️' }
function tempLabel(t) { return { cold:'Низкий интерес', warm:'Средний интерес', hot:'Высокий интерес' }[t] ?? t }
function tempBadge(t) { return { cold:'', warm:'badge-blue', hot:'badge-red' }[t] ?? '' }
function statusLabel(s) { return { new:'Новая', in_progress:'В работе', success:'Успешно', rejected:'Отказ' }[s] ?? s }
function statusBadge(s) { return { new:'badge-blue', in_progress:'badge-gold', success:'badge-green', rejected:'badge-red' }[s] ?? 'badge' }
function dealStatusBadge(s) { return { open:'badge-blue', won:'badge-green', lost:'badge-red', frozen:'' }[s] ?? '' }
function scoreText(s)  { if (s >= 70) return 'text-green-400'; if (s >= 40) return 'text-yellow-400'; return 'text-red-400' }
function scoreBg(s)    { if (s >= 70) return 'bg-green-400'; if (s >= 40) return 'bg-yellow-400'; return 'bg-red-400' }
function formatMoney(amount, currency) {
  return new Intl.NumberFormat('ru-RU', { style:'currency', currency: currency || 'USD', maximumFractionDigits:0 }).format(amount)
}
function fmtDatetime(dt) {
  if (!dt) return ''
  return new Date(dt).toLocaleString('ru-RU', { day:'2-digit', month:'short', hour:'2-digit', minute:'2-digit' })
}
function fmtDuration(s) {
  if (!s) return ''
  const m = Math.floor(s / 60)
  return m >= 60 ? `${Math.floor(m/60)}ч ${m%60}м` : `${m}м`
}
</script>
