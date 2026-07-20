<template>
  <MainLayout>
    <SeoHead
      :title="t('contact.meta_title')"
      :ogTitle="t('contact.meta_title')"
      :description="t('contact.meta_desc')"
      image="/images/og-contacts.jpg"
      path="/contacts"
    />

    <!-- ═══ HERO ═══ -->
    <section class="contacts-hero relative overflow-hidden flex items-end">

      <!-- Background photo -->
      <div class="absolute inset-0">
        <img :src="asset('/images/hero-9.jpg')" alt="" class="w-full h-full object-cover" style="opacity:0.28"/>
        <div class="absolute inset-0" style="background:linear-gradient(to right,rgba(7,11,22,0.95) 0%,rgba(7,11,22,0.75) 55%,rgba(7,11,22,0.50) 100%)"></div>
        <div class="absolute inset-0" style="background:linear-gradient(to top,rgba(7,11,22,0.85) 0%,transparent 45%)"></div>
      </div>

      <!-- Content -->
      <div class="relative z-10 max-w-[1400px] mx-auto px-6 lg:px-12 pb-20 pt-10 w-full">
        <div class="inline-flex items-center gap-3 mb-8">
          <div class="h-px w-10" style="background:linear-gradient(to right,transparent,rgba(201,169,110,0.6))"></div>
          <span class="text-gold/70 text-[9px] font-bold uppercase tracking-[0.55em]">{{ t('nav.contacts') }}</span>
          <div class="h-px w-10" style="background:linear-gradient(to left,transparent,rgba(201,169,110,0.6))"></div>
        </div>

        <h1 class="font-black text-white uppercase leading-none mb-6"
          style="font-size:clamp(44px,7vw,100px); letter-spacing:-0.02em">
          {{ t('contact.title') }}
        </h1>
        <p class="text-white/55 text-base lg:text-lg leading-relaxed max-w-xl">
          {{ t('contact.page_subtitle') }}
        </p>
      </div>

      <!-- Bottom fade -->
      <div class="absolute bottom-0 inset-x-0 h-16 pointer-events-none"
        style="background:linear-gradient(to top,#070B16,transparent)"></div>
    </section>

    <!-- Main -->
    <section class="pb-24 container mx-auto px-6">
      <div class="grid lg:grid-cols-5 gap-10">

        <!-- Contact info -->
        <div class="lg:col-span-2 space-y-6">
          <div v-for="info in contactInfo" :key="info.label"
            class="group bg-[#0D1220] rounded-2xl p-6 border border-white/5 hover:border-gold/20 transition-all duration-300">
            <div class="flex items-start gap-4">
              <div class="w-10 h-10 rounded-xl bg-gold/10 flex items-center justify-center flex-shrink-0 group-hover:bg-gold/20 transition-colors">
                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" :d="info.iconPath"/>
                </svg>
              </div>
              <div>
                <div class="text-gray-500 text-xs mb-1 uppercase tracking-wider">{{ info.label }}</div>
                <a v-if="info.href" :href="info.href" class="text-white font-medium hover:text-gold transition-colors">{{ info.value }}</a>
                <div v-else class="text-white font-medium">{{ info.value }}</div>
                <div v-if="info.sub" class="text-gray-500 text-sm mt-0.5">{{ info.sub }}</div>
              </div>
            </div>
          </div>

          <!-- Social / Map preview -->
          <div class="bg-[#0D1220] rounded-2xl overflow-hidden border border-white/5">
            <div class="relative h-48 bg-[#0A0E1A] flex items-center justify-center">
              <!-- Simple map placeholder -->
              <div class="text-center">
                <Icon name="map-pin" class="w-10 h-10 text-gold/60 mx-auto mb-2" :stroke-width="1.5" />
                <div class="text-gray-400 text-sm">{{ t('contact.map_city') }}</div>
              </div>
              <div class="absolute inset-0 bg-gradient-to-t from-[#0D1220] to-transparent opacity-40"></div>
            </div>
            <div class="p-4">
              <p class="text-gray-500 text-xs text-center">{{ t('contact.map_note') }}</p>
            </div>
          </div>

          <!-- Working hours -->
          <div class="bg-[#0D1220] rounded-2xl p-6 border border-white/5">
            <h4 class="text-white font-semibold mb-4 flex items-center gap-2">
              <Icon name="clock" class="w-4 h-4 text-gold" :stroke-width="1.5" />
              {{ t('contact.hours_title') }}
            </h4>
            <div class="space-y-2">
              <div v-for="h in hours" :key="h.days" class="flex justify-between text-sm">
                <span class="text-gray-400">{{ h.days }}</span>
                <span :class="h.closed ? 'text-red-400' : 'text-white'">{{ h.time }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Form -->
        <div class="lg:col-span-3">
          <div class="bg-[#0D1220] rounded-3xl p-8 md:p-10 border border-white/5">
            <h2 class="text-2xl font-light text-white mb-2">{{ t('contact.form_title') }}</h2>
            <p class="text-gray-400 text-sm mb-8">{{ t('contact.form_subtitle') }}</p>

            <!-- Success message -->
            <div v-if="submitted" class="bg-green-500/10 border border-green-500/30 rounded-2xl p-6 text-center mb-6">
              <div class="text-4xl mb-3">✓</div>
              <div class="text-green-400 font-semibold mb-1">{{ t('contact.success_title') }}</div>
              <div class="text-gray-400 text-sm">{{ t('contact.success_text') }}</div>
            </div>

            <form v-if="!submitted" @submit.prevent="submitForm" class="space-y-5">
              <div class="grid sm:grid-cols-2 gap-5">
                <div>
                  <label for="contact-name" class="block text-gray-400 text-sm mb-2">{{ t('contact.name') }} *</label>
                  <input id="contact-name" v-model="form.name" type="text" required :placeholder="t('contact.name_placeholder')"
                    autocomplete="name"
                    :class="['w-full bg-[#0A0E1A] border rounded-xl px-4 py-3.5 text-white placeholder-gray-600 outline-none transition-colors focus-ring', errors.name ? 'border-red-500/50 focus:border-red-500' : 'border-white/10 focus:border-gold/50']">
                  <p v-if="errors.name" role="alert" class="text-red-400 text-xs mt-1">{{ errors.name }}</p>
                </div>
                <div>
                  <label for="contact-phone" class="block text-gray-400 text-sm mb-2">{{ t('contact.phone') }} *</label>
                  <input id="contact-phone" v-model="form.phone" type="tel" required :placeholder="t('contact.phone_placeholder')"
                    autocomplete="tel"
                    :class="['w-full bg-[#0A0E1A] border rounded-xl px-4 py-3.5 text-white placeholder-gray-600 outline-none transition-colors focus-ring', errors.phone ? 'border-red-500/50 focus:border-red-500' : 'border-white/10 focus:border-gold/50']">
                  <p v-if="errors.phone" role="alert" class="text-red-400 text-xs mt-1">{{ errors.phone }}</p>
                </div>
              </div>

              <div>
                <label for="contact-email" class="block text-gray-400 text-sm mb-2">{{ t('contact.email') }}</label>
                <input id="contact-email" v-model="form.email" type="email" placeholder="your@email.com"
                  autocomplete="email"
                  :class="['w-full bg-[#0A0E1A] border rounded-xl px-4 py-3.5 text-white placeholder-gray-600 outline-none transition-colors focus-ring', errors.email ? 'border-red-500/50 focus:border-red-500' : 'border-white/10 focus:border-gold/50']">
                <p v-if="errors.email" role="alert" class="text-red-400 text-xs mt-1">{{ errors.email }}</p>
              </div>

              <fieldset>
                <legend class="block text-gray-400 text-sm mb-2">{{ t('contact.topic_label') }}</legend>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2" role="group" :aria-label="t('contact.topic_label')">
                  <button v-for="topic in topics" :key="topic.key" type="button"
                    @click="form.topic = topic.key"
                    :aria-pressed="form.topic === topic.key"
                    :class="['px-3 py-2.5 rounded-xl text-xs font-medium border transition-all focus-ring', form.topic === topic.key
                      ? 'bg-gold/20 border-gold/50 text-gold'
                      : 'bg-transparent border-white/10 text-gray-400 hover:border-white/20 hover:text-white']">
                    {{ topic.label }}
                  </button>
                </div>
              </fieldset>

              <div>
                <label for="contact-message" class="block text-gray-400 text-sm mb-2">{{ t('contact.message') }}</label>
                <textarea id="contact-message" v-model="form.message" rows="4" :placeholder="t('contact.msg_placeholder')"
                  class="w-full bg-[#0A0E1A] border border-white/10 focus:border-gold/50 rounded-xl px-4 py-3.5 text-white placeholder-gray-600 outline-none transition-colors resize-none focus-ring"></textarea>
              </div>

              <div class="flex items-start gap-3">
                <input v-model="form.agree" type="checkbox" id="agree" class="mt-0.5 accent-gold" required>
                <label for="agree" class="text-gray-400 text-sm leading-relaxed cursor-pointer">
                  {{ t('contact.agree_text') }}
                </label>
              </div>

              <button type="submit" :disabled="loading"
                class="w-full bg-gold hover:bg-gold-600 disabled:opacity-50 text-[#0A0E1A] font-semibold px-8 py-4 rounded-xl transition-colors flex items-center justify-center gap-3">
                <span v-if="loading" class="w-5 h-5 border-2 border-[#0A0E1A]/30 border-t-[#0A0E1A] rounded-full animate-spin"></span>
                {{ loading ? t('contact.sending') : t('contact.send_btn') }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>

  </MainLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'
import Icon from '@/Components/Icon.vue';
import { useTrans } from '@/composables/useTrans'
import { useAsset } from '@/composables/useAsset.js'

const { t } = useTrans()
const { asset } = useAsset()

// SVG icon paths (Heroicons 2.0 outline)
const ICON_PHONE    = 'M2.25 6.338c0-.48.21-.94.58-1.25l1.88-1.58a1.126 1.126 0 0 1 1.56.08l2.24 2.81c.37.47.32 1.15-.12 1.56l-.83.76a.126.126 0 0 0-.04.13 10.61 10.61 0 0 0 5.14 5.14c.05.02.1.01.13-.04l.76-.83a1.125 1.125 0 0 1 1.56-.12l2.81 2.24c.47.37.53 1.06.08 1.56l-1.58 1.88c-.31.37-.77.58-1.25.58C9.1 19.5 4.5 14.9 4.5 6.338Z'
const ICON_MAIL     = 'M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75'
const ICON_LOCATION = 'M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z'

const submitted = ref(false)
const loading = ref(false)
const errors = reactive({})

const form = reactive({
  name: '',
  phone: '',
  email: '',
  topic: 'apt',
  message: '',
  agree: false,
})

const topics = computed(() => [
  { key: 'apt',     label: t('contact.topic_apt') },
  { key: 'invest',  label: t('contact.topic_invest') },
  { key: 'partner', label: t('contact.topic_partner') },
  { key: 'other',   label: t('contact.topic_other') },
])

const contactInfo = computed(() => [
  { iconPath: ICON_PHONE,    label: t('contact.phone'),    value: '+992 000 000 000', href: 'tel:+992000000000', sub: '' },
  { iconPath: ICON_MAIL,     label: t('contact.email'),    value: 'info@qudrat.tj',   href: 'mailto:info@qudrat.tj', sub: '' },
  { iconPath: ICON_LOCATION, label: t('footer.contacts'), value: t('footer.address'), href: null, sub: '' },
])

const hours = computed(() => [
  { days: t('contact.h1_days'), time: t('contact.h1_time'),   closed: false },
  { days: t('contact.h2_days'), time: t('contact.h2_time'),   closed: false },
  { days: t('contact.h3_days'), time: t('contact.h3_closed'), closed: true },
])

function validate() {
  Object.keys(errors).forEach(k => delete errors[k])
  if (!form.name.trim()) errors.name = t('contact.required_name')
  if (!form.phone.trim()) errors.phone = t('contact.required_phone')
  if (form.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) errors.email = t('contact.invalid_email')
  return Object.keys(errors).length === 0
}

function submitForm() {
  if (!validate()) return
  loading.value = true
  router.post('/contact', { ...form }, {
    onSuccess: () => {
      submitted.value = true
      loading.value = false
    },
    onError: (errs) => {
      Object.assign(errors, errs)
      loading.value = false
    },
    onFinish: () => {
      loading.value = false
    },
  })
}
</script>

<style scoped>
.contacts-hero {
  height: 100vh;
  min-height: 640px;
  background: #070B16;
  padding-top: 90px;
}
</style>
