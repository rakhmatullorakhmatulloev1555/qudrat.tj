<template>
  <div class="min-h-screen flex items-center justify-center p-4" style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%)">
    <Head title="Вход в систему" />

    <!-- Background pattern -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute top-1/4 left-1/4 w-96 h-96 rounded-full opacity-5"
        style="background: radial-gradient(circle, #C9A96E, transparent); filter: blur(60px)"></div>
      <div class="absolute bottom-1/4 right-1/4 w-64 h-64 rounded-full opacity-5"
        style="background: radial-gradient(circle, #C9A96E, transparent); filter: blur(40px)"></div>
    </div>

    <div class="relative w-full max-w-md">

      <!-- Card -->
      <div class="rounded-2xl p-8 shadow-2xl" style="background:#1E293B; border:1px solid rgba(255,255,255,0.08)">

        <!-- Logo -->
        <div class="text-center mb-8">
          <div class="w-16 h-16 rounded-2xl bg-gold flex items-center justify-center mx-auto mb-4 shadow-[0_8px_30px_rgba(201,169,110,0.3)]">
            <span class="text-[#0F172A] font-bold text-3xl" >Q</span>
          </div>
          <h1 class="text-white font-bold text-2xl mb-1" >QUDRAT CRM</h1>
          <p class="text-slate-400 text-sm">Панель управления</p>
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
            <label class="block text-sm font-medium text-slate-300 mb-2">Email</label>
            <input
              v-model="form.email"
              type="email"
              autocomplete="email"
              class="w-full px-4 py-3 rounded-xl text-white placeholder-slate-500 text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-gold/50"
              style="background:#0F172A; border:1px solid rgba(255,255,255,0.1);"
              :class="form.errors.email ? 'border-red-500/50' : 'focus:border-gold/50'"
            />
          </div>

          <!-- Password -->
          <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">Пароль</label>
            <div class="relative">
              <input
                v-model="form.password"
                :type="showPass ? 'text' : 'password'"
                placeholder="••••••••"
                autocomplete="current-password"
                class="w-full px-4 py-3 pr-12 rounded-xl text-white placeholder-slate-500 text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-gold/50"
                style="background:#0F172A; border:1px solid rgba(255,255,255,0.1);"
              />
              <button type="button" @click="showPass = !showPass"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors">
                <svg v-if="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Remember -->
          <div class="flex items-center gap-2">
            <input id="remember" v-model="form.remember" type="checkbox"
              class="w-4 h-4 rounded border-slate-600 text-gold focus:ring-gold"/>
            <label for="remember" class="text-sm text-slate-400 cursor-pointer select-none">Запомнить меня</label>
          </div>

          <!-- Submit -->
          <button
            type="submit"
            :disabled="form.processing"
            class="w-full py-3.5 rounded-xl font-bold text-[#0F172A] text-sm uppercase tracking-widest transition-all duration-300 disabled:opacity-60"
            style="background: #C9A96E; box-shadow: 0 8px 25px rgba(201,169,110,0.25)"
            :style="form.processing ? '' : 'hover:background:#D6BA7A'"
          >
            <span v-if="!form.processing">Войти в систему</span>
            <span v-else class="flex items-center justify-center gap-2">
              <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              Вход...
            </span>
          </button>
        </form>

        <!-- Footer -->
        <div class="mt-6 pt-5 border-t border-white/6 text-center">
          <p class="text-slate-600 text-xs">© QUDRAT LLC CRM System</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';

const showPass = ref(false);

const form = useForm({
  email:    '',
  password: '',
  remember: false,
});

function submit() {
  form.post(route('admin.login.post'), {
    onFinish: () => form.reset('password'),
  });
}
</script>
