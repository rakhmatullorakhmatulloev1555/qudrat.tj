<template>
  <AdminLayout page-title="Конструктор комплекса" page-subtitle="Генплан → Корпуса → Этажи → Контуры квартир">
    <Head title="Конструктор комплекса" />

    <!-- Project selector -->
    <div class="flex flex-wrap items-center gap-4 mb-8">
      <label class="text-slate-400 text-xs uppercase tracking-wider">Проект:</label>
      <select :value="project?.id" @change="switchProject($event.target.value)"
        class="px-4 py-2.5 text-sm rounded-xl text-white outline-none min-w-[260px]"
        style="background:#1E293B; border:1px solid rgba(255,255,255,0.08)">
        <option v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option>
      </select>
      <a v-if="project" :href="route('complex.master', project.slug)" target="_blank"
        class="px-4 py-2.5 rounded-xl text-xs font-medium text-gold border border-gold/30 hover:bg-gold/10 transition-all">
        Открыть на сайте →
      </a>
    </div>

    <div v-if="project" class="space-y-8">

      <!-- ═══ 1. ГЕНПЛАН ═══ -->
      <section class="rounded-2xl border border-white/6 p-6" style="background:#1E293B">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
          <h2 class="text-white font-bold">1. Генеральный план</h2>
          <div class="flex gap-2">
            <input ref="mpInput" type="file" accept="image/*" class="hidden" @change="uploadMasterplan"/>
            <button @click="$refs.mpInput.click()"
              class="px-4 py-2 rounded-lg text-xs font-medium text-slate-300 border border-white/10 hover:border-white/25 transition-all">
              {{ project.masterplan_image ? 'Заменить изображение' : 'Загрузить генплан' }}
            </button>
            <button v-if="project.masterplan_image" @click="openPoiEditor()"
              class="px-4 py-2 rounded-lg text-xs font-medium text-gold border border-gold/30 hover:bg-gold/10 transition-all">
              Зоны генплана ({{ pois.length }})
            </button>
          </div>
        </div>
        <div v-if="project.masterplan_image" class="rounded-xl overflow-hidden border border-white/8 max-w-3xl">
          <img :src="project.masterplan_image" class="w-full h-auto" alt="Генплан"/>
        </div>
        <p v-else class="text-slate-500 text-sm">Загрузите изображение генплана — на нём вы обведёте корпуса и зоны.</p>
      </section>

      <!-- ═══ 2. КОРПУСА ═══ -->
      <section class="rounded-2xl border border-white/6 p-6" style="background:#1E293B">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
          <h2 class="text-white font-bold">2. Корпуса</h2>
          <form @submit.prevent="createBlock" class="flex gap-2 items-center flex-wrap">
            <input v-model="newBlock.name" placeholder="Название (Блок A)" class="field" style="width:170px" required/>
            <input v-model.number="newBlock.floors_total" type="number" min="1" max="200" placeholder="Этажей" class="field" style="width:90px" required/>
            <button type="submit" class="px-4 py-2 rounded-lg text-xs font-semibold text-[#0F172A]" style="background:#C9A96E">
              + Добавить корпус
            </button>
          </form>
        </div>

        <div class="space-y-4">
          <div v-for="b in blocks" :key="b.id" class="rounded-xl border border-white/8 overflow-hidden" style="background:#0F172A">
            <!-- Block header -->
            <div class="flex flex-wrap items-center gap-3 px-5 py-4">
              <button @click="expanded = expanded === b.id ? null : b.id" class="flex items-center gap-3 mr-auto group">
                <svg class="w-4 h-4 text-slate-500 transition-transform" :class="expanded === b.id ? 'rotate-90' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                <span class="text-white font-bold group-hover:text-gold transition-colors">{{ b.name }}</span>
                <span class="text-slate-500 text-xs">{{ b.floors_total }} эт. · {{ b.apartments_count }} кв. · /{{ b.slug }}</span>
              </button>
              <span v-if="!b.is_published" class="text-[10px] font-bold px-2 py-0.5 rounded-full text-slate-400 bg-slate-700/40 uppercase">Скрыт</span>
              <button @click="openBlockPolygon(b)"
                class="px-3 py-1.5 rounded-lg text-xs border transition-all"
                :class="(b.masterplan_polygon || []).length ? 'text-emerald-400 border-emerald-500/25' : 'text-amber-400 border-amber-500/25'"
                :disabled="!project.masterplan_image"
                :title="!project.masterplan_image ? 'Сначала загрузите генплан' : ''">
                {{ (b.masterplan_polygon || []).length ? '✓ Полигон на генплане' : 'Обвести на генплане' }}
              </button>
              <button @click="deleteBlock(b)" class="p-2 rounded-lg text-red-400/60 hover:text-red-400 hover:bg-red-500/10 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
              </button>
            </div>

            <!-- Block detail -->
            <div v-if="expanded === b.id" class="border-t border-white/6 px-5 py-5 space-y-5">
              <!-- Settings row -->
              <div class="flex flex-wrap gap-3 items-end">
                <div><label class="lbl">Название</label><input v-model="b.name" class="field" style="width:160px"/></div>
                <div><label class="lbl">Этажей</label><input v-model.number="b.floors_total" type="number" min="1" class="field" style="width:80px"/></div>
                <div class="flex-1 min-w-[200px]"><label class="lbl">Описание</label><input v-model="b.description" class="field w-full"/></div>
                <label class="flex items-center gap-2 pb-2 cursor-pointer">
                  <input type="checkbox" v-model="b.is_published" class="w-4 h-4 accent-[#C9A96E]"/>
                  <span class="text-xs text-slate-300">Опубликован</span>
                </label>
                <button @click="saveBlock(b)" class="px-4 py-2 rounded-lg text-xs font-semibold text-[#0F172A]" style="background:#C9A96E">Сохранить</button>
              </div>

              <!-- Facade -->
              <div class="flex flex-wrap items-center gap-4">
                <div class="w-40 h-24 rounded-lg overflow-hidden border border-white/10 flex-shrink-0" style="background:#1E293B">
                  <img v-if="b.facade_image" :src="b.facade_image" class="w-full h-full object-cover"/>
                  <div v-else class="w-full h-full flex items-center justify-center text-slate-600 text-[10px]">Нет фасада</div>
                </div>
                <div>
                  <input :ref="el => facadeInputs[b.id] = el" type="file" accept="image/*" class="hidden" @change="e => uploadFacade(b, e)"/>
                  <button @click="facadeInputs[b.id].click()"
                    class="px-4 py-2 rounded-lg text-xs text-slate-300 border border-white/10 hover:border-white/25 transition-all">
                    {{ b.facade_image ? 'Заменить фасад' : 'Загрузить фасад' }}
                  </button>
                  <p class="text-slate-600 text-[11px] mt-1.5">Фасад — изображение, на котором обводятся этажи.</p>
                </div>
              </div>

              <!-- Floors -->
              <div>
                <div class="flex items-center justify-between mb-3">
                  <h4 class="text-slate-300 text-xs font-bold uppercase tracking-wider">Этажи ({{ b.floors.length }} / {{ b.floors_total }})</h4>
                  <button v-if="b.floors.length < b.floors_total" @click="generateFloors(b)"
                    class="px-3 py-1.5 rounded-lg text-xs text-gold border border-gold/30 hover:bg-gold/10 transition-all">
                    Создать этажи 1–{{ b.floors_total }}
                  </button>
                </div>
                <div class="grid gap-1.5" style="grid-template-columns:repeat(auto-fill,minmax(300px,1fr))">
                  <div v-for="f in [...b.floors].reverse()" :key="f.id"
                    class="flex items-center gap-3 rounded-lg border border-white/6 px-3 py-2" style="background:#1E293B">
                    <span class="text-gold font-black w-7 text-right">{{ f.number }}</span>
                    <span class="text-[10px]" :class="f.plan_image ? 'text-emerald-400' : 'text-slate-600'">
                      {{ f.plan_image ? '✓ план' : 'нет плана' }}
                    </span>
                    <span class="text-[10px]" :class="(f.facade_polygon || []).length ? 'text-emerald-400' : 'text-slate-600'">
                      {{ (f.facade_polygon || []).length ? '✓ фасад' : 'нет полосы' }}
                    </span>
                    <div class="ml-auto flex gap-1">
                      <input :ref="el => planInputs[f.id] = el" type="file" accept="image/*" class="hidden" @change="e => uploadFloorPlan(f, e)"/>
                      <button @click="planInputs[f.id].click()" class="px-2 py-1 rounded text-[10px] text-slate-300 border border-white/10 hover:border-white/25 transition-all">План</button>
                      <button @click="openFloorPolygon(b, f)" :disabled="!b.facade_image"
                        class="px-2 py-1 rounded text-[10px] text-slate-300 border border-white/10 hover:border-white/25 disabled:opacity-30 transition-all">На фасаде</button>
                      <button @click="openAptEditor(b, f)" :disabled="!f.plan_image"
                        class="px-2 py-1 rounded text-[10px] text-gold border border-gold/25 hover:bg-gold/10 disabled:opacity-30 transition-all">Квартиры</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <p v-if="!blocks.length" class="text-slate-500 text-sm py-6 text-center">Корпусов пока нет — добавьте первый.</p>
        </div>
      </section>
    </div>

    <!-- ═══ MODAL: Полигон-редактор ═══ -->
    <Teleport to="body">
      <div v-if="editor.open" class="fixed inset-0 z-50 flex items-start justify-center p-4 overflow-y-auto"
        style="background:rgba(0,0,0,0.75); backdrop-filter:blur(4px)" @click.self="editor.open = false">
        <div class="w-full max-w-4xl rounded-2xl border border-white/8 shadow-2xl my-6 p-6" style="background:#1E293B">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-white font-semibold">{{ editor.title }}</h3>
            <button @click="editor.open = false" class="text-slate-400 hover:text-white transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
          </div>

          <!-- POI panel -->
          <div v-if="editor.mode === 'poi'" class="flex flex-wrap gap-2 mb-4 items-center">
            <select v-model="poiDraft.type" class="field" style="width:180px">
              <option v-for="(label, key) in POI_TYPES" :key="key" :value="key">{{ label }}</option>
            </select>
            <input v-model="poiDraft.label" placeholder="Подпись (напр. «Бассейн»)" class="field" style="width:220px"/>
            <span class="text-slate-500 text-xs">— выберите тип, нарисуйте контур, нажмите «Сохранить контур»</span>
            <div class="w-full flex flex-wrap gap-1.5">
              <span v-for="(p, i) in pois" :key="i"
                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] border border-white/10 text-slate-300">
                {{ POI_TYPES[p.type] }}{{ p.label ? ` · ${p.label}` : '' }}
                <button @click="removePoi(i)" class="text-red-400/70 hover:text-red-400">✕</button>
              </span>
            </div>
          </div>

          <!-- Apartment panel -->
          <div v-if="editor.mode === 'apt'" class="mb-4">
            <div class="flex flex-wrap gap-1.5">
              <button v-for="a in editor.floorApts" :key="a.id" @click="selectApt(a)"
                class="px-3 py-1.5 rounded-lg text-xs border transition-all"
                :class="editor.currentApt?.id === a.id
                  ? 'text-[#0F172A] font-bold border-transparent'
                  : (a.polygon || []).length ? 'text-emerald-400 border-emerald-500/25' : 'text-slate-300 border-white/10 hover:border-white/25'"
                :style="editor.currentApt?.id === a.id ? 'background:#C9A96E' : ''">
                № {{ a.number }}{{ (a.polygon || []).length ? ' ✓' : '' }}
              </button>
            </div>
            <p v-if="!editor.floorApts.length" class="text-amber-400/80 text-xs mt-2">
              На этом этаже нет квартир проекта. Добавьте их в разделе «Квартиры» с номером этажа {{ editor.floor?.number }}.
            </p>
          </div>

          <PolygonEditor
            v-if="editor.image"
            :key="editorKey"
            :image="editor.image"
            v-model="editor.value"
            :context="editor.context"
            @save="editor.onSave"/>
        </div>
      </div>
    </Teleport>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PolygonEditor from '@/Components/Complex/PolygonEditor.vue';

