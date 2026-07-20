<template>
  <AdminLayout page-title="Квартиры" page-subtitle="Каталог и статусы квартир">
    <Head title="Квартиры" />

    <!-- Toolbar -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
      <div class="flex flex-wrap items-center gap-2 flex-1">
        <select v-model="projectFilter" @change="doFilter"
          class="px-4 py-2.5 text-sm rounded-xl text-slate-300 outline-none w-full sm:w-auto"
          style="background:#1E293B; border:1px solid rgba(255,255,255,0.08)">
          <option value="">Все проекты</option>
          <option v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option>
        </select>
        <select v-model="statusFilter" @change="doFilter"
          class="px-4 py-2.5 text-sm rounded-xl text-slate-300 outline-none w-full sm:w-auto"
          style="background:#1E293B; border:1px solid rgba(255,255,255,0.08)">
          <option value="">Все статусы</option>
          <option value="available">Свободно</option>
          <option value="reserved">Забронировано</option>
          <option value="sold">Продано</option>
          <option value="soon">Скоро</option>
        </select>
        <select v-model="roomsFilter" @change="doFilter"
          class="px-4 py-2.5 text-sm rounded-xl text-slate-300 outline-none w-full sm:w-auto"
          style="background:#1E293B; border:1px solid rgba(255,255,255,0.08)">
          <option value="">Все комнаты</option>
          <option value="1">1 комн.</option>
          <option value="2">2 комн.</option>
          <option value="3">3 комн.</option>
          <option value="4">4+ комн.</option>
        </select>
      </div>
      <div class="flex gap-2">
        <button @click="importModal = true" class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-sm text-slate-300 border border-white/10 hover:border-white/25 transition-all">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
          Импорт
        </button>
        <button @click="openModal()" class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm text-[#0F172A]" style="background:#C9A96E">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
          Добавить квартиру
        </button>
      </div>
    </div>

    <!-- Bulk bar -->
    <div v-if="selected.length" class="flex flex-wrap items-center gap-3 mb-5 rounded-xl px-5 py-3.5 border border-gold/25" style="background:rgba(201,169,110,0.06)">
      <span class="text-gold text-sm font-bold">Выбрано: {{ selected.length }}</span>
      <select v-model="bulk.status" class="bulk-field">
        <option value="">Статус — без изменений</option>
        <option value="available">Свободно</option>
        <option value="reserved">Бронь</option>
        <option value="sold">Продано</option>
        <option value="soon">Скоро</option>
      </select>
      <input v-model="bulk.price" type="number" min="0" placeholder="Новая цена" class="bulk-field" style="width:120px"/>
      <input v-model="bulk.discount_percent" type="number" min="0" max="90" placeholder="Скидка %" class="bulk-field" style="width:100px"/>
      <input v-model="bulk.floor" type="number" min="1" placeholder="Этаж" class="bulk-field" style="width:80px"/>
      <button @click="applyBulk" :disabled="bulkProcessing"
        class="px-5 py-2 rounded-lg text-xs font-bold text-[#0F172A] disabled:opacity-50" style="background:#C9A96E">
        {{ bulkProcessing ? 'Применяем…' : 'Применить' }}
      </button>
      <button @click="selected = []" class="text-slate-400 hover:text-white text-xs transition-colors">Сбросить</button>
    </div>

    <!-- Summary chips -->
    <div class="flex flex-wrap gap-3 mb-5">
      <div class="px-4 py-2 rounded-xl text-sm" style="background:#1E293B; border:1px solid rgba(255,255,255,0.06)">
        <span class="text-slate-400">Всего: </span><span class="text-white font-bold">{{ apartments.total }}</span>
      </div>
      <div class="px-4 py-2 rounded-xl text-sm" style="background:#1E293B; border:1px solid rgba(16,185,129,0.2)">
        <span class="text-emerald-400 font-bold">{{ availableCount }}</span><span class="text-slate-400"> свободно</span>
      </div>
      <div class="px-4 py-2 rounded-xl text-sm" style="background:#1E293B; border:1px solid rgba(245,158,11,0.2)">
        <span class="text-amber-400 font-bold">{{ reservedCount }}</span><span class="text-slate-400"> бронь</span>
      </div>
      <div class="px-4 py-2 rounded-xl text-sm" style="background:#1E293B; border:1px solid rgba(201,169,110,0.2)">
        <span class="text-gold font-bold">{{ soldCount }}</span><span class="text-slate-400"> продано</span>
      </div>
    </div>

    <!-- Mobile cards (< md) -->
    <div class="md:hidden space-y-2 mb-4">
      <div v-if="apartments.data.length === 0" class="rounded-xl p-8 text-center text-slate-500 text-sm" style="background:#1E293B; border:1px solid rgba(255,255,255,0.06)">
        Квартир пока нет
      </div>
      <div v-for="apt in apartments.data" :key="apt.id"
        class="rounded-xl p-4" style="background:#1E293B; border:1px solid rgba(255,255,255,0.06)">
        <div class="flex items-start justify-between gap-2 mb-2">
          <div>
            <div class="text-white font-bold text-sm">Кв. №{{ apt.number }}</div>
            <div class="text-slate-400 text-xs">{{ apt.project?.name }}</div>
          </div>
          <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold flex-shrink-0" :class="aptStatusClass(apt.status)">
            {{ aptStatusLabel(apt.status) }}
          </span>
        </div>
        <div class="grid grid-cols-3 gap-2 mb-3 text-center">
          <div class="rounded-lg py-2" style="background:rgba(255,255,255,0.04)">
            <div class="text-white text-sm font-semibold">{{ apt.rooms }}-комн.</div>
            <div class="text-slate-500 text-[10px]">Комнаты</div>
          </div>
          <div class="rounded-lg py-2" style="background:rgba(255,255,255,0.04)">
            <div class="text-white text-sm font-semibold">{{ apt.floor }} эт.</div>
            <div class="text-slate-500 text-[10px]">Этаж</div>
          </div>
          <div class="rounded-lg py-2" style="background:rgba(255,255,255,0.04)">
            <div class="text-white text-sm font-semibold">{{ apt.area }} м²</div>
            <div class="text-slate-500 text-[10px]">Площадь</div>
          </div>
        </div>
        <div class="text-gold font-bold mb-1">{{ Number(apt.price).toLocaleString() }} {{ apt.currency }}</div>
        <div v-if="apt.client" class="text-slate-400 text-xs mb-3">Клиент: {{ apt.client.name }}</div>
        <div class="flex items-center gap-2 pt-3 border-t border-white/5">
          <button @click="openPhotos(apt)"
            class="flex-1 py-1.5 text-xs rounded-lg text-slate-300 hover:text-gold hover:bg-gold/10 transition-colors text-center">
            🖼 Фото<span v-if="apt.images?.length" class="text-gold font-bold"> {{ apt.images.length }}</span>
          </button>
          <button @click="openModal(apt)"
            class="flex-1 py-1.5 text-xs rounded-lg text-slate-300 hover:text-white hover:bg-white/10 transition-colors text-center">
            ✏️ Изменить
          </button>
          <button @click="deleteApt(apt)"
            class="flex-1 py-1.5 text-xs rounded-lg text-red-400 hover:bg-red-500/10 transition-colors text-center">
            🗑️ Удалить
          </button>
        </div>
      </div>
    </div>

    <!-- Desktop table (md+) -->
    <div class="hidden md:block rounded-xl border border-white/6 overflow-hidden" style="background:#1E293B">
      <table class="w-full">
        <thead>
          <tr class="border-b border-white/6">
            <th class="px-4 py-3 w-10">
              <input type="checkbox" :checked="allSelected" @change="toggleAll" class="w-4 h-4 accent-[#C9A96E] cursor-pointer"/>
            </th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">№ кв.</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500 hidden lg:table-cell">Проект</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500 hidden lg:table-cell">Этаж</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Комнаты</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Площадь</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Цена</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Статус</th>
            <th class="text-left px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500 hidden xl:table-cell">Клиент</th>
            <th class="text-right px-5 py-3 text-[11px] uppercase tracking-wider text-slate-500">Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="apartments.data.length === 0">
            <td colspan="10" class="px-5 py-12 text-center text-slate-500 text-sm">Квартир пока нет</td>
          </tr>
          <tr v-for="apt in apartments.data" :key="apt.id"
            class="border-b border-white/4 hover:bg-white/3 transition-colors"
            :class="selected.includes(apt.id) ? 'bg-gold/5' : ''">
            <td class="px-4 py-3">
              <input type="checkbox" :value="apt.id" v-model="selected" class="w-4 h-4 accent-[#C9A96E] cursor-pointer"/>
            </td>
            <td class="px-5 py-3 text-white text-sm font-bold">{{ apt.number }}</td>
            <td class="px-5 py-3 text-slate-300 text-sm hidden lg:table-cell">{{ apt.project?.name }}</td>
            <td class="px-5 py-3 text-slate-400 text-sm hidden lg:table-cell">{{ apt.floor }}</td>
            <td class="px-5 py-3 text-slate-300 text-sm">{{ apt.rooms }}-комн.</td>
            <td class="px-5 py-3 text-slate-300 text-sm">{{ apt.area }} м²</td>
            <td class="px-5 py-3 text-gold text-sm font-semibold">{{ Number(apt.price).toLocaleString() }} {{ apt.currency }}</td>
            <td class="px-5 py-3">
              <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold" :class="aptStatusClass(apt.status)">
                {{ aptStatusLabel(apt.status) }}
              </span>
            </td>
            <td class="px-5 py-3 text-slate-400 text-sm hidden xl:table-cell">{{ apt.client?.name || '—' }}</td>
            <td class="px-5 py-3 text-right">
              <div class="flex items-center justify-end gap-2">
                <button @click="openPhotos(apt)" class="relative p-1.5 rounded-lg text-slate-400 hover:text-gold hover:bg-gold/10 transition-all" title="Фотографии">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
                  <span v-if="apt.images?.length" class="absolute -top-1 -right-1 bg-gold text-[#0F172A] text-[9px] font-bold rounded-full min-w-4 h-4 px-1 flex items-center justify-center">{{ apt.images.length }}</span>
                </button>
                <button @click="openModal(apt)" class="p-1.5 rounded-lg text-slate-400 hover:text-gold hover:bg-gold/10 transition-all" title="Изменить">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                </button>
                <button @click="deleteApt(apt)" class="p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-all">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Import modal -->
    <Teleport to="body">
      <div v-if="importModal" class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="background:rgba(0,0,0,0.7); backdrop-filter:blur(4px)" @click.self="importModal = false">
        <div class="w-full max-w-md rounded-2xl p-6 shadow-2xl" style="background:#1E293B; border:1px solid rgba(255,255,255,0.1)">
          <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-white text-lg">Массовый импорт квартир</h3>
            <button @click="importModal = false" class="text-slate-400 hover:text-white">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
          </div>
          <form @submit.prevent="submitImport" class="space-y-4">
            <div>
              <label class="form-label">Проект *</label>
              <select v-model="importProjectId" class="form-input" required>
                <option value="">— Выберите проект —</option>
                <option v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option>
              </select>
            </div>
            <div>
              <label class="form-label">Файл CSV или JSON *</label>
              <input type="file" accept=".csv,.json,text/csv,application/json" class="form-input"
                @change="e => importFile = e.target.files[0]" required/>
            </div>
            <div class="rounded-xl p-4 text-xs text-slate-400 leading-relaxed" style="background:#0F172A">
              <p class="text-slate-300 font-semibold mb-1.5">Колонки (CSV — первая строка заголовки, разделитель , или ;):</p>
              <p class="font-mono text-[11px] text-gold/80 break-all">number*, floor*, rooms*, area*, price*, currency, status, finish, ceiling_height, bathrooms, balcony, view_type, notes</p>
              <p class="mt-2">Квартира с существующим номером в проекте будет <span class="text-white">обновлена</span>, новая — создана. Excel: сохраните лист как CSV.</p>
            </div>
            <div class="flex gap-3">
              <button type="button" @click="importModal = false"
                class="flex-1 py-2.5 rounded-xl text-sm text-slate-300 border border-white/10 hover:border-white/20 transition-all">Отмена</button>
              <button type="submit" :disabled="importProcessing || !importFile || !importProjectId"
                class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-[#0F172A] disabled:opacity-50" style="background:#C9A96E">
                {{ importProcessing ? 'Импортируем…' : 'Импортировать' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- Modal -->
    <Teleport to="body">
      <Transition enter-active-class="transition-all duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100"
        leave-active-class="transition-all duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="modal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.7)">
          <div class="w-full max-w-lg rounded-2xl p-6 shadow-2xl" style="background:#1E293B; border:1px solid rgba(255,255,255,0.1)">
            <div class="flex items-center justify-between mb-5">
              <h3 class="font-bold text-white text-lg">{{ editingApt ? 'Редактировать квартиру' : 'Новая квартира' }}</h3>
              <button @click="modal = false" class="text-slate-400 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
              </button>
            </div>
            <form @submit.prevent="save" class="space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="col-span-2">
                  <label class="form-label">Проект *</label>
                  <select v-model="form.project_id" class="form-input" required>
                    <option value="">— Выберите проект —</option>
                    <option v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option>
                  </select>
                </div>
                <div>
                  <label class="form-label">Номер квартиры *</label>
                  <input v-model="form.number" type="text" class="form-input" required />
                </div>
                <div>
                  <label class="form-label">Этаж *</label>
                  <input v-model="form.floor" type="number" class="form-input" required />
                </div>
                <div>
                  <label class="form-label">Комнат</label>
                  <input v-model="form.rooms" type="number" min="0" class="form-input" />
                </div>
                <div>
                  <label class="form-label">Площадь (м²)</label>
                  <input v-model="form.area" type="number" step="0.01" class="form-input" />
                </div>
                <div>
                  <label class="form-label">Цена</label>
                  <input v-model="form.price" type="number" class="form-input" />
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
                  <label class="form-label">Статус</label>
                  <select v-model="form.status" class="form-input">
                    <option value="available">Свободно</option>
                    <option value="reserved">Бронь</option>
                    <option value="sold">Продано</option>
                  </select>
                </div>
                <div>
                  <label class="form-label">Отделка</label>
                  <select v-model="form.finish" class="form-input">
                    <option value="none">Без отделки</option>
                    <option value="rough">Черновая</option>
                    <option value="fine">Чистовая</option>
                    <option value="furnished">С мебелью</option>
                  </select>
                </div>
                <div class="col-span-2">
                  <label class="form-label">Клиент (если забронировано/продано)</label>
                  <select v-model="form.client_id" class="form-input">
                    <option value="">— Нет —</option>
                    <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }} ({{ c.phone }})</option>
                  </select>
                </div>
                <div class="col-span-2">
                  <label class="form-label">Заметки</label>
                  <textarea v-model="form.notes" rows="2" class="form-input"></textarea>
                </div>
              </div>
              <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="modal = false" class="px-5 py-2.5 rounded-xl text-sm text-slate-400 hover:text-white" style="background:rgba(255,255,255,0.05)">Отмена</button>
                <button type="submit" :disabled="form.processing" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-[#0F172A] disabled:opacity-60" style="background:#C9A96E">
                  {{ editingApt ? 'Сохранить' : 'Добавить' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- ═══ МОДАЛКА ФОТОГРАФИЙ ═══ -->
    <Teleport to="body">
      <Transition name="fade">
        <div v-if="photoModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.7)" @click.self="photoModal=false">
          <div class="w-full max-w-2xl rounded-2xl p-6 max-h-[90vh] overflow-y-auto" style="background:#1E293B;border:1px solid rgba(255,255,255,0.08)">
            <div class="flex items-center justify-between mb-5">
              <h3 class="font-bold text-white text-lg">Фотографии · кв. {{ photoApt?.number }}</h3>
              <button @click="photoModal=false" class="text-slate-400 hover:text-white text-xl leading-none">✕</button>
            </div>

            <!-- Загрузка (Drag &amp; Drop / выбор файлов) -->
            <label class="block rounded-xl border-2 border-dashed border-white/12 hover:border-gold/40 p-6 text-center cursor-pointer transition-colors mb-4">
              <input type="file" accept="image/jpeg,image/png,image/webp" multiple class="hidden" @change="onFiles" />
              <div class="text-3xl mb-2">📷</div>
              <div class="text-slate-300 text-sm font-medium">Выберите фото (можно несколько)</div>
              <div class="text-slate-500 text-xs mt-1">JPG, PNG, WEBP · до 5 МБ каждое</div>
            </label>

            <div v-if="photoForm.images.length" class="flex items-center justify-between mb-4 px-1">
              <span class="text-slate-300 text-sm">Выбрано файлов: {{ photoForm.images.length }}</span>
              <button @click="uploadPhotos" :disabled="photoForm.processing"
                class="px-5 py-2 rounded-xl text-sm font-semibold text-[#0F172A] disabled:opacity-60" style="background:#C9A96E">
                {{ photoForm.processing ? 'Загрузка…' : 'Загрузить' }}
              </button>
            </div>
            <div v-if="photoForm.progress" class="h-1 rounded bg-white/10 mb-4 overflow-hidden">
              <div class="h-full bg-gold transition-all" :style="`width:${photoForm.progress.percentage}%`"></div>
            </div>

            <!-- Текущие фото (перетаскивание для сортировки) -->
            <template v-if="currentPhotos.length">
              <p class="text-slate-500 text-xs mb-2">Перетащите фото, чтобы изменить порядок. Первое (★) — главное.</p>
              <draggable v-model="localPhotos" item-key="id" @end="onReorder" class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                <template #item="{ element: img }">
                  <div class="relative group rounded-lg overflow-hidden aspect-square border cursor-move select-none"
                    :class="img.is_primary ? 'border-gold' : 'border-white/8'">
                    <img :src="`/storage/${img.path}`" class="w-full h-full object-cover pointer-events-none" loading="lazy" draggable="false" />
                    <div class="absolute inset-0 bg-black/55 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-1.5">
                      <button v-if="!img.is_primary" @click="makePrimary(img)" class="text-[10px] px-2 py-1 rounded bg-gold text-[#0F172A] font-bold">Сделать главным</button>
                      <button @click="deletePhoto(img)" class="text-[10px] px-2 py-1 rounded bg-red-500/85 text-white font-bold">Удалить</button>
                    </div>
                    <span v-if="img.is_primary" class="absolute top-1 left-1 text-[9px] bg-gold text-[#0F172A] font-bold px-1.5 py-0.5 rounded">★ Главное</span>
                  </div>
                </template>
              </draggable>
            </template>
            <div v-else class="text-center text-slate-500 text-sm py-8">Фотографий пока нет — загрузите первую.</div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import draggable from 'vuedraggable';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ apartments: Object, projects: Array, clients: Array, filters: Object });
const modal = ref(false);
const editingApt = ref(null);
const projectFilter = ref(props.filters?.project_id || '');
const statusFilter = ref(props.filters?.status || '');
const roomsFilter = ref(props.filters?.rooms || '');

const availableCount = computed(() => props.apartments.data.filter(a => a.status === 'available').length);
const reservedCount  = computed(() => props.apartments.data.filter(a => a.status === 'reserved').length);
const soldCount      = computed(() => props.apartments.data.filter(a => a.status === 'sold').length);

const form = useForm({ project_id:'', number:'', floor:'', rooms:1, area:'', price:'', currency:'USD', status:'available', finish:'rough', client_id:'', notes:'' });

function openModal(a = null) {
  editingApt.value = a;
  if (a) { Object.keys(form).forEach(k => { if (k in a) form[k] = a[k] ?? ''; }); form.client_id = a.client_id || ''; }
  else { form.reset(); form.currency='USD'; form.status='available'; form.finish='rough'; form.rooms=1; }
  modal.value = true;
}
function save() {
  const opts = { onSuccess: () => { modal.value = false; } };
  editingApt.value ? form.put(route('admin.apartments.update', editingApt.value.id), opts) : form.post(route('admin.apartments.store'), opts);
}
function deleteApt(a) {
  if (confirm(`Удалить квартиру №${a.number}?`)) router.delete(route('admin.apartments.destroy', a.id));
}
function doFilter() { router.get(route('admin.apartments.index'), { project_id: projectFilter.value, status: statusFilter.value, rooms: roomsFilter.value }, { preserveState: true }); }

/* ── Фотогалерея квартиры ── */
const photoModal = ref(false);
const photoApt = ref(null);
const photoForm = useForm({ images: [] });
// Всегда актуальный список фото выбранной квартиры (обновляется при partial reload)
const currentPhotos = computed(() => {
  const a = props.apartments.data.find(x => x.id === photoApt.value?.id);
  return a?.images || photoApt.value?.images || [];
});
// Локальный список для drag-and-drop (синхронизируется с актуальными фото)
const localPhotos = ref([]);
watch(currentPhotos, (v) => { localPhotos.value = [...v]; }, { immediate: true, deep: true });

function openPhotos(a) { photoApt.value = a; photoForm.reset(); photoModal.value = true; }
function onFiles(e) { photoForm.images = Array.from(e.target.files); }
const photoOpts = { preserveScroll: true, preserveState: true, only: ['apartments'] };
function onReorder() {
  const order = localPhotos.value.map(p => p.id);
  router.patch(route('admin.apartments.images.reorder', photoApt.value.id), { order }, photoOpts);
}
function uploadPhotos() {
  photoForm.post(route('admin.apartments.images.store', photoApt.value.id), {
    ...photoOpts, forceFormData: true, onSuccess: () => { photoForm.reset(); },
  });
}
function deletePhoto(img) { router.delete(route('admin.apartments.images.destroy', [photoApt.value.id, img.id]), photoOpts); }
function makePrimary(img) { router.patch(route('admin.apartments.images.primary', [photoApt.value.id, img.id]), {}, photoOpts); }

function aptStatusLabel(s) { return { available:'Свободно', reserved:'Бронь', sold:'Продано', soon:'Скоро' }[s]||s; }
function aptStatusClass(s) { return { available:'bg-emerald-500/15 text-emerald-400', reserved:'bg-amber-500/15 text-amber-400', sold:'bg-gold/15 text-gold', soon:'bg-slate-500/15 text-slate-400' }[s]||''; }

/* ── Массовое изменение ── */
const selected = ref([]);
const bulk = reactive({ status: '', price: '', discount_percent: '', floor: '' });
const bulkProcessing = ref(false);

const allSelected = computed(() =>
  props.apartments.data.length > 0 && props.apartments.data.every(a => selected.value.includes(a.id)));

function toggleAll() {
  selected.value = allSelected.value ? [] : props.apartments.data.map(a => a.id);
}

function applyBulk() {
  const payload = { ids: selected.value };
  if (bulk.status) payload.status = bulk.status;
  if (bulk.price !== '' && bulk.price !== null) payload.price = Number(bulk.price);
  if (bulk.discount_percent !== '' && bulk.discount_percent !== null) payload.discount_percent = Number(bulk.discount_percent);
  if (bulk.floor !== '' && bulk.floor !== null) payload.floor = Number(bulk.floor);

  bulkProcessing.value = true;
  router.post(route('admin.apartments.bulk'), payload, {
    preserveScroll: true,
    onSuccess: () => { selected.value = []; Object.assign(bulk, { status: '', price: '', discount_percent: '', floor: '' }); },
    onFinish: () => { bulkProcessing.value = false; },
  });
}

/* ── Импорт CSV / JSON ── */
const importModal = ref(false);
const importFile = ref(null);
const importProjectId = ref('');
const importProcessing = ref(false);

function submitImport() {
  if (!importFile.value || !importProjectId.value) return;
  importProcessing.value = true;
  router.post(route('admin.apartments.import'), {
    project_id: importProjectId.value,
    file: importFile.value,
  }, {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => { importModal.value = false; importFile.value = null; },
    onFinish: () => { importProcessing.value = false; },
  });
}
</script>

<style scoped>
.form-label { display:block; font-size:0.75rem; font-weight:500; color:#94A3B8; margin-bottom:0.375rem; }
.form-input { display:block; width:100%; padding:0.625rem 0.75rem; border-radius:0.75rem; font-size:0.875rem; color:#fff; background:#0F172A; border:1px solid rgba(255,255,255,0.08); outline:none; transition:border-color 0.2s; }
.form-input:focus { border-color:rgba(201,169,110,0.5); }
.bulk-field { background:#0F172A; border:1px solid rgba(255,255,255,0.1); border-radius:0.6rem; padding:0.45rem 0.7rem; font-size:0.75rem; color:#E2E8F0; outline:none; }
.bulk-field:focus { border-color:rgba(201,169,110,0.5); }
</style>
