/**
 * roomsConfig — комнаты, точки камеры и процедурная планировка квартиры.
 *
 * Координаты: пол в плоскости XZ, центр в (0,0). Высота потолка H.
 * Квартира ~12м (X: -6..6) × 8м (Z: -4..4). Уровень глаз ~1.55м.
 *
 * `layout` — «демо-модель» из примитивов. Когда появятся реальные GLB,
 * достаточно заменить <ProceduralApartment> на загрузчик GLTF —
 * камеры/стили/навигация продолжат работать без изменений.
 */
export const APARTMENT = { width: 12, depth: 8, height: 2.8, wallT: 0.15 }

const EYE = 1.55

// ── Комнаты + точки камеры (позиция и цель взгляда) ──
export const rooms = [
  {
    id: 'hall', name: 'Прихожая', icon: '🚪',
    camera: { pos: [0, EYE + 0.05, 3.4], target: [0, 1.0, -1] },
  },
  {
    id: 'living', name: 'Гостиная', icon: '🛋',
    camera: { pos: [3.4, EYE + 0.35, 2.6], target: [-3, 1.0, -1.5] },
  },
  {
    id: 'kitchen', name: 'Кухня', icon: '🍳',
    camera: { pos: [-3.2, EYE + 0.25, 2.4], target: [3.5, 0.9, -1.2] },
  },
  {
    id: 'bedroom', name: 'Спальня', icon: '🛏',
    camera: { pos: [-2.4, EYE + 0.2, -0.6], target: [-4.5, 0.7, -3.2] },
  },
  {
    id: 'bathroom', name: 'Ванная', icon: '🛁',
    camera: { pos: [3.0, EYE, -0.4], target: [4.8, 0.8, -3.2] },
  },
  {
    id: 'balcony', name: 'Балкон', icon: '🌇',
    camera: { pos: [0, EYE + 0.3, 5.6], target: [0, 1.2, 0] },
  },
]

// ── Кинематографический пролёт при открытии (архитектурная презентация) ──
// Вход через дверь → Прихожая → Гостиная → Кухня → Балкон → Спальня → Ванная → Обзор
export const introSequence = [
  { pos: [0, 1.65, 8.6], target: [0, 1.2, 2], duration: 0 },          // снаружи, у входной двери
  { pos: [0, EYE + 0.05, 3.4], target: [0, 1, -1], duration: 2.0 },   // прихожая
  { pos: [3.4, EYE + 0.35, 2.6], target: [-3, 1.0, -1.5], duration: 2.2 }, // гостиная
  { pos: [-3.2, EYE + 0.25, 2.4], target: [3.5, 0.9, -1.2], duration: 2.2 }, // кухня
  { pos: [0, EYE + 0.3, 5.4], target: [0, 1.2, 0], duration: 2.0 },   // балкон
  { pos: [-2.4, EYE + 0.2, -0.6], target: [-4.5, 0.7, -3.2], duration: 2.2 }, // спальня
  { pos: [3.0, EYE, -0.4], target: [4.8, 0.8, -3.2], duration: 2.0 }, // ванная
  { pos: [9, 6, 9], target: [0, 1, 0], duration: 2.6 },              // финальный общий обзор
]

// ── Hotspots: площади и подписи комнат (для миникарты и точек интереса) ──
export const hotspots = [
  { slug: 'hall',     name: 'Прихожая', area: 5.0,  map: [0, 3.2] },
  { slug: 'living',   name: 'Гостиная', area: 24.0, map: [-3, 2] },
  { slug: 'kitchen',  name: 'Кухня',    area: 14.0, map: [3, 2] },
  { slug: 'bedroom',  name: 'Спальня',  area: 16.0, map: [-3.5, -2.5] },
  { slug: 'bathroom', name: 'Ванная',   area: 6.0,  map: [4, -2.5] },
  { slug: 'balcony',  name: 'Балкон',   area: 5.0,  map: [0, 5] },
]

// ── Схема для миникарты (прямоугольники комнат в координатах XZ) ──
export const minimap = {
  bounds: { x1: -6.4, z1: -4.4, x2: 6.4, z2: 6.4 },
  door:   [0, 4],
  zones: [
    { slug: 'bedroom',  name: 'Спальня',  x1: -6, z1: -4, x2: 1.5, z2: 0 },
    { slug: 'bathroom', name: 'Ванная',   x1: 1.5, z1: -4, x2: 6, z2: 0 },
    { slug: 'living',   name: 'Гостиная', x1: -6, z1: 0, x2: 0, z2: 4 },
    { slug: 'kitchen',  name: 'Кухня',    x1: 0, z1: 0, x2: 6, z2: 4 },
    { slug: 'balcony',  name: 'Балкон',   x1: -1.6, z1: 4, x2: 1.6, z2: 6 },
  ],
}

/**
 * Планировка: стены и мебель как примитивы.
 * material — ключ из interiorStyles.materials.
 * type: 'box' (по умолчанию). position — центр. size — [w,h,d].
 */
