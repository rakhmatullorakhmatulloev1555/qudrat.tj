<template>
  <div class="min-h-screen flex items-center justify-center p-4" style="background: linear-gradient(135deg, #080B12 0%, #0F1420 50%, #080B12 100%)">

    <!-- Ambient glow -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute top-1/4 left-1/4 w-96 h-96 rounded-full opacity-5"
        style="background: radial-gradient(circle, #C9A96E, transparent); filter: blur(80px)"></div>
    </div>

    <div class="relative w-full max-w-md">
      <div class="rounded-3xl p-8 shadow-2xl" style="background: rgba(15,20,32,0.95); border: 1px solid rgba(201,169,110,0.12); backdrop-filter: blur(20px)">

        <!-- Logo -->
        <div class="text-center mb-8">
          <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4"
            style="background: linear-gradient(135deg, #C9A96E, #B8935A); box-shadow: 0 8px 30px rgba(201,169,110,0.25)">
            <span class="text-[#0A0800] font-bold text-3xl" >Q</span>
          </div>
          <h1 class="text-white font-bold text-2xl mb-1" >Восстановление пароля</h1>
          <p class="text-gray-400 text-sm">Введите email — мы пришлём ссылку для сброса</p>
        </div>

        <!-- Success -->
        <div v-if="status" class="mb-5 px-4 py-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
          {{ status }}
        </div>

        <!-- Error -->
        <div v-if="form.errors.email" class="mb-5 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm flex items-center gap-2">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
          </svg>
          {{ form.errors.email }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
          <div>
            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Email</label>
            <input
              v-model="form.email"
              type="email"
              placeholder="your@email.com"
              autocomplete="email"
              required
              class="w-full px-4 py-3.5 rounded-xl text-white placeholder-gray-600 text-sm outline-none transition-all duration-200"
              style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);"
              @focus="e => e.target.style.borderColor = 'rgba(201,169,110,0.4)'"
              @blur="e => e.target.style.borderColor = 'rgba(255,255,255,0.08)'"
            />
          </div>

          <button
            type="submit"
            :disabled="form.processing"
            class="w-full py-3.5 rounded-xl font-semibold text-sm transition-all duration-200 disabled:opacity-60"
            style="background: linear-gradient(135deg, #C9A96E, #B8935A); color: #0A0800;"
          >
            <span v-if="form.processing">Отправляем...</span>
            <span v-else>Отправить ссылку</span>
          </button>
        </form>

        <div class="mt-6 text-center">
          <Link :href="route('client.login')" class="text-sm text-gray-500 hover:text-gold transition-colors">
            Вернуться ко входу
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page   = usePage()
const status = computed(() => page.props.flash?.status ?? null)

const form = useForm({ email: '' })

function submit() {
  form.post(route('password.email'))
}
</script>
