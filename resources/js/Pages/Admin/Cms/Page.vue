<template>
  <AdminLayout :title="`CMS — ${label}`">
    <Head :title="`CMS — ${label}`" />
    <div class="space-y-5">

      <!-- Header -->
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
          <Link :href="route('admin.cms.index')"
            class="w-9 h-9 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center text-gray-400 hover:text-white transition-colors">
            ←
          </Link>
          <div>
            <h1 class="text-xl font-bold text-white">{{ label }}</h1>
            <p class="text-sm text-gray-400">Редактирование контента страницы</p>
          </div>
        </div>

        <!-- Init button (if no sections yet) -->
        <button v-if="hasNoSections"
          @click="initFromTemplate"
          :disabled="initing"
          class="btn-primary flex items-center gap-2">
          <span>🚀</span>
          {{ initing ? 'Создание…' : 'Создать из шаблона' }}
        </button>
      </div>

      <!-- Locale tabs -->
      <div class="flex gap-2">
        <button v-for="loc in locales" :key="loc"
          @click="activeLocale = loc"
          class="px-4 py-2 rounded-lg text-sm font-semibold transition-all"
          :class="activeLocale === loc
            ? 'bg-gold text-dark'
            : 'bg-white/5 text-gray-400 hover:text-white hover:bg-white/10'">
          {{ localeFlag(loc) }} {{ localeLabel(loc) }}
        </button>

        <!-- Init for current locale -->
        <button v-if="!hasNoSections"
          @click="initFromTemplate"
          :disabled="initing"
          class="ml-auto btn-secondary text-xs flex items-center gap-1.5">
          <span>🔄</span>
          {{ initing ? 'Создание…' : 'Создать отсутствующие' }}
        </button>
      </div>

      <!-- Sections list -->
      <div class="space-y-3">
        <div v-for="(sectionData, sectionKey) in currentSections" :key="`${activeLocale}-${sectionKey}`"
          class="card border transition-all"
          :class="[
            activeSectionKey === sectionKey ? 'border-gold/40' : 'border-white/5',
            !sectionData.is_active && sectionData.in_db ? 'opacity-60' : '',
          ]">

          <!-- Section header -->
          <div class="flex items-center justify-between gap-3 cursor-pointer"
            @click="toggleSection(sectionKey)">
            <div class="flex items-center gap-3 flex-1 min-w-0">
              <!-- Icon -->
              <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg flex-shrink-0"
                :class="sectionData.in_db ? 'bg-gold/15' : 'bg-white/5'">
                {{ sectionData.schema?.icon ?? '📄' }}
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                  <span class="font-semibold text-white">
                    {{ sectionData.schema?.label ?? sectionKey }}
                  </span>
                  <span class="text-[10px] font-mono text-gray-600 bg-white/5 px-1.5 py-0.5 rounded">
                    {{ sectionKey }}
                  </span>
                  <!-- Status badges -->
                  <span v-if="!sectionData.in_db" class="badge badge-gold text-[10px]">Не заполнена</span>
                  <span v-else-if="sectionData.is_active" class="badge badge-green text-[10px]">Активна</span>
                  <span v-else class="badge badge-red text-[10px]">Скрыта</span>
                </div>
                <div v-if="sectionData.in_db" class="text-xs text-gray-500 mt-0.5">
                  Обновлено: {{ sectionData.updated_at ?? '—' }}
                </div>
                <div v-else class="text-xs text-gray-600 mt-0.5">
                  Нажмите «Редактировать» чтобы создать секцию
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2 flex-shrink-0" @click.stop>
              <!-- Active toggle (only if in DB) -->
              <button v-if="sectionData.in_db"
                @click="toggleActive(sectionKey, sectionData)"
                class="text-xs px-2 py-1 rounded-lg transition-colors"
                :class="sectionData.is_active
                  ? 'bg-green-500/10 text-green-400 hover:bg-red-500/10 hover:text-red-400'
                  : 'bg-red-500/10 text-red-400 hover:bg-green-500/10 hover:text-green-400'"
                :title="sectionData.is_active ? 'Скрыть секцию' : 'Показать секцию'">
                {{ sectionData.is_active ? '👁 Видна' : '🙈 Скрыта' }}
              </button>

              <button @click="openEditor(sectionKey, sectionData)" class="btn-primary text-xs px-3 py-1.5">
                ✏️ Редактировать
              </button>

              <button v-if="sectionData.in_db"
                @click="confirmDestroy(sectionKey, sectionData)"
                class="w-8 h-8 rounded-lg hover:bg-red-500/10 text-gray-600 hover:text-red-400 transition-colors flex items-center justify-center text-sm">
                🗑
              </button>
            </div>
          </div>

          <!-- Content preview (collapsed by default) -->
          <div v-if="sectionData.in_db && activeSectionKey !== sectionKey"
            class="mt-3 grid grid-cols-2 md:grid-cols-3 gap-2">
            <div v-for="(val, key) in previewFields(sectionData)" :key="key"
              class="bg-black/20 rounded-lg p-2">
              <div class="text-[10px] text-gray-500 uppercase tracking-wider mb-0.5">{{ key }}</div>
              <div class="text-xs text-gray-300 truncate">{{ val }}</div>
            </div>
          </div>
        </div>

        <!-- Empty state -->
        <div v-if="!Object.keys(currentSections).length"
          class="card border border-dashed border-white/10 text-center py-16">
          <div class="text-4xl mb-3">📭</div>
          <div class="text-white font-semibold mb-1">Секции не созданы</div>
          <p class="text-gray-400 text-sm mb-4">
            Нажмите «Создать из шаблона» чтобы добавить все секции сразу
          </p>
          <button @click="initFromTemplate" :disabled="initing" class="btn-primary mx-auto">
            🚀 Создать из шаблона
          </button>
        </div>
      </div>

      <!-- Add custom section -->
      <div class="flex justify-center pt-2">
        <button @click="openNewSection" class="btn-secondary text-sm flex items-center gap-2">
          + Добавить кастомную секцию
        </button>
      </div>
    </div>

    <!-- ─── Section Editor Modal ───────────────────────────────────────── -->
    <Teleport to="body">
      <div v-if="editorOpen" class="modal-overlay" @click.self="editorOpen = false">
        <div class="modal-box w-full max-w-2xl max-h-[92vh] flex flex-col">

          <!-- Modal header -->
          <div class="flex items-center justify-between mb-4 shrink-0">
            <div class="flex items-center gap-2">
              <span class="text-xl">{{ editingSchema?.icon ?? '📄' }}</span>
              <div>
                <h2 class="text-base font-bold text-white">
                  {{ isNew ? 'Новая секция' : (editingSchema?.label ?? editingKey) }}
                </h2>
                <div class="text-xs text-gray-500 font-mono">
                  {{ activeLocale.toUpperCase() }} / {{ isNew ? '...' : editingKey }}
                </div>
              </div>
            </div>
            <button @click="editorOpen = false" class="text-gray-400 hover:text-white text-xl leading-none">×</button>
          </div>

          <!-- Copy from RU (shown only on TJ / EN when RU data exists) -->
          <div v-if="activeLocale !== 'ru' && ruSectionContent" class="mb-3 shrink-0">
            <button @click="copyFromRu"
              class="w-full flex items-center justify-center gap-2 py-2 rounded-lg text-xs font-semibold border border-dashed border-gold/40 text-gold hover:bg-gold/5 transition-colors">
              📋 Заполнить из русской версии (для перевода)
            </button>
          </div>

          <!-- Tabs: Form / JSON -->
          <div class="flex gap-1 mb-4 shrink-0">
            <button @click="editorTab = 'form'"
              class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all"
              :class="editorTab === 'form' ? 'bg-gold text-dark' : 'bg-white/5 text-gray-400 hover:text-white'">
              📋 Форма
            </button>
            <button @click="editorTab = 'json'"
              class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all"
              :class="editorTab === 'json' ? 'bg-gold text-dark' : 'bg-white/5 text-gray-400 hover:text-white'">
              {} JSON
            </button>
          </div>

          <!-- Form body -->
          <div class="overflow-y-auto flex-1 space-y-4 pr-1">

            <!-- New section: key input -->
            <div v-if="isNew">
              <label class="block text-xs text-gray-400 mb-1">Ключ секции *</label>
              <input v-model="editForm.section" type="text"
                class="input w-full font-mono text-sm"
                placeholder="hero, cta, stats, custom_block…" />
              <p class="text-xs text-gray-500 mt-1">Латиница, нижнее_подчёркивание. Уникален для страницы.</p>
            </div>

            <!-- ── FORM TAB ── -->
            <template v-if="editorTab === 'form'">
              <template v-if="editingSchema?.fields?.length">
                <div v-for="field in editingSchema.fields" :key="field.key">
                  <label class="block text-xs text-gray-400 mb-1">{{ field.label }}</label>

                  <!-- textarea / richtext -->
                  <textarea v-if="field.type === 'textarea' || field.type === 'richtext'"
                    v-model="formFields[field.key]"
                    :rows="field.type === 'richtext' ? 5 : 3"
                    class="input w-full resize-none text-sm"
                    :placeholder="field.placeholder ?? ''"></textarea>

                  <!-- image: URL input + live preview -->
                  <div v-else-if="field.type === 'image'">
                    <input v-model="formFields[field.key]"
                      type="text"
                      class="input w-full text-sm"
                      :placeholder="field.placeholder ?? 'https://…  или  /storage/…'" />
                    <div v-if="formFields[field.key]"
                      class="mt-2 rounded-lg overflow-hidden border border-white/10 bg-black/20 flex items-center justify-center"
                      style="height:110px">
                      <img :src="formFields[field.key]" :alt="field.label"
                        class="max-h-full max-w-full object-contain"
                        @error="$event.target.style.display='none'" />
                    </div>
                  </div>

                  <!-- default: text input -->
                  <input v-else
                    v-model="formFields[field.key]"
                    type="text"
                    class="input w-full text-sm"
                    :placeholder="field.placeholder ?? ''" />
                </div>
              </template>

              <!-- No schema fields: show JSON tab hint -->
              <div v-else class="text-center py-8 text-gray-500">
                <div class="text-2xl mb-2">🔧</div>
                <p class="text-sm">Для этой секции нет предустановленных полей.</p>
                <button @click="editorTab = 'json'" class="mt-2 text-gold text-sm hover:underline">
                  Открыть JSON-редактор →
                </button>
              </div>
            </template>

            <!-- ── JSON TAB ── -->
            <template v-else>
              <div class="flex items-center justify-between mb-1">
                <label class="block text-xs text-gray-400">Контент (JSON)</label>
                <button @click="formatJson" class="text-xs text-gold hover:underline">Форматировать</button>
              </div>
              <textarea v-model="editForm.content" rows="16"
                class="input w-full font-mono text-xs resize-none leading-relaxed"
                spellcheck="false"
                placeholder='{ "title": "Заголовок", "text": "Описание" }'></textarea>
              <p v-if="jsonError" class="text-red-400 text-xs mt-1">⚠ {{ jsonError }}</p>
            </template>

            <!-- Active toggle -->
            <div class="flex items-center gap-3 pt-2 border-t border-white/5">
              <label class="toggle">
                <input type="checkbox" v-model="editForm.is_active" class="sr-only" />
                <div class="toggle-track" :class="editForm.is_active ? 'bg-green-500' : 'bg-white/10'"
                  @click="editForm.is_active = !editForm.is_active">
                  <div class="w-4 h-4 bg-white rounded-full shadow transition-all"
                    :class="editForm.is_active ? 'translate-x-5' : 'translate-x-1'"></div>
                </div>
              </label>
              <span class="text-sm text-gray-300">
                {{ editForm.is_active ? '✅ Секция активна (видна на сайте)' : '🙈 Секция скрыта' }}
              </span>
            </div>
          </div>

          <!-- Modal footer -->
          <div class="flex gap-3 pt-4 mt-4 border-t border-white/5 shrink-0">
            <button @click="saveSection" :disabled="saving" class="btn-primary flex-1">
              {{ saving ? 'Сохранение…' : '💾 Сохранить' }}
            </button>
            <button @click="editorOpen = false" class="btn-secondary px-6">Отмена</button>
          </div>
        </div>
      </div>

      <!-- Delete confirm -->
      <div v-if="destroyModal.open" class="modal-overlay" @click.self="destroyModal.open = false">
        <div class="modal-box max-w-sm text-center">
          <div class="text-4xl mb-3">🗑️</div>
          <h3 class="text-lg font-bold text-white mb-2">Удалить секцию?</h3>
          <p class="text-sm text-gray-400 mb-5">
            «{{ destroyModal.schema?.label ?? destroyModal.key }}» ({{ activeLocale.toUpperCase() }})
          </p>
          <p class="text-xs text-red-400/80 mb-5">Контент будет удалён без возможности восстановления</p>
          <div class="flex gap-3 justify-center">
            <button @click="destroyModal.open = false" class="btn-secondary">Отмена</button>
            <button @click="doDestroy" :disabled="saving" class="btn-danger">Удалить</button>
          </div>
        </div>
      </div>
    </Teleport>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, reactive, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import axios from 'axios'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  page:     { type: String,  required: true },
  label:    { type: String,  required: true },
  sections: { type: Object,  default: () => ({}) },
  locales:  { type: Array,   default: () => ['ru','en','tj'] },
})

