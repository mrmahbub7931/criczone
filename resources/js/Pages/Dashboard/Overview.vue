<template>
  <DashboardLayout>

    <!-- Role-based welcome banner -->
    <div :class="['rounded-xl p-4 mb-6 flex items-center gap-4', banner.bg]">
      <div :class="['p-2.5 rounded-lg', banner.iconBg]">
        <component :is="banner.icon" :size="20" :class="banner.iconColor" />
      </div>
      <div>
        <p class="font-bold text-gray-900 text-sm">{{ banner.title }}, {{ auth.user?.name }}!</p>
        <p class="text-xs text-gray-500 mt-0.5">{{ banner.message }}</p>
      </div>
      <span :class="['ml-auto text-[11px] font-bold uppercase tracking-wide px-2.5 py-1 rounded-full', banner.badgeBg, banner.badgeText]">
        {{ auth.roles?.[0] }}
      </span>
    </div>

    <!-- Stat Cards — admin/editor see all, viewer sees limited -->
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

    <!-- Chart + Quick Stats (hidden for viewer) -->
    <div v-if="isAdminOrEditor" class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">

      <!-- Bar Chart -->
      <div class="lg:col-span-7 bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-5">
          <h2 class="font-bold text-gray-800 text-sm">Views — Last 7 Days</h2>
          <span class="text-xs font-medium text-[#0D47A1] bg-blue-50 px-2.5 py-1 rounded-full">
            {{ totalWeekViews.toLocaleString() }} total
          </span>
        </div>
        <div class="flex items-end gap-2 h-36">
          <div v-for="d in chartData" :key="d.day" class="flex-1 flex flex-col items-center gap-1 group">
            <span class="text-[10px] text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity">
              {{ (d.views / 1000).toFixed(1) }}k
            </span>
            <div class="w-full relative bg-blue-50 rounded-t-md group-hover:bg-blue-100 transition-colors" style="height:100%">
              <div
                class="absolute bottom-0 left-0 right-0 bg-[#0D47A1] rounded-t-md transition-all duration-500"
                :style="{ height: barHeight(d.views) }"
              />
            </div>
            <span class="text-[11px] text-gray-500">{{ d.day }}</span>
          </div>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="lg:col-span-5 bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h2 class="font-bold text-gray-800 text-sm mb-4">Quick Stats</h2>
        <div class="space-y-2.5">
          <div class="flex justify-between py-1.5 border-b border-gray-50 text-sm">
            <span class="text-gray-500">Published</span>
            <span class="font-bold text-green-600">{{ stats.publishedCount }}</span>
          </div>
          <div class="flex justify-between py-1.5 border-b border-gray-50 text-sm">
            <span class="text-gray-500">Drafts</span>
            <span class="font-bold text-yellow-600">{{ stats.draftCount }}</span>
          </div>
          <div class="flex justify-between py-1.5 border-b border-gray-50 text-sm">
            <span class="text-gray-500">Live Matches</span>
            <span class="font-bold text-[#EF5350]">{{ stats.liveMatches }}</span>
          </div>
          <div class="flex justify-between py-1.5 border-b border-gray-50 text-sm">
            <span class="text-gray-500">Categories</span>
            <span class="font-bold text-purple-600">{{ stats.categories }}</span>
          </div>
          <div class="flex justify-between py-1.5 text-sm">
            <span class="text-gray-500">Total Views</span>
            <span class="font-black text-gray-900">{{ (stats.totalViews / 1000).toFixed(0) }}K</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Articles (admin/editor) -->
    <div v-if="isAdminOrEditor" class="bg-white rounded-xl border border-gray-100 shadow-sm">
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <h2 class="font-bold text-gray-800 text-sm">Recent Articles</h2>
        <Link href="/dashboard/articles" class="text-xs font-semibold text-[#0D47A1] hover:underline">
          View all →
        </Link>
      </div>
      <div class="divide-y divide-gray-50">
        <div
          v-for="art in recentArticles"
          :key="art.id"
          class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50 transition-colors"
        >
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-800 truncate">{{ art.title }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ art.category }} · {{ art.author }}</p>
          </div>
          <span :class="['text-[11px] font-semibold px-2 py-0.5 rounded-full shrink-0', statusClass(art.status)]">
            {{ art.status }}
          </span>
          <span class="text-xs text-gray-400 hidden sm:block shrink-0">{{ (art.views / 1000).toFixed(1) }}k</span>
        </div>
      </div>
    </div>

    <!-- Viewer-only message -->
    <div v-if="isViewer" class="bg-white rounded-xl border border-gray-100 shadow-sm p-8 text-center">
      <Eye :size="36" class="text-gray-200 mx-auto mb-3" />
      <p class="text-gray-500 text-sm">You have view-only access. Contact an admin to get more permissions.</p>
    </div>

  </DashboardLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { Newspaper, Eye, Radio, Tag, ShieldCheck, UserCheck } from 'lucide-vue-next'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { useDashboard } from '@/composables/useDashboard.js'

