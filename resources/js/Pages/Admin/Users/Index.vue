<template>
  <AdminLayout title="Пользователи">
    <Head title="Пользователи" />
    <div class="space-y-6">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-white">Пользователи</h1>
          <p class="text-gray-400 text-sm mt-1">Управление сотрудниками и доступом</p>
        </div>
        <button @click="openCreate" class="btn-primary flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
          </svg>
          Добавить пользователя
        </button>
      </div>

      <!-- Filters -->
      <div class="card flex flex-wrap gap-3">
        <input v-model="filters.search" @input="debouncedSearch"
          class="input flex-1 min-w-[200px]" placeholder="Поиск по имени, email, телефону..." />
        <select v-model="filters.role" @change="applyFilters" class="input w-full sm:w-40">
          <option value="">Все роли</option>
          <option v-for="r in roles" :key="r" :value="r">{{ roleLabel(r) }}</option>
        </select>
        <select v-model="filters.status" @change="applyFilters" class="input w-full sm:w-36">
          <option value="">Все статусы</option>
          <option value="active">Активные</option>
          <option value="inactive">Заблокированные</option>
        </select>
      </div>

      <!-- Stats row -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div v-for="stat in userStats" :key="stat.label" class="card text-center">
          <div class="text-2xl font-black" :style="{ color: stat.color }">{{ stat.value }}</div>
          <div class="text-gray-400 text-xs mt-1">{{ stat.label }}</div>
        </div>
      </div>

      <!-- Mobile cards -->
      <div class="md:hidden space-y-2">
        <div v-if="!users.data.length" class="card text-center text-gray-500 text-sm py-10">Пользователи не найдены</div>
        <div v-for="user in users.data" :key="user.id" class="card p-4">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold shrink-0"
              :style="{ background: avatarBg(user.name), color: '#fff' }">
              {{ initials(user.name) }}
            </div>
            <div class="flex-1 min-w-0">
              <div class="font-semibold text-white text-sm truncate">{{ user.name }}</div>
              <div class="text-gray-500 text-xs truncate">{{ user.email }}</div>
            </div>
            <span class="badge flex-shrink-0" :class="user.is_active ? 'badge-green' : 'badge-red'">
              {{ user.is_active ? 'Активен' : 'Блок.' }}
            </span>
          </div>
          <div class="flex flex-wrap items-center gap-2 mb-3">
            <span class="badge" :class="roleBadge(user.roles[0])">{{ roleLabel(user.roles[0]) }}</span>
            <span v-if="user.phone" class="text-gray-400 text-xs">{{ user.phone }}</span>
            <span v-if="user.two_fa" class="text-green-400 text-xs font-semibold">✓ 2FA</span>
          </div>
          <div class="flex gap-2">
            <button @click="openEdit(user)" class="flex-1 py-2 rounded-lg text-xs font-medium text-gold border border-gold/30 hover:bg-gold/10 transition-all">Изменить</button>
            <button @click="toggleStatus(user)" class="px-3 py-2 rounded-lg text-xs border border-white/10 hover:bg-white/5 transition-all"
              :class="user.is_active ? 'text-yellow-400' : 'text-green-400'">
              {{ user.is_active ? 'Блок.' : 'Актив.' }}
            </button>
            <button v-if="user.id !== $page.props.auth?.id" @click="confirmDelete(user)"
              class="px-3 py-2 rounded-lg text-xs text-red-400 border border-red-500/20 hover:bg-red-500/10 transition-all">Удалить</button>
          </div>
        </div>
        <div v-if="users.last_page > 1" class="flex justify-center gap-1 pt-1">
          <Link v-for="link in users.links" :key="link.label" :href="link.url || '#'"
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
                <th class="th">Пользователь</th>
                <th class="th">Роль</th>
                <th class="th hidden md:table-cell">Телефон</th>
                <th class="th hidden lg:table-cell">2FA</th>
                <th class="th">Статус</th>
                <th class="th hidden lg:table-cell">Создан</th>
                <th class="th text-right">Действия</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users.data" :key="user.id"
                class="border-b border-white/5 hover:bg-white/2 transition-colors">
                <td class="td">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold shrink-0"
                      :style="{ background: avatarBg(user.name), color: '#fff' }">
                      {{ initials(user.name) }}
                    </div>
                    <div>
                      <div class="font-semibold text-white text-sm">{{ user.name }}</div>
                      <div class="text-gray-500 text-xs">{{ user.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="td">
                  <span class="badge" :class="roleBadge(user.roles[0])">
                    {{ roleLabel(user.roles[0]) }}
                  </span>
                </td>
                <td class="td hidden md:table-cell text-gray-400 text-sm">{{ user.phone || '—' }}</td>
                <td class="td hidden lg:table-cell">
                  <span v-if="user.two_fa" class="text-green-400 text-xs font-semibold">✓ Вкл</span>
                  <span v-else class="text-gray-600 text-xs">Выкл</span>
                </td>
                <td class="td">
                  <span class="badge" :class="user.is_active ? 'badge-green' : 'badge-red'">
                    {{ user.is_active ? 'Активен' : 'Заблокирован' }}
                  </span>
                </td>
                <td class="td hidden lg:table-cell text-gray-500 text-xs">{{ user.created_at }}</td>
                <td class="td text-right">
                  <div class="flex items-center justify-end gap-2">
                    <button @click="openEdit(user)" class="icon-btn" title="Редактировать">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                      </svg>
                    </button>
                    <button @click="toggleStatus(user)" class="icon-btn"
                      :title="user.is_active ? 'Заблокировать' : 'Активировать'">
                      <svg v-if="user.is_active" class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>
                      </svg>
                      <svg v-else class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 1 1 9 0v3.75M3.75 21.75h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H3.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>
                      </svg>
                    </button>
                    <button v-if="user.id !== $page.props.auth?.id" @click="confirmDelete(user)"
                      class="icon-btn text-red-400 hover:text-red-300" title="Удалить">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916"/>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="!users.data.length">
                <td colspan="7" class="td text-center text-gray-500 py-12">Пользователи не найдены</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="users.last_page > 1" class="flex items-center justify-between px-6 py-4 border-t border-white/5">
          <span class="text-gray-500 text-sm">Всего: {{ users.total }}</span>
          <div class="flex gap-1">
            <Link v-for="link in users.links" :key="link.label"
              :href="link.url || '#'"
              class="px-3 py-1.5 rounded text-xs font-medium transition-colors"
              :class="link.active ? 'bg-gold text-dark' : 'text-gray-400 hover:text-white hover:bg-white/5'"
              >{{ link.label.replace('&laquo;','«').replace('&raquo;','»') }}</Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal: Create / Edit User -->
    <Teleport to="body">
      <div v-if="modal" class="modal-overlay" @click.self="modal = false">
        <div class="modal-box">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-white">
              {{ editing ? 'Редактировать пользователя' : 'Новый пользователь' }}
            </h2>
            <button @click="modal = false" class="icon-btn">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <form @submit.prevent="submit" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div class="col-span-2 sm:col-span-1">
                <label class="label">Имя *</label>
                <input v-model="form.name" class="input w-full" required />
                <p v-if="form.errors.name" class="error">{{ form.errors.name }}</p>
              </div>
              <div class="col-span-2 sm:col-span-1">
                <label class="label">Телефон</label>
                <input v-model="form.phone" class="input w-full" placeholder="+992 ..." />
              </div>
              <div class="col-span-2">
                <label class="label">Email *</label>
                <input v-model="form.email" type="email" class="input w-full" required />
                <p v-if="form.errors.email" class="error">{{ form.errors.email }}</p>
              </div>
              <div class="col-span-2 sm:col-span-1">
                <label class="label">{{ editing ? 'Новый пароль' : 'Пароль *' }}</label>
                <input v-model="form.password" type="password" class="input w-full"
                  :required="!editing" placeholder="Минимум 8 символов" />
                <p v-if="form.errors.password" class="error">{{ form.errors.password }}</p>
              </div>
              <div class="col-span-2 sm:col-span-1">
                <label class="label">Роль *</label>
                <select v-model="form.role" class="input w-full" required>
                  <option v-for="r in roles" :key="r" :value="r">{{ roleLabel(r) }}</option>
                </select>
              </div>
              <div class="col-span-2 flex items-center gap-3">
                <label class="toggle">
                  <input type="checkbox" v-model="form.is_active" />
                  <span class="toggle-track"></span>
                </label>
                <span class="text-sm text-gray-300">Активен</span>
              </div>
            </div>

            <div class="flex gap-3 pt-2">
              <button type="submit" :disabled="form.processing" class="btn-primary flex-1">
                {{ form.processing ? 'Сохранение...' : (editing ? 'Обновить' : 'Создать') }}
              </button>
              <button type="button" @click="modal = false" class="btn-secondary flex-1">Отмена</button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- Delete Confirm -->
    <Teleport to="body">
      <div v-if="deleteTarget" class="modal-overlay" @click.self="deleteTarget = null">
        <div class="modal-box max-w-sm">
          <div class="text-center space-y-4">
            <div class="w-14 h-14 rounded-full bg-red-500/15 flex items-center justify-center mx-auto">
              <svg class="w-7 h-7 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
              </svg>
            </div>
            <h3 class="text-white font-bold text-lg">Удалить пользователя?</h3>
            <p class="text-gray-400 text-sm">«{{ deleteTarget?.name }}» будет удалён безвозвратно.</p>
            <div class="flex gap-3">
              <button @click="deleteUser" class="btn-danger flex-1">Удалить</button>
              <button @click="deleteTarget = null" class="btn-secondary flex-1">Отмена</button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  users:       { type: Object, required: true },
  roles:       { type: Array,  default: () => [] },
  departments: { type: Array,  default: () => [] },
  filters:     { type: Object, default: () => ({}) },
})

