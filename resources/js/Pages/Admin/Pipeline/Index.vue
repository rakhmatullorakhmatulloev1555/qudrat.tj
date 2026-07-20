<template>
  <AdminLayout title="Настройки воронки">
    <Head title="Воронки продаж" />
    <div class="p-6 max-w-6xl mx-auto space-y-6">

      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Воронки продаж</h1>
          <p class="text-sm text-gray-500 mt-1">Управление этапами и стадиями CRM-воронки</p>
        </div>
        <button @click="openPipelineModal()" class="btn-primary flex items-center gap-2">
          <span class="text-lg">+</span> Новая воронка
        </button>
      </div>

      <!-- Flash -->
      <div v-if="$page.props.flash?.success" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
        ✓ {{ $page.props.flash.success }}
      </div>
      <div v-if="$page.props.flash?.error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
        ✗ {{ $page.props.flash.error }}
      </div>

      <!-- Pipeline Tabs -->
      <div class="flex gap-2 flex-wrap">
        <button
          v-for="p in pipelines" :key="p.id"
          @click="activePipelineId = p.id"
          :class="[
            'px-4 py-2 rounded-xl text-sm font-medium transition-all border',
            activePipelineId === p.id
              ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm'
              : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-300 hover:text-indigo-600'
          ]"
        >
          {{ p.name }}
          <span v-if="p.is_default" class="ml-1.5 text-[10px] opacity-70 uppercase tracking-wider">default</span>
        </button>
      </div>

      <!-- Active Pipeline Card -->
      <template v-if="activePipeline">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

          <!-- Pipeline Header -->
          <div class="flex flex-wrap items-center justify-between gap-2 px-6 py-4 border-b border-gray-100 bg-gray-50" style="color:#111827">
            <div class="flex items-center gap-3">
              <div class="w-2 h-8 rounded-full" :class="activePipeline.is_active ? 'bg-green-400' : 'bg-gray-300'"></div>
              <div>
                <div class="font-semibold text-gray-900">{{ activePipeline.name }}</div>
                <div class="text-xs text-gray-400">{{ typeLabel(activePipeline.type) }} · {{ activePipeline.stages.length }} стадий</div>
              </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
              <button
                v-if="!activePipeline.is_default"
                @click="setDefault(activePipeline)"
                class="text-xs px-3 py-1.5 rounded-lg border border-gray-200 text-gray-500 hover:border-indigo-300 hover:text-indigo-600 transition-colors"
              >
                Сделать основной
              </button>
              <button @click="openPipelineModal(activePipeline)" class="icon-btn">✏️</button>
              <button
                v-if="!activePipeline.is_default"
                @click="deletePipeline(activePipeline)"
                class="icon-btn text-red-400 hover:text-red-600"
              >🗑️</button>
            </div>
          </div>

          <!-- Stages List -->
          <div class="p-6" style="color:#111827">
            <!-- Stage visual flow -->
            <div class="flex items-stretch gap-0 mb-6 overflow-x-auto pb-2">
              <template v-for="(stage, idx) in activePipeline.stages" :key="stage.id">
                <div
                  class="relative flex-shrink-0 group"
                  style="min-width: 140px"
                  draggable="true"
                  @dragstart="onDragStart(idx)"
                  @dragover.prevent="onDragOver(idx)"
                  @drop="onDrop(activePipeline)"
                  @dragend="dragIdx = null"
                >
                  <!-- Stage Chip -->
                  <div
                    :class="[
                      'relative mx-1 px-3 py-3 rounded-xl border-2 transition-all cursor-grab',
                      dragIdx === idx ? 'opacity-40 scale-95' : 'hover:shadow-md',
                      dragOver === idx ? 'border-dashed' : 'border-transparent'
                    ]"
                    :style="{ borderColor: dragOver === idx ? stage.color : 'transparent', backgroundColor: stage.color + '18' }"
                  >
                    <!-- Drag handle -->
                    <div class="absolute top-1 left-2 text-gray-300 text-xs opacity-0 group-hover:opacity-100 transition-opacity">⠿</div>

                    <!-- Color dot + name -->
                    <div class="flex items-center gap-2 mb-2">
                      <div class="w-3 h-3 rounded-full flex-shrink-0" :style="{ background: stage.color }"></div>
                      <span class="text-xs font-semibold text-gray-700 leading-tight">{{ stage.name }}</span>
                    </div>

                    <!-- Probability -->
                    <div class="text-center mb-2">
                      <span class="text-lg font-bold" :style="{ color: stage.color }">{{ stage.probability }}%</span>
                      <div class="text-[10px] text-gray-400">вероятность</div>
                    </div>

                    <!-- Badges -->
                    <div class="flex flex-wrap gap-1 justify-center mb-2">
                      <span v-if="stage.is_won"  class="badge-green">✓ Победа</span>
                      <span v-if="stage.is_lost" class="badge-red">✗ Отказ</span>
                    </div>

                    <!-- Stats -->
                    <div class="flex gap-2 text-[10px] text-gray-400 justify-center">
                      <span v-if="stage.leads_count">{{ stage.leads_count }} лид</span>
                      <span v-if="stage.deals_count">{{ stage.deals_count }} сделка</span>
                      <span v-if="!stage.leads_count && !stage.deals_count">—</span>
                    </div>

                    <!-- Actions (visible on hover) -->
                    <div class="flex gap-1 justify-center mt-2 opacity-0 group-hover:opacity-100 transition-opacity">
                      <button @click.stop="openStageModal(activePipeline, stage)" class="icon-btn-sm">✏️</button>
                      <button @click.stop="deleteStage(activePipeline, stage)" class="icon-btn-sm text-red-400 hover:text-red-600">🗑️</button>
                    </div>
                  </div>

                  <!-- Arrow connector -->
                  <div v-if="idx < activePipeline.stages.length - 1"
                    class="absolute top-1/2 -right-2 -translate-y-1/2 z-10 text-gray-300 text-xs pointer-events-none"
                  >▶</div>
                </div>
              </template>

              <!-- Add Stage Button -->
              <div class="flex-shrink-0 flex items-center ml-3">
                <button
                  @click="openStageModal(activePipeline)"
                  class="w-32 h-full min-h-[120px] border-2 border-dashed border-gray-200 rounded-xl text-gray-400 hover:border-indigo-300 hover:text-indigo-500 transition-colors flex flex-col items-center justify-center gap-1 text-sm"
                >
                  <span class="text-2xl">+</span>
                  <span class="text-xs">Добавить стадию</span>
                </button>
              </div>
            </div>

            <!-- Stages Table (compact) -->
            <table class="w-full text-sm">
              <thead>
                <tr class="text-left text-xs text-gray-400 border-b border-gray-100">
                  <th class="pb-2 font-medium">#</th>
                  <th class="pb-2 font-medium">Стадия</th>
                  <th class="pb-2 font-medium hidden sm:table-cell">Ключ</th>
                  <th class="pb-2 font-medium text-center">Вероятность</th>
                  <th class="pb-2 font-medium text-center hidden sm:table-cell">Лиды</th>
                  <th class="pb-2 font-medium text-center hidden sm:table-cell">Сделки</th>
                  <th class="pb-2 font-medium text-center hidden sm:table-cell">Флаги</th>
                  <th class="pb-2"></th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(stage, idx) in activePipeline.stages" :key="stage.id"
                  class="border-b border-gray-50 hover:bg-gray-50 transition-colors"
                >
                  <td class="py-2.5 text-gray-400 text-xs">{{ idx + 1 }}</td>
                  <td class="py-2.5">
                    <div class="flex items-center gap-2">
                      <div class="w-2.5 h-2.5 rounded-full" :style="{ background: stage.color }"></div>
                      <span class="font-medium text-gray-800">{{ stage.name }}</span>
                    </div>
                  </td>
                  <td class="py-2.5 hidden sm:table-cell">
                    <code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded text-gray-600">{{ stage.key }}</code>
                  </td>
                  <td class="py-2.5 text-center">
                    <div class="inline-flex items-center gap-1.5">
                      <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full" :style="{ width: stage.probability + '%', background: stage.color }"></div>
                      </div>
                      <span class="text-gray-600 text-xs">{{ stage.probability }}%</span>
                    </div>
                  </td>
                  <td class="py-2.5 text-center text-gray-600 hidden sm:table-cell">{{ stage.leads_count || '—' }}</td>
                  <td class="py-2.5 text-center text-gray-600 hidden sm:table-cell">{{ stage.deals_count || '—' }}</td>
                  <td class="py-2.5 text-center hidden sm:table-cell">
                    <span v-if="stage.is_won"  class="badge-green text-[10px]">Победа</span>
                    <span v-if="stage.is_lost" class="badge-red text-[10px]">Отказ</span>
                    <span v-if="!stage.is_won && !stage.is_lost" class="text-gray-300 text-xs">—</span>
                  </td>
                  <td class="py-2.5">
                    <div class="flex items-center gap-1 justify-end">
                      <button @click="openStageModal(activePipeline, stage)" class="icon-btn-sm">✏️</button>
                      <button @click="deleteStage(activePipeline, stage)" class="icon-btn-sm text-red-400 hover:text-red-600">🗑️</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </template>

      <!-- Empty state -->
      <div v-if="pipelines.length === 0" class="text-center py-20 text-gray-400">
        <div class="text-4xl mb-3">🔧</div>
        <div class="font-medium">Нет воронок</div>
        <div class="text-sm mt-1">Создайте первую воронку продаж</div>
      </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════════
         PIPELINE MODAL
    ══════════════════════════════════════════════════════════════ -->
    <Teleport to="body">
      <div v-if="showPipelineModal" class="modal-backdrop" @click.self="showPipelineModal = false">
        <div class="modal-box w-full max-w-md">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-gray-900">
              {{ pipelineForm.id ? 'Редактировать воронку' : 'Новая воронка' }}
            </h2>
            <button @click="showPipelineModal = false" class="text-gray-400 hover:text-gray-600 text-xl">×</button>
          </div>

          <form @submit.prevent="savePipeline" class="space-y-4">
            <div>
              <label class="form-label">Название воронки *</label>
              <input v-model="pipelineForm.name" required class="form-input" placeholder="Продажи недвижимости" />
            </div>
            <div>
              <label class="form-label">Тип</label>
              <select v-model="pipelineForm.type" class="form-input">
                <option value="sales">Продажи</option>
                <option value="real_estate">Недвижимость</option>
                <option value="mining">Горнодобыча</option>
                <option value="custom">Другое</option>
              </select>
            </div>

            <div class="flex gap-3 pt-2">
              <button type="submit" :disabled="saving" class="btn-primary flex-1">
                {{ saving ? 'Сохранение...' : (pipelineForm.id ? 'Сохранить' : 'Создать') }}
              </button>
              <button type="button" @click="showPipelineModal = false" class="btn-secondary flex-1">Отмена</button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- ══════════════════════════════════════════════════════════════
         STAGE MODAL
    ══════════════════════════════════════════════════════════════ -->
    <Teleport to="body">
      <div v-if="showStageModal" class="modal-backdrop" @click.self="showStageModal = false">
        <div class="modal-box w-full max-w-lg">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-gray-900">
              {{ stageForm.id ? 'Редактировать стадию' : 'Новая стадия' }}
            </h2>
            <button @click="showStageModal = false" class="text-gray-400 hover:text-gray-600 text-xl">×</button>
          </div>

          <form @submit.prevent="saveStage" class="space-y-5">
            <!-- Name -->
            <div>
              <label class="form-label">Название стадии *</label>
              <input v-model="stageForm.name" required class="form-input" placeholder="Переговоры" />
            </div>

            <!-- Color + Preview -->
            <div>
              <label class="form-label">Цвет стадии</label>
              <div class="flex items-center gap-3">
                <input v-model="stageForm.color" type="color" class="w-10 h-10 rounded-lg border border-gray-200 cursor-pointer p-0.5" />
                <div class="flex gap-2 flex-wrap">
                  <button
                    v-for="c in PRESET_COLORS" :key="c"
                    type="button"
                    @click="stageForm.color = c"
                    class="w-7 h-7 rounded-full border-2 transition-transform hover:scale-110"
                    :style="{ background: c, borderColor: stageForm.color === c ? '#374151' : 'transparent' }"
                  ></button>
                </div>
                <!-- Preview chip -->
                <div class="ml-auto px-3 py-1 rounded-lg text-xs font-semibold" :style="{ background: stageForm.color + '25', color: stageForm.color }">
                  {{ stageForm.name || 'Стадия' }}
                </div>
              </div>
            </div>

            <!-- Probability -->
            <div>
              <label class="form-label">
                Вероятность закрытия
                <span class="ml-2 font-bold" :style="{ color: stageForm.color }">{{ stageForm.probability }}%</span>
              </label>
              <input
                v-model.number="stageForm.probability"
                type="range" min="0" max="100" step="5"
                class="w-full h-2 rounded-full appearance-none cursor-pointer"
                :style="{ accentColor: stageForm.color }"
              />
              <div class="flex justify-between text-xs text-gray-400 mt-1">
                <span>0% — провал</span>
                <span>100% — победа</span>
              </div>
            </div>

            <!-- Flags -->
            <div class="grid grid-cols-2 gap-3">
              <label
                :class="['flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all',
                  stageForm.is_won ? 'border-green-400 bg-green-50' : 'border-gray-100 hover:border-green-200']"
              >
                <input v-model="stageForm.is_won" type="checkbox" class="sr-only" @change="onWonChange" />
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-lg"
                  :class="stageForm.is_won ? 'bg-green-100' : 'bg-gray-100'">✓</div>
                <div>
                  <div class="text-sm font-semibold" :class="stageForm.is_won ? 'text-green-700' : 'text-gray-700'">Стадия победы</div>
                  <div class="text-xs text-gray-400">Сделка выиграна</div>
                </div>
              </label>

              <label
                :class="['flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all',
                  stageForm.is_lost ? 'border-red-400 bg-red-50' : 'border-gray-100 hover:border-red-200']"
              >
                <input v-model="stageForm.is_lost" type="checkbox" class="sr-only" @change="onLostChange" />
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-lg"
                  :class="stageForm.is_lost ? 'bg-red-100' : 'bg-gray-100'">✗</div>
                <div>
                  <div class="text-sm font-semibold" :class="stageForm.is_lost ? 'text-red-700' : 'text-gray-700'">Стадия отказа</div>
                  <div class="text-xs text-gray-400">Сделка проиграна</div>
                </div>
              </label>
            </div>

            <!-- Key (readonly for existing) -->
            <div v-if="stageForm.id">
              <label class="form-label">Системный ключ</label>
              <input :value="stageForm.key" readonly class="form-input bg-gray-50 text-gray-500 cursor-not-allowed font-mono text-sm" />
              <p class="text-xs text-gray-400 mt-1">Ключ нельзя изменить — он связан с лидами и сделками</p>
            </div>

            <div class="flex gap-3 pt-2">
              <button type="submit" :disabled="saving" class="btn-primary flex-1">
                {{ saving ? 'Сохранение...' : (stageForm.id ? 'Сохранить' : 'Добавить стадию') }}
              </button>
              <button type="button" @click="showStageModal = false" class="btn-secondary flex-1">Отмена</button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  pipelines: { type: Array, default: () => [] },
})

