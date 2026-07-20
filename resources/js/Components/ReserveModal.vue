<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="modelValue"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-y-auto"
        style="background:rgba(0,0,0,0.8); backdrop-filter:blur(6px)"
        @click.self="close">
        <div class="w-full max-w-lg rounded-2xl p-7 shadow-2xl my-auto"
          style="background:#0C1220; border:1px solid rgba(201,169,110,0.15)">

          <!-- Header -->
          <div class="flex items-start justify-between mb-6">
            <div>
              <div class="text-gold text-[10px] font-bold uppercase tracking-[0.3em] mb-1">{{ t('objects.modal_badge') }}</div>
              <h3 class="text-white font-bold text-xl">{{ t('objects.modal_title') }}</h3>
              <p v-if="apartment" class="text-white/40 text-xs mt-1">
                {{ roomLabel(apartment.rooms) }}, {{ apartment.area }} м² · {{ apartment.floor }} {{ t('objects.floor_word') }} · ${{ fmtPrice(apartment.price) }}
              </p>
              <p v-else class="text-white/35 text-xs mt-1">{{ t('objects.modal_no_apt') }}</p>
            </div>
            <button @click="close" class="text-white/30 hover:text-white mt-1 transition-colors" aria-label="Закрыть">
              <Icon name="close" class="w-5 h-5" :stroke-width="2" />
            </button>
          </div>

          <!-- Progress -->
          <div v-if="step < 4" class="flex items-center gap-2 mb-7">
            <div v-for="s in 3" :key="s" class="h-1 flex-1 rounded-full transition-all duration-300"
              :style="s <= step ? 'background:#C9A96E' : 'background:rgba(255,255,255,0.08)'"></div>
          </div>

          <!-- Step 1 -->
          <div v-if="step === 1" class="space-y-4">
            <p class="text-white/50 text-xs uppercase tracking-widest mb-4">{{ t('objects.step1') }}</p>
            <div>
              <label class="res-label">{{ t('objects.field_name') }} *</label>
              <input v-model="form.name" type="text" required class="res-input" placeholder="Иванов Иван"/>
              <p v-if="errors.name" class="text-red-400 text-xs mt-1">{{ errors.name }}</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="res-label">{{ t('objects.field_phone') }} *</label>
                <input v-model="form.phone" type="tel" required class="res-input" placeholder="+992 ..."/>
                <p v-if="errors.phone" class="text-red-400 text-xs mt-1">{{ errors.phone }}</p>
              </div>
              <div>
                <label class="res-label">{{ t('objects.field_email') }}</label>
                <input v-model="form.email" type="email" class="res-input" placeholder="email@..."/>
              </div>
            </div>
            <div>
              <label class="res-label">{{ t('objects.field_citizenship') }}</label>
              <select v-model="form.citizenship" class="res-input">
                <option value="tj">{{ t('objects.citizenship_tj') }}</option>
                <option value="ru">{{ t('objects.citizenship_ru') }}</option>
                <option value="uz">{{ t('objects.citizenship_uz') }}</option>
                <option value="kz">{{ t('objects.citizenship_kz') }}</option>
                <option value="other">{{ t('objects.citizenship_other') }}</option>
              </select>
            </div>
          </div>

          <!-- Step 2 -->
          <div v-if="step === 2" class="space-y-4">
            <p class="text-white/50 text-xs uppercase tracking-widest mb-4">{{ t('objects.step2') }}</p>
            <div>
              <label class="res-label">{{ t('objects.field_rooms') }}</label>
              <div class="grid grid-cols-4 gap-2">
                <button v-for="r in ['1','2','3','4+']" :key="r" type="button" @click="form.rooms = r"
                  class="py-2.5 rounded-xl text-sm font-bold transition-all border"
                  :style="form.rooms === r ? 'background:#C9A96E;color:#0C1220;border-color:#C9A96E' : 'background:transparent;color:rgba(255,255,255,0.4);border-color:rgba(255,255,255,0.08)'">
                  {{ r }}
                </button>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="res-label">{{ t('objects.field_budget_from') }}</label>
                <input v-model="form.budget_from" type="number" class="res-input" placeholder="30 000"/>
              </div>
              <div>
                <label class="res-label">{{ t('objects.field_budget_to') }}</label>
                <input v-model="form.budget_to" type="number" class="res-input" placeholder="80 000"/>
              </div>
            </div>
            <div>
              <label class="res-label">{{ t('objects.field_finish_sel') }}</label>
              <select v-model="form.finish" class="res-input">
                <option value="any">{{ t('objects.finish_any') }}</option>
                <option value="rough">{{ t('objects.finish_rough') }}</option>
                <option value="fine">{{ t('objects.finish_fine') }}</option>
                <option value="furnished">{{ t('objects.finish_furnished') }}</option>
              </select>
            </div>
          </div>

          <!-- Step 3 -->
          <div v-if="step === 3" class="space-y-4">
            <p class="text-white/50 text-xs uppercase tracking-widest mb-4">{{ t('objects.step3') }}</p>
            <div class="space-y-2">
              <label v-for="pm in payMethods" :key="pm.value"
                class="flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition-all"
                :style="form.payment_method === pm.value
                  ? 'border-color:rgba(201,169,110,0.4);background:rgba(201,169,110,0.06)'
                  : 'border-color:rgba(255,255,255,0.06)'">
                <input type="radio" v-model="form.payment_method" :value="pm.value" class="accent-gold"/>
                <div>
                  <div class="text-white text-sm font-medium">{{ pm.label }}</div>
                  <div class="text-white/30 text-xs">{{ pm.desc }}</div>
                </div>
              </label>
            </div>
            <!-- Summary -->
            <div class="rounded-xl p-4 border border-white/5" style="background:#0C1220">
              <div class="text-white/30 text-[10px] uppercase tracking-wider mb-3">{{ t('objects.summary_title') }}</div>
              <div class="grid grid-cols-2 gap-y-2 text-xs">
                <div class="text-white/40">{{ t('objects.summary_name') }}</div><div class="text-white font-medium">{{ form.name || '—' }}</div>
                <div class="text-white/40">{{ t('objects.summary_phone') }}</div><div class="text-white font-medium">{{ form.phone || '—' }}</div>
                <div class="text-white/40">{{ t('objects.summary_rooms') }}</div><div class="text-white font-medium">{{ form.rooms || '—' }}</div>
                <template v-if="apartment">
                  <div class="text-white/40">{{ t('objects.summary_apt') }}</div><div class="text-gold font-bold">{{ t('objects.apt_abbr') }} {{ apartment.number }}, {{ apartment.area }} м²</div>
                  <div class="text-white/40">{{ t('objects.summary_price') }}</div><div class="text-gold font-bold">${{ fmtPrice(apartment.price) }}</div>
                </template>
              </div>
            </div>
          </div>

          <!-- Done -->
          <div v-if="step === 4" class="text-center py-8">
            <div class="w-16 h-16 rounded-full bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center mx-auto mb-5">
              <Icon name="check" class="w-8 h-8 text-emerald-400" :stroke-width="2" />
            </div>
            <h3 class="text-white font-bold text-xl mb-2">{{ t('objects.done_title') }}</h3>
            <p class="text-white/40 text-sm">{{ t('objects.done_desc') }}</p>
            <button type="button" @click="close"
              class="mt-6 px-8 py-3 rounded-xl font-bold text-sm uppercase tracking-wider"
              style="background:#C9A96E;color:#0C1220">
              {{ t('objects.done_close') }}
            </button>
          </div>

          <!-- Nav buttons -->
          <div v-if="step < 4" class="flex gap-3 mt-7">
            <button v-if="step > 1" type="button" @click="step--"
              class="flex-1 py-3 rounded-xl text-sm font-semibold text-white/50 hover:text-white border border-white/8 transition-colors">
              {{ t('objects.btn_back') }}
            </button>
            <button v-if="step < 3" type="button" @click="next"
              class="flex-1 py-3 rounded-xl text-sm font-bold transition-colors disabled:opacity-50"
              style="background:#C9A96E;color:#0C1220">
              {{ t('objects.btn_next') }}
            </button>
            <button v-if="step === 3" type="button" @click="submit"
              :disabled="loading"
              class="flex-1 py-3 rounded-xl text-sm font-bold transition-colors disabled:opacity-50"
              style="background:#C9A96E;color:#0C1220">
              <span v-if="!loading">{{ t('objects.btn_confirm') }}</span>
              <span v-else class="flex items-center justify-center gap-2">
                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/>
                  <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" class="opacity-75"/>
                </svg>
                {{ t('objects.btn_sending') }}
              </span>
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { reactive, ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import Icon from '@/Components/Icon.vue'
import { useTrans } from '@/composables/useTrans'
import { useApartmentFormat } from '@/composables/useApartmentFormat'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  apartment:  { type: Object,  default: null },
})
const emit = defineEmits(['update:modelValue'])

