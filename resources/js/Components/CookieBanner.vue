<template>
  <Transition
    enter-active-class="transition-all duration-500 ease-out"
    enter-from-class="translate-y-4 opacity-0"
    enter-to-class="translate-y-0 opacity-100"
    leave-active-class="transition-all duration-300 ease-in"
    leave-from-class="translate-y-0 opacity-100"
    leave-to-class="translate-y-4 opacity-0"
  >
    <div
      v-if="visible"
      class="fixed bottom-0 left-0 right-0 z-[9999] p-4 sm:p-6"
      role="dialog"
      aria-live="polite"
      aria-label="Cookie consent"
    >
      <div class="max-w-4xl mx-auto rounded-2xl px-5 py-4 flex flex-col sm:flex-row items-start sm:items-center gap-4"
        style="background: rgba(10,14,26,0.97); border: 1px solid rgba(201,169,110,0.2); backdrop-filter: blur(20px); box-shadow: 0 -4px 40px rgba(0,0,0,0.5)">

        <!-- Cookie icon -->
        <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center"
          style="background: rgba(201,169,110,0.1)">
          <svg class="w-5 h-5" fill="none" stroke="#C9A96E" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v.008M12 12v.008M12 15.75v.008M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
          </svg>
        </div>

        <!-- Text -->
        <p class="text-gray-400 text-sm leading-relaxed flex-1">
          {{ t('cookie.text') }}
          <Link :href="route('privacy')" class="text-gold hover:underline">
            {{ t('cookie.policy') }}
          </Link>
        </p>

        <!-- Buttons (GDPR Compliant - equal prominence) -->
        <div class="flex items-center gap-3 flex-shrink-0 flex-wrap sm:flex-nowrap">
          <button
            @click="decline"
            class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 text-gray-400 hover:text-white"
            style="border: 1px solid rgba(255,255,255,0.2); background: transparent;"
          >
            {{ t('cookie.reject_all') }}
          </button>
          <button
            @click="openPreferences"
            class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 text-gray-400 hover:text-white"
            style="border: 1px solid rgba(255,255,255,0.2); background: transparent;"
          >
            {{ t('cookie.preferences') }}
          </button>
          <button
            @click="accept"
            class="px-5 py-2 rounded-xl text-sm font-semibold transition-all duration-200"
            style="background: linear-gradient(135deg, #C9A96E, #B8935A); color: #0A0800;"
          >
            {{ t('cookie.accept_all') }}
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useTrans } from '@/composables/useTrans'

const { t } = useTrans()

const visible = ref(false)
const STORAGE_KEY = 'qudrat_cookie_consent'

onMounted(() => {
  // Show banner only if user hasn't decided yet
  if (!localStorage.getItem(STORAGE_KEY)) {
    // Small delay so it doesn't flash immediately on page load
    setTimeout(() => { visible.value = true }, 800)
  }
})

function accept() {
  localStorage.setItem(STORAGE_KEY, 'accepted')
  localStorage.setItem('qudrat_analytics', 'true')
  localStorage.setItem('qudrat_marketing', 'true')
  visible.value = false
}

function decline() {
  localStorage.setItem(STORAGE_KEY, 'declined')
  localStorage.setItem('qudrat_analytics', 'false')
  localStorage.setItem('qudrat_marketing', 'false')
  visible.value = false
}

function openPreferences() {
  // TODO: Implement cookie preferences modal
  // For now, treat as "decline all non-essential"
  localStorage.setItem(STORAGE_KEY, 'preferences-set')
  localStorage.setItem('qudrat_analytics', 'false')
  localStorage.setItem('qudrat_marketing', 'false')
  visible.value = false
}
</script>
