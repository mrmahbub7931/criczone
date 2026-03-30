<template>
  <DashboardLayout>

    <!-- ── Stat Cards ── -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div v-for="card in statCards" :key="card.label" class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-3">
          <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ card.label }}</span>
          <div :class="['p-2 rounded-lg', card.bg]">
            <component :is="card.icon" :size="16" :class="card.color" />
          </div>
        </div>
        <p class="text-2xl font-black text-gray-900">{{ card.value }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ card.sub }}</p>
      </div>
    </div>

    <!-- ── Chart + Recent Articles ── -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">

      <!-- Bar Chart (pure CSS) -->
      <div class="lg:col-span-7 bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-5">
          <h2 class="font-bold text-gray-800">Views — Last 7 Days</h2>
          <span class="text-xs font-medium text-primary bg-primary/10 px-2.5 py-1 rounded-full">
            {{ totalWeekViews.toLocaleString() }} total
          </span>
        </div>
        <div class="flex items-end gap-3 h-40">
          <div
            v-for="d in chartData"
            :key="d.day"
            class="flex-1 flex flex-col items-center gap-1 group"
          >
            <span class="text-[10px] text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity font-medium">
              {{ (d.views / 1000).toFixed(1) }}k
            </span>
            <div class="w-full rounded-t-md bg-primary/15 group-hover:bg-primary/25 transition-colors relative overflow-hidden" style="height: 100%;">
              <div
                class="absolute bottom-0 left-0 right-0 bg-primary rounded-t-md transition-all duration-500"
                :style="{ height: barHeight(d.views) }"
              />
            </div>
            <span class="text-[11px] text-gray-500 font-medium">{{ d.day }}</span>
          </div>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="lg:col-span-5 bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h2 class="font-bold text-gray-800 mb-4">Quick Stats</h2>
        <div class="space-y-3">
          <div class="flex items-center justify-between py-2 border-b border-gray-50">
            <span class="text-sm text-gray-600">Published</span>
            <span class="text-sm font-bold text-green-600">{{ stats.publishedCount }}</span>
          </div>
          <div class="flex items-center justify-between py-2 border-b border-gray-50">
            <span class="text-sm text-gray-600">Drafts</span>
            <span class="text-sm font-bold text-yellow-600">{{ stats.draftCount }}</span>
          </div>
          <div class="flex items-center justify-between py-2 border-b border-gray-50">
            <span class="text-sm text-gray-600">Live Matches</span>
            <span class="text-sm font-bold text-secondary">{{ stats.liveMatches }}</span>
          </div>
          <div class="flex items-center justify-between py-2 border-b border-gray-50">
            <span class="text-sm text-gray-600">Categories</span>
            <span class="text-sm font-bold text-primary">{{ stats.categories }}</span>
          </div>
          <div class="flex items-center justify-between py-2">
            <span class="text-sm text-gray-600">Total Views</span>
            <span class="text-sm font-black text-gray-900">{{ (stats.totalViews / 1000).toFixed(0) }}K</span>
          </div>
        </div>
      </div>
    </div>

    <!-- ── Recent Articles ── -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <h2 class="font-bold text-gray-800">Recent Articles</h2>
        <Link href="/dashboard/articles" class="text-xs font-semibold text-primary hover:text-primary-dark transition-colors">
          View all →
        </Link>
      </div>
      <div class="divide-y divide-gray-50">
        <div
          v-for="art in recentArticles"
          :key="art.id"
          class="flex items-center gap-4 px-5 py-3.5 hover:bg-gray-50 transition-colors"
        >
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-800 truncate">{{ art.title }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ art.category }} · {{ art.author }} · {{ art.date }}</p>
          </div>
          <span :class="['text-[11px] font-semibold px-2 py-0.5 rounded-full shrink-0', statusClass(art.status)]">
            {{ art.status }}
          </span>
          <span class="text-xs text-gray-400 shrink-0 hidden sm:block">
            {{ (art.views / 1000).toFixed(1) }}k views
          </span>
        </div>
      </div>
    </div>

  </DashboardLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Newspaper, Eye, Radio, Tag } from 'lucide-vue-next'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { useDashboard } from '@/composables/useDashboard.js'

const { articles, stats, chartData, liveScores } = useDashboard()

const maxViews = computed(() => Math.max(...chartData.value.map(d => d.views)))
const totalWeekViews = computed(() => chartData.value.reduce((s, d) => s + d.views, 0))

const barHeight = (views) => `${Math.round((views / maxViews.value) * 100)}%`

const recentArticles = computed(() => articles.value.slice(0, 6))

const statCards = computed(() => [
  { label: 'Total Articles', value: stats.value.totalArticles,               icon: Newspaper, bg: 'bg-blue-50',   color: 'text-primary',   sub: `${stats.value.publishedCount} published` },
  { label: 'Total Views',    value: (stats.value.totalViews / 1000).toFixed(0) + 'K', icon: Eye,       bg: 'bg-green-50',  color: 'text-green-600', sub: 'all time' },
  { label: 'Live Matches',   value: stats.value.liveMatches,                 icon: Radio,     bg: 'bg-red-50',    color: 'text-secondary', sub: 'currently live' },
  { label: 'Categories',     value: stats.value.categories,                  icon: Tag,       bg: 'bg-purple-50', color: 'text-purple-600', sub: 'active categories' },
])

const statusClass = (s) => s === 'published'
  ? 'bg-green-100 text-green-700'
  : 'bg-yellow-100 text-yellow-700'
</script>
