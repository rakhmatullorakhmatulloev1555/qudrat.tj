<template>
  <div class="min-h-screen flex" style="background:#0F172A; color:#E2E8F0">

    <!-- Mobile overlay -->
    <Transition enter-from-class="opacity-0" enter-to-class="opacity-100" leave-from-class="opacity-100" leave-to-class="opacity-0" enter-active-class="transition-opacity duration-200" leave-active-class="transition-opacity duration-200">
      <div v-if="mobileOpen" class="fixed inset-0 z-30 bg-black/60 lg:hidden" @click="mobileOpen = false"></div>
    </Transition>

    <!-- Sidebar spacer: резервирует место в flex-потоке, чтобы контент не уходил под сайдбар -->
    <div class="hidden lg:block flex-shrink-0 transition-all duration-300"
      :class="sidebarOpen ? 'w-64' : 'w-16'"></div>

    <!-- Sidebar -->
    <aside
      class="fixed top-0 left-0 h-full z-40 flex flex-col transition-all duration-300 w-64"
      :class="[
        sidebarOpen ? 'lg:w-64' : 'lg:w-16',
        mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
      ]"
      style="background:#1E293B; border-right:1px solid rgba(255,255,255,0.06)"
    >
      <!-- Logo -->
      <div class="flex items-center gap-3 px-4 py-5 border-b border-white/6 min-h-[64px]">
        <img :src="'/images/adminlogo.png'" class="w-8 h-8 rounded-lg object-contain flex-shrink-0" alt="QUDRAT" />
        <span v-if="sidebarOpen || mobileOpen" class="font-bold text-white tracking-wider text-sm truncate">QUDRAT CMS</span>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 py-4 overflow-y-auto overflow-x-hidden" aria-label="Навигация CMS">
        <div v-for="group in navGroups" :key="group.label" class="mb-4">
          <div v-if="sidebarOpen || mobileOpen" class="px-4 mb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">
            {{ group.label }}
          </div>
          <Link
            v-for="item in group.items" :key="item.route"
            :href="route(item.route)"
            @click="mobileOpen = false"
            class="flex items-center gap-3 px-4 py-2.5 mx-2 rounded-lg transition-all duration-200 group relative"
            :class="isActive(item.route)
              ? 'bg-gold/15 text-gold'
              : 'text-slate-400 hover:text-white hover:bg-white/5'"
          >
            <span class="flex-shrink-0 w-5 h-5 flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" :d="item.icon" /></svg>
            </span>
            <span v-if="sidebarOpen || mobileOpen" class="text-sm font-medium truncate">{{ item.label }}</span>
            <!-- Tooltip when desktop-collapsed -->
            <div v-if="!sidebarOpen && !mobileOpen"
              class="absolute left-full ml-2 px-2 py-1 bg-slate-800 text-white text-xs rounded shadow-lg whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-50">
              {{ item.label }}
            </div>
          </Link>
        </div>
      </nav>

      <!-- User + toggle -->
      <div class="border-t border-white/6 p-3">
        <div v-if="sidebarOpen || mobileOpen" class="flex items-center gap-3 px-2 py-2 rounded-lg bg-white/5 mb-2">
          <img :src="'/images/adminlogo.png'" class="w-8 h-8 rounded-full object-contain flex-shrink-0" alt="Logo" />
          <div class="flex-1 min-w-0">
            <div class="text-sm font-medium text-white truncate">{{ $page.props.auth?.user?.name }}</div>
            <div class="text-[10px] text-slate-500 uppercase tracking-wider">{{ $page.props.auth?.user?.role }}</div>
          </div>
        </div>
        <!-- Desktop collapse toggle -->
        <button
          @click="sidebarOpen = !sidebarOpen"
          class="hidden lg:flex w-full items-center justify-center py-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition-colors"
          :aria-label="sidebarOpen ? 'Свернуть меню' : 'Развернуть меню'"
          :aria-expanded="sidebarOpen"
        >
          <svg class="w-4 h-4 transition-transform duration-300" :class="sidebarOpen ? '' : 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>
        <!-- Mobile close button -->
        <button
          @click="mobileOpen = false"
          class="lg:hidden flex w-full items-center justify-center gap-2 py-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition-colors text-sm"
          aria-label="Закрыть меню"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
          </svg>
          Закрыть
        </button>
      </div>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col min-h-screen min-w-0 transition-all duration-300">
      <!-- Top bar -->
      <header class="sticky top-0 z-30 flex items-center justify-between px-4 lg:px-6 py-3 lg:py-4 border-b border-white/6"
        style="background:rgba(15,23,42,0.95); backdrop-filter:blur(12px)">
        <div class="flex items-center gap-3 min-w-0">
          <!-- Mobile hamburger -->
          <button
            @click="mobileOpen = true"
            class="lg:hidden flex-shrink-0 w-9 h-9 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center text-slate-400 hover:text-white transition-colors"
            aria-label="Открыть меню"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
            </svg>
          </button>
          <div class="min-w-0">
            <h1 class="text-white font-semibold text-base lg:text-lg truncate">{{ computedTitle }}</h1>
            <p v-if="pageSubtitle" class="text-slate-500 text-xs hidden sm:block">{{ pageSubtitle }}</p>
          </div>
        </div>
        <div class="flex items-center gap-2 lg:gap-4 flex-shrink-0">
          <!-- Notifications -->
          <button class="relative w-9 h-9 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center text-slate-400 hover:text-white transition-colors" aria-label="Уведомления">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
            </svg>
          </button>
          <!-- Logout -->
          <form @submit.prevent="logout">
            <button type="submit" class="flex items-center gap-2 text-slate-400 hover:text-red-400 transition-colors text-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/>
              </svg>
              <span class="hidden sm:inline">Выйти</span>
            </button>
          </form>
        </div>
      </header>

      <!-- Page content -->
      <main class="flex-1 p-4 lg:p-6 min-w-0 overflow-x-auto">
        <slot />
      </main>
    </div>

    <!-- Toast уведомления -->
    <ToastContainer />
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import ToastContainer from '@/Components/Admin/ToastContainer.vue';
import { useToast } from '@/composables/useToast';