// ── Preset colours ─────────────────────────────────────────────────────────
const PRESET_COLORS = [
  '#6366F1', '#3B82F6', '#06B6D4', '#10B981', '#F59E0B',
  '#EF4444', '#8B5CF6', '#EC4899', '#F97316', '#84CC16',
]

// ── Active pipeline ────────────────────────────────────────────────────────
const activePipelineId = ref(props.pipelines[0]?.id ?? null)
const activePipeline   = computed(() => props.pipelines.find(p => p.id === activePipelineId.value) ?? null)

function typeLabel(type) {
  return { sales: 'Продажи', real_estate: 'Недвижимость', mining: 'Горнодобыча', custom: 'Другое' }[type] ?? type
}

// ── Drag-and-drop reorder ──────────────────────────────────────────────────
const dragIdx  = ref(null)
const dragOver = ref(null)

function onDragStart(idx) { dragIdx.value = idx }
function onDragOver(idx)  { dragOver.value = idx }
function onDrop(pipeline) {
  if (dragIdx.value === null || dragOver.value === null || dragIdx.value === dragOver.value) {
    dragIdx.value = dragOver.value = null
    return
  }
  // reorder local array optimistically
  const stages = [...pipeline.stages]
  const [moved] = stages.splice(dragIdx.value, 1)
  stages.splice(dragOver.value, 0, moved)

  // send new order to server
  router.post(route('admin.pipelines.stages.reorder', pipeline.id), {
    order: stages.map(s => s.id),
  }, {
    preserveScroll: true,
    onFinish: () => { dragIdx.value = dragOver.value = null },
  })
}

