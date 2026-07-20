<template>
  <AdminLayout page-title="Панель управления" page-subtitle="Аналитика и обзор системы">
    <Head title="Панель управления" />

    <!-- ═══ STAT CARDS ═══ -->
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
      <div v-for="card in statCards" :key="card.label"
        class="stat-card" :style="`border-left: 3px solid ${card.color}`">
        <div class="flex items-start justify-between mb-3">
          <div class="stat-icon" :style="`background:${card.color}18`">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" :style="`color:${card.color}`" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" :d="card.icon" /></svg>
          </div>
          <span class="stat-badge" :style="`background:${card.color}18; color:${card.color}`">
            {{ card.badge }}
          </span>
        </div>
        <div class="stat-value">{{ card.value }}</div>
        <div class="stat-label">{{ card.label }}</div>
      </div>
    </div>

    <!-- ═══ CRM ROW: Deals + Temperature + Funnel ═══ -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

      <!-- Deals KPI -->
      <div class="chart-card">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-white font-semibold text-sm">Сделки</h3>
          <Link :href="route('admin.deals.index')" class="text-gold text-xs hover:text-white transition-colors">Все →</Link>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div class="rounded-xl p-3" style="background:#0F172A">
            <div class="text-2xl font-black text-blue-400">{{ deals_stats?.open ?? 0 }}</div>
            <div class="text-xs text-gray-500 mt-1">Открыто</div>
          </div>
          <div class="rounded-xl p-3" style="background:#0F172A">
            <div class="text-2xl font-black text-green-400">{{ deals_stats?.won ?? 0 }}</div>
            <div class="text-xs text-gray-500 mt-1">Выиграно</div>
          </div>
          <div class="rounded-xl p-3 col-span-2" style="background:#0F172A">
            <div class="text-lg font-black text-gold leading-tight">
              {{ formatMoney(deals_stats?.total_value) }}
            </div>
            <div class="text-xs text-gray-500 mt-1">Сумма открытых сделок</div>
          </div>
        </div>
        <!-- Won value -->
        <div class="mt-3 flex items-center justify-between text-xs">
          <span class="text-gray-500">Закрыто на сумму:</span>
          <span class="text-green-400 font-bold">{{ formatMoney(deals_stats?.won_value) }}</span>
        </div>
      </div>

      <!-- Temperature distribution -->
      <div class="chart-card">
        <h3 class="text-white font-semibold text-sm mb-4">Температура лидов</h3>
        <div class="space-y-3">
          <div v-for="temp in tempStats" :key="temp.key">
            <div class="flex items-center justify-between mb-1.5">
              <span class="text-sm flex items-center gap-1.5">
                <span>{{ temp.icon }}</span>
                <span class="text-gray-300">{{ temp.label }}</span>
              </span>
              <span class="font-bold text-white text-sm">{{ temp.value }}</span>
            </div>
            <div class="h-2.5 rounded-full overflow-hidden" style="background:rgba(255,255,255,0.06)">
              <div class="h-full rounded-full transition-all duration-700"
                :style="`width:${temp.pct}%; background:${temp.color}`"></div>
            </div>
          </div>
        </div>
        <div class="mt-4 pt-3 border-t border-white/5 flex justify-between items-center">
          <span class="text-xs text-gray-500">Всего лидов</span>
          <span class="text-xl font-bold text-white">{{ stats?.leads_total ?? 0 }}</span>
        </div>
      </div>

      <!-- Pipeline Funnel -->
      <div class="chart-card">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-white font-semibold text-sm">Воронка продаж</h3>
          <Link :href="route('admin.leads.kanban')" class="text-gold text-xs hover:text-white transition-colors">Kanban →</Link>
        </div>

        <div v-if="pipeline_funnel?.length" class="space-y-2">
          <div v-for="(stage, idx) in pipeline_funnel" :key="stage.key">
            <div class="flex items-center justify-between mb-1">
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full" :style="`background:${stage.color}`"></span>
                <span class="text-xs text-gray-300">{{ stage.label }}</span>
              </div>
              <span class="text-xs font-bold text-white">{{ stage.count }}</span>
            </div>
            <!-- Funnel bar — width proportional to max -->
            <div class="h-6 rounded-lg overflow-hidden relative" style="background:rgba(255,255,255,0.04)">
              <div class="h-full rounded-lg flex items-center px-2 transition-all duration-700"
                :style="`width:${funnelPct(stage.count)}%; background:${stage.color}30; border-left: 3px solid ${stage.color}`">
              </div>
              <span class="absolute inset-0 flex items-center px-2 text-[10px] text-gray-400">
                {{ funnelPct(stage.count).toFixed(0) }}%
              </span>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-8 text-gray-500">
          <div class="text-2xl mb-1">📊</div>
          <p class="text-xs">Лиды по этапам ещё не распределены</p>
        </div>
      </div>
    </div>

    <!-- ═══ CHARTS ROW: Leads trend + Conversion ═══ -->
    <div class="grid lg:grid-cols-3 gap-4 mb-6">

      <!-- Leads Line Chart -->
      <div class="lg:col-span-2 chart-card">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-white font-semibold text-sm">Заявки по месяцам</h3>
            <p class="text-gray-500 text-xs mt-0.5">Динамика за последние 6 месяцев</p>
          </div>
          <span class="text-[#6366F1] text-xs font-semibold px-2 py-1 rounded" style="background:#6366F115">
            Leads
          </span>
        </div>
        <!-- Fixed height wrapper — fixes the spike bug -->
        <div style="position:relative; height:180px">
          <canvas ref="leadsChartRef"></canvas>
        </div>
      </div>

      <!-- Conversion Doughnut -->
      <div class="chart-card">
        <div class="mb-4">
          <h3 class="text-white font-semibold text-sm">Конверсия заявок</h3>
          <p class="text-gray-500 text-xs mt-0.5">Статусы всех заявок</p>
        </div>
        <div style="position:relative; height:160px">
          <canvas ref="convChartRef"></canvas>
        </div>
        <div class="mt-4 space-y-1.5">
          <div v-for="item in convLegend" :key="item.label" class="flex items-center justify-between text-xs">
            <div class="flex items-center gap-2">
              <span class="w-2 h-2 rounded-full" :style="`background:${item.color}`"></span>
              <span class="text-gray-400">{{ item.label }}</span>
            </div>
            <span class="text-white font-semibold">{{ item.value }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- ═══ ROW: Top Managers + Mining + Apartments ═══ -->
    <div class="grid lg:grid-cols-3 gap-4 mb-6">

      <!-- Top Managers -->
      <div class="chart-card">
        <h3 class="text-white font-semibold text-sm mb-4">Топ менеджеры</h3>
        <div v-if="top_managers?.length" class="space-y-3">
          <div v-for="(mgr, idx) in top_managers" :key="mgr.id"
            class="flex items-center gap-3">
            <!-- Rank -->
            <span class="text-xs font-bold w-4 flex-shrink-0"
              :class="idx === 0 ? 'text-gold' : 'text-gray-600'">
              #{{ idx + 1 }}
            </span>
            <!-- Avatar -->
            <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-xs font-bold"
              :style="`background:${mgrColor(idx)}20; color:${mgrColor(idx)}`">
              {{ mgr.name.charAt(0) }}
            </div>
            <!-- Info -->
            <div class="flex-1 min-w-0">
              <div class="text-sm font-semibold text-white truncate">{{ mgr.name }}</div>
              <div class="flex items-center gap-2 mt-0.5">
                <!-- Bar -->
                <div class="flex-1 h-1.5 bg-white/10 rounded-full overflow-hidden">
                  <div class="h-full rounded-full transition-all"
                    :style="`width:${mgrPct(mgr.leads_count)}%; background:${mgrColor(idx)}`"></div>
                </div>
              </div>
            </div>
            <!-- Stats -->
            <div class="text-right flex-shrink-0">
              <div class="text-sm font-bold text-white">{{ mgr.leads_count }}</div>
              <div class="text-[10px] text-green-400">{{ mgr.conversion }}%</div>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-8 text-gray-500 text-xs">
          Заявки ещё не назначены менеджерам
        </div>
      </div>

      <!-- Mining Bar Chart -->
      <div class="chart-card">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-white font-semibold text-sm">Объём добычи угля</h3>
            <p class="text-gray-500 text-xs mt-0.5">Тонн по месяцам</p>
          </div>
          <span class="text-amber-400 text-xs font-semibold px-2 py-1 rounded" style="background:#F59E0B15">Mining</span>
        </div>
        <div style="position:relative; height:160px">
          <canvas ref="miningChartRef"></canvas>
        </div>
        <div class="mt-3 grid grid-cols-2 gap-2">
          <div class="rounded-lg p-2.5" style="background:#0F172A">
            <div class="text-[10px] text-gray-500 uppercase tracking-wider mb-1">Объём (т)</div>
            <div class="text-amber-400 font-bold">{{ stats?.mining_volume_total?.toLocaleString() || 0 }}</div>
          </div>
          <div class="rounded-lg p-2.5" style="background:#0F172A">
            <div class="text-[10px] text-gray-500 uppercase tracking-wider mb-1">Стоимость</div>
            <div class="text-green-400 font-bold">${{ formatNum(stats?.mining_value_total) }}</div>
          </div>
        </div>
      </div>

      <!-- Apartments -->
      <div class="chart-card">
        <div class="mb-4">
          <h3 class="text-white font-semibold text-sm">Квартиры</h3>
          <p class="text-gray-500 text-xs mt-0.5">Распределение по статусам</p>
        </div>
        <div class="space-y-4 mt-2">
          <div v-for="apt in aptStats" :key="apt.label">
            <div class="flex items-center justify-between mb-1.5">
              <span class="text-gray-400 text-xs">{{ apt.label }}</span>
              <span class="text-white font-semibold text-sm">{{ apt.value }}</span>
            </div>
            <div class="h-2 rounded-full overflow-hidden" style="background:rgba(255,255,255,0.06)">
              <div class="h-2 rounded-full transition-all duration-700"
                :style="`width:${apt.pct}%; background:${apt.color}`"></div>
            </div>
          </div>
        </div>
        <div class="mt-5 pt-4 border-t border-white/5 flex justify-between items-center">
          <span class="text-gray-500 text-xs uppercase tracking-wider">Всего квартир</span>
          <span class="text-2xl font-bold text-white">{{ aptTotal }}</span>
        </div>
      </div>
    </div>

    <!-- ═══ ROW: Recent Leads + Activity ═══ -->
    <div class="grid lg:grid-cols-3 gap-4">

      <!-- Recent Leads -->
      <div class="lg:col-span-2 chart-card p-0 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-white/5">
          <h3 class="text-white font-semibold text-sm">Последние заявки</h3>
          <div class="flex items-center gap-3">
            <a :href="route('admin.leads.export')" class="export-btn">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
              </svg>
              CSV
            </a>
            <Link :href="route('admin.leads.index')" class="text-gold hover:text-white text-xs transition-colors">Все →</Link>
          </div>
        </div>

        <div v-if="!recent_leads?.length" class="px-5 py-10 text-center text-gray-500 text-sm">Заявок пока нет</div>
        <table v-else class="w-full">
          <thead>
            <tr class="border-b border-white/5">
              <th class="tbl-th">Имя</th>
              <th class="tbl-th hidden md:table-cell">Телефон</th>
              <th class="tbl-th">Темп.</th>
              <th class="tbl-th">Статус</th>
              <th class="tbl-th hidden lg:table-cell">Score</th>
              <th class="tbl-th hidden lg:table-cell">Когда</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="lead in recent_leads" :key="lead.id"
              class="border-b border-white/4 hover:bg-white/3 transition-colors cursor-pointer"
              @click="router.visit(route('admin.leads.show', lead.id))">
              <td class="tbl-td font-medium text-white">
                {{ lead.name }}
                <div v-if="lead.interest" class="text-[10px] text-gray-500">{{ lead.interest }}</div>
              </td>
              <td class="tbl-td text-gray-400 hidden md:table-cell">{{ lead.phone }}</td>
              <td class="tbl-td">
                <span class="text-base" :title="tempLabel(lead.temperature)">{{ tempIcon(lead.temperature) }}</span>
              </td>
              <td class="tbl-td">
                <span class="status-badge" :class="statusClass(lead.status)">{{ statusLabel(lead.status) }}</span>
              </td>
              <td class="tbl-td hidden lg:table-cell">
                <div class="flex items-center gap-1.5">
                  <div class="w-10 h-1 bg-white/10 rounded-full overflow-hidden">
                    <div class="h-full rounded-full" :class="scoreBg(lead.score)"
                      :style="`width:${lead.score}%`"></div>
                  </div>
                  <span class="text-[10px] text-gray-500">{{ lead.score }}</span>
                </div>
              </td>
              <td class="tbl-td text-gray-500 text-xs hidden lg:table-cell">{{ lead.created_at }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Activity + Quick links -->
      <div class="chart-card">
        <h3 class="text-white font-semibold text-sm mb-4">Последние события</h3>
        <div class="space-y-3">
          <div v-for="(event, i) in activity" :key="i" class="flex items-start gap-3">
            <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5"
              :style="`background:${event.color}20`">
              <span class="text-xs">{{ event.type === 'lead' ? '💬' : '⛏' }}</span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-gray-300 text-xs leading-snug">{{ event.text }}</p>
              <p class="text-gray-600 text-[10px] mt-0.5">{{ event.time }}</p>
            </div>
          </div>
          <div v-if="!activity?.length" class="text-gray-500 text-xs text-center py-4">Нет событий</div>
        </div>

        <div class="mt-5 pt-4 border-t border-white/5 space-y-1.5">
          <p class="text-gray-500 text-[10px] uppercase tracking-wider mb-2">Быстрый переход</p>
          <Link v-for="link in quickLinks" :key="link.route" :href="route(link.route)"
            class="flex items-center gap-2 text-gray-400 hover:text-white text-xs py-1.5 transition-colors group">
            <span class="w-1.5 h-1.5 rounded-full bg-gold opacity-0 group-hover:opacity-100 transition-opacity"></span>
            {{ link.label }}
          </Link>
        </div>
      </div>
    </div>

  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const props = defineProps({
  stats:            Object,
  deals_stats:      Object,
  recent_leads:     Array,
  leads_chart:      Array,
  mining_chart:     Array,
  conversion_data:  Object,
  temperature_data: Object,
  pipeline_funnel:  Array,
  top_managers:     Array,
  apartments_rooms: Object,
  activity:         Array,
})

// ── Chart refs ──
const leadsChartRef  = ref(null)
const convChartRef   = ref(null)
const miningChartRef = ref(null)

// ── Stat cards ──
const statCards = computed(() => [
  {
    label: 'Всего заявок',
    value: props.stats?.leads_total ?? 0,
    icon: 'M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z',
    color: '#6366F1',
    badge: `+${props.stats?.leads_today ?? 0} сегодня`,
  },
  {
    label: '🔥 Горячих лидов',
    value: props.stats?.leads_hot ?? 0,
    icon: 'M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.975 5.975 0 0 1-2.133-1.001A3.75 3.75 0 0 0 12 18Z',
    color: '#EF4444',
    badge: 'Требуют звонка',
  },
  {
    label: 'Клиентов',
    value: props.stats?.clients_total ?? 0,
    icon: 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z',
    color: '#10B981',
    badge: 'В базе',
  },
  {
    label: 'Сделок открыто',
    value: props.deals_stats?.open ?? 0,
    icon: 'M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z',
    color: '#C9A96E',
    badge: `Выиграно: ${props.deals_stats?.won ?? 0}`,
  },
])

// ── Apartment stats ──
const aptTotal = computed(() =>
  (props.stats?.apartments_available ?? 0) +
  (props.stats?.apartments_reserved  ?? 0) +
  (props.stats?.apartments_sold      ?? 0)
)
const aptStats = computed(() => {
  const total = aptTotal.value || 1
  return [
    { label:'Свободно', value: props.stats?.apartments_available ?? 0, color:'#10B981', pct: ((props.stats?.apartments_available ?? 0) / total * 100).toFixed(1) },
    { label:'Бронь',    value: props.stats?.apartments_reserved  ?? 0, color:'#F59E0B', pct: ((props.stats?.apartments_reserved  ?? 0) / total * 100).toFixed(1) },
    { label:'Продано',  value: props.stats?.apartments_sold      ?? 0, color:'#C9A96E', pct: ((props.stats?.apartments_sold      ?? 0) / total * 100).toFixed(1) },
  ]
})

// ── Temperature ──
const tempStats = computed(() => {
  const total = (props.stats?.leads_total || 1)
  const td = props.temperature_data ?? {}
  return [
    { key:'hot',  icon:'🔥', label:'Высокий интерес', value: td.hot  ?? 0, color:'#EF4444', pct: ((td.hot  ?? 0) / total * 100).toFixed(1) },
    { key:'warm', icon:'🌤', label:'Средний интерес', value: td.warm ?? 0, color:'#F59E0B', pct: ((td.warm ?? 0) / total * 100).toFixed(1) },
    { key:'cold', icon:'❄️', label:'Низкий интерес',  value: td.cold ?? 0, color:'#6366F1', pct: ((td.cold ?? 0) / total * 100).toFixed(1) },
  ]
})

// ── Funnel ──
const funnelMax = computed(() => Math.max(...(props.pipeline_funnel ?? []).map(s => s.count), 1))
function funnelPct(count) { return (count / funnelMax.value) * 100 }

// ── Top managers ──
const mgrColors = ['#C9A96E','#6366F1','#10B981','#F59E0B','#EF4444']
function mgrColor(idx) { return mgrColors[idx] ?? '#64748B' }
const mgrMax = computed(() => Math.max(...(props.top_managers ?? []).map(m => m.leads_count), 1))
function mgrPct(count) { return (count / mgrMax.value) * 100 }

// ── Conversion legend ──
const convLegend = computed(() => [
  { label:'Новые',    value: props.conversion_data?.new         ?? 0, color:'#6366F1' },
  { label:'В работе', value: props.conversion_data?.in_progress ?? 0, color:'#F59E0B' },
  { label:'Успешно',  value: props.conversion_data?.success     ?? 0, color:'#10B981' },
  { label:'Отказ',    value: props.conversion_data?.rejected    ?? 0, color:'#EF4444' },
])

// ── Helpers ──
function formatNum(n) {
  if (!n) return '0'
  if (n >= 1_000_000) return (n / 1_000_000).toFixed(1) + 'M'
  if (n >= 1_000)     return (n / 1_000).toFixed(0) + 'K'
  return Number(n).toLocaleString()
}
function formatMoney(amount, currency = 'USD') {
  if (!amount) return '$0'
  return new Intl.NumberFormat('ru-RU', { style:'currency', currency, maximumFractionDigits:0 }).format(amount)
}
function statusLabel(s)  { return { new:'Новая', in_progress:'В работе', success:'Успешно', rejected:'Отказ' }[s] ?? s }
function statusClass(s)  { return { new:'bg-blue-500/15 text-blue-400', in_progress:'bg-amber-500/15 text-amber-400', success:'bg-emerald-500/15 text-emerald-400', rejected:'bg-red-500/15 text-red-400' }[s] ?? 'bg-slate-500/15 text-slate-400' }
function tempIcon(t)     { return { cold:'❄️', warm:'🌤', hot:'🔥' }[t] ?? '❄️' }
function tempLabel(t)    { return { cold:'Низкий интерес', warm:'Средний интерес', hot:'Высокий интерес' }[t] ?? t }
function scoreBg(s)      { if (s >= 70) return 'bg-green-400'; if (s >= 40) return 'bg-yellow-400'; return 'bg-red-400' }

const quickLinks = [
  { route:'admin.leads.index',        label:'Все заявки CRM' },
  { route:'admin.leads.kanban',       label:'Kanban-доска' },
  { route:'admin.deals.index',        label:'Сделки' },
  { route:'admin.clients.index',      label:'База клиентов' },
  { route:'admin.construction.index', label:'Прогресс стройки' },
]

// ── Chart.js ──
const tooltipDefaults = {
  backgroundColor: '#0F172A',
  borderColor: 'rgba(255,255,255,0.1)',
  borderWidth: 1,
  titleColor: '#E2E8F0',
  bodyColor: '#94A3B8',
  padding: 10,
}

onMounted(() => {
  // 1. Leads Line Chart — bar style для sparse data, выглядит лучше чем line с иглой
  if (leadsChartRef.value && props.leads_chart?.length) {
    const allZero = props.leads_chart.every(d => d.count === 0)
    new Chart(leadsChartRef.value, {
      type: 'bar',
      data: {
        labels: props.leads_chart.map(d => d.label),
        datasets: [
          {
            label: 'Заявки',
            data: props.leads_chart.map(d => d.count),
            backgroundColor: 'rgba(99,102,241,0.65)',
            borderColor: '#6366F1',
            borderWidth: 1,
            borderRadius: 5,
            borderSkipped: false,
          },
          {
            // Invisible line overlay for trend feel
            type: 'line',
            label: 'Тренд',
            data: props.leads_chart.map(d => d.count),
            borderColor: 'rgba(99,102,241,0.4)',
            borderWidth: 2,
            pointRadius: 3,
            pointBackgroundColor: '#6366F1',
            fill: false,
            tension: 0.3,
          }
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: tooltipDefaults,
        },
        scales: {
          x: {
            grid: { color:'rgba(255,255,255,0.04)' },
            ticks: { color:'#64748B', font:{ size:11 } },
          },
          y: {
            grid: { color:'rgba(255,255,255,0.04)' },
            ticks: { color:'#64748B', font:{ size:11 }, stepSize:1 },
            beginAtZero: true,
            // Ensure minimum range so single-point looks decent
            suggestedMax: allZero ? 5 : undefined,
          },
        },
      },
    })
  }

  // 2. Conversion Doughnut
  if (convChartRef.value) {
    const d = props.conversion_data ?? {}
    const total = (d.new ?? 0) + (d.in_progress ?? 0) + (d.success ?? 0) + (d.rejected ?? 0)
    new Chart(convChartRef.value, {
      type: 'doughnut',
      data: {
        labels: ['Новые', 'В работе', 'Успешно', 'Отказ'],
        datasets: [{
          data: total > 0
            ? [d.new ?? 0, d.in_progress ?? 0, d.success ?? 0, d.rejected ?? 0]
            : [1, 0, 0, 0],
          backgroundColor: total > 0
            ? ['#6366F1CC','#F59E0BCC','#10B981CC','#EF4444CC']
            : ['rgba(255,255,255,0.06)','transparent','transparent','transparent'],
          borderColor: '#1E293B',
          borderWidth: 3,
          hoverOffset: 6,
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '68%',
        plugins: { legend:{ display:false }, tooltip: total > 0 ? tooltipDefaults : { enabled:false } },
      },
    })
  }

  // 3. Mining Bar Chart
  if (miningChartRef.value && props.mining_chart?.length) {
    new Chart(miningChartRef.value, {
      type: 'bar',
      data: {
        labels: props.mining_chart.map(d => d.label),
        datasets: [{
          label: 'Объём (тонн)',
          data: props.mining_chart.map(d => d.volume),
          backgroundColor: 'rgba(245,158,11,0.65)',
          borderColor: '#F59E0B',
          borderWidth: 1,
          borderRadius: 4,
          borderSkipped: false,
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend:{ display:false }, tooltip: tooltipDefaults },
        scales: {
          x: { grid:{ display:false }, ticks:{ color:'#64748B', font:{ size:11 } } },
          y: { grid:{ color:'rgba(255,255,255,0.04)' }, ticks:{ color:'#64748B', font:{ size:11 } }, beginAtZero:true },
        },
      },
    })
  }
})
</script>

<style scoped>
.stat-card {
  background: #1E293B;
  border-radius: 0.875rem;
  padding: 1.25rem;
  border: 1px solid rgba(255,255,255,0.06);
  transition: border-color 0.2s;
}
.stat-card:hover { border-color: rgba(255,255,255,0.12); }
.stat-icon  { width:2.5rem; height:2.5rem; border-radius:0.625rem; display:flex; align-items:center; justify-content:center; }
.stat-badge { font-size:0.65rem; font-weight:600; letter-spacing:0.05em; padding:0.2rem 0.5rem; border-radius:999px; white-space:nowrap; }
.stat-value { font-size:2rem; font-weight:700; color:#fff; line-height:1; margin-bottom:0.25rem; }
.stat-label { font-size:0.75rem; color:#64748B; }

.chart-card {
  background: #1E293B;
  border-radius: 0.875rem;
  padding: 1.25rem;
  border: 1px solid rgba(255,255,255,0.06);
}

.export-btn {
  display:inline-flex; align-items:center; gap:0.375rem;
  font-size:0.7rem; font-weight:600; letter-spacing:0.05em;
  color:#10B981; background:rgba(16,185,129,0.1);
  border:1px solid rgba(16,185,129,0.2);
  padding:0.25rem 0.625rem; border-radius:0.375rem;
  transition:all 0.2s; text-decoration:none;
}
.export-btn:hover { background:rgba(16,185,129,0.2); color:#fff; }

.tbl-th { padding:0.625rem 1.25rem; font-size:0.65rem; text-transform:uppercase; letter-spacing:0.1em; color:#475569; text-align:left; font-weight:600; }
.tbl-td { padding:0.75rem 1.25rem; font-size:0.8125rem; }
.status-badge { display:inline-block; padding:0.2rem 0.5rem; border-radius:999px; font-size:0.65rem; font-weight:600; }
</style>
