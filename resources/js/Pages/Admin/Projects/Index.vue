<template>
  <AdminLayout page-title="Проекты" page-subtitle="Управление объектами недвижимости">
    <Head title="Проекты" />

    <div class="flex items-center justify-between mb-6">
      <div class="flex flex-wrap items-center gap-3">
        <select v-model="statusFilter" @change="doFilter"
          class="px-4 py-2.5 text-sm rounded-xl text-slate-300 outline-none w-full sm:w-auto"
          style="background:#1E293B; border:1px solid rgba(255,255,255,0.08)">
          <option value="">Все статусы</option>
          <option value="planned">Планируется</option>
          <option value="under_construction">Строится</option>
          <option value="on_sale">В продаже</option>
          <option value="completed">Сдан</option>
        </select>
      </div>
      <button @click="openModal()" class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm text-[#0F172A] flex-shrink-0" style="background:#C9A96E">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Добавить проект
      </button>
    </div>

    <!-- Cards Grid -->
    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5">
      <div v-if="projects.data.length === 0" class="col-span-3 text-center py-16 text-slate-500">Проектов пока нет</div>
      <div v-for="project in projects.data" :key="project.id"
        class="rounded-xl border border-white/6 p-5 hover:border-white/12 transition-colors" style="background:#1E293B">
        <div class="flex items-start justify-between mb-3">
          <div>
            <h3 class="text-white font-semibold">{{ project.name }}</h3>
            <p class="text-slate-500 text-xs mt-0.5">{{ project.city }} · {{ classLabel(project.class) }}</p>
          </div>
          <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full" :class="statusDot(project.status)"></span>
            <span class="text-[11px] text-slate-400">{{ projectStatusLabel(project.status) }}</span>
          </div>
        </div>

        <div class="grid grid-cols-3 gap-2 mb-4">
          <div class="rounded-lg p-2 text-center" style="background:#0F172A">
            <div class="text-white font-bold text-sm">{{ project.floors_count || '—' }}</div>
            <div class="text-slate-500 text-[10px]">Этажей</div>
          </div>
          <div class="rounded-lg p-2 text-center" style="background:#0F172A">
            <div class="text-white font-bold text-sm">{{ project.apartments_count }}</div>
            <div class="text-slate-500 text-[10px]">Квартир</div>
          </div>
          <div class="rounded-lg p-2 text-center" style="background:#0F172A">
            <div class="text-white font-bold text-sm">{{ project.completion_year || '—' }}</div>
            <div class="text-slate-500 text-[10px]">Год сдачи</div>
          </div>
        </div>

        <div v-if="project.price_from" class="text-gold text-sm mb-3">
          от {{ Number(project.price_from).toLocaleString() }} {{ project.currency }}
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full" :class="project.is_published ? 'bg-emerald-400' : 'bg-slate-600'"></span>
            <span class="text-xs text-slate-500">{{ project.is_published ? 'Опубликован' : 'Черновик' }}</span>
          </div>
          <div class="flex gap-2">
            <button @click="openModal(project)" class="p-1.5 rounded-lg text-slate-400 hover:text-gold hover:bg-gold/10 transition-all">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
            </button>
            <button @click="deleteProject(project)" class="p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-all">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <Teleport to="body">
      <Transition enter-active-class="transition-all duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100"
        leave-active-class="transition-all duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" style="background:rgba(0,0,0,0.7)">
          <div class="w-full max-w-xl rounded-2xl p-6 shadow-2xl my-auto" style="background:#1E293B; border:1px solid rgba(255,255,255,0.1)">
            <div class="flex items-center justify-between mb-5">
              <h3 class="font-bold text-white text-lg">{{ editingProject ? 'Редактировать проект' : 'Новый проект' }}</h3>
              <button @click="modal = false" class="text-slate-400 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
              </button>
            </div>
            <form @submit.prevent="save" class="space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="col-span-1 sm:col-span-2">
                  <label class="form-label">Название проекта *</label>
                  <input v-model="form.name" type="text" class="form-input" required />
                </div>
                <div>
                  <label class="form-label">Тип</label>
                  <select v-model="form.type" class="form-input">
                    <option value="residential">Жилой</option>
                    <option value="commercial">Коммерческий</option>
                    <option value="mixed">Смешанный</option>
                  </select>
                </div>
                <div>
                  <label class="form-label">Класс</label>
                  <select v-model="form.class" class="form-input">
                    <option value="economy">Эконом</option>
                    <option value="comfort">Комфорт</option>
                    <option value="business">Бизнес</option>
                    <option value="premium">Премиум</option>
                  </select>
                </div>
                <div>
                  <label class="form-label">Статус</label>
                  <select v-model="form.status" class="form-input">
                    <option value="planned">Планируется</option>
                    <option value="under_construction">Строится</option>
                    <option value="on_sale">В продаже</option>
                    <option value="completed">Сдан</option>
                  </select>
                </div>
                <div>
                  <label class="form-label">Город</label>
                  <input v-model="form.city" type="text" class="form-input" />
                </div>
                <div class="col-span-1 sm:col-span-2">
                  <label class="form-label">Адрес</label>
                  <input v-model="form.address" type="text" class="form-input" />
                </div>
                <div>
                  <label class="form-label">Цена от</label>
                  <input v-model="form.price_from" type="number" class="form-input" />
                </div>
                <div>
                  <label class="form-label">Цена до</label>
                  <input v-model="form.price_to" type="number" class="form-input" />
                </div>
                <div>
                  <label class="form-label">Валюта</label>
                  <select v-model="form.currency" class="form-input">
                    <option value="USD">USD</option>
                    <option value="TJS">TJS</option>
                    <option value="RUB">RUB</option>
                  </select>
                </div>
                <div>
                  <label class="form-label">Год сдачи</label>
                  <input v-model="form.completion_year" type="number" class="form-input" />
                </div>
                <div>
                  <label class="form-label">Этажей</label>
                  <input v-model="form.floors_count" type="number" class="form-input" />
                </div>
                <div>
                  <label class="form-label">Квартир</label>
                  <input v-model="form.apartments_count" type="number" class="form-input" />
                </div>
                <div class="col-span-1 sm:col-span-2">
                  <label class="form-label">Описание</label>
                  <textarea v-model="form.description" rows="2" class="form-input"></textarea>
                </div>
                <div class="col-span-1 sm:col-span-2 flex items-center gap-3">
                  <input id="published" v-model="form.is_published" type="checkbox" class="w-4 h-4 rounded" />
                  <label for="published" class="text-sm text-slate-300 cursor-pointer">Опубликовать на сайте</label>
                </div>
              </div>
              <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="modal = false" class="px-5 py-2.5 rounded-xl text-sm text-slate-400 hover:text-white" style="background:rgba(255,255,255,0.05)">Отмена</button>
                <button type="submit" :disabled="form.processing" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-[#0F172A] disabled:opacity-60" style="background:#C9A96E">
                  {{ editingProject ? 'Сохранить' : 'Добавить' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ projects: Object, filters: Object });
const modal = ref(false);
const editingProject = ref(null);
const statusFilter = ref(props.filters?.status || '');

const form = useForm({ name:'', type:'residential', status:'planned', class:'premium', city:'Душанбе', address:'', description:'', price_from:'', price_to:'', currency:'USD', floors_count:'', apartments_count:'', completion_year:'', is_published:false });

function openModal(p = null) {
  editingProject.value = p;
  if (p) { Object.keys(form).forEach(k => { if (k in p) form[k] = p[k] ?? ''; }); form.is_published = !!p.is_published; }
  else { form.reset(); form.type='residential'; form.status='planned'; form.class='premium'; form.city='Душанбе'; form.currency='USD'; form.is_published=false; }
  modal.value = true;
}
function save() {
  const opts = { onSuccess: () => { modal.value = false; } };
  editingProject.value ? form.put(route('admin.projects.update', editingProject.value.id), opts) : form.post(route('admin.projects.store'), opts);
}
function deleteProject(p) {
  if (confirm(`Удалить проект "${p.name}"?`)) router.delete(route('admin.projects.destroy', p.id));
}
function doFilter() { router.get(route('admin.projects.index'), { status: statusFilter.value }, { preserveState: true }); }
function projectStatusLabel(s) { return { planned:'Планируется', under_construction:'Строится', on_sale:'В продаже', completed:'Сдан' }[s]||s; }
function classLabel(c) { return { economy:'Эконом', comfort:'Комфорт', business:'Бизнес', premium:'Премиум' }[c]||c; }
function statusDot(s) { return { planned:'bg-slate-400', under_construction:'bg-amber-400', on_sale:'bg-blue-400', completed:'bg-emerald-400' }[s]||'bg-slate-400'; }
</script>

<style scoped>
.form-label { display:block; font-size:0.75rem; font-weight:500; color:#94A3B8; margin-bottom:0.375rem; }
.form-input { display:block; width:100%; padding:0.625rem 0.75rem; border-radius:0.75rem; font-size:0.875rem; color:#fff; background:#0F172A; border:1px solid rgba(255,255,255,0.08); outline:none; transition:border-color 0.2s; }
.form-input:focus { border-color:rgba(201,169,110,0.5); }
</style>