const $page = usePage()
const modal = ref(false)
const editing = ref(null)
const deleteTarget = ref(null)
const filters = ref({ ...props.filters })

const form = useForm({
  name: '', email: '', phone: '', password: '', role: 'viewer', is_active: true,
})

const userStats = computed(() => [
  { label: 'Всего',       value: props.users.total,
    color: '#C9A96E' },
  { label: 'Активных',    value: props.users.data.filter(u => u.is_active).length,
    color: '#4ade80' },
  { label: 'С 2FA',       value: props.users.data.filter(u => u.two_fa).length,
    color: '#60a5fa' },
  { label: 'Заблокирован',value: props.users.data.filter(u => !u.is_active).length,
    color: '#f87171' },
])

function openCreate() {
  editing.value = null
  form.reset()
  form.clearErrors()
  form.is_active = true
  form.role = 'viewer'
  modal.value = true
}
function openEdit(user) {
  editing.value = user
  form.name      = user.name
  form.email     = user.email
  form.phone     = user.phone || ''
  form.password  = ''
  form.role      = user.roles[0] || 'viewer'
  form.is_active = user.is_active
  form.clearErrors()
  modal.value = true
}
function submit() {
  if (editing.value) {
    form.put(route('admin.users.update', editing.value.id), {
      onSuccess: () => { modal.value = false; form.reset() },
    })
  } else {
    form.post(route('admin.users.store'), {
      onSuccess: () => { modal.value = false; form.reset() },
    })
  }
}
function confirmDelete(user) { deleteTarget.value = user }
function deleteUser() {
  router.delete(route('admin.users.destroy', deleteTarget.value.id), {
    onSuccess: () => { deleteTarget.value = null },
  })
}
function toggleStatus(user) {
  router.patch(route('admin.users.toggle-status', user.id))
}

let searchTimer = null
function debouncedSearch() {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(applyFilters, 400)
}
function applyFilters() {
  router.get(route('admin.users.index'), filters.value, {
    preserveState: true, replace: true,
  })
}

function initials(name) {
  return name?.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2) || '?'
}
const colors = ['#C9A96E','#6366F1','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4']
function avatarBg(name) { return colors[(name?.charCodeAt(0) || 0) % colors.length] }

const roleMeta = {
  super_admin: { label: 'Super Admin', cls: 'badge-gold' },
  admin:       { label: 'Admin',       cls: 'badge-gold' },
  manager:     { label: 'Менеджер',    cls: 'badge-blue' },
  sales:       { label: 'Продажи',     cls: 'badge-green' },
  editor:      { label: 'Редактор',    cls: 'badge-purple' },
  viewer:      { label: 'Наблюдатель', cls: 'badge-gray' },
  client:      { label: 'Клиент',      cls: 'badge-gray' },
}
function roleLabel(r) { return roleMeta[r]?.label || r }
function roleBadge(r) { return roleMeta[r]?.cls || 'badge-gray' }
</script>