// ── Saving state ───────────────────────────────────────────────────────────
const saving = ref(false)

// ═══════════════════════════════════════════════════════════
// PIPELINE MODAL
// ═══════════════════════════════════════════════════════════
const showPipelineModal = ref(false)
const pipelineForm = ref({ id: null, name: '', type: 'real_estate' })

function openPipelineModal(pipeline = null) {
  pipelineForm.value = pipeline
    ? { id: pipeline.id, name: pipeline.name, type: pipeline.type }
    : { id: null, name: '', type: 'real_estate' }
  showPipelineModal.value = true
}

function savePipeline() {
  saving.value = true
  const isEdit = !!pipelineForm.value.id
  const url    = isEdit
    ? route('admin.pipelines.update', pipelineForm.value.id)
    : route('admin.pipelines.store')
  const method = isEdit ? 'put' : 'post'

  router[method](url, {
    name: pipelineForm.value.name,
    type: pipelineForm.value.type,
  }, {
    preserveScroll: true,
    onSuccess: () => { showPipelineModal.value = false },
    onFinish:  () => { saving.value = false },
  })
}

function setDefault(pipeline) {
  router.patch(route('admin.pipelines.default', pipeline.id), {}, { preserveScroll: true })
}

function deletePipeline(pipeline) {
  if (!confirm(`Удалить воронку «${pipeline.name}»? Все стадии будут удалены.`)) return
  router.delete(route('admin.pipelines.destroy', pipeline.id), { preserveScroll: true })
}