// ── State ────────────────────────────────────────────────────────────────
const activeLocale    = ref('ru')
const activeSectionKey= ref(null)
const editorOpen      = ref(false)
const editorTab       = ref('form')
const isNew           = ref(false)
const editingKey      = ref('')
const editingSchema   = ref(null)
const saving          = ref(false)
const initing         = ref(false)
const jsonError       = ref('')

const editForm  = reactive({ section: '', content: '{}', is_active: true })
const formFields= reactive({})
const destroyModal = reactive({ open: false, key: '', id: null, schema: null })

// ── Computed ─────────────────────────────────────────────────────────────
const currentSections = computed(() => props.sections[activeLocale.value] ?? {})

const hasNoSections = computed(() =>
  Object.values(props.sections[activeLocale.value] ?? {}).every(s => !s.in_db)
)

// ── Helpers ───────────────────────────────────────────────────────────────
function localeLabel(loc) { return { ru:'Русский', en:'English', tj:'Тоҷикӣ' }[loc] ?? loc }
function localeFlag(loc)  { return { ru:'🇷🇺', en:'🇬🇧', tj:'🇹🇯' }[loc] ?? '🌐' }

function toggleSection(key) {
  activeSectionKey.value = activeSectionKey.value === key ? null : key
}

function previewFields(sectionData) {
  const content = sectionData.content ?? {}
  // Show first 3 non-empty fields
  return Object.fromEntries(
    Object.entries(content)
      .filter(([, v]) => v !== null && v !== '' && typeof v !== 'object')
      .slice(0, 3)
  )
}

