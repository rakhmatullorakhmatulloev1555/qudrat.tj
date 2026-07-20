/**
 * useTrans — система переводов RU/TJ/EN
 * Использование: const { t, locale } = useTrans()
 * t('hero.title') → 'Живите на уровне будущего'
 * t('apartments.finish.fine') → 'Чистовая'
 */
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useTrans() {
  const page = usePage();

  const locale = computed(() => page.props.locale ?? 'ru');
  const translations = computed(() => page.props.translations ?? {});

  /**
   * Получить перевод по точечному ключу
   * @param {string} key  — 'hero.title' или 'common.close'
   * @param {Object} vars — замены {:name} → 'Алишер'
   */
  function t(key, vars = {}) {
    const keys = key.split('.');
    let value = translations.value;

    for (const k of keys) {
      if (value && typeof value === 'object' && k in value) {
        value = value[k];
      } else {
        // Ключ не найден — возвращаем сам ключ (видно в разработке)
        return key;
      }
    }

    if (typeof value !== 'string') return key;

    // Заменяем переменные: t('hello', { name: 'Мир' }) при 'Привет, :name!'
    return Object.entries(vars).reduce(
      (str, [k, v]) => str.replace(`:${k}`, v),
      value
    );
  }

  /**
   * Число с правильным окончанием (pluralization)
   * t_plural(5, 'год', 'года', 'лет') → '5 лет'
   */
  function tp(n, one, few, many) {
    const abs = Math.abs(n);
    const mod10 = abs % 10;
    const mod100 = abs % 100;

    if (mod100 >= 11 && mod100 <= 14) return `${n} ${many}`;
    if (mod10 === 1) return `${n} ${one}`;
    if (mod10 >= 2 && mod10 <= 4) return `${n} ${few}`;
    return `${n} ${many}`;
  }

  return { t, tp, locale };
}
