<script setup>
/**
 * Browser Push Notifications — uses the Web Notifications API.
 * - Requests permission on click
 * - Polls /api/articles/latest and /api/live-scores for new content
 * - Shows browser notifications when breaking news or new live scores appear
 * - Stores opt-in state in localStorage
 */
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { Bell, BellOff } from 'lucide-vue-next'
import axios from 'axios'
import { useSettings } from '@/composables/useSettings.js'

const { get: setting } = useSettings()
const siteName = computed(() => setting('general', 'site_name', 'CricZone'))

const STORAGE_KEY   = 'cz_push_enabled'
const POLL_INTERVAL = 90_000 // 90 seconds

const supported  = ref(typeof Notification !== 'undefined')
const enabled    = ref(false)
const permission = ref(Notification?.permission ?? 'default')
const showTip    = ref(false)

let   lastArticleId  = null
let   lastLiveCount  = null
let   pollingTimer   = null

// Initialise from stored preference
onMounted(() => {
  enabled.value    = localStorage.getItem(STORAGE_KEY) === '1'
  permission.value = Notification?.permission ?? 'default'
  if (enabled.value && permission.value === 'granted') startPolling()
})

onUnmounted(() => stopPolling())

const toggle = async () => {
  if (!supported.value) return

  if (enabled.value) {
    // Turn off
    enabled.value = false
    localStorage.setItem(STORAGE_KEY, '0')
    stopPolling()
    return
  }

  // Request permission
  if (permission.value !== 'granted') {
    const result = await Notification.requestPermission()
    permission.value = result
    if (result !== 'granted') {
      showTip.value = true
      setTimeout(() => { showTip.value = false }, 3500)
      return
    }
  }

  enabled.value = true
  localStorage.setItem(STORAGE_KEY, '1')
  startPolling()
  // Confirm notification
  new Notification(`${siteName.value} Alerts`, {
    body: 'You\'ll now receive breaking news & live score alerts.',
    icon: '/favicon.ico',
  })
}

const notify = (title, body, url = '/') => {
  if (permission.value !== 'granted') return
  const n = new Notification(title, { body, icon: '/favicon.ico', tag: url })
  n.onclick = () => { window.focus(); window.location.href = url; n.close() }
}

const startPolling = () => {
  if (pollingTimer) return
  // Seed initial state without notifying
  seedState()
  pollingTimer = setInterval(checkForUpdates, POLL_INTERVAL)
}

const stopPolling = () => {
  clearInterval(pollingTimer)
  pollingTimer = null
}

const seedState = async () => {
  try {
    const [articles, scores] = await Promise.all([
      axios.get('/api/articles/latest', { params: { per_page: 1 } }),
      axios.get('/api/live-scores'),
    ])
    lastArticleId = articles.data?.data?.[0]?.id ?? null
    lastLiveCount = scores.data?.filter(s => s.is_live)?.length ?? 0
  } catch { /* silent */ }
}

const checkForUpdates = async () => {
  if (!enabled.value || permission.value !== 'granted') return
  try {
    // Check latest articles
    const { data: artData } = await axios.get('/api/articles/latest', { params: { per_page: 1 } })
    const newestId = artData?.data?.[0]?.id
    if (newestId && lastArticleId && newestId !== lastArticleId) {
      const art = artData.data[0]
      notify(
        `📰 ${siteName.value}: New Article`,
        art.title,
        `/article/${art.slug}`
      )
    }
    if (newestId) lastArticleId = newestId

    // Check live scores
    const { data: scores } = await axios.get('/api/live-scores')
    const liveCount = scores.filter(s => s.is_live).length
    if (lastLiveCount !== null && liveCount > lastLiveCount) {
      notify(
        `🏏 ${siteName.value}: Live Match Started`,
        `${liveCount} match${liveCount !== 1 ? 'es' : ''} live right now`,
        '/live-scores'
      )
    }
    lastLiveCount = liveCount
  } catch { /* silent */ }
}
</script>

<template>
  <div class="relative" v-if="supported">
    <button
      @click="toggle"
      :title="enabled ? 'Disable notifications' : 'Enable notifications'"
      :class="[
        'w-9 h-9 rounded-full flex items-center justify-center transition-all',
        enabled
          ? 'text-secondary bg-white/10 hover:bg-white/20'
          : 'text-white/50 hover:text-white hover:bg-white/10'
      ]"
    >
      <component :is="enabled ? Bell : BellOff" class="w-[17px] h-[17px]" />
      <!-- Active pulse dot -->
      <span
        v-if="enabled"
        class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-secondary animate-pulse border border-white"
      />
    </button>

    <!-- Permission denied tooltip -->
    <Transition
      enter-active-class="transition-all duration-200"
      enter-from-class="opacity-0 translate-y-1"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition-all duration-150"
      leave-to-class="opacity-0"
    >
      <div
        v-if="showTip"
        class="absolute top-full right-0 mt-2 w-52 bg-gray-900 text-white text-xs rounded-xl px-3 py-2.5 shadow-xl z-50"
      >
        Notifications blocked. Enable them in your browser settings.
      </div>
    </Transition>
  </div>
</template>