// ── Editor open ───────────────────────────────────────────────────────────
function openEditor(key, data) {
  isNew.value       = false
  editingKey.value  = key
  editingSchema.value = data.schema ?? null
  editorTab.value   = data.schema?.fields?.length ? 'form' : 'json'
  jsonError.value   = ''

  editForm.section   = key
  editForm.is_active = data.is_active ?? true
  editForm.content   = JSON.stringify(data.content ?? {}, null, 2)

  // Fill form fields from content
  Object.keys(formFields).forEach(k => delete formFields[k])
  if (data.schema?.fields) {
    data.schema.fields.forEach(f => {
      formFields[f.key] = (data.content ?? {})[f.key] ?? (data.schema?.default?.[f.key] ?? '')
    })
  }

  editorOpen.value = true
}

function openNewSection() {
  isNew.value         = true
  editingKey.value    = ''
  editingSchema.value = null
  editorTab.value     = 'json'
  jsonError.value     = ''

  editForm.section   = ''
  editForm.is_active = true
  editForm.content   = '{\n  \n}'

  Object.keys(formFields).forEach(k => delete formFields[k])
  editorOpen.value = true
}

// ── Sync form ↔ JSON when switching tabs ─────────────────────────────────
watch(editorTab, (tab) => {
  if (tab === 'json' && editingSchema.value?.fields?.length) {
    // Sync form fields → JSON
    try {
      const existing = JSON.parse(editForm.content)
      editingSchema.value.fields.forEach(f => {
        if (formFields[f.key] !== undefined) existing[f.key] = formFields[f.key]
      })
      editForm.content = JSON.stringify(existing, null, 2)
    } catch {}
  }
  if (tab === 'form' && editingSchema.value?.fields?.length) {
    // Sync JSON → form fields
    try {
      const parsed = JSON.parse(editForm.content)
      editingSchema.value.fields.forEach(f => {
        formFields[f.key] = parsed[f.key] ?? formFields[f.key] ?? ''
      })
    } catch {}
  }
})

