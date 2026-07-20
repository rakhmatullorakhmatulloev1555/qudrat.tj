<template>
  <AdminLayout title="Журнал аудита">
    <Head title="Журнал аудита" />
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-white">Журнал аудита</h1>
          <p class="text-gray-400 text-sm mt-1">Все действия сотрудников в системе</p>
        </div>
        <Link :href="route('admin.security.login-history')"
          class="btn-secondary flex items-center gap-2 text-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
          </svg>
          История входов
        </Link>
      </div>

      <!-- Filters -->
      <div class="card flex flex-wrap gap-3">
        <input v-model="filters.search" @input="debouncedSearch"
          class="input flex-1 min-w-[200px]" placeholder="Email, описание, объект..."/>
        <select v-model="filters.module" @change="applyFilters" class="input w-full sm:w-40">
          <option value="">Все модули</option>
          <option v-for="m in modules" :key="m" :value="m">{{ m }}</option>
        </select>
        <select v-model="filters.action" @change="applyFilters" class="input w-full sm:w-36">
          <option value="">Все действия</option>
          <option v-for="a in actions" :key="a" :value="a">{{ actionLabel(a) }}</option>
        </select>
        <input v-model="filters.date_from" type="date" @change="applyFilters" class="input w-full sm:w-36"/>
        <input v-model="filters.date_to"   type="date" @change="applyFilters" class="input w-full sm:w-36"/>
      </div>

      <!-- Mobile cards -->
      <div class="md:hidden space-y-2">
        <div v-if="!logs.data.length" class="card text-center text-gray-500 text-sm py-10">Записей нет</div>
        <div v-for="log in logs.data" :key="log.id" class="card p-4 cursor-pointer"
          @click="expanded === log.id ? expanded = null : expanded = log.id">
          <div class="flex items-start justify-between gap-3 mb-2">
            <div>
              <div class="text-sm font-medium text-white">{{ log.user_name }}</div>
              <div class="text-xs text-gray-500">{{ log.user_email }}</div>
            </div>
            <span class="badge text-xs flex-shrink-0" :class="actionBadge(log.action)">{{ actionLabel(log.action) }}</span>
          </div>
          <div class="flex flex-wrap gap-2 text-xs mb-2">
            <span class="text-gray-400">{{ log.module }}</span>
            <span class="text-gray-500">{{ log.created_at }}</span>
          </div>
          <div v-if="log.description" class="text-gray-400 text-xs truncate mb-2">{{ log.description }}</div>
          <!-- Expanded detail -->
          <div v-if="expanded === log.id" class="mt-2 pt-2 border-t border-white/5">
            <div class="grid grid-cols-1 gap-3 text-xs font-mono">
              <div v-if="log.old_values">
                <div class="text-red-400 font-bold mb-1">Было:</div>
                <pre class="text-gray-400 whitespace-pre-wrap text-[11px]">{{ JSON.stringify(log.old_values, null, 2) }}</pre>
              </div>
              <div v-if="log.new_values">
                <div class="text-green-400 font-bold mb-1">Стало:</div>
                <pre class="text-gray-300 whitespace-pre-wrap text-[11px]">{{ JSON.stringify(log.new_values, null, 2) }}</pre>
              </div>
              <div v-if="!log.old_values && !log.new_values" class="text-gray-500">Детали не записаны</div>
            </div>
          </div>
          <svg class="w-4 h-4 text-gray-500 mx-auto mt-1 transition-transform" :class="expanded === log.id ? 'rotate-180' : ''"
            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
          </svg>
        </div>
        <div v-if="logs.last_page > 1" class="flex justify-center gap-1 pt-1">
          <Link v-for="link in logs.links" :key="link.label" :href="link.url || '#'"
            class="px-3 py-1.5 rounded text-xs font-medium transition-colors"
            :class="link.active ? 'bg-gold text-dark' : 'text-gray-400 hover:text-white hover:bg-white/5'"
            >{{ link.label.replace('&laquo;','«').replace('&raquo;','»') }}</Link>
        </div>
      </div>

      <!-- Desktop Table -->
      <div class="hidden md:block card overflow-hidden p-0">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-white/5">
                <th class="th">Время</th>
                <th class="th">Пользователь</th>
                <th class="th">Действие</th>
                <th class="th hidden md:table-cell">Модуль</th>
                <th class="th hidden lg:table-cell">Описание</th>
                <th class="th hidden xl:table-cell">IP</th>
                <th class="th"></th>
              </tr>
            </thead>
            <tbody>
              <template v-for="log in logs.data" :key="log.id">
                <tr class="border-b border-white/5 hover:bg-white/2 transition-colors cursor-pointer"
                  @click="expanded === log.id ? expanded = null : expanded = log.id">
                  <td class="td text-gray-400 text-xs whitespace-nowrap">{{ log.created_at }}</td>
                  <td class="td">
                    <div class="text-sm font-medium text-white">{{ log.user_name }}</div>
                    <div class="text-xs text-gray-500">{{ log.user_email }}</div>
                  </td>
                  <td class="td">
                    <span class="badge text-xs" :class="actionBadge(log.action)">
                      {{ actionLabel(log.action) }}
                    </span>
                  </td>
                  <td class="td hidden md:table-cell text-gray-400 text-sm">{{ log.module }}</td>
                  <td class="td hidden lg:table-cell text-gray-300 text-sm max-w-xs truncate">
                    {{ log.description }}
                  </td>
                  <td class="td hidden xl:table-cell text-gray-500 text-xs font-mono">
                    {{ log.ip_address }}
                  </td>
                  <td class="td text-gray-500">
                    <svg class="w-4 h-4 transition-transform" :class="expanded === log.id ? 'rotate-180' : ''"
                      fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                    </svg>
                  </td>
                </tr>
                <!-- Expanded row: old/new values -->
                <tr v-if="expanded === log.id" class="bg-[#0A1220]">
                  <td colspan="7" class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-mono">
                      <div v-if="log.old_values">
                        <div class="text-red-400 font-bold mb-2">Было:</div>
                        <pre class="text-gray-400 whitespace-pre-wrap">{{ JSON.stringify(log.old_values, null, 2) }}</pre>
                      </div>
                      <div v-if="log.new_values">
                        <div class="text-green-400 font-bold mb-2">Стало:</div>
                        <pre class="text-gray-300 whitespace-pre-wrap">{{ JSON.stringify(log.new_values, null, 2) }}</pre>
                      </div>
                      <div v-if="!log.old_values && !log.new_values" class="text-gray-500 col-span-2">
                        Детали не записаны
                      </div>
                    </div>
                  </td>
                </tr>
              </template>
              <tr v-if="!logs.data.length">
                <td colspan="7" class="td text-center text-gray-500 py-12">Записей нет</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="logs.last_page > 1" class="flex items-center justify-between px-6 py-4 border-t border-white/5">
          <span class="text-gray-500 text-sm">Всего: {{ logs.total }} записей</span>
          <div class="flex gap-1">
            <Link v-for="link in logs.links" :key="link.label"
              :href="link.url || '#'"
              class="px-3 py-1.5 rounded text-xs font-medium transition-colors"
              :class="link.active ? 'bg-gold text-dark' : 'text-gray-400 hover:text-white hover:bg-white/5'"
              >{{ link.label.replace('&laquo;','«').replace('&raquo;','»') }}</Link>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  logs:    { type: Object, required: true },
  modules: { type: Array,  default: () => [] },
  actions: { type: Array,  default: () => [] },
  filters: { type: Object, default: () => ({}) },
})

const filters  = ref({ ...props.filters })
const expanded = ref(null)

const actionsMeta = {
  created: { label: 'Создан',    cls: 'badge-green' },
  updated: { label: 'Изменён',   cls: 'badge-blue' },
  deleted: { label: 'Удалён',    cls: 'badge-red' },
  restored:{ label: 'Восстановл',cls: 'badge-yellow' },
  login:   { label: 'Вход',      cls: 'badge-gray' },
  logout:  { label: 'Выход',     cls: 'badge-gray' },
  exported:{ label: 'Экспорт',   cls: 'badge-purple' },
  approved:{ label: 'Одобрен',   cls: 'badge-green' },
  rejected:{ label: 'Отклонён',  cls: 'badge-red' },
}
function actionLabel(a) { return actionsMeta[a]?.label || a }
function actionBadge(a) { return actionsMeta[a]?.cls   || 'badge-gray' }

let timer = null
function debouncedSearch() {
  clearTimeout(timer)
  timer = setTimeout(applyFilters, 400)
}
function applyFilters() {
  router.get(route('admin.security.audit-logs'), filters.value, {
    preserveState: true, replace: true,
  })
}
</script>
