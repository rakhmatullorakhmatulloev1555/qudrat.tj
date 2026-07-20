<template>
  <MainLayout>
    <SeoHead
      title="Новости"
      ogTitle="Новости — QUDRAT | Строительство, горнодобыча, корпоративные обновления"
      description="Последние новости и события компании QUDRAT. Строительство, горнодобыча, корпоративные обновления."
      image="/images/og-news.jpg"
      path="/news"
    />

    <!-- ═══ HERO ═══ -->
    <section class="news-hero relative overflow-hidden flex items-end">
      <div class="absolute inset-0">
        <img :src="asset('/images/hero-8.jpg')" alt="" class="w-full h-full object-cover" style="opacity:0.28"/>
        <div class="absolute inset-0" style="background:linear-gradient(to right,rgba(7,11,22,0.95) 0%,rgba(7,11,22,0.75) 55%,rgba(7,11,22,0.50) 100%)"></div>
        <div class="absolute inset-0" style="background:linear-gradient(to top,rgba(7,11,22,0.85) 0%,transparent 45%)"></div>
      </div>

      <div class="relative z-10 max-w-[1400px] mx-auto px-6 lg:px-12 pb-20 pt-10 w-full">
        <div class="inline-flex items-center gap-3 mb-8">
          <div class="h-px w-10" style="background:linear-gradient(to right,transparent,rgba(201,169,110,0.6))"></div>
          <span class="text-gold/70 text-[9px] font-bold uppercase tracking-[0.55em]">QUDRAT LLC</span>
          <div class="h-px w-10" style="background:linear-gradient(to left,transparent,rgba(201,169,110,0.6))"></div>
        </div>
        <h1 class="font-black text-white uppercase leading-none mb-6"
          style="font-size:clamp(44px,7vw,100px); letter-spacing:-0.02em">
          Новости
        </h1>
        <p class="text-white/55 text-base lg:text-lg leading-relaxed max-w-xl">
          Последние новости и события компании QUDRAT. Строительство, горнодобыча, корпоративные обновления.
        </p>
      </div>

      <div class="absolute bottom-0 inset-x-0 h-16 pointer-events-none"
        style="background:linear-gradient(to top,#070B16,transparent)"></div>
    </section>

    <!-- ═══ ЛЕНТА ═══ -->
    <section class="py-20" style="background:#070B16">
      <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <!-- Фильтр по категориям -->
        <div v-if="posts.length" class="flex flex-wrap gap-2 mb-12">
          <button
            v-for="cat in categories" :key="cat.key"
            @click="activeCat = cat.key"
            class="px-4 py-2 rounded-full text-[11px] font-bold uppercase tracking-wider border transition-all duration-300"
            :class="activeCat === cat.key
              ? 'border-gold bg-gold/10 text-gold'
              : 'border-white/10 text-white/40 hover:text-white hover:border-white/25'"
          >
            {{ cat.label }}
          </button>
        </div>

        <!-- Сетка карточек -->
        <div v-if="filtered.length" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          <Link
            v-for="post in filtered" :key="post.id"
            :href="route('news.show', post.slug)"
            class="news-card group flex flex-col rounded-2xl overflow-hidden border border-white/6 hover:border-gold/30 transition-all duration-500"
            style="background:#0C1220"
          >
            <!-- Изображение или градиент-заглушка -->
            <div class="relative aspect-[16/10] overflow-hidden">
              <img v-if="post.image" :src="asset(post.image)" :alt="post.title" loading="lazy"
                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"/>
              <div v-else class="w-full h-full flex items-center justify-center"
                style="background:linear-gradient(135deg,#111C2E,#0A1420)">
                <span class="font-black text-white/5" style="font-family:'Cormorant Garamond',serif; font-size:64px">Q</span>
              </div>
              <span class="absolute top-4 left-4 px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest text-gold"
                style="background:rgba(7,11,22,0.75); backdrop-filter:blur(6px); border:1px solid rgba(201,169,110,0.25)">
                {{ post.category_label }}
              </span>
            </div>

            <!-- Текст -->
            <div class="flex flex-col flex-1 p-6">
              <time class="text-white/35 text-[11px] uppercase tracking-widest mb-3" :datetime="post.date_iso">{{ post.date }}</time>
              <h2 class="text-white font-bold text-lg leading-snug mb-3 group-hover:text-gold transition-colors duration-300"
                style="font-family:'Cormorant Garamond',Georgia,serif; font-size:22px">
                {{ post.title }}
              </h2>
              <p v-if="post.excerpt" class="text-white/45 text-sm leading-relaxed mb-5 line-clamp-3">{{ post.excerpt }}</p>
              <span class="mt-auto inline-flex items-center gap-2 text-gold text-[11px] font-bold uppercase tracking-widest">
                Читать
                <Icon name="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" :stroke-width="2" />
              </span>
            </div>
          </Link>
        </div>

        <!-- Пусто (нет новостей в выбранной категории) -->
        <div v-else-if="posts.length" class="text-center py-24">
          <p class="text-white/40 text-sm">В этой категории пока нет публикаций.</p>
        </div>

        <!-- Пусто (совсем нет новостей) -->
        <div v-else class="text-center py-24 max-w-md mx-auto">
          <div class="w-16 h-16 mx-auto mb-6 rounded-2xl border border-gold/20 bg-gold/5 flex items-center justify-center">
            <Icon name="mail" class="w-7 h-7 text-gold/60" :stroke-width="1.4" />
          </div>
          <h2 class="text-white font-bold text-xl mb-3" style="font-family:'Cormorant Garamond',serif">Скоро здесь появятся новости</h2>
          <p class="text-white/45 text-sm leading-relaxed mb-8">
            Мы готовим публикации о ходе строительства, горнодобыче и корпоративных событиях. Подпишитесь, чтобы узнавать первыми.
          </p>
          <Link :href="route('contacts')"
            class="inline-flex items-center gap-2 bg-gold hover:bg-gold-600 text-dark font-bold px-6 py-3 rounded-xl uppercase tracking-widest text-[11px] transition-colors">
            Связаться с нами
            <Icon name="arrow-right" class="w-4 h-4" :stroke-width="2" />
          </Link>
        </div>
      </div>
    </section>
  </MainLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import Icon from '@/Components/Icon.vue';
import { useAsset } from '@/composables/useAsset.js';

const props = defineProps({
  posts: { type: Array, default: () => [] },
});

const { asset } = useAsset();

const activeCat = ref('all');

// Категории строим динамически из того, что реально есть в ленте
const categories = computed(() => {
  const labels = {};
  props.posts.forEach(p => { labels[p.category] = p.category_label; });
  return [{ key: 'all', label: 'Все' },
    ...Object.entries(labels).map(([key, label]) => ({ key, label }))];
});

const filtered = computed(() =>
  activeCat.value === 'all'
    ? props.posts
    : props.posts.filter(p => p.category === activeCat.value)
);
</script>

<style scoped>
.news-hero {
  height: 100vh;
  min-height: 640px;
  background: #070B16;
  padding-top: 90px;
}
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
