<template>
  <AdminLayout title="Роли и права">
    <Head title="Роли и права" />
    <div class="space-y-6">

      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-white">Роли и права доступа</h1>
          <p class="text-gray-400 text-sm mt-1">Управление RBAC — кто что может делать</p>
        </div>
        <button @click="openCreate" class="btn-primary flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
          </svg>
          Создать роль
        </button>
      </div>

      <!-- Roles cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <div v-for="role in roles" :key="role.id"
          class="card border border-white/5 hover:border-gold/20 transition-colors">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg"
                :class="roleIconBg(role.name)">
                {{ roleIcon(role.name) }}
              </div>
              <div>
                <div class="font-bold text-white">{{ roleLabel(role.name) }}</div>
                <div class="text-xs text-gray-500 font-mono">{{ role.name }}</div>
              </div>
            </div>
            <div class="flex items-center gap-1">
              <span v-if="role.is_system"
                class="text-[10px] bg-gold/15 text-gold px-2 py-0.5 rounded font-semibold">
                SYSTEM
              </span>
              <button v-if="!role.is_system" @click="openEdit(role)" class="icon-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                </svg>
              </button>
              <button @click="openPermissions(role)" class="icon-btn text-gold" title="Настроить права">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/>
                </svg>
              </button>
              <button v-if="!role.is_system" @click="confirmDeleteRole(role)"
                class="icon-btn text-red-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916"/>
                </svg>
              </button>
            </div>
          </div>

          <div class="flex items-center justify-between text-sm mb-3">
            <span class="text-gray-400">
              <span class="text-white font-semibold">{{ role.users_count }}</span> пользователей
            </span>
            <span class="text-gray-400">
              <span class="text-white font-semibold">{{ role.permissions.length }}</span> прав
            </span>
          </div>

          <!-- Permission pills preview -->
          <div class="flex flex-wrap gap-1">
            <span v-for="p in role.permissions.slice(0,6)" :key="p"
              class="text-[10px] bg-white/5 text-gray-400 px-2 py-0.5 rounded">
              {{ p }}
            </span>
            <span v-if="role.permissions.length > 6"
              class="text-[10px] bg-gold/10 text-gold px-2 py-0.5 rounded">
              +{{ role.permissions.length - 6 }}
            </span>
            <span v-if="role.name === 'super_admin'"
              class="text-[10px] bg-gold/10 text-gold px-2 py-0.5 rounded">
              ∞ все права
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Permission Matrix Modal -->
    <Teleport to="body">
      <div v-if="permModal" class="modal-overlay" @click.self="permModal = false">
        <div class="modal-box max-w-3xl max-h-[85vh] flex flex-col">
          <div class="flex items-center justify-between mb-6 shrink-0">
            <h2 class="text-lg font-bold text-white">
              Права: {{ roleLabel(editingRole?.name) }}
            </h2>
            <button @click="permModal = false" class="icon-btn">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <div class="overflow-y-auto flex-1 space-y-5 pr-1">
            <div v-for="(perms, module) in permissions" :key="module">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs font-bold text-gold uppercase tracking-widest">
                  {{ moduleLabel(module) }}
                </h3>
                <button @click="toggleModule(module, perms)"
                  class="text-xs text-gray-500 hover:text-white transition-colors">
                  {{ allChecked(perms) ? 'Снять все' : 'Выбрать все' }}
                </button>
              </div>
              <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                <label v-for="perm in perms" :key="perm"
                  class="flex items-center gap-2 cursor-pointer group">
                  <input type="checkbox" :value="perm" v-model="selectedPerms"
                    class="accent-gold w-4 h-4 cursor-pointer" />
                  <span class="text-sm text-gray-300 group-hover:text-white transition-colors">
                    {{ permLabel(perm) }}
                  </span>
                </label>
              </div>
            </div>
          </div>

          <div class="flex gap-3 pt-4 mt-4 border-t border-white/5 shrink-0">
            <button @click="savePermissions" :disabled="saving" class="btn-primary flex-1">
              {{ saving ? 'Сохранение...' : 'Сохранить права' }}
            </button>
            <button @click="permModal = false" class="btn-secondary flex-1">Отмена</button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Create Role Modal -->
    <Teleport to="body">
      <div v-if="createModal" class="modal-overlay" @click.self="createModal = false">
        <div class="modal-box max-w-sm">
          <h2 class="text-lg font-bold text-white mb-5">Создать роль</h2>
          <form @submit.prevent="createRole" class="space-y-4">
            <div>
              <label class="label">Название роли *</label>
              <input v-model="newRoleName" class="input w-full" placeholder="sales_lead" required />
              <p class="text-xs text-gray-500 mt-1">Только латинские буквы и _</p>
            </div>
            <div class="flex gap-3">
              <button type="submit" class="btn-primary flex-1">Создать</button>
              <button type="button" @click="createModal = false" class="btn-secondary flex-1">Отмена</button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- Delete Role Confirm -->
    <Teleport to="body">
      <div v-if="deleteRoleTarget" class="modal-overlay" @click.self="deleteRoleTarget = null">
        <div class="modal-box max-w-sm text-center space-y-4">
          <div class="w-14 h-14 rounded-full bg-red-500/15 flex items-center justify-center mx-auto">
            <svg class="w-7 h-7 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
            </svg>
          </div>
          <h3 class="text-white font-bold">Удалить роль «{{ deleteRoleTarget?.name }}»?</h3>
          <p class="text-gray-400 text-sm">Все пользователи с этой ролью потеряют доступ.</p>
          <div class="flex gap-3">
            <button @click="deleteRole" class="btn-danger flex-1">Удалить</button>
            <button @click="deleteRoleTarget = null" class="btn-secondary flex-1">Отмена</button>
          </div>
        </div>
      </div>
    </Teleport>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import axios from 'axios'