const H = APARTMENT.height
const T = APARTMENT.wallT

export const layout = {
  // Пол и потолок генерируются в компоненте отдельно (по размерам APARTMENT).
  walls: [
    // Внешний периметр
    { position: [0, H / 2, -4], size: [12, H, T], material: 'wall' },        // задняя
    { position: [-6, H / 2, 0], size: [T, H, 8], material: 'wall' },         // левая
    { position: [6, H / 2, 0], size: [T, H, 8], material: 'wall' },          // правая
    // Передняя стена — два простенка, между ними панорамное остекление (балкон)
    { position: [-4.5, H / 2, 4], size: [3, H, T], material: 'wall' },
    { position: [4.5, H / 2, 4], size: [3, H, T], material: 'wall' },
    { position: [0, H - 0.35, 4], size: [3, 0.7, T], material: 'wall' },     // перемычка над окном
    // Панорамное остекление (балконная зона)
    { position: [0, (H - 0.7) / 2 + 0.0, 4], size: [3, H - 0.7, 0.05], material: 'glass' },
    // Перегородка front/back (с дверным проёмом справа)
    { position: [-3, H / 2, 0], size: [6, H, T], material: 'wall' },
    { position: [4.5, H / 2, 0], size: [3, H, T], material: 'wall' },
    // Перегородка спальня / ванная
    { position: [1.5, H / 2, -2], size: [T, H, 4], material: 'wall' },
    // Двери
    { position: [1.5, 1.05, -0.9], size: [0.9, 2.1, 0.06], material: 'door' },
  ],
  furniture: [
    // ── Гостиная (front-left) ──
    { room: 'living', position: [-3.6, 0.4, -2.2], size: [2.6, 0.8, 1.0], material: 'sofa' },          // диван
    { room: 'living', position: [-3.6, 0.85, -2.7], size: [2.6, 0.5, 0.25], material: 'sofa' },        // спинка
    { room: 'living', position: [-3.4, 0.25, -0.9], size: [1.2, 0.4, 0.6], material: 'wood' },         // журн. столик
    { room: 'living', position: [-5.7, 1.2, -2.0], size: [0.15, 1.4, 2.4], material: 'accent' },       // ТВ-панель
    { room: 'living', position: [-3.6, 0.02, -1.6], size: [3.2, 0.04, 2.4], material: 'accent' },      // ковёр
    // ── Кухня (front-right) ──
    { room: 'kitchen', position: [4.2, 0.45, -2.4], size: [3.2, 0.9, 0.65], material: 'wood' },        // гарнитур
    { room: 'kitchen', position: [4.2, 0.92, -2.4], size: [3.2, 0.05, 0.7], material: 'metal' },       // столешница
    { room: 'kitchen', position: [3.2, 0.5, -0.6], size: [1.6, 1.0, 0.9], material: 'wood' },          // остров
    { room: 'kitchen', position: [3.2, 1.02, -0.6], size: [1.7, 0.06, 1.0], material: 'metal' },       // топ острова
    // ── Спальня (back-left) ──
    { room: 'bedroom', position: [-4.2, 0.35, -3.0], size: [2.0, 0.5, 1.0], material: 'wood', rot: 0 },// кровать-база
    { room: 'bedroom', position: [-4.2, 0.7, -3.35], size: [2.0, 0.7, 0.2], material: 'sofa' },        // изголовье
    { room: 'bedroom', position: [-4.2, 0.62, -2.75], size: [1.9, 0.18, 0.9], material: 'sofa' },      // матрас/бельё
    { room: 'bedroom', position: [-1.6, 1.1, -3.4], size: [0.5, 2.2, 1.6], material: 'wood' },         // шкаф
    // ── Ванная (back-right) ──
    { room: 'bathroom', position: [4.6, 0.3, -3.2], size: [1.4, 0.6, 0.75], material: 'glass' },       // ванна
    { room: 'bathroom', position: [2.6, 0.5, -3.4], size: [0.7, 1.0, 0.5], material: 'metal' },        // тумба
    { room: 'bathroom', position: [2.6, 1.05, -3.4], size: [0.75, 0.08, 0.55], material: 'accent' },   // раковина-топ
    // ── Балкон (front, снаружи) ──
    { room: 'balcony', position: [0, 0.05, 5.0], size: [3.2, 0.1, 1.8], material: 'wood' },            // настил
    { room: 'balcony', position: [0, 0.55, 5.9], size: [3.2, 1.0, 0.06], material: 'glass' },          // ограждение
    { room: 'balcony', position: [-1.0, 0.35, 4.9], size: [0.6, 0.6, 0.6], material: 'accent' },       // кресло
  ],
}

// Табличка «пол/потолок» размеры для генерации плоскостей
export const groundConfig = {
  floor:   { size: [APARTMENT.width, APARTMENT.depth], y: 0,      material: 'floor' },
  ceiling: { size: [APARTMENT.width, APARTMENT.depth], y: H,      material: 'ceiling' },
}