const page = usePage();
const sidebarOpen = ref(true);
const mobileOpen = ref(false);
const toast = useToast();

watch(() => page.props.flash, (flash) => {
  if (flash?.success) toast.success(flash.success);
  if (flash?.error)   toast.error(flash.error);
  if (flash?.warning) toast.warning(flash.warning);
  if (flash?.info)    toast.info(flash.info);
}, { deep: true, immediate: true });

const userInitials = computed(() => {
  const name = page.props.auth?.user?.name || 'A';
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
});

const props = defineProps({
  title:        { type: String, default: '' },
  pageTitle:    { type: String, default: 'Панель управления' },
  pageSubtitle: { type: String, default: '' },
});

const computedTitle = computed(() => props.title || props.pageTitle);

function isActive(routeName) {
  // Convert 'admin.leads.kanban' → '/admin/leads/kanban', 'admin.leads.index' → '/admin/leads'
  const path = '/' + routeName.replace(/\./g, '/').replace(/\/index$/, '')
  if (page.url === path) return true
  // Also highlight parent for detail pages (/admin/leads/42), but NOT for sibling routes (/admin/leads/kanban)
  if (page.url.startsWith(path + '/')) {
    const next = page.url.slice(path.length + 1).split('/')[0]
    return /^\d+/.test(next)
  }
  return false
}

function logout() {
  router.post(route('admin.logout'));
}

const navGroups = [
  {
    label: 'Главная',
    items: [
      {
        route: 'admin.dashboard',
        label: 'Панель управления',
        icon: 'M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z',
      },
    ],
  },
  {
    label: 'CRM',
    items: [
      {
        route: 'admin.leads.index',
        label: 'Заявки',
        icon: 'M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155',
      },
      {
        route: 'admin.leads.kanban',
        label: 'Kanban-доска',
        icon: 'M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z',
      },
      {
        route: 'admin.deals.index',
        label: 'Сделки',
        icon: 'M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z',
      },
      {
        route: 'admin.clients.index',
        label: 'Клиенты',
        icon: 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z',
      },
      {
        route: 'admin.pipelines.index',
        label: 'Воронка (настройки)',
        icon: 'M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75',
      },
    ],
  },
  {
    label: 'Недвижимость',
    items: [
      {
        route: 'admin.projects.index',
        label: 'Проекты',
        icon: 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3.75h.75m-.75 3.75h.75m3-7.5h.75m-.75 3.75h.75m-.75 3.75h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z',
      },
      {
        route: 'admin.apartments.index',
        label: 'Квартиры',
        icon: 'm2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25',
      },
    ],
  },
  {
    label: 'Горнодобыча',
    items: [
      {
        route: 'admin.mining.index',
        label: 'Партии угля',
        icon: 'M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125',
      },
    ],
  },
  {
    label: 'Бизнес',
    items: [
      {
        route: 'admin.partners.index',
        label: 'Партнёры',
        icon: 'M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z',
      },
      {
        route: 'admin.documents.index',
        label: 'Документы',
        icon: 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z',
      },
    ],
  },
  {
    label: 'Контент сайта',
    items: [
      {
        route: 'admin.settings.index',
        label: 'Настройки',
        icon: 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z',
      },
      {
        route: 'admin.testimonials.index',
        label: 'Отзывы',
        icon: 'M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z',
      },
      {
        route: 'admin.gallery.index',
        label: 'Галерея',
        icon: 'm2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z',
      },
      {
        route: 'admin.news.index',
        label: 'Новости',
        icon: 'M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z',
      },
      {
        route: 'admin.scene3d.index',
        label: '3D Квартиры',
        icon: 'M21 7.5 12 2.25 3 7.5m18 0-9 5.25m9-5.25v9L12 21.75m0-8.25L3 7.5m9 5.25v8.25M3 7.5v9L12 21.75',
      },
    ],
  },
  {
    label: 'CMS',
    items: [
      {
        route: 'admin.cms.index',
        label: 'Редактор страниц',
        icon: 'M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5a17.92 17.92 0 0 1-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418',
      },
      {
        route: 'admin.showcase.index',
        label: 'Знаковые объекты',
        icon: 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z',
      },
      {
        route: 'admin.complex.index',
        label: 'Конструктор комплекса',
        icon: 'M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z',
      },
      {
        route: 'admin.construction.index',
        label: 'Прогресс стройки',
        icon: 'M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6',
      },
    ],
  },
  {
    label: 'IAM / Доступ',
    items: [
      {
        route: 'admin.users.index',
        label: 'Пользователи',
        icon: 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z',
      },
      {
        route: 'admin.roles.index',
        label: 'Роли и права',
        icon: 'M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z',
      },
    ],
  },
  {
    label: 'Безопасность',
    items: [
      {
        route: 'admin.2fa.setup',
        label: 'Двухфакторная 2FA',
        icon: 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z',
      },
      {
        route: 'admin.security.audit-logs',
        label: 'Журнал аудита',
        icon: 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25M9 12l2.25 2.25L15 9M8.25 6.75H5.625c-.621 0-1.125.504-1.125 1.125v12c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z',
      },
      {
        route: 'admin.security.login-history',
        label: 'История входов',
        icon: 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
      },
    ],
  },
];
</script>
