<template>
  <div class="logo-wrap" :style="{ width: width + 'px' }">
    <svg
      viewBox="0 0 420 420"
      xmlns="http://www.w3.org/2000/svg"
      class="logo-svg"
    >
      <!-- Буква Q (появляется с масштабированием) -->
      <g class="q-group" transform="scale(0.055)">
        <path
          fill="#C9A96E"
          d="M0 2950 l0 -2950 3835 0 3835 0 0 2950 0 2950 -3835 0 -3835 0 0
-2950z m4183 2336 c772 -126 1420 -587 1776 -1268 268 -509 341 -1077 215
-1647 -63 -284 -197 -603 -347 -826 -58 -87 -110 -149 -201 -242 -44 -46 -76
-83 -69 -83 28 0 155 46 223 80 199 102 303 251 346 495 l7 40 14 -55 c8 -30
17 -122 20 -205 4 -131 2 -161 -16 -233 -53 -214 -198 -387 -404 -482 -164
-76 -344 -105 -609 -98 l-179 5 -91 -44 c-457 -224 -1013 -294 -1518 -192
-913 184 -1640 873 -1866 1767 -61 242 -69 309 -69 602 0 232 3 281 23 393
113 631 440 1166 942 1542 329 247 747 411 1180 464 112 14 507 6 623 -13z"
          transform="translate(0,590) scale(0.1,-0.1)"
        />
      </g>

      <!-- Билдинги внутри Q (выезжают снизу вверх) -->
      <g class="buildings-wrapper" transform="scale(0.055)">
        <g class="buildings">
          <path
            fill="#C9A96E"
            d="M3585 4919 c-735 -99 -1337 -546 -1627 -1209..."
            transform="translate(0,590) scale(0.1,-0.1)"
          />
        </g>
      </g>
    </svg>

    <div class="text-block">
      <div class="brand-name">QUDRAT</div>
      <div class="brand-sub">LLC | ESTD. 2010</div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  width: {
    type: Number,
    default: 420
  }
})
</script>

<style scoped>
.logo-wrap {
  display: inline-flex;
  align-items: center;
  gap: 12px;
}

.logo-svg {
  width: 78px;
  height: 78px;
  overflow: visible;
  flex-shrink: 0;
}

/* Анимация буквы Q */
.q-group {
  opacity: 0;
  transform-origin: 210px 210px;
  animation: showQ 0.7s cubic-bezier(0.2, 0.9, 0.4, 1.1) forwards;
}

@keyframes showQ {
  0% {
    opacity: 0;
    transform: scale(0.055) scale(0.7);
  }
  100% {
    opacity: 1;
    transform: scale(0.055) scale(1);
  }
}

/* Контейнер для билдингов — маскируем через overflow и сдвиг */
.buildings-wrapper {
  transform-origin: 210px 210px;
  transform: scale(0.055);
  overflow: hidden;
  position: relative;
}

.buildings {
  animation: slideUp 0.8s cubic-bezier(0.2, 0.8, 0.4, 1) 0.6s forwards;
  transform: translateY(100%);
}

@keyframes slideUp {
  0% {
    transform: translateY(100%);
  }
  100% {
    transform: translateY(0%);
  }
}

/* Анимация текста */
.text-block {
  opacity: 0;
  transform: translateX(16px);
  animation: showText 0.6s ease 1.3s forwards;
}

@keyframes showText {
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.brand-name {
  font-family: 'Inter', sans-serif;
  font-size: 42px;
  font-weight: 700;
  line-height: 1;
  color: #0D355D;
  letter-spacing: 1px;
}

.brand-sub {
  font-family: 'Inter', sans-serif;
  font-size: 11px;
  letter-spacing: 2px;
  color: #C9A96E;
  text-align: center;
  margin-top: 4px;
}
</style>