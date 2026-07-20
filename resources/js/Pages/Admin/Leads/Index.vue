<template>
  <AdminLayout title="Заявки">
    <Head title="Заявки" />
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
      <div>
        <h1 class="text-xl font-bold text-white">Заявки</h1>
        <p class="text-sm text-gray-400 mt-0.5">CRM — управление обращениями</p>
      </div>
      <div class="flex items-center gap-2">
        <Link :href="route('admin.leads.kanban')" class="btn-secondary text-sm px-3 py-1.5">
          ⬜ Kanban
        </Link>
        <a :href="route('admin.leads.export') + '?' + exportQuery" class="btn-secondary text-sm px-3 py-1.5">
          ⬇ CSV
        </a>
        <button @click="openCreate" class="btn-primary">+ Новая заявка</button>
      </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
      <div class="card p-4">
        <div class="text-2xl font-black text-white">{{ stats.total }}</div>
        <div class="text-xs text-gray-400 mt-1">Всего заявок</div>
      </div>
      <div class="card p-4">
        <div class="text-2xl font-black text-blue-400">{{ stats.new }}</div>
        <div class="text-xs text-gray-400 mt-1">Новых</div>
      </div>
      <div class="card p-4">
        <div class="text-2xl font-black text-red-400">{{ stats.hot }}</div>
        <div class="text-xs text-gray-400 mt-1">🔥 Горячих</div>
      </div>
      <div class="card p-4">
        <div class="text-2xl font-black text-green-400">{{ stats.won }}</div>
        <div class="text-xs text-gray-400 mt-1">Успешно</div>
      </div>
    </div>

    <!-- Filters -->
    <div class="card mb-5 p-3">
      <div class="flex flex-wrap gap-2">
        <input v-model="filters.search" @input="applyFilters" type="text"
          placeholder="Поиск по имени, телефону, email…"
          class="input text-sm flex-1 min-w-[160px]" />
        <select v-model="filters.status" @change="applyFilters" class="input text-sm w-full sm:w-36">
          <option value="">Все статусы</option>
          <option value="new">Новые</option>
          <option value="in_progress">В работе</option>
          <option value="success">Успешно</option>
          <option value="rejected">Отказ</option>
        </select>
        <select v-model="filters.temperature" @change="applyFilters" class="input text-sm w-full sm:w-36">
          <option value="">Все темп.</option>
          <option value="cold">❄️ Низкий интерес</option>
          <option value="warm">🌤 Средний интерес</option>
          <option value="hot">🔥 Высокий интерес</option>
        </select>
        <select v-model="filters.assigned_to" @change="applyFilters" class="input text-sm w-full sm:w-44">
          <option value="">Все менеджеры</option>
          <option v-for="m in managers" :key="m.id" :value="m.id">{{ m.name }}</option>
        </select>
      </div>
    </div>

    <!-- Mobile cards (< md) -->
    <div class="md:hidden space-y-2 mb-4">
      <div v-if="!leads.data.length" class="card p-8 text-center text-gray-500">
        <div class="text-3xl mb-2">📭</div>
        <p>Заявок не найдено</p>
      </div>
      <div v-for="lead in leads.data" :key="lead.id"
        class="card p-4 cursor-pointer hover:border-white/10 transition-colors"
        @click="router.visit(route('admin.leads.show', lead.id))">
        <div class="flex items-start justify-between gap-2 mb-2">
          <div class="min-w-0">
            <div class="font-semibold text-white truncate">{{ lead.name }}</div>
            <div v-if="lead.interest" class="text-xs text-gray-500 truncate">{{ lead.interest }}</div>
          </div>
          <span class="badge flex-shrink-0" :class="tempBadge(lead.temperature)">
            {{ tempIcon(lead.temperature) }} {{ tempLabel(lead.temperature) }}
          </span>
        </div>
        <div class="text-sm text-gray-300 mb-2">{{ lead.phone }}</div>
        <div class="flex items-center gap-2 flex-wrap">
          <span class="badge" :class="statusBadge(lead.status)">{{ statusLabel(lead.status) }}</span>
          <span class="text-xs text-gray-500">{{ stageLabel(lead.pipeline_stage) }}</span>
          <span class="text-xs text-gray-500 ml-auto">{{ fmtDate(lead.created_at) }}</span>
        </div>
        <div class="flex items-center gap-2 mt-3 pt-3 border-t border-white/5" @click.stop>
          <button @click="router.visit(route('admin.leads.show', lead.id))"
            class="flex-1 py-1.5 text-xs rounded-lg text-gray-300 hover:text-white hover:bg-white/10 transition-colors text-center">
            👁 Открыть
          </button>
          <button @click="startEdit(lead)"
            class="flex-1 py-1.5 text-xs rounded-lg text-gray-300 hover:text-white hover:bg-white/10 transition-colors text-center">
            ✏️ Изменить
          </button>
          <button @click="confirmDelete(lead)"
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
              <th class="text-left px-4 py-3 text-xs text-gray-400 font-semibold uppercase tracking-wide">Имя</th>
              <th class="text-left px-4 py-3 text-xs text-gray-400 font-semibold uppercase tracking-wide">Контакты</th>
              <th class="text-left px-4 py-3 text-xs text-gray-400 font-semibold uppercase tracking-wide">Температура</th>
              <th class="text-left px-4 py-3 text-xs text-gray-400 font-semibold uppercase tracking-wide hidden lg:table-cell">Этап</th>
              <th class="text-left px-4 py-3 text-xs text-gray-400 font-semibold uppercase tracking-wide">Статус</th>
              <th class="text-left px-4 py-3 text-xs text-gray-400 font-semibold uppercase tracking-wide hidden lg:table-cell">Score</th>
              <th class="text-left px-4 py-3 text-xs text-gray-400 font-semibold uppercase tracking-wide hidden xl:table-cell">Менеджер</th>
              <th class="text-left px-4 py-3 text-xs text-gray-400 font-semibold uppercase tracking-wide">Дата</th>
              <th class="px-4 py-3"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!leads.data.length">
              <td colspan="9" class="text-center py-16 text-gray-500">
                <div class="text-3xl mb-2">📭</div>
                <p>Заявок не найдено</p>
              </td>
            </tr>
            <tr v-for="lead in leads.data" :key="lead.id"
              class="border-b border-white/5 hover:bg-white/3 transition-colors group cursor-pointer"
              @click="router.visit(route('admin.leads.show', lead.id))">
              <td class="px-4 py-3">
                <div class="font-semibold text-white">{{ lead.name }}</div>
                <div v-if="lead.interest" class="text-xs text-gray-500 mt-0.5">{{ lead.interest }}</div>
              </td>
              <td class="px-4 py-3">
                <div class="text-gray-300">{{ lead.phone }}</div>
                <div v-if="lead.email" class="text-xs text-gray-500 truncate max-w-[160px]">{{ lead.email }}</div>
              </td>
              <td class="px-4 py-3" @click.stop>
                <span class="badge" :class="tempBadge(lead.temperature)">
                  {{ tempIcon(lead.temperature) }} {{ tempLabel(lead.temperature) }}
                </span>
              </td>
              <td class="px-4 py-3 hidden lg:table-cell">
                <span class="text-xs text-gray-400">{{ stageLabel(lead.pipeline_stage) }}</span>
              </td>
              <td class="px-4 py-3">
                <span class="badge" :class="statusBadge(lead.status)">{{ statusLabel(lead.status) }}</span>
              </td>
              <td class="px-4 py-3 hidden lg:table-cell">
                <div class="flex items-center gap-1.5">
                  <div class="w-12 h-1.5 bg-white/10 rounded-full overflow-hidden">
                    <div class="h-full rounded-full" :class="scoreBg(lead.score)"
                      :style="{width: (lead.score ?? 0) + '%'}"></div>
                  </div>
                  <span class="text-xs text-gray-400">{{ lead.score ?? 0 }}</span>
                </div>
              </td>
              <td class="px-4 py-3 hidden xl:table-cell">
                <div v-if="lead.assignee" class="flex items-center gap-1.5">
                  <div class="w-6 h-6 rounded-full bg-gold/20 flex items-center justify-center text-gold text-[10px] font-bold">
                    {{ lead.assignee.name.charAt(0) }}
                  </div>
                  <span class="text-xs text-gray-300">{{ lead.assignee.name.split(' ')[0] }}</span>
                </div>
                <span v-else class="text-gray-600 text-xs">—</span>
              </td>
              <td class="px-4 py-3">
                <span class="text-xs text-gray-400">{{ fmtDate(lead.created_at) }}</span>
              </td>
              <td class="px-4 py-3" @click.stop>
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                  <button @click="router.visit(route('admin.leads.show', lead.id))"
                    class="p-1.5 hover:bg-white/10 rounded-lg text-gray-400 hover:text-white transition-colors" title="Открыть профиль">👁</button>
                  <button @click="startEdit(lead)"
                    class="p-1.5 hover:bg-white/10 rounded-lg text-gray-400 hover:text-white transition-colors" title="Редактировать">✏️</button>
                  <button @click="confirmDelete(lead)"
                    class="p-1.5 hover:bg-red-500/10 rounded-lg text-gray-400 hover:text-red-400 transition-colors" title="Удалить">🗑️</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination (desktop) -->
      <div v-if="leads.last_page > 1" class="flex items-center justify-between px-4 py-3 border-t border-white/5">
        <span class="text-sm text-gray-400">Показано {{ leads.from }}–{{ leads.to }} из {{ leads.total }}</span>
        <div class="flex gap-1">
          <Link v-for="link in leads.links" :key="link.label"
            :href="link.url ?? '#'"
            class="px-2.5 py-1 rounded text-xs transition-colors"
            :class="link.active ? 'bg-gold text-dark font-bold' : 'text-gray-400 hover:bg-white/5'"
            >{{ link.label.replace('&laquo;','«').replace('&raquo;','»') }}</Link>
        </div>
      </div>
    </div>

    <!-- Pagination (mobile) -->
    <div v-if="leads.last_page > 1" class="md:hidden flex items-center justify-between mt-3">
      <span class="text-sm text-gray-400">{{ leads.from }}–{{ leads.to }} из {{ leads.total }}</span>
      <div class="flex gap-1">
        <Link v-for="link in leads.links" :key="link.label"
          :href="link.url ?? '#'"
          class="px-2.5 py-1.5 rounded text-xs transition-colors"
          :class="link.active ? 'bg-gold text-dark font-bold' : 'text-gray-400 hover:bg-white/5'"
          >{{ link.label.replace('&laquo;','«').replace('&raquo;','»') }}</Link>
      </div>
    </div>

    <!-- Create / Edit Modal -->
    <Teleport to="body">
      <div v-if="modal.open" class="modal-overlay" @click.self="closeModal">
        <div class="modal-box w-full max-w-lg">
          <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-white">{{ modal.lead ? 'Редактировать заявку' : 'Новая заявка' }}</h2>
            <button @click="closeModal" class="text-gray-400 hover:text-white text-xl">×</button>
          </div>
          <form @submit.prevent="submitModal" class="space-y-3">
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
                <label class="block text-xs text-gray-400 mb-1">Статус</label>
                <select v-model="form.status" class="input w-full">
                  <option value="new">Новая</option>
                  <option value="in_progress">В работе</option>
                  <option value="success">Успешно</option>
                  <option value="rejected">Отказ</option>
                </select>
              </div>
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
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs text-gray-400 mb-1">Температура</label>
                <select v-model="form.temperature" class="input w-full">
                  <option value="cold">❄️ Низкий интерес</option>
                  <option value="warm">🌤 Средний интерес</option>
                  <option value="hot">🔥 Высокий интерес</option>
                </select>
              </div>
              <div>
                <label class="block text-xs text-gray-400 mb-1">Менеджер</label>
                <select v-model="form.assigned_to" class="input w-full">
                  <option :value="null">Не назначен</option>
                  <option v-for="m in managers" :key="m.id" :value="m.id">{{ m.name }}</option>
                </select>
              </div>
            </div>
            <div>
              <label class="block text-xs text-gray-400 mb-1">Интерес / Объект</label>
              <input v-model="form.interest" type="text" class="input w-full" placeholder="Квартира 2-комн., инвестиции…" />
            </div>
            <div>
              <label class="block text-xs text-gray-400 mb-1">Бюджет</label>
              <input v-model="form.budget_range" type="text" class="input w-full" placeholder="$50 000 – $80 000" />
            </div>
            <div>
              <label class="block text-xs text-gray-400 mb-1">Сообщение</label>
              <textarea v-model="form.message" rows="2" class="input w-full resize-none"></textarea>
            </div>
            <div>
              <label class="block text-xs text-gray-400 mb-1">Заметки</label>
              <textarea v-model="form.notes" rows="2" class="input w-full resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
              <button type="button" @click="closeModal" class="btn-secondary">Отмена</button>
              <button type="submit" class="btn-primary" :disabled="processing">
                {{ processing ? 'Сохранение…' : (modal.lead ? 'Сохранить' : 'Создать') }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Delete confirm -->
      <div v-if="deleteModal.open" class="modal-overlay" @click.self="deleteModal.open = false">
        <div class="modal-box w-full max-w-sm text-center">
          <div class="text-4xl mb-3">🗑️</div>
          <h3 class="text-lg font-bold text-white mb-2">Удалить заявку?</h3>
          <p class="text-sm text-gray-400 mb-5">{{ deleteModal.lead?.name }}</p>
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
  leads:    Object,
  managers: Array,
  filters:  Object,
  stats:    Object,
})