const props = defineProps({
  projects:   { type: Array, default: () => [] },
  project:    { type: Object, default: null },
  blocks:     { type: Array, default: () => [] },
  apartments: { type: Array, default: () => [] },
});

const POI_TYPES = {
  green: 'Зелёная зона', road: 'Дороги', parking: 'Парковка', playground: 'Детская площадка',
  pool: 'Бассейн', leisure: 'Зона отдыха', commercial: 'Коммерция',
};

const expanded = ref(null);
const facadeInputs = reactive({});
const planInputs = reactive({});
const newBlock = reactive({ name: '', floors_total: 9 });
const pois = ref([...(props.project?.masterplan_pois || [])]);
const poiDraft = reactive({ type: 'green', label: '' });

const editor = reactive({
  open: false, mode: '', title: '', image: null, value: [], context: [],
  onSave: () => {}, floorApts: [], currentApt: null, floor: null, block: null,
});
const editorKey = computed(() => `${editor.mode}-${editor.currentApt?.id ?? editor.floor?.id ?? editor.block?.id ?? 'x'}`);

const opts = { preserveScroll: true, preserveState: false };

function switchProject(id) {
  router.get(route('admin.complex.index'), { project: id });
}

// ── Генплан ──

function uploadMasterplan(e) {
  const f = e.target.files[0];
  if (!f) return;
  router.post(route('admin.complex.masterplan', props.project.id), { image: f }, { ...opts, forceFormData: true });
}

