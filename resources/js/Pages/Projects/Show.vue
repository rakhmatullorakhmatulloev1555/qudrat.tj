<template>
  <MainLayout>
    <SeoHead
      :title="p.seo.metaTitle || p.name"
      :ogTitle="p.seo.ogTitle || `${p.name} — QUDRAT`"
      :description="p.seo.metaDescription || p.tag"
      :image="p.seo.ogImage || p.hero"
      :path="`/projects/${p.slug}`"
      :canonical="p.seo.canonical || ''"
      :schema="p.seo.schema"
    />

    <!-- ═══ PREVIEW BANNER (черновик) ═══ -->
    <div v-if="isPreview" class="fixed top-0 inset-x-0 z-[100] text-center py-2 text-[11px] font-bold uppercase tracking-widest"
      style="background:#b45309;color:#fff">
      Режим предпросмотра — проект не опубликован
    </div>

    <!-- ═══════════════════════════════════════════
         HERO
    ═══════════════════════════════════════════ -->
    <section class="relative overflow-hidden" style="background:#070B16">
      <!-- Background image -->
      <div class="absolute inset-0">
        <img v-if="p.hero" :src="asset(p.hero)" :alt="p.name" class="w-full h-full object-cover" style="opacity:0.5"/>
        <div class="absolute inset-0" style="background:linear-gradient(to top,#070B16 4%,rgba(7,11,22,0.55) 45%,rgba(7,11,22,0.75) 100%)"></div>
        <div class="absolute inset-0" style="background:linear-gradient(to right,rgba(7,11,22,0.85) 0%,transparent 60%)"></div>
      </div>

      <div class="relative max-w-[1400px] mx-auto px-6 lg:px-12 pt-36 pb-16 flex flex-col justify-end" style="min-height:76vh">

        <!-- Back link -->
        <Link :href="route('objects') + '#featured'"
          class="inline-flex items-center gap-2 text-white/40 hover:text-gold text-[11px] font-bold uppercase tracking-widest mb-10 transition-colors self-start">
          <Icon name="arrow-long-right" class="w-4 h-4 rotate-180" :stroke-width="1.8" />
          {{ t('project.back') }}
        </Link>

        <!-- Badges -->
        <div class="flex items-center gap-3 mb-5">
          <span v-if="p.type" class="text-[9px] font-black uppercase tracking-wider px-2.5 py-1 rounded-md"
            :style="`background:${rgba(p.accent, 0.15)};border:1px solid ${rgba(p.accent, 0.3)};color:${p.accent}`">
            {{ p.type }}
          </span>
          <span class="text-gold/70 text-[9px] font-bold uppercase tracking-[0.4em]">{{ t('project.hero_badge') }}</span>
        </div>

        <!-- Title -->
        <h1 class="font-black text-white leading-none mb-4"
          style="font-size:clamp(34px,5vw,72px);letter-spacing:-0.02em">
          {{ p.name }}
        </h1>

        <!-- Location -->
        <div v-if="p.loc" class="flex items-center gap-2 text-white/45 text-sm mb-5">
          <Icon name="map-pin" class="w-4 h-4 flex-shrink-0 text-gold/70" :stroke-width="2" />
          {{ p.loc }}
        </div>

        <!-- Tagline -->
        <p class="text-white/60 text-base lg:text-lg leading-relaxed max-w-xl mb-10 font-light">
          {{ p.tag }}
        </p>

        <!-- Hero stats -->
        <div class="flex flex-wrap gap-8 lg:gap-14">
          <div v-for="s in p.stats" :key="s.l">
            <div class="text-gold font-black leading-none" style="font-size:clamp(22px,2.5vw,34px)">{{ s.v }}</div>
            <div class="text-white/35 text-[10px] uppercase tracking-[0.25em] mt-2">{{ s.l }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ FACTS BAR ═══ -->
    <div class="border-y border-white/5" style="background:#0A0E1A">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="grid grid-cols-2 lg:grid-cols-4">
          <div v-for="(f, i) in facts" :key="f.l"
            class="py-6 px-2 lg:px-8 text-center lg:text-left"
            :class="i > 0 ? 'lg:border-l lg:border-white/5' : ''">
            <div class="text-white/30 text-[9px] font-bold uppercase tracking-[0.3em] mb-1.5">{{ f.l }}</div>
            <div class="text-white font-bold text-sm">{{ f.v }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════
         ABOUT
    ═══════════════════════════════════════════ -->
    <section class="py-24" style="background:#070B16">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-14 items-center">

          <div>
            <div class="inline-flex items-center gap-3 mb-5">
              <span class="block h-px w-8 bg-gold"></span>
              <span class="text-gold/70 text-[9px] font-bold uppercase tracking-[0.55em]">{{ t('project.about_badge') }}</span>
            </div>
            <h2 class="font-black text-white leading-tight mb-8"
              style="font-size:clamp(26px,3vw,44px);letter-spacing:-0.02em">
              {{ t('project.about_title') }}
            </h2>
            <p class="text-white/55 text-[15px] leading-relaxed mb-5">{{ p.about1 }}</p>
            <p class="text-white/55 text-[15px] leading-relaxed">{{ p.about2 }}</p>
          </div>

          <div v-if="aboutImage" class="relative rounded-2xl overflow-hidden border border-white/6" style="aspect-ratio:4/3">
            <img :src="asset(aboutImage)" :alt="p.name" loading="lazy"
              class="w-full h-full object-cover" style="opacity:0.85"/>
            <div class="absolute inset-0" style="background:linear-gradient(to top,rgba(7,11,22,0.55),transparent 50%)"></div>
            <div class="absolute bottom-5 left-6">
              <div class="text-[9px] font-bold uppercase tracking-widest mb-1" :style="`color:${p.accent}`">
                {{ p.type }}
              </div>
              <div class="text-white font-black text-lg">{{ p.name }}</div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════
         FEATURES
    ═══════════════════════════════════════════ -->
    <section v-if="p.features.length" class="py-24 border-t border-white/5" style="background:#0C1220">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="text-center mb-14">
          <div class="inline-flex items-center gap-3 mb-5">
            <div class="h-px w-10" style="background:linear-gradient(to right,transparent,rgba(201,169,110,0.5))"></div>
            <span class="text-gold/70 text-[9px] font-bold uppercase tracking-[0.55em]">{{ t('project.feat_badge') }}</span>
            <div class="h-px w-10" style="background:linear-gradient(to left,transparent,rgba(201,169,110,0.5))"></div>
          </div>
          <h2 class="font-black text-white leading-none"
            style="font-size:clamp(26px,3vw,46px);letter-spacing:-0.02em">
            {{ t('project.feat_title') }}
          </h2>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
          <div v-for="feat in p.features" :key="feat.title"
            class="rounded-2xl p-6 group transition-all duration-300 hover:-translate-y-1"
            style="background:#070B16;border:1px solid rgba(255,255,255,0.06)">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-5"
              :style="`background:${rgba(p.accent, 0.12)};border:1px solid ${rgba(p.accent, 0.25)}`">
              <svg class="w-5 h-5" fill="none" :stroke="p.accent" stroke-width="1.5" viewBox="0 0 24 24">
                <path v-for="(d, di) in (ICONS[feat.icon] || ICONS.building)" :key="di" stroke-linecap="round" stroke-linejoin="round" :d="d"/>
              </svg>
            </div>
            <div class="text-white font-bold text-sm mb-2 group-hover:text-gold transition-colors">{{ feat.title }}</div>
            <div class="text-white/40 text-xs leading-relaxed">{{ feat.desc }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════
         GALLERY
    ═══════════════════════════════════════════ -->
    <section v-if="p.gallery.length" class="py-24 border-t border-white/5" style="background:#070B16">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="flex items-center gap-3 mb-10">
          <span class="block h-px w-8 bg-gold"></span>
          <h2 class="text-[10px] font-bold uppercase tracking-[0.38em] text-gold">{{ t('project.gal_title') }}</h2>
        </div>
        <div class="grid sm:grid-cols-3 gap-5">
          <div v-for="(img, i) in p.gallery" :key="img"
            class="rounded-2xl overflow-hidden border border-white/6 group" style="aspect-ratio:4/5">
            <img :src="asset(img)" :alt="`${p.name} — ${i + 1}`" loading="lazy"
              class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" style="opacity:0.85"/>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════
         CTA
    ═══════════════════════════════════════════ -->
    <section class="py-20 border-t border-white/5" style="background:#0C1220">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="rounded-2xl p-10 lg:p-14 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-8 relative overflow-hidden"
          style="background:linear-gradient(135deg,rgba(201,169,110,0.08),rgba(201,169,110,0.02));border:1px solid rgba(201,169,110,0.15)">
          <div class="absolute top-0 right-0 w-64 h-64 opacity-5 pointer-events-none" style="background:radial-gradient(circle,#C9A96E,transparent)"></div>
          <div class="relative">
            <p class="text-white font-black text-2xl mb-2">{{ t('project.cta_title') }}</p>
            <p class="text-white/45 text-sm max-w-md">{{ t('project.cta_sub') }}</p>
          </div>
          <div class="relative flex flex-wrap gap-3">
            <Link :href="primaryCta.href"
              class="bg-gold hover:bg-gold-600 text-dark font-bold px-8 py-4 rounded-xl text-xs tracking-widest uppercase transition-all shadow-[0_4px_20px_rgba(201,169,110,0.25)]">
              {{ primaryCta.label }}
            </Link>
            <Link :href="secondaryCta.href"
              class="border border-white/15 hover:border-white/30 text-white/60 hover:text-white font-semibold px-7 py-4 rounded-xl text-xs tracking-widest uppercase transition-colors">
              {{ secondaryCta.label }}
            </Link>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════
         OTHER PROJECTS
    ═══════════════════════════════════════════ -->
    <section v-if="others.length" class="py-24 border-t border-white/5" style="background:#070B16">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="flex items-center gap-3 mb-10">
          <span class="block h-px w-8 bg-gold"></span>
          <h2 class="text-[10px] font-bold uppercase tracking-[0.38em] text-gold">{{ t('project.other_title') }}</h2>
        </div>
        <div class="grid sm:grid-cols-3 gap-5">
          <Link v-for="o in others" :key="o.slug" :href="route('projects.show', o.slug)"
            class="group rounded-2xl overflow-hidden border border-white/6 hover:border-gold/30 transition-all duration-500"
            style="background:#0C1220">
            <div class="h-40 relative overflow-hidden">
              <img v-if="o.img" :src="asset(o.img)" :alt="o.name" loading="lazy"
                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" style="opacity:0.7"/>
              <div class="absolute inset-0" style="background:linear-gradient(to top,rgba(7,11,22,0.9) 0%,rgba(7,11,22,0.15) 60%)"></div>
              <div class="absolute top-4 left-4">
                <span v-if="o.type" class="text-[9px] font-black uppercase tracking-wider px-2.5 py-1 rounded-md"
                  :style="`background:${rgba(o.accent, 0.12)};border:1px solid ${rgba(o.accent, 0.25)};color:${o.accent}`">{{ o.type }}</span>
              </div>
            </div>
            <div class="p-5">
              <div class="text-white font-black text-base tracking-tight group-hover:text-gold transition-colors mb-1">{{ o.name }}</div>
              <div class="text-white/40 text-xs leading-relaxed">{{ o.desc }}</div>
            </div>
          </Link>
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
import { SHOWCASE_ICONS as ICONS, hexToRgba as rgba } from '@/showcaseIcons'

const props = defineProps({
  project:   { type: Object, required: true }, // ShowcaseProject::toPage()
  others:    { type: Array, default: () => [] },
  isPreview: { type: Boolean, default: false },
})

const { asset } = useAsset()
const { t } = useTrans()

const p = computed(() => props.project)

// Изображение в блоке «О проекте» — первое из галереи, иначе hero
const aboutImage = computed(() => p.value.gallery[0] || p.value.hero)

// ── Facts bar ──
const facts = computed(() => [
  { l: t('project.fact_type'),   v: p.value.type },
  { l: t('project.fact_loc'),    v: p.value.loc },
  { l: t('project.fact_status'), v: p.value.statusLabel },
  { l: t('project.fact_year'),   v: p.value.stats[1]?.v ?? '—' },
].filter(f => f.v))

// ── CTA ──
const primaryCta = computed(() => {
  if (p.value.ctaType === 'apts')   return { href: route('objects') + '#catalog', label: t('project.cta_apts') }
  if (p.value.ctaType === 'mining') return { href: route('mining'), label: t('project.cta_mining') }
  return { href: route('contacts'), label: t('project.cta_contact') }
})
const secondaryCta = computed(() => p.value.ctaType === 'contact'
  ? { href: route('objects') + '#featured', label: t('project.back') }
  : { href: route('contacts'), label: t('project.cta_contact') })
</script>
