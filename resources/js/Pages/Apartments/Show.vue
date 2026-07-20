<template>
  <MainLayout>
    <SeoHead
      :title="metaTitle"
      :ogTitle="`${metaTitle} — QUDRAT`"
      :description="metaDescription"
      :image="apartment.plan_image || '/images/og-home.jpg'"
    />
    <!-- Schema.org (Apartment / Residence) -->
    <Head>
      <component :is="'script'" type="application/ld+json">{{ schemaJson }}</component>
    </Head>

    <div style="background:#070B16">
      <!-- ═══ BREADCRUMBS ═══ -->
      <nav class="max-w-[1400px] mx-auto px-6 lg:px-12 pt-32 pb-2" aria-label="Хлебные крошки">
        <ol class="flex flex-wrap items-center gap-2 text-[11px] tracking-wide text-white/35">
          <li><Link :href="route('home')" class="hover:text-gold transition-colors">{{ t('objects.d_bc_home') }}</Link></li>
          <li class="text-white/20">/</li>
          <li><Link :href="route('objects')" class="hover:text-gold transition-colors">{{ t('objects.d_bc_projects') }}</Link></li>
          <template v-if="project">
            <li class="text-white/20">/</li>
            <li><Link :href="route('objects')" class="hover:text-gold transition-colors">{{ project.name }}</Link></li>
          </template>
          <li class="text-white/20">/</li>
          <li class="text-gold">{{ t('objects.apt_abbr') }} {{ apartment.number }}</li>
        </ol>
      </nav>

      <!-- ═══ ВЕРХНИЙ БЛОК: галерея + сводка ═══ -->
      <section class="max-w-[1400px] mx-auto px-6 lg:px-12 pt-6 pb-16">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-start">

          <!-- ── Галерея / планировка ── -->
          <div>
            <div class="relative rounded-2xl overflow-hidden border border-white/6 group"
              style="background:#0C1220; aspect-ratio:4/3">
              <img v-if="images.length" :src="asset(images[activeIdx])" :alt="`${t('objects.apt_abbr')} ${apartment.number}`"
                loading="eager"
                class="w-full h-full object-contain cursor-zoom-in transition-transform duration-500"
                @click="openLightbox(activeIdx)"/>
              <!-- Заглушка, если изображений нет -->
              <div v-else class="w-full h-full flex flex-col items-center justify-center gap-4"
                style="background:linear-gradient(135deg,#0A0E1A,#0D1525)">
                <svg class="w-28 h-28 text-gray-700" fill="none" stroke="currentColor" stroke-width="0.7" viewBox="0 0 100 100">
                  <rect x="10" y="10" width="80" height="80" rx="2"/>
                  <rect x="10" y="10" width="50" height="40" rx="1"/><rect x="60" y="10" width="30" height="40" rx="1"/>
                  <rect x="10" y="50" width="35" height="40" rx="1"/><rect x="45" y="50" width="45" height="40" rx="1"/>
                </svg>
                <span class="text-gray-600 text-xs uppercase tracking-widest">{{ t('objects.plan_word') }}</span>
              </div>

              <!-- Статус -->
              <span :class="statusBadge(apartment.status)"
                class="absolute top-4 left-4 text-[10px] font-bold px-3 py-1 rounded-full tracking-wider uppercase backdrop-blur-sm">
                {{ statusLabel(apartment.status) }}
              </span>
              <!-- Кнопка «в полный размер» -->
              <button v-if="images.length" @click="openLightbox(activeIdx)"
                class="absolute bottom-4 right-4 flex items-center gap-2 px-3 py-2 rounded-lg text-[11px] font-semibold text-white/80 hover:text-white transition-all"
                style="background:rgba(7,11,22,0.75); backdrop-filter:blur(6px); border:1px solid rgba(255,255,255,0.12)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15M3.75 20.25h4.5m-4.5 0v-4.5m0 4.5L9 15"/>
                </svg>
                {{ t('objects.d_full_size') }}
              </button>
            </div>

            <!-- Миниатюры (если >1 изображения) -->
            <div v-if="images.length > 1" class="flex gap-3 mt-3 flex-wrap">
              <button v-for="(img, i) in images" :key="i" @click="activeIdx = i"
                class="w-20 h-16 rounded-lg overflow-hidden border transition-all"
                :style="i === activeIdx ? 'border-color:#C9A96E' : 'border-color:rgba(255,255,255,0.08)'">
                <img :src="asset(img)" :alt="`Фото ${i + 1}`" loading="lazy" class="w-full h-full object-cover"/>
              </button>
            </div>
          </div>

          <!-- ── Сводка + CTA (sticky на десктопе) ── -->
          <div class="lg:sticky lg:top-24">
            <div class="inline-flex items-center gap-2 mb-4">
              <span class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-black text-[#0A0800]" style="background:#C9A96E">
                {{ apartment.rooms }}
              </span>
              <span class="text-white/40 text-[11px] uppercase tracking-[0.3em]">
                {{ project ? project.name : t('objects.apt_abbr') }}
              </span>
            </div>

            <h1 class="text-white font-bold leading-tight mb-2"
              style="font-family:'Cormorant Garamond',Georgia,serif; font-size:clamp(30px,4vw,46px)">
              {{ roomLabel(apartment.rooms) }}, {{ apartment.area }} м²
            </h1>
            <p class="text-white/45 text-sm mb-7">
              {{ t('objects.apt_abbr') }} {{ apartment.number }} · {{ apartment.floor }} {{ t('objects.floor_word') }}
              <template v-if="apartment.finish"> · {{ finishLabel(apartment.finish) }}</template>
            </p>

            <!-- Цена -->
            <div class="rounded-2xl p-6 mb-6 border border-gold/15" style="background:linear-gradient(160deg,rgba(201,169,110,0.06),transparent)">
              <div class="flex items-end justify-between flex-wrap gap-2">
                <div>
                  <div class="text-white/40 text-[10px] uppercase tracking-widest mb-1">{{ t('objects.summary_price') }}</div>
                  <div class="text-gold font-black leading-none" style="font-size:clamp(28px,4vw,40px)">
                    ${{ fmtPrice(apartment.price) }}
                  </div>
                  <div class="text-white/35 text-xs mt-1">{{ apartment.currency }}</div>
                </div>
                <div v-if="ppm" class="text-right">
                  <div class="text-white/40 text-[10px] uppercase tracking-widest mb-1">{{ t('objects.d_per_m2') }}</div>
                  <div class="text-white font-bold text-lg">${{ fmtPrice(ppm) }}</div>
                </div>
              </div>
            </div>

            <!-- CTA -->
            <button v-if="apartment.status === 'available'" @click="reserveOpen = true"
              class="w-full flex items-center justify-center gap-2 bg-gold hover:bg-gold-600 text-dark font-bold py-4 rounded-xl uppercase tracking-widest text-sm transition-all mb-3">
              {{ t('objects.d_book_apt') }}
              <Icon name="arrow-right" class="w-4 h-4" :stroke-width="2" />
            </button>
            <div v-else class="w-full text-center py-4 rounded-xl border border-white/8 text-gray-500 font-bold uppercase tracking-widest text-sm mb-3"
              style="background:rgba(255,255,255,0.03)">
              {{ statusLabel(apartment.status) }}
            </div>

            <!-- 3D-просмотр -->
            <Link :href="`/objects/${project?.slug || 'obj'}/${apartment.id}/3d`"
              class="w-full flex items-center justify-center gap-2 py-3.5 rounded-xl font-bold text-sm uppercase tracking-widest transition-all mb-3 text-gold"
              style="border:1px solid rgba(201,169,110,0.35); background:linear-gradient(120deg,rgba(201,169,110,0.10),rgba(201,169,110,0.02))">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" width="18" height="18">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5 12 2.25 3 7.5m18 0-9 5.25m9-5.25v9L12 21.75m0-8.25L3 7.5m9 5.25v8.25M3 7.5v9L12 21.75"/>
              </svg>
              3D-просмотр квартиры
            </Link>

            <a :href="`tel:${contactPhone}`"
              class="w-full flex items-center justify-center gap-2 border border-white/12 text-white/70 hover:text-gold hover:border-gold/40 py-3.5 rounded-xl font-semibold text-sm transition-all">
              <Icon name="phone" class="w-4 h-4" :stroke-width="1.6" />
              {{ contactPhone }}
            </a>

            <!-- WhatsApp / Telegram -->
            <div class="grid grid-cols-2 gap-3 mt-3">
              <a :href="whatsappUrl" target="_blank" rel="noopener"
                class="flex items-center justify-center gap-2 py-3.5 rounded-xl font-bold text-sm transition-all text-[#25D366] hover:brightness-125"
                style="border:1px solid rgba(37,211,102,0.35); background:rgba(37,211,102,0.07)">
                <svg viewBox="0 0 24 24" fill="currentColor" width="17" height="17">
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.52.149-.174.198-.298.297-.497.1-.198.05-.371-.025-.52-.074-.149-.668-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                </svg>
                WhatsApp
              </a>
              <a :href="telegramUrl" target="_blank" rel="noopener"
                class="flex items-center justify-center gap-2 py-3.5 rounded-xl font-bold text-sm transition-all text-[#2AABEE] hover:brightness-125"
                style="border:1px solid rgba(42,171,238,0.35); background:rgba(42,171,238,0.07)">
                <svg viewBox="0 0 24 24" fill="currentColor" width="17" height="17">
                  <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.911.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                </svg>
                Telegram
              </a>
            </div>
          </div>
        </div>

        <!-- ═══ ХАРАКТЕРИСТИКИ (карточки) ═══ -->
        <div class="mt-16">
          <div class="flex items-center gap-3 mb-7">
            <span class="block h-px w-8 bg-gold"></span>
            <h2 class="text-[10px] font-bold uppercase tracking-[0.38em] text-gold">{{ t('objects.d_specs') }}</h2>
          </div>
          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div v-for="spec in specs" :key="spec.label" class="rounded-xl p-5 border border-white/6" style="background:#0C1220">
              <div class="text-2xl mb-2">{{ spec.icon }}</div>
              <div class="text-white/35 text-[10px] uppercase tracking-widest mb-1">{{ spec.label }}</div>
              <div class="text-white font-bold text-sm">{{ spec.value }}</div>
            </div>
          </div>
        </div>

        <!-- ═══ ОПИСАНИЕ (если есть заметки) ═══ -->
        <div v-if="apartment.notes" class="mt-14 max-w-[820px]">
          <div class="flex items-center gap-3 mb-5">
            <span class="block h-px w-8 bg-gold"></span>
            <h2 class="text-[10px] font-bold uppercase tracking-[0.38em] text-gold">{{ t('objects.d_description') }}</h2>
          </div>
          <p class="text-white/60 leading-relaxed whitespace-pre-line">{{ apartment.notes }}</p>
        </div>

        <!-- ═══ ПОХОЖИЕ КВАРТИРЫ ═══ -->
        <div v-if="similar.length" class="mt-20">
          <div class="flex items-center gap-3 mb-7">
            <span class="block h-px w-8 bg-gold"></span>
            <h2 class="text-[10px] font-bold uppercase tracking-[0.38em] text-gold">{{ t('objects.d_similar') }}</h2>
          </div>
          <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <Link v-for="a in similar" :key="a.id"
              :href="route('apartments.show', [a.project_slug, a.id])"
              class="group rounded-2xl overflow-hidden border border-white/6 hover:border-gold/30 hover:-translate-y-0.5 transition-all duration-300"
              style="background:#0F1828">
              <div class="relative" style="height:150px">
                <img v-if="a.plan_image" :src="asset(a.plan_image)" :alt="`${t('objects.apt_abbr')} ${a.number}`" loading="lazy"
                  class="w-full h-full object-contain bg-[#080E18]"/>
                <div v-else class="w-full h-full flex items-center justify-center" style="background:linear-gradient(135deg,#0A0E1A,#0D1525)">
                  <span class="text-gold/40 font-black text-2xl">{{ a.rooms }}</span>
                </div>
                <span :class="statusBadge(a.status)" class="absolute top-3 left-3 text-[9px] font-bold px-2 py-0.5 rounded-full tracking-wider uppercase">
                  {{ statusLabel(a.status) }}
                </span>
              </div>
              <div class="p-4">
                <div class="text-white font-semibold text-sm">{{ roomLabel(a.rooms) }}, {{ a.area }} м²</div>
                <div class="text-gray-500 text-xs mt-0.5">{{ t('objects.apt_abbr') }} {{ a.number }} · {{ a.floor }} {{ t('objects.floor_word') }}</div>
                <div class="text-gold font-bold text-base mt-3">${{ fmtPrice(a.price) }}</div>
              </div>
            </Link>
          </div>
        </div>
      </section>

      <!-- ═══ STICKY CTA (мобильный) ═══ -->
      <div class="lg:hidden fixed bottom-0 inset-x-0 z-40 px-4 py-3 flex items-center gap-3"
        style="background:rgba(7,11,22,0.96); backdrop-filter:blur(10px); border-top:1px solid rgba(255,255,255,0.08)">
        <div class="flex-1 min-w-0">
          <div class="text-gold font-black text-lg leading-none truncate">${{ fmtPrice(apartment.price) }}</div>
          <div class="text-white/40 text-[10px] truncate">{{ roomLabel(apartment.rooms) }}, {{ apartment.area }} м²</div>
        </div>
        <button v-if="apartment.status === 'available'" @click="reserveOpen = true"
          class="shrink-0 bg-gold hover:bg-gold-600 text-dark font-bold px-6 py-3 rounded-xl uppercase tracking-wider text-xs transition-all">
          {{ t('objects.bar_book') }}
        </button>
        <span v-else class="shrink-0 text-gray-500 font-bold uppercase tracking-wider text-xs px-4 py-3">
          {{ statusLabel(apartment.status) }}
        </span>
      </div>
      <!-- отступ, чтобы sticky-бар не перекрывал контент на мобильных -->
      <div class="lg:hidden" style="height:76px"></div>
    </div>

    <!-- ═══ LIGHTBOX ═══ -->
    <Teleport to="body">
      <Transition name="lb">
        <div v-if="lightbox" class="fixed inset-0 z-[110] flex items-center justify-center p-4"
          style="background:rgba(0,0,0,0.92)" @click.self="lightbox = false">
          <button @click="lightbox = false" class="absolute top-5 right-5 text-white/60 hover:text-white transition-colors" aria-label="Закрыть">
            <Icon name="close" class="w-7 h-7" :stroke-width="1.8" />
          </button>
          <img :src="asset(images[activeIdx])" :alt="`${t('objects.apt_abbr')} ${apartment.number}`"
            class="max-w-full max-h-full object-contain rounded-lg"/>
        </div>
      </Transition>
    </Teleport>

    <!-- ═══ МОДАЛКА БРОНИРОВАНИЯ (переиспользуемая) ═══ -->
    <ReserveModal v-model="reserveOpen" :apartment="apartment" />
  </MainLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, Head, usePage } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'
