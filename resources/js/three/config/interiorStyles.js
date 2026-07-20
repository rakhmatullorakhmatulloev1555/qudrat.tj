/**
 * interiorStyles — data-driven конфигурация стилей интерьера.
 *
 * Добавить новый стиль = добавить объект в этот файл.
 * НИКАКОЙ код сцены менять не нужно — материалы и свет читаются отсюда.
 *
 * Каждый материал: { color, roughness, metalness, emissive?, emissiveIntensity? }
 * Позже сюда же можно добавить `map`/`normalMap` (URL текстур) для реальных PBR-материалов.
 */
export const interiorStyles = {
  'hi-tech': {
    id: 'hi-tech',
    name: 'Hi-Tech',
    accentHex: '#3B82F6',
    background: '#0B0F17',
    materials: {
      wall:    { color: '#E7EAEF', roughness: 0.95, metalness: 0.0 },
      floor:   { color: '#33383F', roughness: 0.30, metalness: 0.15 },
      ceiling: { color: '#F3F5F8', roughness: 1.0,  metalness: 0.0 },
      door:    { color: '#C7CBD1', roughness: 0.25, metalness: 0.85 },
      wood:    { color: '#4C5158', roughness: 0.55, metalness: 0.10 },
      metal:   { color: '#C9CCD1', roughness: 0.20, metalness: 0.95 },
      sofa:    { color: '#454A52', roughness: 0.85, metalness: 0.0 },
      accent:  { color: '#3B82F6', roughness: 0.40, metalness: 0.55, emissive: '#1E40AF', emissiveIntensity: 0.35 },
      glass:   { color: '#BFE3FF', roughness: 0.05, metalness: 0.0, opacity: 0.35, transparent: true },
    },
    lighting: {
      ambient: 0.55, hemi: 0.35, dir: 1.15,
      accentColor: '#3B82F6', accentIntensity: 14,
      exposure: 1.05,
    },
  },

  'modern-classic': {
    id: 'modern-classic',
    name: 'Modern Classic',
    accentHex: '#B8935A',
    background: '#14110C',
    materials: {
      wall:    { color: '#EDE6D8', roughness: 0.9,  metalness: 0.0 },
      floor:   { color: '#7A5C3E', roughness: 0.45, metalness: 0.05 },
      ceiling: { color: '#F6F1E7', roughness: 1.0,  metalness: 0.0 },
      door:    { color: '#E7DFD0', roughness: 0.6,  metalness: 0.05 },
      wood:    { color: '#8A6A47', roughness: 0.5,  metalness: 0.05 },
      metal:   { color: '#C9A96E', roughness: 0.35, metalness: 0.9 },
      sofa:    { color: '#B9AC93', roughness: 0.9,  metalness: 0.0 },
      accent:  { color: '#C9A96E', roughness: 0.35, metalness: 0.85, emissive: '#000000', emissiveIntensity: 0 },
      glass:   { color: '#DCEBF5', roughness: 0.06, metalness: 0.0, opacity: 0.30, transparent: true },
    },
    lighting: {
      ambient: 0.5, hemi: 0.4, dir: 1.05,
      accentColor: '#FFCF8A', accentIntensity: 10,
      exposure: 1.0,
    },
  },

  'classic-luxury': {
    id: 'classic-luxury',
    name: 'Classic Luxury',
    accentHex: '#D4AF37',
    background: '#120E08',
    materials: {
      wall:    { color: '#F3ECD9', roughness: 0.85, metalness: 0.0 },
      floor:   { color: '#4E3520', roughness: 0.35, metalness: 0.10 },
      ceiling: { color: '#FBF6E9', roughness: 0.95, metalness: 0.0 },
      door:    { color: '#3A2817', roughness: 0.4,  metalness: 0.1 },
      wood:    { color: '#5C3D22', roughness: 0.4,  metalness: 0.08 },
      metal:   { color: '#D4AF37', roughness: 0.25, metalness: 1.0 },
      sofa:    { color: '#7C1F2B', roughness: 0.75, metalness: 0.0 },
      accent:  { color: '#D4AF37', roughness: 0.2,  metalness: 1.0, emissive: '#3A2A00', emissiveIntensity: 0.15 },
      glass:   { color: '#EAD9B0', roughness: 0.05, metalness: 0.0, opacity: 0.28, transparent: true },
    },
    lighting: {
      ambient: 0.45, hemi: 0.35, dir: 1.0,
      accentColor: '#FFE29A', accentIntensity: 12,
      exposure: 0.98,
    },
  },
}

export const defaultStyleId = 'modern-classic'

export const styleList = () => Object.values(interiorStyles)