// ═══════════════════════════════════════════════════════════
// STAGE MODAL
// ═══════════════════════════════════════════════════════════
const showStageModal   = ref(false)
const stagePipelineRef = ref(null)
const stageForm = ref({
  id: null, name: '', color: '#6366F1', probability: 20, is_won: false, is_lost: false, key: '',
})

function openStageModal(pipeline, stage = null) {
  stagePipelineRef.value = pipeline
  stageForm.value = stage ? {
    id:          stage.id,
    name:        stage.name,
    color:       stage.color,
    probability: stage.probability,
    is_won:      stage.is_won,
    is_lost:     stage.is_lost,
    key:         stage.key,
  } : {
    id: null, name: '', color: PRESET_COLORS[pipeline.stages.length % PRESET_COLORS.length],
    probability: 20, is_won: false, is_lost: false, key: '',
  }
  showStageModal.value = true
}

function saveStage() {
  saving.value = true
  const pipeline = stagePipelineRef.value
  const isEdit   = !!stageForm.value.id
  const url      = isEdit
    ? route('admin.pipelines.stages.update', { pipeline: pipeline.id, stage: stageForm.value.id })
    : route('admin.pipelines.stages.store', pipeline.id)
  const method   = isEdit ? 'put' : 'post'

  router[method](url, {
    name:        stageForm.value.name,
    color:       stageForm.value.color,
    probability: stageForm.value.probability,
    is_won:      stageForm.value.is_won,
    is_lost:     stageForm.value.is_lost,
  }, {
    preserveScroll: true,
    onSuccess: () => { showStageModal.value = false },
    onFinish:  () => { saving.value = false },
  })
}

