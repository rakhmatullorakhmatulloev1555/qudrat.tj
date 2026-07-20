<template>
  <Head>
    <!-- title без суффикса — Inertia's titleCallback добавляет "— QUDRAT" автоматически -->
    <title>{{ title }}</title>
    <meta name="description" :content="description" />
    <link rel="canonical" :href="canonicalUrl" />

    <!-- Open Graph — используем полный title с суффиксом -->
    <meta property="og:type"        content="website" />
    <meta property="og:site_name"   content="QUDRAT" />
    <meta property="og:title"       :content="ogFullTitle" />
    <meta property="og:description" :content="description" />
    <meta property="og:url"         :content="canonicalUrl" />
    <meta property="og:image"       :content="ogImage" />
    <meta property="og:locale"      :content="ogLocale" />

    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary_large_image" />
    <meta name="twitter:title"       :content="ogFullTitle" />
    <meta name="twitter:description" :content="description" />
    <meta name="twitter:image"       :content="ogImage" />

    <!-- Schema.org JSON-LD (опционально) -->
    <component v-if="schema" :is="'script'" type="application/ld+json" v-text="schemaJson" />
  </Head>
</template>

<script setup>
import { computed } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import { useAsset } from '@/composables/useAsset'

const props = defineProps({
  title:       { type: String, required: true },
  ogTitle:     { type: String, default: '' },
  description: { type: String, default: '' },
  image:       { type: String, default: '/images/og-default.jpg' },
  path:        { type: String, default: '' },
  canonical:   { type: String, default: '' },   // полный URL — переопределяет canonical
  schema:      { type: Object, default: null }, // JSON-LD объект (Schema.org)
})

const page   = usePage()
const { asset } = useAsset()

// ogTitle — полный SEO-заголовок для OG/Twitter (если передан, используется как есть).
// Если не передан — формируется из title + " — QUDRAT".
// title без суффикса передаётся в <title>: Inertia's titleCallback добавляет "— QUDRAT" автоматически.
const ogFullTitle  = computed(() => props.ogTitle || `${props.title} — QUDRAT`)
const ogImage      = computed(() => asset(props.image))
const canonicalUrl = computed(() => {
  if (props.canonical) return props.canonical
  const base = (page.props.assetBase ?? '').replace(/\/$/, '')
  return `${base}${props.path || page.url}`
})
const schemaJson = computed(() => props.schema ? JSON.stringify(props.schema) : '')
const ogLocale = computed(() => {
  const map = { ru: 'ru_RU', en: 'en_US', tj: 'tg_TJ' }
  return map[page.props.locale] ?? 'ru_RU'
})
</script>