const filters = reactive({
  search:      props.filters?.search      ?? '',
  status:      props.filters?.status      ?? '',
  temperature: props.filters?.temperature ?? '',
  assigned_to: props.filters?.assigned_to ?? '',
})

const exportQuery = computed(() => new URLSearchParams(
  Object.fromEntries(Object.entries(filters).filter(([, v]) => v))
).toString())

const processing  = ref(false)
const modal       = reactive({ open: false, lead: null })
const deleteModal = reactive({ open: false, lead: null })

const form = reactive({
  name: '', phone: '', email: '', message: '',
  status: 'new', source: 'website',
  temperature: 'cold', assigned_to: null,
  interest: '', budget_range: '', notes: '',
})

function applyFilters() {
  router.get(route('admin.leads.index'), filters, { preserveState: true, replace: true })
}

function openCreate() {
  modal.lead = null
  Object.assign(form, { name:'', phone:'', email:'', message:'', status:'new', source:'website', temperature:'cold', assigned_to:null, interest:'', budget_range:'', notes:'' })
  modal.open = true
}

function startEdit(lead) {
  modal.lead = lead
  Object.assign(form, {
    name:         lead.name,
    phone:        lead.phone,
    email:        lead.email ?? '',
    message:      lead.message ?? '',
    status:       lead.status,
    source:       lead.source ?? 'other',
    temperature:  lead.temperature ?? 'cold',
    assigned_to:  lead.assigned_to ?? null,
    interest:     lead.interest ?? '',
    budget_range: lead.budget_range ?? '',
    notes:        lead.notes ?? '',
  })
  modal.open = true
}

