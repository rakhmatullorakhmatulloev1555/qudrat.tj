<template>
  <MainLayout>
    <SeoHead
      :title="t('mining.meta_title')"
      :ogTitle="t('mining.meta_title')"
      :description="t('mining.meta_desc')"
      path="/mining"
    />

    <!-- ═══ HERO ═══ -->
    <section class="relative flex flex-col items-center justify-center overflow-hidden" style="height:100vh; min-height:600px">
      <!-- Background mining photo -->
      <div class="absolute inset-0 z-[0]">
        <img :src="'/images/mining.webp'" class="w-full h-full object-cover object-center" alt="Mining" style="filter:brightness(0.38) saturate(0.75)"/>
      </div>
      <!-- Dark gradient overlay for readability -->
      <div class="absolute inset-0 z-[1]" style="background:linear-gradient(to bottom, rgba(10,8,0,0.45) 0%, rgba(10,8,0,0.25) 45%, rgba(10,8,0,0.72) 100%)"></div>
      <!-- Ambient glow -->
      <div class="absolute inset-0 pointer-events-none overflow-hidden z-[2]">
        <div class="absolute w-96 h-96 rounded-full opacity-10" style="background:radial-gradient(circle,#C9A96E,transparent); top:-8%; left:-5%"></div>
        <div class="absolute w-80 h-80 rounded-full opacity-6" style="background:radial-gradient(circle,#C9A96E,transparent); bottom:10%; right:5%"></div>
      </div>
      <!-- Bottom fade -->
      <div class="absolute bottom-0 left-0 right-0 h-40 z-[2]" style="background:linear-gradient(to bottom,transparent,#0A0800)"></div>

      <div class="relative z-10 text-center px-6 flex flex-col items-center">
        <!-- Icon -->
        <div class="mb-8 w-20 h-20 rounded-full border border-gold/30 flex items-center justify-center" style="background:rgba(201,169,110,0.06)">
          <svg class="w-10 h-10 text-gold" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125"/>
          </svg>
        </div>

        <p class="text-gold text-[10px] font-bold tracking-[0.45em] uppercase mb-6 opacity-70">
          QUDRAT · {{ t('mining.badge').toUpperCase() }}
        </p>

        <h1 class="font-bold leading-[0.9] text-white mb-6 uppercase"
          style="font-family:'Cormorant Garamond',Georgia,serif; font-size:clamp(40px,5.8vw,88px); letter-spacing:0.02em; text-shadow:0 4px 60px rgba(201,169,110,0.2)">
          {{ t('mining.type').toUpperCase() }}
        </h1>

        <div class="w-16 h-px bg-gold/40 mb-6"></div>

        <p class="text-white/50 text-sm lg:text-base font-light tracking-[0.15em] uppercase">
          {{ t('mining.subtitle') }}
        </p>
        <p class="text-white/30 text-xs tracking-widest uppercase mt-2">
          {{ t('mining.hero_place') }}
        </p>
      </div>

      <!-- Scroll arrow -->
      <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-10 flex flex-col items-center gap-2">
        <div class="w-px h-10 bg-gradient-to-b from-gold/40 to-transparent"></div>
        <Icon name="chevron-down" class="w-5 h-5 text-gold/50 animate-bounce" :stroke-width="1.5" />
      </div>
    </section>

    <!-- ═══ STATS BAR ═══ -->
    <div class="border-y border-gold/15 py-8" style="background:#0D0B07">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="grid grid-cols-2 lg:grid-cols-5 divide-x divide-white/5">
          <div v-for="stat in miningStats" :key="stat.label"
            class="text-center px-6 py-4 group cursor-default">
            <div class="font-black text-white group-hover:text-gold transition-colors duration-300 leading-none mb-2"
              style="font-size:clamp(26px,3.5vw,48px)">
              {{ stat.value }}
            </div>
            <div class="text-gold/50 text-[10px] font-semibold uppercase tracking-[0.3em] mb-1">{{ stat.unit }}</div>
            <div class="text-white/25 text-xs">{{ stat.label }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- ═══ О ДОБЫЧЕ — split ═══ -->
    <section class="py-28 overflow-hidden" style="background:#0A0800">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-16 items-center">

          <!-- Left: content -->
          <div>
            <div class="flex items-center gap-3 mb-6">
              <div class="w-8 h-px bg-gold"></div>
              <span class="text-gold text-[10px] font-bold uppercase tracking-[0.4em]">{{ t('mining.about_badge') }}</span>
            </div>
            <h2 class="font-bold leading-tight text-white mb-8"
              style="font-size:clamp(32px,4vw,52px)">
              {{ t('mining.about_title') }}<br>
              <span class="text-gold">{{ t('mining.about_title_hl') }}</span>
            </h2>
            <p class="text-white/50 leading-relaxed mb-6 text-base">
              {{ t('mining.about_p1') }}
            </p>
            <p class="text-white/40 leading-relaxed mb-10 text-sm">
              {{ t('mining.about_p2') }}
            </p>

            <!-- Key points -->
            <div class="space-y-4">
              <div v-for="point in keyPoints" :key="point.title"
                class="flex items-start gap-4 group">
                <div class="w-8 h-8 rounded-lg border border-gold/20 bg-gold/5 flex items-center justify-center flex-shrink-0 mt-0.5 group-hover:bg-gold/15 transition-colors">
                  <div class="w-1.5 h-1.5 rounded-full bg-gold"></div>
                </div>
                <div>
                  <div class="text-white font-semibold text-sm mb-1">{{ point.title }}</div>
                  <div class="text-white/35 text-xs leading-relaxed">{{ point.desc }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right: visual card grid -->
          <div class="grid grid-cols-2 gap-4">
            <div class="rounded-2xl p-7 border border-white/5 col-span-2" style="background:#12100E">
              <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-xl bg-gold/10 border border-gold/20 flex items-center justify-center">
                  <Icon name="map-pin" class="w-5 h-5 text-gold" :stroke-width="1.5" />
                </div>
                <div>
                  <div class="text-white font-semibold text-sm">{{ t('mining.loc_title') }}</div>
                  <div class="text-white/35 text-xs">{{ t('mining.loc_sub') }}</div>
                </div>
              </div>
              <p class="text-white/40 text-xs leading-relaxed">
                {{ t('mining.loc_desc') }}
              </p>
            </div>
            <div v-for="card in miningCards" :key="card.title"
              class="rounded-2xl p-6 border border-white/5 hover:border-gold/20 transition-colors duration-300 group cursor-default"
              style="background:#12100E">
              <div class="text-gold font-black text-3xl mb-2 leading-none">
                {{ card.value }}
              </div>
              <div class="text-white font-semibold text-sm mb-1">{{ card.title }}</div>
              <div class="text-white/30 text-xs leading-relaxed">{{ card.desc }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ МЕСТОРОЖДЕНИЕ ГУЗН ═══ -->
    <section class="py-24 border-y border-white/5" style="background:#0C0A06">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-16 items-start">

          <!-- Left: deposit info -->
          <div>
            <div class="flex items-center gap-3 mb-6">
              <div class="w-8 h-px bg-gold"></div>
              <span class="text-gold text-[10px] font-bold uppercase tracking-[0.4em]">{{ t('mining.deposit_badge') }}</span>
            </div>
            <h2 class="font-bold leading-tight text-white mb-6"
              style="font-size:clamp(28px,3.5vw,48px)">
              {{ t('mining.deposit_name') }}
            </h2>
            <p class="text-white/50 leading-relaxed mb-4 text-sm">
              {{ t('mining.deposit_p1') }}
            </p>
            <p class="text-white/35 leading-relaxed mb-8 text-sm">
              {{ t('mining.deposit_p2') }}
            </p>

            <!-- Real production stats -->
            <div class="grid grid-cols-2 gap-4 mb-8">
              <div v-for="ps in productionStats" :key="ps.label"
                class="rounded-xl border border-white/6 p-5 group hover:border-gold/25 transition-colors"
                style="background:#12100E">
                <div class="text-gold font-bold mb-1 leading-none"
                  style="font-size:clamp(22px,2.5vw,32px)">
                  {{ ps.value }}
                </div>
                <div class="text-white text-xs font-semibold mb-0.5">{{ ps.label }}</div>
                <div class="text-white/30 text-[10px]">{{ ps.note }}</div>
              </div>
            </div>

            <!-- License block -->
            <div class="rounded-xl border border-gold/20 p-5" style="background:rgba(201,169,110,0.04)">
              <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg bg-gold/10 border border-gold/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                  <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                  </svg>
                </div>
                <div>
                  <div class="text-gold text-[10px] font-bold uppercase tracking-widest mb-1">{{ t('mining.license_badge') }}</div>
                  <div class="text-white font-semibold text-sm mb-1">{{ t('mining.license_serial') }}</div>
                  <div class="text-white/40 text-xs">{{ t('mining.license_date') }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right: license documents gallery -->
          <div>
            <div class="flex items-center gap-3 mb-6">
              <div class="w-8 h-px bg-gold"></div>
              <span class="text-gold text-[10px] font-bold uppercase tracking-[0.4em]">{{ t('mining.docs_badge') }}</span>
            </div>

            <div class="grid grid-cols-3 gap-3 mb-4">
              <div v-for="(doc, i) in licDocs" :key="i"
                class="group relative rounded-xl overflow-hidden border border-white/8 hover:border-gold/40 transition-all cursor-pointer"
                style="aspect-ratio:3/4"
                @click="lightboxImg = doc.src; lightboxOpen = true">
                <!-- Real document image -->
                <img :src="asset(doc.src)" :alt="doc.title" loading="lazy"
                  class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"/>
                <!-- Overlay on hover -->
                <div class="absolute inset-0 bg-[#0A0800]/0 group-hover:bg-[#0A0800]/40 transition-all duration-300 flex items-center justify-center">
                  <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 text-center">
                    <svg class="w-8 h-8 text-gold mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607zM10.5 7.5v6m3-3h-6"/>
                    </svg>
                    <span class="text-gold text-[10px] uppercase tracking-widest font-bold">{{ t('mining.docs_zoom') }}</span>
                  </div>
                </div>
                <!-- Label badge -->
                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-[#0A0800] to-transparent p-3">
                  <div class="text-white/60 text-[9px] uppercase tracking-wider">{{ doc.type }}</div>
                </div>
              </div>
            </div>

            <p class="text-white/25 text-xs text-center">{{ t('mining.docs_hint') }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ ЭКСПОРТНЫЕ НАПРАВЛЕНИЯ ═══ -->
    <section class="py-24" style="background:#0A0800">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="text-center mb-14">
          <div class="flex items-center justify-center gap-4 mb-5">
            <div class="w-10 h-px bg-gold/40"></div>
            <span class="text-gold text-[10px] font-bold uppercase tracking-[0.4em]">{{ t('mining.export_badge') }}</span>
            <div class="w-10 h-px bg-gold/40"></div>
          </div>
          <h2 class="text-3xl lg:text-4xl font-bold text-white mb-3">{{ t('mining.export_title') }}</h2>
          <p class="text-white/40 text-sm max-w-xl mx-auto">
            {{ t('mining.export_sub') }}
          </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-12">
          <div v-for="country in exportCountries" :key="country.name"
            class="group rounded-2xl border border-white/6 p-6 hover:border-gold/30 transition-all duration-300 text-center cursor-default"
            style="background:#12100E">
            <div class="w-12 h-8 rounded-md flex items-center justify-center border border-white/10 text-gold font-bold text-xs tracking-widest mb-4 mx-auto" style="background:#1A1510">{{ country.code }}</div>
            <div class="text-white font-bold text-base mb-1 group-hover:text-gold transition-colors">{{ country.name }}</div>
            <div class="text-white/35 text-xs leading-relaxed mb-3">{{ country.desc }}</div>
            <div class="inline-block bg-gold/10 text-gold text-[9px] font-bold uppercase tracking-wider px-3 py-1 rounded-full">
              {{ country.badge }}
            </div>
          </div>
        </div>

        <!-- Export stats bar -->
        <div class="rounded-2xl border border-white/6 p-8 grid sm:grid-cols-3 gap-8 text-center" style="background:#12100E">
          <div>
            <div class="text-gold font-bold text-3xl mb-1" >102K</div>
            <div class="text-white text-sm font-medium">{{ t('mining.export_st1_l') }}</div>
            <div class="text-white/30 text-xs mt-1">{{ t('mining.export_st1_n') }}</div>
          </div>
          <div class="border-x border-white/5">
            <div class="text-gold font-bold text-3xl mb-1" >50K</div>
            <div class="text-white text-sm font-medium">{{ t('mining.export_st2_l') }}</div>
            <div class="text-white/30 text-xs mt-1">{{ t('mining.export_st2_n') }}</div>
          </div>
          <div>
            <div class="text-gold font-bold text-3xl mb-1" >4+</div>
            <div class="text-white text-sm font-medium">{{ t('mining.export_st3_l') }}</div>
            <div class="text-white/30 text-xs mt-1">{{ t('mining.export_st3_n') }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ ЛОГИСТИКА И МАРШРУТЫ ═══ -->
    <section class="py-24 border-y border-white/5" style="background:#0D0B07">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="text-center mb-14">
          <div class="flex items-center justify-center gap-4 mb-5">
            <div class="w-10 h-px bg-gold/40"></div>
            <span class="text-gold text-[10px] font-bold uppercase tracking-[0.4em]">{{ t('mining.logistic_badge') }}</span>
            <div class="w-10 h-px bg-gold/40"></div>
          </div>
          <h2 class="text-3xl lg:text-4xl font-bold text-white">{{ t('mining.logistic_title') }}</h2>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
          <div v-for="route in logisticsRoutes" :key="route.to"
            class="group rounded-2xl border border-white/6 p-7 hover:border-gold/25 transition-all duration-300"
            style="background:#12100E">
            <!-- Route visual -->
            <div class="flex items-center gap-3 mb-5">
              <div class="w-3 h-3 rounded-full bg-gold flex-shrink-0"></div>
              <div class="flex-1 h-px bg-gradient-to-r from-gold/50 to-gold/10 relative">
                <div class="absolute top-1/2 left-1/2 -translate-y-1/2 -translate-x-1/2 bg-[#0D0B07] px-1">
                  <Icon name="arrow-long-right" class="w-3 h-3 text-gold/50" :stroke-width="2" />
                </div>
              </div>
              <div class="w-3 h-3 rounded-full border-2 border-gold/40 flex-shrink-0"></div>
            </div>

            <div class="text-white/35 text-[10px] uppercase tracking-wider mb-1">{{ route.from }}</div>
            <div class="text-white font-bold text-base mb-1 group-hover:text-gold transition-colors">{{ route.to }}</div>
            <div class="text-white/40 text-sm mb-4">{{ route.desc }}</div>

            <!-- Distance badges -->
            <div class="flex flex-wrap gap-2">
              <span v-for="seg in route.segments" :key="seg.label"
                class="inline-flex items-center gap-1.5 bg-gold/8 border border-gold/15 rounded-lg px-3 py-1.5">
                <span class="text-gold text-xs font-bold">{{ seg.km }}</span>
                <span class="text-white/30 text-[9px]">{{ seg.label }}</span>
              </span>
            </div>
          </div>
        </div>

        <!-- Transport modes -->
        <div class="grid sm:grid-cols-3 gap-5 mt-6">
          <div v-for="transport in transports" :key="transport.title"
            class="flex gap-4 p-5 rounded-xl border border-white/5 hover:border-gold/20 transition-colors"
            style="background:#12100E">
            <div class="w-9 h-9 rounded-lg bg-gold/8 border border-gold/15 flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" :d="transport.iconPath"/>
              </svg>
            </div>
            <div>
              <div class="text-white font-semibold text-sm mb-1">{{ transport.title }}</div>
              <div class="text-white/30 text-xs leading-relaxed">{{ transport.desc }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ ЧТО МЫ ПРЕДЛАГАЕМ ═══ -->
    <section class="py-28 border-y border-white/5" style="background:#0D0B07">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="text-center mb-16">
          <div class="flex items-center justify-center gap-4 mb-5">
            <div class="w-10 h-px bg-gold/40"></div>
            <span class="text-gold text-[10px] font-bold uppercase tracking-[0.4em]">{{ t('mining.offers_badge') }}</span>
            <div class="w-10 h-px bg-gold/40"></div>
          </div>
          <h2 class="text-4xl lg:text-5xl font-bold text-white">{{ t('mining.offers_title') }}</h2>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
          <div v-for="(offer, i) in offers" :key="i"
            class="group relative rounded-2xl p-8 border border-white/5 hover:border-gold/25 transition-all duration-400 overflow-hidden cursor-default"
            style="background:#12100E">
            <div class="absolute top-0 left-0 right-0 h-px opacity-0 group-hover:opacity-100 transition-opacity duration-500"
              style="background:linear-gradient(90deg,transparent,#C9A96E,transparent)"></div>
            <div class="w-12 h-12 rounded-xl border border-gold/20 bg-gold/5 flex items-center justify-center mb-6 group-hover:bg-gold/12 transition-colors">
              <svg class="w-6 h-6 text-gold" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" :d="offer.icon" /></svg>
            </div>
            <h2 class="text-white font-bold text-base mb-3 group-hover:text-gold transition-colors">{{ offer.title }}</h2>
            <p class="text-white/40 text-sm leading-relaxed">{{ offer.desc }}</p>
            <div class="absolute bottom-5 right-5 text-5xl font-black text-white/3 leading-none">
              {{ String(i + 1).padStart(2, '0') }}
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ ПРОЦЕСС ДОБЫЧИ ═══ -->
    <section class="py-28 overflow-hidden" style="background:#0A0800">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="flex items-center gap-3 mb-5">
          <div class="w-8 h-px bg-gold"></div>
          <span class="text-gold text-[10px] font-bold uppercase tracking-[0.4em]">{{ t('mining.process_badge') }}</span>
        </div>
        <h2 class="text-4xl lg:text-5xl font-bold text-white mb-16">{{ t('mining.process_title') }}</h2>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div v-for="(step, i) in processSteps" :key="i"
            class="relative group">
            <!-- Number -->
            <div class="text-7xl font-black text-white/5 leading-none mb-4 group-hover:text-gold/10 transition-colors">
              {{ String(i + 1).padStart(2, '0') }}
            </div>
            <!-- Connector line -->
            <div v-if="i < processSteps.length - 1"
              class="hidden lg:block absolute top-8 left-[calc(100%-12px)] w-full h-px z-10"
              style="background:linear-gradient(90deg,rgba(201,169,110,0.3),transparent)">
            </div>
            <div class="w-10 h-px bg-gold/30 mb-5"></div>
            <h3 class="text-white font-bold text-base mb-3">{{ step.title }}</h3>
            <p class="text-white/35 text-sm leading-relaxed">{{ step.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ B2B — ТЕХНИЧЕСКИЕ ХАРАКТЕРИСТИКИ ═══ -->
    <section class="py-28 border-y border-white/5" style="background:#0A0800">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-16 items-start">

          <!-- Left: intro -->
          <div>
            <div class="flex items-center gap-3 mb-6">
              <div class="w-8 h-px bg-gold"></div>
              <span class="text-gold text-[10px] font-bold uppercase tracking-[0.4em]">{{ t('mining.b2b_badge') }}</span>
            </div>
            <h2 class="font-bold leading-tight text-white mb-6"
              style="font-size:clamp(28px,3.5vw,46px)">
              {{ t('mining.b2b_title') }}
            </h2>
            <p class="text-white/40 leading-relaxed text-sm mb-8">
              {{ t('mining.b2b_sub') }}
            </p>

            <!-- Download docs -->
            <div class="space-y-3">
              <div v-for="doc in coalDocs" :key="doc.name"
                class="flex items-center gap-4 p-4 rounded-xl border border-white/5 hover:border-gold/20 transition-colors cursor-pointer group"
                style="background:#12100E">
                <div class="w-9 h-9 rounded-lg bg-gold/10 border border-gold/15 flex items-center justify-center flex-shrink-0">
                  <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z"/>
                  </svg>
                </div>
                <div class="flex-1">
                  <div class="text-white text-sm font-medium group-hover:text-gold transition-colors">{{ doc.name }}</div>
                  <div class="text-white/25 text-xs">{{ doc.type }}</div>
                </div>
                <Icon name="arrow-down-tray" class="w-4 h-4 text-white/20 group-hover:text-gold transition-colors" :stroke-width="1.5" />
              </div>
            </div>
          </div>

          <!-- Right: specs tables -->
          <div class="space-y-5">
            <div v-for="grade in coalGrades" :key="grade.name"
              class="rounded-2xl border border-white/5 overflow-hidden"
              style="background:#12100E">
              <!-- Grade header -->
              <div class="flex items-center justify-between px-6 py-4 border-b border-white/5">
                <div>
                  <div class="text-white font-bold text-base">{{ grade.name }}</div>
                  <div class="text-white/35 text-xs">{{ grade.desc }}</div>
                </div>
                <div class="bg-gold/10 border border-gold/25 text-gold text-[10px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-lg">
                  {{ grade.badge }}
                </div>
              </div>
              <!-- Specs -->
              <div class="divide-y divide-white/4">
                <div v-for="spec in grade.specs" :key="spec.label"
                  class="flex items-center justify-between px-6 py-3 hover:bg-white/2 transition-colors">
                  <span class="text-white/35 text-xs">{{ spec.label }}</span>
                  <span class="text-white text-xs font-semibold">{{ spec.value }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ КОНТРОЛЬ КАЧЕСТВА + ОТДЕЛ СБЫТА ═══ -->
    <section class="py-24 border-y border-white/5" style="background:#0C0A06">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-12">

          <!-- Quality control -->
          <div class="rounded-2xl border border-gold/15 p-8" style="background:rgba(201,169,110,0.03)">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-10 h-10 rounded-xl bg-gold/10 border border-gold/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12z"/>
                </svg>
              </div>
              <div>
                <div class="text-white font-bold text-base">{{ t('mining.qc_title') }}</div>
                <div class="text-white/35 text-xs">{{ t('mining.qc_sub') }}</div>
              </div>
            </div>
            <p class="text-white/45 text-sm leading-relaxed mb-6">
              {{ t('mining.qc_desc') }}
            </p>
            <div class="space-y-3">
              <div v-for="qc in qualitySteps" :key="qc.label" class="flex items-start gap-3">
                <div class="w-5 h-5 rounded-full bg-gold/15 border border-gold/25 flex items-center justify-center flex-shrink-0 mt-0.5">
                  <div class="w-1.5 h-1.5 rounded-full bg-gold"></div>
                </div>
                <div>
                  <div class="text-white text-sm font-medium">{{ qc.label }}</div>
                  <div class="text-white/30 text-xs mt-0.5">{{ qc.desc }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sales department -->
          <div>
            <div class="flex items-center gap-3 mb-6">
              <div class="w-8 h-px bg-gold"></div>
              <span class="text-gold text-[10px] font-bold uppercase tracking-[0.4em]">{{ t('mining.sales_badge') }}</span>
            </div>
            <h3 class="font-bold text-white mb-3" style="font-size:clamp(22px,2.5vw,34px)">
              <span class="text-gold">{{ t('mining.sales_title') }}</span>
            </h3>
            <p class="text-white/35 text-sm leading-relaxed mb-7">
              {{ t('mining.sales_sub') }}
            </p>
            <div class="space-y-4">
              <div v-for="(fn, i) in salesFunctions" :key="i"
                class="group flex items-start gap-4 p-4 rounded-xl border border-white/5 hover:border-gold/20 transition-all cursor-default"
                style="background:#12100E">
                <div class="w-8 h-8 rounded-lg bg-gold/10 flex items-center justify-center flex-shrink-0 group-hover:bg-gold/20 transition-colors">
                  <span class="text-gold font-bold text-xs">{{ String(i + 1).padStart(2,'0') }}</span>
                </div>
                <div>
                  <div class="text-white text-sm font-semibold mb-0.5 group-hover:text-gold transition-colors">{{ fn.title }}</div>
                  <div class="text-white/30 text-xs leading-relaxed">{{ fn.desc }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ ФОТОГАЛЕРЕЯ ПРОИЗВОДСТВА ═══ -->
    <section class="py-24" style="background:#0A0800">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="flex items-end justify-between mb-10">
          <div>
            <div class="flex items-center gap-3 mb-4">
              <div class="w-8 h-px bg-gold"></div>
              <span class="text-gold text-[10px] font-bold uppercase tracking-[0.4em]">{{ t('mining.prod_badge') }}</span>
            </div>
            <h2 class="text-3xl lg:text-4xl font-bold text-white">{{ t('mining.prod_title') }}</h2>
          </div>
          <p class="hidden md:block text-white/25 text-xs uppercase tracking-widest">{{ t('mining.scroll_hint') }}</p>
        </div>

        <!-- Horizontal scroll gallery — реальные фото -->
        <div class="flex gap-4 overflow-x-auto pb-4 mining-gallery">
          <div v-for="(ph, i) in miningPhotos" :key="i"
            class="flex-shrink-0 rounded-xl overflow-hidden group relative border border-white/5 hover:border-gold/40 transition-all cursor-pointer"
            :style="`width:${ph.wide ? '480px' : '320px'}; height:320px`"
            @click="lightboxImg = ph.src; lightboxOpen = true">
            <img :src="asset(ph.src)" :alt="ph.label" loading="lazy"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"/>
            <!-- Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-[#0A0800]/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <!-- Number badge -->
            <div class="absolute top-4 left-4 bg-[#0A0800]/80 border border-gold/20 rounded-lg px-2.5 py-1 backdrop-blur-sm">
              <span class="text-gold text-[10px] font-bold">{{ String(i + 1).padStart(2,'0') }}</span>
            </div>
            <!-- Label on hover -->
            <div class="absolute bottom-0 left-0 right-0 p-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
              <div class="text-white text-xs font-medium">{{ ph.label }}</div>
            </div>
          </div>
        </div>
        <p class="text-center text-white/20 text-xs mt-4">{{ t('mining.geo_note') }}</p>
      </div>
    </section>

    <!-- ═══ КОНТАКТ (оптовые заявки) ═══ -->
    <section class="py-28 border-t border-white/5" style="background:#0D0B07">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-16 items-start">

          <div>
            <div class="flex items-center gap-3 mb-6">
              <div class="w-8 h-px bg-gold"></div>
              <span class="text-gold text-[10px] font-bold uppercase tracking-[0.4em]">{{ t('mining.collab_badge') }}</span>
            </div>
            <h2 class="font-bold leading-tight text-white mb-6"
              style="font-size:clamp(32px,4vw,52px)">
              {{ t('mining.collab_title') }}
            </h2>
            <p class="text-white/45 text-base leading-relaxed mb-10">
              {{ t('mining.collab_desc') }}
            </p>

            <div class="space-y-5">
              <div v-for="contact in miningContacts" :key="contact.label"
                class="flex items-center gap-5 group">
                <div class="w-10 h-10 rounded-xl border border-gold/15 bg-gold/5 flex items-center justify-center flex-shrink-0">
                  <Icon :name="contact.icon" class="w-5 h-5 text-gold" :stroke-width="1.5" />
                </div>
                <div>
                  <div class="text-white/30 text-[10px] uppercase tracking-[0.3em] mb-0.5">{{ contact.label }}</div>
                  <div class="text-white font-semibold text-sm">{{ contact.value }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Form -->
          <div class="rounded-2xl p-8 border border-white/5" style="background:#12100E">
            <form @submit.prevent="sendMiningContact" class="space-y-4">
              <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                  <label for="mine-company" class="m-label">{{ t('mining.form_company') }}</label>
                  <input id="mine-company" v-model="mForm.name" type="text" required autocomplete="organization" class="m-input" :placeholder="t('mining.form_ph_name')"/>
                </div>
                <div>
                  <label for="mine-phone" class="m-label">{{ t('mining.form_phone') }}</label>
                  <input id="mine-phone" v-model="mForm.phone" type="tel" required autocomplete="tel" class="m-input" placeholder="+992 ..."/>
                </div>
                <div>
                  <label for="mine-email" class="m-label">{{ t('mining.form_email') }}</label>
                  <input id="mine-email" v-model="mForm.email" type="email" autocomplete="email" class="m-input" placeholder="email@company.com"/>
                </div>
                <div>
                  <label for="mine-volume" class="m-label">{{ t('mining.form_volume') }}</label>
                  <input id="mine-volume" v-model="mForm.volume" type="text" class="m-input" :placeholder="t('mining.form_ph_vol')"/>
                </div>
                <div>
                  <label for="mine-coal-type" class="m-label">{{ t('mining.form_coal_type') }}</label>
                  <select id="mine-coal-type" v-model="mForm.coal_type" class="m-input">
                    <option value="">{{ t('mining.form_choose') }}</option>
                    <option value="coking">{{ t('mining.form_coking') }}</option>
                    <option value="steam">{{ t('mining.form_steam') }}</option>
                    <option value="anthracite">{{ t('mining.form_anthracite') }}</option>
                    <option value="any">{{ t('mining.form_any') }}</option>
                  </select>
                </div>
                <div class="col-span-2">
                  <label for="mine-info" class="m-label">{{ t('mining.form_info') }}</label>
                  <textarea id="mine-info" v-model="mForm.message" rows="3" class="m-input" :placeholder="t('mining.form_ph_msg')"></textarea>
                </div>
              </div>
              <button type="submit"
                class="w-full py-4 rounded-xl font-bold uppercase tracking-widest text-[12px] transition-all duration-300"
                style="background:#C9A96E; color:#0A0800"
                :disabled="mSent">
                {{ mSent ? t('mining.form_sent') : t('mining.form_send') }}
              </button>
              <p class="text-white/20 text-xs text-center">{{ t('mining.form_callback') }}</p>
            </form>
          </div>

        </div>
      </div>
    </section>

    <!-- ═══ LIGHTBOX ═══ -->
    <Transition
      enter-active-class="transition-all duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-all duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="lightboxOpen"
        class="fixed inset-0 z-[999] flex items-center justify-center p-4 cursor-zoom-out"
        style="background:rgba(5,7,14,0.95); backdrop-filter:blur(8px)"
        @click="lightboxOpen = false">
        <div class="relative max-w-5xl max-h-[90vh] w-full" @click.stop>
          <img :src="asset(lightboxImg)" :alt="t('mining.lightbox_alt')"
            class="w-full h-full object-contain rounded-xl shadow-[0_0_80px_rgba(0,0,0,0.8)]"
            style="max-height:85vh"/>
          <button @click="lightboxOpen = false"
            class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/10 hover:bg-gold/20 border border-white/20 flex items-center justify-center transition-colors">
            <Icon name="close" class="w-5 h-5 text-white" :stroke-width="2" />
          </button>
        </div>
      </div>
    </Transition>

  </MainLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import Icon from '@/Components/Icon.vue';
import { useAsset } from '@/composables/useAsset';
import { useTrans } from '@/composables/useTrans';

const { asset } = useAsset();
const { t } = useTrans();

/* ── Lightbox ── */
const lightboxOpen = ref(false);
const lightboxImg  = ref('');

const miningStats = computed(() => [
  { value: t('mining.ms1_v'), unit: t('mining.ms1_u'), label: t('mining.ms1_l') },
  { value: '93.5K',  unit: t('mining.ms2_u'), label: t('mining.ms2_l') },
  { value: '500K',   unit: t('mining.ms3_u'), label: t('mining.ms3_l') },
  { value: '102K',   unit: t('mining.ms4_u'), label: t('mining.ms4_l') },
  { value: '2017',   unit: t('mining.ms5_u'), label: t('mining.ms5_l') },
]);

/* ── Реальная статистика производства ── */
const productionStats = computed(() => [
  { value: '93.5K', label: t('mining.ps1_l'), note: t('mining.ps1_n') },
  { value: '140K',  label: t('mining.ps2_l'), note: t('mining.ps2_n') },
  { value: '500K',  label: t('mining.ps3_l'), note: t('mining.ps3_n') },
  { value: '~100K', label: t('mining.ps4_l'), note: t('mining.ps4_n') },
]);

/* ── Лицензионные документы (реальные файлы) ── */
const licDocs = computed(() => [
  { src: '/images/mining/01.jpeg',          title: t('mining.ld1_t'), type: t('mining.ld1_y') },
  { src: '/images/mining/02-rotated.jpeg',  title: t('mining.ld2_t'), type: t('mining.ld2_y') },
  { src: '/images/mining/03-rotated.jpeg',  title: t('mining.ld3_t'), type: t('mining.ld3_y') },
]);

/* ── Страны экспорта ── */
const exportCountries = computed(() => [
  { code: 'AF', name: t('mining.ec1_n'), desc: t('mining.ec1_d'), badge: t('mining.ec1_b') },
  { code: 'PK', name: t('mining.ec2_n'), desc: t('mining.ec2_d'), badge: t('mining.ec2_b') },
  { code: 'UZ', name: t('mining.ec3_n'), desc: t('mining.ec3_d'), badge: t('mining.ec3_b') },
  { code: 'EU', name: t('mining.ec4_n'), desc: t('mining.ec4_d'), badge: t('mining.ec4_b') },
]);

/* ── Логистические маршруты ── */
const logisticsRoutes = computed(() => [
  {
    from: t('mining.lr1_f'),
    to: t('mining.lr1_t'),
    desc: t('mining.lr1_d'),
    segments: [
      { km: `72 ${t('mining.unit_km')}`, label: t('mining.lr1_s1') },
      { km: `100 ${t('mining.unit_km')}`, label: t('mining.lr1_s2') },
    ],
  },
  {
    from: t('mining.lr2_f'),
    to: t('mining.lr2_t'),
    desc: t('mining.lr2_d'),
    segments: [
      { km: `160 ${t('mining.unit_km')}`, label: t('mining.lr2_s1') },
    ],
  },
  {
    from: t('mining.lr3_f'),
    to: t('mining.lr3_t'),
    desc: t('mining.lr3_d'),
    segments: [
      { km: t('mining.unit_rail'), label: t('mining.lr3_s1') },
      { km: t('mining.unit_auto'), label: t('mining.lr3_s2') },
    ],
  },
]);

const transports = computed(() => [
  { iconPath: 'M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12', title: t('mining.tr1_t'), desc: t('mining.tr1_d') },
  { iconPath: 'M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6', title: t('mining.tr2_t'), desc: t('mining.tr2_d') },
  { iconPath: 'M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z', title: t('mining.tr3_t'), desc: t('mining.tr3_d') },
]);

/* ── Контроль качества ── */
const qualitySteps = computed(() => [
  { label: t('mining.qs1_l'), desc: t('mining.qs1_d') },
  { label: t('mining.qs2_l'), desc: t('mining.qs2_d') },
  { label: t('mining.qs3_l'), desc: t('mining.qs3_d') },
  { label: t('mining.qs4_l'), desc: t('mining.qs4_d') },
]);

/* ── Отдел сбыта ── */
const salesFunctions = computed(() => [
  { title: t('mining.sf1_t'), desc: t('mining.sf1_d') },
  { title: t('mining.sf2_t'), desc: t('mining.sf2_d') },
  { title: t('mining.sf3_t'), desc: t('mining.sf3_d') },
  { title: t('mining.sf4_t'), desc: t('mining.sf4_d') },
  { title: t('mining.sf5_t'), desc: t('mining.sf5_d') },
]);

/* ── Фотогалерея — реальные снимки производства ── */
const miningPhotos = computed(() => [
  { src: '/images/mining/1-768x1086.jpg',   label: t('mining.ph1_l'),  wide: false },
  { src: '/images/mining/2-768x1086.jpg',   label: t('mining.ph2_l'),  wide: false },
  { src: '/images/mining/3-768x1086.jpg',   label: t('mining.ph3_l'),  wide: true  },
  { src: '/images/mining/4-724x1024.jpg',   label: t('mining.ph4_l'),  wide: false },
  { src: '/images/mining/5-724x1024.jpg',   label: t('mining.ph5_l'),  wide: false },
  { src: '/images/mining/6-768x994.jpg',    label: t('mining.ph6_l'),  wide: true  },
  { src: '/images/mining/7-768x994.jpg',    label: t('mining.ph7_l'),  wide: false },
  { src: '/images/mining/8-768x994.jpg',    label: t('mining.ph8_l'),  wide: false },
  { src: '/images/mining/9-768x994.jpg',    label: t('mining.ph9_l'),  wide: true  },
  { src: '/images/mining/10-768x994.jpg',   label: t('mining.ph10_l'), wide: false },
  { src: '/images/mining/11-768x994.jpg',   label: t('mining.ph11_l'), wide: false },
  { src: '/images/mining/12-768x994.jpg',   label: t('mining.ph12_l'), wide: true  },
  { src: '/images/mining/13-768x994.jpg',   label: t('mining.ph13_l'), wide: false },
  { src: '/images/mining/14-724x1024.jpg',  label: t('mining.ph14_l'), wide: false },
  { src: '/images/mining/15-724x1024.jpg',  label: t('mining.ph15_l'), wide: true  },
  { src: '/images/mining/16-724x1024.jpg',  label: t('mining.ph16_l'), wide: false },
  { src: '/images/mining/17-724x1024.jpg',  label: t('mining.ph17_l'), wide: false },
  { src: '/images/mining/18-724x1024.jpg',  label: t('mining.ph18_l'), wide: false },
  { src: '/images/mining/19-724x1024.jpg',  label: t('mining.ph19_l'), wide: true  },
  { src: '/images/mining/20-724x1024.jpg',  label: t('mining.ph20_l'), wide: false },
]);

const keyPoints = computed(() => [
  { title: t('mining.kp1_t'), desc: t('mining.kp1_d') },
  { title: t('mining.kp2_t'), desc: t('mining.kp2_d') },
  { title: t('mining.kp3_t'), desc: t('mining.kp3_d') },
  { title: t('mining.kp4_t'), desc: t('mining.kp4_d') },
]);

const miningCards = computed(() => [
  { value: '500K', title: t('mining.mc1_t'), desc: t('mining.mc1_d') },
  { value: 'A/B',  title: t('mining.mc2_t'), desc: t('mining.mc2_d') },
  { value: '24/7', title: t('mining.mc3_t'), desc: t('mining.mc3_d') },
  { value: 'TJ',   title: t('mining.mc4_t'), desc: t('mining.mc4_d') },
]);

const offers = computed(() => [
  {
    title: t('mining.of1_t'),
    desc: t('mining.of1_d'),
    icon: 'M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48z M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.975 5.975 0 0 1-2.133-1.001A3.75 3.75 0 0 0 12 18z',
  },
  {
    title: t('mining.of2_t'),
    desc: t('mining.of2_d'),
    icon: 'M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125',
  },
  {
    title: t('mining.of3_t'),
    desc: t('mining.of3_d'),
    icon: 'M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12',
  },
  {
    title: t('mining.of4_t'),
    desc: t('mining.of4_d'),
    icon: 'M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25',
  },
  {
    title: t('mining.of5_t'),
    desc: t('mining.of5_d'),
    icon: 'M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253',
  },
  {
    title: t('mining.of6_t'),
    desc: t('mining.of6_d'),
    icon: 'm2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25',
  },
]);

const processSteps = computed(() => [
  { title: t('mining.proc1_t'), desc: t('mining.proc1_d') },
  { title: t('mining.proc2_t'), desc: t('mining.proc2_d') },
  { title: t('mining.proc3_t'), desc: t('mining.proc3_d') },
  { title: t('mining.proc4_t'), desc: t('mining.proc4_d') },
]);

const miningContacts = computed(() => [
  {
    label: t('mining.ct_phone'),
    value: '+992 902000255',
    icon: 'phone',
  },
  {
    label: 'Email',
    value: 'coal@qudrat.tj',
    icon: 'mail',
  },
  {
    label: t('mining.ct_addr'),
    value: t('mining.ct_addr_val'),
    icon: 'map-pin',
  },
]);

/* ── B2B docs ── */
const coalDocs = computed(() => [
  { name: t('mining.cd1_n'), type: t('mining.cd1_y') },
  { name: t('mining.cd2_n'), type: t('mining.cd2_y') },
  { name: t('mining.cd3_n'), type: t('mining.cd3_y') },
  { name: t('mining.cd4_n'), type: t('mining.cd4_y') },
]);

/* ── Реальные лаб. данные с месторождения Гузн ── */
const coalGrades = computed(() => [
  {
    name: t('mining.cg1_n'), badge: t('mining.cg1_b'), desc: t('mining.cg1_d'),
    specs: [
      { label: t('mining.cg1_s1_l'), value: t('mining.cg1_s1_v') },
      { label: t('mining.cg1_s2_l'), value: t('mining.cg1_s2_v') },
      { label: t('mining.cg1_s3_l'), value: t('mining.cg1_s3_v') },
      { label: t('mining.cg1_s4_l'), value: t('mining.cg1_s4_v') },
      { label: t('mining.cg1_s5_l'), value: t('mining.cg1_s5_v') },
      { label: t('mining.cg1_s6_l'), value: t('mining.cg1_s6_v') },
    ],
  },
  {
    name: t('mining.cg2_n'), badge: t('mining.cg2_b'), desc: t('mining.cg2_d'),
    specs: [
      { label: t('mining.cg2_s1_l'), value: t('mining.cg2_s1_v') },
      { label: t('mining.cg2_s2_l'), value: t('mining.cg2_s2_v') },
      { label: t('mining.cg2_s3_l'), value: t('mining.cg2_s3_v') },
      { label: t('mining.cg2_s4_l'), value: t('mining.cg2_s4_v') },
      { label: t('mining.cg2_s5_l'), value: t('mining.cg2_s5_v') },
      { label: t('mining.cg2_s6_l'), value: t('mining.cg2_s6_v') },
    ],
  },
]);

/* ── B2B Contact Form ── */
const miningForm = useForm({
  name: '', phone: '', email: '', volume: '', coal_type: '', message: '',
});
const mSent = ref(false);

// Для шаблона — совместимость с v-model
const mForm = miningForm;

function sendMiningContact() {
  miningForm.post(route('mining.contact'), {
    preserveScroll: true,
    onSuccess: () => {
      mSent.value = true;
      miningForm.reset();
      setTimeout(() => { mSent.value = false; }, 4000);
    },
  });
}
</script>

<style scoped>
.m-label {
  display: block;
  font-size: 0.7rem;
  font-weight: 600;
  color: #94A3B8;
  margin-bottom: 0.375rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}
.m-input {
  display: block;
  width: 100%;
  padding: 0.7rem 0.875rem;
  border-radius: 0.75rem;
  font-size: 0.875rem;
  color: #fff;
  background: #1A1612;
  border: 1px solid rgba(255,255,255,0.06);
  outline: none;
  transition: border-color 0.2s;
}
.m-input:focus { border-color: rgba(201,169,110,0.45); }
.m-input::placeholder { color: rgba(255,255,255,0.18); }

/* Mining photo gallery */
.mining-gallery { scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; scrollbar-width: none; }
.mining-gallery::-webkit-scrollbar { display: none; }
.mining-gallery > div { scroll-snap-align: start; }
</style>