const props = defineProps({
  roles:       { type: Array, default: () => [] },
  permissions: { type: Object, default: () => ({}) },
})

const permModal     = ref(false)
const createModal   = ref(false)
const editingRole   = ref(null)
const selectedPerms = ref([])
const saving        = ref(false)
const newRoleName   = ref('')
const deleteRoleTarget = ref(null)

function openPermissions(role) {
  editingRole.value   = role
  selectedPerms.value = [...role.permissions]
  permModal.value     = true
}
function openCreate() { newRoleName.value = ''; createModal.value = true }
function openEdit(role) { openPermissions(role) }
function confirmDeleteRole(role) { deleteRoleTarget.value = role }

function allChecked(perms) {
  return perms.every(p => selectedPerms.value.includes(p))
}
function toggleModule(module, perms) {
  if (allChecked(perms)) {
    selectedPerms.value = selectedPerms.value.filter(p => !perms.includes(p))
  } else {
    const toAdd = perms.filter(p => !selectedPerms.value.includes(p))
    selectedPerms.value.push(...toAdd)
  }
}

function savePermissions() {
  saving.value = true
  router.put(route('admin.roles.update', editingRole.value.id),
    { permissions: selectedPerms.value },
    { onSuccess: () => { permModal.value = false }, onFinish: () => { saving.value = false } }
  )
}
function createRole() {
  router.post(route('admin.roles.store'), { name: newRoleName.value }, {
    onSuccess: () => { createModal.value = false },
  })
}
function deleteRole() {
  router.delete(route('admin.roles.destroy', deleteRoleTarget.value.id), {
    onSuccess: () => { deleteRoleTarget.value = null },
  })
}

const rolesMeta = {
  super_admin: { label: 'Super Admin', icon: '👑', bg: 'bg-gold/15 text-gold' },
  admin:       { label: 'Администратор', icon: '🛡️', bg: 'bg-gold/10 text-gold' },
  manager:     { label: 'Менеджер',    icon: '📊', bg: 'bg-blue-500/10 text-blue-400' },
  sales:       { label: 'Продажи',     icon: '💼', bg: 'bg-green-500/10 text-green-400' },
  editor:      { label: 'Редактор',    icon: '✏️', bg: 'bg-purple-500/10 text-purple-400' },
  viewer:      { label: 'Наблюдатель', icon: '👁️', bg: 'bg-white/5 text-gray-400' },
  client:      { label: 'Клиент',      icon: '👤', bg: 'bg-white/5 text-gray-400' },
}
function roleLabel(r)   { return rolesMeta[r]?.label || r }
function roleIcon(r)    { return rolesMeta[r]?.icon  || '🔑' }
function roleIconBg(r)  { return rolesMeta[r]?.bg    || 'bg-white/5 text-gray-400' }

const modulesLabels = {
  dashboard: 'Dashboard', users: 'Пользователи', leads: 'Заявки (CRM)',
  contacts: 'Контакты', deals: 'Сделки', projects: 'Проекты',
  apartments: 'Квартиры', documents: 'Документы', cms: 'CMS',
  translations: 'Переводы', seo: 'SEO', media: 'Медиа',
  mining: 'Горнодобыча', news: 'Новости', partners: 'Партнёры',
  testimonials: 'Отзывы', gallery: 'Галерея',
  analytics: 'Аналитика', settings: 'Настройки', audit: 'Аудит',
  security: 'Безопасность', integrations: 'Интеграции',
  notifications: 'Уведомления', other: 'Прочее',
}
function moduleLabel(m) { return modulesLabels[m] || m }

const actionLabels = {
  view: 'Просмотр', create: 'Создание', edit: 'Редактирование',
  delete: 'Удаление', export: 'Экспорт', approve: 'Одобрение',
  manage: 'Управление', send: 'Отправка', publish: 'Публикация',
}
function permLabel(p) {
  const parts = p.split(' ')
  const action = actionLabels[parts[0]] || parts[0]
  return action
}
</script>
