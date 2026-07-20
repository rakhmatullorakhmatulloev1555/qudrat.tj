<template>
  <AdminLayout title="Прогресс стройки">
    <Head title="Прогресс стройки" />
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
      <div>
        <h1 class="text-xl font-bold text-white">Прогресс строительства</h1>
        <p class="text-sm text-gray-400 mt-0.5">Обновления отображаются на публичной странице /progress</p>
      </div>
      <button @click="openCreate" class="btn-primary">+ Добавить запись</button>
    </div>

    <!-- Current progress card -->
    <div v-if="current" class="card mb-6 border border-gold/20">
      <div class="flex items-center gap-2 mb-3">
        <span class="badge badge-green text-xs">● Текущий статус</span>
        <span class="text-xs text-gray-400">{{ fmtDate(current.update_date) }}</span>
      </div>
      <div class="flex items-start justify-between gap-4 flex-wrap">
        <div class="flex-1 min-w-[200px]">
          <h3 class="text-lg font-bold text-white mb-1">{{ current.title }}</h3>
          <p v-if="current.description" class="text-sm text-gray-400">{{ current.description }}</p>
        </div>
        <div class="flex-shrink-0 text-center">
          <!-- Circular progress -->
          <div class="relative w-20 h-20">
            <svg class="w-20 h-20 -rotate-90" viewBox="0 0 80 80">
              <circle cx="40" cy="40" r="32" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="8"/>
              <circle cx="40" cy="40" r="32" fill="none" stroke="#C9A96E" stroke-width="8"
                stroke-linecap="round"
                :stroke-dasharray="`${current.progress * 2.01} 201`"/>
            </svg>
            <span class="absolute inset-0 flex items-center justify-center text-lg font-black text-gold">
              {{ current.progress }}%
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Timeline list -->
    <div class="card overflow-hidden p-0">
      <div class="px-5 py-3 border-b border-white/5 flex items-center justify-between">
        <span class="text-sm font-semibold text-white">Все записи</span>
        <span class="badge">{{ updates.total }}</span>
      </div>

      <div v-if="!updates.data.length" class="text-center py-16 text-gray-500">
        <div class="text-4xl mb-3">🏗️</div>
        <p>Записей нет. Добавьте первую!</p>
      </div>

      <div v-else class="divide-y divide-white/5">
        <div v-for="item in updates.data" :key="item.id"
          class="flex items-start gap-4 px-5 py-4 hover:bg-white/3 transition-colors group">

          <!-- Date column -->
          <div class="flex-shrink-0 w-20 text-center">
            <div class="text-xs font-bold text-gold">{{ fmtDay(item.update_date) }}</div>
            <div class="text-xs text-gray-500">{{ fmtMonth(item.update_date) }}</div>
          </div>

          <!-- Progress bar column -->
          <div class="flex-shrink-0 w-14 pt-1">
            <div class="h-1.5 bg-white/10 rounded-full overflow-hidden">
              <div class="h-full rounded-full transition-all"
                :class="progressColor(item.progress)"
                :style="{width: item.progress + '%'}"></div>
            </div>
            <div class="text-xs text-center mt-1 font-bold"
              :class="progressColor(item.progress).replace('bg-', 'text-')">
              {{ item.progress }}%
            </div>
          </div>

          <!-- Content -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
              <span class="font-semibold text-white text-sm">{{ item.title }}</span>
              <span v-if="item.is_current" class="badge badge-gold text-[10px]">Текущий</span>
              <span v-if="!item.is_published" class="badge badge-red text-[10px]">Скрыт</span>
            </div>
            <p v-if="item.description" class="text-xs text-gray-400 mt-0.5 truncate max-w-xl">
              {{ item.description }}
            </p>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-1 flex-shrink-0">
            <button @click="startEdit(item)"
              class="p-1.5 hover:bg-white/10 rounded-lg text-gray-400 hover:text-white transition-colors"
              title="Редактировать">✏️</button>
            <button @click="confirmDelete(item)"
              class="p-1.5 hover:bg-red-500/10 rounded-lg text-gray-400 hover:text-red-400 transition-colors"
              title="Удалить">🗑️</button>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="updates.last_page > 1" class="flex items-center justify-between px-5 py-3 border-t border-white/5">
        <span class="text-sm text-gray-400">{{ updates.from }}–{{ updates.to }} из {{ updates.total }}</span>
        <div class="flex gap-1">
          <Link v-for="link in updates.links" :key="link.label"
            :href="link.url ?? '#'"
            class="px-2.5 py-1 rounded text-xs transition-colors"
            :class="link.active ? 'bg-gold text-dark font-bold' : 'text-gray-400 hover:bg-white/5'"
            >{{ link.label.replace('&laquo;','«').replace('&raquo;','»') }}</Link>
        </div>
      </div>
    </div>

    <!-- Create / Edit Modal -->
    <Teleport to="body">
      <div v-if="modal.open" class="modal-overlay" @click.self="closeModal">
        <div class="modal-box w-full max-w-lg">
          <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-white">
              {{ modal.item ? 'Редактировать запись' : 'Новая запись' }}
            </h2>
            <button @click="closeModal" class="text-gray-400 hover:text-white text-xl leading-none">×</button>
          </div>

          <form @submit.prevent="submitModal" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-xs text-gray-400 mb-1">Дата обновления *</label>
                <input v-model="form.update_date" type="date" class="input w-full" required />
              </div>
              <div>
                <label class="block text-xs text-gray-400 mb-1">Прогресс (%) *</label>
                <input v-model.number="form.progress" type="number" min="0" max="100" class="input w-full" required />
              </div>
            </div>

            <!-- Visual progress slider -->
            <div>
              <input v-model.number="form.progress" type="range" min="0" max="100" class="w-full" />
              <div class="flex justify-between text-xs text-gray-500 mt-0.5">
                <span>0%</span>
                <span class="font-bold text-gold">{{ form.progress }}%</span>
                <span>100%</span>
              </div>
            </div>

            <div>
              <label class="block text-xs text-gray-400 mb-1">Заголовок *</label>
              <input v-model="form.title" type="text" class="input w-full"
                placeholder="например: Завершён монолитный каркас" required />
            </div>

            <div>
              <label class="block text-xs text-gray-400 mb-1">Описание</label>
              <textarea v-model="form.description" rows="3" class="input w-full resize-none"
                placeholder="Подробности о текущем этапе строительства…"></textarea>
            </div>

            <div class="flex items-center gap-6">
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="form.is_current" type="checkbox" class="toggle" />
                <span class="text-sm text-gray-300">Текущий статус</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="form.is_published" type="checkbox" class="toggle" />
                <span class="text-sm text-gray-300">Опубликован</span>
              </label>
            </div>

            <p v-if="form.is_current" class="text-xs text-gold bg-gold/10 rounded-lg px-3 py-2">
              ⚠️ Отметка "Текущий" уберёт этот статус у предыдущей записи
            </p>

            <div class="flex justify-end gap-2 pt-1">
              <button type="button" @click="closeModal" class="btn-secondary">Отмена</button>
              <button type="submit" class="btn-primary" :disabled="processing">
                {{ processing ? 'Сохранение…' : (modal.item ? 'Сохранить' : 'Добавить') }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Delete confirm -->
      <div v-if="deleteModal.open" class="modal-overlay" @click.self="deleteModal.open = false">
        <div class="modal-box w-full max-w-sm text-center">
          <div class="text-4xl mb-3">🗑️</div>
          <h3 class="text-lg font-bold text-white mb-2">Удалить запись?</h3>
          <p class="text-sm text-gray-400 mb-5 px-4">«{{ deleteModal.item?.title }}»</p>
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
  updates: Object,
})

