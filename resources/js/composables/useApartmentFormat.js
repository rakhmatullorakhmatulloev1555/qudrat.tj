/**
 * useApartmentFormat — единые форматтеры и лейблы для квартир.
 * Используется в Objects.vue, ReserveModal.vue и на странице квартиры,
 * чтобы не дублировать логику (цена, комнатность, отделка, статус).
 */
import { useTrans } from '@/composables/useTrans'

export function useApartmentFormat() {
  const { t } = useTrans()

  const fmtPrice = (n) => (n ? Number(n).toLocaleString('ru-RU') : '0')

  const roomLabel = (n) => {
    const map = {
      1: t('objects.room_1'),
      2: t('objects.room_2'),
      3: t('objects.room_3'),
      4: t('objects.room_4'),
    }
    return map[n] || `${n}${t('objects.room_n')}`
  }

  const finishLabel = (f) => {
    const map = {
      none:      t('objects.finish_none'),
      rough:     t('objects.finish_rough'),
      fine:      t('objects.finish_fine'),
      furnished: t('objects.finish_furnished'),
    }
    return map[f] || f
  }

  const statusLabel = (s) => {
    const map = {
      available: t('objects.status_available'),
      reserved:  t('objects.status_reserved'),
      sold:      t('objects.status_sold'),
    }
    return map[s] || s
  }

  const statusBadge = (s) => {
    if (s === 'available') return 'bg-emerald-500/20 border border-emerald-500/40 text-emerald-400'
    if (s === 'reserved')  return 'bg-amber-500/20 border border-amber-500/40 text-amber-400'
    return 'bg-gray-500/15 border border-gray-500/30 text-gray-500'
  }

  // Цена за м² (округлённая). null, если данных нет — интерфейс это учитывает.
  const pricePerM2 = (price, area) =>
    (price && area && Number(area) > 0) ? Math.round(Number(price) / Number(area)) : null

  return { fmtPrice, roomLabel, finishLabel, statusLabel, statusBadge, pricePerM2 }
}