// ── Copy from RU ──────────────────────────────────────────────────────────
const ruSectionContent = computed(() => {
  if (!editingKey.value) return null
  const ruData = props.sections['ru']?.[editingKey.value]
  return (ruData?.in_db && ruData?.content) ? ruData.content : null
})

function copyFromRu() {
  const content = ruSectionContent.value
  if (!content) return
  const json = JSON.stringify(content, null, 2)
  editForm.content = json
  if (editingSchema.value?.fields) {
    editingSchema.value.fields.forEach(f => {
      formFields[f.key] = content[f.key] ?? ''
    })
  }
}

// ── Format JSON ───────────────────────────────────────────────────────────
function formatJson() {
  try {
    editForm.content = JSON.stringify(JSON.parse(editForm.content), null, 2)
    jsonError.value = ''
  } catch {
    jsonError.value = 'Невалидный JSON — проверьте синтаксис'
  }
}

// ── Save ──────────────────────────────────────────────────────────────────
function saveSection() {
  let parsed

  if (editorTab.value === 'form' && editingSchema.value?.fields?.length) {
    // Build content from form fields
    parsed = {}
    editingSchema.value.fields.forEach(f => { parsed[f.key] = formFields[f.key] ?? '' })
    // Merge with existing JSON (to keep extra keys)
    try {
      const existing = JSON.parse(editForm.content)
      parsed = { ...existing, ...parsed }
    } catch {}
    jsonError.value = ''
  } else {
    try { parsed = JSON.parse(editForm.content); jsonError.value = '' }
    catch { jsonError.value = 'Невалидный JSON — исправьте синтаксис'; return }
  }

  saving.value = true
  router.post(route('admin.cms.section.upsert', props.page), {
    section:   editForm.section || editingKey.value,
    locale:    activeLocale.value,
    content:   parsed,
    is_active: editForm.is_active,
  }, {
    preserveState: true,
    onSuccess: () => { editorOpen.value = false },
    onFinish:  () => { saving.value = false },
  })
}