const current = computed(() => props.updates.data.find(u => u.is_current) ?? null)

const processing = ref(false)
const modal       = reactive({ open: false, item: null })
const deleteModal = reactive({ open: false, item: null })

const form = reactive({
  update_date:  '',
  title:        '',
  description:  '',
  progress:     0,
  is_current:   false,
  is_published: true,
})

function openCreate() {
  modal.item = null
  Object.assign(form, { update_date: today(), title:'', description:'', progress:0, is_current:false, is_published:true })
  modal.open = true
}

function startEdit(item) {
  modal.item = item
  Object.assign(form, {
    update_date:  item.update_date?.split('T')[0] ?? item.update_date,
    title:        item.title,
    description:  item.description ?? '',
    progress:     item.progress,
    is_current:   !!item.is_current,
    is_published: !!item.is_published,
  })
  modal.open = true
}

function closeModal() {
  modal.open = false
  modal.item = null
}

function submitModal() {
  processing.value = true
  if (modal.item) {
    router.put(route('admin.construction.update', modal.item.id), form, {
      onSuccess: () => { closeModal(); processing.value = false },
      onError:   () => { processing.value = false },
    })
  } else {
    router.post(route('admin.construction.store'), form, {
      onSuccess: () => { closeModal(); processing.value = false },
      onError:   () => { processing.value = false },
    })
  }
}

function confirmDelete(item) {
  deleteModal.item = item
  deleteModal.open = true
}

function doDelete() {
  processing.value = true
  router.delete(route('admin.construction.destroy', deleteModal.item.id), {
    onSuccess: () => { deleteModal.open = false; processing.value = false },
    onError:   () => { processing.value = false },
  })
}

// Helpers
function today() {
  return new Date().toISOString().split('T')[0]
}
function fmtDate(dt) {
  if (!dt) return ''
  return new Date(dt).toLocaleDateString('ru-RU', { day:'2-digit', month:'long', year:'numeric' })
}
function fmtDay(dt) {
  if (!dt) return ''
  return new Date(dt).toLocaleDateString('ru-RU', { day:'2-digit' })
}
function fmtMonth(dt) {
  if (!dt) return ''
  return new Date(dt).toLocaleDateString('ru-RU', { month:'short', year:'2-digit' })
}
function progressColor(p) {
  if (p >= 70) return 'bg-green-400'
  if (p >= 40) return 'bg-yellow-400'
  return 'bg-blue-400'
}
</script>
