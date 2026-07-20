<template>
  <AdminLayout page-title="Двухфакторная аутентификация" page-subtitle="Безопасность аккаунта">
    <Head title="Двухфакторная аутентификация" />

    <div class="max-w-2xl mx-auto space-y-6">

      <!-- Status card -->
      <div class="rounded-xl p-5 border flex items-center gap-4"
        :style="enabled
          ? 'background:#0f2a1f; border-color:rgba(16,185,129,0.3)'
          : 'background:#1E293B; border-color:rgba(255,255,255,0.06)'">
        <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0"
          :style="enabled ? 'background:rgba(16,185,129,0.2)' : 'background:rgba(255,255,255,0.06)'">
          <svg class="w-6 h-6" :class="enabled ? 'text-emerald-400' : 'text-slate-500'"
            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/>
          </svg>
        </div>
        <div>
          <h3 class="text-white font-semibold text-sm">
            2FA {{ enabled ? 'включена' : 'выключена' }}
          </h3>
          <p class="text-slate-400 text-xs mt-0.5">
            {{ enabled
              ? 'Ваш аккаунт защищён двухфакторной аутентификацией'
              : 'Рекомендуем включить для защиты аккаунта администратора'
            }}
          </p>
        </div>
        <div class="ml-auto">
          <span class="text-xs font-bold px-3 py-1.5 rounded-full"
            :class="enabled
              ? 'bg-emerald-500/15 text-emerald-400'
              : 'bg-slate-500/15 text-slate-400'">
            {{ enabled ? '✓ АКТИВНА' : '✕ НЕАКТИВНА' }}
          </span>
        </div>
      </div>

      <!-- QR code setup (если не включена) -->
      <div v-if="!enabled" class="rounded-xl p-6 border border-white/6" style="background:#1E293B">
        <h3 class="text-white font-semibold mb-1">Настройка аутентификатора</h3>
        <p class="text-slate-400 text-xs mb-6">
          Установите Google Authenticator или Authy на телефон и отсканируйте QR-код.
        </p>

        <!-- Steps -->
        <div class="space-y-6 mb-6">
          <!-- Step 1: QR -->
          <div class="flex gap-4">
            <div class="step-num">1</div>
            <div class="flex-1">
              <p class="text-white text-sm font-medium mb-3">Отсканируйте QR-код</p>
              <div class="inline-block p-3 rounded-xl" style="background:#fff">
                <img
                  :src="`https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${encodeURIComponent(qr_url)}`"
                  alt="2FA QR Code"
                  class="w-44 h-44"
                />
              </div>
              <!-- Manual key -->
              <div class="mt-3">
                <p class="text-slate-500 text-xs mb-1">Или введите ключ вручную:</p>
                <div class="flex items-center gap-2">
                  <code class="bg-black/30 px-3 py-1.5 rounded text-gold text-xs font-mono tracking-wider">
                    {{ secret }}
                  </code>
                  <button @click="copySecret" class="text-slate-500 hover:text-white transition-colors" title="Копировать">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184"/>
                    </svg>
                  </button>
                  <span v-if="copied" class="text-emerald-400 text-xs">Скопировано!</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 2: Verify -->
          <div class="flex gap-4">
            <div class="step-num">2</div>
            <div class="flex-1">
              <p class="text-white text-sm font-medium mb-3">Введите 6-значный код из приложения</p>
              <form @submit.prevent="enableTwoFactor">
                <div class="flex gap-3">
                  <input
                    v-model="code"
                    type="text"
                    inputmode="numeric"
                    maxlength="6"
                    placeholder="000000"
                    class="code-input"
                    :class="form.errors.code ? 'border-red-500' : ''"
                    autofocus
                  />
                  <button type="submit" class="btn-gold" :disabled="form.processing || code.length < 6">
                    <span v-if="form.processing">...</span>
                    <span v-else>Включить 2FA</span>
                  </button>
                </div>
                <p v-if="form.errors.code" class="text-red-400 text-xs mt-2">{{ form.errors.code }}</p>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Recovery codes (если включена) -->
      <div v-if="enabled && codes?.length" class="rounded-xl p-6 border border-amber-500/20" style="background:#1E293B">
        <div class="flex items-start gap-3 mb-4">
          <div class="w-8 h-8 rounded-lg bg-amber-500/15 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-white font-semibold text-sm">Коды восстановления</h3>
            <p class="text-slate-400 text-xs mt-0.5">Сохраните эти коды в надёжном месте. Каждый код можно использовать только один раз.</p>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-2 mb-4">
          <code v-for="c in codes" :key="c"
            class="block bg-black/30 text-gold text-xs font-mono px-3 py-2 rounded text-center tracking-widest">
            {{ c }}
          </code>
        </div>
        <button @click="copyCodes" class="text-xs text-slate-400 hover:text-white transition-colors flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184"/>
          </svg>
          {{ codesCopied ? 'Скопировано!' : 'Копировать все коды' }}
        </button>
      </div>

      <!-- Disable 2FA -->
      <div v-if="enabled" class="rounded-xl p-6 border border-red-500/15" style="background:#1E293B">
        <h3 class="text-white font-semibold text-sm mb-1">Отключить 2FA</h3>
        <p class="text-slate-400 text-xs mb-4">Отключение снизит безопасность вашего аккаунта.</p>
        <form @submit.prevent="disableTwoFactor" class="flex gap-3">
          <input
            v-model="password"
            type="password"
            placeholder="Введите пароль для подтверждения"
            class="code-input flex-1"
            :class="disableForm.errors.password ? 'border-red-500' : ''"
          />
          <button type="submit" class="btn-danger" :disabled="disableForm.processing">
            Отключить
          </button>
        </form>
        <p v-if="disableForm.errors.password" class="text-red-400 text-xs mt-2">{{ disableForm.errors.password }}</p>
      </div>

    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  qr_url:  String,
  secret:  String,
  enabled: Boolean,
  codes:   Array,
});