const { t } = useTrans()
const { fmtPrice, roomLabel } = useApartmentFormat()

const step    = ref(1)
const loading = ref(false)
const errors  = reactive({})
const form = reactive({
  name: '', phone: '', email: '',
  citizenship: 'tj',
  rooms: '2', budget_from: '', budget_to: '',
  finish: 'any',
  payment_method: 'bank',
})

const payMethods = computed(() => [
  { value: 'bank', label: t('objects.pay_bank'), desc: t('objects.pay_bank_desc') },
  { value: 'card', label: t('objects.pay_card'), desc: t('objects.pay_card_desc') },
  { value: 'cash', label: t('objects.pay_cash'), desc: t('objects.pay_cash_desc') },
])

// При открытии — префилл из квартиры + сброс шага/ошибок
watch(() => props.modelValue, (open) => {
  if (open) {
    step.value = 1
    loading.value = false
    Object.keys(errors).forEach(k => delete errors[k])
    if (props.apartment) {
      form.rooms  = String(props.apartment.rooms)
      if (props.apartment.finish && props.apartment.finish !== 'none') form.finish = props.apartment.finish
    }
  }
})

function close() {
  emit('update:modelValue', false)
  step.value = 1
  loading.value = false
}

function next() {
  if (step.value === 1) {
    let valid = true
    if (!form.name.trim())  { errors.name = t('objects.err_name'); valid = false }  else delete errors.name
    if (!form.phone.trim()) { errors.phone = t('objects.err_phone'); valid = false } else delete errors.phone
    if (!valid) return
  }
  step.value++
}

