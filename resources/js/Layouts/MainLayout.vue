<template>
  <div class="min-h-screen bg-[#0C1628] text-white overflow-x-hidden">

    <!-- Skip to main content (a11y) -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[200] focus:bg-gold focus:text-dark focus:px-4 focus:py-2 focus:rounded focus:font-bold focus:text-sm">
      Перейти к содержимому
    </a>

    <!-- Navbar -->
    <header
      class="fixed top-0 left-0 right-0 z-50 transition-all duration-500"
      :class="scrolled ? 'bg-[#0C1628]/95 backdrop-blur-xl shadow-[0_1px_0_rgba(201,169,110,0.15)]' : 'bg-[#0C1628]/90 backdrop-blur-sm xl:bg-transparent xl:backdrop-blur-none'"
    >
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="flex items-center justify-between h-20">

          <!-- Logo -->
          <Link :href="route('home')" class="flex items-center gap-3 shrink-0">
            <img :src="asset('/images/logo-mark.webp')" alt="QUDRAT" class="h-12 w-12 object-contain" />
            <div class="flex flex-col leading-none gap-1">
              <span
                class="text-[21px] font-bold text-white leading-none"
                style="font-family: Georgia, 'Times New Roman', serif; letter-spacing: 0.06em;"
              >QUDRAT</span>
              <div class="flex items-center gap-1.5">
                <span class="h-px w-3 bg-gold"></span>
                <span
                  class="text-[8px] font-semibold text-gold whitespace-nowrap"
                  style="letter-spacing: 0.05em;"
                >{{ t('footer.legal_badge') }}</span>
                <span class="h-px w-3 bg-gold"></span>
              </div>
            </div>
          </Link>

          <!-- Desktop Nav -->
          <nav class="hidden xl:flex items-center gap-8" aria-label="Основная навигация">
            <Link
              v-for="item in navItems" :key="item.route"
              :href="route(item.route)"
              class="relative text-[12px] font-medium tracking-widest uppercase text-gray-400 hover:text-white transition-colors duration-300 group"
              :class="{ '!text-white': isActive(item.path) }"
            >
              {{ item.label }}
              <span
                class="absolute -bottom-1 left-0 h-px bg-gold transition-all duration-300"
                :class="isActive(item.path) ? 'w-full' : 'w-0 group-hover:w-full'"
              ></span>
            </Link>
          </nav>

          <!-- Right -->
          <div class="flex items-center gap-4">

            <!-- Lang dropdown with flags — доступен с клавиатуры, мыши и тач -->
            <div
              ref="langRef"
              class="relative hidden md:block"
              @mouseenter="langOpen = true"
              @mouseleave="langOpen = false"
              @keydown.esc="langOpen = false"
            >
              <button
                type="button"
                @click="langOpen = !langOpen"
                class="flex items-center gap-2 text-[12px] font-medium text-gray-400 hover:text-white transition-colors tracking-widest uppercase border border-white/10 hover:border-white/30 rounded px-3 py-1.5"
                :aria-expanded="langOpen"
                aria-haspopup="listbox"
                aria-label="Выбор языка / Change language"
              >
                <span class="text-base">{{ currentFlag }}</span>
                <span>{{ currentLabel }}</span>
                <Icon name="chevron-down" class="w-3 h-3 transition-transform duration-200" :stroke-width="2.5" />
              </button>

              <!-- Dropdown -->
              <Transition
                enter-active-class="transition-all duration-200"
                enter-from-class="opacity-0 translate-y-1"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition-all duration-150"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 translate-y-1"
              >
                <div v-if="langOpen" role="listbox" aria-label="Язык сайта"
                  class="absolute right-0 top-full mt-2 w-36 rounded-xl overflow-hidden shadow-[0_8px_30px_rgba(0,0,0,0.4)] border border-white/10"
                  style="background: #111E35"
                >
                  <button
                    v-for="(lang, code) in langs" :key="code"
                    type="button"
                    role="option"
                    :aria-selected="locale === code"
                    @click="switchLang(code)"
                    class="w-full flex items-center gap-3 px-4 py-3 text-[12px] tracking-widest uppercase transition-all duration-200 hover:bg-gold/10"
                    :class="locale === code ? 'text-gold font-semibold' : 'text-gray-400 hover:text-white'"
                  >
                    <span class="text-base">{{ lang.flag }}</span>
                    <span>{{ lang.label }}</span>
                    <span v-if="locale === code" class="ml-auto w-1.5 h-1.5 rounded-full bg-gold"></span>
                  </button>
                </div>
              </Transition>
            </div>

            <!-- ── АВТОРИЗОВАННЫЙ КЛИЕНТ ── -->
            <template v-if="isClient">
              <Link :href="route('client.cabinet')"
                class="hidden xl:flex items-center gap-2 bg-gold hover:bg-gold-600 text-dark text-[11px] font-bold px-4 py-2.5 rounded transition-all duration-300 tracking-widest uppercase">
                <span class="w-6 h-6 rounded-full bg-dark/20 flex items-center justify-center text-[10px] font-bold">
                  {{ authInitials }}
                </span>
                {{ t('nav.cabinet') }}
              </Link>
            </template>

            <!-- ── СОТРУДНИК (admin/manager/viewer) ── -->
            <template v-else-if="isStaff">
              <Link :href="route('admin.dashboard')"
                class="hidden xl:flex items-center gap-2 bg-gold hover:bg-gold-600 text-dark text-[11px] font-bold px-5 py-2.5 rounded transition-all duration-300 tracking-widest uppercase">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/>
                </svg>
                {{ t('nav.crm') }}
              </Link>
            </template>

            <!-- ── ГОСТЬ (не авторизован) ── -->
            <template v-else>
              <!-- Оставить заявку -->
              <Link :href="route('register')"
                class="hidden xl:flex items-center gap-2 bg-gold hover:bg-gold-600 text-dark text-[11px] font-bold px-5 py-2.5 rounded transition-all duration-300 tracking-widest uppercase">
                <Icon name="plus" class="w-3.5 h-3.5" :stroke-width="2.2" />
                {{ t('nav.cta') }}
              </Link>
              <!-- Войти -->
              <Link :href="route('client.login')"
                class="hidden xl:flex items-center gap-1.5 border border-white/15 text-gray-400 hover:border-gold/40 hover:text-gold text-[10px] font-bold px-3.5 py-2.5 rounded transition-all duration-300 tracking-widest uppercase">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/>
                </svg>
                {{ t('nav.login') }}
              </Link>
            </template>

            <!-- Burger -->
            <button
              @click="mobileOpen = !mobileOpen"
              class="xl:hidden w-10 h-10 flex flex-col items-center justify-center gap-1.5"
              :aria-expanded="mobileOpen"
              aria-controls="mobile-menu"
              :aria-label="mobileOpen ? 'Закрыть меню' : 'Открыть меню'"
            >
              <span class="block w-6 h-px bg-white transition-all duration-300" :class="mobileOpen ? 'rotate-45 translate-y-[7px]' : ''"></span>
              <span class="block w-4 h-px bg-gold transition-all duration-300 self-start" :class="mobileOpen ? 'opacity-0' : ''"></span>
              <span class="block w-6 h-px bg-white transition-all duration-300" :class="mobileOpen ? '-rotate-45 -translate-y-[7px]' : ''"></span>
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile menu -->
      <Transition
        enter-active-class="transition-all duration-300"
        enter-from-class="opacity-0 -translate-y-4"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition-all duration-200"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-4"
      >
        <div v-if="mobileOpen" id="mobile-menu" class="xl:hidden bg-[#0E1830] border-t border-white/5" role="navigation" aria-label="Мобильное меню">
          <div class="max-w-[1400px] mx-auto px-6 py-6 space-y-1">
            <Link
              v-for="item in navItems" :key="item.route"
              :href="route(item.route)"
              @click="mobileOpen = false"
              class="flex items-center gap-3 text-sm uppercase tracking-widest text-gray-300 hover:text-gold transition-colors py-3 border-b border-white/5"
            >
              {{ item.label }}
            </Link>
            <!-- Mobile lang with flags -->
            <div class="flex gap-2 pt-4">
              <button v-for="(lang, code) in langs" :key="code"
                @click="switchLang(code)"
                class="flex items-center gap-1.5 text-[11px] font-medium px-3 py-2 rounded border transition-all"
                :class="locale === code
                  ? 'border-gold text-gold bg-gold/10'
                  : 'border-white/10 text-gray-400'"
              >
                <span class="text-sm">{{ lang.flag }}</span>
                <span>{{ lang.label }}</span>
              </button>
            </div>
            <!-- Mobile CTA: client -->
            <template v-if="isClient">
              <Link :href="route('client.cabinet')" @click="mobileOpen = false"
                class="flex items-center justify-center gap-2 bg-gold hover:bg-gold-600 text-dark font-bold py-3.5 rounded uppercase tracking-widest text-sm mt-4">
                {{ t('nav.cabinet') }}
              </Link>
            </template>
            <!-- Mobile CTA: staff -->
            <template v-else-if="isStaff">
              <Link :href="route('admin.dashboard')" @click="mobileOpen = false"
                class="flex items-center justify-center gap-2 bg-gold hover:bg-gold-600 text-dark font-bold py-3.5 rounded uppercase tracking-widest text-sm mt-4">
                {{ t('nav.crm') }}
              </Link>
            </template>
            <!-- Mobile CTA: guest -->
            <template v-else>
              <Link :href="route('register')" @click="mobileOpen = false"
                class="flex items-center justify-center gap-2 bg-gold hover:bg-gold-600 text-dark font-bold py-3.5 rounded uppercase tracking-widest text-sm mt-4">
                {{ t('nav.cta') }}
              </Link>
              <Link :href="route('client.login')" @click="mobileOpen = false"
                class="flex items-center justify-center gap-2 border border-white/15 text-gray-400 font-bold py-3 rounded uppercase tracking-widest text-xs mt-2">
                {{ t('nav.login') }}
              </Link>
            </template>
          </div>
        </div>
      </Transition>
    </header>

    <!-- ═══ Полоса тестового режима (выключается: SITE_TEST_BANNER=false в .env) ═══ -->
    <div v-if="testBanner && !testBannerDismissed"
      class="fixed inset-x-0 z-40 flex items-center justify-center gap-3 px-4 py-1.5 text-center"
      style="top:80px; background:linear-gradient(90deg,rgba(180,83,9,0.96),rgba(146,64,14,0.96)); backdrop-filter:blur(6px)">
      <svg class="w-3.5 h-3.5 text-amber-200 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
      </svg>
      <span class="text-amber-100 text-[11px] font-bold uppercase tracking-[0.2em]">{{ testBannerText }}</span>
      <button @click="dismissTestBanner" class="text-amber-200/70 hover:text-white transition-colors text-sm leading-none" aria-label="Скрыть">✕</button>
    </div>

    <!-- Page -->
    <main id="main-content">
      <slot />
    </main>


    <!-- ═══ FOOTER ═══ -->
    <footer style="background:#030B22; position:relative; overflow:hidden">

      <!-- ── BRAND WATERMARK ── -->
      <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none select-none" style="z-index:0; top:10%">
        <div class="footer-watermark-text">QUDRAT</div>
        <div class="footer-watermark-sub">EST. 2010</div>
      </div>

      <!-- ── MAIN BODY ── -->
      <div class="relative max-w-[1600px] mx-auto px-6 lg:px-16 pt-24 pb-14" style="z-index:1">

        <!-- 4-column grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-14 xl:gap-16 mb-20">

          <!-- COL 1: Brand identity -->
          <div>
            <Link :href="route('home')" class="inline-flex items-center gap-3 mb-8 group">
              <img :src="asset('/images/logo-mark.webp')" alt="QUDRAT"
                class="h-14 w-14 object-contain transition-all duration-500 group-hover:drop-shadow-[0_0_22px_rgba(201,169,110,0.5)]"
                loading="lazy"/>
              <div class="flex flex-col leading-none gap-1.5">
                <span class="text-[22px] font-bold text-white leading-none group-hover:text-gold transition-colors duration-300"
                  style="font-family:Georgia,'Times New Roman',serif; letter-spacing:0.06em">QUDRAT</span>
                <div class="flex items-center gap-1.5">
                  <span class="h-px w-3 bg-gold"></span>
                  <span class="text-[8px] font-semibold text-gold whitespace-nowrap" style="letter-spacing:0.16em">{{ t('footer.legal_badge') }}</span>
                  <span class="h-px w-3 bg-gold"></span>
                </div>
              </div>
            </Link>

            <p class="text-white/55 text-sm leading-[1.95] mb-9 max-w-[210px]">
              {{ t('footer.brand_desc') }}
            </p>

            <!-- Social icons -->
            <div class="flex gap-3">
              <a v-for="social in socials" :key="social.name"
                :href="social.href"
                target="_blank" rel="noopener noreferrer"
                :aria-label="social.name"
                class="social-icon w-10 h-10 flex items-center justify-center rounded-lg text-white/30 transition-all duration-300"
                style="border:1px solid rgba(255,255,255,0.07)">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path :d="social.icon" /></svg>
              </a>
            </div>
          </div>

          <!-- COL 2: Directions -->
          <div>
            <div class="flex items-center gap-3 mb-9">
              <span class="block h-px w-6 bg-gold"></span>
              <h4 class="text-[10px] font-bold uppercase tracking-[0.38em] text-gold">{{ t('footer.col_directions') }}</h4>
            </div>

            <div class="space-y-7">
              <!-- Real estate -->
              <div>
                <div class="text-[9px] uppercase tracking-[0.32em] text-white/20 mb-3 font-semibold">{{ t('footer.sub_realty') }}</div>
                <ul class="space-y-2.5">
                  <li><Link :href="route('objects')" class="footer-link">{{ t('footer.link_city') }}</Link></li>
                  <li><a href="#" class="footer-link">{{ t('footer.link_commercial') }}</a></li>
                </ul>
              </div>
              <!-- Mining -->
              <div>
                <div class="text-[9px] uppercase tracking-[0.32em] text-white/20 mb-3 font-semibold">{{ t('footer.sub_mining') }}</div>
                <ul class="space-y-2.5">
                  <li><Link :href="route('mining')" class="footer-link">{{ t('footer.link_coal') }}</Link></li>
                  <li><a href="#" class="footer-link">{{ t('footer.link_coal_proc') }}</a></li>
                </ul>
              </div>
              <!-- Tech -->
              <div>
                <ul class="space-y-2.5">
                  <li><Link :href="route('bms')" class="footer-link">{{ t('footer.link_smart') }}</Link></li>
                </ul>
              </div>
            </div>
          </div>

          <!-- COL 3: Investors -->
          <div>
            <div class="flex items-center gap-3 mb-9">
              <span class="block h-px w-6 bg-gold"></span>
              <h4 class="text-[10px] font-bold uppercase tracking-[0.38em] text-gold">{{ t('footer.col_investors') }}</h4>
            </div>
            <ul class="space-y-3">
              <li><Link :href="route('partners')" class="footer-link">{{ t('footer.link_partners') }}</Link></li>
              <li><a href="#" class="footer-link">{{ t('footer.link_presentations') }}</a></li>
              <li><a href="#" class="footer-link">{{ t('footer.link_docs') }}</a></li>
              <li><Link :href="route('news')" class="footer-link">{{ t('footer.link_news') }}</Link></li>
              <li><Link :href="route('career')" class="footer-link">{{ t('footer.link_career') }}</Link></li>
            </ul>
          </div>

          <!-- COL 4: Contacts + Newsletter -->
          <div>
            <div class="flex items-center gap-3 mb-9">
              <span class="block h-px w-6 bg-gold"></span>
              <h4 class="text-[10px] font-bold uppercase tracking-[0.38em] text-gold">{{ t('footer.col_contacts') }}</h4>
            </div>

            <ul class="space-y-4 mb-9">
              <li class="flex items-start gap-3">
                <Icon name="map-pin" class="w-4 h-4 text-gold/70 shrink-0 mt-0.5" :stroke-width="1.5" />
                <span class="text-white/60 text-sm leading-relaxed">{{ t('footer.address') }}<br>{{ t('footer.address2') }}</span>
              </li>
              <li>
                <a :href="`tel:${contactPhone}`"
                  class="flex items-center gap-3 text-white/60 hover:text-gold transition-colors duration-300 text-sm">
                  <Icon name="phone" class="w-4 h-4 text-gold/70 shrink-0" :stroke-width="1.5" />
                  {{ contactPhone }}
                </a>
              </li>
              <li>
                <a :href="`mailto:${contactEmail}`"
                  class="flex items-center gap-3 text-white/60 hover:text-gold transition-colors duration-300 text-sm">
                  <Icon name="mail" class="w-4 h-4 text-gold/70 shrink-0" :stroke-width="1.5" />
                  {{ contactEmail }}
                </a>
              </li>
              <li class="flex items-center gap-3 text-white/60 text-sm">
                <Icon name="clock" class="w-4 h-4 text-gold/70 shrink-0" :stroke-width="1.5" />
                {{ t('footer.hours') }}
              </li>
            </ul>

            <!-- Newsletter -->
            <div class="pt-7" style="border-top:1px solid rgba(255,255,255,0.06)">
              <div class="text-[9px] font-bold uppercase tracking-[0.38em] text-gold/80 mb-4">{{ t('footer.newsletter_title') }}</div>
              <form @submit.prevent="sendNewsletter" class="flex" role="form" :aria-label="t('footer.newsletter_title')">
                <label for="footer-email" class="sr-only">{{ t('footer.email_placeholder') }}</label>
                <input id="footer-email" v-model="footerEmail" type="email" required
                  :placeholder="t('footer.email_placeholder')"
                  autocomplete="email"
                  class="newsletter-input flex-1 min-w-0 text-white text-sm px-4 py-3 rounded-l outline-none"
                  style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08); border-right:none"
                />
                <button type="submit" :disabled="footerSent"
                  class="flex items-center justify-center px-5 py-3 rounded-r font-bold transition-all duration-300 disabled:opacity-60 hover:opacity-90"
                  style="background:#C9A96E; color:#030B22; min-width:52px"
                  :aria-label="footerSent ? 'Подписка оформлена' : 'Подписаться'">
                  <span v-if="footerSent" class="text-base">✓</span>
                  <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                  </svg>
                </button>
              </form>
            </div>
          </div>
        </div>

        <!-- ── BOTTOM BAR ── -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-5 pt-8" style="border-top:1px solid rgba(255,255,255,0.05)">
          <p class="text-white/45 text-[11px] tracking-[0.18em] uppercase">
            {{ t('footer.copyright') }}
          </p>
          <div class="flex flex-wrap items-center gap-2 md:gap-5">
            <Link :href="route('privacy')"
              class="text-white/45 hover:text-gold text-[10px] tracking-[0.15em] uppercase transition-colors duration-300">
              {{ t('footer.privacy_short') }}
            </Link>
            <span class="w-px h-3 bg-white/10 hidden md:block"></span>
            <Link :href="route('terms')"
              class="text-white/45 hover:text-gold text-[10px] tracking-[0.15em] uppercase transition-colors duration-300">
              {{ t('footer.terms') }}
            </Link>
            <span class="w-px h-3 bg-white/10 hidden md:block"></span>
            <a href="#"
              class="text-white/45 hover:text-gold text-[10px] tracking-[0.15em] uppercase transition-colors duration-300">
              {{ t('footer.sitemap') }}
            </a>
          </div>
        </div>
      </div>
    </footer>

    <!-- Cookie consent banner -->
    <CookieBanner />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage, useForm, router } from '@inertiajs/vue3';
