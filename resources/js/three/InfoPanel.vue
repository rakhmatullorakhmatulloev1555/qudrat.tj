<template>
  <div class="ip">
    <div class="ip-head">
      <div class="ip-badge">{{ roomLabel(apartment.rooms) }}</div>
      <div class="ip-num">{{ t('objects.apt_abbr') }} {{ apartment.number }}</div>
    </div>

    <div class="ip-price">
      <div class="ip-price-val">${{ fmtPrice(apartment.price) }}</div>
      <div class="ip-price-sub">{{ apartment.currency }}<span v-if="ppm"> · ${{ fmtPrice(ppm) }}/м²</span></div>
    </div>

    <div class="ip-specs">
      <div class="ip-spec"><span>Площадь</span><b>{{ apartment.area }} м²</b></div>
      <div class="ip-spec"><span>Комнат</span><b>{{ apartment.rooms }}</b></div>
      <div class="ip-spec"><span>Этаж</span><b>{{ apartment.floor }}</b></div>
      <div class="ip-spec"><span>Потолки</span><b>{{ apartment.ceiling_height }} м</b></div>
      <div v-if="apartment.finish" class="ip-spec"><span>Отделка</span><b>{{ finishLabel(apartment.finish) }}</b></div>
      <div class="ip-spec"><span>Статус</span><b :class="statusText(apartment.status)">{{ statusLabel(apartment.status) }}</b></div>
    </div>

    <!-- Фиксированная Premium CTA -->
    <button v-if="apartment.status === 'available'" class="ip-cta" @click="$emit('book')">
      Забронировать квартиру
    </button>
    <div v-else class="ip-cta ip-cta--off">{{ statusLabel(apartment.status) }}</div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useApartmentFormat } from '@/composables/useApartmentFormat'
import { useTrans } from '@/composables/useTrans'

const props = defineProps({
  apartment: { type: Object, required: true },
})
defineEmits(['book'])

const { t } = useTrans()
const { fmtPrice, roomLabel, finishLabel, statusLabel, pricePerM2 } = useApartmentFormat()

const ppm = computed(() => pricePerM2(props.apartment.price, props.apartment.area))
const statusText = (s) => s === 'available' ? 'text-emerald-400' : s === 'reserved' ? 'text-amber-400' : 'text-gray-400'
</script>

<style scoped>
.ip {
  width: 250px;
  padding: 18px;
  border-radius: 18px;
  background: rgba(10,14,26,0.78);
  backdrop-filter: blur(16px);
  border: 1px solid rgba(255,255,255,0.08);
  box-shadow: 0 12px 40px rgba(0,0,0,0.5);
  pointer-events: auto;
}
.ip-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
.ip-badge {
  font-size: 10px; font-weight: 800; letter-spacing: .04em; text-transform: uppercase;
  color: #0C1220; background: #C9A96E; padding: 4px 9px; border-radius: 7px;
}
.ip-num { color: rgba(255,255,255,0.4); font-size: 12px; }
.ip-price { margin-bottom: 16px; }
.ip-price-val { color: #C9A96E; font-weight: 800; font-size: 26px; line-height: 1; }
.ip-price-sub { color: rgba(255,255,255,0.4); font-size: 11px; margin-top: 4px; }
.ip-specs { display: flex; flex-direction: column; gap: 1px; margin-bottom: 16px; }
.ip-spec {
  display: flex; align-items: center; justify-content: space-between;
  padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.05);
  font-size: 12.5px;
}
.ip-spec span { color: rgba(255,255,255,0.45); }
.ip-spec b { color: #fff; font-weight: 600; }
.ip-cta {
  display: block; width: 100%; text-align: center;
  padding: 13px; border-radius: 12px;
  background: #C9A96E; color: #0C1220;
  font-weight: 700; font-size: 12px; text-transform: uppercase; letter-spacing: .1em;
  cursor: pointer; transition: background .2s;
}
.ip-cta:hover { background: #D8BC85; }
.ip-cta--off {
  background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.4);
  border: 1px solid rgba(255,255,255,0.08); cursor: default;
}

@media (max-width: 1024px) {
  .ip { width: 100%; }
  .ip-specs { display: grid; grid-template-columns: 1fr 1fr; gap: 0 16px; }
}
</style>
