<template>
  <MainLayout>
    <SeoHead
      :title="t('about.meta_title')"
      :ogTitle="t('about.meta_title')"
      :description="t('about.meta_desc')"
      image="/images/og-about.jpg"
      path="/about"
    />

    <!-- ═══ 1. HERO ═══ -->
    <section class="about-hero relative overflow-hidden flex items-center">

      <!-- Background photo — prominent on right -->
      <div class="absolute inset-0">
        <img :src="asset('/images/hero-17.webp')" alt="" class="w-full h-full object-cover"
          style="opacity:0.62; object-position:65% center"/>
        <!-- Strong dark gradient on left, fades to photo on right -->
        <div class="absolute inset-0"
          style="background:linear-gradient(to right,rgba(7,11,22,1) 0%,rgba(7,11,22,0.96) 28%,rgba(7,11,22,0.72) 55%,rgba(7,11,22,0.18) 100%)"></div>
        <div class="absolute inset-0"
          style="background:linear-gradient(to top,rgba(7,11,22,0.75) 0%,transparent 32%)"></div>
      </div>

      <!-- Content — left-aligned block -->
      <div class="relative z-10 max-w-[1400px] mx-auto px-6 lg:px-12 w-full">
        <div class="max-w-[680px]">

          <!-- Badge -->
          <div class="flex items-center gap-3 mb-8">
            <div class="h-px w-10 bg-gold/60"></div>
            <span class="text-gold/70 text-[9px] font-bold uppercase tracking-[0.55em]">
              {{ t('about.hero_badge') }}
            </span>
          </div>

          <!-- H1 -->
          <h1 class="font-bold text-white leading-[0.9] mb-8"
            style="font-family:'Cormorant Garamond',Georgia,serif; font-size:clamp(40px,5.8vw,88px); letter-spacing:0.02em">
            {{ t('about.hero_pre') }}
          </h1>

          <!-- Subtitle -->
          <p class="text-white/55 text-base lg:text-lg leading-relaxed max-w-lg mb-10">
            {{ t('about.hero_subtitle') }}
          </p>

          <!-- CTA buttons -->
          <div class="flex flex-wrap gap-4 mb-16">
            <Link :href="route('objects')"
              class="inline-flex items-center gap-3 bg-gold hover:bg-[#D6BA7A] text-dark font-bold px-8 py-4 rounded-xl transition-all duration-300 uppercase tracking-widest text-[11px] shadow-[0_8px_40px_rgba(201,169,110,0.3)] hover:scale-105">
              {{ t('about.cta_projects') }}
              <Icon name="arrow-right" class="w-4 h-4" :stroke-width="2.5" />
            </Link>
            <button
              class="inline-flex items-center gap-3 border border-white/20 hover:border-gold/50 text-white/70 hover:text-white font-semibold px-8 py-4 rounded-xl transition-all duration-300 uppercase tracking-widest text-[11px]">
              <svg class="w-4 h-4 text-gold" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z"/>
              </svg>
              {{ t('about.cta_video') }}
            </button>
          </div>

          <!-- Stats with icons -->
          <div class="flex flex-wrap gap-8 lg:gap-12">
            <div v-for="(stat, i) in heroStats" :key="stat.label"
              class="flex items-start gap-4 group">
              <!-- Vertical divider (except first) -->
              <div v-if="i > 0" class="w-px h-12 bg-white/10 self-center hidden sm:block -ml-4 lg:-ml-6"></div>
              <!-- Icon + text -->
              <div>
                <svg class="w-5 h-5 text-gold/75 mb-2" fill="none" stroke="currentColor"
                  stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" :d="stat.iconPath"/>
                </svg>
                <div class="text-white font-black text-2xl leading-none mb-1">{{ stat.value }}</div>
                <div class="text-white/40 text-[9px] font-semibold uppercase tracking-[0.15em] leading-snug max-w-[95px]">
                  {{ stat.label }}
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Bottom fade -->
      <div class="absolute bottom-0 inset-x-0 h-20 pointer-events-none"
        style="background:linear-gradient(to top,#070B16,transparent)"></div>
    </section>

    <!-- ═══ 2. ИСТОРИЯ ═══ -->
    <section class="py-28" style="background:#070B16">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-16 lg:gap-24 items-center">

          <!-- Left: story text -->
          <div>
            <div class="flex items-center gap-3 mb-6">
              <div class="w-8 h-px bg-gold"></div>
              <span class="text-gold text-[10px] font-semibold uppercase tracking-[0.4em]">{{ t('about.story_badge') }}</span>
            </div>
            <h2 class="font-bold text-white leading-tight mb-8"
              style="font-size:clamp(32px,4vw,52px)">
              {{ t('about.story_title') }}
            </h2>
            <div class="space-y-5">
              <p class="text-white/45 text-base leading-relaxed">{{ t('about.story_text1') }}</p>
              <p class="text-white/55 text-sm leading-relaxed">{{ t('about.story_text2') }}</p>
              <p class="text-white/55 text-sm leading-relaxed">{{ t('about.story_text3') }}</p>
            </div>
          </div>

          <!-- Right: photo collage + badge -->
          <div class="relative">
            <div class="grid grid-cols-2 gap-3">
              <img :src="asset('/images/hero-17.webp')" class="rounded-2xl h-52 w-full object-cover"
                alt="QUDRAT Mining" loading="lazy">
              <img :src="asset('/images/hero-13.jpg')" class="rounded-2xl h-52 w-full object-cover mt-10"
                alt="QUDRAT Construction" loading="lazy">
              <img :src="asset('/images/hero-4.jpg')" class="rounded-2xl h-52 w-full object-cover -mt-4"
                alt="QUDRAT CITY" loading="lazy">
              <img :src="asset('/images/hero-18.webp')" class="rounded-2xl h-52 w-full object-cover mt-6"
                alt="QUDRAT Architecture" loading="lazy">
            </div>
            <!-- Floating badge -->
            <div class="absolute -bottom-4 -left-4 lg:-left-8 rounded-2xl border border-gold/25 p-6 shadow-2xl"
              style="background:#0F1828">
              <div class="text-gold font-black text-4xl leading-none mb-1">{{ t('about.years_badge') }}</div>
              <div class="text-white text-sm font-semibold">{{ t('about.years_exp') }}</div>
              <div class="text-white/35 text-[10px] mt-0.5 uppercase tracking-wider">QUDRAT · 2010</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ 3. ЦЕННОСТИ ═══ -->
    <section class="py-28 border-y border-white/5" style="background:#0C1220">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-end justify-between mb-16">
          <div>
            <div class="flex items-center gap-3 mb-5">
              <div class="w-8 h-px bg-gold"></div>
              <span class="text-gold text-[10px] font-semibold uppercase tracking-[0.4em]">{{ t('about.values_badge') }}</span>
            </div>
            <h2 class="font-bold text-white leading-tight"
              style="font-size:clamp(32px,4vw,52px)">
              {{ t('about.values_title') }}
            </h2>
          </div>
          <p class="mt-4 lg:mt-0 text-white/35 text-sm max-w-xs leading-relaxed">
            {{ t('about.values_sub') }}
          </p>
        </div>

        <!-- Value cards -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5">
          <div v-for="(val, i) in values" :key="i" class="about-card group">
            <div class="about-card-top" :style="`background:${val.color}`"></div>
            <div class="p-8 flex flex-col h-full">
              <!-- Icon -->
              <div class="about-icon mb-6" :style="`border-color:${val.color}40; background:${val.color}12`">
                <svg class="w-5 h-5" :style="`color:${val.color}`" fill="none" stroke="currentColor"
                  stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" :d="val.iconPath"/>
                </svg>
              </div>
              <!-- Number + title -->
              <div class="flex items-start justify-between mb-4">
                <h3 class="text-white font-bold text-base leading-tight group-hover:text-gold transition-colors duration-300">
                  {{ val.title }}
                </h3>
                <span class="text-white/6 font-black text-4xl leading-none flex-shrink-0 ml-3">0{{ i + 1 }}</span>
              </div>
              <p class="text-white/55 text-sm leading-relaxed flex-1">{{ val.text }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ 4. ХРОНОЛОГИЯ ═══ -->
    <section class="py-28" style="background:#070B16">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <!-- Header -->
        <div class="max-w-2xl mb-20">
          <div class="flex items-center gap-3 mb-5">
            <div class="w-8 h-px bg-gold"></div>
            <span class="text-gold text-[10px] font-semibold uppercase tracking-[0.4em]">{{ t('about.timeline_badge') }}</span>
          </div>
          <h2 class="font-bold text-white leading-tight"
            style="font-size:clamp(30px,4vw,48px)">
            {{ t('about.timeline_title') }}
          </h2>
        </div>

        <!-- Timeline as process-style grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5">
          <div v-for="(item, i) in timeline" :key="i" class="tl-card group">
            <!-- Year badge -->
            <div class="flex items-center gap-4 mb-5">
              <div class="tl-year">{{ item.year }}</div>
              <div v-if="i < timeline.length - 1" class="flex-1 h-px hidden xl:block"
                style="background:linear-gradient(to right,rgba(201,169,110,0.2),transparent)"></div>
            </div>
            <h4 class="text-white font-bold text-sm mb-3 group-hover:text-gold transition-colors duration-300">
              {{ item.title }}
            </h4>
            <p class="text-white/35 text-xs leading-relaxed">{{ item.text }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ 5. НАГРАДЫ ═══ -->
    <section class="py-28 border-y border-white/5" style="background:#0C1220">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-end justify-between mb-16">
          <div>
            <div class="flex items-center gap-3 mb-5">
              <div class="w-8 h-px bg-gold"></div>
              <span class="text-gold text-[10px] font-semibold uppercase tracking-[0.4em]">{{ t('about.awards_badge') }}</span>
            </div>
            <h2 class="font-bold text-white leading-tight"
              style="font-size:clamp(32px,4vw,52px)">
              {{ t('about.awards_title') }}
            </h2>
          </div>
          <p class="mt-4 lg:mt-0 text-white/35 text-sm max-w-xs leading-relaxed">
            {{ t('about.awards_sub') }}
          </p>
        </div>

        <!-- Awards grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
          <div v-for="(aw, i) in awards" :key="i"
            class="award-card group relative overflow-hidden rounded-2xl border border-white/6 transition-all duration-300"
            style="background:rgba(255,255,255,0.02)">

            <!-- Top accent line -->
            <div class="absolute top-0 inset-x-0 h-px"
              style="background:linear-gradient(90deg,transparent,rgba(201,169,110,0.5),transparent)"></div>

            <div class="p-7 flex gap-5 items-start">
              <!-- Trophy icon -->
              <div class="w-12 h-12 rounded-xl flex-shrink-0 flex items-center justify-center border border-gold/25"
                style="background:rgba(201,169,110,0.08)">
                <svg class="w-6 h-6 text-gold" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0"/>
                </svg>
              </div>

              <!-- Content -->
              <div class="flex-1 min-w-0">
                <!-- Year badge -->
                <span class="inline-block text-gold text-[9px] font-bold tracking-[0.2em] uppercase mb-2 px-2 py-0.5 rounded"
                  style="background:rgba(201,169,110,0.1); border:1px solid rgba(201,169,110,0.2)">
                  {{ aw.year }}
                </span>
                <h4 class="text-white font-bold text-sm leading-snug mb-1.5 group-hover:text-gold transition-colors duration-300">
                  {{ aw.title }}
                </h4>
                <p class="text-white/35 text-[11px] leading-relaxed">{{ aw.org }}</p>
              </div>
            </div>

            <!-- Hover glow -->
            <div class="absolute inset-0 pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-500"
              style="background:radial-gradient(ellipse at 20% 50%,rgba(201,169,110,0.05) 0%,transparent 70%)"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ 6. КОМАНДА ═══ -->
    <section class="py-28 border-y border-white/5" style="background:#0C1220">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-16 items-center">

          <!-- Left: text -->
          <div>
            <div class="flex items-center gap-3 mb-6">
              <div class="w-8 h-px bg-gold"></div>
              <span class="text-gold text-[10px] font-semibold uppercase tracking-[0.4em]">{{ t('about.team_badge') }}</span>
            </div>
            <h2 class="font-bold text-white leading-tight mb-6"
              style="font-size:clamp(30px,4vw,50px)">
              {{ t('about.team_title') }}
            </h2>
            <p class="text-white/55 text-base leading-relaxed mb-10 max-w-md">
              {{ t('about.team_text') }}
            </p>
            <Link :href="route('contacts')"
              class="inline-flex items-center gap-3 bg-gold hover:bg-[#D6BA7A] text-dark font-bold px-9 py-4 rounded-xl transition-all duration-300 uppercase tracking-widest text-[11px] shadow-[0_8px_40px_rgba(201,169,110,0.25)] hover:scale-105">
              {{ t('about.team_cta') }}
              <Icon name="arrow-right" class="w-4 h-4" :stroke-width="2" />
            </Link>
          </div>

          <!-- Right: 2×2 stat grid -->
          <div class="grid grid-cols-2 gap-4">
            <div v-for="stat in teamStats" :key="stat.label"
              class="rounded-2xl p-8 border border-white/5 hover:border-gold/20 transition-all duration-300 group"
              style="background:rgba(255,255,255,0.025)">
              <div class="text-gold font-black mb-2 leading-none group-hover:scale-105 transition-transform"
                style="font-size:clamp(28px,3vw,42px)">
                {{ stat.value }}
              </div>
              <div class="text-white/55 text-[11px] uppercase tracking-wider leading-snug">{{ stat.label }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ 7. CTA ═══ -->
    <section class="py-28" style="background:#070B16">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="relative rounded-3xl overflow-hidden p-12 lg:p-20 border border-gold/10"
          style="background:linear-gradient(140deg,#0F1E35 0%,#080D1A 60%,#0A1525 100%)">

          <!-- Glow + top line -->
          <div class="absolute inset-0 pointer-events-none"
            style="background:radial-gradient(ellipse 60% 80% at 80% 50%,rgba(201,169,110,0.07) 0%,transparent 65%)"></div>
          <div class="absolute top-0 inset-x-0 h-px"
            style="background:linear-gradient(90deg,transparent,rgba(201,169,110,0.4),transparent)"></div>

          <div class="relative text-center max-w-2xl mx-auto">
            <div class="flex items-center justify-center gap-3 mb-7">
              <div class="w-8 h-px bg-gold"></div>
              <span class="text-gold text-[10px] font-semibold uppercase tracking-[0.4em]">{{ t('about.cta_badge') }}</span>
              <div class="w-8 h-px bg-gold"></div>
            </div>
            <h2 class="font-bold text-white leading-tight mb-6"
              style="font-size:clamp(30px,4vw,52px)">
              {{ t('about.cta_title') }}<br>
              <span class="text-gold">{{ t('about.cta_title_hl') }}</span>
            </h2>
            <p class="text-white/45 text-base leading-relaxed mb-10 max-w-lg mx-auto">
              {{ t('about.cta_sub') }}
            </p>
            <div class="flex flex-wrap gap-4 justify-center">
              <Link :href="route('contacts')"
                class="inline-flex items-center gap-3 bg-gold hover:bg-[#D6BA7A] text-dark font-bold px-9 py-4 rounded-xl transition-all duration-300 uppercase tracking-widest text-[11px] shadow-[0_8px_40px_rgba(201,169,110,0.25)] hover:scale-105">
                {{ t('about.cta_btn1') }}
                <Icon name="arrow-right" class="w-4 h-4" :stroke-width="2" />
              </Link>
              <Link :href="route('objects')"
                class="inline-flex items-center gap-3 border border-white/15 hover:border-gold/40 text-white/60 hover:text-white font-semibold px-8 py-4 rounded-xl transition-all duration-300 text-[11px] uppercase tracking-widest">
                {{ t('about.cta_btn2') }}
              </Link>
            </div>
          </div>
        </div>
      </div>
    </section>

  </MainLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'
import Icon from '@/Components/Icon.vue'
import { useAsset } from '@/composables/useAsset'
import { useTrans } from '@/composables/useTrans'

const { asset } = useAsset()
const { t } = useTrans()

/* ── Hero stats ── */
const heroStats = computed(() => [
  {
    value: t('about.ts1_v'), label: t('about.ts1_l'),
    iconPath: 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z',
  },
  {
    value: t('about.ts2_v'), label: t('about.ts2_l'),
    iconPath: 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z',
  },
  {
    value: t('about.ts3_v'), label: t('about.ts3_l'),
    iconPath: 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z',
  },
  {
    value: t('about.ts4_v'), label: t('about.ts4_l'),
    iconPath: 'M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z',
  },
])

/* ── Values ── */
const values = computed(() => [
  {
    title: t('about.val1_title'),
    text: t('about.val1_text'),
    color: '#C9A96E',
    iconPath: 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z',
  },
  {
    title: t('about.val2_title'),
    text: t('about.val2_text'),
    color: '#7EAAEF',
    iconPath: 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z',
  },
  {
    title: t('about.val3_title'),
    text: t('about.val3_text'),
    color: '#6EAF8E',
    iconPath: 'M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z',
  },
  {
    title: t('about.val4_title'),
    text: t('about.val4_text'),
    color: '#A87EEF',
    iconPath: 'M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
  },
])

/* ── Timeline ── */
const timeline = computed(() => [
  { year: t('about.tl1_year'), title: t('about.tl1_title'), text: t('about.tl1_text') },
  { year: t('about.tl2_year'), title: t('about.tl2_title'), text: t('about.tl2_text') },
  { year: t('about.tl3_year'), title: t('about.tl3_title'), text: t('about.tl3_text') },
  { year: t('about.tl4_year'), title: t('about.tl4_title'), text: t('about.tl4_text') },
  { year: t('about.tl5_year'), title: t('about.tl5_title'), text: t('about.tl5_text') },
])

/* ── Awards ── */
const awards = computed(() => [
  { year: t('about.aw1_year'), title: t('about.aw1_title'), org: t('about.aw1_org') },
  { year: t('about.aw2_year'), title: t('about.aw2_title'), org: t('about.aw2_org') },
  { year: t('about.aw3_year'), title: t('about.aw3_title'), org: t('about.aw3_org') },
  { year: t('about.aw4_year'), title: t('about.aw4_title'), org: t('about.aw4_org') },
  { year: t('about.aw5_year'), title: t('about.aw5_title'), org: t('about.aw5_org') },
  { year: t('about.aw6_year'), title: t('about.aw6_title'), org: t('about.aw6_org') },
])

/* ── Team stats ── */
const teamStats = computed(() => [
  { value: t('about.ts1_v'), label: t('about.ts1_l') },
  { value: t('about.ts2_v'), label: t('about.ts2_l') },
  { value: t('about.ts3_v'), label: t('about.ts3_l') },
  { value: t('about.ts4_v'), label: t('about.ts4_l') },
])
</script>

<style scoped>
/* ── Hero ── */
.about-hero {
  height: 100vh;
  min-height: 640px;
  background: #070B16;
  padding-top: 90px;
}
.about-grid {
  background-image:
    linear-gradient(rgba(201,169,110,0.035) 1px, transparent 1px),
    linear-gradient(90deg, rgba(201,169,110,0.035) 1px, transparent 1px);
  background-size: 60px 60px;
}

/* ── Value cards (same pattern as svc-card) ── */
.about-card {
  display: flex;
  flex-direction: column;
  border-radius: 1.25rem;
  border: 1px solid rgba(255,255,255,0.06);
  background: rgba(255,255,255,0.02);
  overflow: hidden;
  transition: border-color 0.35s, transform 0.35s;
}
.about-card:hover {
  border-color: rgba(201,169,110,0.2);
  transform: translateY(-4px);
}
.about-card-top {
  height: 3px;
  width: 100%;
  opacity: 0.7;
  transition: opacity 0.3s;
}
.about-card:hover .about-card-top { opacity: 1; }
.about-icon {
  width: 2.75rem;
  height: 2.75rem;
  border-radius: 0.75rem;
  border: 1px solid;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* ── Award cards ── */
.award-card:hover {
  border-color: rgba(201,169,110,0.22);
  transform: translateY(-3px);
}

/* ── Timeline cards (same pattern as process-card) ── */
.tl-card {
  padding: 1.75rem;
  border-radius: 1rem;
  border: 1px solid rgba(255,255,255,0.06);
  background: rgba(255,255,255,0.02);
  transition: border-color 0.3s, background 0.3s;
}
.tl-card:hover {
  border-color: rgba(201,169,110,0.2);
  background: rgba(201,169,110,0.025);
}
.tl-year {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.4rem 0.85rem;
  border-radius: 0.625rem;
  border: 1px solid rgba(201,169,110,0.3);
  background: rgba(201,169,110,0.08);
  font-weight: 700;
  font-size: 0.75rem;
  color: #C9A96E;
  flex-shrink: 0;
  letter-spacing: 0.05em;
  white-space: nowrap;
}
</style>
