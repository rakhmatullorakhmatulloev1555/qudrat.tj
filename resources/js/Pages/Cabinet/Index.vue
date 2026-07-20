<template>
  <MainLayout title="Личный кабинет — QUDRAT">

    <!-- Welcome banner (shows once after registration) -->
    <Transition name="slide-down">
      <div v-if="showWelcome"
        class="fixed top-20 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3 px-6 py-3.5 rounded-2xl shadow-2xl"
        style="background: linear-gradient(135deg,#C9A96E,#B8935A); min-width:260px">
        <svg class="w-5 h-5 text-[#0A0800] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
        </svg>
        <span class="text-[#0A0800] font-semibold text-sm">Аккаунт создан! Добро пожаловать!</span>
        <button @click="showWelcome = false" class="ml-auto text-[#0A0800]/60 hover:text-[#0A0800]">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
    </Transition>

    <!-- Edit profile modal -->
    <Transition name="fade">
      <div v-if="editOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" @click.self="editOpen = false">
        <div class="w-full max-w-md rounded-2xl p-7 shadow-2xl" style="background:#0F1420; border:1px solid rgba(201,169,110,0.15)">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-white font-semibold text-lg">Редактировать профиль</h3>
            <button @click="editOpen = false" class="text-gray-500 hover:text-white transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
          <form @submit.prevent="saveProfile" class="space-y-4">
            <div>
              <label class="cab-label">Имя и фамилия</label>
              <input v-model="editForm.name" type="text" class="cab-input" required/>
            </div>
            <div>
              <label class="cab-label">Телефон</label>
              <input v-model="editForm.phone" type="tel" class="cab-input" required/>
            </div>
            <div class="flex gap-3 pt-2">
              <button type="button" @click="editOpen = false"
                class="flex-1 py-3 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:border-white/20 transition-all text-sm font-medium">
                Отмена
              </button>
              <button type="submit" :disabled="editLoading"
                class="flex-1 py-3 rounded-xl font-semibold text-sm text-[#0A0800] transition-all disabled:opacity-60"
                style="background: linear-gradient(135deg,#C9A96E,#B8935A)">
                {{ editLoading ? 'Сохранение...' : 'Сохранить' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>

    <div class="min-h-screen" style="background: linear-gradient(180deg, #080B12 0%, #0A0E1A 100%)">

      <!-- ── HERO HEADER ── -->
      <section class="pt-32 pb-12 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none">
          <div class="absolute top-0 right-0 w-[500px] h-[400px] opacity-3"
            style="background: radial-gradient(ellipse at top right, #C9A96E, transparent)"></div>
        </div>
        <div class="max-w-5xl mx-auto px-6">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
            <!-- Avatar + greeting -->
            <div class="flex items-center gap-5">
              <div class="w-16 h-16 rounded-2xl flex items-center justify-center flex-shrink-0 text-[#0A0800] font-bold text-2xl"
                style="background: linear-gradient(135deg, #C9A96E, #B8935A)">
                {{ initials }}
              </div>
              <div>
                <p class="text-gold text-xs font-semibold uppercase tracking-widest mb-1">Личный кабинет</p>
                <h1 class="text-white font-bold text-2xl leading-tight">
                  Добро пожаловать,<br>{{ client.name }}!
                </h1>
              </div>
            </div>
            <!-- Meta + actions -->
            <div class="flex items-center gap-3">
              <button @click="openEdit"
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:border-gold/30 transition-all text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                </svg>
                Редактировать
              </button>
              <button @click="logout"
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-red-500/20 text-red-400 hover:bg-red-500/10 transition-all text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25"/>
                </svg>
                Выйти
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- ── MAIN CONTENT ── -->
      <section class="pb-28 max-w-5xl mx-auto px-6">
        <div class="grid lg:grid-cols-3 gap-6">

          <!-- Left: Profile card -->
          <div class="lg:col-span-1 space-y-5">

            <!-- Profile card -->
            <div class="rounded-2xl p-6" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07)">
              <div class="text-gold text-[10px] font-bold uppercase tracking-widest mb-5">Профиль</div>

              <div class="space-y-4">
                <div>
                  <div class="text-gray-500 text-xs mb-1">Имя</div>
                  <div class="text-white font-medium">{{ client.name }}</div>
                </div>
                <div>
                  <div class="text-gray-500 text-xs mb-1">Телефон</div>
                  <div class="text-white">{{ client.phone || '—' }}</div>
                </div>
                <div>
                  <div class="text-gray-500 text-xs mb-1">Email</div>
                  <div class="text-white text-sm">{{ client.email }}</div>
                </div>
                <div>
                  <div class="text-gray-500 text-xs mb-1">Клиент с</div>
                  <div class="text-white text-sm">{{ client.since }}</div>
                </div>
                <div class="pt-2 border-t border-white/5">
                  <div class="text-gray-500 text-xs mb-2">Направление интереса</div>
                  <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium"
                    style="background: rgba(201,169,110,0.1); border: 1px solid rgba(201,169,110,0.2); color: #C9A96E">
                    <span>{{ interestIcon }}</span>
                    {{ interestLabel }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Status card -->
            <div class="rounded-2xl p-6" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07)">
              <div class="text-gold text-[10px] font-bold uppercase tracking-widest mb-4">Статус заявки</div>
              <div class="flex items-center gap-3">
                <div class="w-2 h-2 rounded-full bg-gold animate-pulse"></div>
                <span class="text-white text-sm font-medium">Новая заявка</span>
              </div>
              <p class="text-gray-500 text-xs mt-3 leading-relaxed">
                Наш менеджер рассматривает вашу заявку. Ожидайте звонок в рабочее время (09:00–18:00).
              </p>
            </div>
          </div>

          <!-- Right: Quick actions + info -->
          <div class="lg:col-span-2 space-y-6">

            <!-- Welcome / manager message -->
            <div class="rounded-2xl p-7 relative overflow-hidden"
              style="background: linear-gradient(135deg, rgba(201,169,110,0.08) 0%, rgba(201,169,110,0.03) 100%); border: 1px solid rgba(201,169,110,0.15)">
              <div class="absolute top-0 right-0 w-40 h-40 opacity-5" style="background: radial-gradient(circle, #C9A96E, transparent)"></div>
              <div class="relative">
                <h2 class="text-white font-semibold text-lg mb-2">Ваша заявка принята ✓</h2>
                <p class="text-gray-400 text-sm leading-relaxed mb-4">
                  Персональный менеджер QUDRAT свяжется с вами в течение 30 минут для уточнения деталей и подбора наилучшего предложения.
                </p>
                <div class="flex flex-wrap gap-3">
                  <a href="tel:+992000000000"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-[#0A0800] text-sm font-semibold transition-all"
                    style="background: linear-gradient(135deg,#C9A96E,#B8935A)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/>
                    </svg>
                    Позвонить нам
                  </a>
                  <a href="https://t.me/qudrat_tj" target="_blank"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/10 text-gray-300 hover:border-gold/30 hover:text-white transition-all text-sm font-medium">
                    Написать в Telegram
                  </a>
                </div>
              </div>
            </div>

            <!-- Quick nav cards -->
            <div>
              <div class="text-gray-500 text-xs font-semibold uppercase tracking-widest mb-4">Навигация по сайту</div>
              <div class="grid sm:grid-cols-2 gap-4">
                <Link v-for="nav in navCards" :key="nav.route" :href="route(nav.route)"
                  class="group flex items-center gap-4 p-5 rounded-2xl border transition-all duration-300 hover:border-gold/30"
                  style="background: rgba(255,255,255,0.03); border-color: rgba(255,255,255,0.07)">
                  <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 transition-colors"
                    style="background: rgba(201,169,110,0.08); border: 1px solid rgba(201,169,110,0.15)">
                    <span class="text-lg">{{ nav.icon }}</span>
                  </div>
                  <div>
                    <div class="text-white font-medium text-sm group-hover:text-gold transition-colors">{{ nav.label }}</div>
                    <div class="text-gray-500 text-xs mt-0.5">{{ nav.desc }}</div>
                  </div>
                  <svg class="w-4 h-4 text-gray-600 group-hover:text-gold ml-auto transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                  </svg>
                </Link>
              </div>
            </div>

            <!-- Info block: what's next -->
            <div class="rounded-2xl p-7" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06)">
              <div class="text-gold text-[10px] font-bold uppercase tracking-widest mb-5">Что дальше?</div>
              <div class="space-y-4">
                <div v-for="(step, i) in nextSteps" :key="i" class="flex items-start gap-4">
                  <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 text-xs font-bold"
                    :style="i === 0 ? 'background:rgba(201,169,110,0.15);color:#C9A96E;border:1px solid rgba(201,169,110,0.3)' : 'background:rgba(255,255,255,0.04);color:#6B7280;border:1px solid rgba(255,255,255,0.08)'">
                    {{ i + 1 }}
                  </div>
                  <div>
                    <div class="text-sm font-medium" :class="i === 0 ? 'text-white' : 'text-gray-500'">{{ step.title }}</div>
                    <div class="text-xs text-gray-600 mt-0.5">{{ step.desc }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

  </MainLayout>
</template>

<script setup>
import { ref, computed, reactive, onMounted } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'

const props = defineProps({
  client: Object,
  flash:  Object,
})

// ── Welcome banner ──
const showWelcome = ref(false)
onMounted(() => {
  if (props.flash?.registered) {
    showWelcome.value = true
    setTimeout(() => { showWelcome.value = false }, 5000)
  }
})

// ── Computed ──
const initials = computed(() => {
  const parts = (props.client.name || '').split(' ')
  return parts.map(p => p[0]).join('').toUpperCase().slice(0, 2)
})

const interestMap = {
  apartment:  { label: 'Покупка квартиры',      icon: '🏠' },
  invest:     { label: 'Инвестиции',             icon: '📈' },
  commercial: { label: 'Коммерческая недвижимость', icon: '🏢' },
  mining:     { label: 'Партнёрство / Горнодобыча', icon: '⛏️' },
}

const interestLabel = computed(() => interestMap[props.client.interest]?.label ?? 'Общий интерес')
const interestIcon  = computed(() => interestMap[props.client.interest]?.icon  ?? '📋')

// ── Nav cards ──
const navCards = [
  { route: 'objects',   icon: '🏗️', label: 'Наши объекты',       desc: 'Квартиры и планировки' },
  { route: 'progress',  icon: '📊', label: 'Ход строительства',   desc: 'Актуальные фото и отчёты' },
  { route: 'mining',    icon: '⛏️', label: 'Горнодобыча',         desc: 'Партнёрство и поставки' },
  { route: 'contacts',  icon: '📞', label: 'Контакты',            desc: 'Офисы и адреса' },
  { route: 'about',     icon: '🏛️', label: 'О компании',          desc: 'История и команда QUDRAT' },
]

// ── Next steps ──
const nextSteps = [
  { title: 'Ожидайте звонка менеджера',       desc: 'Мы перезвоним в течение 30 минут в рабочее время' },
  { title: 'Выберите квартиру или объект',    desc: 'Просмотрите доступные планировки на странице объектов' },
  { title: 'Встреча или онлайн-показ',        desc: 'Менеджер организует удобный формат знакомства с объектом' },
  { title: 'Оформление документов',           desc: 'Договор, нотариус, рассрочка — всё под ключ' },
]

// ── Edit profile ──
const editOpen    = ref(false)
const editLoading = ref(false)
const editForm    = reactive({ name: '', phone: '' })

function openEdit() {
  editForm.name  = props.client.name  || ''
  editForm.phone = props.client.phone || ''
  editOpen.value = true
}

function saveProfile() {
  editLoading.value = true
  router.put(route('client.profile.update'), { ...editForm }, {
    onSuccess() { editOpen.value = false; editLoading.value = false },
    onFinish()  { editLoading.value = false },
  })
}

// ── Logout ──
function logout() {
  router.post(route('client.logout'))
}
</script>

<style scoped>
.cab-label {
  display: block;
  font-size: 0.7rem;
  font-weight: 600;
  color: #6B7280;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  margin-bottom: 0.5rem;
}
.cab-input {
  display: block;
  width: 100%;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 0.75rem;
  padding: 0.75rem 1rem;
  color: #fff;
  font-size: 0.875rem;
  outline: none;
  transition: border-color 0.2s;
}
.cab-input:focus { border-color: rgba(201,169,110,0.4); }

/* Transitions */
.slide-down-enter-active, .slide-down-leave-active { transition: all 0.4s cubic-bezier(0.4,0,0.2,1); }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translate(-50%,-20px); }

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
