<template>
  <AdminLayout title="CMS — Страницы сайта">
    <Head title="CMS — Страницы" />
    <div class="space-y-6">
      <div>
        <h1 class="text-2xl font-bold text-white">CMS — Редактор страниц</h1>
        <p class="text-gray-400 text-sm mt-1">Управление контентом всех страниц сайта без кода</p>
      </div>

      <!-- Pages grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        <Link v-for="(info, key) in pages" :key="key"
          :href="route('admin.cms.page', key)"
          class="card border border-white/5 hover:border-gold/30 hover:bg-gold/3 transition-all group cursor-pointer">

          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-gold/10 flex items-center justify-center text-xl">
                {{ pageIcon(key) }}
              </div>
              <div>
                <div class="font-bold text-white group-hover:text-gold transition-colors">
                  {{ info.label }}
                </div>
                <div class="text-xs text-gray-500 font-mono">/{{ key }}</div>
              </div>
            </div>
            <svg class="w-5 h-5 text-gray-600 group-hover:text-gold transition-all group-hover:translate-x-1"
              fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
            </svg>
          </div>

          <div class="flex items-center gap-4 text-sm">
            <div>
              <span class="font-semibold"
                :class="info.sections > 0 ? 'text-white' : 'text-gray-600'">
                {{ info.sections }}
              </span>
              <span class="text-gray-500">/{{ info.schema_total }}</span>
              <span class="text-gray-500 ml-1">секций</span>
            </div>
            <!-- Progress bar -->
            <div class="flex-1 h-1 bg-white/5 rounded-full overflow-hidden">
              <div class="h-full rounded-full transition-all"
                :class="info.sections >= info.schema_total ? 'bg-green-500' : 'bg-gold'"
                :style="{ width: info.schema_total ? (info.sections / info.schema_total * 100) + '%' : '0%' }">
              </div>
            </div>
            <div class="flex gap-1">
              <span v-for="loc in info.locales" :key="loc"
                class="text-[10px] font-bold px-1.5 py-0.5 rounded uppercase"
                :class="loc === 'ru' ? 'bg-blue-500/20 text-blue-400'
                      : loc === 'en' ? 'bg-green-500/20 text-green-400'
                      : 'bg-orange-500/20 text-orange-400'">
                {{ loc }}
              </span>
            </div>
          </div>
        </Link>
      </div>

      <!-- Info block -->
      <div class="card border border-gold/10 bg-gold/3">
        <div class="flex gap-4">
          <div class="text-2xl">💡</div>
          <div>
            <div class="font-semibold text-white mb-1">Как работает CMS</div>
            <ul class="text-gray-400 text-sm space-y-1">
              <li>• Выберите страницу → нажмите «Создать из шаблона» → заполните секции</li>
              <li>• CMS-контент автоматически перекрывает переводы из файлов (RU/EN/TJ)</li>
              <li>• Изменения применяются мгновенно — обновляется кеш страницы</li>
              <li>• Прогресс-бар показывает сколько секций заполнено из схемы</li>
              <li>• Переключатель «Активна» скрывает секцию без удаления</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  pages: { type: Object, default: () => ({}) },
})

const icons = {
  home: '🏠', objects: '🏗️', mining: '⛏️', services: '🛠️',
  about: '🏢', investors: '📈', contacts: '📞', progress: '📊',
}
function pageIcon(key) { return icons[key] || '📄' }
</script>