import Icon from '@/Components/Icon.vue'
import ReserveModal from '@/Components/ReserveModal.vue'
import { useAsset } from '@/composables/useAsset'
import { useTrans } from '@/composables/useTrans'
import { useApartmentFormat } from '@/composables/useApartmentFormat'

const props = defineProps({
  apartment:     { type: Object, required: true },
  project:       { type: Object, default: null },
  similar:       { type: Array,  default: () => [] },
  canonicalSlug: { type: String, default: '' },
})

const { asset } = useAsset()
const { t } = useTrans()
const { fmtPrice, roomLabel, finishLabel, statusLabel, statusBadge, pricePerM2 } = useApartmentFormat()
const page = usePage()

const contactPhone = computed(() => page.props.contacts?.phone ?? '+992 00 000 00 00')

// WhatsApp / Telegram — контакт с менеджером с предзаполненным сообщением о квартире
const shareMessage = computed(() => {
  const url = typeof window !== 'undefined' ? window.location.href : ''
  return `Здравствуйте! Интересует квартира №${props.apartment.number} (${props.apartment.rooms}-комн., ${props.apartment.area} м², ${props.apartment.floor} этаж). ${url}`
})
const whatsappUrl = computed(() => {
  const num = String(page.props.contacts?.whatsapp || '').replace(/\D/g, '')
  return `https://wa.me/${num}?text=${encodeURIComponent(shareMessage.value)}`
})
const telegramUrl = computed(() => {
  const user = String(page.props.contacts?.telegram || '').replace(/^@/, '')
  return `https://t.me/${user}`
})