function openPoiEditor() {
  Object.assign(editor, {
    open: true, mode: 'poi', title: 'Зоны генплана (дороги, парковки, бассейны…)',
    image: props.project.masterplan_image, value: [],
    context: [
      ...props.blocks.filter(b => (b.masterplan_polygon || []).length).map(b => ({ label: b.name, polygon: b.masterplan_polygon })),
      ...pois.value.map(p => ({ label: p.label || POI_TYPES[p.type], polygon: p.polygon })),
    ],
    onSave: (points) => {
      pois.value.push({ type: poiDraft.type, label: poiDraft.label, polygon: points });
      poiDraft.label = '';
      savePois();
    },
  });
}

function removePoi(i) {
  pois.value.splice(i, 1);
  savePois();
}

function savePois() {
  router.post(route('admin.complex.masterplan', props.project.id), { pois: pois.value }, { preserveScroll: true, preserveState: true, onSuccess: () => { editor.open = false } });
}

// ── Корпуса ──

function createBlock() {
  router.post(route('admin.complex.blocks.store', props.project.id), { ...newBlock }, { ...opts, onSuccess: () => { newBlock.name = ''; } });
}

function saveBlock(b) {
  router.put(route('admin.complex.blocks.update', b.id), {
    name: b.name, floors_total: b.floors_total, description: b.description, is_published: b.is_published,
  }, opts);
}

