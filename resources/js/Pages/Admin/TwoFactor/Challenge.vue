<template>
  <div class="min-h-screen flex items-center justify-center" style="background:#0F172A">
    <Head title="Двухфакторная аутентификация" />

    <div class="w-full max-w-sm mx-4">

      <!-- Logo -->
      <div class="text-center mb-8">
        <div class="inline-flex w-16 h-16 rounded-2xl bg-gold items-center justify-center font-bold text-[#0F172A] text-2xl mb-4">
          Q
        </div>
        <h1 class="text-white font-bold text-xl">Подтверждение входа</h1>
        <p class="text-slate-400 text-sm mt-1">Введите код из приложения-аутентификатора</p>
      </div>

      <!-- Card -->
      <div class="rounded-2xl p-8 border border-white/6" style="background:#1E293B">

        <!-- Icon -->
        <div class="flex justify-center mb-6">
          <div class="w-14 h-14 rounded-full bg-gold/15 flex items-center justify-center">
            <svg class="w-7 h-7 text-gold" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 8.25h3m-3 3.75h3m-3 3.75h3"/>
            </svg>
          </div>
        </div>

        <form @submit.prevent="submit">
          <!-- Code input -->
          <div class="mb-5">
            <label class="block text-slate-400 text-xs font-medium tracking-wider uppercase mb-2">
              Код аутентификатора
            </label>
            <input
              v-model="form.code"
              type="text"
              inputmode="numeric"
              maxlength="6"
              placeholder="000 000"
              class="w-full text-center text-2xl font-mono tracking-[0.5em] bg-white/5 border rounded-xl px-4 py-4 text-white outline-none transition-colors"
              :class="form.errors.code ? 'border-red-500' : 'border-white/10 focus:border-gold'"
              autofocus
              @input="form.code = form.code.replace(/\D/g, '')"
            />
            <p v-if="form.errors.code" class="text-red-400 text-xs mt-2 text-center">
              {{ form.errors.code }}
            </p>
          </div>

          <!-- Submit -->
          <button
            type="submit"
            class="w-full bg-gold hover:bg-[#D6BA7A] text-dark font-bold py-3.5 rounded-xl transition-colors"
            :disabled="form.processing || form.code.length < 6"
          >
            <span v-if="form.processing">Проверка...</span>
            <span v-else>Войти →</span>
          </button>

          <!-- Recovery code hint -->
          <p class="text-center text-slate-500 text-xs mt-4">
            Нет доступа к телефону?
            <button type="button" @click="useRecovery = !useRecovery"
              class="text-gold hover:text-white transition-colors ml-1">
              Использовать код восстановления
            </button>
          </p>

          <!-- Recovery code input -->
          <div v-if="useRecovery" class="mt-4">
            <input
              v-model="form.code"
              type="text"
              placeholder="XXXX-XXXX"
              class="w-full text-center font-mono tracking-widest bg-white/5 border border-white/10 focus:border-gold rounded-xl px-4 py-3 text-white outline-none transition-colors text-sm"
            />
            <p class="text-slate-500 text-xs text-center mt-2">
              Введите один из кодов восстановления (будет использован однократно)
            </p>
          </div>
        </form>
      </div>

      <!-- Back to login -->
      <div class="text-center mt-6">
        <Link :href="route('admin.login')" class="text-slate-500 hover:text-slate-300 text-sm transition-colors">
          ← Вернуться к входу
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const useRecovery = ref(false);
const form = useForm({ code: '' });

function submit() {
  form.post(route('admin.2fa.verify'));
}
</script>
