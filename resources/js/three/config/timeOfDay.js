/**
 * timeOfDay — пресеты времени суток. Меняют освещение/экспозицию/фон
 * поверх выбранного стиля (мультипликаторы). Расширяемо: добавить пресет = добавить объект.
 */
export const timesOfDay = {
  day: {
    id: 'day', name: 'День', icon: '☀',
    ambientMul: 1.0, hemiMul: 1.0, dirMul: 1.0, accentMul: 0.6,
    dirColor: '#ffffff', exposureMul: 1.0, bg: null,
  },
  sunset: {
    id: 'sunset', name: 'Закат', icon: '🌇',
    ambientMul: 0.75, hemiMul: 0.7, dirMul: 0.85, accentMul: 1.0,
    dirColor: '#FF9A52', exposureMul: 1.06, bg: '#1A1008',
  },
  night: {
    id: 'night', name: 'Ночь', icon: '🌙',
    ambientMul: 0.35, hemiMul: 0.4, dirMul: 0.25, accentMul: 1.35,
    dirColor: '#9DB4FF', exposureMul: 0.9, bg: '#05070D',
  },
}

export const defaultTimeId = 'day'
export const timeList = () => Object.values(timesOfDay)
