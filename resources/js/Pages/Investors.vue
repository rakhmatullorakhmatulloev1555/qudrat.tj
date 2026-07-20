<template>
  <MainLayout>
    <SeoHead
      :title="t('investors.meta_title')"
      :ogTitle="t('investors.meta_title')"
      :description="t('investors.meta_desc')"
      path="/investors"
    />

    <!-- ═══ 1. HERO ═══ -->
    <section class="inv-hero relative overflow-hidden flex items-center">

      <!-- Background photo -->
      <div class="absolute inset-0">
        <img :src="asset('/images/hero-4.jpg')" alt="" class="w-full h-full object-cover"
             style="opacity:0.58; object-position:65% center"/>
        <div class="absolute inset-0"
          style="background:linear-gradient(to right,rgba(7,11,22,1) 0%,rgba(7,11,22,0.82) 48%,rgba(7,11,22,0.18) 100%)"></div>
      </div>

      <!-- Ambient glow gold -->
      <div class="absolute pointer-events-none"
        style="width:900px;height:700px;top:50%;left:65%;transform:translate(-50%,-50%);
               background:radial-gradient(ellipse,rgba(201,169,110,0.09) 0%,transparent 65%);
               border-radius:50%"></div>

      <!-- Main content -->
      <div class="relative z-10 max-w-[1400px] mx-auto px-6 lg:px-12 w-full">
        <div class="grid lg:grid-cols-[1fr_420px] gap-10 lg:gap-16 items-center">

          <!-- Left: text -->
          <div>
            <!-- Badge -->
            <div class="inline-flex items-center gap-3 mb-8">
              <div class="h-px w-10" style="background:linear-gradient(to right,transparent,rgba(201,169,110,0.6))"></div>
              <span class="text-gold/70 text-[9px] font-bold uppercase tracking-[0.55em]">{{ t('investors.badge') }}</span>
              <div class="h-px w-10" style="background:linear-gradient(to left,transparent,rgba(201,169,110,0.6))"></div>
            </div>

            <!-- H1 -->
            <h1 class="font-bold text-white uppercase leading-[0.9] mb-8"
              style="font-family:'Cormorant Garamond',Georgia,serif; font-size:clamp(40px,5.8vw,88px); letter-spacing:0.02em">
              {{ t('investors.hero_pre') }}<br>
              <span class="text-gold">{{ t('investors.hero_hl') }}</span>
            </h1>

            <p class="text-white/55 text-base lg:text-lg leading-relaxed max-w-xl mb-10">
              {{ t('investors.hero_subtitle') }}
            </p>

            <!-- CTA buttons -->
            <div class="flex flex-wrap gap-4">
              <a href="/contacts"
                class="inline-flex items-center gap-2 px-8 py-4 rounded-xl font-bold text-sm uppercase tracking-wider transition-all hover:opacity-90"
                style="background:#C9A96E;color:#080D18">
                {{ t('investors.cta_investor') }}
                <Icon name="arrow-right" class="w-4 h-4" :stroke-width="2.5" />
              </a>
              <a href="#"
                class="inline-flex items-center gap-2 px-8 py-4 rounded-xl font-bold text-sm uppercase tracking-wider border transition-all hover:bg-white/5"
                style="border-color:rgba(201,169,110,0.3);color:rgba(201,169,110,0.8)">
                <Icon name="arrow-down-tray" class="w-4 h-4" :stroke-width="2" />
                {{ t('investors.cta_download') }}
              </a>
            </div>
          </div>

          <!-- Right: floating "Why QUDRAT" card -->
          <div class="hidden lg:block">
            <div class="rounded-2xl border border-gold/20 p-7"
              style="background:rgba(7,11,22,0.82); backdrop-filter:blur(12px)">
              <div class="text-gold/80 text-[9px] font-bold uppercase tracking-[0.35em] mb-6 leading-relaxed text-center">
                {{ t('investors.why_card_title') }}
              </div>
              <div class="divide-y divide-white/5">
                <div v-for="item in whyItems" :key="item.title"
                  class="flex items-center gap-4 py-4">
                  <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                    style="background:rgba(201,169,110,0.08)">
                    <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor"
                      stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" :d="INV_ICONS[item.icon]" /></svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="text-white font-bold text-sm uppercase tracking-wide">{{ item.title }}</div>
                    <div class="text-white/45 text-xs mt-0.5">{{ item.text }}</div>
                  </div>
                  <div class="text-gold font-black text-xl flex-shrink-0">{{ item.val }}</div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Bottom fade -->
      <div class="absolute bottom-0 inset-x-0 h-24 pointer-events-none"
        style="background:linear-gradient(to top,#080D18,transparent)"></div>
    </section>

    <!-- ═══ 2. INVESTMENT OPTIONS ═══ -->
    <section class="py-28" style="background:#070B16">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <div class="text-center mb-16">
          <div class="inline-flex items-center gap-3 mb-6">
            <div class="h-px w-10" style="background:linear-gradient(to right,transparent,rgba(201,169,110,0.5))"></div>
            <span class="text-gold/70 text-[9px] font-bold uppercase tracking-[0.55em]">{{ t('investors.how_badge') }}</span>
            <div class="h-px w-10" style="background:linear-gradient(to left,transparent,rgba(201,169,110,0.5))"></div>
          </div>
          <h2 class="font-bold text-white leading-tight mb-4"
            style="font-size:clamp(22px,2.8vw,42px)">
            {{ t('investors.opts_title') }}
          </h2>
          <p class="text-white/55 text-base max-w-xl mx-auto">{{ t('investors.opts_subtitle') }}</p>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
          <div v-for="(opt, i) in investOptions" :key="i"
            class="inv-opt-card relative rounded-2xl overflow-hidden border transition-all duration-500 group"
            :class="opt.featured ? 'border-gold/25' : 'border-white/6'"
            :style="opt.featured
              ? 'background:linear-gradient(160deg,#131C14,#0B180D)'
              : 'background:#0C1220'">

            <!-- Top colour stripe -->
            <div class="h-1 w-full" :style="`background:${opt.color}`"></div>

            <!-- Featured badge -->
            <div v-if="opt.featured" class="absolute top-6 right-6">
              <span class="text-[10px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-full"
                style="background:rgba(201,169,110,0.12);color:#C9A96E;border:1px solid rgba(201,169,110,0.3)">
                {{ t('investors.popular_badge') }}
              </span>
            </div>

            <div class="p-8">
              <!-- Icon + title -->
              <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                  :style="`background:${opt.color}18`">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" :style="`color:${opt.color}`"><path stroke-linecap="round" stroke-linejoin="round" :d="INV_ICONS[opt.icon]" /></svg>
                </div>
                <div>
                  <div class="text-white font-bold text-lg leading-tight">{{ opt.title }}</div>
                  <div class="font-black text-2xl mt-1"
                    :style="`color:${opt.color}`">
                    {{ opt.return }}
                  </div>
                </div>
              </div>

              <p class="text-white/55 text-sm leading-relaxed mb-6">{{ opt.desc }}</p>

              <ul class="space-y-3 mb-8">
                <li v-for="f in opt.features" :key="f" class="flex items-start gap-3 text-sm text-white/60">
                  <Icon name="check" class="w-4 h-4 flex-shrink-0 mt-0.5" :stroke-width="2.5" />
                  {{ f }}
                </li>
              </ul>

              <a href="/contacts"
                class="block text-center py-3 rounded-xl font-bold text-sm uppercase tracking-wider transition-all"
                :style="opt.featured
                  ? 'background:#C9A96E;color:#080D18'
                  : `border:1px solid ${opt.color}50;color:${opt.color}`">
                {{ t('investors.learn_more') }} →
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ 3. PROCESS — HOW TO INVEST ═══ -->
    <section class="py-24 border-y border-white/5" style="background:#0C1220">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <div class="text-center mb-16">
          <div class="inline-flex items-center gap-3 mb-6">
            <div class="h-px w-10" style="background:linear-gradient(to right,transparent,rgba(201,169,110,0.5))"></div>
            <span class="text-gold/70 text-[9px] font-bold uppercase tracking-[0.55em]">{{ t('investors.process_badge') }}</span>
            <div class="h-px w-10" style="background:linear-gradient(to left,transparent,rgba(201,169,110,0.5))"></div>
          </div>
          <h2 class="font-bold text-white leading-tight mb-4"
            style="font-size:clamp(22px,2.8vw,42px)">
            {{ t('investors.process_title') }}<br>
            <span class="text-gold">{{ t('investors.process_title_hl') }}</span>
          </h2>
          <p class="text-white/55 text-base max-w-lg mx-auto">{{ t('investors.process_sub') }}</p>
        </div>

        <!-- Steps grid -->
        <div class="grid lg:grid-cols-4 gap-5 relative">
          <!-- Connector line (desktop) -->
          <div class="hidden lg:block absolute h-px pointer-events-none"
            style="top:44px;left:calc(12.5% + 28px);right:calc(12.5% + 28px);
                   background:linear-gradient(90deg,transparent,rgba(201,169,110,0.25),rgba(201,169,110,0.25),transparent)">
          </div>

          <div v-for="step in processSteps" :key="step.num"
            class="relative text-center rounded-2xl p-8 border border-white/5 hover:border-gold/20 transition-all duration-300 group"
            style="background:#070B16">
            <!-- Hover glow -->
            <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"
              style="background:radial-gradient(ellipse at 50% 0%,rgba(201,169,110,0.04),transparent 70%)"></div>
            <!-- Number circle -->
            <div class="w-[56px] h-[56px] rounded-full border border-gold/25 flex items-center justify-center mx-auto mb-6 relative z-10"
              style="background:rgba(201,169,110,0.06)">
              <span class="font-black text-xl text-gold">{{ step.num }}</span>
            </div>
            <h4 class="text-white font-bold text-base mb-3 relative z-10">{{ step.title }}</h4>
            <p class="text-white/55 text-sm leading-relaxed relative z-10">{{ step.desc }}</p>
          </div>
        </div>

        <!-- CTA under process -->
        <div class="text-center mt-12">
          <a href="/contacts"
            class="inline-flex items-center gap-2 px-8 py-3 rounded-xl border font-semibold text-sm transition-all hover:bg-white/5"
            style="border-color:rgba(201,169,110,0.25);color:rgba(201,169,110,0.8)">
            {{ t('investors.cta_btn') }} →
          </a>
        </div>
      </div>
    </section>

    <!-- ═══ 4. WHY QUDRAT + DOCS ═══ -->
    <section class="py-24" style="background:#070B16">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-14 items-start">

          <!-- Left: reasons -->
          <div>
            <div class="inline-flex items-center gap-3 mb-6">
              <div class="h-px w-10" style="background:linear-gradient(to right,transparent,rgba(201,169,110,0.5))"></div>
              <span class="text-gold/70 text-[9px] font-bold uppercase tracking-[0.55em]">{{ t('investors.why_badge') }}</span>
              <div class="h-px w-10" style="background:linear-gradient(to left,transparent,rgba(201,169,110,0.5))"></div>
            </div>
            <h2 class="font-bold text-white leading-tight mb-10"
              style="font-size:clamp(20px,2.5vw,38px)">
              {{ t('investors.why_title') }}<br>
              <span class="text-gold">{{ t('investors.why_title2') }}</span>
            </h2>
            <div class="space-y-4">
              <div v-for="reason in reasons" :key="reason.title"
                class="flex gap-4 p-5 rounded-xl border border-white/5 hover:border-gold/15 transition-all group"
                style="background:#0C1220">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                  style="background:rgba(201,169,110,0.08)">
                  <svg class="w-5 h-5 text-gold/80" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" :d="INV_ICONS[reason.icon]" /></svg>
                </div>
                <div>
                  <h4 class="text-white font-semibold mb-1 text-sm group-hover:text-gold transition-colors">{{ reason.title }}</h4>
                  <p class="text-white/55 text-sm leading-relaxed">{{ reason.text }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Right: docs panel -->
          <div class="rounded-2xl border border-white/6 overflow-hidden sticky top-28" style="background:#0C1220">
            <div class="px-6 py-5 border-b border-white/5">
              <h3 class="text-white font-bold">{{ t('investors.docs_title') }}</h3>
            </div>
            <div class="p-5 space-y-3">
              <div v-for="doc in docs" :key="doc.name"
                class="flex items-center gap-4 p-4 rounded-xl border border-white/5 hover:border-gold/20 transition-all cursor-pointer group"
                style="background:#070B16">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                  style="background:rgba(201,169,110,0.08)">
                  <svg class="w-5 h-5 text-gold/80" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" :d="INV_ICONS[doc.icon]" /></svg>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="text-white text-sm font-medium group-hover:text-gold transition-colors truncate">{{ doc.name }}</div>
                  <div class="text-white/30 text-xs mt-0.5">{{ doc.size }}</div>
                </div>
                <svg class="w-4 h-4 text-white/20 group-hover:text-gold transition-colors flex-shrink-0"
                  fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
              </div>
            </div>
            <div class="px-5 pb-5">
              <p class="text-white/25 text-xs px-1">{{ t('investors.docs_note') }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ 5. GUARANTEES ═══ -->
    <section class="py-24 border-y border-white/5" style="background:#0C1220">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <div class="text-center mb-16">
          <div class="inline-flex items-center gap-3 mb-6">
            <div class="h-px w-10" style="background:linear-gradient(to right,transparent,rgba(201,169,110,0.5))"></div>
            <span class="text-gold/70 text-[9px] font-bold uppercase tracking-[0.55em]">{{ t('investors.guar_badge') }}</span>
            <div class="h-px w-10" style="background:linear-gradient(to left,transparent,rgba(201,169,110,0.5))"></div>
          </div>
          <h2 class="font-bold text-white leading-tight mb-4"
            style="font-size:clamp(22px,2.8vw,42px)">
            {{ t('investors.guar_title') }}<br>
            <span class="text-gold">{{ t('investors.guar_title_hl') }}</span>
          </h2>
          <p class="text-white/55 text-base max-w-lg mx-auto">{{ t('investors.guar_sub') }}</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5">
          <div v-for="(g, i) in guarantees" :key="i"
            class="relative rounded-2xl p-7 border border-white/5 hover:border-gold/20 transition-all duration-300 group overflow-hidden"
            style="background:#070B16">
            <!-- Gold top stripe -->
            <div class="absolute top-0 left-0 right-0 h-0.5"
              style="background:linear-gradient(90deg,#C9A96E,rgba(201,169,110,0.1),transparent)"></div>
            <!-- Watermark number -->
            <div class="absolute -top-2 -right-1 select-none pointer-events-none font-black leading-none text-gold/[0.05]"
              style="font-size:90px">{{ i + 1 }}</div>
            <!-- Icon -->
            <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-5 relative z-10"
              style="background:rgba(201,169,110,0.08)">
              <svg class="w-6 h-6 text-gold/80" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" :d="INV_ICONS[g.icon]" /></svg>
            </div>
            <h4 class="text-white font-bold text-base mb-3 relative z-10">{{ g.title }}</h4>
            <p class="text-white/55 text-sm leading-relaxed relative z-10">{{ g.text }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ 6. INVESTMENT CALCULATOR ═══ -->
    <section id="calculator" class="py-28 border-b border-white/5" style="background:#070B16">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="text-center mb-14">
          <div class="inline-flex items-center gap-4 mb-6">
            <div class="h-px w-10" style="background:linear-gradient(to right,transparent,rgba(201,169,110,0.5))"></div>
            <span class="text-gold/70 text-[9px] font-bold uppercase tracking-[0.4em]">{{ t('investors.calc_badge') }}</span>
            <div class="h-px w-10" style="background:linear-gradient(to left,transparent,rgba(201,169,110,0.5))"></div>
          </div>
          <h2 class="font-bold text-white leading-tight mb-3"
            style="font-size:clamp(22px,2.8vw,42px)">
            {{ t('investors.calc_heading') }}
          </h2>
          <p class="text-white/40 text-base">{{ t('investors.calc_subtitle') }}</p>
        </div>

        <div class="grid lg:grid-cols-2 gap-8 items-start">

          <!-- Controls -->
          <div class="rounded-2xl p-8 border border-white/6" style="background:#0C1220">
            <!-- Investment type -->
            <div class="mb-8">
              <label class="calc-label">{{ t('investors.calc_type_label') }}</label>
              <div class="grid grid-cols-3 gap-2 mt-2">
                <button v-for="ctype in calcTypes" :key="ctype.key"
                  @click="calc.type = ctype.key"
                  class="py-3 px-2 rounded-xl text-xs font-semibold uppercase tracking-wider transition-all duration-200 border text-center"
                  :style="calc.type === ctype.key
                    ? 'background:#C9A96E;color:#080D18;border-color:#C9A96E'
                    : 'background:transparent;color:rgba(255,255,255,0.4);border-color:rgba(255,255,255,0.08)'">
                  {{ ctype.label }}
                </button>
              </div>
            </div>
            <!-- Amount slider -->
            <div class="mb-8">
              <div class="flex items-center justify-between mb-3">
                <label class="calc-label">{{ t('investors.calc_amount_label') }}</label>
                <span class="text-gold font-bold text-lg">${{ Number(calc.amount).toLocaleString() }}</span>
              </div>
              <input type="range" v-model="calc.amount" min="10000" max="500000" step="5000" class="calc-range w-full"/>
              <div class="flex justify-between text-white/25 text-[10px] mt-1"><span>$10 000</span><span>$500 000</span></div>
            </div>
            <!-- Duration -->
            <div class="mb-8">
              <label class="calc-label">{{ t('investors.calc_period_label') }}</label>
              <div class="grid grid-cols-4 gap-2 mt-2">
                <button v-for="y in [1,2,3,5]" :key="y"
                  @click="calc.years = y"
                  class="py-3 rounded-xl text-sm font-bold transition-all duration-200 border"
                  :style="calc.years === y
                    ? 'background:#C9A96E;color:#080D18;border-color:#C9A96E'
                    : 'background:transparent;color:rgba(255,255,255,0.4);border-color:rgba(255,255,255,0.08)'">
                  {{ y }} {{ y === 1 ? t('investors.year') : y < 5 ? t('investors.years_few') : t('investors.years') }}
                </button>
              </div>
            </div>
            <!-- Currency -->
            <div>
              <label class="calc-label">{{ t('investors.calc_currency_label') }}</label>
              <div class="grid grid-cols-3 gap-2 mt-2">
                <button v-for="c in ['USD','TJS','RUB']" :key="c"
                  @click="calc.currency = c"
                  class="py-2.5 rounded-xl text-xs font-bold transition-all duration-200 border"
                  :style="calc.currency === c
                    ? 'background:rgba(201,169,110,0.15);color:#C9A96E;border-color:rgba(201,169,110,0.4)'
                    : 'background:transparent;color:rgba(255,255,255,0.35);border-color:rgba(255,255,255,0.08)'">
                  {{ c }}
                </button>
              </div>
            </div>
          </div>

          <!-- Results -->
          <div class="flex flex-col gap-4">
            <!-- Main result -->
            <div class="rounded-2xl p-8 border border-gold/20 relative overflow-hidden"
              style="background:linear-gradient(135deg,#0F1E10,#0C1810)">
              <div class="absolute top-0 left-0 right-0 h-px" style="background:linear-gradient(90deg,transparent,#C9A96E,transparent)"></div>
              <div class="text-white/40 text-[10px] uppercase tracking-[0.3em] font-semibold mb-1">
                {{ t('investors.total_after') }} {{ calc.years }} {{ calc.years === 1 ? t('investors.year') : calc.years < 5 ? t('investors.years_few') : t('investors.years') }}
              </div>
              <div class="font-black text-gold leading-none mb-2"
                style="font-size:clamp(32px,4vw,54px)">
                {{ formatCurrency(calcResult.total) }}
              </div>
              <div class="text-white/50 text-sm">{{ t('investors.invested_label') }} {{ formatCurrency(calcResult.invested) }}</div>
            </div>
            <!-- Breakdown -->
            <div class="grid grid-cols-2 gap-4">
              <div class="rounded-xl p-5 border border-white/5" style="background:#0C1220">
                <div class="text-white/30 text-[10px] uppercase tracking-[0.25em] mb-1">{{ t('investors.net_profit') }}</div>
                <div class="text-emerald-400 font-bold text-2xl">+{{ formatCurrency(calcResult.profit) }}</div>
                <div class="text-white/25 text-xs mt-1">{{ t('investors.all_period') }}</div>
              </div>
              <div class="rounded-xl p-5 border border-white/5" style="background:#0C1220">
                <div class="text-white/30 text-[10px] uppercase tracking-[0.25em] mb-1">{{ t('investors.monthly') }}</div>
                <div class="text-white font-bold text-2xl">{{ formatCurrency(calcResult.monthly) }}</div>
                <div class="text-white/25 text-xs mt-1">{{ t('investors.passive_income') }}</div>
              </div>
              <div class="rounded-xl p-5 border border-white/5" style="background:#0C1220">
                <div class="text-white/30 text-[10px] uppercase tracking-[0.25em] mb-1">{{ t('investors.per_year') }}</div>
                <div class="text-gold font-bold text-2xl">{{ currentType.rate }}%</div>
                <div class="text-white/25 text-xs mt-1">{{ t('investors.avg_return') }}</div>
              </div>
              <div class="rounded-xl p-5 border border-white/5" style="background:#0C1220">
                <div class="text-white/30 text-[10px] uppercase tracking-[0.25em] mb-1">{{ t('investors.capital_growth') }}</div>
                <div class="text-white font-bold text-2xl">×{{ calcResult.multiplier }}</div>
                <div class="text-white/25 text-xs mt-1">{{ t('investors.for_period') }} {{ calc.years }} {{ calc.years === 1 ? t('investors.year') : calc.years < 5 ? t('investors.years_few') : t('investors.years') }}</div>
              </div>
            </div>
            <!-- Progress bar -->
            <div class="rounded-xl p-5 border border-white/5" style="background:#0C1220">
              <div class="flex justify-between text-[10px] text-white/30 uppercase tracking-wider mb-3">
                <span>{{ t('investors.invested_short') }}</span><span>{{ t('investors.profit_short') }}</span>
              </div>
              <div class="h-3 rounded-full overflow-hidden" style="background:rgba(255,255,255,0.05)">
                <div class="h-full rounded-full transition-all duration-700"
                  style="background:linear-gradient(90deg,#C9A96E,#E8D5A3)"
                  :style="`width:${calcResult.profitPct}%`"></div>
              </div>
              <div class="flex justify-between text-xs text-white/40 mt-2">
                <span>{{ formatCurrency(calcResult.invested) }}</span>
                <span class="text-emerald-400">+{{ formatCurrency(calcResult.profit) }}</span>
              </div>
            </div>
            <a :href="route('contacts')"
              class="block text-center py-4 rounded-xl font-bold uppercase tracking-widest text-[12px] transition-all hover:opacity-90"
              style="background:#C9A96E;color:#080D18">
              {{ t('investors.discuss_btn') }}
            </a>
            <p class="text-white/20 text-xs text-center">{{ t('investors.calc_disclaimer') }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ 7. FAQ ═══ -->
    <section class="py-24 border-b border-white/5" style="background:#0C1220">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-[1fr_1.4fr] gap-16 items-start">

          <!-- Left: sticky header -->
          <div class="lg:sticky lg:top-32">
            <div class="inline-flex items-center gap-3 mb-6">
              <div class="h-px w-10" style="background:linear-gradient(to right,transparent,rgba(201,169,110,0.5))"></div>
              <span class="text-gold/70 text-[9px] font-bold uppercase tracking-[0.55em]">{{ t('investors.faq_badge') }}</span>
              <div class="h-px w-10" style="background:linear-gradient(to left,transparent,rgba(201,169,110,0.5))"></div>
            </div>
            <h2 class="font-bold text-white leading-tight mb-6"
              style="font-size:clamp(20px,2.5vw,38px)">
              {{ t('investors.faq_title') }}
            </h2>
            <p class="text-white/55 text-sm leading-relaxed mb-8 max-w-xs">
              {{ t('investors.faq_sub') }}
            </p>
            <a href="/contacts"
              class="inline-flex items-center gap-2 px-7 py-3 rounded-xl font-bold text-sm uppercase tracking-wider transition-all hover:opacity-90"
              style="background:#C9A96E;color:#080D18">
              {{ t('investors.cta_btn') }} →
            </a>
          </div>

          <!-- Right: accordion -->
          <div class="space-y-3">
            <div v-for="(faq, i) in faqs" :key="i"
              class="rounded-xl border overflow-hidden transition-all duration-300"
              :class="openFaq === i ? 'border-gold/20' : 'border-white/6'"
              style="background:#070B16">
              <button
                class="w-full flex items-center justify-between gap-4 px-6 py-5 text-left"
                @click="openFaq = openFaq === i ? null : i">
                <span class="text-white font-semibold text-sm leading-snug">{{ faq.q }}</span>
                <div class="w-7 h-7 rounded-full border flex items-center justify-center flex-shrink-0 transition-all"
                  :style="openFaq === i
                    ? 'border-color:#C9A96E;background:rgba(201,169,110,0.1)'
                    : 'border-color:rgba(255,255,255,0.12)'">
                  <svg class="w-3.5 h-3.5 text-gold transition-transform duration-300"
                    :class="openFaq === i ? 'rotate-45' : ''"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                  </svg>
                </div>
              </button>
              <div v-if="openFaq === i" class="px-6 pb-6 border-t border-white/5 pt-4">
                <p class="text-white/50 text-sm leading-relaxed">{{ faq.a }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ 8. CTA ═══ -->
    <section class="py-24" style="background:#070B16">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="relative rounded-3xl overflow-hidden p-12 lg:p-16 text-center border border-gold/12"
          style="background:linear-gradient(135deg,#0F1B10,#0B1520,#1B100A)">
          <!-- Glow -->
          <div class="absolute inset-0 pointer-events-none"
            style="background:radial-gradient(ellipse at 50% 50%,rgba(201,169,110,0.07) 0%,transparent 65%)"></div>
          <!-- Top line -->
          <div class="absolute top-0 inset-x-0 h-px pointer-events-none"
            style="background:linear-gradient(90deg,transparent,rgba(201,169,110,0.6),transparent)"></div>

          <div class="relative z-10">
            <!-- Badge -->
            <div class="inline-flex items-center gap-3 mb-6">
              <div class="h-px w-10" style="background:linear-gradient(to right,transparent,rgba(201,169,110,0.5))"></div>
              <span class="text-gold/70 text-[9px] font-bold uppercase tracking-[0.55em]">{{ t('investors.cta_badge') }}</span>
              <div class="h-px w-10" style="background:linear-gradient(to left,transparent,rgba(201,169,110,0.5))"></div>
            </div>

            <h2 class="font-bold text-white leading-tight mb-6"
              style="font-size:clamp(24px,3vw,48px)">
              {{ t('investors.cta_title') }}
            </h2>
            <p class="text-white/40 text-lg max-w-xl mx-auto mb-10">{{ t('investors.cta_subtitle') }}</p>

            <!-- Mini stats -->
            <div class="flex flex-wrap justify-center gap-8 mb-10">
              <div v-for="stat in keyStats" :key="stat.label" class="text-center">
                <div class="text-gold font-black text-2xl">{{ stat.value }}</div>
                <div class="text-white/30 text-[10px] uppercase tracking-wider mt-0.5">{{ stat.label }}</div>
              </div>
            </div>

            <div class="flex flex-wrap gap-4 justify-center">
              <a href="/contacts"
                class="px-10 py-4 rounded-xl font-bold text-sm uppercase tracking-wider transition-all hover:opacity-90"
                style="background:#C9A96E;color:#080D18">
                {{ t('investors.apply_btn') }}
              </a>
              <a href="tel:+992000000000"
                class="px-10 py-4 rounded-xl font-bold text-sm uppercase tracking-wider border transition-all hover:bg-white/5"
                style="border-color:rgba(201,169,110,0.3);color:rgba(201,169,110,0.8)">
                {{ t('investors.call_btn') }}
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

  </MainLayout>
</template>

<style scoped>
.inv-hero {
  height: 100vh;
  min-height: 640px;
  background: #070B16;
  padding-top: 90px;
}
.inv-opt-card:hover {
  transform: translateY(-4px);
}
.calc-label {
  display: block;
  font-size: 0.7rem;
  font-weight: 600;
  color: rgba(255,255,255,0.4);
  text-transform: uppercase;
  letter-spacing: 0.1em;
}
.calc-range {
  -webkit-appearance: none;
  height: 4px;
  border-radius: 4px;
  background: rgba(255,255,255,0.08);
  outline: none;
  cursor: pointer;
}
.calc-range::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #C9A96E;
  cursor: pointer;
  border: 3px solid #080D18;
  box-shadow: 0 0 0 2px rgba(201,169,110,0.3);
}
</style>

<script setup>
import { ref, reactive, computed } from 'vue'
import MainLayout from '@/Layouts/MainLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'
import Icon from '@/Components/Icon.vue'
import { useAsset } from '@/composables/useAsset'
import { useTrans } from '@/composables/useTrans'

const { asset } = useAsset()
const { t } = useTrans()

/* ── FAQ accordion ── */
const openFaq = ref(null)

/* ── Investment Calculator ── */
const calcTypes = computed(() => [
  { key: 'apartment',  label: t('investors.calc_apt'),   rate: 12 },
  { key: 'share',      label: t('investors.calc_share'), rate: 18 },
  { key: 'commercial', label: t('investors.calc_comm'),  rate: 15 },
])
const calc = reactive({ type: 'share', amount: 50000, years: 3, currency: 'USD' })
const rates = { USD: 1, TJS: 10.9, RUB: 91 }

const currentType = computed(() => calcTypes.value.find(ct => ct.key === calc.type))

const calcResult = computed(() => {
  const rate    = currentType.value.rate / 100
  const total   = calc.amount * Math.pow(1 + rate, calc.years)
  const profit  = total - calc.amount
  const monthly = profit / (calc.years * 12)
  return {
    invested:   calc.amount,
    total:      Math.round(total),
    profit:     Math.round(profit),
    monthly:    Math.round(monthly),
    multiplier: (total / calc.amount).toFixed(2),
    profitPct:  Math.min(95, Math.round((profit / total) * 100)),
  }
})

function formatCurrency(val) {
  const converted = Math.round(val * rates[calc.currency])
  const sym = { USD: '$', TJS: 'с', RUB: '₽' }[calc.currency]
  const prefix = calc.currency === 'USD' ? sym : ''
  const suffix = calc.currency !== 'USD' ? ` ${sym}` : ''
  return prefix + Number(converted).toLocaleString('ru-RU') + suffix
}

/* ── Key stats ── */
const keyStats = computed(() => [
  { value: t('investors.stat1_v'), label: t('investors.stat1_l') },
  { value: t('investors.stat2_v'), label: t('investors.stat2_l') },
  { value: t('investors.stat3_v'), label: t('investors.stat3_l') },
  { value: t('investors.stat4_v'), label: t('investors.stat4_l') },
])

/* ── SVG Icon map (replaces all emoji) ── */
const INV_ICONS = {
  // Investment options
  home:      'm2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25',
  chart:     'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z',
  commercial:'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z',
  // Reasons
  shield:    'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z',
  analytics: 'M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z',
  star:      'M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z',
  globe:     'M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418',
  // Docs
  docText:   'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z',
  clipboard: 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z',
  libBuild:  'M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z',
  // Guarantees
  lock:      'M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z',
  user:      'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z',
}

/* ── Investment options ── */
const investOptions = computed(() => [
  {
    icon: 'home', color: '#7EAAEF',
    title:    t('investors.opt1_title'),
    return:   t('investors.opt1_return'),
    desc:     t('investors.opt1_desc'),
    features: [t('investors.opt1_f1'), t('investors.opt1_f2'), t('investors.opt1_f3'), t('investors.opt1_f4')],
    featured: false,
  },
  {
    icon: 'chart', color: '#C9A96E',
    title:    t('investors.opt2_title'),
    return:   t('investors.opt2_return'),
    desc:     t('investors.opt2_desc'),
    features: [t('investors.opt2_f1'), t('investors.opt2_f2'), t('investors.opt2_f3'), t('investors.opt2_f4')],
    featured: true,
  },
  {
    icon: 'commercial', color: '#6EAF8E',
    title:    t('investors.opt3_title'),
    return:   t('investors.opt3_return'),
    desc:     t('investors.opt3_desc'),
    features: [t('investors.opt3_f1'), t('investors.opt3_f2'), t('investors.opt3_f3'), t('investors.opt3_f4')],
    featured: false,
  },
])

/* ── Hero "Why" card items ── */
const whyItems = computed(() => [
  { icon: 'shield',    val: t('investors.why1_val'), title: t('investors.why1_title'), text: t('investors.why1_text') },
  { icon: 'chart',     val: t('investors.why2_val'), title: t('investors.why2_title'), text: t('investors.why2_text') },
  { icon: 'analytics', val: t('investors.why3_val'), title: t('investors.why3_title'), text: t('investors.why3_text') },
  { icon: 'user',      val: t('investors.why4_val'), title: t('investors.why4_title'), text: t('investors.why4_text') },
])

/* ── Why QUDRAT reasons ── */
const reasons = computed(() => [
  { icon: 'shield',    title: t('investors.reason1_title'), text: t('investors.reason1_text') },
  { icon: 'analytics', title: t('investors.reason2_title'), text: t('investors.reason2_text') },
  { icon: 'star',      title: t('investors.reason3_title'), text: t('investors.reason3_text') },
  { icon: 'globe',     title: t('investors.reason4_title'), text: t('investors.reason4_text') },
])

/* ── Documents ── */
const docs = computed(() => [
  { icon: 'docText',  name: t('investors.doc1_name'), size: t('investors.doc1_size') },
  { icon: 'clipboard',name: t('investors.doc2_name'), size: t('investors.doc2_size') },
  { icon: 'libBuild', name: t('investors.doc3_name'), size: t('investors.doc3_size') },
])

/* ── Process steps ── */
const processSteps = computed(() => [
  { num: '01', title: t('investors.step1_title'), desc: t('investors.step1_desc') },
  { num: '02', title: t('investors.step2_title'), desc: t('investors.step2_desc') },
  { num: '03', title: t('investors.step3_title'), desc: t('investors.step3_desc') },
  { num: '04', title: t('investors.step4_title'), desc: t('investors.step4_desc') },
])

/* ── Guarantees ── */
const guarantees = computed(() => [
  { icon: 'docText',   title: t('investors.guar1_title'), text: t('investors.guar1_text') },
  { icon: 'lock',      title: t('investors.guar2_title'), text: t('investors.guar2_text') },
  { icon: 'analytics', title: t('investors.guar3_title'), text: t('investors.guar3_text') },
  { icon: 'user',      title: t('investors.guar4_title'), text: t('investors.guar4_text') },
])

/* ── FAQ ── */
const faqs = computed(() => [
  { q: t('investors.q1'), a: t('investors.a1') },
  { q: t('investors.q2'), a: t('investors.a2') },
  { q: t('investors.q3'), a: t('investors.a3') },
  { q: t('investors.q4'), a: t('investors.a4') },
  { q: t('investors.q5'), a: t('investors.a5') },
])
</script>
