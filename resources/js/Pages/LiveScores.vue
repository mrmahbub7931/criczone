<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">

      <!-- Header -->
      <div class="flex items-center gap-4 mb-8">
        <div class="flex items-center gap-3">
          <div class="h-8 w-1 bg-secondary rounded-full" />
          <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tight">
            Live <span class="text-secondary">Scores</span>
          </h1>
        </div>
        <span v-if="liveCount" class="flex items-center gap-1.5 text-xs font-bold text-secondary bg-secondary/10 px-3 py-1 rounded-full">
          <span class="w-1.5 h-1.5 rounded-full bg-secondary animate-pulse" />
          {{ liveCount }} Live
        </span>
        <span class="ml-auto text-xs text-gray-400">Auto-updates every 60s</span>
      </div>

      <!-- Skeleton -->
      <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <div v-for="i in 3" :key="i" class="bg-white rounded-2xl shadow-sm h-40 animate-pulse" />
      </div>

      <!-- Match cards -->
      <div v-else-if="scores.length" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <div
          v-for="match in scores"
          :key="match.id"
          class="bg-white rounded-2xl shadow-sm border overflow-hidden"
          :class="match.is_live ? 'border-secondary/30' : 'border-gray-100'"
        >
          <!-- Card header -->
          <div
            class="flex items-center justify-between px-4 py-2.5 border-b text-xs font-bold uppercase tracking-wide"
            :class="match.is_live ? 'bg-secondary/5 border-secondary/20 text-secondary' : 'bg-gray-50 border-gray-100 text-gray-400'"
          >
            <span class="flex items-center gap-1.5">
              <span v-if="match.is_live" class="w-1.5 h-1.5 rounded-full bg-secondary animate-pulse" />
              {{ match.is_live ? 'Live' : 'Upcoming' }}
              · {{ match.match_type }}
            </span>
            <span class="font-normal text-gray-500 normal-case">{{ match.status }}</span>
          </div>

          <!-- Score -->
          <div class="px-5 py-5">
            <div class="flex items-center justify-between gap-3">
              <div class="text-center flex-1">
                <p class="text-3xl font-black text-gray-900 font-mono tracking-tight">{{ match.team_a }}</p>
                <p class="text-xl font-bold font-mono mt-1.5" :class="match.is_live ? 'text-primary' : 'text-gray-400'">
                  {{ match.score_a || '—' }}
                </p>
              </div>
              <div class="text-center shrink-0">
                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                  <span class="text-[10px] font-black text-gray-400">VS</span>
                </div>
                <p v-if="match.overs" class="text-[10px] text-gray-400 mt-1 font-mono">{{ match.overs }} ov</p>
              </div>
              <div class="text-center flex-1">
                <p class="text-3xl font-black text-gray-900 font-mono tracking-tight">{{ match.team_b }}</p>
                <p class="text-xl font-bold text-gray-400 font-mono mt-1.5">{{ match.score_b || '—' }}</p>
              </div>
            </div>
            <p v-if="match.venue" class="text-center text-[11px] text-gray-400 mt-4 font-medium">
              📍 {{ match.venue }}
            </p>
          </div>
        </div>
      </div>

      <!-- Empty -->
      <div v-else class="text-center py-20 bg-white rounded-2xl shadow-sm">
        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
          <Radio :size="28" class="text-gray-300" />
        </div>
        <p class="font-bold text-gray-600 mb-1">No matches right now</p>
        <p class="text-sm text-gray-400">Check back soon for live cricket scores.</p>
      </div>

    </div>
  </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import MainLayout from '@/Layouts/MainLayout.vue'
import { Radio } from 'lucide-vue-next'

const scores  = ref([])
const loading = ref(true)
let   timer   = null

const liveCount = computed(() => scores.value.filter(m => m.is_live).length)

const fetchScores = async () => {
  try {
    const { data } = await axios.get('/api/live-scores')
    scores.value = data
  } catch { /* silent */ }
  finally { loading.value = false }
}

onMounted(() => {
  fetchScores()
  // Auto-refresh every 60s
  timer = setInterval(fetchScores, 60_000)
})

onUnmounted(() => clearInterval(timer))
</script>
