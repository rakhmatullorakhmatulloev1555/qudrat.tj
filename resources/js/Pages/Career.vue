<template>
  <MainLayout>
    <SeoHead
      :title="t('career.meta_title')"
      :ogTitle="t('career.meta_title')"
      :description="t('career.meta_desc')"
      path="/career"
    />

    <!-- ═══ VACANCIES ═══ -->
    <section id="vacancies" class="pt-36 pb-28" style="background:#070B16">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <div class="text-center mb-14">
          <div class="inline-flex items-center gap-4 mb-6">
            <div class="h-px w-10" style="background:linear-gradient(to right,transparent,rgba(201,169,110,0.5))"></div>
            <span class="text-gold/70 text-[9px] font-bold uppercase tracking-[0.5em]">{{ t('career.vac_badge') }}</span>
            <div class="h-px w-10" style="background:linear-gradient(to left,transparent,rgba(201,169,110,0.5))"></div>
          </div>
          <h2 class="font-bold text-white mb-3" style="font-size:clamp(24px,3vw,42px)">{{ t('career.vac_title') }}</h2>
          <p class="text-white/40 text-base">{{ t('career.vac_sub') }}</p>
        </div>

        <!-- Filter tabs -->
        <div class="flex flex-wrap gap-2 justify-center mb-12">
          <button v-for="f in filters" :key="f.key"
            @click="activeFilter = f.key"
            class="px-5 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-all duration-200 border"
            :style="activeFilter === f.key
              ? 'background:#C9A96E; color:#070B16; border-color:#C9A96E'
              : 'background:transparent; color:rgba(255,255,255,0.35); border-color:rgba(255,255,255,0.08)'">
            {{ f.label }}
          </button>
        </div>

        <!-- Vacancy grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
          <div v-for="vac in filteredVacancies" :key="vac.id"
            class="group rounded-2xl border border-white/6 hover:border-gold/25 transition-all duration-400 overflow-hidden cursor-default"
            style="background:#0C1220">
            <!-- Top accent -->
            <div class="h-0.5 w-full transition-all duration-500"
              :style="`background:${vac.color}`"></div>

            <div class="p-7">
              <!-- Dept + type -->
              <div class="flex items-center justify-between mb-5">
                <span class="text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full"
                  :style="`background:${vac.color}18; color:${vac.color}`">
                  {{ vac.dept }}
                </span>
                <span class="text-white/25 text-[10px] uppercase tracking-wider">{{ vac.type }}</span>
              </div>

              <!-- Title -->
              <h3 class="text-white font-bold text-lg mb-2 leading-snug group-hover:text-gold transition-colors duration-300">
                {{ vac.title }}
              </h3>

              <!-- Location + salary -->
              <div class="flex items-center gap-4 mb-4">
                <div class="flex items-center gap-1.5 text-white/35 text-xs">
                  <Icon name="map-pin" class="w-3.5 h-3.5 text-gold/60" :stroke-width="1.5" />
                  {{ vac.location }}
                </div>
                <div v-if="vac.salary" class="text-gold text-xs font-semibold">
                  {{ t('career.salary_from') }} {{ vac.salary }}
                </div>
              </div>

              <!-- Description -->
              <p class="text-white/40 text-sm leading-relaxed mb-6 line-clamp-3">{{ vac.desc }}</p>

              <!-- Requirements -->
              <ul class="space-y-1.5 mb-7">
                <li v-for="req in vac.reqs" :key="req"
                  class="flex items-start gap-2 text-white/30 text-xs">
                  <div class="w-1 h-1 rounded-full bg-gold/50 mt-1.5 shrink-0"></div>
                  {{ req }}
                </li>
              </ul>

              <!-- Apply button -->
              <button @click="openApply(vac.title)"
                class="w-full py-3 rounded-xl font-bold text-sm uppercase tracking-wider transition-all duration-300 hover:opacity-90"
                style="background:#C9A96E; color:#070B16">
                {{ t('career.apply_btn') }}
              </button>
            </div>
          </div>
        </div>

        <!-- Empty state -->
        <div v-if="filteredVacancies.length === 0" class="text-center py-20 text-white/25 text-sm">
          Вакансий в этой категории пока нет. Оставьте заявку ниже.
        </div>

      </div>
    </section>

    <!-- ═══ VALUES ═══ -->
    <section class="py-24 border-y border-white/5" style="background:#0C1220">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="text-center mb-14">
          <div class="inline-flex items-center gap-4 mb-6">
            <div class="h-px w-10" style="background:linear-gradient(to right,transparent,rgba(201,169,110,0.5))"></div>
            <span class="text-gold/70 text-[9px] font-bold uppercase tracking-[0.5em]">{{ t('career.val_badge') }}</span>
            <div class="h-px w-10" style="background:linear-gradient(to left,transparent,rgba(201,169,110,0.5))"></div>
          </div>
          <h2 class="font-bold text-white" style="font-size:clamp(22px,2.8vw,40px)">{{ t('career.val_title') }}</h2>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5">
          <div v-for="(v, i) in values" :key="i"
            class="relative rounded-2xl p-7 border border-white/5 hover:border-gold/20 transition-all duration-300 group overflow-hidden"
            style="background:#070B16">
            <div class="absolute top-0 left-0 right-0 h-0.5"
              style="background:linear-gradient(90deg,#C9A96E,rgba(201,169,110,0.1),transparent)"></div>
            <div class="absolute -top-1 -right-1 select-none pointer-events-none font-black leading-none text-gold/[0.04]"
              style="font-size:80px">{{ i + 1 }}</div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-5 relative z-10"
              style="background:rgba(201,169,110,0.08)">
              <svg class="w-6 h-6 text-gold/80" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" :d="v.icon" /></svg>
            </div>
            <h4 class="text-white font-bold text-base mb-3 relative z-10">{{ v.title }}</h4>
            <p class="text-white/45 text-sm leading-relaxed relative z-10">{{ v.text }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ APPLICATION FORM ═══ -->
    <section id="apply" class="py-28" style="background:#070B16">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-16 items-start">

          <!-- Left: info -->
          <div>
            <div class="inline-flex items-center gap-3 mb-6">
              <div class="h-px w-8 bg-gold"></div>
              <span class="text-gold text-[9px] font-bold uppercase tracking-[0.45em]">{{ t('career.form_badge') }}</span>
            </div>
            <h2 class="font-bold text-white mb-5" style="font-size:clamp(22px,2.8vw,40px)">
              {{ t('career.form_title') }}
            </h2>
            <p class="text-white/45 text-base leading-relaxed mb-10 max-w-sm">
              {{ t('career.form_sub') }}
            </p>

            <!-- Contact details -->
            <div class="space-y-5">
              <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:rgba(201,169,110,0.08); border:1px solid rgba(201,169,110,0.15)">
                  <Icon name="mail" class="w-5 h-5 text-gold" :stroke-width="1.5" />
                </div>
                <div>
                  <div class="text-white/25 text-[10px] uppercase tracking-wider mb-0.5">HR Email</div>
                  <a href="mailto:hr@qudrat.tj" class="text-white text-sm hover:text-gold transition-colors">hr@qudrat.tj</a>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:rgba(201,169,110,0.08); border:1px solid rgba(201,169,110,0.15)">
                  <Icon name="phone" class="w-5 h-5 text-gold" :stroke-width="1.5" />
                </div>
                <div>
                  <div class="text-white/25 text-[10px] uppercase tracking-wider mb-0.5">HR телефон</div>
                  <a href="tel:+992902000255" class="text-white text-sm hover:text-gold transition-colors">+992 902 000 255</a>
                </div>
              </div>
            </div>
          </div>

          <!-- Right: form -->
          <div class="rounded-2xl p-8 border border-white/6" style="background:#0C1220">
            <form @submit.prevent="submitApply" class="space-y-4">
              <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                  <label for="career-name" class="c-label">{{ t('career.form_name') }}</label>
                  <input id="career-name" v-model="form.name" type="text" required autocomplete="name" class="c-input" :placeholder="t('career.form_ph_name')"/>
                </div>
                <div>
                  <label for="career-phone" class="c-label">{{ t('career.form_phone') }}</label>
                  <input id="career-phone" v-model="form.phone" type="tel" required autocomplete="tel" class="c-input" :placeholder="t('career.form_ph_phone')"/>
                </div>
                <div>
                  <label for="career-email" class="c-label">{{ t('career.form_email') }}</label>
                  <input id="career-email" v-model="form.email" type="email" autocomplete="email" class="c-input" placeholder="email@example.com"/>
                </div>
                <div class="col-span-2">
                  <label for="career-position" class="c-label">{{ t('career.form_position') }}</label>
                  <input id="career-position" v-model="form.position" type="text" class="c-input" :placeholder="t('career.form_ph_pos')"/>
                </div>
                <div class="col-span-2">
                  <label for="career-message" class="c-label">{{ t('career.form_message') }}</label>
                  <textarea id="career-message" v-model="form.message" rows="4" class="c-input" :placeholder="t('career.form_ph_msg')"></textarea>
                </div>
              </div>

              <button type="submit" :disabled="sent"
                class="w-full py-4 rounded-xl font-bold uppercase tracking-widest text-[12px] transition-all duration-300 hover:opacity-90 disabled:opacity-60"
                style="background:#C9A96E; color:#070B16">
                {{ sent ? t('career.form_sent') : t('career.form_send') }}
              </button>
              <p class="text-white/20 text-xs text-center">{{ t('career.form_note') }}</p>
            </form>
          </div>
        </div>
      </div>
    </section>

  </MainLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'
import Icon from '@/Components/Icon.vue';
import { useTrans } from '@/composables/useTrans'
import { useAsset } from '@/composables/useAsset'

const { t } = useTrans()
const { asset } = useAsset()

const activeFilter = ref('all')
const sent = ref(false)

const form = ref({ name: '', phone: '', email: '', position: '', message: '' })
const inertiaForm = useForm({ name: '', phone: '', email: '', position: '', message: '' })

function openApply(title) {
  form.value.position = title
  document.getElementById('apply')?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

function submitApply() {
  Object.assign(inertiaForm, form.value)
  inertiaForm.post(route('career.apply'), {
    preserveScroll: true,
    onSuccess: () => {
      sent.value = true
      form.value = { name: '', phone: '', email: '', position: '', message: '' }
      setTimeout(() => { sent.value = false }, 5000)
    },
  })
}

const stats = computed(() => [
  { value: t('career.stat1_v'), label: t('career.stat1_l') },
  { value: t('career.stat2_v'), label: t('career.stat2_l') },
  { value: t('career.stat3_v'), label: t('career.stat3_l') },
  { value: t('career.stat4_v'), label: t('career.stat4_l') },
])

const filters = computed(() => [
  { key: 'all',     label: t('career.filter_all') },
  { key: 'build',   label: t('career.filter_build') },
  { key: 'mining',  label: t('career.filter_mining') },
  { key: 'it',      label: t('career.filter_it') },
  { key: 'mgmt',    label: t('career.filter_mgmt') },
  { key: 'finance', label: t('career.filter_finance') },
])

const vacancies = computed(() => [
  {
    id: 2, key: 'build', color: '#7EAAEF',
    dept: t('career.filter_build'),
    title: 'Прораб / Мастер участка',
    location: t('career.dushanbe'),
    type: t('career.full_time'),
    salary: '$800–1 400',
    desc: 'Организация и контроль строительных работ на объекте. Ежедневная отчётность, приёмка материалов, руководство бригадой.',
    reqs: ['Строительное образование', 'Опыт от 3 лет на аналогичной позиции', 'Знание нормативной базы РТ'],
  },
  {
    id: 3, key: 'build', color: '#7EAAEF',
    dept: t('career.filter_build'),
    title: 'Архитектор / Дизайнер интерьеров',
    location: t('career.dushanbe'),
    type: t('career.full_time'),
    salary: '$1 000–2 000',
    desc: 'Разработка архитектурных концепций и дизайн-проектов для премиальных жилых объектов. Работа с международными стандартами и материалами.',
    reqs: ['Портфолио премиальных проектов', 'Знание ArchiCAD, SketchUp, 3ds Max', 'Опыт от 3 лет'],
  },
  {
    id: 4, key: 'mining', color: '#E8A95E',
    dept: t('career.filter_mining'),
    title: 'Горный инженер',
    location: t('career.northern_tj'),
    type: t('career.full_time'),
    salary: '$1 200–2 000',
    desc: 'Техническое руководство добычей угля на месторождении Гузн. Планирование горных работ, контроль безопасности, взаимодействие с международными специалистами.',
    reqs: ['Горное образование', 'Опыт открытой добычи от 3 лет', 'Готовность к работе в полевых условиях'],
  },
  {
    id: 5, key: 'mining', color: '#E8A95E',
    dept: t('career.filter_mining'),
    title: 'Механик горной техники',
    location: t('career.northern_tj'),
    type: t('career.full_time'),
    salary: '$700–1 200',
    desc: 'Техническое обслуживание и ремонт горного оборудования. Диагностика неисправностей, ведение технической документации.',
    reqs: ['Техническое образование', 'Опыт работы с горной техникой', 'Знание гидравлических систем'],
  },
  {
    id: 6, key: 'it', color: '#6EAF8E',
    dept: t('career.filter_it'),
    title: 'Full-Stack разработчик',
    location: t('career.dushanbe'),
    type: t('career.full_time'),
    salary: '$1 500–3 000',
    desc: 'Разработка и поддержка внутренних систем (CRM, ERP, клиентский портал). Стек: Laravel, Vue.js, PostgreSQL. Работа в небольшой команде с высокой автономией.',
    reqs: ['Laravel + Vue.js от 2 лет', 'Опыт с Inertia.js / SPA', 'Понимание безопасности и оптимизации'],
  },
  {
    id: 7, key: 'mgmt', color: '#C9A96E',
    dept: t('career.filter_mgmt'),
    title: 'Менеджер по продажам недвижимости',
    location: t('career.dushanbe'),
    type: t('career.full_time'),
    salary: '$600–1 800',
    desc: 'Работа с клиентами на всех этапах сделки — от первого звонка до подписания договора. Консультации по квартирам, инвестиционным программам и условиям рассрочки.',
    reqs: ['Опыт в продажах от 2 лет', 'Грамотная речь на русском и таджикском', 'Ориентация на результат'],
  },
  {
    id: 8, key: 'finance', color: '#A96EC9',
    dept: t('career.filter_finance'),
    title: 'Финансовый аналитик',
    location: t('career.dushanbe'),
    type: t('career.full_time'),
    salary: '$1 000–1 800',
    desc: 'Финансовое моделирование строительных проектов, анализ инвестиционной привлекательности, подготовка отчётности для менеджмента и партнёров.',
    reqs: ['Высшее финансовое образование', 'Excel / Google Sheets на уровне эксперта', 'Опыт работы с девелоперскими проектами'],
  },
  {
    id: 9, key: 'mgmt', color: '#C9A96E',
    dept: t('career.filter_mgmt'),
    title: 'HR-менеджер',
    location: t('career.dushanbe'),
    type: t('career.full_time'),
    salary: '$600–1 000',
    desc: 'Подбор персонала, адаптация новых сотрудников, ведение кадровой документации. Работа с иностранными специалистами из КНР, Польши и других стран.',
    reqs: ['Опыт в HR от 2 лет', 'Знание Трудового кодекса РТ', 'Английский на уровне Intermediate+'],
  },
])

const filteredVacancies = computed(() =>
  activeFilter.value === 'all'
    ? vacancies.value
    : vacancies.value.filter(v => v.key === activeFilter.value)
)

const values = computed(() => [
  {
    icon: 'M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12z',
    title: t('career.val1_title'), text: t('career.val1_text'),
  },
  {
    icon: 'M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941',
    title: t('career.val2_title'), text: t('career.val2_text'),
  },
  {
    icon: 'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21',
    title: t('career.val3_title'), text: t('career.val3_text'),
  },
  {
    icon: 'M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z',
    title: t('career.val4_title'), text: t('career.val4_text'),
  },
])
</script>

<style scoped>
.c-label {
  display: block;
  font-size: 0.68rem;
  font-weight: 600;
  color: rgba(255,255,255,0.35);
  text-transform: uppercase;
  letter-spacing: 0.08em;
  margin-bottom: 0.4rem;
}
.c-input {
  display: block;
  width: 100%;
  padding: 0.7rem 0.9rem;
  border-radius: 0.75rem;
  font-size: 0.875rem;
  color: #fff;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.07);
  outline: none;
  transition: border-color 0.2s;
  resize: none;
}
.c-input:focus { border-color: rgba(201,169,110,0.45); }
.c-input::placeholder { color: rgba(255,255,255,0.18); }
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