function onWonChange()  { if (stageForm.value.is_won)  stageForm.value.is_lost = false }
function onLostChange() { if (stageForm.value.is_lost) stageForm.value.is_won  = false }

function deleteStage(pipeline, stage) {
  const hasData = stage.leads_count > 0 || stage.deals_count > 0
  const msg = hasData
    ? `Стадия «${stage.name}» содержит ${stage.leads_count} лид(ов) и ${stage.deals_count} сделку(ок). Удалить нельзя.`
    : `Удалить стадию «${stage.name}»?`
  if (hasData) { alert(msg); return }
  if (!confirm(msg)) return
  router.delete(route('admin.pipelines.stages.destroy', { pipeline: pipeline.id, stage: stage.id }), {
    preserveScroll: true,
  })
}
</script>

<style scoped>
@reference "tailwindcss";

/* ── Модальное окно: сбрасываем наследуемый светлый цвет AdminLayout ── */
.modal-backdrop {
  @apply fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4;
}
.modal-box {
  @apply bg-white rounded-2xl shadow-2xl p-6 max-h-[90vh] overflow-y-auto;
  color: #111827; /* gray-900 — жёсткий сброс, не наследуется от dark-layout */
}

/* ── Формы внутри модалки ── */
.form-label {
  @apply block text-sm font-medium mb-1.5;
  color: #374151; /* gray-700 */
}
.form-input {
  @apply w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm
         focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
         transition-shadow;
  background-color: #ffffff;
  color: #111827;
}
.form-input::placeholder { color: #9CA3AF; }
.form-input option        { color: #111827; background: #ffffff; }

/* ── Кнопки ── */
.btn-primary {
  @apply bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium
         px-4 py-2.5 rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed;
}
.btn-secondary {
  @apply bg-gray-100 hover:bg-gray-200 text-sm font-medium px-4 py-2.5 rounded-xl transition-colors;
  color: #374151;
}

/* ── Иконки-кнопки (вне модалки, на тёмном фоне — цвет от родителя) ── */
.icon-btn    { @apply w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/10 transition-colors text-sm; }
.icon-btn-sm { @apply w-6 h-6 flex items-center justify-center rounded text-xs hover:bg-white/10 transition-colors; }

/* ── Бейджи (внутри карточек на тёмном и в таблице) ── */
.badge-green { @apply inline-block bg-green-100 text-green-700 text-[10px] font-semibold px-1.5 py-0.5 rounded-full; }
.badge-red   { @apply inline-block bg-red-100 text-red-700 text-[10px] font-semibold px-1.5 py-0.5 rounded-full; }
</style>