const code        = ref('');
const password    = ref('');
const copied      = ref(false);
const codesCopied = ref(false);

const form        = useForm({ code: '' });
const disableForm = useForm({ password: '' });

function enableTwoFactor() {
  form.code = code.value;
  form.post(route('admin.2fa.enable'));
}

function disableTwoFactor() {
  disableForm.password = password.value;
  disableForm.post(route('admin.2fa.disable'));
}

function copySecret() {
  navigator.clipboard.writeText(props.secret);
  copied.value = true;
  setTimeout(() => copied.value = false, 2000);
}

function copyCodes() {
  navigator.clipboard.writeText((props.codes ?? []).join('\n'));
  codesCopied.value = true;
  setTimeout(() => codesCopied.value = false, 2000);
}
</script>

<style scoped>
.step-num {
  width: 2rem; height: 2rem; border-radius: 50%;
  background: rgba(201,169,110,0.15); color: #C9A96E;
  font-weight: 700; font-size: 0.8125rem;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.code-input {
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 0.5rem;
  padding: 0.625rem 0.875rem;
  color: #fff;
  font-size: 1.125rem;
  font-family: monospace;
  letter-spacing: 0.25em;
  outline: none;
  transition: border-color 0.2s;
  width: 10rem;
}
.code-input:focus { border-color: #C9A96E; }
.btn-gold {
  background: #C9A96E; color: #0F172A;
  font-weight: 700; font-size: 0.8125rem;
  padding: 0.625rem 1.25rem;
  border-radius: 0.5rem;
  border: none; cursor: pointer;
  transition: background 0.2s;
  white-space: nowrap;
}
.btn-gold:hover:not(:disabled) { background: #D6BA7A; }
.btn-gold:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-danger {
  background: rgba(239,68,68,0.15); color: #F87171;
  border: 1px solid rgba(239,68,68,0.3);
  font-weight: 600; font-size: 0.8125rem;
  padding: 0.625rem 1.25rem;
  border-radius: 0.5rem;
  cursor: pointer; transition: all 0.2s;
  white-space: nowrap;
}
.btn-danger:hover:not(:disabled) { background: rgba(239,68,68,0.25); }
</style>