const { articles, stats, chartData } = useDashboard()

const page = usePage()
const auth = computed(() => page.props.auth ?? {})
const roles = computed(() => auth.value?.roles ?? [])

const isAdmin        = computed(() => roles.value.includes('admin'))
const isAdminOrEditor = computed(() => roles.value.includes('admin') || roles.value.includes('editor'))
const isViewer       = computed(() => !isAdminOrEditor.value)

const roleBanners = {
  admin: {
    title: 'Welcome back',
    message: 'You have full admin access. Manage users, roles, content, and live scores.',
    icon: ShieldCheck,
    bg: 'bg-blue-50',
    iconBg: 'bg-blue-100',
    iconColor: 'text-[#0D47A1]',
    badgeBg: 'bg-[#0D47A1]',
    badgeText: 'text-white',
  },
  editor: {
    title: 'Welcome back',
    message: 'You can create and publish articles and manage categories.',
    icon: Newspaper,
    bg: 'bg-green-50',
    iconBg: 'bg-green-100',
    iconColor: 'text-green-600',
    badgeBg: 'bg-green-600',
    badgeText: 'text-white',
  },
  viewer: {
    title: 'Welcome',
    message: 'You have read-only access to the dashboard.',
    icon: UserCheck,
    bg: 'bg-gray-50',
    iconBg: 'bg-gray-100',
    iconColor: 'text-gray-500',
    badgeBg: 'bg-gray-200',
    badgeText: 'text-gray-700',
  },
}

const banner = computed(() => {
  if (isAdmin.value) return roleBanners.admin
  if (roles.value.includes('editor')) return roleBanners.editor
  return roleBanners.viewer
})

const maxViews = computed(() => Math.max(...chartData.value.map(d => d.views)))
const totalWeekViews = computed(() => chartData.value.reduce((s, d) => s + d.views, 0))
const barHeight = (views) => `${Math.round((views / maxViews.value) * 100)}%`

const recentArticles = computed(() => articles.value.slice(0, 6))

const statCards = computed(() => {
  const base = [
    { label: 'Total Articles', value: stats.value.totalArticles, icon: Newspaper, bg: 'bg-blue-50',   color: 'text-[#0D47A1]',   sub: `${stats.value.publishedCount} published` },
    { label: 'Total Views',    value: (stats.value.totalViews / 1000).toFixed(0) + 'K', icon: Eye, bg: 'bg-green-50', color: 'text-green-600', sub: 'all time' },
  ]
  if (isAdminOrEditor.value) {
    base.push(
      { label: 'Live Matches', value: stats.value.liveMatches, icon: Radio, bg: 'bg-red-50',    color: 'text-[#EF5350]',  sub: 'currently live' },
      { label: 'Categories',   value: stats.value.categories,  icon: Tag,   bg: 'bg-purple-50', color: 'text-purple-600', sub: 'active categories' },
    )
  }
  return base
})

const statusClass = (s) => s === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'
</script>
