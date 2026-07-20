<template>
  <MainLayout>
    <SeoHead
      :title="t('bms.meta_title')"
      :description="t('bms.meta_desc')"
      path="/bms"
    />

    <!-- ═══ 1. HERO ═══ -->
    <section class="bms-hero relative overflow-hidden">
      <!-- Background image -->
      <div class="absolute inset-0 z-0">
        <img :src="asset('/images/bms.webp')" alt="BMS Smart Building"
          class="w-full h-full object-cover object-center"/>
        <div class="absolute inset-0" style="background:linear-gradient(135deg,rgba(6,18,40,0.92) 0%,rgba(6,18,40,0.75) 50%,rgba(6,18,40,0.55) 100%)"></div>
      </div>

      <div class="bms-hero-inner relative z-10 max-w-[1400px] mx-auto px-6 lg:px-14 pt-36 pb-24">

        <!-- Eyebrow -->
        <div class="flex items-center gap-3 mb-8">
          <div class="w-10 h-px" style="background:#D4AF37"></div>
          <span class="text-[10px] font-bold uppercase tracking-[0.5em]" style="color:rgba(212,175,55,0.7)">QUDRAT · SMART LIVING · BMS</span>
        </div>

        <!-- Main heading -->
        <h1 class="bms-h1 mb-6">
          <span class="block text-white">{{ t('bms.hero_l1') }}</span>
          <span class="block" style="background:linear-gradient(130deg,#fff 20%,#e8d5a0 55%,#D4AF37 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">{{ t('bms.hero_l2') }}</span>
        </h1>

        <!-- Description -->
        <p class="text-white/50 leading-relaxed mb-10 max-w-xl" style="font-size:clamp(14px,1.1vw,17px);font-weight:300">
          {{ t('bms.hero_desc') }}
        </p>

        <!-- Feature pills -->
        <div class="flex flex-wrap gap-3 mb-12">
          <span v-for="pill in heroPills" :key="pill"
            class="flex items-center gap-2 px-4 py-2 rounded-full border text-xs font-medium"
            style="border-color:rgba(212,175,55,0.25);color:rgba(212,175,55,0.8);background:rgba(212,175,55,0.06)">
            <span style="color:#D4AF37">◆</span> {{ pill }}
          </span>
        </div>

        <!-- CTA -->
        <div class="flex flex-wrap gap-4">
          <Link :href="route('contacts')"
            class="inline-flex items-center gap-2 font-bold text-[11px] uppercase tracking-widest px-8 py-4 rounded transition-all hover:opacity-90"
            style="background:#D4AF37;color:#061228">
            {{ t('bms.cta_consult') }}
            <Icon name="arrow-right" class="w-4 h-4" :stroke-width="2" />
          </Link>
          <Link :href="route('objects')"
            class="inline-flex items-center gap-2 font-semibold text-[11px] uppercase tracking-widest px-8 py-4 rounded border transition-all"
            style="border-color:rgba(212,175,55,0.3);color:rgba(212,175,55,0.7)">
            {{ t('bms.cta_projects') }}
          </Link>
        </div>

      </div>
    </section>

    <!-- ═══ 2. SMART FEATURES — смартфон + карточки ═══ -->
    <section class="py-28" style="background:#061228">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-14">

        <!-- Section header -->
        <div class="text-center mb-20">
          <div class="flex items-center justify-center gap-3 mb-5">
            <div class="h-px w-12" style="background:linear-gradient(to right,transparent,#D4AF37)"></div>
            <span class="text-[10px] font-bold uppercase tracking-[0.5em]" style="color:rgba(212,175,55,0.6)">{{ t('bms.feat_badge') }}</span>
            <div class="h-px w-12" style="background:linear-gradient(to left,transparent,#D4AF37)"></div>
          </div>
          <h2 class="bms-h2 text-white mb-4">{{ t('bms.feat_title') }}</h2>
          <p class="text-white/40 max-w-lg mx-auto" style="font-size:15px">{{ t('bms.feat_sub') }}</p>
        </div>

        <!-- 3-column: left features | phone | right features -->
        <div class="grid lg:grid-cols-3 gap-10 items-center">

          <!-- LEFT -->
          <div class="space-y-10">
            <div v-for="f in featLeft" :key="f.title" class="bms-feat-card">
              <div class="bms-feat-label">{{ f.label }}</div>
              <div class="bms-feat-title">{{ f.title }}</div>
              <div class="bms-feat-desc">{{ f.desc }}</div>
            </div>
          </div>

          <!-- PHONE MOCKUP -->
          <div class="flex justify-center">
            <div class="bms-phone">
              <!-- Screen -->
              <div class="bms-phone-screen">
                <!-- App header -->
                <div class="flex items-center justify-between mb-4">
                  <div>
                    <div class="text-[8px] text-white/40 uppercase tracking-widest mb-0.5">{{ t('bms.app_title') }}</div>
                    <div class="text-white font-bold text-sm" style="font-family:'Cormorant Garamond',serif">QUDRAT</div>
                  </div>
                  <div class="w-6 h-6 rounded-full flex items-center justify-center" style="background:rgba(212,175,55,0.15)">
                    <div class="w-2 h-2 rounded-full" style="background:#D4AF37"></div>
                  </div>
                </div>
                <!-- App grid -->
                <div class="grid grid-cols-2 gap-2 mb-4">
                  <div v-for="app in appItems" :key="app.l" class="bms-app-item">
                    <svg class="w-4 h-4 mb-1.5" style="color:#D4AF37" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" :d="app.icon"/>
                    </svg>
                    <span>{{ app.l }}</span>
                  </div>
                </div>
                <!-- Status bar -->
                <div class="flex items-center gap-2 px-3 py-2 rounded-lg" style="background:rgba(212,175,55,0.07);border:1px solid rgba(212,175,55,0.15)">
                  <div class="w-2 h-2 rounded-full" style="background:#22c55e;box-shadow:0 0 5px #22c55e"></div>
                  <span class="text-[10px] text-white/60">{{ t('bms.app_status') }}</span>
                </div>
              </div>
              <!-- Phone notch -->
              <div class="bms-phone-notch"></div>
            </div>
          </div>

          <!-- RIGHT -->
          <div class="space-y-10">
            <div v-for="f in featRight" :key="f.title" class="bms-feat-card bms-feat-card--right">
              <div class="bms-feat-label">{{ f.label }}</div>
              <div class="bms-feat-title">{{ f.title }}</div>
              <div class="bms-feat-desc">{{ f.desc }}</div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- ═══ 3. 6 CAPABILITY CARDS ═══ -->
    <section class="py-28" style="background:#040d1e">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-14">

        <div class="text-center mb-16">
          <div class="flex items-center justify-center gap-3 mb-5">
            <div class="h-px w-12" style="background:linear-gradient(to right,transparent,#D4AF37)"></div>
            <span class="text-[10px] font-bold uppercase tracking-[0.5em]" style="color:rgba(212,175,55,0.6)">{{ t('bms.cap_badge') }}</span>
            <div class="h-px w-12" style="background:linear-gradient(to left,transparent,#D4AF37)"></div>
          </div>
          <h2 class="bms-h2 text-white mb-4">{{ t('bms.cap_title') }}</h2>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="(cap, i) in capabilities" :key="cap.title"
            class="bms-cap-card group">
            <div class="bms-cap-num">{{ String(i+1).padStart(2,'0') }}</div>
            <div class="bms-cap-icon-wrap">
              <svg class="w-6 h-6" style="color:#D4AF37" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" :d="cap.icon"/>
              </svg>
            </div>
            <h3 class="bms-cap-title">{{ cap.title }}</h3>
            <p class="bms-cap-desc">{{ cap.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ 4. CTA FINAL ═══ -->
    <section class="py-24 text-center relative overflow-hidden" style="background:#061228">
      <div class="absolute inset-0 pointer-events-none" style="background:radial-gradient(ellipse at center,rgba(212,175,55,0.07) 0%,transparent 65%)"></div>
      <div class="relative z-10 max-w-2xl mx-auto px-6">
        <div class="flex items-center justify-center gap-3 mb-6">
          <div class="h-px w-12" style="background:linear-gradient(to right,transparent,#D4AF37)"></div>
          <span class="text-[10px] font-bold uppercase tracking-[0.5em]" style="color:rgba(212,175,55,0.6)">BMS · QUDRAT</span>
          <div class="h-px w-12" style="background:linear-gradient(to left,transparent,#D4AF37)"></div>
        </div>
        <h2 class="bms-h2 text-white mb-5">{{ t('bms.final_title') }}</h2>
        <p class="text-white/40 mb-10" style="font-size:15px">{{ t('bms.final_sub') }}</p>
        <Link :href="route('contacts')"
          class="inline-flex items-center gap-2 font-bold text-[11px] uppercase tracking-widest px-10 py-4 rounded transition-all hover:opacity-90"
          style="background:#D4AF37;color:#061228">
          {{ t('bms.cta_consult') }}
          <Icon name="arrow-right" class="w-4 h-4" :stroke-width="2" />
        </Link>
      </div>
    </section>

  </MainLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import Icon from '@/Components/Icon.vue';
import { useAsset } from '@/composables/useAsset';
import { useTrans } from '@/composables/useTrans';

const { asset } = useAsset();
const { t } = useTrans();

const heroPills = computed(() => [
  t('bms.pill1'), t('bms.pill2'), t('bms.pill3'),
  t('bms.pill4'), t('bms.pill5'), t('bms.pill6'),
  t('bms.pill7'), t('bms.pill8'),
]);

const featLeft = computed(() => [
  { label: 'SMART ACCESS', title: t('bms.fl1_t'), desc: t('bms.fl1_d') },
  { label: 'AI CCTV', title: t('bms.fl2_t'), desc: t('bms.fl2_d') },
  { label: 'FIRE SAFETY', title: t('bms.fl3_t'), desc: t('bms.fl3_d') },
]);

const featRight = computed(() => [
  { label: 'BMS', title: t('bms.fr1_t'), desc: t('bms.fr1_d') },
  { label: 'SMART NOTIFICATIONS', title: t('bms.fr2_t'), desc: t('bms.fr2_d') },
  { label: 'ONLINE SERVICES', title: t('bms.fr3_t'), desc: t('bms.fr3_d') },
]);

const appItems = [
  { l: t('bms.app_cameras'), icon: 'M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z' },
  { l: t('bms.app_access'), icon: 'M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z' },
  { l: t('bms.app_guests'), icon: 'M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z' },
  { l: t('bms.app_pay'), icon: 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z' },
];

const capabilities = computed(() => [
  {
    icon: 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z',
    title: t('bms.cap1_t'), desc: t('bms.cap1_d'),
  },
  {
    icon: 'M6 13.5V3.75m0 9.75a1.5 1.5 0 0 1 0 3m0-3a1.5 1.5 0 0 0 0 3m0 3.75V16.5m12-3V3.75m0 9.75a1.5 1.5 0 0 1 0 3m0-3a1.5 1.5 0 0 0 0 3m0 3.75V16.5m-6-9V3.75m0 3.75a1.5 1.5 0 0 1 0 3m0-3a1.5 1.5 0 0 0 0 3m0 9.75V10.5',
    title: t('bms.cap2_t'), desc: t('bms.cap2_d'),
  },
  {
    icon: 'M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z',
    title: t('bms.cap3_t'), desc: t('bms.cap3_d'),
  },
  {
    icon: 'M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3',
    title: t('bms.cap4_t'), desc: t('bms.cap4_d'),
  },
  {
    icon: 'M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z',
    title: t('bms.cap5_t'), desc: t('bms.cap5_d'),
  },
  {
    icon: 'M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z',
    title: t('bms.cap6_t'), desc: t('bms.cap6_d'),
  },
]);
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,400&display=swap');

/* ── HERO ── */
.bms-hero { min-height: 100vh; display:flex; align-items:center; }
.bms-hero-inner { width:100%; }

.bms-h1 {
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: clamp(36px, 5.5vw, 82px);
  font-weight: 700; line-height: 0.92; letter-spacing: -0.02em;
}
.bms-h2 {
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: clamp(28px, 3.5vw, 52px);
  font-weight: 700; letter-spacing: -0.015em; line-height: 1.05;
}

/* ── FEATURE CARDS ── */
.bms-feat-card {
  position: relative;
  padding-left: 20px;
  border-left: 2px solid rgba(212,175,55,0.2);
}
.bms-feat-card--right {
  padding-left: 0;
  padding-right: 20px;
  border-left: none;
  border-right: 2px solid rgba(212,175,55,0.2);
  text-align: right;
}
.bms-feat-label {
  font-family: 'Inter', sans-serif;
  font-size: 9px; font-weight: 700;
  letter-spacing: 0.35em; color: #D4AF37;
  text-transform: uppercase; margin-bottom: 6px;
}
.bms-feat-title {
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: clamp(16px, 1.4vw, 20px);
  font-weight: 700; color: #fff;
  margin-bottom: 6px; letter-spacing: 0.01em;
}
.bms-feat-desc {
  font-family: 'Inter', sans-serif;
  font-size: 12px; color: rgba(255,255,255,0.38);
  line-height: 1.6;
}

/* ── PHONE MOCKUP ── */
.bms-phone {
  position: relative;
  width: 220px; height: 420px;
  background: linear-gradient(160deg,#0c1a30,#061228);
  border-radius: 28px;
  border: 1px solid rgba(212,175,55,0.25);
  box-shadow: 0 0 60px rgba(212,175,55,0.1), 0 30px 80px rgba(0,0,0,0.6);
  padding: 40px 16px 20px;
  overflow: hidden;
}
.bms-phone-notch {
  position: absolute; top: 12px; left: 50%; transform: translateX(-50%);
  width: 60px; height: 8px;
  background: rgba(212,175,55,0.2);
  border-radius: 4px;
}
.bms-phone-screen { height: 100%; display: flex; flex-direction: column; }

.bms-app-item {
  display: flex; flex-direction: column; align-items: center;
  padding: 10px 8px; border-radius: 10px;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.06);
  font-family: 'Inter', sans-serif;
  font-size: 9px; color: rgba(255,255,255,0.5);
  text-align: center;
}

/* ── CAPABILITY CARDS ── */
.bms-cap-card {
  position: relative;
  padding: 32px 28px;
  border-radius: 4px;
  background: rgba(255,255,255,0.02);
  border: 1px solid rgba(212,175,55,0.1);
  transition: border-color 0.3s, background 0.3s, transform 0.3s;
  overflow: hidden;
}
.bms-cap-card:hover {
  border-color: rgba(212,175,55,0.3);
  background: rgba(212,175,55,0.03);
  transform: translateY(-4px);
}
.bms-cap-card::before {
  content: '';
  position: absolute; top: 0; left: 0;
  width: 3px; height: 0;
  background: #D4AF37;
  transition: height 0.35s;
}
.bms-cap-card:hover::before { height: 100%; }

.bms-cap-num {
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: 48px; font-weight: 300;
  color: rgba(212,175,55,0.08);
  line-height: 1; position: absolute;
  top: 16px; right: 24px;
  letter-spacing: -0.04em;
}
.bms-cap-icon-wrap {
  width: 44px; height: 44px;
  border-radius: 10px;
  background: rgba(212,175,55,0.08);
  border: 1px solid rgba(212,175,55,0.18);
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 18px;
}
.bms-cap-title {
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: clamp(16px,1.4vw,20px);
  font-weight: 700; color: #fff;
  margin-bottom: 10px; letter-spacing: 0.01em;
  line-height: 1.2;
}
.bms-cap-desc {
  font-family: 'Inter', sans-serif;
  font-size: 13px; color: rgba(255,255,255,0.35);
  line-height: 1.65;
}
</style>
