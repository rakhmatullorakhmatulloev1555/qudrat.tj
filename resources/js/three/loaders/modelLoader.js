/**
 * modelLoader — точка расширения для реальных GLB-моделей (Этап 2+).
 *
 * Настроен GLTFLoader + DRACOLoader (Draco-декодер самохостится в /public/draco/,
 * без внешних CDN → CSP-safe). MVP использует процедурную геометрию и этот
 * загрузчик не вызывает, но когда появятся реальные модели квартир —
 * достаточно вызвать loadModel(url) в компоненте сцены, БЕЗ изменения логики стилей/камеры.
 *
 * Пример (Этап 2):
 *   const gltf = await loadModel('/models/apartments/12/base.glb')
 *   scene.add(gltf.scene)
 */
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js'
import { DRACOLoader } from 'three/examples/jsm/loaders/DRACOLoader.js'

let _loader = null

export function getModelLoader() {
  if (_loader) return _loader
  const loader = new GLTFLoader()
  const draco = new DRACOLoader()
  draco.setDecoderPath('/draco/')      // самохостинг, см. public/draco/
  loader.setDRACOLoader(draco)
  _loader = loader
  return _loader
}

export function loadModel(url, onProgress) {
  return new Promise((resolve, reject) => {
    getModelLoader().load(url, resolve, onProgress, reject)
  })
}

/** Освобождение ресурсов декодера (при полном выходе из 3D). */
export function disposeModelLoader() {
  try { _loader?.dracoLoader?.dispose?.() } catch (e) {}
  _loader = null
}