function submit() {
  loading.value = true
  router.post(route('reserve.store'), {
    name:           form.name,
    phone:          form.phone,
    email:          form.email,
    citizenship:    form.citizenship,
    rooms:          form.rooms,
    budget_from:    form.budget_from,
    budget_to:      form.budget_to,
    finish:         form.finish,
    payment_method: form.payment_method,
    // Контекст квартиры (если бронируют конкретную) — попадёт в заметку лида
    apartment_id:     props.apartment?.id ?? null,
    apartment_number: props.apartment?.number ?? null,
  }, {
    preserveScroll: true,
    onSuccess() { step.value = 4; loading.value = false },
    onError(e)  { Object.assign(errors, e); loading.value = false },
    onFinish()  { loading.value = false },
  })
}
</script>

<style scoped>
.res-label {
  display: block;
  font-size: 0.68rem;
  font-weight: 600;
  color: rgba(255,255,255,0.35);
  text-transform: uppercase;
  letter-spacing: 0.1em;
  margin-bottom: 0.375rem;
}
.res-input {
  display: block;
  width: 100%;
  padding: 0.7rem 0.875rem;
  border-radius: 0.75rem;
  font-size: 0.875rem;
  color: #fff;
  background: #080E18;
  border: 1px solid rgba(255,255,255,0.07);
  outline: none;
  transition: border-color 0.2s;
}
.res-input:focus { border-color: rgba(201,169,110,0.45); }
.res-input::placeholder { color: rgba(255,255,255,0.18); }
option { background: #080E18; }

/* Modal transition */
.modal-enter-active, .modal-leave-active { transition: opacity 0.25s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