// ── Toggle active (inline) ────────────────────────────────────────────────
async function toggleActive(key, data) {
  const newState = !data.is_active
  try {
    await axios.patch(route('admin.cms.section.toggle', props.page), {
      section:   key,
      locale:    activeLocale.value,
      is_active: newState,
    })
    router.reload({ only: ['sections'] })
  } catch (e) {
    console.error(e)
  }
}

// ── Init from template ────────────────────────────────────────────────────
function initFromTemplate() {
  initing.value = true
  router.post(route('admin.cms.page.init', props.page), {
    locale: activeLocale.value,
  }, {
    preserveState: true,
    onFinish: () => { initing.value = false },
  })
}

// ── Destroy ───────────────────────────────────────────────────────────────
function confirmDestroy(key, data) {
  destroyModal.key    = key
  destroyModal.id     = data.id
  destroyModal.schema = data.schema
  destroyModal.open   = true
}

function doDestroy() {
  if (!destroyModal.id) return
  saving.value = true
  router.delete(route('admin.cms.section.destroy', destroyModal.id), {
    preserveState: true,
    onSuccess: () => { destroyModal.open = false },
    onFinish:  () => { saving.value = false },
  })
}
</script>

<style scoped>
.toggle-track {
  width: 2.5rem;
  height: 1.5rem;
  border-radius: 999px;
  cursor: pointer;
  display: flex;
  align-items: center;
  padding: 0.125rem;
  transition: background 0.2s;
}
</style>
