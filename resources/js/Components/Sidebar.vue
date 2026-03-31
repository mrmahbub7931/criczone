<script setup>
import { ref, computed, onMounted } from 'vue'
import { TrendingUp, Flame, Mail, Send, Eye, CheckCircle2, AlertCircle, Loader2 } from 'lucide-vue-next'
import axios from 'axios'
import { useSettings } from '@/composables/useSettings.js'
import { useMenu }     from '@/composables/useMenu.js'
import PollWidget      from '@/Components/PollWidget.vue'

const { get: setting } = useSettings()
const newsletterEnabled = computed(() => setting('newsletter', 'newsletter_enabled', '1') === '1')

// ── Most Read This Week ───────────────────────────────────────────────────────
const mostRead        = ref([])
const mostReadLoading = ref(true)

// Quick Links — driven by footer_quick menu; static fallback if empty
const { items: quickMenuItems } = useMenu('footer_quick')
const quickLinksFallback = [
  { label: 'Live Scores', url: '/live-scores' },
  { label: 'Search',      url: '/search'      },
  { label: 'T20',         url: '/category/t20'},
  { label: 'IPL',         url: '/category/ipl'},
  { label: 'Test',        url: '/category/test'},
  { label: 'ODI',         url: '/category/odi' },
]
const quickLinks = computed(() => quickMenuItems.value.length ? quickMenuItems.value : quickLinksFallback)

// ── Trending ─────────────────────────────────────────────────────────────────
const trending        = ref([])
const trendingLoading = ref(true)

onMounted(async () => {
  const [trendRes, mostRes] = await Promise.allSettled([
    axios.get('/api/articles/trending'),
    axios.get('/api/articles/most-read-week'),
  ])
  if (trendRes.status === 'fulfilled') trending.value  = trendRes.value.data ?? []
  if (mostRes.status  === 'fulfilled') mostRead.value  = mostRes.value.data  ?? []
  trendingLoading.value = false
  mostReadLoading.value = false
})

const formatViews = (n) => {
  if (!n && n !== 0) return '0'
  if (n >= 1_000_000) return (n / 1_000_000).toFixed(1).replace(/\.0$/, '') + 'M'
  if (n >= 1_000)     return (n / 1_000).toFixed(1).replace(/\.0$/, '') + 'K'
  return String(n)
}

// ── Newsletter ────────────────────────────────────────────────────────────────
const email       = ref('')
const subName     = ref('')
const submitting  = ref(false)
const subStatus   = ref(null) // null | 'success' | 'error'
const subMessage  = ref('')