function closeModal() {
  modal.open = false
  modal.lead = null
}

function submitModal() {
  processing.value = true
  if (modal.lead) {
    router.put(route('admin.leads.update', modal.lead.id), form, {
      onSuccess: () => { closeModal(); processing.value = false },
      onError:   () => { processing.value = false },
    })
  } else {
    router.post(route('admin.leads.store'), form, {
      onSuccess: () => { closeModal(); processing.value = false },
      onError:   () => { processing.value = false },
    })
  }
}

function confirmDelete(lead) {
  deleteModal.lead = lead
  deleteModal.open = true
}

function doDelete() {
  processing.value = true
  router.delete(route('admin.leads.destroy', deleteModal.lead.id), {
    onSuccess: () => { deleteModal.open = false; processing.value = false },
    onError:   () => { processing.value = false },
  })
}

// Helpers
function tempIcon(t)  { return { cold:'❄️', warm:'🌤', hot:'🔥' }[t] ?? '❄️' }
function tempLabel(t) { return { cold:'Низкий интерес', warm:'Средний интерес', hot:'Высокий интерес' }[t] ?? '—' }
function tempBadge(t) { return { cold:'', warm:'badge-blue', hot:'badge-red' }[t] ?? '' }
function stageLabel(s) {
  return { new:'Новый', contacted:'Контакт', qualified:'Квалифицирован', proposal:'Предложение', converted:'Продажа', lost:'Отказ' }[s] ?? (s ?? '—')
}
function statusLabel(s) { return { new:'Новая', in_progress:'В работе', success:'Успешно', rejected:'Отказ' }[s] ?? s }
function statusBadge(s) { return { new:'badge-blue', in_progress:'badge-gold', success:'badge-green', rejected:'badge-red' }[s] ?? '' }
function scoreBg(s)  { if (s >= 70) return 'bg-green-400'; if (s >= 40) return 'bg-yellow-400'; return 'bg-red-400' }
function fmtDate(dt) {
  if (!dt) return ''
  return new Date(dt).toLocaleDateString('ru-RU', { day:'2-digit', month:'short' })
}
</script>