function deleteBlock(b) {
  if (!confirm(`Удалить корпус «${b.name}» со всеми этажами? Квартиры останутся, но потеряют привязку.`)) return;
  router.delete(route('admin.complex.blocks.destroy', b.id), opts);
}

function uploadFacade(b, e) {
  const f = e.target.files[0];
  if (!f) return;
  router.post(route('admin.complex.blocks.update', b.id), { _method: 'PUT', facade: f }, { ...opts, forceFormData: true });
}

function openBlockPolygon(b) {
  Object.assign(editor, {
    open: true, mode: 'block', block: b, title: `${b.name} — полигон на генплане`,
    image: props.project.masterplan_image, value: [...(b.masterplan_polygon || [])],
    context: props.blocks.filter(x => x.id !== b.id && (x.masterplan_polygon || []).length)
      .map(x => ({ label: x.name, polygon: x.masterplan_polygon })),
    onSave: (points) => {
      router.put(route('admin.complex.blocks.update', b.id), { masterplan_polygon: points }, { ...opts, onSuccess: () => { editor.open = false } });
    },
  });
}

// ── Этажи ──

function generateFloors(b) {
  router.post(route('admin.complex.floors.generate', b.id), {}, opts);
}

function uploadFloorPlan(f, e) {
  const file = e.target.files[0];
  if (!file) return;
  router.post(route('admin.complex.floors.update', f.id), { _method: 'PUT', plan: file }, { ...opts, forceFormData: true });
}

function openFloorPolygon(b, f) {
  Object.assign(editor, {
    open: true, mode: 'floor', floor: f, title: `${b.name} — этаж ${f.number} на фасаде`,
    image: b.facade_image, value: [...(f.facade_polygon || [])],
    context: b.floors.filter(x => x.id !== f.id && (x.facade_polygon || []).length)
      .map(x => ({ label: `Этаж ${x.number}`, polygon: x.facade_polygon })),
    onSave: (points) => {
      router.put(route('admin.complex.floors.update', f.id), { facade_polygon: points }, { ...opts, onSuccess: () => { editor.open = false } });
    },
  });
}

// ── Контуры квартир ──

function openAptEditor(b, f) {
  const floorApts = props.apartments.filter(a => a.floor === f.number && (!a.block_id || a.block_id === b.id));
  Object.assign(editor, {
    open: true, mode: 'apt', block: b, floor: f,
    title: `${b.name} — этаж ${f.number}: контуры квартир`,
    image: f.plan_image, floorApts, currentApt: null, value: [], context: [],
  });
  if (floorApts.length) selectApt(floorApts[0]);
}

function selectApt(a) {
  editor.currentApt = a;
  editor.value = [...(a.polygon || [])];
  editor.context = editor.floorApts.filter(x => x.id !== a.id && (x.polygon || []).length)
    .map(x => ({ label: `№${x.number}`, polygon: x.polygon }));
  editor.onSave = (points) => {
    router.put(route('admin.complex.apartments.shape', a.id), {
      block_id: editor.block.id, polygon: points,
    }, {
      preserveScroll: true, preserveState: true,
      onSuccess: () => {
        a.polygon = [...points];
        a.block_id = editor.block.id;
        editor.context = editor.floorApts.filter(x => x.id !== a.id && (x.polygon || []).length)
          .map(x => ({ label: `№${x.number}`, polygon: x.polygon }));
      },
    });
  };
}
</script>

<style scoped>
.field {
  background: #0F172A;
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 0.6rem;
  padding: 0.5rem 0.8rem;
  font-size: 0.8rem;
  color: #E2E8F0;
  outline: none;
}
.field:focus { border-color: rgba(201,169,110,0.5); }
.lbl { display:block; font-size:0.65rem; color:#94A3B8; margin-bottom:0.3rem; }
</style>