import { useAsset } from '@/composables/useAsset';
import { useTrans } from '@/composables/useTrans';
import CookieBanner from '@/Components/CookieBanner.vue';
import Icon from '@/Components/Icon.vue';

const page = usePage();
// Реактивная локаль — обновляется после смены языка без полной перезагрузки
const locale = computed(() => page.props.locale ?? 'ru');
const { asset } = useAsset();
const { t } = useTrans();

// ── Контактные данные из page props (шарятся через HandleInertiaRequests) ──
const contactPhone    = computed(() => page.props.contacts?.phone    ?? '+992 00 000 00 00');
const contactWhatsapp = computed(() => page.props.contacts?.whatsapp ?? '992000000000');
const contactTelegram = computed(() => page.props.contacts?.telegram  ?? 'qudrat_tj');
const contactEmail    = computed(() => page.props.contacts?.email    ?? 'info@qudrat.tj');

// ── Footer newsletter ──
const footerEmail = ref('');
const footerSent  = ref(false);
const footerForm  = useForm({ email: '' });

function sendNewsletter() {
  footerForm.email = footerEmail.value;
  footerForm.post(route('newsletter.subscribe'), {
    preserveScroll: true,
    onSuccess: () => {
      footerSent.value = true;
      footerEmail.value = '';
      setTimeout(() => { footerSent.value = false; }, 4000);
    },
  });
}
const mobileOpen = ref(false);
const langOpen = ref(false);
const langRef = ref(null);
const scrolled = ref(false);

