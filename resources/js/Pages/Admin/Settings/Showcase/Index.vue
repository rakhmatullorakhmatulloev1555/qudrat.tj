<template>
  <AdminLayout page-title="Знаковые объекты" page-subtitle="CMS — блок на странице «Проекты» и страницы /projects/{slug}">
    <Head title="Знаковые объекты" />

    <!-- Toolbar -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
      <p class="text-slate-500 text-xs">
        Порядок карточек на сайте — стрелками. Черновики видны только по ссылке «Предпросмотр».
      </p>
      <button @click="openEditor()"
        class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm text-[#0F172A] transition-all"
        style="background:#C9A96E">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Создать проект
      </button>
    </div>

    <!-- List -->
    <div class="space-y-3">
      <div v-for="(p, i) in projects" :key="p.id"
        class="rounded-xl border border-white/6 p-4 flex flex-wrap items-center gap-4"
        style="background:#1E293B">

        <!-- Order controls -->
        <div class="flex flex-col gap-1">
          <button @click="move(i, -1)" :disabled="i === 0"
            class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 disabled:opacity-20 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5"/></svg>
          </button>
          <button @click="move(i, 1)" :disabled="i === projects.length - 1"
            class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 disabled:opacity-20 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
          </button>
        </div>

        <!-- Thumb -->
        <div class="w-24 h-16 rounded-lg overflow-hidden flex-shrink-0" style="background:#0F172A">
          <img v-if="p.hero_image" :src="p.hero_image" :alt="p.name" class="w-full h-full object-cover"
            @error="e => e.target.style.display = 'none'"/>
        </div>

        <!-- Info -->
        <div class="flex-1 min-w-[200px]">
          <div class="flex items-center gap-2 flex-wrap">
            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" :style="`background:${p.accent}`"></span>
            <span class="text-white font-semibold text-sm">{{ p.name }}</span>
            <span :class="statusBadge(p.status)" class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">
              {{ statusLabel(p.status) }}
            </span>
            <span v-if="p.is_featured" class="text-[10px] font-bold px-2 py-0.5 rounded-full text-gold bg-gold/10 border border-gold/20 uppercase tracking-wider">
              В блоке
            </span>
            <span v-if="!p.is_visible" class="text-[10px] font-bold px-2 py-0.5 rounded-full text-slate-400 bg-slate-700/40 uppercase tracking-wider">
              Скрыт
            </span>
          </div>
          <div class="text-slate-500 text-xs mt-1">
            /projects/{{ p.slug }}
            <span v-if="p.published_at" class="ml-3">Опубликован: {{ p.published_at }}</span>
            <span class="ml-3">Изменён: {{ p.updated_at }}</span>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-1.5 flex-wrap">
          <a :href="p.preview_url" target="_blank"
            class="px-3 py-2 rounded-lg text-xs font-medium text-slate-300 border border-white/10 hover:border-white/25 hover:text-white transition-all">
            Предпросмотр
          </a>
          <button @click="togglePublish(p)"
            class="px-3 py-2 rounded-lg text-xs font-medium border transition-all"
            :class="p.status === 'published'
              ? 'text-amber-400 border-amber-500/25 hover:bg-amber-500/10'
              : 'text-emerald-400 border-emerald-500/25 hover:bg-emerald-500/10'">
            {{ p.status === 'published' ? 'Снять' : 'Опубликовать' }}
          </button>
          <button @click="openEditor(p)"
            class="px-3 py-2 rounded-lg text-xs font-medium text-gold border border-gold/30 hover:bg-gold/10 transition-all">
            Редактировать
          </button>
          <button @click="duplicateItem(p)" title="Дублировать"
            class="p-2 rounded-lg text-slate-400 border border-white/10 hover:text-white hover:border-white/25 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75"/></svg>
          </button>
          <button @click="toggleArchive(p)" :title="p.status === 'archived' ? 'Вернуть из архива' : 'В архив'"
            class="p-2 rounded-lg text-slate-400 border border-white/10 hover:text-amber-400 hover:border-amber-500/30 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>
          </button>
          <button @click="deleteItem(p)" title="Удалить"
            class="p-2 rounded-lg text-red-400/70 border border-red-500/15 hover:text-red-400 hover:bg-red-500/10 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
          </button>
        </div>
      </div>

      <div v-if="!projects.length" class="py-16 text-center text-slate-500 text-sm rounded-xl border border-white/6" style="background:#1E293B">
        Проектов пока нет. Нажмите «Создать проект».
      </div>
    </div>

    <!-- ═══ EDITOR MODAL ═══ -->
    <Teleport to="body">
      <div v-if="editor.open" class="fixed inset-0 z-50 flex items-start justify-center p-4 overflow-y-auto"
        style="background:rgba(0,0,0,0.7); backdrop-filter:blur(4px)" @click.self="closeEditor">
        <div class="w-full max-w-3xl rounded-2xl border border-white/8 shadow-2xl my-6" style="background:#1E293B">

          <!-- Header -->
          <div class="flex items-center justify-between px-6 py-4 border-b border-white/6 sticky top-0 rounded-t-2xl z-10" style="background:#1E293B">
            <h3 class="text-white font-semibold">{{ editor.editing ? `Редактировать: ${editor.editing.name}` : 'Новый проект' }}</h3>
            <button @click="closeEditor" class="text-slate-400 hover:text-white transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
          </div>

          <!-- Tabs -->
          <div class="flex gap-1 px-6 pt-4 flex-wrap">
            <button v-for="tab in tabs" :key="tab.key" @click="activeTab = tab.key"
              class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all"
              :class="activeTab === tab.key ? 'text-[#0F172A]' : 'text-slate-400 hover:text-white'"
              :style="activeTab === tab.key ? 'background:#C9A96E' : ''">
              {{ tab.label }}
            </button>
          </div>

          <form @submit.prevent="submit" class="p-6 space-y-5">

            <!-- ═══ TAB: Основное ═══ -->
            <div v-show="activeTab === 'main'" class="space-y-4">
              <div class="grid sm:grid-cols-2 gap-4">
                <div>
                  <label class="lbl">Название *</label>
                  <input v-model="form.name" class="field w-full" placeholder="QUDRAT RESIDENCE" required/>
                  <p v-if="errors.name" class="err">{{ errors.name }}</p>
                </div>
                <div>
                  <label class="lbl">Slug (URL страницы)</label>
                  <input v-model="form.slug" class="field w-full" placeholder="qudrat-residence"/>
                  <p class="hint">/projects/{{ form.slug || 'авто-из-названия' }}</p>
                </div>
              </div>

              <div class="grid sm:grid-cols-3 gap-4">
                <div>
                  <label class="lbl">Акцентный цвет</label>
                  <div class="flex items-center gap-2">
                    <input v-model="form.accent" type="color" class="w-10 h-10 rounded-lg cursor-pointer border border-white/10" style="background:#0F172A"/>
                    <input v-model="form.accent" class="field flex-1" placeholder="#C9A96E"/>
                  </div>
                </div>
                <div>
                  <label class="lbl">Кнопка CTA на странице</label>
                  <select v-model="form.cta_type" class="field w-full">
                    <option value="apts">Выбрать квартиру (каталог)</option>
                    <option value="contact">Связаться с нами</option>
                    <option value="mining">Подробнее о горнодобыче</option>
                  </select>
                </div>
                <div class="space-y-2.5 pt-1">
                  <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" v-model="form.is_featured" class="w-4 h-4 accent-[#C9A96E]"/>
                    <span class="text-sm text-slate-300">Показывать в блоке «Знаковые объекты»</span>
                  </label>
                  <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" v-model="form.is_visible" class="w-4 h-4 accent-[#C9A96E]"/>
                    <span class="text-sm text-slate-300">Видимость на сайте</span>
                  </label>
                </div>
              </div>

              <div>
                <label class="lbl">Иконки особенностей (общие для всех языков)</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                  <select v-for="(icon, i) in form.feature_icons" :key="i" v-model="form.feature_icons[i]" class="field w-full">
                    <option v-for="(label, key) in iconLabels" :key="key" :value="key">{{ i + 1 }}. {{ label }}</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- ═══ TABS: RU / TJ / EN ═══ -->
            <div v-for="lc in ['ru', 'tj', 'en']" :key="lc" v-show="activeTab === lc" class="space-y-4">
              <div class="grid sm:grid-cols-2 gap-4">
                <div>
                  <label class="lbl">Тип / класс ({{ lc.toUpperCase() }})</label>
                  <input v-model="form.content[lc].type_label" class="field w-full" placeholder="Премиум"/>
                </div>
                <div>
                  <label class="lbl">Расположение</label>
                  <input v-model="form.content[lc].location" class="field w-full" placeholder="Душанбе, Таджикистан"/>
                </div>
              </div>
              <div class="grid sm:grid-cols-2 gap-4">
                <div>
                  <label class="lbl">Описание на карточке</label>
                  <input v-model="form.content[lc].card_desc" class="field w-full" placeholder="Элитный жилой комплекс…"/>
                </div>
                <div>
                  <label class="lbl">Статус (текст)</label>
                  <input v-model="form.content[lc].status_label" class="field w-full" placeholder="В продаже"/>
                </div>
              </div>
              <div>
                <label class="lbl">Слоган (hero страницы)</label>
                <input v-model="form.content[lc].tagline" class="field w-full" placeholder="Элитная резиденция бизнес-класса…"/>
              </div>
              <div>
                <label class="lbl">О проекте — абзац 1</label>
                <textarea v-model="form.content[lc].about1" rows="3" class="field w-full"></textarea>
              </div>
              <div>
                <label class="lbl">О проекте — абзац 2</label>
                <textarea v-model="form.content[lc].about2" rows="3" class="field w-full"></textarea>
              </div>

              <div>
                <label class="lbl">Статистика (3 показателя: значение + подпись)</label>
                <div class="grid grid-cols-3 gap-2">
                  <div v-for="(s, i) in form.content[lc].stats" :key="i" class="space-y-1.5">
                    <input v-model="s.v" class="field w-full" :placeholder="['от 45 м²','2025','$1,200'][i]"/>
                    <input v-model="s.l" class="field w-full" :placeholder="['Площадь','Сдача','За м²'][i]"/>
                  </div>
                </div>
                <p class="hint">Второй показатель также выводится как «Срок сдачи» в полосе фактов.</p>
              </div>

              <div>
                <label class="lbl">Ключевые особенности (4 шт.)</label>
                <div class="space-y-2">
                  <div v-for="(f, i) in form.content[lc].features" :key="i" class="grid sm:grid-cols-[1fr_2fr] gap-2">
                    <input v-model="f.title" class="field w-full" :placeholder="`Особенность ${i + 1}`"/>
                    <input v-model="f.desc" class="field w-full" placeholder="Краткое описание"/>
                  </div>
                </div>
              </div>
            </div>

            <!-- ═══ TAB: Медиа ═══ -->
            <div v-show="activeTab === 'media'" class="space-y-5">
              <div>
                <label class="lbl">Главное изображение (hero + карточка)</label>
                <div class="flex items-center gap-4">
                  <div class="w-40 h-24 rounded-lg overflow-hidden border border-white/10 flex-shrink-0" style="background:#0F172A">
                    <img v-if="heroPreview" :src="heroPreview" class="w-full h-full object-cover"/>
                  </div>
                  <div>
                    <input ref="heroInput" type="file" accept="image/*" class="hidden" @change="e => onFile(e, 'hero')"/>
                    <button type="button" @click="$refs.heroInput.click()"
                      class="px-4 py-2 rounded-lg text-xs font-medium text-slate-300 border border-white/10 hover:border-white/25 transition-all">
                      Загрузить изображение
                    </button>
                    <p class="hint mt-1">JPEG, PNG, WebP — макс. 8 МБ</p>
                    <p v-if="errors.hero_file" class="err">{{ errors.hero_file }}</p>
                  </div>
                </div>
              </div>

              <div>
                <label class="lbl">Галерея (первое фото также показывается в блоке «О проекте»)</label>
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                  <div v-for="(img, i) in galleryKeep" :key="'k' + i" class="relative group rounded-lg overflow-hidden border border-white/10 aspect-[4/3]" style="background:#0F172A">
                    <img :src="img" class="w-full h-full object-cover"/>
                    <button type="button" @click="galleryKeep.splice(i, 1)"
                      class="absolute top-1 right-1 w-6 h-6 rounded-full bg-black/70 text-red-400 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-xs">✕</button>
                  </div>
                  <div v-for="(f, i) in galleryFiles" :key="'n' + i" class="relative group rounded-lg overflow-hidden border border-gold/30 aspect-[4/3]" style="background:#0F172A">
                    <img :src="galleryPreviews[i]" class="w-full h-full object-cover"/>
                    <button type="button" @click="removeNewGallery(i)"
                      class="absolute top-1 right-1 w-6 h-6 rounded-full bg-black/70 text-red-400 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-xs">✕</button>
                    <span class="absolute bottom-1 left-1 text-[9px] font-bold text-gold bg-black/60 px-1.5 py-0.5 rounded">NEW</span>
                  </div>
                  <button type="button" @click="$refs.galleryInput.click()"
                    class="aspect-[4/3] rounded-lg border-2 border-dashed border-white/15 hover:border-gold/40 transition-colors flex items-center justify-center text-slate-500 hover:text-gold text-2xl">
                    +
                  </button>
                </div>
                <input ref="galleryInput" type="file" accept="image/*" multiple class="hidden" @change="e => onFile(e, 'gallery')"/>
              </div>
            </div>

            <!-- ═══ TAB: SEO ═══ -->
            <div v-show="activeTab === 'seo'" class="space-y-5">
              <div class="flex gap-1">
                <button v-for="lc in ['ru', 'tj', 'en']" :key="lc" type="button" @click="seoLocale = lc"
                  class="px-3 py-1.5 rounded-lg text-[11px] font-bold uppercase transition-all"
                  :class="seoLocale === lc ? 'text-[#0F172A]' : 'text-slate-400 hover:text-white'"
                  :style="seoLocale === lc ? 'background:#C9A96E' : 'background:#0F172A'">
                  {{ lc }}
                </button>
              </div>

              <div>
                <label class="lbl">Meta title ({{ seoLocale.toUpperCase() }})</label>
                <input v-model="form.seo.meta_title[seoLocale]" class="field w-full" placeholder="По умолчанию — название проекта"/>
              </div>
              <div>
                <label class="lbl">Meta description ({{ seoLocale.toUpperCase() }})</label>
                <textarea v-model="form.seo.meta_description[seoLocale]" rows="2" class="field w-full" placeholder="По умолчанию — слоган"></textarea>
              </div>
              <div class="grid sm:grid-cols-2 gap-4">
                <div>
                  <label class="lbl">Open Graph title ({{ seoLocale.toUpperCase() }})</label>
                  <input v-model="form.seo.og_title[seoLocale]" class="field w-full" placeholder="По умолчанию — «Название — QUDRAT»"/>
                </div>
                <div>
                  <label class="lbl">Open Graph description ({{ seoLocale.toUpperCase() }})</label>
                  <input v-model="form.seo.og_description[seoLocale]" class="field w-full" placeholder="По умолчанию — meta description"/>
                </div>
              </div>

              <div class="border-t border-white/6 pt-4 grid sm:grid-cols-2 gap-4">
                <div>
                  <label class="lbl">Open Graph изображение</label>
                  <div class="flex items-center gap-3">
                    <div class="w-24 h-14 rounded-lg overflow-hidden border border-white/10 flex-shrink-0" style="background:#0F172A">
                      <img v-if="ogPreview" :src="ogPreview" class="w-full h-full object-cover"/>
                    </div>
                    <div>
                      <input ref="ogInput" type="file" accept="image/*" class="hidden" @change="e => onFile(e, 'og')"/>
                      <button type="button" @click="$refs.ogInput.click()"
                        class="px-3 py-1.5 rounded-lg text-xs text-slate-300 border border-white/10 hover:border-white/25 transition-all">
                        Загрузить
                      </button>
                      <p class="hint mt-1">По умолчанию — главное изображение</p>
                    </div>
                  </div>
                </div>
                <div>
                  <label class="lbl">Canonical URL</label>
                  <input v-model="form.seo.canonical_url" class="field w-full" placeholder="https://qudrat.tj/projects/…"/>
                  <p class="hint">Пусто — формируется автоматически.</p>
                </div>
              </div>

              <div class="border-t border-white/6 pt-4 grid sm:grid-cols-2 gap-4">
                <div>
                  <label class="lbl">Schema.org — тип</label>
                  <select v-model="form.seo.schema_type" class="field w-full">
                    <option value="ApartmentComplex">ApartmentComplex (жилой комплекс)</option>
                    <option value="Residence">Residence (резиденция)</option>
                    <option value="Place">Place (место)</option>
                    <option value="Organization">Organization (организация)</option>
                    <option value="Product">Product (продукт)</option>
                    <option value="none">Отключить разметку</option>
                  </select>
                </div>
                <div>
                  <label class="lbl">Свой JSON-LD (переопределяет тип)</label>
                  <textarea v-model="form.seo.schema_json" rows="3" class="field w-full font-mono text-xs" placeholder='{"@context":"https://schema.org", …}'></textarea>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-between gap-3 pt-3 border-t border-white/6">
              <p v-if="Object.keys(errors).length" class="text-red-400 text-xs">Проверьте поля: {{ Object.keys(errors).join(', ') }}</p>
              <div class="flex gap-3 ml-auto">
                <button type="button" @click="closeEditor"
                  class="px-6 py-2.5 rounded-xl text-sm text-slate-300 border border-white/10 hover:border-white/20 transition-all">
                  Отмена
                </button>
                <button type="submit" :disabled="processing"
                  class="px-8 py-2.5 rounded-xl text-sm font-semibold text-[#0F172A] transition-all disabled:opacity-60"
                  style="background:#C9A96E">
                  {{ processing ? 'Сохраняем…' : (editor.editing ? 'Сохранить' : 'Создать (черновик)') }}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { SHOWCASE_ICON_LABELS as iconLabels } from '@/showcaseIcons';

const props = defineProps({
  projects: { type: Array, default: () => [] },
});

const tabs = [
  { key: 'main',  label: 'Основное' },
  { key: 'ru',    label: 'Русский' },
  { key: 'tj',    label: 'Тоҷикӣ' },
  { key: 'en',    label: 'English' },
  { key: 'media', label: 'Медиа' },
  { key: 'seo',   label: 'SEO' },
];

const editor     = reactive({ open: false, editing: null });
const activeTab  = ref('main');
const seoLocale  = ref('ru');
const errors     = ref({});
const processing = ref(false);

// Файлы
const heroFile        = ref(null);
const ogFile          = ref(null);
const galleryFiles    = ref([]);
const galleryPreviews = ref([]);
const galleryKeep     = ref([]);
const heroLocalPreview = ref(null);
const ogLocalPreview   = ref(null);

const emptyLocale = () => ({
  type_label: '', location: '', card_desc: '', tagline: '', status_label: '',
  about1: '', about2: '',
  stats: [{ v: '', l: '' }, { v: '', l: '' }, { v: '', l: '' }],
  features: [{ title: '', desc: '' }, { title: '', desc: '' }, { title: '', desc: '' }, { title: '', desc: '' }],
});

const emptySeo = () => ({
  meta_title:       { ru: '', tj: '', en: '' },
  meta_description: { ru: '', tj: '', en: '' },
  og_title:         { ru: '', tj: '', en: '' },
  og_description:   { ru: '', tj: '', en: '' },
  og_image: '', canonical_url: '', schema_type: 'ApartmentComplex', schema_json: '',
});

const form = reactive({
  name: '', slug: '', accent: '#C9A96E', cta_type: 'apts',
  is_featured: true, is_visible: true,
  feature_icons: ['building', 'building', 'building', 'building'],
  content: { ru: emptyLocale(), tj: emptyLocale(), en: emptyLocale() },
  seo: emptySeo(),
});

const heroPreview = computed(() => heroLocalPreview.value || editor.editing?.hero_image || null);
const ogPreview   = computed(() => ogLocalPreview.value || form.seo.og_image || null);

/** Нормализует локаль из БД к полной структуре формы. */
function normalizeLocale(src = {}) {
  const base = emptyLocale();
  const out = { ...base, ...src };
  out.stats = base.stats.map((s, i) => ({ v: src.stats?.[i]?.v ?? '', l: src.stats?.[i]?.l ?? '' }));
  out.features = base.features.map((f, i) => ({ title: src.features?.[i]?.title ?? '', desc: src.features?.[i]?.desc ?? '' }));
  return out;
}

function normalizeSeo(src = {}) {
  const base = emptySeo();
  const out = { ...base };
  for (const k of ['meta_title', 'meta_description', 'og_title', 'og_description']) {
    out[k] = { ru: src[k]?.ru ?? '', tj: src[k]?.tj ?? '', en: src[k]?.en ?? '' };
  }
  out.og_image      = src.og_image ?? '';
  out.canonical_url = src.canonical_url ?? '';
  out.schema_type   = src.schema_type || 'ApartmentComplex';
  out.schema_json   = src.schema_json ?? '';
  return out;
}

function openEditor(item = null) {
  editor.editing = item;
  editor.open = true;
  activeTab.value = 'main';
  seoLocale.value = 'ru';
  errors.value = {};
  heroFile.value = null; ogFile.value = null;
  galleryFiles.value = []; galleryPreviews.value = [];
  heroLocalPreview.value = null; ogLocalPreview.value = null;

  form.name          = item?.name ?? '';
  form.slug          = item?.slug ?? '';
  form.accent        = item?.accent ?? '#C9A96E';
  form.cta_type      = item?.cta_type ?? 'apts';
  form.is_featured   = item?.is_featured ?? true;
  form.is_visible    = item?.is_visible ?? true;
  form.feature_icons = [0, 1, 2, 3].map(i => item?.feature_icons?.[i] ?? 'building');
  form.content = {
    ru: normalizeLocale(item?.content?.ru),
    tj: normalizeLocale(item?.content?.tj),
    en: normalizeLocale(item?.content?.en),
  };
  form.seo = normalizeSeo(item?.seo);
  galleryKeep.value = [...(item?.gallery ?? [])];
}

function closeEditor() { editor.open = false; editor.editing = null; }

function onFile(e, kind) {
  const files = Array.from(e.target.files || []);
  if (!files.length) return;
  if (kind === 'hero') { heroFile.value = files[0]; heroLocalPreview.value = URL.createObjectURL(files[0]); }
  if (kind === 'og')   { ogFile.value = files[0]; ogLocalPreview.value = URL.createObjectURL(files[0]); }
  if (kind === 'gallery') {
    for (const f of files) {
      galleryFiles.value.push(f);
      galleryPreviews.value.push(URL.createObjectURL(f));
    }
  }
  e.target.value = '';
}

function removeNewGallery(i) {
  galleryFiles.value.splice(i, 1);
  galleryPreviews.value.splice(i, 1);
}

function submit() {
  processing.value = true;
  errors.value = {};

  const payload = {
    name: form.name, slug: form.slug, accent: form.accent, cta_type: form.cta_type,
    is_featured: form.is_featured, is_visible: form.is_visible,
    feature_icons: form.feature_icons,
    content: form.content,
    seo: form.seo,
    gallery_sync: '1',
    gallery_keep: galleryKeep.value,
    hero_file: heroFile.value,
    og_file: ogFile.value,
    gallery_files: galleryFiles.value,
  };

  const opts = {
    preserveScroll: true,
    forceFormData: true,
    onSuccess: closeEditor,
    onError: e => { errors.value = e; },
    onFinish: () => { processing.value = false; },
  };

  if (editor.editing) {
    router.post(route('admin.showcase.update', editor.editing.id), { _method: 'PUT', ...payload }, opts);
  } else {
    router.post(route('admin.showcase.store'), payload, opts);
  }
}

// ── Быстрые действия ──

function togglePublish(p) {
  router.patch(route('admin.showcase.publish', p.id), {}, { preserveScroll: true });
}

function toggleArchive(p) {
  router.patch(route('admin.showcase.archive', p.id), {}, { preserveScroll: true });
}

function duplicateItem(p) {
  router.post(route('admin.showcase.duplicate', p.id), {}, { preserveScroll: true });
}

function deleteItem(p) {
  if (!confirm(`Удалить проект «${p.name}» безвозвратно? Для временного скрытия используйте архив.`)) return;
  router.delete(route('admin.showcase.destroy', p.id), { preserveScroll: true });
}

function move(i, dir) {
  const j = i + dir;
  if (j < 0 || j >= props.projects.length) return;
  const ids = props.projects.map(p => p.id);
  [ids[i], ids[j]] = [ids[j], ids[i]];
  router.post(route('admin.showcase.reorder'), { ids }, { preserveScroll: true });
}

// ── Отображение ──

function statusLabel(s) {
  return { draft: 'Черновик', published: 'Опубликован', archived: 'Архив' }[s] ?? s;
}

function statusBadge(s) {
  return {
    draft:     'text-slate-300 bg-slate-600/30',
    published: 'text-emerald-400 bg-emerald-400/10',
    archived:  'text-amber-400 bg-amber-400/10',
  }[s] ?? '';
}
</script>

<style scoped>
.field {
  background: #0F172A;
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 0.75rem;
  padding: 0.55rem 0.9rem;
  font-size: 0.875rem;
  color: #E2E8F0;
  outline: none;
  transition: border-color 0.15s;
}
.field:focus { border-color: rgba(201,169,110,0.5); }
.lbl { display: block; font-size: 0.72rem; color: #94A3B8; margin-bottom: 0.35rem; }
.hint { font-size: 0.68rem; color: #64748B; margin-top: 0.25rem; }
.err { font-size: 0.72rem; color: #F87171; margin-top: 0.25rem; }
</style>
