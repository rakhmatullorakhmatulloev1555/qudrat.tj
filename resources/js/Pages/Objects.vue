<template>
  <MainLayout>
    <SeoHead
      :title="t('objects.meta_title')"
      :ogTitle="t('objects.meta_title')"
      :description="t('objects.meta_desc')"
      path="/objects"
    />

    <!-- ═══════════════════════════════════════════
         HERO — Premium Projects
    ═══════════════════════════════════════════ -->
    <section class="oh-stage">

      <!-- Full-bleed background image (right 65%) -->
      <div class="oh-img-wrap">
        <img :src="asset('/images/hero-21.webp')" alt="QUDRAT Projects" class="oh-img"/>
      </div>

      <!-- Left-to-right gradient overlay -->
      <div class="oh-left-ol"></div>

      <!-- Content — flex: 1, centers text vertically -->
      <div class="oh-content-wrap">
        <div class="oh-content">
          <div class="oh-inner">

            <!-- Eyebrow -->
            <div class="oh-ew">
              <span class="oh-ew-line"></span>
              <span class="oh-ew-txt">{{ t('objects.hero_eyebrow') }}</span>
            </div>

            <!-- 4-line heading -->
            <h1 class="oh-h1">
              <span class="oh-h1-w">{{ t('objects.hero_hw1') }}</span>
              <span class="oh-h1-w">{{ t('objects.hero_hw2') }}</span>
              <span class="oh-h1-w">{{ t('objects.hero_hw3') }}</span>
              <span class="oh-h1-g">{{ t('objects.hero_hg') }}</span>
            </h1>

            <!-- Subtitle -->
            <p class="oh-sub">{{ t('objects.hero_sub_new') }}</p>

            <!-- CTAs -->
            <div class="oh-actions">
              <a href="#catalog" class="oh-cta-p">
                {{ t('objects.cta_choose_apt') }}
                <Icon name="arrow-right" class="w-3.5 h-3.5 flex-shrink-0" :stroke-width="2.5" />
              </a>
              <a href="#" class="oh-cta-s">{{ t('objects.cta_presentation') }}</a>
            </div>

          </div>
        </div>
      </div>

      <!-- Glass feature bar — pinned to bottom -->
      <div class="oh-glass-bar">
        <div class="oh-glass-in">

          <!-- Feature 1: Security -->
          <div class="oh-feat">
            <div class="oh-feat-icon">
              <Icon name="check-circle" class="w-5 h-5" :stroke-width="1.5" />
            </div>
            <div>
              <div class="oh-feat-title">{{ t('objects.feat_safe') }}</div>
              <div class="oh-feat-sub">{{ t('objects.feat_safe_sub') }}</div>
            </div>
          </div>

          <div class="oh-feat-div"></div>

          <!-- Feature 2: Smart Home -->
          <div class="oh-feat">
            <div class="oh-feat-icon">
              <svg class="w-5 h-5" fill="none" stroke="#C9A96E" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/>
              </svg>
            </div>
            <div>
              <div class="oh-feat-title">{{ t('objects.feat_smart') }}</div>
              <div class="oh-feat-sub">{{ t('objects.feat_smart_sub') }}</div>
            </div>
          </div>

          <div class="oh-feat-div"></div>

          <!-- Feature 3: Infrastructure -->
          <div class="oh-feat">
            <div class="oh-feat-icon">
              <svg class="w-5 h-5" fill="none" stroke="#C9A96E" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z"/>
              </svg>
            </div>
            <div>
              <div class="oh-feat-title">{{ t('objects.feat_infra') }}</div>
              <div class="oh-feat-sub">{{ t('objects.feat_infra_sub') }}</div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- ═══ STATS BAR ═══ -->
    <div class="border-y border-white/5 py-5" style="background:#070B16">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="flex flex-wrap items-center gap-6">
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            <span class="text-white font-semibold text-sm">{{ stats.available }}</span>
            <span class="text-gray-500 text-xs">{{ t('objects.bar_available') }}</span>
          </div>
          <div class="w-px h-4 bg-white/10 hidden sm:block"></div>
          <div class="text-gray-500 text-xs">{{ t('objects.bar_price_from') }} <span class="text-white font-semibold">${{ fmtPrice(stats.min_price) }}</span></div>
          <div class="text-gray-500 text-xs" v-if="stats.max_price">{{ t('objects.bar_price_to') }} <span class="text-white font-semibold">${{ fmtPrice(stats.max_price) }}</span></div>
          <div class="w-px h-4 bg-white/10 hidden sm:block"></div>
          <div class="text-gray-500 text-xs">{{ t('objects.bar_area') }} <span class="text-white font-semibold">{{ stats.min_area }} м²</span> {{ t('objects.bar_price_to') }} <span class="text-white font-semibold">{{ stats.max_area }} м²</span></div>
          <div class="ml-auto">
            <button @click="openReserve(null)"
              class="flex items-center gap-2 bg-gold hover:bg-gold-600 text-dark text-xs font-bold px-5 py-2.5 rounded-xl transition-all uppercase tracking-widest">
              <Icon name="plus" class="w-3.5 h-3.5" :stroke-width="2.5" />
              {{ t('objects.bar_book') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════
         CATEGORY TABS
    ═══════════════════════════════════════════ -->
    <div class="border-b border-white/5 sticky top-[72px] z-30" style="background:#0A0E1A">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="flex gap-0 overflow-x-auto obj-tabs-scroll">
          <button v-for="tab in projectTabs" :key="tab.key"
            @click="activeTab = tab.key"
            :class="['flex-shrink-0 px-5 py-4 text-[11px] font-bold uppercase tracking-wider transition-all border-b-2',
              activeTab === tab.key
                ? 'text-gold border-gold'
                : 'text-white/40 border-transparent hover:text-white/70']">
            {{ tab.label }}
          </button>
        </div>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════
         FEATURED PROJECTS (управляются через админку → «Знаковые объекты»)
    ═══════════════════════════════════════════ -->
    <section v-if="featuredProjects.length" id="featured" class="py-20" style="background:#070B16">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <!-- Section header -->
        <div class="flex items-end justify-between mb-12">
          <div>
            <div class="inline-flex items-center gap-3 mb-4">
              <div class="h-px w-10" style="background:linear-gradient(to right,transparent,rgba(201,169,110,0.6))"></div>
              <span class="text-gold/70 text-[9px] font-bold uppercase tracking-[0.55em]">{{ t('objects.featured_badge') }}</span>
              <div class="h-px w-10" style="background:linear-gradient(to left,transparent,rgba(201,169,110,0.6))"></div>
            </div>
            <h2 class="font-black text-white leading-none"
              style="font-size:clamp(28px,3.5vw,52px);letter-spacing:-0.02em">
              {{ t('objects.featured_title') }}
            </h2>
          </div>
          <a href="#catalog"
            class="hidden lg:inline-flex items-center gap-2 px-6 py-3 rounded-xl border font-bold text-xs uppercase tracking-wider transition-all hover:bg-white/5"
            style="border-color:rgba(201,169,110,0.25);color:rgba(201,169,110,0.8)">
            {{ t('objects.btn_show_all') }} →
          </a>
        </div>

        <!-- Project cards -->
        <div class="grid lg:grid-cols-4 gap-5">
          <Link v-for="fp in featuredProjects" :key="fp.slug"
            :href="route('projects.show', fp.slug)"
            class="obj-fp-card block rounded-2xl overflow-hidden border border-white/6 transition-all duration-400 group cursor-pointer"
            style="background:#0C1220">

            <!-- Card image -->
            <div class="h-44 relative overflow-hidden">
              <img v-if="fp.img" :src="asset(fp.img)" :alt="fp.name"
                   class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                   style="opacity:0.72; object-position:50% 40%"/>
              <div class="absolute inset-0"
                style="background:linear-gradient(to top,rgba(7,11,22,0.9) 0%,rgba(7,11,22,0.15) 60%)"></div>
              <!-- Type badge -->
              <div class="absolute top-4 left-4">
                <span v-if="fp.type" class="text-[9px] font-black uppercase tracking-wider px-2.5 py-1 rounded-md"
                  :style="`background:${hexToRgba(fp.accent, 0.15)};border:1px solid ${hexToRgba(fp.accent, 0.3)};color:${fp.accent}`">{{ fp.type }}</span>
              </div>
              <!-- Arrow on hover -->
              <div class="absolute top-4 right-4 w-8 h-8 rounded-full border flex items-center justify-center transition-all opacity-0 group-hover:opacity-100"
                style="border-color:rgba(201,169,110,0.4);background:rgba(201,169,110,0.08)">
                <svg class="w-3.5 h-3.5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/>
                </svg>
              </div>
            </div>

            <!-- Card body -->
            <div class="p-5">
              <h3 class="text-white font-black text-base tracking-tight leading-tight mb-1 group-hover:text-gold transition-colors">{{ fp.name }}</h3>
              <!-- Location -->
              <div class="flex items-center gap-1 text-white/35 text-[11px] mb-3">
                <Icon name="map-pin" class="w-3 h-3 flex-shrink-0" :stroke-width="2" />
                {{ fp.loc }}
              </div>
              <p class="text-white/40 text-xs leading-relaxed mb-4">{{ fp.desc }}</p>

              <!-- 3 mini stats -->
              <div class="grid grid-cols-3 gap-2 pt-4 border-t border-white/5">
                <div v-for="s in fp.stats" :key="s.l" class="text-center">
                  <div class="text-gold font-black text-sm leading-none">{{ s.v }}</div>
                  <div class="text-white/30 text-[9px] uppercase tracking-wider mt-0.5">{{ s.l }}</div>
                </div>
              </div>
            </div>
          </Link>
        </div>

        <!-- Mobile "show all" -->
        <div class="text-center mt-8 lg:hidden">
          <a href="#catalog"
            class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl border font-bold text-sm uppercase tracking-wider"
            style="border-color:rgba(201,169,110,0.25);color:rgba(201,169,110,0.8)">
            {{ t('objects.btn_show_all') }} →
          </a>
        </div>
      </div>
    </section>

    <!-- ═══ MAIN ═══ -->
    <section id="catalog" class="max-w-[1400px] mx-auto px-6 lg:px-12 py-10">
      <div class="flex gap-8">

        <!-- ── SIDEBAR ── -->
        <aside class="hidden lg:block shrink-0 sticky top-24 self-start rounded-2xl p-6" style="width:270px; background:#0A0E1A; border:1px solid rgba(255,255,255,0.07); height:fit-content">

          <div class="flex items-center justify-between mb-6">
            <span class="text-white text-xs font-bold tracking-[0.2em] uppercase flex items-center gap-2">
              <svg class="w-3.5 h-3.5 text-gold" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75"/>
              </svg>
              {{ t('objects.filter_title') }}
            </span>
            <button v-if="hasFilters" @click="resetFilters" class="text-gold text-[10px] tracking-wider hover:opacity-70 transition-opacity">
              {{ t('objects.filter_reset') }} ×
            </button>
          </div>

          <div class="space-y-7">

            <!-- Комнатность -->
            <div>
              <h4 class="text-[10px] font-bold tracking-[0.25em] text-gray-500 uppercase mb-3">{{ t('objects.filter_rooms') }}</h4>
              <div class="grid grid-cols-4 gap-2">
                <button v-for="r in roomOptions" :key="r.val" @click="toggleRoom(r.val)"
                  :class="['py-2 rounded-lg border text-xs font-bold transition-all', selectedRooms.includes(r.val)
                    ? 'border-gold bg-gold/10 text-gold'
                    : 'border-white/8 text-gray-400 hover:border-white/20 hover:text-white']">
                  {{ r.label }}
                </button>
              </div>
            </div>

            <!-- Отделка -->
            <div>
              <h4 class="text-[10px] font-bold tracking-[0.25em] text-gray-500 uppercase mb-3">{{ t('objects.filter_finish') }}</h4>
              <div class="space-y-2">
                <label v-for="f in finishOptions" :key="f.val" class="flex items-center gap-2.5 cursor-pointer group">
                  <input type="checkbox" v-model="selectedFinish" :value="f.val" class="w-3.5 h-3.5 accent-gold rounded cursor-pointer"/>
                  <span class="text-gray-400 text-sm group-hover:text-white transition-colors">{{ f.label }}</span>
                </label>
              </div>
            </div>

            <!-- Статус -->
            <div>
              <h4 class="text-[10px] font-bold tracking-[0.25em] text-gray-500 uppercase mb-3">{{ t('objects.filter_status') }}</h4>
              <div class="space-y-2">
                <label v-for="s in statusOptions" :key="s.val" class="flex items-center justify-between cursor-pointer group">
                  <div class="flex items-center gap-2.5">
                    <input type="checkbox" v-model="selectedStatuses" :value="s.val" class="w-3.5 h-3.5 accent-gold cursor-pointer"/>
                    <span class="text-sm group-hover:text-white transition-colors" :class="s.color">{{ s.label }}</span>
                  </div>
                  <span class="text-gray-600 text-xs">{{ apartments.filter(a => a.status === s.val).length }}</span>
                </label>
              </div>
            </div>

            <!-- Цена -->
            <div>
              <h4 class="text-[10px] font-bold tracking-[0.25em] text-gray-500 uppercase mb-3">{{ t('objects.filter_price') }}</h4>
              <div class="grid grid-cols-2 gap-2">
                <input v-model.number="priceFrom" type="number" :placeholder="t('objects.bar_price_from')"
                  class="w-full bg-white/5 border border-white/8 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-600 focus:border-gold/40 focus:outline-none transition-colors"/>
                <input v-model.number="priceTo" type="number" :placeholder="t('objects.bar_price_to')"
                  class="w-full bg-white/5 border border-white/8 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-600 focus:border-gold/40 focus:outline-none transition-colors"/>
              </div>
            </div>

            <!-- Площадь -->
            <div>
              <h4 class="text-[10px] font-bold tracking-[0.25em] text-gray-500 uppercase mb-3">{{ t('objects.filter_area') }}</h4>
              <div class="grid grid-cols-2 gap-2">
                <input v-model.number="areaFrom" type="number" :placeholder="t('objects.bar_price_from')"
                  class="w-full bg-white/5 border border-white/8 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-600 focus:border-gold/40 focus:outline-none transition-colors"/>
                <input v-model.number="areaTo" type="number" :placeholder="t('objects.bar_price_to')"
                  class="w-full bg-white/5 border border-white/8 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-600 focus:border-gold/40 focus:outline-none transition-colors"/>
              </div>
            </div>

            <!-- Этаж -->
            <div>
              <h4 class="text-[10px] font-bold tracking-[0.25em] text-gray-500 uppercase mb-3">{{ t('objects.filter_floor') }}</h4>
              <div class="grid grid-cols-2 gap-2">
                <input v-model.number="floorFrom" type="number" :placeholder="t('objects.bar_price_from')" min="1"
                  class="w-full bg-white/5 border border-white/8 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-600 focus:border-gold/40 focus:outline-none transition-colors"/>
                <input v-model.number="floorTo" type="number" :placeholder="`${t('objects.bar_price_to')} ${stats.floors||'—'}`"
                  class="w-full bg-white/5 border border-white/8 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-600 focus:border-gold/40 focus:outline-none transition-colors"/>
              </div>
            </div>
          </div>

          <!-- Count -->
          <div class="mt-6 pt-5 border-t border-white/5 text-center">
            <p class="text-gray-400 text-sm">
              {{ t('objects.filter_found') }}: <span class="text-white font-semibold">{{ filtered.length }}</span> {{ t('objects.filter_apts') }}
            </p>
          </div>
        </aside>

        <!-- ── RIGHT ── -->
        <div class="flex-1 min-w-0">

          <!-- Toolbar -->
          <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
            <div class="flex items-center gap-3">
              <!-- View toggle -->
              <div class="flex items-center border border-white/10 rounded-xl overflow-hidden">
                <button @click="viewMode = 'grid'"
                  :class="['p-2.5 transition-colors', viewMode === 'grid' ? 'bg-gold text-dark' : 'text-gray-500 hover:text-white']">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                  </svg>
                </button>
                <button @click="viewMode = 'list'"
                  :class="['p-2.5 transition-colors', viewMode === 'list' ? 'bg-gold text-dark' : 'text-gray-500 hover:text-white']">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                  </svg>
                </button>
              </div>

              <!-- Sort -->
              <select v-model="sortBy"
                class="bg-transparent border border-white/10 text-gray-400 text-xs px-3 py-2.5 rounded-xl focus:outline-none focus:border-gold/40 cursor-pointer">
                <option value="rooms_asc"  class="bg-[#0A0E1A]">{{ t('objects.sort_rooms') }}</option>
                <option value="price_asc"  class="bg-[#0A0E1A]">{{ t('objects.sort_price_asc') }}</option>
                <option value="price_desc" class="bg-[#0A0E1A]">{{ t('objects.sort_price_desc') }}</option>
                <option value="area_asc"   class="bg-[#0A0E1A]">{{ t('objects.sort_area_asc') }}</option>
                <option value="area_desc"  class="bg-[#0A0E1A]">{{ t('objects.sort_area_desc') }}</option>
                <option value="floor_asc"  class="bg-[#0A0E1A]">{{ t('objects.sort_floor') }}</option>
              </select>
            </div>

            <p class="text-gray-500 text-xs">
              {{ filtered.length }} {{ t('objects.of_count') }} {{ apartments.length }} {{ t('objects.apts_count') }}
            </p>
          </div>

          <!-- Mobile filters toggle button (opens bottom sheet) -->
          <div class="lg:hidden mb-5">
            <button @click="mobileFilters = true"
              class="flex items-center gap-2.5 rounded-xl px-4 py-3 text-sm font-semibold transition-all w-full justify-center"
              :class="hasFilters
                ? 'border border-gold/40 bg-gold/6 text-gold'
                : 'border border-white/10 text-white/50 hover:text-white hover:border-white/20'">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75"/>
              </svg>
              <span>{{ t('objects.filter_title') }}</span>
              <span v-if="hasFilters" class="ml-auto text-[11px] bg-gold text-[#0A0800] rounded-full px-2.5 py-0.5 font-bold leading-none">
                {{ filtered.length }}
              </span>
            </button>
          </div>

          <!-- ── EMPTY STATE ── -->
          <div v-if="apartments.length === 0" class="text-center py-24">
            <div class="w-20 h-20 rounded-full bg-gold/8 border border-gold/20 flex items-center justify-center mx-auto mb-6">
              <svg class="w-10 h-10 text-gold/40" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"/>
              </svg>
            </div>
            <h3 class="text-white font-semibold text-xl mb-3">{{ t('objects.empty_title') }}</h3>
            <p class="text-gray-500 text-sm max-w-sm mx-auto mb-6">{{ t('objects.empty_desc') }}</p>
            <button @click="openReserve(null)" class="inline-flex items-center gap-2 bg-gold hover:bg-gold-600 text-dark font-bold px-8 py-4 rounded-xl transition-all text-sm uppercase tracking-widest">
              {{ t('objects.empty_btn') }}
            </button>
          </div>

          <!-- ── NO RESULTS AFTER FILTER ── -->
          <div v-else-if="filtered.length === 0" class="text-center py-16">
            <div class="w-12 h-12 mx-auto mb-4 text-white/20">
              <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
            </div>
            <h3 class="text-white font-semibold text-lg mb-2">{{ t('objects.no_results') }}</h3>
            <p class="text-gray-500 text-sm mb-5">{{ t('objects.no_results_hint') }} <button @click="resetFilters" class="text-gold hover:underline">{{ t('objects.reset_link') }}</button></p>
          </div>

          <!-- ── GRID VIEW ── -->
          <div v-else-if="viewMode === 'grid'"
            class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
            <div v-for="apt in paginated" :key="apt.id"
              class="group rounded-2xl overflow-hidden cursor-pointer transition-all duration-300 hover:border-gold/30 hover:-translate-y-0.5"
              style="background:#0F1828; border:1px solid rgba(255,255,255,0.06)">

              <!-- Image / plan -->
              <div class="relative overflow-hidden" style="height:200px">
                <img v-if="apt.plan_image" :src="asset(apt.plan_image)" :alt="`${t('objects.apt_abbr')} ${apt.number}`" loading="lazy"
                  class="w-full h-full object-contain bg-[#080E18] group-hover:scale-105 transition-transform duration-700"/>
                <div v-else class="w-full h-full flex flex-col items-center justify-center gap-3" style="background:linear-gradient(135deg,#0A0E1A,#0D1525)">
                  <svg class="w-24 h-24 text-gray-700" fill="none" stroke="currentColor" stroke-width="0.8" viewBox="0 0 100 100">
                    <rect x="10" y="10" width="80" height="80" rx="2"/>
                    <rect x="10" y="10" width="50" height="40" rx="1"/>
                    <rect x="60" y="10" width="30" height="40" rx="1"/>
                    <rect x="10" y="50" width="35" height="40" rx="1"/>
                    <rect x="45" y="50" width="45" height="40" rx="1"/>
                    <line x1="10" y1="50" x2="90" y2="50"/>
                    <line x1="60" y1="10" x2="60" y2="50"/>
                    <line x1="45" y1="50" x2="45" y2="90"/>
                    <rect x="38" y="48" width="14" height="4" rx="1" fill="currentColor" opacity="0.4"/>
                  </svg>
                  <span class="text-gray-600 text-xs">{{ t('objects.plan_word') }}</span>
                </div>
                <!-- Status badge -->
                <div class="absolute top-3 left-3">
                  <span :class="statusBadge(apt.status)"
                    class="text-[10px] font-bold px-2.5 py-1 rounded-full tracking-wider uppercase">
                    {{ statusLabel(apt.status) }}
                  </span>
                </div>
                <!-- Rooms badge -->
                <div class="absolute top-3 right-3 w-8 h-8 rounded-xl flex items-center justify-center text-xs font-black text-[#0A0800]"
                  style="background:#C9A96E">
                  {{ apt.rooms }}
                </div>
                <div class="absolute inset-x-0 bottom-0 h-16" style="background:linear-gradient(to top,rgba(13,21,37,0.9),transparent)"></div>
              </div>

              <!-- Body -->
              <div class="p-5">
                <div class="flex items-start justify-between mb-3">
                  <div>
                    <h3 class="text-white font-semibold text-sm leading-snug">
                      {{ roomLabel(apt.rooms) }}, {{ apt.area }} м²
                    </h3>
                    <p class="text-gray-500 text-xs mt-0.5">
                      {{ t('objects.apt_abbr') }} {{ apt.number }} · {{ apt.floor }} {{ t('objects.floor_word') }}
                    </p>
                  </div>
                  <div class="text-right">
                    <div class="text-gold font-bold text-base">
                      ${{ fmtPrice(apt.price) }}
                    </div>
                    <div class="text-gray-600 text-[10px]">{{ apt.currency }}</div>
                  </div>
                </div>

                <!-- Tags -->
                <div class="flex flex-wrap gap-1.5 mb-4">
                  <span class="text-[10px] px-2 py-0.5 rounded-full border border-white/8 text-gray-500">
                    {{ finishLabel(apt.finish) }}
                  </span>
                </div>

                <!-- CTA -->
                <div class="flex gap-2">
                  <Link :href="route('apartments.show', [apt.project?.slug || 'obj', apt.id])"
                    class="flex-1 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider text-center border border-white/12 text-white/70 hover:text-gold hover:border-gold/40 transition-all">
                    {{ t('objects.view_btn') }}
                  </Link>
                  <button @click="apt.status === 'available' ? openReserve(apt) : null"
                    :disabled="apt.status !== 'available'"
                    :class="['flex-1 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all', apt.status === 'available'
                      ? 'bg-gold/10 border border-gold/30 text-gold hover:bg-gold hover:text-[#0A0800]'
                      : 'bg-white/3 border border-white/5 text-gray-600 cursor-not-allowed']">
                    {{ apt.status === 'available' ? t('objects.bar_book') : statusLabel(apt.status) }}
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- ── LIST VIEW ── -->
          <div v-else class="space-y-3">
            <div v-for="apt in paginated" :key="apt.id"
              class="group flex items-center gap-5 rounded-2xl p-4 transition-all duration-300 hover:border-gold/25 cursor-pointer"
              style="background:#0F1828; border:1px solid rgba(255,255,255,0.06)">

              <!-- Mini plan -->
              <div class="w-20 h-16 rounded-xl overflow-hidden flex-shrink-0 flex items-center justify-center" style="background:#0C1220">
                <img v-if="apt.plan_image" :src="asset(apt.plan_image)" :alt="`${t('objects.apt_abbr')} ${apt.number}`" loading="lazy" class="w-full h-full object-contain"/>
                <span v-else class="text-gold font-black text-xl">{{ apt.rooms }}</span>
              </div>

              <!-- Info -->
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3 mb-1">
                  <span class="text-white font-semibold text-sm">{{ roomLabel(apt.rooms) }}, {{ apt.area }} м²</span>
                  <span :class="statusBadge(apt.status)" class="text-[10px] font-bold px-2 py-0.5 rounded-full tracking-wider uppercase">
                    {{ statusLabel(apt.status) }}
                  </span>
                </div>
                <div class="flex flex-wrap gap-4 text-xs text-gray-500">
                  <span>{{ t('objects.apt_abbr') }} {{ apt.number }}</span>
                  <span>{{ apt.floor }} {{ t('objects.floor_word') }}</span>
                  <span>{{ finishLabel(apt.finish) }}</span>
                </div>
              </div>

              <!-- Price + action -->
              <div class="flex items-center gap-4 flex-shrink-0">
                <div class="text-right hidden sm:block">
                  <div class="text-gold font-bold text-lg">${{ fmtPrice(apt.price) }}</div>
                  <div class="text-gray-600 text-[10px]">{{ apt.currency }}</div>
                </div>
                <Link :href="route('apartments.show', [apt.project?.slug || 'obj', apt.id])"
                  class="px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider text-center border border-white/12 text-white/70 hover:text-gold hover:border-gold/40 transition-all whitespace-nowrap">
                  {{ t('objects.view_btn') }}
                </Link>
                <button @click="apt.status === 'available' ? openReserve(apt) : null"
                  :disabled="apt.status !== 'available'"
                  :class="['px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all whitespace-nowrap', apt.status === 'available'
                    ? 'bg-gold/10 border border-gold/30 text-gold hover:bg-gold hover:text-[#0A0800]'
                    : 'border border-white/5 text-gray-600 cursor-not-allowed']">
                  {{ apt.status === 'available' ? t('objects.bar_book') : statusLabel(apt.status) }}
                </button>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="filtered.length > perPage" class="flex items-center justify-center gap-3 mt-10">
            <button @click="currentPage--" :disabled="currentPage === 1"
              class="px-5 py-2.5 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:border-white/25 disabled:opacity-30 text-sm transition-all">
              {{ t('objects.pagination_prev') }}
            </button>
            <span class="text-gray-500 text-sm">{{ currentPage }} / {{ totalPages }}</span>
            <button @click="currentPage++" :disabled="currentPage === totalPages"
              class="px-5 py-2.5 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:border-white/25 disabled:opacity-30 text-sm transition-all">
              {{ t('objects.pagination_next') }}
            </button>
          </div>

          <!-- Consultation banner -->
          <div class="mt-14 rounded-2xl p-8 flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden"
            style="background:linear-gradient(135deg,rgba(201,169,110,0.08),rgba(201,169,110,0.02)); border:1px solid rgba(201,169,110,0.15)">
            <div class="absolute top-0 right-0 w-48 h-48 opacity-5" style="background:radial-gradient(circle,#C9A96E,transparent)"></div>
            <div class="relative">
              <p class="text-white font-bold text-lg mb-1">{{ t('objects.cta_title') }}</p>
              <p class="text-gray-400 text-sm">{{ t('objects.cta_desc') }}</p>
            </div>
            <div class="relative flex flex-wrap gap-3">
              <button @click="openReserve(null)"
                class="bg-gold hover:bg-gold-600 text-dark font-bold px-8 py-3.5 rounded-xl text-xs tracking-widest uppercase transition-all shadow-[0_4px_20px_rgba(201,169,110,0.25)]">
                {{ t('objects.cta_btn') }}
              </button>
              <a :href="`tel:${contactPhone}`"
                class="border border-white/15 hover:border-white/30 text-white/60 hover:text-white font-semibold px-6 py-3.5 rounded-xl text-xs tracking-widest uppercase transition-colors">
                {{ t('objects.call_btn') }}
              </a>
            </div>
          </div>

        </div><!-- /right -->
      </div>
    </section>

    <!-- ═══════════════════════════════════════════
         CAPABILITIES — Mining + Construction
    ═══════════════════════════════════════════ -->
    <section class="py-28 border-t border-white/5" style="background:#0C1220">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <!-- Header -->
        <div class="text-center mb-16">
          <div class="inline-flex items-center gap-3 mb-5">
            <div class="h-px w-10" style="background:linear-gradient(to right,transparent,rgba(201,169,110,0.5))"></div>
            <span class="text-gold/70 text-[9px] font-bold uppercase tracking-[0.55em]">{{ t('objects.cap_badge') }}</span>
            <div class="h-px w-10" style="background:linear-gradient(to left,transparent,rgba(201,169,110,0.5))"></div>
          </div>
          <h2 class="font-black text-white leading-none"
            style="font-size:clamp(28px,3.5vw,52px);letter-spacing:-0.02em">
            {{ t('objects.cap_badge') }}
          </h2>
        </div>

        <div class="grid lg:grid-cols-2 gap-6">

          <!-- Mining column -->
          <div class="rounded-2xl overflow-hidden relative" style="background:#070B16;border:1px solid rgba(255,255,255,0.06)">
            <div class="h-40 relative overflow-hidden">
              <img :src="asset('/images/hero-17.webp')" alt="Mining" class="w-full h-full object-cover" style="opacity:0.6; object-position:50% 35%"/>
              <div class="absolute inset-0" style="background:linear-gradient(to bottom,rgba(7,11,22,0.3) 0%,rgba(7,11,22,0.92) 100%)"></div>
              <div class="absolute bottom-5 left-6">
                <div class="text-amber-400/80 text-[9px] font-bold uppercase tracking-widest mb-1">{{ t('objects.hero_img2_badge') }}</div>
                <div class="text-white font-black text-xl">{{ t('objects.cap_mining_title') }}</div>
              </div>
            </div>
            <div class="p-6 space-y-4">
              <div v-for="item in capMining" :key="item.title" class="flex gap-4 group">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5"
                  style="background:rgba(251,191,36,0.08);border:1px solid rgba(251,191,36,0.15)">
                  <svg class="w-4 h-4 text-amber-400/70" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" :d="OBJ_ICONS[item.icon]" /></svg>
                </div>
                <div>
                  <div class="text-white font-bold text-sm group-hover:text-amber-400/80 transition-colors">{{ item.title }}</div>
                  <div class="text-white/35 text-xs leading-relaxed mt-0.5">{{ item.desc }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Construction column -->
          <div class="rounded-2xl overflow-hidden relative" style="background:#070B16;border:1px solid rgba(255,255,255,0.06)">
            <div class="h-40 relative overflow-hidden">
              <img :src="asset('/images/hero-13.jpg')" alt="Construction" class="w-full h-full object-cover" style="opacity:0.6; object-position:50% 35%"/>
              <div class="absolute inset-0" style="background:linear-gradient(to bottom,rgba(7,11,22,0.3) 0%,rgba(7,11,22,0.92) 100%)"></div>
              <div class="absolute bottom-5 left-6">
                <div class="text-gold/80 text-[9px] font-bold uppercase tracking-widest mb-1">{{ t('objects.hero_img1_badge') }}</div>
                <div class="text-white font-black text-xl">{{ t('objects.cap_build_title') }}</div>
              </div>
            </div>
            <div class="p-6 space-y-4">
              <div v-for="item in capBuild" :key="item.title" class="flex gap-4 group">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5"
                  style="background:rgba(201,169,110,0.08);border:1px solid rgba(201,169,110,0.15)">
                  <svg class="w-4 h-4 text-gold/70" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" :d="OBJ_ICONS[item.icon]" /></svg>
                </div>
                <div>
                  <div class="text-white font-bold text-sm group-hover:text-gold transition-colors">{{ item.title }}</div>
                  <div class="text-white/35 text-xs leading-relaxed mt-0.5">{{ item.desc }}</div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════
         PIPELINE TIMELINE — 5 stages
    ═══════════════════════════════════════════ -->
    <section class="py-24 border-t border-white/5" style="background:#070B16">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="text-center mb-14">
          <div class="inline-flex items-center gap-3 mb-5">
            <div class="h-px w-10" style="background:linear-gradient(to right,transparent,rgba(201,169,110,0.5))"></div>
            <span class="text-gold/70 text-[9px] font-bold uppercase tracking-[0.55em]">{{ t('objects.pipe_badge') }}</span>
            <div class="h-px w-10" style="background:linear-gradient(to left,transparent,rgba(201,169,110,0.5))"></div>
          </div>
          <h2 class="font-black text-white leading-none"
            style="font-size:clamp(26px,3vw,46px);letter-spacing:-0.02em">
            {{ t('objects.pipe_badge') }}
          </h2>
        </div>

        <div class="grid lg:grid-cols-5 gap-4 relative">
          <!-- Connector line -->
          <div class="hidden lg:block absolute h-px pointer-events-none"
            style="top:40px;left:calc(10% + 28px);right:calc(10% + 28px);
                   background:linear-gradient(90deg,transparent,rgba(201,169,110,0.2),rgba(201,169,110,0.2),transparent)">
          </div>

          <div v-for="stage in pipelineStages" :key="stage.n"
            class="relative rounded-2xl p-6 border border-white/5 hover:border-gold/20 transition-all duration-300 group text-center"
            style="background:#0C1220">
            <!-- Number circle -->
            <div class="w-14 h-14 rounded-full border border-gold/25 flex items-center justify-center mx-auto mb-5 relative z-10"
              style="background:rgba(201,169,110,0.06)">
              <span class="font-black text-lg text-gold">{{ stage.n }}</span>
            </div>
            <div class="text-white font-bold text-sm mb-2 group-hover:text-gold transition-colors">{{ stage.t }}</div>
            <div class="text-white/40 text-xs leading-relaxed">{{ stage.d }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════
         PERFORMANCE METRICS — 6 KPIs
    ═══════════════════════════════════════════ -->
    <section class="py-24 border-t border-white/5" style="background:#0C1220">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="text-center mb-14">
          <div class="inline-flex items-center gap-3 mb-5">
            <div class="h-px w-10" style="background:linear-gradient(to right,transparent,rgba(201,169,110,0.5))"></div>
            <span class="text-gold/70 text-[9px] font-bold uppercase tracking-[0.55em]">{{ t('objects.met_badge') }}</span>
            <div class="h-px w-10" style="background:linear-gradient(to left,transparent,rgba(201,169,110,0.5))"></div>
          </div>
          <h2 class="font-black text-white leading-none"
            style="font-size:clamp(26px,3vw,46px);letter-spacing:-0.02em">{{ t('objects.met_badge') }}</h2>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
          <div v-for="m in perfMetrics" :key="m.l"
            class="rounded-2xl p-6 text-center border border-white/5 hover:border-gold/15 transition-all group"
            style="background:#070B16">
            <svg class="w-6 h-6 text-gold/50 mx-auto mb-3 group-hover:text-gold transition-colors" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" :d="OBJ_ICONS[m.icon]" /></svg>
            <div class="text-gold font-black leading-none mb-2" style="font-size:clamp(20px,2.2vw,32px)">{{ m.v }}</div>
            <div class="text-white/35 text-[10px] uppercase tracking-wider leading-tight">{{ m.l }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════
         ESG + PARTNERS
    ═══════════════════════════════════════════ -->
    <section class="py-24 border-t border-white/5" style="background:#070B16">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-10 items-stretch">

          <!-- ESG block -->
          <div class="rounded-2xl relative overflow-hidden" style="background:#0C1220;border:1px solid rgba(255,255,255,0.06)">
            <div class="h-32 relative overflow-hidden">
              <img :src="asset('/images/hero-16.jpg')" alt="ESG" class="w-full h-full object-cover" style="opacity:0.5"/>
              <div class="absolute inset-0" style="background:linear-gradient(to bottom,transparent,rgba(12,18,32,0.95))"></div>
            </div>
            <div class="p-7 pt-4">
              <div class="text-gold/70 text-[9px] font-bold uppercase tracking-[0.45em] mb-2">{{ t('objects.esg_badge') }}</div>
              <h3 class="text-white font-bold text-xl mb-3">{{ t('objects.esg_title') }}</h3>
              <p class="text-white/45 text-sm leading-relaxed mb-5">{{ t('objects.esg_desc') }}</p>
              <ul class="space-y-2 mb-6">
                <li v-for="ei in esgItems" :key="ei"
                  class="flex items-center gap-3 text-white/60 text-sm">
                  <Icon name="check" class="w-4 h-4 text-gold/60 flex-shrink-0" :stroke-width="2.5" />
                  {{ ei }}
                </li>
              </ul>
              <a href="/about"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border font-bold text-xs uppercase tracking-wider transition-all hover:bg-white/5"
                style="border-color:rgba(201,169,110,0.25);color:rgba(201,169,110,0.8)">
                {{ t('objects.esg_cta') }} →
              </a>
            </div>
          </div>

          <!-- Partners block -->
          <div class="rounded-2xl p-7" style="background:#0C1220;border:1px solid rgba(255,255,255,0.06)">
            <div class="text-gold/70 text-[9px] font-bold uppercase tracking-[0.45em] mb-2">{{ t('objects.partner_badge') }}</div>
            <h3 class="text-white font-bold text-xl mb-8">{{ t('objects.partner_title') }}</h3>

            <!-- Partner logos grid -->
            <div class="grid grid-cols-4 gap-4">
              <div v-for="partner in partnerLogos" :key="partner.name"
                class="flex items-center justify-center rounded-xl p-4 border border-white/5 hover:border-gold/15 transition-all"
                style="background:rgba(255,255,255,0.02); aspect-ratio:2/1">
                <span class="text-white/35 text-xs font-black uppercase tracking-wider text-center leading-tight hover:text-white/60 transition-colors">{{ partner.name }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════
         FINAL CTA — "Build the Future Together"
    ═══════════════════════════════════════════ -->
    <section class="relative py-32 overflow-hidden border-t border-white/5">
      <div class="absolute inset-0">
        <img :src="asset('/images/hero-3.jpg')" alt="" class="w-full h-full object-cover" style="opacity:0.22; object-position:50% 60%"/>
        <div class="absolute inset-0" style="background:linear-gradient(135deg,rgba(7,11,22,0.95) 0%,rgba(7,11,22,0.78) 100%)"></div>
      </div>
      <!-- Gold glow -->
      <div class="absolute pointer-events-none inset-0 flex items-center justify-center">
        <div style="width:700px;height:400px;background:radial-gradient(ellipse,rgba(201,169,110,0.07) 0%,transparent 65%);border-radius:50%"></div>
      </div>
      <div class="relative z-10 max-w-[900px] mx-auto px-6 text-center">
        <div class="inline-flex items-center gap-3 mb-8">
          <div class="h-px w-14" style="background:linear-gradient(to right,transparent,rgba(201,169,110,0.5))"></div>
          <span class="text-gold/60 text-[9px] font-bold uppercase tracking-[0.55em]">QUDRAT</span>
          <div class="h-px w-14" style="background:linear-gradient(to left,transparent,rgba(201,169,110,0.5))"></div>
        </div>
        <h2 class="font-bold text-white leading-[0.95] mb-6"
          style="font-family:'Cormorant Garamond',Georgia,serif;font-size:clamp(28px,3vw,48px);letter-spacing:-0.01em">
          {{ t('objects.fcta_title') }}
        </h2>
        <p class="text-white/45 text-base lg:text-lg leading-relaxed mb-12 max-w-xl mx-auto">
          {{ t('objects.fcta_sub') }}
        </p>
        <div class="flex flex-wrap items-center justify-center gap-4">
          <a href="/contacts"
            class="inline-flex items-center gap-2 px-9 py-4 rounded-xl font-bold text-sm uppercase tracking-wider transition-all hover:opacity-90"
            style="background:#C9A96E;color:#080D18">
            {{ t('objects.fcta_btn1') }}
            <Icon name="arrow-right" class="w-4 h-4" :stroke-width="2.5" />
          </a>
          <a href="/contacts"
            class="inline-flex items-center gap-2 px-9 py-4 rounded-xl font-bold text-sm uppercase tracking-wider border transition-all hover:bg-white/5"
            style="border-color:rgba(201,169,110,0.3);color:rgba(201,169,110,0.8)">
            <Icon name="phone" class="w-4 h-4" :stroke-width="2" />
            {{ t('objects.fcta_btn2') }}
          </a>
          <a href="#"
            class="inline-flex items-center gap-2 px-9 py-4 rounded-xl font-bold text-sm uppercase tracking-wider border transition-all hover:bg-white/5"
            style="border-color:rgba(255,255,255,0.12);color:rgba(255,255,255,0.55)">
            <Icon name="arrow-down-tray" class="w-4 h-4" :stroke-width="2" />
            {{ t('objects.fcta_btn3') }}
          </a>
        </div>
      </div>
    </section>

    <!-- ═══ MOBILE FILTER BOTTOM SHEET ═══ -->
    <Teleport to="body">
      <Transition name="sheet-fade">
        <div v-if="mobileFilters" class="fixed inset-0 z-50 lg:hidden">
          <!-- Backdrop -->
          <div class="absolute inset-0 bg-black/65" style="backdrop-filter:blur(4px)" @click="mobileFilters = false"></div>
          <!-- Drawer -->
          <div class="absolute bottom-0 inset-x-0 rounded-t-3xl sheet-slide overflow-hidden"
            style="background:#0C1220; border-top:1px solid rgba(255,255,255,0.08); max-height:90svh">
            <!-- Handle -->
            <div class="flex justify-center pt-3 pb-1 cursor-pointer" @click="mobileFilters = false">
              <div class="w-10 h-1 rounded-full bg-white/15"></div>
            </div>
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-white/5">
              <h3 class="text-white font-bold text-base">{{ t('objects.filter_title') }}</h3>
              <div class="flex items-center gap-4">
                <button v-if="hasFilters" @click="resetFilters"
                  class="text-gold text-xs font-semibold hover:opacity-70 transition-opacity">
                  {{ t('objects.filter_reset') }}
                </button>
                <button @click="mobileFilters = false" class="text-white/30 hover:text-white transition-colors">
                  <Icon name="close" class="w-5 h-5" :stroke-width="2" />
                </button>
              </div>
            </div>
            <!-- Scrollable filters -->
            <div class="overflow-y-auto" style="max-height:calc(90svh - 160px)">
              <div class="px-6 py-5 space-y-6">

                <!-- Rooms -->
                <div>
                  <h4 class="text-[10px] font-bold tracking-[0.25em] text-white/40 uppercase mb-3">{{ t('objects.filter_rooms') }}</h4>
                  <div class="grid grid-cols-4 gap-2">
                    <button v-for="r in roomOptions" :key="r.val" @click="toggleRoom(r.val)"
                      :class="['py-3 rounded-xl border text-sm font-bold transition-all', selectedRooms.includes(r.val)
                        ? 'border-gold bg-gold/10 text-gold'
                        : 'border-white/8 text-white/40 hover:border-white/20']">
                      {{ r.label }}
                    </button>
                  </div>
                </div>

                <!-- Status -->
                <div>
                  <h4 class="text-[10px] font-bold tracking-[0.25em] text-white/40 uppercase mb-3">{{ t('objects.filter_status') }}</h4>
                  <div class="flex flex-wrap gap-2">
                    <button v-for="s in statusOptions" :key="s.val" @click="toggleStatus(s.val)"
                      :class="['px-4 py-2.5 rounded-xl border text-sm font-semibold transition-all', selectedStatuses.includes(s.val)
                        ? 'border-gold bg-gold/10 text-gold'
                        : 'border-white/8 text-white/40']">
                      {{ s.label }}
                    </button>
                  </div>
                </div>

                <!-- Finish -->
                <div>
                  <h4 class="text-[10px] font-bold tracking-[0.25em] text-white/40 uppercase mb-3">{{ t('objects.filter_finish') }}</h4>
                  <div class="flex flex-wrap gap-2">
                    <button v-for="f in finishOptions" :key="f.val" @click="toggleFinish(f.val)"
                      :class="['px-4 py-2.5 rounded-xl border text-sm font-medium transition-all', selectedFinish.includes(f.val)
                        ? 'border-gold bg-gold/10 text-gold'
                        : 'border-white/8 text-white/40']">
                      {{ f.label }}
                    </button>
                  </div>
                </div>

                <!-- Price range -->
                <div>
                  <h4 class="text-[10px] font-bold tracking-[0.25em] text-white/40 uppercase mb-3">{{ t('objects.filter_price') }}</h4>
                  <div class="grid grid-cols-2 gap-3">
                    <input v-model.number="priceFrom" type="number" :placeholder="t('objects.bar_price_from')" class="mob-input"/>
                    <input v-model.number="priceTo"   type="number" :placeholder="t('objects.bar_price_to')"   class="mob-input"/>
                  </div>
                </div>

                <!-- Area range -->
                <div>
                  <h4 class="text-[10px] font-bold tracking-[0.25em] text-white/40 uppercase mb-3">{{ t('objects.filter_area') }}</h4>
                  <div class="grid grid-cols-2 gap-3">
                    <input v-model.number="areaFrom" type="number" :placeholder="t('objects.bar_price_from')" class="mob-input"/>
                    <input v-model.number="areaTo"   type="number" :placeholder="t('objects.bar_price_to')"   class="mob-input"/>
                  </div>
                </div>

                <!-- Floor range -->
                <div>
                  <h4 class="text-[10px] font-bold tracking-[0.25em] text-white/40 uppercase mb-3">{{ t('objects.filter_floor') }}</h4>
                  <div class="grid grid-cols-2 gap-3">
                    <input v-model.number="floorFrom" type="number" placeholder="от 1" min="1" class="mob-input"/>
                    <input v-model.number="floorTo"   type="number" :placeholder="`до ${stats.floors || '—'}`" class="mob-input"/>
                  </div>
                </div>

              </div>
            </div>
            <!-- Footer: Apply / Reset -->
            <div class="px-6 py-4 border-t border-white/5" style="background:#0C1220">
              <div class="flex gap-3">
                <button @click="resetFilters"
                  class="flex-1 py-3.5 rounded-xl border border-white/10 text-white/50 text-sm font-semibold hover:border-white/25 hover:text-white transition-all">
                  {{ t('objects.filter_reset') }}
                </button>
                <button @click="mobileFilters = false"
                  class="flex-2 px-6 py-3.5 rounded-xl text-sm font-bold transition-all"
                  style="background:#C9A96E; color:#0A0800; flex:2">
                  {{ t('objects.filter_found') }} {{ filtered.length }} {{ t('objects.filter_apts') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- ═══ RESERVATION MODAL ═══ -->
    <!-- Модалка бронирования — общий компонент (переиспользуется на странице квартиры) -->
    <ReserveModal v-model="reserveModal" :apartment="selectedApt" />

  </MainLayout>
</template>

<script setup>
import { ref, computed, reactive, watch } from 'vue'
import { Link, router, usePage, useForm } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'
import Icon from '@/Components/Icon.vue'
import ReserveModal from '@/Components/ReserveModal.vue'
import { useAsset } from '@/composables/useAsset'
import { useTrans } from '@/composables/useTrans'
import { hexToRgba } from '@/showcaseIcons'

const props = defineProps({
  apartments:       { type: Array,  default: () => [] },
  stats:            { type: Object, default: () => ({}) },
  updates:          { type: Array,  default: () => [] },
  overallProgress:  { type: Number, default: 0 },
  // Знаковые объекты — из БД (админка → CMS → «Знаковые объекты»)
  featuredProjects: { type: Array,  default: () => [] },
})

const { asset } = useAsset()
const { t } = useTrans()
const page = usePage()

// Contact phone from shared props (for CTA call button)
const contactPhone = computed(() => page.props.contacts?.phone ?? '+992 00 000 00 00')

// ── Hero stats — reactive to locale ──
const heroStats = computed(() => [
  { val: '16',   label: t('objects.stat_floors') },
  { val: '2026', label: t('objects.stat_year') },
  { val: '24/7', label: t('objects.stat_security') },
  { val: '5+',   label: t('objects.stat_layouts') },
])

// ── Hero flagship card stats ──
const cardStats = computed(() => [
  { val: '16',     label: t('objects.hero_card_floors') },
  { val: '2026',   label: t('objects.hero_card_year') },
  { val: '45 м²',  label: t('objects.hero_card_area') },
  { val: '$950/м²',label: t('objects.hero_card_price') },
])

// ── Filter options — reactive to locale ──
const finishOptions = computed(() => [
  { val: 'none',      label: t('objects.finish_none') },
  { val: 'rough',     label: t('objects.finish_rough') },
  { val: 'fine',      label: t('objects.finish_fine') },
  { val: 'furnished', label: t('objects.finish_furnished') },
])

const statusOptions = computed(() => [
  { val: 'available', label: t('objects.status_available'), color: 'text-emerald-400' },
  { val: 'reserved',  label: t('objects.status_reserved'),  color: 'text-amber-400' },
  { val: 'sold',      label: t('objects.status_sold'),       color: 'text-gray-400' },
])


// ── Filter state ──
const selectedRooms    = ref([])
const selectedFinish   = ref([])
const selectedStatuses = ref([])
const priceFrom = ref(null)
const priceTo   = ref(null)
const areaFrom  = ref(null)
const areaTo    = ref(null)
const floorFrom = ref(null)
const floorTo   = ref(null)
const sortBy    = ref('rooms_asc')
const viewMode  = ref('grid')
const mobileFilters = ref(false)

// Pagination
const perPage     = ref(12)
const currentPage = ref(1)

const roomOptions = [
  { val: 1, label: '1' },
  { val: 2, label: '2' },
  { val: 3, label: '3' },
  { val: 4, label: '4+' },
]

// ── Helpers ──
function fmtPrice(n) {
  if (!n) return '0'
  return Number(n).toLocaleString('ru-RU')
}
function roomLabel(n) {
  const map = {
    1: t('objects.room_1'),
    2: t('objects.room_2'),
    3: t('objects.room_3'),
    4: t('objects.room_4'),
  }
  return map[n] || `${n}${t('objects.room_n')}`
}
function finishLabel(f) {
  const map = {
    none:      t('objects.finish_none'),
    rough:     t('objects.finish_rough'),
    fine:      t('objects.finish_fine'),
    furnished: t('objects.finish_furnished'),
  }
  return map[f] || f
}
function statusLabel(s) {
  const map = {
    available: t('objects.status_available'),
    reserved:  t('objects.status_reserved'),
    sold:      t('objects.status_sold'),
  }
  return map[s] || s
}
function statusBadge(s) {
  if (s === 'available') return 'bg-emerald-500/20 border border-emerald-500/40 text-emerald-400'
  if (s === 'reserved')  return 'bg-amber-500/20 border border-amber-500/40 text-amber-400'
  return 'bg-gray-500/15 border border-gray-500/30 text-gray-500'
}

function toggleRoom(val) {
  const i = selectedRooms.value.indexOf(val)
  if (i >= 0) selectedRooms.value.splice(i, 1)
  else selectedRooms.value.push(val)
  currentPage.value = 1
}
function toggleStatus(val) {
  const i = selectedStatuses.value.indexOf(val)
  if (i >= 0) selectedStatuses.value.splice(i, 1)
  else selectedStatuses.value.push(val)
  currentPage.value = 1
}

function toggleFinish(val) {
  const i = selectedFinish.value.indexOf(val)
  if (i >= 0) selectedFinish.value.splice(i, 1)
  else selectedFinish.value.push(val)
  currentPage.value = 1
}

function resetFilters() {
  selectedRooms.value    = []
  selectedFinish.value   = []
  selectedStatuses.value = []
  priceFrom.value = null
  priceTo.value   = null
  areaFrom.value  = null
  areaTo.value    = null
  floorFrom.value = null
  floorTo.value   = null
  currentPage.value = 1
}

const hasFilters = computed(() =>
  selectedRooms.value.length || selectedFinish.value.length ||
  selectedStatuses.value.length || priceFrom.value || priceTo.value ||
  areaFrom.value || areaTo.value || floorFrom.value || floorTo.value
)

// ── Filtering + sorting ──
const filtered = computed(() => {
  let list = [...props.apartments]

  if (selectedRooms.value.length) {
    list = list.filter(a => {
      if (selectedRooms.value.includes(4)) {
        return selectedRooms.value.some(r => r === 4 ? a.rooms >= 4 : a.rooms === r)
      }
      return selectedRooms.value.includes(a.rooms)
    })
  }
  if (selectedFinish.value.length)   list = list.filter(a => selectedFinish.value.includes(a.finish))
  if (selectedStatuses.value.length) list = list.filter(a => selectedStatuses.value.includes(a.status))
  if (priceFrom.value)   list = list.filter(a => Number(a.price) >= priceFrom.value)
  if (priceTo.value)     list = list.filter(a => Number(a.price) <= priceTo.value)
  if (areaFrom.value)    list = list.filter(a => Number(a.area) >= areaFrom.value)
  if (areaTo.value)      list = list.filter(a => Number(a.area) <= areaTo.value)
  if (floorFrom.value)   list = list.filter(a => a.floor >= floorFrom.value)
  if (floorTo.value)     list = list.filter(a => a.floor <= floorTo.value)

  // Sort
  const [field, dir] = sortBy.value.split('_')
  const mul = dir === 'desc' ? -1 : 1
  const key = field === 'rooms' ? 'rooms' : field === 'price' ? 'price' : field === 'area' ? 'area' : 'floor'
  list.sort((a, b) => (Number(a[key]) - Number(b[key])) * mul)

  return list
})

const totalPages = computed(() => Math.ceil(filtered.value.length / perPage.value))

const paginated = computed(() => {
  const start = (currentPage.value - 1) * perPage.value
  return filtered.value.slice(start, start + perPage.value)
})

watch(filtered, () => { currentPage.value = 1 }, { deep: false })

// ── Генплан ──
const planZones = computed(() => [
  { label: t('objects.plan_zone1'), color: '#6482D2' },
  { label: t('objects.plan_zone2'), color: '#C9A96E' },
  { label: t('objects.plan_zone3'), color: '#50B464' },
  { label: t('objects.plan_zone4'), color: '#50C8DC' },
  { label: t('objects.plan_zone5'), color: '#A064DC' },
  { label: t('objects.plan_zone6'), color: '#A0AAB4' },
])

const planAdvantages = computed(() => [
  { title: t('objects.plan_adv1_title'), desc: t('objects.plan_adv1_desc') },
  { title: t('objects.plan_adv2_title'), desc: t('objects.plan_adv2_desc') },
  { title: t('objects.plan_adv3_title'), desc: t('objects.plan_adv3_desc') },
])

const planMetrics = computed(() => [
  { value: t('objects.plan_m1_v'), label: t('objects.plan_m1_l') },
  { value: t('objects.plan_m2_v'), label: t('objects.plan_m2_l') },
  { value: t('objects.plan_m3_v'), label: t('objects.plan_m3_l') },
  { value: t('objects.plan_m4_v'), label: t('objects.plan_m4_l') },
])


// ── Construction progress (merged from Progress page) ──
const progressCurrentDate = computed(() => {
  const cur = props.updates.find(u => u.is_current) ?? props.updates[0]
  return cur?.date ?? ''
})

const progressPhases = computed(() => [
  { name: t('progress.ph1_name'), pct: 100, color: '#22c55e' },
  { name: t('progress.ph2_name'), pct: 60,  color: '#C9A96E' },
  { name: t('progress.ph3_name'), pct: 15,  color: '#60a5fa' },
  { name: t('progress.ph4_name'), pct: 0,   color: 'rgba(255,255,255,0.2)' },
  { name: t('progress.ph5_name'), pct: 10,  color: '#a78bfa' },
  { name: t('progress.ph6_name'), pct: 0,   color: 'rgba(255,255,255,0.2)' },
])

const progressSiteStats = computed(() => [
  { value: t('progress.ss1_v'), label: t('progress.ss1_l') },
  { value: t('progress.ss2_v'), label: t('progress.ss2_l') },
  { value: t('progress.ss3_v'), label: t('progress.ss3_l') },
  { value: t('progress.ss4_v'), label: t('progress.ss4_l') },
])

const progressMonthlyUpdates = computed(() => {
  if (props.updates.length) {
    return props.updates.map(u => ({
      date:     u.date,
      current:  u.is_current,
      progress: u.progress,
      title:    u.title,
      desc:     u.description ?? '',
    }))
  }
  // Static fallback when DB is empty
  return [
    { date: t('progress.upd1_date'), current: true,  progress: 34, title: t('progress.upd1_title'), desc: t('progress.upd1_desc') },
    { date: t('progress.upd2_date'), current: false, progress: 31, title: t('progress.upd2_title'), desc: t('progress.upd2_desc') },
    { date: t('progress.upd3_date'), current: false, progress: 27, title: t('progress.upd3_title'), desc: t('progress.upd3_desc') },
    { date: t('progress.upd4_date'), current: false, progress: 22, title: t('progress.upd4_title'), desc: t('progress.upd4_desc') },
    { date: t('progress.upd5_date'), current: false, progress: 0,  title: t('progress.upd5_title'), desc: t('progress.upd5_desc') },
  ]
})

const progressMilestones = computed(() => [
  { title: t('progress.ms1_title'), desc: t('progress.ms1_desc'), period: t('progress.ms1_period'), done: true,  active: false },
  { title: t('progress.ms2_title'), desc: t('progress.ms2_desc'), period: t('progress.ms2_period'), done: false, active: true  },
  { title: t('progress.ms3_title'), desc: t('progress.ms3_desc'), period: t('progress.ms3_period'), done: false, active: false },
  { title: t('progress.ms4_title'), desc: t('progress.ms4_desc'), period: t('progress.ms4_period'), done: false, active: false },
])

// Newsletter subscribe form (reused from Progress)
const newsletterForm = useForm({ email: '' })
const subSent = ref(false)

const subEmail = computed({
  get: () => newsletterForm.email,
  set: (v) => { newsletterForm.email = v },
})

function subscribeProgress() {
  newsletterForm.post(route('newsletter.subscribe'), {
    preserveScroll: true,
    onSuccess: () => {
      subSent.value = true
      newsletterForm.reset()
      setTimeout(() => { subSent.value = false }, 4000)
    },
  })
}

// ── Reservation modal ──
// Бронирование — состояние вынесено в общий компонент ReserveModal.
const reserveModal = ref(false)
const selectedApt  = ref(null)

function openReserve(apt) {
  selectedApt.value = apt
  reserveModal.value = true
}


// ── Hero KPIs ──
const heroKpis = computed(() => [
  { v: t('objects.hero_kpi1_v'), l: t('objects.hero_kpi1_l') },
  { v: t('objects.hero_kpi2_v'), l: t('objects.hero_kpi2_l') },
  { v: t('objects.hero_kpi3_v'), l: t('objects.hero_kpi3_l') },
  { v: t('objects.hero_kpi4_v'), l: t('objects.hero_kpi4_l') },
  { v: t('objects.hero_kpi5_v'), l: t('objects.hero_kpi5_l') },
])

// ── Category Tabs ──
const activeTab = ref('all')
const projectTabs = computed(() => [
  { key: 'all',         label: t('objects.cat_all') },
  { key: 'residential', label: t('objects.cat_residential') },
  { key: 'commercial',  label: t('objects.cat_commercial') },
  { key: 'mining',      label: t('objects.cat_mining_tab') },
  { key: 'infra',       label: t('objects.cat_infra_tab') },
  { key: 'energy',      label: t('objects.cat_energy') },
])

// Featured Projects («Знаковые объекты») приходят пропом featuredProjects из БД.

// ── OBJ_ICONS — SVG path map for capabilities & metrics ──
const OBJ_ICONS = {
  // Capabilities — mining
  pick:      'M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l5.654-4.654m5.664-5.803a9.373 9.373 0 0 0-1.636-1.56',
  truck:     'M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12',
  cog:       'M4.5 12a7.5 7.5 0 0 0 15 0m-15 0a7.5 7.5 0 1 1 15 0m-15 0H3m16.5 0H21m-1.5 0H12m-8.457 3.077 1.41-.513m14.095-5.13 1.41-.513M5.106 17.785l1.15-.964m11.49-9.642 1.149-.964M7.501 19.795l.75-1.3m7.5-12.99.75-1.3m-6.063 16.658.26-1.477m2.605-14.772.26-1.477m0 17.726-.26-1.477M10.698 4.614l-.26-1.477M16.5 19.794l-.75-1.299M7.5 4.205 12 12m6.894 5.785-1.149-.964M6.256 7.178l-1.15-.964m15.352 8.864-1.41-.513M4.954 9.435l-1.41-.514M12.002 12l-3.75 6.495',
  shield2:   'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z',
  // Capabilities — construction
  pencil:    'm16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10',
  hammer:    'M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l5.654-4.654m5.664-5.803a9.373 9.373 0 0 0-1.636-1.56',
  clipboard: 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z',
  key:       'M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z',
  // Performance metrics icons
  chart:     'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z',
  percent:   'M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185ZM9.75 9h.008v.008H9.75V9Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 4.5h.008v.008h-.008V13.5Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z',
  check2:    'M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z',
  mountain:  'm2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z',
  clock:     'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
  users:     'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z',
}

// ── Capabilities ──
const capMining = computed(() => [
  { icon: 'pick',    title: t('objects.cap_m1'), desc: t('objects.cap_m1d') },
  { icon: 'truck',   title: t('objects.cap_m2'), desc: t('objects.cap_m2d') },
  { icon: 'cog',     title: t('objects.cap_m3'), desc: t('objects.cap_m3d') },
  { icon: 'shield2', title: t('objects.cap_m4'), desc: t('objects.cap_m4d') },
])

const capBuild = computed(() => [
  { icon: 'pencil',    title: t('objects.cap_b1'), desc: t('objects.cap_b1d') },
  { icon: 'hammer',    title: t('objects.cap_b2'), desc: t('objects.cap_b2d') },
  { icon: 'clipboard', title: t('objects.cap_b3'), desc: t('objects.cap_b3d') },
  { icon: 'key',       title: t('objects.cap_b4'), desc: t('objects.cap_b4d') },
])

// ── Pipeline Stages ──
const pipelineStages = computed(() => [
  { n: t('objects.pipe_s1_n'), t: t('objects.pipe_s1_t'), d: t('objects.pipe_s1_d') },
  { n: t('objects.pipe_s2_n'), t: t('objects.pipe_s2_t'), d: t('objects.pipe_s2_d') },
  { n: t('objects.pipe_s3_n'), t: t('objects.pipe_s3_t'), d: t('objects.pipe_s3_d') },
  { n: t('objects.pipe_s4_n'), t: t('objects.pipe_s4_t'), d: t('objects.pipe_s4_d') },
  { n: t('objects.pipe_s5_n'), t: t('objects.pipe_s5_t'), d: t('objects.pipe_s5_d') },
])

// ── Performance Metrics ──
const perfMetrics = computed(() => [
  { v: t('objects.met1_v'), l: t('objects.met1_l'), icon: 'chart'    },
  { v: t('objects.met2_v'), l: t('objects.met2_l'), icon: 'percent'  },
  { v: t('objects.met3_v'), l: t('objects.met3_l'), icon: 'check2'   },
  { v: t('objects.met4_v'), l: t('objects.met4_l'), icon: 'mountain' },
  { v: t('objects.met5_v'), l: t('objects.met5_l'), icon: 'clock'    },
  { v: t('objects.met6_v'), l: t('objects.met6_l'), icon: 'users'    },
])

// ── ESG Items ──
const esgItems = computed(() => [
  t('objects.esg_i1'),
  t('objects.esg_i2'),
  t('objects.esg_i3'),
  t('objects.esg_i4'),
])

// ── Partner Logos ──
const partnerLogos = computed(() => [
  { name: 'Tajik Mining' },
  { name: 'IFC' },
  { name: 'EBRD' },
  { name: 'Tajiksanoatbank' },
  { name: 'USAID' },
  { name: 'Agrobank' },
  { name: 'Aga Khan Fund' },
  { name: 'KOICA' },
])
</script>

<style scoped>
/* ════════════════════════════════════════════
   Objects Hero v5 — Premium Split Layout
   Left 35% dark panel · Right 65% image
   ════════════════════════════════════════════ */

/* Stage: flex column so glass bar sits at bottom naturally */
.oh-stage {
  position: relative;
  display: flex;
  flex-direction: column;
  height: 100vh;
  min-height: 700px;
  overflow: hidden;
  background: #020B1F;
}

/* ── Background image (right 65%) ── */
.oh-img-wrap {
  position: absolute;
  top: 0; bottom: 0;
  left: 32%;
  right: 0;
  z-index: 1;
}
.oh-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: 50% 30%;
  display: block;
}

/* ── Left-to-right dark gradient overlay ── */
.oh-left-ol {
  position: absolute;
  inset: 0;
  z-index: 2;
  pointer-events: none;
  background: linear-gradient(
    to right,
    #020B1F 0%,
    #020B1F 28%,
    rgba(2,11,31,0.92) 38%,
    rgba(2,11,31,0.55) 52%,
    rgba(2,11,31,0.15) 68%,
    transparent 100%
  );
}

/* ── Content wrapper — grows to fill remaining space, centers content ── */
.oh-content-wrap {
  position: relative;
  z-index: 10;
  flex: 1;
  display: flex;
  align-items: center;
  width: 100%;
  padding-top: 88px;    /* clear the navbar */
  padding-bottom: 16px;
}

/* ── Inner content container — matches navbar width ── */
.oh-content {
  width: 100%;
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 24px;
}
@media (min-width: 1024px) { .oh-content { padding: 0 48px; } }

/* Text column limited to 520px so it stays in the dark zone */
.oh-inner { max-width: 520px; width: 100%; }

/* ── Eyebrow ── */
.oh-ew {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 30px;
}
.oh-ew-line {
  display: block;
  width: 36px;
  height: 1px;
  background: #C9A96E;
  opacity: 0.75;
  flex-shrink: 0;
}
.oh-ew-txt {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.48em;
  color: #C9A96E;
  text-transform: uppercase;
}

/* ── Heading — 4 stacked lines ── */
.oh-h1 {
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-weight: 700;
  line-height: 0.91;
  letter-spacing: 0.025em;
  text-transform: uppercase;
  margin-bottom: 26px;
  font-size: clamp(46px, 5.8vw, 90px);
  display: flex;
  flex-direction: column;
  gap: 0;
}
.oh-h1-w {
  color: #ffffff;
  display: block;
}
.oh-h1-g {
  display: block;
  background: linear-gradient(135deg, #D4AF37 0%, #C9A96E 45%, #B8860B 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* ── Subtitle ── */
.oh-sub {
  color: rgba(255,255,255,0.48);
  font-size: clamp(13px, 1.05vw, 15.5px);
  line-height: 1.72;
  max-width: 400px;
  margin-bottom: 38px;
  font-weight: 400;
}

/* ── CTA buttons ── */
.oh-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}
.oh-cta-p {
  display: inline-flex;
  align-items: center;
  gap: 9px;
  background: linear-gradient(135deg, #C9A96E 0%, #D4AF37 100%);
  color: #080D18;
  font-size: 10.5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  padding: 14px 32px;
  border-radius: 10px;
  text-decoration: none;
  transition: opacity 0.22s, transform 0.22s;
}
.oh-cta-p:hover { opacity: 0.87; transform: translateY(-1px); }

.oh-cta-s {
  display: inline-flex;
  align-items: center;
  gap: 9px;
  border: 1px solid rgba(201,169,110,0.32);
  color: rgba(201,169,110,0.82);
  font-size: 10.5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  padding: 14px 32px;
  border-radius: 10px;
  text-decoration: none;
  transition: background 0.22s, border-color 0.22s, transform 0.22s;
}
.oh-cta-s:hover {
  background: rgba(201,169,110,0.07);
  border-color: rgba(201,169,110,0.55);
  transform: translateY(-1px);
}

/* ════════════════════════════════════════════
   Glassmorphism feature bar — bottom
   ════════════════════════════════════════════ */
.oh-glass-bar {
  position: relative;
  z-index: 20;
  background: rgba(4, 10, 24, 0.78);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border-top: 1px solid rgba(255,255,255,0.07);
}
.oh-glass-in {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 24px;
  display: flex;
  align-items: stretch;
}
@media (min-width: 1024px) { .oh-glass-in { padding: 0 48px; } }

.oh-feat {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 24px 20px 24px 0;
}

.oh-feat-icon {
  width: 42px;
  height: 42px;
  border-radius: 11px;
  background: rgba(201,169,110,0.10);
  border: 1px solid rgba(201,169,110,0.18);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.oh-feat-title {
  font-size: 13px;
  font-weight: 600;
  color: #ffffff;
  margin-bottom: 3px;
  white-space: nowrap;
}
.oh-feat-sub {
  font-size: 11px;
  color: rgba(255,255,255,0.42);
  line-height: 1.4;
}

.oh-feat-div {
  width: 1px;
  align-self: stretch;
  margin: 16px 24px 16px 0;
  background: rgba(255,255,255,0.07);
  flex-shrink: 0;
}

/* ── Mobile ── */
@media (max-width: 767px) {
  .oh-stage    { height: auto; min-height: 100svh; }
  .oh-img-wrap { left: 0; opacity: 0.18; }
  .oh-left-ol  {
    background: linear-gradient(
      to right,
      #020B1F 0%, #020B1F 60%,
      rgba(2,11,31,0.85) 80%, rgba(2,11,31,0.4) 100%
    );
  }
  .oh-h1       { font-size: clamp(46px, 13vw, 72px); }
  .oh-inner    { max-width: 100%; }
  .oh-glass-in { flex-direction: column; gap: 0; padding-bottom: 8px; }
  .oh-feat     { width: 100%; padding: 16px 0; border-bottom: 1px solid rgba(255,255,255,0.06); }
  .oh-feat:last-child { border-bottom: none; }
  .oh-feat-div { display: none; }
  .oh-feat-title { white-space: normal; }
}

/* ── Tablet ── */
@media (min-width: 768px) and (max-width: 1023px) {
  .oh-img-wrap { left: 42%; }
  .oh-h1       { font-size: clamp(46px, 7vw, 72px); }
  .oh-inner    { max-width: 380px; }
}

.res-label {
  display: block;
  font-size: 0.68rem;
  font-weight: 600;
  color: rgba(255,255,255,0.35);
  text-transform: uppercase;
  letter-spacing: 0.1em;
  margin-bottom: 0.375rem;
}
.res-input {
  display: block;
  width: 100%;
  padding: 0.7rem 0.875rem;
  border-radius: 0.75rem;
  font-size: 0.875rem;
  color: #fff;
  background: #080E18;
  border: 1px solid rgba(255,255,255,0.07);
  outline: none;
  transition: border-color 0.2s;
}
.res-input:focus { border-color: rgba(201,169,110,0.45); }
.res-input::placeholder { color: rgba(255,255,255,0.18); }
option { background: #080E18; }

.modal-enter-active, .modal-leave-active { transition: opacity 0.2s; }
.modal-enter-from, .modal-leave-to { opacity: 0; }

/* ── Mobile bottom sheet ── */
.sheet-fade-enter-active,
.sheet-fade-leave-active { transition: opacity 0.25s ease; }
.sheet-fade-enter-from,
.sheet-fade-leave-to { opacity: 0; }

.sheet-fade-enter-active .sheet-slide { animation: sheetSlideUp 0.3s cubic-bezier(0.32, 0.72, 0, 1); }
.sheet-fade-leave-active .sheet-slide { animation: sheetSlideDown 0.22s ease-in; }

@keyframes sheetSlideUp   { from { transform: translateY(100%); } to { transform: translateY(0); } }
@keyframes sheetSlideDown { from { transform: translateY(0); }    to { transform: translateY(100%); } }

/* Mobile inputs */
.mob-input {
  display: block; width: 100%;
  padding: 0.75rem 0.875rem;
  border-radius: 0.75rem;
  font-size: 0.875rem;
  color: #fff;
  background: #080E18;
  border: 1px solid rgba(255,255,255,0.08);
  outline: none;
  transition: border-color 0.2s;
}
.mob-input:focus { border-color: rgba(201,169,110,0.45); }
.mob-input::placeholder { color: rgba(255,255,255,0.2); }
</style>
