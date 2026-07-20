<template>
  <AdminLayout>
    <Head title="3D Квартиры" />

    <div class="mb-6">
      <h1 class="text-2xl font-bold text-white">3D Квартиры</h1>
      <p class="text-slate-400 text-sm mt-1">Управление интерактивными 3D-турами квартир, стилями и ассетами.</p>
    </div>

    <!-- Сводка -->
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
      <div v-for="s in statCards" :key="s.label" class="card flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center text-xl" :style="`background:${s.color}1a;color:${s.color}`">{{ s.icon }}</div>
        <div>
          <div class="text-2xl font-bold text-white leading-none">{{ s.value }}</div>
          <div class="text-slate-400 text-xs mt-1">{{ s.label }}</div>
        </div>
      </div>
    </div>

    <!-- Разделы (каркас Этапа 2) -->
    <div class="flex flex-wrap gap-2 mb-6">
      <span v-for="sec in sections" :key="sec"
        class="px-3 py-1.5 rounded-lg text-xs font-medium border border-white/8 text-slate-400 bg-white/3">
        {{ sec }}
      </span>
    </div>

    <div class="card">
      <!-- Поиск -->
      <div class="flex items-center justify-between mb-4 gap-3">
        <h2 class="font-semibold text-white">Квартиры</h2>
        <input v-model="search" @keyup.enter="doSearch" type="text" placeholder="Поиск по №…"
          class="input max-w-[220px]" />
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-white/5">
              <th class="th">№</th><th class="th">Проект</th><th class="th">Комнат</th>
              <th class="th">Площадь</th><th class="th">Этаж</th><th class="th">3D-тур</th><th class="th text-right">Действие</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="a in apartments.data" :key="a.id" class="border-b border-white/4 hover:bg-white/2">
              <td class="td font-semibold text-white">{{ a.number }}</td>
              <td class="td text-slate-300">{{ a.project || '—' }}</td>
              <td class="td text-slate-400">{{ a.rooms }}</td>
              <td class="td text-slate-400">{{ a.area }} м²</td>
              <td class="td text-slate-400">{{ a.floor }}</td>
              <td class="td">
                <span :class="a.enabled ? 'badge-green' : 'badge-gray'" class="badge">
                  {{ a.enabled ? 'Включён' : 'Выключен' }}
                </span>
              </td>
              <td class="td text-right">
                <button @click="toggle(a)" :disabled="busy === a.id"
                  :class="a.enabled ? 'btn-secondary' : 'btn-primary'" class="text-xs">
                  {{ a.enabled ? 'Выключить' : 'Включить 3D' }}
                </button>
              </td>
            </tr>
            <tr v-if="!apartments.data.length"><td colspan="7" class="td text-center text-slate-500 py-10">Квартиры не найдены</td></tr>
          </tbody>
        </table>
      </div>

      <Pagination v-if="apartments.links" :links="apartments.links" class="mt-4" />
    </div>

    <p class="text-slate-500 text-xs mt-5 leading-relaxed">
      💡 Включение 3D создаёт структуру хранения <code class="text-slate-400">storage/app/public/3d/apartment_&#123;id&#125;/</code>
      (models / textures / hdri / config). Загрузка GLB/HDRI/текстур, редакторы комнат, камер и hotspots —
      следующий шаг Этапа 2 (архитектура и API уже готовы).
    </p>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Admin/Pagination.vue'

const props = defineProps({
  apartments: { type: Object, default: () => ({ data: [], links: [] }) },
  filters:    { type: Object, default: () => ({}) },
  stats:      { type: Object, default: () => ({}) },
})

const search = ref(props.filters.search || '')
const busy = ref(null)

const statCards = [
  { label: 'Стилей интерьера', value: props.stats.styles ?? 0, icon: '🎨', color: '#C9A96E' },
  { label: 'Материалов',       value: props.stats.materials ?? 0, icon: '🧱', color: '#60A5FA' },
  { label: '3D-моделей',        value: props.stats.models ?? 0, icon: '📦', color: '#34D399' },
  { label: 'Включённых туров',  value: props.stats.enabled ?? 0, icon: '🏠', color: '#A78BFA' },
]

const sections = ['Квартиры', 'Комнаты', 'Стили', '3D модели', 'Текстуры', 'HDRI', 'Материалы', 'Маршруты камеры', 'Hotspots']

function doSearch() {
  router.get(route('admin.scene3d.index'), { search: search.value }, { preserveState: true, replace: true })
}

function toggle(a) {
  busy.value = a.id
  router.patch(route('admin.scene3d.toggle', a.id), {}, {
    preserveScroll: true,
    onFinish: () => { busy.value = null },
  })
}
</script>
