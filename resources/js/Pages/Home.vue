<template>
  <MainLayout>
    <SeoHead
      :title="t('home.meta_title')"
      :ogTitle="t('home.meta_title_og')"
      :description="t('hero.subtitle')"
      image="/images/og-home.jpg"
      path="/"
    />

    <!-- ═══ 1. PREMIUM HERO — full-bleed ═══ -->
    <section class="ph-stage" ref="heroSection">

      <!-- Full-bleed background image -->
      <div class="ph-bg">
        <img :src="asset('/images/hero-20.webp')" alt="QUDRAT CITY" class="ph-bg-img"/>
      </div>

      <!-- Gradient overlays -->
      <div class="ph-bg-ol"></div>
      <div class="ph-bg-ob"></div>

      <!-- Main content: full-width container aligned with navbar -->
      <div class="ph-content">
        <!-- Inner: limits text column width, starts at same X as navbar logo -->
        <div class="ph-inner">

          <!-- Eyebrow: line — text — line -->
          <div class="ph-ew2">
            <span class="ph-ew2-line"></span>
            <span class="ph-ew2-txt">{{ t('home.ph_eyebrow') }}</span>
            <span class="ph-ew2-line"></span>
          </div>

          <!-- ALL-CAPS heading: 3 lines -->
          <h1 class="ph-h2">
            <span class="ph-h2-l1">{{ t('home.ph_l1') }}</span>
            <span class="ph-h2-l2">{{ t('home.ph_l2') }}</span>
            <span class="ph-h2-l3">{{ t('home.ph_l3') }}</span>
          </h1>

          <!-- Subtitle -->
          <p class="ph-sub2">{{ t('home.ph_desc') }}</p>

          <!-- CTA buttons -->
          <div class="ph-actions2">
            <Link :href="route('objects')" class="ph-btn-a">
              {{ t('home.ph_cta1') }}
              <Icon name="arrow-right" class="w-4 h-4" :stroke-width="2" />
            </Link>
            <a href="#" class="ph-btn-b">
              <Icon name="arrow-down-tray" class="w-[14px] h-[14px]" :stroke-width="1.8" />
              {{ t('home.ph_cta2') }}
            </a>
          </div>

        </div>
      </div>

      <!-- Stats bar with icons — anchored to bottom -->
      <div class="ph-sbar">
        <div class="ph-sbar-in">
          <template v-for="(s, i) in heroStats" :key="i">
            <div class="ph-sitem">
              <div class="ph-sitem-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path :d="s.iconPath" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <div class="ph-sitem-text">
                <div class="ph-sitem-n">{{ s.n }}</div>
                <div class="ph-sitem-l">{{ s.l }}</div>
              </div>
            </div>
            <div v-if="i < heroStats.length - 1" class="ph-sitem-sep"></div>
          </template>
        </div>
      </div>

    </section>

    <!-- ── PARTNERS BELT ── -->
    <div class="ph-partners">
      <div class="ph-ptr-in">
        <span class="ph-ptr-label">{{ t('home.ph_partners') }}</span>
        <div class="ph-ptr-line"></div>
        <div class="ph-ptr-list">
          <span v-for="p in partnerBelt" :key="p" class="ph-ptr-item">{{ p }}</span>
        </div>
      </div>
    </div>

    <!-- ═══ 2. БЕГУЩАЯ СТРОКА (скрыт на мобильных) ═══ -->
    <div id="ticker" class="hidden sm:block overflow-hidden border-y border-gold/20 py-4" style="background:#0C1220">
      <div class="ticker-track flex items-center gap-0 whitespace-nowrap">
        <span v-for="i in 6" :key="i" class="flex items-center gap-0 shrink-0">
          <span class="text-gold font-bold text-[13px] tracking-[0.15em] uppercase px-10">{{ t('home.ticker_towers') }}</span>
          <span class="text-white/20 text-[10px]">·</span>
          <span class="text-white/60 font-light text-[13px] tracking-[0.12em] uppercase px-10">{{ t('home.ticker_area') }}</span>
          <span class="text-white/20 text-[10px]">·</span>
          <span class="text-gold font-bold text-[13px] tracking-[0.15em] uppercase px-10">{{ t('home.ticker_apts') }}</span>
          <span class="text-white/20 text-[10px]">·</span>
          <span class="text-white/60 font-light text-[13px] tracking-[0.12em] uppercase px-10">{{ t('home.ticker_delivery') }}</span>
          <span class="text-white/20 text-[10px]">·</span>
          <span class="text-gold font-bold text-[13px] tracking-[0.15em] uppercase px-10">{{ t('home.ticker_land') }}</span>
          <span class="text-white/20 text-[10px]">·</span>
        </span>
      </div>
    </div>

    <!-- ═══ 3. О КОМПАНИИ ═══ -->
    <section class="ha-stage">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="ha-wrap">

          <!-- ── LEFT: image + overlaid stats bar ── -->
          <div class="ha-left">
            <div class="ha-img-wrap">
              <img :src="asset('/images/hero-18.webp')" alt="QUDRAT CITY" loading="lazy" class="ha-img"/>
              <div class="ha-img-fade"></div>
            </div>
          </div>

          <!-- ── RIGHT: heading + timeline + CTA ── -->
          <div class="ha-right">

            <!-- Eyebrow -->
            <div class="ha-ew">
              <span class="ha-ew-line"></span>
              <span class="ha-ew-txt">{{ t('home.about_badge') }}</span>
            </div>

            <!-- Heading: 3 lines -->
            <h2 class="ha-h2">
              <span class="ha-h2-w">{{ t('home.about_title') }}</span>
              <span class="ha-h2-g">{{ t('home.about_title_hl') }}</span>
              <span class="ha-h2-w">{{ t('home.about_title_end') }}</span>
            </h2>

            <!-- Subtitle -->
            <p class="ha-sub">{{ t('home.about_sub') }}</p>

            <!-- Timeline with thumbnail images -->
            <div class="ha-tl">

              <div class="ha-tl-item">
                <div class="ha-tl-dot"></div>
                <div class="ha-tl-body">
                  <div class="ha-tl-year">2010</div>
                  <div class="ha-tl-title">{{ t('home.about_tl1_title') }}</div>
                  <div class="ha-tl-text">{{ t('home.about_tl1_text') }}</div>
                </div>
                <div class="ha-tl-img">
                  <img :src="asset('/images/mining.webp')" alt="2010" loading="lazy"/>
                </div>
              </div>

              <div class="ha-tl-item">
                <div class="ha-tl-dot"></div>
                <div class="ha-tl-body">
                  <div class="ha-tl-year">2015</div>
                  <div class="ha-tl-title">{{ t('home.about_tl2_title') }}</div>
                  <div class="ha-tl-text">{{ t('home.about_tl2_text') }}</div>
                </div>
                <div class="ha-tl-img">
                  <img :src="asset('/images/stroy.webp')" alt="2015" loading="lazy"/>
                </div>
              </div>

              <div class="ha-tl-item ha-tl-item--last">
                <div class="ha-tl-dot"></div>
                <div class="ha-tl-body">
                  <div class="ha-tl-year">{{ t('home.about_tl3_year') }}</div>
                  <div class="ha-tl-title">{{ t('home.about_tl3_title') }}</div>
                  <div class="ha-tl-text">{{ t('home.about_tl3_text') }}</div>
                </div>
                <div class="ha-tl-img">
                  <img :src="asset('/images/hero-8.jpg')" alt="QUDRAT CITY" loading="lazy"/>
                </div>
              </div>

            </div>

            <!-- CTA button -->
            <Link :href="route('about')" class="ha-btn">
              {{ t('home.about_link') }}
              <Icon name="arrow-right" class="w-4 h-4" :stroke-width="2" />
            </Link>

          </div>
        </div>
      </div>
    </section>

    <!-- ═══ 4. ГОРНОДОБЫЧА ═══ -->
    <section class="mn-stage">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="mn-wrap">

          <!-- LEFT: Content panel -->
          <div class="mn-left">

            <!-- Eyebrow -->
            <div class="mn-ew">
              <span class="mn-ew-line"></span>
              <span class="mn-ew-txt">{{ t('home.mine_badge') }}</span>
            </div>

            <!-- Big heading -->
            <h2 class="mn-h2">
              <span class="mn-h2-w">{{ t('home.mine_h1') }}</span>
              <span class="mn-h2-w">{{ t('home.mine_h2') }}</span>
              <span class="mn-h2-g">{{ t('home.mine_hg') }}</span>
            </h2>

            <!-- Subtitle -->
            <p class="mn-sub">{{ t('home.mine_sub') }}</p>

            <!-- 2×2 feature tiles -->
            <div class="mn-feats">
              <div class="mn-feat">
                <div class="mn-feat-icon">
                  <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 17l1.5-9 3.5 5 3-8 3 8 3.5-5L19 17H3z"/>
                    <line x1="3" y1="20" x2="21" y2="20"/>
                  </svg>
                </div>
                <div class="mn-feat-body">
                  <div class="mn-feat-t">{{ t('home.mine_f1_t') }}</div>
                  <div class="mn-feat-d">{{ t('home.mine_f1_d') }}</div>
                </div>
              </div>
              <div class="mn-feat">
                <div class="mn-feat-icon">
                  <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 12l2 2 4-4"/>
                    <path d="M12 2a10 10 0 100 20A10 10 0 0012 2z"/>
                  </svg>
                </div>
                <div class="mn-feat-body">
                  <div class="mn-feat-t">{{ t('home.mine_f2_t') }}</div>
                  <div class="mn-feat-d">{{ t('home.mine_f2_d') }}</div>
                </div>
              </div>
              <div class="mn-feat">
                <div class="mn-feat-icon">
                  <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="2" y1="12" x2="22" y2="12"/>
                    <path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/>
                  </svg>
                </div>
                <div class="mn-feat-body">
                  <div class="mn-feat-t">{{ t('home.mine_f3_t') }}</div>
                  <div class="mn-feat-d">{{ t('home.mine_f3_d') }}</div>
                </div>
              </div>
              <div class="mn-feat">
                <div class="mn-feat-icon">
                  <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                  </svg>
                </div>
                <div class="mn-feat-body">
                  <div class="mn-feat-t">{{ t('home.mine_f4_t') }}</div>
                  <div class="mn-feat-d">{{ t('home.mine_f4_d') }}</div>
                </div>
              </div>
            </div>

            <!-- CTA -->
            <Link :href="route('mining')" class="mn-btn">
              {{ t('home.mine_link') }}
              <Icon name="arrow-right" class="w-4 h-4" :stroke-width="2" />
            </Link>
          </div>

          <!-- RIGHT: Image with overlaid stats -->
          <div class="mn-right">
            <div class="mn-img-wrap">
              <img :src="asset('/images/mining.webp')" class="mn-img" alt="Mining operations"/>
              <div class="mn-img-ol"></div>
              <div class="mn-stats">
                <div class="mn-stat">
                  <div class="mn-stat-n">{{ t('home.mine_s1_v') }}</div>
                  <div class="mn-stat-l">{{ t('home.mine_s1_l') }}</div>
                </div>
                <div class="mn-stat">
                  <div class="mn-stat-n">{{ t('home.mine_s2_v') }}</div>
                  <div class="mn-stat-l">{{ t('home.mine_s2_l') }}</div>
                </div>
                <div class="mn-stat">
                  <div class="mn-stat-n">{{ t('home.mine_s3_v') }}</div>
                  <div class="mn-stat-l">{{ t('home.mine_s3_l') }}</div>
                </div>
                <div class="mn-stat">
                  <div class="mn-stat-n">{{ t('home.mine_s4_v') }}</div>
                  <div class="mn-stat-l">{{ t('home.mine_s4_l') }}</div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- ═══ 5. СТРОИТЕЛЬСТВО ═══ -->
    <section class="cs-stage">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="cs-wrap">

          <!-- LEFT: Image with overlaid stats -->
          <div class="cs-left">
            <div class="cs-img-wrap">
              <img :src="asset('/images/stroy.webp')" class="cs-img" alt="Construction"/>
              <div class="cs-img-ol"></div>
              <div class="cs-stats">
                <div class="cs-stat">
                  <div class="cs-stat-n">{{ t('home.cs_s1_v') }}</div>
                  <div class="cs-stat-l">{{ t('home.cs_s1_l') }}</div>
                </div>
                <div class="cs-stat">
                  <div class="cs-stat-n">{{ t('home.cs_s2_v') }}</div>
                  <div class="cs-stat-l">{{ t('home.cs_s2_l') }}</div>
                </div>
                <div class="cs-stat">
                  <div class="cs-stat-n">{{ t('home.cs_s3_v') }}</div>
                  <div class="cs-stat-l">{{ t('home.cs_s3_l') }}</div>
                </div>
                <div class="cs-stat">
                  <div class="cs-stat-n">{{ t('home.cs_s4_v') }}</div>
                  <div class="cs-stat-l">{{ t('home.cs_s4_l') }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- RIGHT: Content panel -->
          <div class="cs-right">

            <!-- Eyebrow -->
            <div class="cs-ew">
              <span class="cs-ew-line"></span>
              <span class="cs-ew-txt">{{ t('home.cs_badge') }}</span>
            </div>

            <!-- Big heading -->
            <h2 class="cs-h2">
              <span class="cs-h2-w">{{ t('home.cs_h1') }}</span>
              <span class="cs-h2-w">{{ t('home.cs_h2') }}</span>
              <span class="cs-h2-g">{{ t('home.cs_hg') }}</span>
            </h2>

            <!-- Subtitle -->
            <p class="cs-sub">{{ t('home.cs_sub') }}</p>

            <!-- 2×2 feature tiles -->
            <div class="cs-feats">
              <div class="cs-feat">
                <div class="cs-feat-icon">
                  <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>
                  </svg>
                </div>
                <div class="cs-feat-body">
                  <div class="cs-feat-t">{{ t('home.cs_f1_t') }}</div>
                  <div class="cs-feat-d">{{ t('home.cs_f1_d') }}</div>
                </div>
              </div>
              <div class="cs-feat">
                <div class="cs-feat-icon">
                  <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 12l2 2 4-4M12 2a10 10 0 100 20A10 10 0 0012 2z"/>
                  </svg>
                </div>
                <div class="cs-feat-body">
                  <div class="cs-feat-t">{{ t('home.cs_f2_t') }}</div>
                  <div class="cs-feat-d">{{ t('home.cs_f2_d') }}</div>
                </div>
              </div>
              <div class="cs-feat">
                <div class="cs-feat-icon">
                  <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 20h20M4.5 20V9L12 4l7.5 5v11"/>
                    <path d="M9 20v-5h6v5"/>
                  </svg>
                </div>
                <div class="cs-feat-body">
                  <div class="cs-feat-t">{{ t('home.cs_f3_t') }}</div>
                  <div class="cs-feat-d">{{ t('home.cs_f3_d') }}</div>
                </div>
              </div>
              <div class="cs-feat">
                <div class="cs-feat-icon">
                  <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
                  </svg>
                </div>
                <div class="cs-feat-body">
                  <div class="cs-feat-t">{{ t('home.cs_f4_t') }}</div>
                  <div class="cs-feat-d">{{ t('home.cs_f4_d') }}</div>
                </div>
              </div>
            </div>

            <!-- CTA -->
            <Link :href="route('objects')" class="cs-btn">
              {{ t('home.cs_link') }}
              <Icon name="arrow-right" class="w-4 h-4" :stroke-width="2" />
            </Link>

          </div>
        </div>
      </div>
    </section>

    <!-- ═══ 6. ВИЗУАЛИЗАЦИИ ═══ -->
    <section class="py-28 overflow-hidden" style="background:#0C1220">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12 mb-12">
        <div class="flex items-end justify-between">
          <div>
            <div class="flex items-center gap-3 mb-4">
              <div class="w-8 h-px bg-gold"></div>
              <span class="text-gold text-[10px] font-semibold uppercase tracking-[0.4em]">{{ t('home.gallery_badge') }}</span>
            </div>
            <h2 class="text-4xl lg:text-5xl font-bold text-white" style="font-family:'Cormorant Garamond',Georgia,serif">{{ t('home.gallery_title') }}</h2>
          </div>
          <p class="hidden md:block text-white/30 text-xs uppercase tracking-widest">{{ t('home.gallery_hint') }}</p>
        </div>
      </div>

      <div class="gallery-scroll flex gap-5 px-6 lg:px-12 overflow-x-auto pb-4" style="scroll-snap-type:x mandatory; -webkit-overflow-scrolling:touch; scrollbar-width:none; max-width:100%">
        <div v-for="(img, i) in galleryImages" :key="i"
          class="flex-shrink-0 rounded-2xl overflow-hidden group relative"
          :style="`width:${img.wide ? '640px' : '420px'}; height:520px; scroll-snap-align:start`">
          <img :src="asset(img.src)" :alt="img.alt" loading="lazy"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"/>
          <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
            style="background:linear-gradient(to top, rgba(7,11,22,0.7) 0%, transparent 60%)"></div>
          <div class="absolute bottom-6 left-6 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <div class="text-white font-semibold text-sm">{{ img.alt }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ 6. ПОЧЕМУ QUDRAT ═══ -->
    <section class="py-24 border-y border-white/5" style="background:#0C1220">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-16 lg:gap-28 items-center">

          <div>
            <div class="flex items-center gap-3 mb-7">
              <div class="w-8 h-px bg-gold"></div>
              <span class="text-gold text-[10px] font-semibold uppercase tracking-[0.4em]">{{ t('home.why_badge') }}</span>
            </div>
            <h2 class="font-bold leading-[1.1] mb-7 text-white"
              style="font-size:clamp(34px,4vw,56px); font-family:'Cormorant Garamond',Georgia,serif">
              {{ t('home.why_title_1') }}<br>{{ t('home.why_title_2') }}<br>
              <span class="text-gold">{{ t('home.why_title_3') }}</span>
            </h2>
            <p class="text-white/55 text-base leading-relaxed mb-10 max-w-md">
              {{ t('home.why_text') }}
            </p>
            <Link :href="route('about')"
              class="inline-flex items-center gap-3 text-gold border-b border-gold/30 hover:border-gold pb-1 text-xs font-semibold uppercase tracking-[0.3em] transition-all group w-fit">
              {{ t('home.why_link') }}
              <Icon name="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform" :stroke-width="2" />
            </Link>
          </div>

          <!-- 2×2 feature cards -->
          <div class="grid sm:grid-cols-2 gap-4">
            <div v-for="feat in whyFeatures" :key="feat.titleKey" class="why-card group">
              <div class="why-icon mb-5">
                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" :d="feat.icon"/>
                </svg>
              </div>
              <h3 class="text-white font-bold text-sm mb-2">{{ feat.title }}</h3>
              <p class="text-white/40 text-xs leading-relaxed">{{ feat.text }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ 7. ОТЗЫВЫ ═══ -->
    <section class="py-24" style="background:#080D1A">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <div class="mb-14">
          <div class="flex items-center gap-3 mb-5">
            <div class="w-8 h-px bg-gold"></div>
            <span class="text-gold text-[10px] font-semibold uppercase tracking-[0.4em]">{{ t('home.reviews_badge') }}</span>
          </div>
          <h2 class="font-bold text-white leading-tight"
            style="font-size:clamp(32px,4vw,52px); font-family:'Cormorant Garamond',Georgia,serif">
            {{ t('home.reviews_title') }}<br>
            <span class="text-gold">{{ t('home.reviews_hl') }}</span>
          </h2>
        </div>

        <!-- Testimonial cards — using t_ alias to avoid conflict with t() function -->
        <div class="grid lg:grid-cols-3 gap-5 mb-14">
          <div v-for="review in testimonials" :key="review.name" class="testimonial-card">
            <div class="flex gap-1 mb-4">
              <svg v-for="s in review.rating" :key="s" class="w-3.5 h-3.5 text-gold" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
              </svg>
            </div>
            <p class="text-white/60 text-sm leading-relaxed mb-6 flex-1">&ldquo;{{ review.text }}&rdquo;</p>
            <div class="border-t border-white/6 pt-4 flex items-center gap-3">
              <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold text-dark flex-shrink-0"
                style="background:linear-gradient(135deg,#C9A96E,#E2BE7D)">
                {{ review.initials || review.name.charAt(0) }}
              </div>
              <div>
                <div class="text-white font-semibold text-sm">{{ review.name }}</div>
                <div class="text-white/35 text-[10px]">{{ review.role }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Trust strip -->
        <div class="border-t border-white/6 pt-10 grid grid-cols-2 lg:grid-cols-4 gap-6 text-center">
          <div v-for="ts in trustStats" :key="ts.label">
            <div class="text-gold font-black mb-1"
              style="font-size:clamp(26px,3.5vw,40px)">
              {{ ts.value }}
            </div>
            <div class="text-white/35 text-[10px] uppercase tracking-wider leading-snug">{{ ts.label }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ 8. ИНВЕСТОРАМ ═══
         Временно скрыт по просьбе заказчика (v-if="false").
         Для возврата: убрать v-if. Страница /investors скрыта аналогично (см. routes/web.php). -->
    <section v-if="false" class="py-28 overflow-hidden" style="background:#080D1A">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="relative rounded-3xl overflow-hidden p-12 lg:p-20 border border-gold/10"
          style="background:linear-gradient(140deg, #0F1E35 0%, #080D1A 60%, #0A1525 100%)">
          <div class="absolute inset-0 pointer-events-none" style="background:radial-gradient(ellipse 50% 70% at 85% 50%, rgba(201,169,110,0.06) 0%, transparent 65%)"></div>
          <div class="absolute top-0 left-0 right-0 h-px" style="background:linear-gradient(90deg,transparent,rgba(201,169,110,0.4),transparent)"></div>
          <div class="absolute bottom-0 left-0 right-0 h-px" style="background:linear-gradient(90deg,transparent,rgba(201,169,110,0.15),transparent)"></div>

          <div class="relative grid lg:grid-cols-2 gap-20 items-center">
            <div>
              <div class="flex items-center gap-3 mb-6">
                <div class="w-8 h-px bg-gold"></div>
                <span class="text-gold text-[10px] font-semibold uppercase tracking-[0.4em]">{{ t('home.invest_badge') }}</span>
              </div>
              <h2 class="font-bold leading-tight mb-6 text-white"
                style="font-size:clamp(36px,4vw,60px); font-family:'Cormorant Garamond',Georgia,serif">
                {{ t('home.invest_title') }}<br>
                <span class="text-gold">{{ t('home.invest_title_hl') }}</span>
              </h2>
              <p class="text-white/45 leading-relaxed mb-10 text-base max-w-md">
                {{ t('home.invest_text') }}
              </p>
              <Link :href="route('contacts')"
                class="inline-flex items-center gap-3 bg-gold hover:bg-[#D6BA7A] text-dark font-bold px-9 py-4 rounded-xl transition-all duration-300 uppercase tracking-widest text-[11px] shadow-[0_8px_40px_rgba(201,169,110,0.2)] group">
                {{ t('home.invest_cta') }}
                <Icon name="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform" :stroke-width="2" />
              </Link>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div v-for="inv in investStats" :key="inv.label"
                class="rounded-2xl p-7 border border-white/5 hover:border-gold/30 transition-all duration-300 group cursor-default"
                style="background:rgba(255,255,255,0.025)">
                <div class="font-black text-gold mb-3 leading-none"
                  style="font-size:clamp(28px,3.5vw,44px)">
                  {{ inv.value }}
                </div>
                <div class="text-white/35 text-[11px] uppercase tracking-wider leading-relaxed">{{ inv.label }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ 9. НАШИ ПАРТНЁРЫ ═══ -->
    <section class="py-20 border-t border-white/5 overflow-hidden" style="background:#080D1A">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-12">
          <div>
            <div class="flex items-center gap-3 mb-3">
              <div class="w-8 h-px bg-gold/50"></div>
              <span class="text-gold text-[10px] font-bold uppercase tracking-[0.45em]">{{ t('home.partners_badge') }}</span>
            </div>
            <h2 class="text-3xl lg:text-4xl font-bold text-white" style="font-family:'Cormorant Garamond',Georgia,serif">{{ t('home.partners_title') }}</h2>
          </div>
          <Link :href="route('partners')"
            class="inline-flex items-center gap-2 text-sm text-gold hover:text-white transition-colors group shrink-0">
            {{ t('home.partners_all') }}
            <Icon name="arrow-long-right" class="w-4 h-4 transition-transform group-hover:translate-x-1" :stroke-width="2.5" />
          </Link>
        </div>

        <!-- Logo grid -->
        <div v-if="homePartners.length" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-7 gap-4 mb-10">
          <Link
            v-for="p in homePartners" :key="p.id"
            :href="route('partners')"
            class="group aspect-square rounded-xl flex items-center justify-center p-3 transition-all duration-300 hover:border-gold/40 hover:bg-gold/5"
            style="background:#0F1828; border:1px solid rgba(255,255,255,0.07)"
            :aria-label="p.name"
          >
            <img
              :src="p.logo"
              :alt="p.name"
              class="w-full h-full object-contain filter brightness-70 group-hover:brightness-100 transition-all duration-300 group-hover:scale-110"
            />
          </Link>
        </div>

        <!-- Stats row -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
          <div class="rounded-xl p-5 text-center" style="background:#0F1828; border:1px solid rgba(255,255,255,0.07)">
            <div class="text-2xl font-light text-gold mb-1">16+</div>
            <div class="text-[11px] text-white/30 uppercase tracking-widest">{{ t('home.partners_stat1_l') }}</div>
          </div>
          <div class="rounded-xl p-5 text-center" style="background:#0F1828; border:1px solid rgba(255,255,255,0.07)">
            <div class="text-sm font-semibold text-gold mb-1 tracking-wider">AF · PK · UZ</div>
            <div class="text-[11px] text-white/30 uppercase tracking-widest">{{ t('home.partners_stat2_l') }}</div>
          </div>
          <div class="rounded-xl p-5 text-center" style="background:#0F1828; border:1px solid rgba(255,255,255,0.07)">
            <div class="text-2xl font-light text-gold mb-1">102K</div>
            <div class="text-[11px] text-white/30 uppercase tracking-widest">{{ t('home.partners_stat3_l') }}</div>
          </div>
          <div class="rounded-xl p-5 text-center" style="background:#0F1828; border:1px solid rgba(255,255,255,0.07)">
            <div class="text-2xl font-light text-gold mb-1">2017</div>
            <div class="text-[11px] text-white/30 uppercase tracking-widest">{{ t('home.partners_stat4_l') }}</div>
          </div>
        </div>

      </div>
    </section>

    <!-- ═══ 10. КОНТАКТ + КАРТА ═══ -->
    <section class="py-28 border-t border-white/5" style="background:#0C1220">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <div class="text-center mb-16">
          <div class="flex items-center justify-center gap-4 mb-5">
            <div class="w-10 h-px bg-gold/40"></div>
            <span class="text-gold text-[10px] font-semibold uppercase tracking-[0.4em]">{{ t('home.contact_badge') }}</span>
            <div class="w-10 h-px bg-gold/40"></div>
          </div>
          <h2 class="text-4xl lg:text-5xl font-bold text-white mb-4">{{ t('home.contact_title') }}</h2>
          <p class="text-white/40 text-base max-w-md mx-auto">{{ t('home.contact_sub') }}</p>
        </div>

        <div class="grid lg:grid-cols-2 gap-10 items-start">

          <!-- Contact form -->
          <div class="rounded-2xl p-8 border border-white/6" style="background:#0F1828">
            <div v-if="contactSent"
              class="mb-5 p-4 rounded-xl border border-gold/25 flex items-center gap-3"
              style="background:rgba(201,169,110,0.07)">
              <svg class="w-5 h-5 text-gold flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
              </svg>
              <p class="text-gold text-sm font-medium">{{ t('home.contact_success') }}</p>
            </div>

            <form @submit.prevent="sendContact" class="space-y-4">
              <div v-if="Object.keys(contactForm.errors).length"
                class="p-3 rounded-lg border border-red-500/20 bg-red-500/5">
                <p class="text-red-400 text-xs">{{ Object.values(contactForm.errors)[0] }}</p>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                  <label class="contact-label">{{ t('home.contact_name_lbl') }}</label>
                  <input v-model="contactForm.name" type="text" required class="contact-input"
                    :placeholder="t('home.contact_name_ph')"
                    :class="contactForm.errors.name ? 'border-red-500/50' : ''"/>
                </div>
                <div>
                  <label class="contact-label">{{ t('home.contact_ph_lbl') }}</label>
                  <input v-model="contactForm.phone" type="tel" required class="contact-input"
                    :placeholder="t('home.contact_ph_ph')"
                    :class="contactForm.errors.phone ? 'border-red-500/50' : ''"/>
                </div>
                <div>
                  <label class="contact-label">{{ t('home.contact_email_lbl') }}</label>
                  <input v-model="contactForm.email" type="email" class="contact-input"
                    :placeholder="t('home.contact_email_ph')"/>
                </div>
                <div class="col-span-2">
                  <label class="contact-label">{{ t('home.contact_int_lbl') }}</label>
                  <select v-model="contactForm.interest" class="contact-input">
                    <option value="">{{ t('home.contact_int_sel') }}</option>
                    <option value="apartment">{{ t('home.contact_int_apt') }}</option>
                    <option value="invest">{{ t('home.contact_int_inv') }}</option>
                    <option value="commercial">{{ t('home.contact_int_com') }}</option>
                    <option value="other">{{ t('home.contact_int_oth') }}</option>
                  </select>
                </div>
                <div class="col-span-2">
                  <label class="contact-label">{{ t('home.contact_msg_lbl') }}</label>
                  <textarea v-model="contactForm.message" rows="3" class="contact-input"
                    :placeholder="t('home.contact_msg_ph')"></textarea>
                </div>
              </div>
              <button type="submit"
                class="w-full py-4 rounded-xl text-dark font-bold uppercase tracking-widest text-[12px] transition-all duration-300 disabled:opacity-60"
                style="background:#C9A96E"
                :disabled="contactForm.processing || contactSent">
                <span v-if="contactSent">{{ t('home.contact_sent_btn') }}</span>
                <span v-else-if="contactForm.processing" class="flex items-center justify-center gap-2">
                  <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                  </svg>
                  {{ t('home.contact_sending') }}
                </span>
                <span v-else>{{ t('home.contact_btn') }}</span>
              </button>
              <p class="text-white/25 text-xs text-center leading-relaxed">
                {{ t('home.contact_privacy') }}
                <Link :href="route('privacy')" class="underline hover:text-white/50 transition-colors">{{ t('home.contact_priv_lnk') }}</Link>
              </p>
            </form>
          </div>

          <!-- Map + contacts -->
          <div class="flex flex-col gap-6">
            <div class="rounded-2xl overflow-hidden border border-white/6" style="height:320px">
              <iframe
                src="https://www.openstreetmap.org/export/embed.html?bbox=68.71%2C38.54%2C68.80%2C38.58&layer=mapnik&marker=38.5598%2C68.7564"
                width="100%" height="100%" frameborder="0"
                style="border:0; filter:invert(0.85) hue-rotate(180deg) brightness(0.85) contrast(1.1)"
                :title="t('home.contact_title')"
              ></iframe>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="rounded-xl p-5 border border-white/6" style="background:#0F1828">
                <div class="text-gold text-[10px] uppercase tracking-[0.3em] font-semibold mb-2">{{ t('home.contact_ph_label') }}</div>
                <a href="tel:+992000000000" class="text-white text-base font-semibold hover:text-gold transition-colors">+992 00 000-00-00</a>
                <div class="text-white/30 text-xs mt-1">{{ t('home.contact_hours') }}</div>
              </div>
              <div class="rounded-xl p-5 border border-white/6" style="background:#0F1828">
                <div class="text-gold text-[10px] uppercase tracking-[0.3em] font-semibold mb-2">{{ t('home.contact_addr_lbl') }}</div>
                <div class="text-white text-sm font-semibold">{{ t('home.contact_city') }}</div>
                <div class="text-white/30 text-xs mt-1">{{ t('home.contact_office') }}</div>
              </div>
              <div class="rounded-xl p-5 border border-white/6 sm:col-span-2" style="background:#0F1828">
                <div class="text-gold text-[10px] uppercase tracking-[0.3em] font-semibold mb-2">Email</div>
                <a href="mailto:info@qudrat.tj" class="text-white text-sm font-semibold hover:text-gold transition-colors">info@qudrat.tj</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

  </MainLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import Icon from '@/Components/Icon.vue';
import { useAsset } from '@/composables/useAsset';
import { useTrans } from '@/composables/useTrans';

const { asset } = useAsset();
const { t } = useTrans();

const props = defineProps({
  apartments:    { type: Array, default: () => [] },
  testimonials:  { type: Array, default: () => [] },
  homePartners:  { type: Array, default: () => [] },
});

/* ── Grand Stage Hero ── */
const heroSection = ref(null);
const mx = ref(0);
const my = ref(0);

function onMouseMove(e) {
  const el = heroSection.value;
  if (!el) return;
  const rect = el.getBoundingClientRect();
  mx.value = e.clientX - rect.left - rect.width  / 2;
  my.value = e.clientY - rect.top  - rect.height / 2;
}

function scrollToTicker() {
  document.getElementById('ticker')?.scrollIntoView({ behavior: 'smooth' });
}

/* ── Premium Hero stats ── */
const heroStats = computed(() => [
  {
    n: '15+', l: t('home.ph_stat1_l'),
    iconPath: 'M2.25 21h19.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21',
  },
  {
    n: '50+', l: t('home.ph_stat2_l'),
    iconPath: 'M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5',
  },
  {
    n: '80+', l: t('home.ph_stat3_l'),
    iconPath: 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z',
  },
  {
    n: '2010', l: t('home.ph_stat4_l'),
    iconPath: 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5',
  },
])

/* ── Partner belt (text names in hero ticker) ── */
const partnerBelt = [
  'OrientFinansBank', 'City Dushanbe', 'Taj Invest',
  'Zeppelin', 'Knauf', 'Hikvision', 'Agrobank',
]

/* ── Apartments tabs — computed so labels update with locale ── */
const activeTab = ref('1');
const apartmentTabs = computed(() => [
  { key: '1', label: t('home.apt_tab1') },
  { key: '2', label: t('home.apt_tab2') },
  { key: '3', label: t('home.apt_tab3') },
  { key: '4', label: t('home.apt_tab4') },
]);

const currentApartments = computed(() =>
  props.apartments.filter(a => String(a.rooms) === activeTab.value).slice(0, 6)
);

function finishLabel(key) {
  return t('apartments.finish.' + key) || key;
}

/* ── Gallery ── */
const galleryImages = [
  { src: '/images/hero-3.jpg',  alt: 'QUDRAT CITY — башни', wide: true },
  { src: '/images/hero-1.jpg',  alt: 'Панорама Душанбе', wide: false },
  { src: '/images/hero-4.jpg',  alt: 'Фасад комплекса', wide: false },
  { src: '/images/hero-6.jpg',  alt: 'Архитектурный облик', wide: true },
  { src: '/images/hero-18.webp', alt: 'Строительство', wide: false },
];

/* ── Why QUDRAT features — computed ── */
const whyFeatures = computed(() => [
  {
    titleKey: 'why_f1',
    title: t('home.why_f1_title'),
    text:  t('home.why_f1_text'),
    icon: 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12',
  },
  {
    titleKey: 'why_f2',
    title: t('home.why_f2_title'),
    text:  t('home.why_f2_text'),
    icon: 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z',
  },
  {
    titleKey: 'why_f3',
    title: t('home.why_f3_title'),
    text:  t('home.why_f3_text'),
    icon: 'M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75',
  },
  {
    titleKey: 'why_f4',
    title: t('home.why_f4_title'),
    text:  t('home.why_f4_text'),
    icon: 'M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 0 1 21.75 8.25Z',
  },
]);

/* ── Trust stats — computed ── */
const trustStats = computed(() => [
  { value: t('home.trust_stat1_v'), label: t('home.trust_stat1_l') },
  { value: t('home.trust_stat2_v'), label: t('home.trust_stat2_l') },
  { value: t('home.trust_stat3_v'), label: t('home.trust_stat3_l') },
  { value: t('home.trust_stat4_v'), label: t('home.trust_stat4_l') },
]);

/* ── Testimonials — из БД (с fallback) ── */
const fallbackTestimonials = [
  { name: 'Алишер Р.',  role: 'Покупатель 3-комн. · 2023', rating: 5, initials: 'АР', text: 'Купил квартиру на стадии котлована. Менеджер сопровождал на каждом этапе — от выбора планировки до регистрации. Сдача прошла точно по графику.' },
  { name: 'Мадина Х.',  role: 'Инвестор · 2 квартиры · 2022', rating: 5, initials: 'МХ', text: 'Вложила средства в два объекта QUDRAT. За год стоимость выросла на 18%. Юридическое оформление прозрачно, договор зарегистрирован в ГКН.' },
  { name: 'Баходур Н.', role: 'Покупатель 2-комн. · 2024', rating: 5, initials: 'БН', text: 'Брал рассрочку на 3 года — никаких скрытых процентов, цена зафиксирована в договоре. Управляющая компания нашла арендаторов за неделю.' },
];
const testimonials = computed(() =>
  props.testimonials.length ? props.testimonials : fallbackTestimonials
);

/* ── Invest stats — computed ── */
const investStats = computed(() => [
  { value: t('home.invest_s1_v'), label: t('home.invest_s1_l') },
  { value: t('home.invest_s2_v'), label: t('home.invest_s2_l') },
  { value: t('home.invest_s3_v'), label: t('home.invest_s3_l') },
  { value: t('home.invest_s4_v'), label: t('home.invest_s4_l') },
]);

/* ── Contact form ── */
const contactSent = ref(false);
const contactForm = useForm({ name: '', phone: '', email: '', interest: '', message: '' });

function sendContact() {
  contactForm.post(route('contact.send'), {
    preserveScroll: true,
    onSuccess: () => {
      contactSent.value = true;
      contactForm.reset();
      setTimeout(() => { contactSent.value = false; }, 6000);
    },
  });
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400;1,600&display=swap');

/* ════════════════════════════════════════
   PREMIUM HERO — full-bleed
   ════════════════════════════════════════ */

.ph-stage {
  position: relative;
  min-height: 100vh;
  background: #061228;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

/* ── Full-bleed background ── */
.ph-bg {
  position: absolute;
  inset: 0;
  z-index: 0;
}
.ph-bg-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center 30%;
  display: block;
}

/* ── Overlay: left-to-right dark (text readability) ── */
.ph-bg-ol {
  position: absolute;
  inset: 0;
  z-index: 1;
  background: linear-gradient(
    to right,
    rgba(6,18,40,.97) 0%,
    rgba(6,18,40,.88) 25%,
    rgba(6,18,40,.62) 48%,
    rgba(6,18,40,.22) 68%,
    transparent 88%
  );
}

/* ── Overlay: bottom-up dark (stats bar readability) ── */
.ph-bg-ob {
  position: absolute;
  inset: 0;
  z-index: 1;
  background: linear-gradient(
    to top,
    rgba(4,10,24,.90) 0%,
    rgba(4,10,24,.55) 18%,
    transparent 36%
  );
}

/* ── Main content — full-width, aligned with navbar max-w-[1400px] mx-auto px-6 lg:px-12 ── */
.ph-content {
  position: relative;
  z-index: 10;
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  /* Same container as navbar */
  width: 100%;
  max-width: 1400px;
  margin: 0 auto;
  padding: 108px 24px 90px;   /* px-6 = 24px, matches navbar mobile */
}
@media (min-width: 1024px) {
  .ph-content { padding-left: 48px; padding-right: 48px; }  /* lg:px-12 = 48px */
}

/* ── Inner text column (limits width to ~half the container) ── */
.ph-inner {
  max-width: 720px;
  width: 100%;
}

/* ── Eyebrow: line — text — line ── */
.ph-ew2 {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 30px;
  animation: phFU .7s .15s both;
}
.ph-ew2-line {
  flex: 0 0 36px;
  height: 1px;
  background: rgba(212,175,55,.50);
}
.ph-ew2-txt {
  font-family: 'Inter', sans-serif;
  font-size: 9px;
  font-weight: 600;
  letter-spacing: .36em;
  color: rgba(212,175,55,.65);
  text-transform: uppercase;
  white-space: nowrap;
}
@media (max-width: 479px) {
  .ph-ew2-txt  { font-size: 7px; letter-spacing: .20em; }
  .ph-ew2-line { flex: 0 0 22px; }
}

/* ── ALL-CAPS heading ── */
.ph-h2 {
  line-height: 0.9;
  margin-bottom: 28px;
  animation: phFU .9s .30s both;
  overflow-wrap: normal;
  word-break: normal;
  hyphens: none;
}
.ph-h2-l1 {
  display: block;
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: clamp(34px, 7.5vw, 80px);
  font-weight: 700;
  font-style: normal;
  letter-spacing: .02em;
  text-transform: uppercase;
  color: rgba(255,255,255,.93);
  white-space: nowrap;
}
.ph-h2-l2 {
  display: block;
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: clamp(34px, 7.5vw, 80px);
  font-weight: 700;
  font-style: normal;
  letter-spacing: .02em;
  text-transform: uppercase;
  white-space: nowrap;
  color: rgba(255,255,255,.93);
}
.ph-h2-l3 {
  display: block;
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: clamp(34px, 7.5vw, 80px);
  font-weight: 700;
  font-style: italic;
  letter-spacing: .02em;
  text-transform: uppercase;
  white-space: nowrap;
  background: linear-gradient(130deg, #ffffff 0%, #e8d5a0 40%, #D4AF37 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
/* On lg+ calibrate to fit long words */
@media (min-width: 1024px) {
  .ph-h2-l1, .ph-h2-l2, .ph-h2-l3 { font-size: clamp(56px, 5.8vw, 86px); }
}

/* ── Subtitle ── */
.ph-sub2 {
  font-family: 'Inter', sans-serif;
  font-size: clamp(13px, 1.1vw, 17px);
  color: rgba(255,255,255,.42);
  line-height: 1.78;
  max-width: 440px;
  margin-bottom: 40px;
  font-weight: 300;
  letter-spacing: .012em;
  animation: phFU .8s .52s both;
}
@media (max-width: 639px) { .ph-sub2 { margin-bottom: 28px; } }

/* ── CTA buttons ── */
.ph-actions2 {
  display: flex;
  flex-wrap: wrap;
  gap: 14px;
  animation: phFU .8s .68s both;
}

.ph-btn-a {
  display: inline-flex; align-items: center; gap: 10px;
  background: #D4AF37; color: #061228;
  font-family: 'Inter', sans-serif;
  font-size: 11px; font-weight: 700;
  letter-spacing: .16em; text-transform: uppercase;
  padding: 15px 30px; border-radius: 3px;
  transition: all .28s ease; white-space: nowrap;
}
.ph-btn-a:hover {
  background: #e8c847;
  transform: translateY(-2px);
  box-shadow: 0 10px 34px rgba(212,175,55,.28);
}
.ph-btn-b {
  display: inline-flex; align-items: center; gap: 8px;
  border: 1px solid rgba(212,175,55,.28); color: rgba(212,175,55,.68);
  font-family: 'Inter', sans-serif;
  font-size: 11px; font-weight: 600;
  letter-spacing: .16em; text-transform: uppercase;
  padding: 15px 26px; border-radius: 3px;
  transition: all .28s ease; white-space: nowrap;
}
.ph-btn-b:hover {
  border-color: #D4AF37; color: #D4AF37;
  background: rgba(212,175,55,.05);
}

/* ════════════════════════════════════════
   About / Company Section (block 3)
   ════════════════════════════════════════ */
.ha-stage {
  background: #070B16;
  padding: 80px 0 90px;
  overflow: hidden;
}
.ha-wrap {
  display: grid;
  grid-template-columns: 55% 1fr;
  gap: 72px;
  align-items: center;
}
@media (max-width: 1023px) {
  .ha-wrap { grid-template-columns: 1fr; gap: 52px; }
}

/* ── Left: rounded image + stats ── */
.ha-left {}
.ha-img-wrap {
  position: relative;
  border-radius: 20px;
  overflow: hidden;
  height: clamp(480px, 65vh, 700px);
  box-shadow: 0 30px 80px rgba(0,0,0,0.55);
}
.ha-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center 15%;
  display: block;
}
.ha-img-fade {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(4,8,20,0.55) 0%, rgba(4,8,20,0.15) 30%, transparent 55%);
  pointer-events: none;
}

/* ── Right content ── */
.ha-ew {
  display: flex; align-items: center; gap: 14px; margin-bottom: 22px;
}
.ha-ew-line {
  display: block; width: 32px; height: 1px;
  background: #C9A96E; opacity: .8; flex-shrink: 0;
}
.ha-ew-txt {
  font-size: 10px; font-weight: 700; letter-spacing: .45em;
  text-transform: uppercase; color: #C9A96E;
}

/* Heading */
.ha-h2 {
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: clamp(38px, 4.2vw, 66px);
  font-weight: 700;
  line-height: 1.08;
  margin-bottom: 20px;
  display: flex;
  flex-direction: column;
}
.ha-h2-w { color: #ffffff; }
.ha-h2-g { color: #D4AF37; }

/* Subtitle */
.ha-sub {
  font-size: clamp(13px, 1vw, 15px);
  color: rgba(255,255,255,0.50);
  line-height: 1.78;
  max-width: 480px;
  margin-bottom: 32px;
}

/* Timeline */
.ha-tl {
  border-left: 2px solid rgba(201,169,110,0.20);
  padding-left: 30px;
  margin-bottom: 32px;
  display: flex;
  flex-direction: column;
}
.ha-tl-item {
  position: relative;
  display: grid;
  grid-template-columns: 1fr 96px;
  gap: 16px;
  align-items: start;
  padding-bottom: 26px;
}
.ha-tl-item--last { padding-bottom: 0; }
.ha-tl-dot {
  position: absolute;
  left: -38px; top: 5px;
  width: 12px; height: 12px;
  border-radius: 50%;
  background: #C9A96E;
  box-shadow: 0 0 0 4px #070B16, 0 0 0 6px rgba(201,169,110,0.22);
}
.ha-tl-year {
  font-size: 11px; font-weight: 700;
  letter-spacing: .22em; text-transform: uppercase;
  color: #C9A96E; margin-bottom: 4px;
}
.ha-tl-title {
  font-size: 13.5px; font-weight: 700;
  color: #ffffff; text-transform: uppercase;
  letter-spacing: .06em; margin-bottom: 6px;
}
.ha-tl-text {
  font-size: 12.5px; color: rgba(255,255,255,0.48);
  line-height: 1.62;
}
.ha-tl-img {
  width: 96px; height: 72px;
  border-radius: 9px; overflow: hidden; flex-shrink: 0;
}
.ha-tl-img img {
  width: 100%; height: 100%; object-fit: cover; display: block;
}
@media (max-width: 639px) {
  .ha-tl-item { grid-template-columns: 1fr; }
  .ha-tl-img { display: none; }
}

/* CTA button */
.ha-btn {
  display: inline-flex; align-items: center; justify-content: center; gap: 12px;
  width: 100%;
  background: rgba(201,169,110,0.07);
  border: 1px solid rgba(201,169,110,0.32);
  color: #C9A96E;
  font-size: 11px; font-weight: 700;
  letter-spacing: .18em; text-transform: uppercase;
  padding: 18px 36px; border-radius: 5px;
  text-decoration: none;
  transition: all .25s ease;
}
.ha-btn:hover {
  background: rgba(201,169,110,0.14);
  border-color: #C9A96E;
  transform: translateY(-2px);
  box-shadow: 0 8px 28px rgba(201,169,110,0.15);
}

/* ── Stats bar with icons ── */
.ph-sbar {
  position: relative;
  z-index: 10;
  border-top: 1px solid rgba(212,175,55,.14);
  background: rgba(4,10,24,.75);
  backdrop-filter: blur(18px);
}
.ph-sbar-in {
  display: flex;
  align-items: stretch;
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 24px;    /* px-6 */
}
@media (min-width: 1024px) { .ph-sbar-in { padding: 0 48px; } }  /* lg:px-12 */

.ph-sitem {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 22px 16px;
}
@media (max-width: 479px) { .ph-sitem { gap: 9px; padding: 16px 8px; } }

.ph-sitem-icon {
  color: rgba(212,175,55,.52);
  flex-shrink: 0;
  line-height: 0;
}
@media (max-width: 479px) { .ph-sitem-icon svg { width: 17px; height: 17px; } }

.ph-sitem-text {
  display: flex;
  flex-direction: column;
}

.ph-sitem-n {
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: clamp(22px, 2.6vw, 42px);
  font-weight: 700;
  color: #D4AF37;
  line-height: 1;
  letter-spacing: -.01em;
}

.ph-sitem-l {
  font-family: 'Inter', sans-serif;
  font-size: 8px;
  font-weight: 600;
  letter-spacing: .18em;
  text-transform: uppercase;
  color: rgba(255,255,255,.30);
  margin-top: 5px;
  white-space: nowrap;
}
@media (max-width: 479px) { .ph-sitem-l { font-size: 6.5px; letter-spacing: .11em; } }

.ph-sitem-sep {
  width: 1px;
  background: rgba(212,175,55,.08);
  align-self: stretch;
  margin: 14px 0;
}
@media (max-width: 479px) { .ph-sitem-sep { display: none; } }

/* ── Partners belt ── */
.ph-partners {
  background: #040d1e;
  border-top: 1px solid rgba(212,175,55,.06);
  padding: 18px 0;
}

.ph-ptr-in {
  display: flex; align-items: center; gap: 28px;
  max-width: 1400px; margin: 0 auto;
  padding: 0 24px;    /* px-6 */
  overflow-x: auto; scrollbar-width: none;
}
@media (min-width: 1024px) { .ph-ptr-in { padding: 0 48px; } }  /* lg:px-12 */
.ph-ptr-in::-webkit-scrollbar { display: none; }

.ph-ptr-label {
  font-family: 'Inter', sans-serif;
  font-size: 9px; font-weight: 600;
  letter-spacing: .34em; color: rgba(255,255,255,.18);
  text-transform: uppercase; white-space: nowrap;
}
.ph-ptr-line { width: 1px; height: 18px; background: rgba(212,175,55,.10); flex-shrink: 0; }
.ph-ptr-list {
  display: flex; align-items: center; gap: 32px; flex-wrap: nowrap;
}
.ph-ptr-item {
  font-family: 'Inter', sans-serif;
  font-size: 11px; font-weight: 700;
  letter-spacing: .1em; text-transform: uppercase;
  color: rgba(255,255,255,.16);
  transition: color .3s; white-space: nowrap; cursor: default;
}
.ph-ptr-item:hover { color: rgba(212,175,55,.48); }

/* ── Animations ── */
@keyframes phFU {
  from { opacity: 0; transform: translateY(22px); }
  to   { opacity: 1; transform: translateY(0); }
}
@keyframes phFI {
  from { opacity: 0; }
  to   { opacity: 1; }
}

/* ════════════════════════════════════════
   HERO LOGO ANIMATION
   ════════════════════════════════════════ */

.hero-logo-wrap {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 10rem;
  height: 10rem;
}
@media (min-width: 1024px) {
  .hero-logo-wrap { width: 12rem; height: 12rem; }
}

/* Concentric pulsing rings */
.hero-logo-ring {
  position: absolute;
  inset: 0;
  border-radius: 50%;
  border: 1px solid rgba(201,169,110,0.35);
}
.hero-logo-ring--outer {
  inset: -18px;
  border-color: rgba(201,169,110,0.18);
  animation: heroRingPulse 3.2s ease-in-out infinite;
}
.hero-logo-ring--inner {
  inset: -8px;
  border-color: rgba(201,169,110,0.3);
  animation: heroRingPulse 3.2s 0.6s ease-in-out infinite;
}
@keyframes heroRingPulse {
  0%, 100% { transform: scale(1);    opacity: 0.5; }
  50%       { transform: scale(1.07); opacity: 1;   }
}

/* Logo image: entrance + float */
.hero-logo-img {
  animation:
    heroLogoEnter 1.1s 0.25s cubic-bezier(0.34, 1.56, 0.64, 1) both,
    heroLogoFloat 5s    1.4s ease-in-out infinite;
  filter: drop-shadow(0 0 40px rgba(201,169,110,0.45));
}
@keyframes heroLogoEnter {
  from { transform: scale(0.55) rotate(-8deg); opacity: 0; filter: blur(4px) drop-shadow(0 0 0 rgba(201,169,110,0)); }
  to   { transform: scale(1)    rotate(0deg);  opacity: 1; filter: blur(0)   drop-shadow(0 0 40px rgba(201,169,110,0.45)); }
}
@keyframes heroLogoFloat {
  0%, 100% { transform: translateY(0px);  filter: drop-shadow(0 0   35px rgba(201,169,110,0.4)); }
  50%       { transform: translateY(-9px); filter: drop-shadow(0 10px 55px rgba(201,169,110,0.6)); }
}

/* ════════════════════════════════════════
   GRAND STAGE HERO
   ════════════════════════════════════════ */

.hero-stage {
  height: 100vh; min-height: 640px;
  background: #070B16;
  display: flex; align-items: center; justify-content: center;
}

.hero-grid {
  background-image:
    linear-gradient(rgba(201,169,110,0.04) 1px, transparent 1px),
    linear-gradient(90deg, rgba(201,169,110,0.04) 1px, transparent 1px);
  background-size: 60px 60px;
}

.hero-glow-gold {
  width: 800px; height: 800px; top: 75%; left: 18%;
  background: radial-gradient(ellipse, rgba(201,169,110,0.13) 0%, rgba(201,169,110,0.04) 40%, transparent 70%);
  border-radius: 50%; transition: transform 0.1s linear; will-change: transform;
}

.hero-glow-blue {
  width: 700px; height: 700px; top: 20%; left: 80%;
  background: radial-gradient(ellipse, rgba(100,149,237,0.10) 0%, rgba(100,149,237,0.03) 40%, transparent 70%);
  border-radius: 50%; transition: transform 0.1s linear; will-change: transform;
}

.hero-q-mark {
  font-family: 'Inter', sans-serif;
  font-size: clamp(380px, 55vw, 700px); font-weight: 900; line-height: 1;
  color: transparent; -webkit-text-stroke: 1px rgba(201,169,110,0.045);
  top: 50%; left: 50%; user-select: none;
  transition: transform 0.15s linear; will-change: transform;
}

.hero-title {
  font-family: Georgia, 'Times New Roman', serif;
  font-size: clamp(52px, 7vw, 96px);
  font-weight: 700;
  letter-spacing: 0.08em;
  background: linear-gradient(160deg, #ffffff 0%, #e8d5b0 45%, #C9A96E 100%);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}

.hero-gold-line {
  height: 2px; width: 0;
  background: linear-gradient(90deg, transparent, #C9A96E, rgba(201,169,110,0.3));
  animation: drawLine 1.2s 0.9s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}
@keyframes drawLine { to { width: clamp(120px, 20vw, 280px); } }

.hero-card {
  display: block; padding: 1.5rem; border-radius: 1rem;
  border: 1px solid rgba(255,255,255,0.06); background: rgba(255,255,255,0.02);
  backdrop-filter: blur(8px);
  transition: border-color 0.35s, background 0.35s, transform 0.35s;
}
.hero-card:hover { transform: translateY(-3px); }
.hero-card-gold:hover { border-color: rgba(201,169,110,0.25); background: rgba(201,169,110,0.04); }
.hero-card-blue:hover { border-color: rgba(100,149,237,0.2); background: rgba(100,149,237,0.03); }
.hero-card-icon {
  width: 2.25rem; height: 2.25rem; flex-shrink: 0;
  border-radius: 0.625rem; border: 1px solid;
  display: flex; align-items: center; justify-content: center;
}

.hero-btn-primary {
  background: linear-gradient(135deg, #C9A96E 0%, #E2BE7D 50%, #C9A96E 100%);
  background-size: 200% 100%;
  box-shadow: 0 8px 40px rgba(201,169,110,0.25), 0 2px 8px rgba(0,0,0,0.4);
  transition: background-position 0.4s, transform 0.3s, box-shadow 0.3s;
}
.hero-btn-primary:hover {
  background-position: 100% 0;
  box-shadow: 0 12px 50px rgba(201,169,110,0.35), 0 4px 12px rgba(0,0,0,0.5);
}

.hero-scroll-beam {
  background: linear-gradient(to bottom, rgba(201,169,110,0.7), transparent);
  animation: scrollBeam 1.6s ease-in-out infinite;
}
@keyframes scrollBeam {
  0%   { transform: translateY(-100%); opacity: 1; }
  100% { transform: translateY(100%);  opacity: 0; }
}

@keyframes heroFadeUp {
  from { opacity: 0; transform: translateY(24px); }
  to   { opacity: 1; transform: translateY(0); }
}
.hero-anim-1 { opacity: 0; animation: heroFadeUp 0.7s 0.1s forwards; }
.hero-anim-2 { opacity: 0; animation: heroFadeUp 0.8s 0.3s forwards; }
.hero-anim-3 { opacity: 0; animation: heroFadeUp 0.6s 0.7s forwards; }
.hero-anim-4 { opacity: 0; animation: heroFadeUp 0.7s 0.85s forwards; }
.hero-anim-5 { opacity: 0; animation: heroFadeUp 0.8s 1.1s forwards; }

/* ════════════════════════════════════════
   WHY QUDRAT
   ════════════════════════════════════════ */
.why-card {
  padding: 1.5rem; border-radius: 1rem;
  border: 1px solid rgba(255,255,255,0.06); background: rgba(255,255,255,0.02);
  transition: border-color 0.3s, background 0.3s, transform 0.3s;
}
.why-card:hover {
  border-color: rgba(201,169,110,0.22); background: rgba(201,169,110,0.03); transform: translateY(-2px);
}
.why-icon {
  width: 2.5rem; height: 2.5rem; border-radius: 0.625rem;
  border: 1px solid rgba(201,169,110,0.2); background: rgba(201,169,110,0.07);
  display: flex; align-items: center; justify-content: center;
}

/* ════════════════════════════════════════
   TESTIMONIALS
   ════════════════════════════════════════ */
.testimonial-card {
  display: flex; flex-direction: column; padding: 1.75rem; border-radius: 1rem;
  border: 1px solid rgba(255,255,255,0.06); background: rgba(255,255,255,0.025);
  transition: border-color 0.3s, transform 0.3s;
}
.testimonial-card:hover { border-color: rgba(201,169,110,0.18); transform: translateY(-2px); }

/* ════════════════════════════════════════
   REST
   ════════════════════════════════════════ */
.ticker-track { animation: ticker 35s linear infinite; }
@keyframes ticker {
  0%   { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

.gallery-scroll::-webkit-scrollbar { display: none; }

.contact-label {
  display: block; font-size: 0.7rem; font-weight: 600;
  color: #94A3B8; margin-bottom: 0.375rem;
  text-transform: uppercase; letter-spacing: 0.08em;
}
.contact-input {
  display: block; width: 100%; padding: 0.7rem 0.875rem;
  border-radius: 0.75rem; font-size: 0.875rem; color: #fff;
  background: #111E35; border: 1px solid rgba(255,255,255,0.07);
  outline: none; transition: border-color 0.2s;
}
.contact-input:focus { border-color: rgba(201,169,110,0.5); }
.contact-input::placeholder { color: rgba(255,255,255,0.2); }

/* ════════════════════════════════════════
   Construction Block (home block 5)
   ════════════════════════════════════════ */
.cs-stage {
  background: #070B16;
  padding: 100px 0 90px;
  overflow: hidden;
}
.cs-wrap {
  display: grid;
  grid-template-columns: 55% 1fr;
  gap: 80px;
  align-items: center;
}

/* ── Left: image ── */
.cs-img-wrap {
  position: relative; border-radius: 20px; overflow: hidden;
  height: clamp(500px, 68vh, 720px);
  box-shadow: 0 40px 100px rgba(0,0,0,0.55), 0 0 0 1px rgba(255,255,255,0.04);
}
.cs-img {
  width: 100%; height: 100%;
  object-fit: cover; object-position: center 30%;
  display: block;
}
.cs-img-ol {
  position: absolute; inset: 0;
  background: linear-gradient(to top, rgba(4,8,20,0.90) 0%, rgba(4,8,20,0.18) 38%, transparent 55%);
}
.cs-stats {
  position: absolute; bottom: 0; left: 0; right: 0;
  display: grid; grid-template-columns: repeat(4,1fr);
  border-top: 1px solid rgba(201,169,110,0.12);
  border-radius: 0 0 20px 20px;
  background: rgba(4,10,24,0.60);
  backdrop-filter: blur(14px);
}
.cs-stat {
  padding: 20px 12px 22px;
  display: flex; flex-direction: column; align-items: center;
  text-align: center; gap: 6px;
}
.cs-stat + .cs-stat { border-left: 1px solid rgba(255,255,255,0.05); }
.cs-stat-n {
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: clamp(22px, 2.4vw, 34px);
  font-weight: 700; color: #D4AF37;
}
.cs-stat-l {
  font-size: 7.5px; font-weight: 700;
  letter-spacing: 0.18em; text-transform: uppercase;
  color: rgba(255,255,255,0.35);
}

/* ── Right: content ── */
.cs-ew {
  display: flex; align-items: center; gap: 12px; margin-bottom: 22px;
}
.cs-ew-line {
  display: block; width: 32px; height: 1px;
  background: #C9A96E; flex-shrink: 0;
}
.cs-ew-txt {
  font-size: 10px; font-weight: 700;
  letter-spacing: 0.42em; text-transform: uppercase; color: #C9A96E;
}
.cs-h2 {
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: clamp(46px, 5.2vw, 82px);
  font-weight: 700;
  line-height: 0.9;
  letter-spacing: 0.02em;
  text-transform: uppercase;
  display: flex; flex-direction: column;
  margin-bottom: 28px;
}
.cs-h2-w { color: #ffffff; display: block; }
.cs-h2-g {
  display: block;
  background: linear-gradient(135deg, #D4AF37 0%, #C9A96E 45%, #B8860B 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
.cs-sub {
  font-size: 15px; line-height: 1.72;
  color: rgba(255,255,255,0.50);
  max-width: 480px; margin-bottom: 36px;
}
.cs-feats {
  display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
  margin-bottom: 36px;
}
.cs-feat {
  display: flex; align-items: flex-start; gap: 13px;
  padding: 16px 14px; border-radius: 11px;
  background: rgba(255,255,255,0.025);
  border: 1px solid rgba(255,255,255,0.05);
  transition: border-color 0.22s, background 0.22s;
}
.cs-feat:hover {
  background: rgba(201,169,110,0.04);
  border-color: rgba(201,169,110,0.16);
}
.cs-feat-icon {
  width: 36px; height: 36px; flex-shrink: 0;
  border-radius: 9px;
  background: rgba(201,169,110,0.08);
  border: 1px solid rgba(201,169,110,0.16);
  display: flex; align-items: center; justify-content: center;
  color: #C9A96E;
}
.cs-feat-t {
  font-size: 12.5px; font-weight: 700;
  color: rgba(255,255,255,0.88); margin-bottom: 3px;
}
.cs-feat-d {
  font-size: 11px; line-height: 1.55;
  color: rgba(255,255,255,0.35);
}
.cs-btn {
  display: inline-flex; align-items: center; gap: 10px;
  background: rgba(201,169,110,0.07);
  border: 1px solid rgba(201,169,110,0.32);
  color: #C9A96E;
  font-size: 11px; font-weight: 700;
  letter-spacing: 0.18em; text-transform: uppercase;
  padding: 16px 32px; border-radius: 5px;
  transition: all 0.22s; text-decoration: none;
}
.cs-btn:hover {
  background: rgba(201,169,110,0.14);
  border-color: #C9A96E;
  transform: translateY(-2px);
}

/* ── Responsive ── */
@media (max-width: 1023px) {
  .cs-wrap { grid-template-columns: 1fr; gap: 52px; }
  .cs-img-wrap { height: clamp(300px, 50vw, 480px); }
}
@media (max-width: 639px) {
  .cs-feats { grid-template-columns: 1fr; }
  .cs-stats { grid-template-columns: repeat(2,1fr); }
  .cs-stage { padding: 70px 0 60px; }
}

/* ════════════════════════════════════════
   Mining Block (home block 4 / "block 3")
   ════════════════════════════════════════ */
.mn-stage {
  background: #0C1220;
  padding: 100px 0 90px;
  overflow: hidden;
  position: relative;
}
.mn-wrap {
  display: grid;
  grid-template-columns: 45% 1fr;
  gap: 80px;
  align-items: center;
}

/* ── Left panel ── */
.mn-ew {
  display: flex; align-items: center; gap: 12px; margin-bottom: 22px;
}
.mn-ew-line {
  display: block; width: 32px; height: 1px;
  background: #C9A96E; flex-shrink: 0;
}
.mn-ew-txt {
  font-size: 10px; font-weight: 700;
  letter-spacing: 0.42em; text-transform: uppercase; color: #C9A96E;
}
.mn-h2 {
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: clamp(46px, 5.2vw, 82px);
  font-weight: 700;
  line-height: 0.9;
  letter-spacing: 0.02em;
  text-transform: uppercase;
  display: flex;
  flex-direction: column;
  margin-bottom: 28px;
}
.mn-h2-w { color: #ffffff; display: block; }
.mn-h2-g {
  display: block;
  background: linear-gradient(135deg, #D4AF37 0%, #C9A96E 45%, #B8860B 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
.mn-sub {
  font-size: 15px; line-height: 1.72;
  color: rgba(255,255,255,0.50);
  max-width: 480px; margin-bottom: 36px;
}

/* Feature tiles 2×2 */
.mn-feats {
  display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
  margin-bottom: 36px;
}
.mn-feat {
  display: flex; align-items: flex-start; gap: 13px;
  padding: 16px 14px; border-radius: 11px;
  background: rgba(255,255,255,0.025);
  border: 1px solid rgba(255,255,255,0.05);
  transition: border-color 0.22s, background 0.22s;
}
.mn-feat:hover {
  background: rgba(201,169,110,0.04);
  border-color: rgba(201,169,110,0.16);
}
.mn-feat-icon {
  width: 36px; height: 36px; flex-shrink: 0;
  border-radius: 9px;
  background: rgba(201,169,110,0.08);
  border: 1px solid rgba(201,169,110,0.16);
  display: flex; align-items: center; justify-content: center;
  color: #C9A96E;
}
.mn-feat-t {
  font-size: 12.5px; font-weight: 700;
  color: rgba(255,255,255,0.88); margin-bottom: 3px;
}
.mn-feat-d {
  font-size: 11px; line-height: 1.55;
  color: rgba(255,255,255,0.35);
}

/* CTA button */
.mn-btn {
  display: inline-flex; align-items: center; gap: 10px;
  background: #C9A96E; color: #070B16;
  font-size: 11px; font-weight: 700;
  letter-spacing: 0.18em; text-transform: uppercase;
  padding: 16px 32px; border-radius: 5px;
  transition: background 0.2s, transform 0.2s;
  text-decoration: none;
}
.mn-btn:hover { background: #D4AF37; transform: translateY(-2px); }

/* ── Right panel: image ── */
.mn-img-wrap {
  position: relative; border-radius: 20px; overflow: hidden;
  height: clamp(500px, 68vh, 720px);
  box-shadow: 0 40px 100px rgba(0,0,0,0.55), 0 0 0 1px rgba(255,255,255,0.04);
}
.mn-img {
  width: 100%; height: 100%;
  object-fit: cover; object-position: center 55%;
  display: block;
}
.mn-img-ol {
  position: absolute; inset: 0;
  background: linear-gradient(to top, rgba(4,8,20,0.90) 0%, rgba(4,8,20,0.18) 38%, transparent 55%);
}

/* Stats bar on image */
.mn-stats {
  position: absolute; bottom: 0; left: 0; right: 0;
  display: grid; grid-template-columns: repeat(4,1fr);
  border-top: 1px solid rgba(201,169,110,0.12);
  border-radius: 0 0 20px 20px;
  background: rgba(4,10,24,0.60);
  backdrop-filter: blur(14px);
}
.mn-stat {
  padding: 20px 12px 22px;
  display: flex; flex-direction: column; align-items: center;
  text-align: center; gap: 6px;
}
.mn-stat + .mn-stat { border-left: 1px solid rgba(255,255,255,0.05); }
.mn-stat-n {
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: clamp(22px, 2.4vw, 34px);
  font-weight: 700; color: #D4AF37;
}
.mn-stat-l {
  font-size: 7.5px; font-weight: 700;
  letter-spacing: 0.18em; text-transform: uppercase;
  color: rgba(255,255,255,0.35);
}

/* ── Responsive ── */
@media (max-width: 1023px) {
  .mn-wrap { grid-template-columns: 1fr; gap: 52px; }
  .mn-right { order: -1; }
  .mn-img-wrap { height: clamp(300px, 50vw, 480px); }
}
@media (max-width: 639px) {
  .mn-feats { grid-template-columns: 1fr; }
  .mn-stats { grid-template-columns: repeat(2,1fr); }
  .mn-stage { padding: 70px 0 60px; }
}
</style>