// ── Полоса тестового режима ──
const testBanner = computed(() => page.props.testBanner ?? false);
const testBannerDismissed = ref(false);
try { testBannerDismissed.value = sessionStorage.getItem('test-banner-hidden') === '1'; } catch (e) {}

const testBannerText = computed(() => ({
  ru: 'Сайт работает в тестовом режиме',
  tj: 'Сомона дар реҷаи озмоишӣ кор мекунад',
  en: 'The website is running in test mode',
}[locale.value] ?? 'Сайт работает в тестовом режиме'));

function dismissTestBanner() {
  testBannerDismissed.value = true;
  try { sessionStorage.setItem('test-banner-hidden', '1'); } catch (e) {}
}

// ── Auth state ──
const authUser    = computed(() => page.props.auth?.user ?? null);
const isClient    = computed(() => authUser.value?.role === 'client');
const isStaff     = computed(() => ['admin', 'manager', 'viewer'].includes(authUser.value?.role));
const authInitials = computed(() => {
  const name = authUser.value?.name || '';
  return name.split(' ').map(p => p[0]).join('').toUpperCase().slice(0, 2);
});

const langs = {
  ru: { label: 'RU', flag: '🇷🇺' },
  tj: { label: 'TJ', flag: '🇹🇯' },
  en: { label: 'EN', flag: '🇬🇧' },
};

