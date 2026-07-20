<template>
  <div class="min-h-screen flex flex-col items-center justify-center p-6 relative overflow-hidden"
    style="background: linear-gradient(135deg, #080B12 0%, #0F1420 50%, #080B12 100%)">
    <Head :title="`Ошибка ${status}`" />

    <!-- Ambient glow -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
      <div class="absolute top-1/3 left-1/2 -translate-x-1/2 w-[600px] h-[600px] rounded-full opacity-[0.06]"
        style="background: radial-gradient(circle, #C9A96E, transparent); filter: blur(80px)"></div>
      <div class="absolute bottom-0 right-0 w-[400px] h-[400px] rounded-full opacity-[0.03]"
        style="background: radial-gradient(circle, #4A6CF7, transparent); filter: blur(80px)"></div>
    </div>

    <main class="relative max-w-xl w-full text-center">

      <!-- Status icon -->
      <div class="flex justify-center mb-8">
        <div class="w-20 h-20 rounded-2xl flex items-center justify-center"
          style="background: rgba(201,169,110,0.08); border: 1px solid rgba(201,169,110,0.2)">
          <component :is="statusIcon" />
        </div>
      </div>

      <!-- Status code -->
      <div class="text-[96px] leading-none font-bold mb-2 select-none"
        style="color: rgba(201,169,110,0.15); text-shadow: 0 0 60px rgba(201,169,110,0.08)">
        {{ status }}
      </div>

      <!-- Title -->
      <h1 class="text-2xl md:text-3xl font-bold text-white mb-4">
        {{ title }}
      </h1>

      <!-- Description -->
      <p class="text-gray-400 text-base mb-10 leading-relaxed max-w-md mx-auto">
        {{ description }}
      </p>

      <!-- Actions -->
      <div class="flex flex-col sm:flex-row gap-3 justify-center mb-10">
        <Link :href="route('home')"
          class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold text-sm transition-all duration-200 hover:opacity-90 active:scale-95"
          style="background: linear-gradient(135deg, #C9A96E, #B8935A); color: #0A0800;">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            <polyline stroke-linecap="round" stroke-linejoin="round" points="9 22 9 12 15 12 15 22"/>
          </svg>
          На главную
        </Link>

        <button v-if="status === 419"
          @click="() => window.location.reload()"
          class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold text-sm transition-all duration-200 hover:bg-white/10 active:scale-95"
          style="background: rgba(255,255,255,0.04); border: 1px solid rgba(201,169,110,0.2); color: #C9A96E;">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Обновить страницу
        </button>

        <button v-else
          @click="() => history.back()"
          class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold text-sm transition-all duration-200 hover:bg-white/10 active:scale-95"
          style="background: rgba(255,255,255,0.04); border: 1px solid rgba(201,169,110,0.2); color: #C9A96E;">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
          Назад
        </button>
      </div>

      <!-- Quick links for 404 -->
      <div v-if="status === 404" class="border-t border-white/5 pt-8">
        <p class="text-gray-600 text-[11px] uppercase tracking-widest mb-4">Популярные разделы</p>
        <div class="flex flex-wrap gap-2 justify-center">
          <Link v-for="page in quickLinks" :key="page.route" :href="route(page.route)"
            class="px-4 py-2 rounded-lg text-sm text-gray-400 hover:text-gold transition-colors"
            style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06)">
            {{ page.label }}
          </Link>
        </div>
      </div>

      <!-- Contact for 5xx -->
      <div v-if="status >= 500" class="border-t border-white/5 pt-8">
        <p class="text-gray-500 text-sm">
          Если ошибка повторяется —
          <a href="mailto:info@qudrat.tj" class="text-gold hover:underline">напишите нам</a>
        </p>
      </div>

      <!-- Logo -->
      <div class="mt-12">
        <Link :href="route('home')"
          class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-400 transition-colors text-xs tracking-widest uppercase">
          <span class="font-bold tracking-widest">QUDRAT</span>
          <span class="text-gold/50">·</span>
          <span>На главную</span>
        </Link>
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed, h } from 'vue'
import { Link, Head } from '@inertiajs/vue3'

const props = defineProps({
  status: { type: Number, required: true },
})

// Уникальная SVG-иконка для каждого кода ошибки
const iconPaths = {
  403: 'M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z',
  404: 'M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z',
  419: 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
  429: 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z',
  500: 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z',
  503: 'M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z',
}

const statusIcon = computed(() => {
  const d = iconPaths[props.status] ?? iconPaths[500]
  return () => h('svg', { class: 'w-10 h-10 text-gold', fill: 'none', stroke: 'currentColor', 'stroke-width': '1.5', viewBox: '0 0 24 24', 'aria-hidden': 'true' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d }),
  ])
})

const titles = {
  403: 'Доступ запрещён',
  404: 'Страница не найдена',
  419: 'Сессия устарела',
  429: 'Слишком много запросов',
  500: 'Ошибка сервера',
  503: 'Технические работы',
}

const descriptions = {
  403: 'У вас нет прав для просмотра этой страницы. Если считаете это ошибкой — обратитесь к администратору.',
  404: 'Запрашиваемая страница не существует или была перемещена.',
  419: 'Страница устарела. Обновите её и попробуйте снова.',
  429: 'Вы отправили слишком много запросов. Подождите немного и повторите попытку.',
  500: 'Внутренняя ошибка сервера. Мы уже работаем над её устранением.',
  503: 'Сайт временно недоступен в связи с техническим обслуживанием. Зайдите позже.',
}

const title       = computed(() => titles[props.status]       ?? 'Произошла ошибка')
const description = computed(() => descriptions[props.status] ?? 'Что-то пошло не так. Пожалуйста, попробуйте позже.')

const quickLinks = [
  { route: 'objects',   label: 'Квартиры' },
  { route: 'about',     label: 'О компании' },
  { route: 'mining',    label: 'Горнодобыча' },
  { route: 'contacts',  label: 'Контакты' },
]
</script>
