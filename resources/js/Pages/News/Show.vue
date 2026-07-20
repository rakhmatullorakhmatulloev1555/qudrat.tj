<template>
  <MainLayout>
    <SeoHead
      :title="post.title"
      :ogTitle="`${post.title} — QUDRAT`"
      :description="post.excerpt || post.title"
      :image="post.image || '/images/og-news.jpg'"
    />

    <article class="pt-32 pb-24" style="background:#070B16">
      <div class="mx-auto px-6" style="max-width:900px">

        <!-- Назад -->
        <Link :href="route('news')"
          class="inline-flex items-center gap-2 text-white/40 hover:text-gold text-[11px] font-bold uppercase tracking-widest mb-10 transition-colors">
          <Icon name="arrow-long-right" class="w-4 h-4 rotate-180" :stroke-width="1.8" />
          Все новости
        </Link>

        <!-- Мета -->
        <div class="flex items-center gap-3 mb-5">
          <span class="px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest text-gold"
            style="background:rgba(201,169,110,0.1); border:1px solid rgba(201,169,110,0.25)">
            {{ post.category_label }}
          </span>
          <time class="text-white/35 text-[11px] uppercase tracking-widest" :datetime="post.date_iso">{{ post.date }}</time>
        </div>

        <!-- Заголовок -->
        <h1 class="text-white font-bold leading-tight mb-8"
          style="font-family:'Cormorant Garamond',Georgia,serif; font-size:clamp(30px,5vw,52px)">
          {{ post.title }}
        </h1>

        <!-- Изображение -->
        <div v-if="post.image" class="rounded-2xl overflow-hidden mb-10 border border-white/6">
          <img :src="asset(post.image)" :alt="post.title" class="w-full object-cover"/>
        </div>

        <!-- Лид -->
        <p v-if="post.excerpt" class="text-white/70 text-lg leading-relaxed mb-8 font-light">{{ post.excerpt }}</p>

        <!-- Тело статьи (rich-text, редактируется только в админке) -->
        <div class="news-body" v-html="post.body"></div>

        <!-- CTA -->
        <div class="mt-14 pt-10 border-t border-white/8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
          <p class="text-white/50 text-sm">Остались вопросы по проектам QUDRAT?</p>
          <Link :href="route('contacts')"
            class="inline-flex items-center gap-2 bg-gold hover:bg-gold-600 text-dark font-bold px-6 py-3 rounded-xl uppercase tracking-widest text-[11px] transition-colors shrink-0">
            Связаться с нами
            <Icon name="arrow-right" class="w-4 h-4" :stroke-width="2" />
          </Link>
        </div>
      </div>

      <!-- Другие новости -->
      <div v-if="more.length" class="max-w-[1400px] mx-auto px-6 lg:px-12 mt-24">
        <div class="flex items-center gap-3 mb-8">
          <span class="block h-px w-8 bg-gold"></span>
          <h2 class="text-[10px] font-bold uppercase tracking-[0.38em] text-gold">Читайте также</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
          <Link
            v-for="m in more" :key="m.slug"
            :href="route('news.show', m.slug)"
            class="group flex flex-col rounded-2xl overflow-hidden border border-white/6 hover:border-gold/30 transition-all duration-500"
            style="background:#0C1220"
          >
            <div class="relative aspect-[16/10] overflow-hidden">
              <img v-if="m.image" :src="asset(m.image)" :alt="m.title" loading="lazy"
                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"/>
              <div v-else class="w-full h-full flex items-center justify-center" style="background:linear-gradient(135deg,#111C2E,#0A1420)">
                <span class="font-black text-white/5" style="font-family:'Cormorant Garamond',serif; font-size:52px">Q</span>
              </div>
            </div>
            <div class="p-5">
              <div class="text-white/35 text-[10px] uppercase tracking-widest mb-2">{{ m.category_label }} · {{ m.date }}</div>
              <div class="text-white font-bold leading-snug group-hover:text-gold transition-colors"
                style="font-family:'Cormorant Garamond',serif; font-size:18px">{{ m.title }}</div>
            </div>
          </Link>
        </div>
      </div>
    </article>
  </MainLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import Icon from '@/Components/Icon.vue';
import { useAsset } from '@/composables/useAsset.js';

defineProps({
  post: { type: Object, required: true },
  more: { type: Array, default: () => [] },
});

const { asset } = useAsset();
</script>

<style scoped>
/* Типографика тела статьи (контент из админки) */
.news-body {
  color: rgba(255,255,255,0.72);
  font-size: 16px;
  line-height: 1.85;
}
.news-body :deep(p) { margin-bottom: 1.25em; }
.news-body :deep(h2),
.news-body :deep(h3) {
  color: #fff;
  font-family: 'Cormorant Garamond', Georgia, serif;
  margin: 1.6em 0 0.6em;
  line-height: 1.25;
}
.news-body :deep(h2) { font-size: 28px; }
.news-body :deep(h3) { font-size: 22px; }
.news-body :deep(strong) { color: #fff; font-weight: 700; }
.news-body :deep(a) { color: #C9A96E; text-decoration: underline; }
.news-body :deep(ul),
.news-body :deep(ol) { margin: 0 0 1.25em 1.4em; }
.news-body :deep(li) { margin-bottom: 0.5em; }
.news-body :deep(blockquote) {
  border-left: 3px solid #C9A96E;
  padding-left: 1.2em;
  margin: 1.5em 0;
  color: rgba(255,255,255,0.6);
  font-style: italic;
}
.news-body :deep(img) { max-width: 100%; border-radius: 12px; margin: 1.5em 0; }
</style>