// Вычисляемые свойства для текущего языка
const currentFlag = computed(() => langs[locale.value]?.flag || '🌐');
const currentLabel = computed(() => langs[locale.value]?.label || locale.value.toUpperCase());

const navItems = computed(() => [
  { route: 'home',      label: t('nav.home'),      path: '/' },
  { route: 'objects',   label: t('nav.objects'),   path: '/objects' },
  { route: 'mining',    label: t('nav.mining'),    path: '/mining' },
  { route: 'services',  label: t('nav.services'),  path: '/services' },
  { route: 'about',     label: t('nav.about'),     path: '/about' },
]);

const socials = [
  { name: 'Instagram', href: '#', icon: 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z' },
  { name: 'Telegram', href: '#', icon: 'M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z' },
  { name: 'LinkedIn', href: '#', icon: 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z' },
  { name: 'WhatsApp', href: '#', icon: 'M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z' },
];

function switchLang(code) {
  router.post(route('lang.switch', code), {}, { preserveScroll: true });
}

function isActive(path) {
  if (path === '/') return window.location.pathname === '/';
  return window.location.pathname.startsWith(path);
}

function onScroll() {
  scrolled.value = window.scrollY > 40;
}

// Закрываем языковое меню при клике вне его (для тач/клавиатуры)
function onDocClick(e) {
  if (langOpen.value && langRef.value && !langRef.value.contains(e.target)) {
    langOpen.value = false;
  }
}

onMounted(() => {
  window.addEventListener('scroll', onScroll, { passive: true });
  document.addEventListener('click', onDocClick);
});
onUnmounted(() => {
  window.removeEventListener('scroll', onScroll);
  document.removeEventListener('click', onDocClick);
});
</script>

<style scoped>
/* ── Footer links ── */
.footer-link {
  display: inline-flex;
  align-items: center;
  gap: 0;
  font-size: 0.8125rem;
  color: rgba(255,255,255,0.55);
  transition: color 0.25s ease, letter-spacing 0.25s ease;
  letter-spacing: 0.01em;
  text-decoration: none;
  position: relative;
}
.footer-link::before {
  content: '';
  display: inline-block;
  width: 0;
  height: 1px;
  background: #C9A96E;
  margin-right: 0;
  transition: width 0.25s ease, margin-right 0.25s ease;
  vertical-align: middle;
}
.footer-link:hover {
  color: rgba(255,255,255,0.88);
  letter-spacing: 0.02em;
}
.footer-link:hover::before {
  width: 12px;
  margin-right: 7px;
}

/* ── Social icon hover ── */
.social-icon:hover {
  color: #C9A96E;
  border-color: rgba(201,169,110,0.35) !important;
  background: rgba(201,169,110,0.06);
  box-shadow: 0 0 14px rgba(201,169,110,0.12);
}

/* ── Newsletter input focus ── */
.newsletter-input:focus {
  border-color: rgba(201,169,110,0.4) !important;
  background: rgba(255,255,255,0.05) !important;
}
.newsletter-input::placeholder {
  color: rgba(255,255,255,0.18);
}

/* ── Brand watermark ── */
.footer-watermark-text {
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: clamp(120px, 17vw, 260px);
  font-weight: 900;
  color: rgba(201,169,110,0.038);
  letter-spacing: 0.1em;
  line-height: 0.85;
  text-align: center;
  filter: blur(1.5px);
  white-space: nowrap;
  user-select: none;
}
.footer-watermark-sub {
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: clamp(16px, 2.2vw, 32px);
  font-weight: 400;
  color: rgba(201,169,110,0.03);
  letter-spacing: 0.6em;
  text-align: center;
  filter: blur(0.5px);
  margin-top: 6px;
  user-select: none;
}

/* ── Reduced motion ── */
@media (prefers-reduced-motion: reduce) {
  .footer-link,
  .footer-link::before,
  .social-icon { transition: none; }
}
</style>