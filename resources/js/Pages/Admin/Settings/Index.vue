<template>
  <AdminLayout page-title="Настройки сайта" page-subtitle="CMS — управление контентом">
    <Head title="Настройки" />

    <form @submit.prevent="save">
      <!-- Group tabs -->
      <div class="flex gap-2 mb-6 flex-wrap">
        <button
          v-for="(label, key) in groupLabels" :key="key"
          type="button"
          @click="activeGroup = key"
          class="px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition-all"
          :class="activeGroup === key
            ? 'text-[#0F172A]'
            : 'text-slate-400 hover:text-white hover:bg-white/5'"
          :style="activeGroup === key ? 'background:#C9A96E' : ''"
        >{{ label }}</button>
      </div>

      <!-- Settings form -->
      <div class="rounded-xl border border-white/6 p-6 space-y-5" style="background:#1E293B">
        <div v-for="setting in currentGroup" :key="setting.key">
          <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
            {{ setting.label }}
          </label>
          <textarea
            v-if="setting.type === 'textarea'"
            v-model="form[setting.key]"
            rows="3"
            class="w-full px-4 py-2.5 text-sm text-white rounded-xl resize-none outline-none transition-all"
            style="background:#0F172A; border:1px solid rgba(255,255,255,0.08)"
          />
          <input
            v-else
            v-model="form[setting.key]"
            :type="setting.type === 'url' ? 'url' : 'text'"
            class="w-full px-4 py-2.5 text-sm text-white rounded-xl outline-none transition-all"
            style="background:#0F172A; border:1px solid rgba(255,255,255,0.08)"
          />
        </div>
      </div>

      <!-- Save button -->
      <div class="mt-6 flex justify-end">
        <button
          type="submit"
          :disabled="saving"
          class="flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-sm text-[#0F172A] transition-all disabled:opacity-60"
          style="background:#C9A96E"
        >
          <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
          </svg>
          <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75m9 0H18A2.25 2.25 0 0 1 20.25 6v15A2.25 2.25 0 0 1 18 23.25H6A2.25 2.25 0 0 1 3.75 21V6A2.25 2.25 0 0 1 6 3.75h1.5m9 0h-9"/>
          </svg>
          {{ saving ? 'Сохраняем...' : 'Сохранить настройки' }}
        </button>
      </div>
    </form>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, reactive } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  grouped:     { type: Object, default: () => ({}) },
  groupLabels: { type: Object, default: () => ({}) },
});

const activeGroup = ref(Object.keys(props.groupLabels)[0] || 'general');
const saving = ref(false);

// Build flat form object { key: value }
const form = reactive({});
Object.values(props.grouped).forEach(group => {
  Object.values(group).forEach(setting => {
    form[setting.key] = setting.value ?? '';
  });
});

// Current group settings array
const currentGroup = computed(() => {
  return Object.values(props.grouped[activeGroup.value] || {});
});

function save() {
  saving.value = true;
  const settings = Object.entries(form).map(([key, value]) => ({ key, value }));
  router.post(route('admin.settings.update'), { settings }, {
    preserveScroll: true,
    onFinish: () => { saving.value = false; },
  });
}
</script>