// Галерея: пока в БД есть только план — при добавлении фото массив расширится
// Галерея: реальные фото квартиры (из админки); если их нет — план как запасной вариант
const images = computed(() => {
  const photos = props.apartment.images || []
  return photos.length ? photos : [props.apartment.plan_image].filter(Boolean)
})
const activeIdx = ref(0)
const lightbox = ref(false)
const reserveOpen = ref(false)

function openLightbox(i) {
  if (!images.value.length) return
  activeIdx.value = i
  lightbox.value = true
}

const ppm = computed(() => pricePerM2(props.apartment.price, props.apartment.area))

// Карточки характеристик — только те, что реально есть
const specs = computed(() => {
  const a = props.apartment
  const list = [
    { icon: '📐', label: t('objects.d_sp_area'),  value: `${a.area} м²` },
    { icon: '🛏', label: t('objects.d_sp_rooms'),   value: roomLabel(a.rooms) },
    { icon: '🏢', label: t('objects.d_sp_floor'),     value: `${a.floor}` },
    { icon: '💰', label: t('objects.d_sp_price'), value: `$${fmtPrice(a.price)}` },
  ]
  if (ppm.value) list.push({ icon: '📊', label: t('objects.d_sp_ppm'), value: `$${fmtPrice(ppm.value)}` })
  if (a.finish)  list.push({ icon: '🎨', label: t('objects.d_sp_finish'),   value: finishLabel(a.finish) })
  if (props.project) list.push({ icon: '📍', label: t('objects.d_sp_project'), value: props.project.name })
  list.push({ icon: '📦', label: t('objects.d_sp_status'), value: statusLabel(a.status) })
  return list
})

