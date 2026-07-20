<template>
  <AdminLayout title="История входов">
    <Head title="История входов" />
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-white">История входов</h1>
          <p class="text-gray-400 text-sm mt-1">Все попытки аутентификации в системе</p>
        </div>
        <Link :href="route('admin.security.audit-logs')" class="btn-secondary text-sm">
          Журнал аудита
        </Link>
      </div>

      <!-- Filters -->
      <div class="card flex flex-wrap gap-3">
        <input v-model="filters.search" @input="debouncedSearch"
          class="input flex-1 min-w-[200px]" placeholder="Email или IP..."/>
        <select v-model="filters.status" @change="applyFilters" class="input w-full sm:w-40">
          <option value="">Все статусы</option>
          <option value="success">Успешно</option>
          <option value="failed">Неудачно</option>
          <option value="blocked">Заблокирован</option>
        </select>
      </div>

      <!-- Mobile cards -->
      <div class="md:hidden space-y-2">
        <div v-if="!history.data.length" class="card text-center text-gray-500 text-sm py-10">Записей нет</div>
        <div v-for="h in history.data" :key="h.id" class="card p-4">
          <div class="flex items-start justify-between gap-3 mb-2">
            <div>
              <div class="text-sm font-medium text-white">{{ h.email || '—' }}</div>
              <div class="text-xs text-gray-500 font-mono">{{ h.ip_address }}</div>
            </div>
            <span class="badge flex-shrink-0" :class="statusBadge(h.status)">{{ statusLabel(h.status) }}</span>
          </div>
          <div class="flex flex-wrap gap-2 text-xs text-gray-400">
            <span>{{ h.created_at }}</span>
            <span>{{ deviceIcon(h.device) }} {{ h.device }}</span>
            <span v-if="h.two_fa" class="text-green-400 font-semibold">✓ 2FA</span>
          </div>
        </div>
        <div v-if="history.last_page > 1" class="flex justify-center gap-1 pt-1">
          <Link v-for="link in history.links" :key="link.label" :href="link.url || '#'"
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
                <th class="th">Email</th>
                <th class="th">Статус</th>
                <th class="th hidden md:table-cell">IP</th>
                <th class="th hidden lg:table-cell">Браузер</th>
                <th class="th hidden lg:table-cell">Устройство</th>
                <th class="th hidden xl:table-cell">2FA</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="h in history.data" :key="h.id"
                class="border-b border-white/5 hover:bg-white/2 transition-colors">
                <td class="td text-gray-400 text-xs whitespace-nowrap">{{ h.created_at }}</td>
                <td class="td text-sm text-white">{{ h.email || '—' }}</td>
                <td class="td">
                  <span class="badge" :class="statusBadge(h.status)">{{ statusLabel(h.status) }}</span>
                </td>
                <td class="td hidden md:table-cell text-gray-400 text-xs font-mono">{{ h.ip_address }}</td>
                <td class="td hidden lg:table-cell text-gray-400 text-sm">{{ h.browser }}</td>
                <td class="td hidden lg:table-cell">
                  <span class="text-gray-400 text-xs">{{ deviceIcon(h.device) }} {{ h.device }}</span>
                </td>
                <td class="td hidden xl:table-cell">
                  <span v-if="h.two_fa" class="text-green-400 text-xs">✓ Да</span>
                  <span v-else class="text-gray-600 text-xs">Нет</span>
                </td>
              </tr>
              <tr v-if="!history.data.length">
                <td colspan="7" class="td text-center text-gray-500 py-12">Записей нет</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-if="history.last_page > 1" class="flex items-center justify-between px-6 py-4 border-t border-white/5">
          <span class="text-gray-500 text-sm">Всего: {{ history.total }}</span>
          <div class="flex gap-1">
            <Link v-for="link in history.links" :key="link.label"
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
  history: { type: Object, required: true },
  filters: { type: Object, default: () => ({}) },
})

const filters = ref({ ...props.filters })

const sm = { success: { l: 'Успешно', c: 'badge-green' }, failed: { l: 'Неудачно', c: 'badge-red' }, blocked: { l: 'Заблокирован', c: 'badge-red' } }
function statusLabel(s) { return sm[s]?.l || s }
function statusBadge(s) { return sm[s]?.c || 'badge-gray' }
function deviceIcon(d) { return d === 'mobile' ? '📱' : d === 'tablet' ? '📟' : '💻' }

let timer = null
function debouncedSearch() { clearTimeout(timer); timer = setTimeout(applyFilters, 400) }
function applyFilters() {
  router.get(route('admin.security.login-history'), filters.value, { preserveState: true, replace: true })
}
</script>