const subscribe = async () => {
  if (!email.value.trim() || submitting.value) return
  submitting.value = true
  subStatus.value  = null

  try {
    const { data } = await axios.post('/api/newsletter/subscribe', {
      email: email.value.trim(),
      name:  subName.value.trim() || undefined,
    })
    subStatus.value  = 'success'
    subMessage.value = data.message ?? 'Successfully subscribed!'
    email.value   = ''
    subName.value = ''
  } catch (err) {
    subStatus.value  = 'error'
    subMessage.value = err.response?.data?.message
      ?? err.response?.data?.errors?.email?.[0]
      ?? 'Failed to subscribe. Please try again.'
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <aside class="space-y-6">

    <!-- ── Trending Now ───────────────────────────────────────────────────── -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100">
        <TrendingUp class="w-4 h-4 text-secondary" />
        <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">Trending Now</h3>
      </div>

      <!-- Skeleton -->
      <ul v-if="trendingLoading" class="divide-y divide-gray-50">
        <li v-for="i in 5" :key="i" class="flex items-start gap-3 px-5 py-3 animate-pulse">
          <div class="w-7 h-7 rounded-lg bg-gray-100 shrink-0 mt-0.5" />
          <div class="flex-1 space-y-1.5">
            <div class="h-3.5 bg-gray-100 rounded w-full" />
            <div class="h-3 bg-gray-50 rounded w-1/3" />
          </div>
        </li>
      </ul>

      <!-- Results -->
      <ul v-else-if="trending.length" class="divide-y divide-gray-50">
        <li
          v-for="(topic, i) in trending"
          :key="topic.id"
          class="hover:bg-gray-50 transition-colors duration-150 group"
        >
          <a :href="`/article/${topic.slug}`" class="flex items-start gap-3 px-5 py-3 w-full">
            <span
              class="flex-shrink-0 w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold mt-0.5"
              :class="i < 3 ? 'bg-secondary/10 text-secondary' : 'bg-gray-100 text-gray-400'"
            >{{ i + 1 }}</span>
            <div class="min-w-0">
              <p class="text-sm font-semibold text-gray-800 group-hover:text-primary transition-colors duration-150 leading-snug">
                {{ topic.title }}
              </p>
              <span class="flex items-center gap-1 text-xs text-gray-400 mt-1">
                <Eye class="w-3 h-3" />
                {{ formatViews(topic.views) }} views
              </span>
            </div>
          </a>
        </li>
      </ul>

      <!-- Empty fallback -->
      <ul v-else class="divide-y divide-gray-50">
        <li
          v-for="(topic, i) in [
            { title: 'IPL 2026 Points Table',        views: 0, slug: '#' },
            { title: 'T20 World Cup Squad Announced', views: 0, slug: '#' },
            { title: 'WTC Final 2026 Venue Confirmed',views: 0, slug: '#' },
            { title: 'Border-Gavaskar Trophy',        views: 0, slug: '#' },
            { title: 'Virat Kohli Century Record',    views: 0, slug: '#' },
          ]"
          :key="i"
          class="flex items-start gap-3 px-5 py-3 hover:bg-gray-50 transition-colors group"
        >
          <span
            class="flex-shrink-0 w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold mt-0.5"
            :class="i < 3 ? 'bg-secondary/10 text-secondary' : 'bg-gray-100 text-gray-400'"
          >{{ i + 1 }}</span>
          <div class="min-w-0">
            <p class="text-sm font-semibold text-gray-800 leading-snug">{{ topic.title }}</p>
            <span class="flex items-center gap-1 text-xs text-gray-400 mt-1">
              <Eye class="w-3 h-3" /> 0 views
            </span>
          </div>
        </li>
      </ul>
    </div>

    <!-- ── Most Read This Week ───────────────────────────────────────────── -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100">
        <Flame class="w-4 h-4 text-orange-500" />
        <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">Most Read</h3>
        <span class="ml-auto text-[10px] font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">This Week</span>
      </div>
      <!-- Skeleton -->
      <ul v-if="mostReadLoading" class="divide-y divide-gray-50">
        <li v-for="i in 4" :key="i" class="flex items-center gap-3 px-5 py-3 animate-pulse">
          <div class="w-6 h-6 bg-gray-100 rounded-md shrink-0" />
          <div class="flex-1 h-4 bg-gray-100 rounded" />
        </li>
      </ul>
      <ul v-else-if="mostRead.length" class="divide-y divide-gray-50">
        <li
          v-for="(item, i) in mostRead"
          :key="item.id"
          class="hover:bg-gray-50 transition-colors group"
        >
          <a :href="`/article/${item.slug}`" class="flex items-start gap-3 px-5 py-3 w-full">
            <span
              class="flex-shrink-0 w-6 h-6 rounded-md flex items-center justify-center text-[11px] font-black mt-0.5"
              :class="i === 0 ? 'bg-orange-100 text-orange-600' : i === 1 ? 'bg-orange-50 text-orange-500' : 'bg-gray-100 text-gray-400'"
            >{{ i + 1 }}</span>
            <div class="min-w-0">
              <p class="text-sm font-semibold text-gray-800 group-hover:text-primary transition-colors leading-snug line-clamp-2">
                {{ item.title }}
              </p>
              <span class="flex items-center gap-1 text-xs text-gray-400 mt-1">
                <Eye class="w-3 h-3" />
                {{ formatViews(item.views) }} views
              </span>
            </div>
          </a>
        </li>
      </ul>
      <p v-else class="px-5 py-4 text-xs text-gray-400">No articles this week yet.</p>
    </div>

    <!-- ── Poll Widget ────────────────────────────────────────────────────── -->
    <PollWidget />

    <!-- ── Newsletter ─────────────────────────────────────────────────────── -->
    <div v-if="newsletterEnabled" class="bg-gradient-to-br from-primary to-primary-dark rounded-xl p-5 text-white shadow-lg">
      <div class="flex items-center gap-2 mb-1">
        <Mail class="w-5 h-5 text-secondary-light" />
        <h3 class="font-bold text-sm uppercase tracking-wider">Newsletter</h3>
      </div>
      <p class="text-white/70 text-xs mb-4 leading-relaxed">
        Get the latest cricket updates delivered straight to your inbox. No spam, we promise.
      </p>

      <!-- Success -->
      <div v-if="subStatus === 'success'" class="flex items-start gap-2.5 bg-white/10 rounded-lg p-3.5">
        <CheckCircle2 class="w-4 h-4 text-secondary-light shrink-0 mt-0.5" />
        <p class="text-xs text-white/90 leading-relaxed">{{ subMessage }}</p>
      </div>

      <!-- Form -->
      <div v-else class="space-y-2.5">
        <input
          v-model="subName"
          type="text"
          placeholder="Your name (optional)"
          class="w-full px-4 py-2.5 rounded-lg text-sm text-gray-900 placeholder-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-secondary"
        />
        <input
          v-model="email"
          type="email"
          placeholder="Enter your email"
          class="w-full px-4 py-2.5 rounded-lg text-sm text-gray-900 placeholder-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-secondary"
          @keydown.enter="subscribe"
        />

        <!-- Error -->
        <div v-if="subStatus === 'error'" class="flex items-start gap-2 bg-red-500/20 rounded-lg px-3 py-2">
          <AlertCircle class="w-3.5 h-3.5 text-red-300 shrink-0 mt-0.5" />
          <p class="text-xs text-red-200 leading-relaxed">{{ subMessage }}</p>
        </div>

        <button
          @click="subscribe"
          :disabled="submitting || !email.trim()"
          class="w-full flex items-center justify-center gap-2 bg-secondary hover:bg-secondary-dark disabled:opacity-60 text-white py-2.5 rounded-lg font-bold text-sm transition-colors duration-200 shadow-md"
        >
          <Loader2 v-if="submitting" class="w-3.5 h-3.5 animate-spin" />
          <Send v-else class="w-3.5 h-3.5" />
          {{ submitting ? 'Subscribing…' : 'Subscribe' }}
        </button>
      </div>
    </div>

    <!-- ── Quick Links (driven by footer_quick menu) ────────────────────── -->
    <div class="bg-white rounded-xl shadow-sm p-5">
      <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider mb-3">Quick Links</h3>
      <div class="grid grid-cols-2 gap-2">
        <a
          v-for="link in quickLinks"
          :key="link.label ?? link.id"
          :href="link.url ?? link.href"
          :target="link.target ?? '_self'"
          class="text-xs font-medium text-gray-500 hover:text-primary bg-gray-50 hover:bg-primary/5 rounded-lg px-3 py-2 text-center transition-colors duration-150"
        >{{ link.label }}</a>
      </div>
    </div>

  </aside>
</template>