// SEO
const metaTitle = computed(() =>
  `${roomLabel(props.apartment.rooms)}, ${props.apartment.area} м² — ${t('objects.apt_abbr')} ${props.apartment.number}`)
const metaDescription = computed(() => {
  const a = props.apartment
  const proj = props.project ? ` в ${props.project.name}` : ''
  return `Купить ${roomLabel(a.rooms).toLowerCase()} квартиру ${a.area} м²${proj}, ${a.floor} этаж. Цена $${fmtPrice(a.price)}. QUDRAT.`
})

// Schema.org — Apartment
const schemaJson = computed(() => JSON.stringify({
  '@context': 'https://schema.org',
  '@type': 'Apartment',
  name: metaTitle.value,
  numberOfRooms: props.apartment.rooms,
  floorSize: { '@type': 'QuantitativeValue', value: props.apartment.area, unitCode: 'MTK' },
  floorLevel: String(props.apartment.floor),
  ...(props.project ? { address: { '@type': 'PostalAddress', addressLocality: props.project.city, streetAddress: props.project.address } } : {}),
  offers: {
    '@type': 'Offer',
    price: props.apartment.price,
    priceCurrency: props.apartment.currency,
    availability: props.apartment.status === 'available'
      ? 'https://schema.org/InStock'
      : 'https://schema.org/SoldOut',
  },
}))
</script>

<style scoped>
.lb-enter-active, .lb-leave-active { transition: opacity 0.25s ease; }
.lb-enter-from, .lb-leave-to { opacity: 0; }
</style>
