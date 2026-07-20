<template>
  <div class="min-h-screen flex items-center justify-center p-4" style="background: linear-gradient(135deg, #080B12 0%, #0F1420 50%, #080B12 100%)">

    <!-- Ambient glow -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute top-1/4 left-1/4 w-96 h-96 rounded-full opacity-5"
        style="background: radial-gradient(circle, #C9A96E, transparent); filter: blur(80px)"></div>
      <div class="absolute bottom-1/3 right-1/4 w-64 h-64 rounded-full opacity-4"
        style="background: radial-gradient(circle, #C9A96E, transparent); filter: blur(60px)"></div>
    </div>

    <div class="relative w-full max-w-md">

      <!-- Card -->
      <div class="rounded-3xl p-8 shadow-2xl" style="background: rgba(15,20,32,0.95); border: 1px solid rgba(201,169,110,0.12); backdrop-filter: blur(20px)">

        <!-- Logo -->
        <div class="text-center mb-8">
          <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4"
            style="background: linear-gradient(135deg, #C9A96E, #B8935A); box-shadow: 0 8px 30px rgba(201,169,110,0.25)">
            <span class="text-[#0A0800] font-bold text-3xl" >Q</span>
          </div>
          <h1 class="text-white font-bold text-2xl mb-1" >Личный кабинет</h1>
          <p class="text-gray-400 text-sm">QUDRAT — Вход в аккаунт</p>
        </div>

        <!-- Error -->
        <div v-if="form.errors.email" class="mb-5 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm flex items-center gap-2">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
          </svg>
          {{ form.errors.email }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">

          <!-- Email -->
          <div>
            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Email</label>
            <input
              v-model="form.email"
              type="email"
              placeholder="your@email.com"
              autocomplete="email"
              class="w-full px-4 py-3.5 rounded-xl text-white placeholder-gray-600 text-sm outline-none transition-all duration-200"
              style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);"
              :style="form.errors.email ? 'border-color: rgba(239,68,68,0.4)' : ''"
              @focus="e => e.target.style.borderColor = 'rgba(201,169,110,0.4)'"
              @blur="e => e.target.style.borderColor = form.errors.email ? 'rgba(239,68,68,0.4)' : 'rgba(255,255,255,0.08)'"
            />
          </div>

          <!-- Password -->
          <div>
            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Пароль</label>
            <div class="relative">
              <input
                v-model="form.password"
                :type="showPass ? 'text' : 'password'"
                placeholder="••••••••"
                autocomplete="current-password"
                class="w-full px-4 py-3.5 pr-12 rounded-xl text-white placeholder-gray-600 text-sm outline-none transition-all duration-200"
                style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);"
                @focus="e => e.target.style.borderColor = 'rgba(201,169,110,0.4)'"
                @blur="e => e.target.style.borderColor = 'rgba(255,255,255,0.08)'"
              />
              <button type="button" @click="showPass = !showPass"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300 transition-colors">
                <svg v-if="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Remember -->
          <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer select-none">
              <input v-model="form.remember" type="checkbox"
                class="w-4 h-4 rounded accent-gold"/>
              <span class="text-sm text-gray-400">Запомнить меня</span>
            </label>
            <span class="text-xs text-gray-600 cursor-default">Забыли пароль?<br>
              <a href="tel:+992000000000" class="text-gold/60 hover:text-gold transition-colors">Свяжитесь с нами</a>
            </span>
          </div>

          <!-- Submit -->
          <button
            type="submit"
            :disabled="form.processing"
            class="w-full py-4 rounded-xl font-bold text-[#0A0800] text-sm uppercase tracking-widest transition-all duration-300 disabled:opacity-60 disabled:cursor-not-allowed"
            style="background: linear-gradient(135deg, #C9A96E, #D6BA7A); box-shadow: 0 8px 25px rgba(201,169,110,0.2)"
          >
            <span v-if="!form.processing">Войти в кабинет</span>
            <span v-else class="flex items-center justify-center gap-2">
              <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              Входим...
            </span>
          </button>
        </form>

        <!-- Divider -->
        <div class="my-6 flex items-center gap-4">
          <div class="flex-1 h-px" style="background: rgba(255,255,255,0.06)"></div>
          <span class="text-gray-600 text-xs">или</span>
          <div class="flex-1 h-px" style="background: rgba(255,255,255,0.06)"></div>
        </div>

        <!-- Register link -->
        <Link :href="route('register')"
          class="block w-full py-3.5 rounded-xl text-center text-sm font-semibold transition-all duration-200 text-gold hover:text-white"
          style="border: 1px solid rgba(201,169,110,0.25); background: rgba(201,169,110,0.04);"
        >
          Создать новый аккаунт
        </Link>

        <!-- Footer -->
        <div class="mt-6 pt-5 border-t text-center" style="border-color: rgba(255,255,255,0.05)">
          <Link :href="route('home')" class="text-gray-600 text-xs hover:text-gray-400 transition-colors flex items-center justify-center gap-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
            На главную
          </Link>
        </div>
      </div>

      <!-- Admin link hint -->
      <p class="text-center text-gray-700 text-xs mt-4">
        Сотрудник QUDRAT?
        <Link :href="route('admin.login')" class="text-gray-500 hover:text-gray-400 transition-colors ml-1">Войти в CRM →</Link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm, Head } from '@inertiajs/vue3'

const showPass = ref(false)

const form = useForm({
  email:    '',
  password: '',
  remember: false,
})

function submit() {
  form.post(route('client.login.post'), {
    onFinish: () => form.reset('password'),
  })
}
</script>
