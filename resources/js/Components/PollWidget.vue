<script setup>
import { ref, computed, onMounted } from 'vue'
import { BarChart2, CheckCircle2, Loader2 } from 'lucide-vue-next'
import axios from 'axios'
import { useSettings } from '@/composables/useSettings.js'

const { get: setting, loaded: settingsLoaded } = useSettings()
const pollEnabled = computed(() => setting('poll', 'poll_enabled', '0') === '1')

const poll       = ref(null)
const fetching   = ref(true)
const voting     = ref(false)
const selected   = ref(null)
const voteError  = ref('')

const totalVotes = computed(() => poll.value?.total_votes ?? 0)

const percent = (votes) => {
  if (!totalVotes.value) return 0
  return Math.round((votes / totalVotes.value) * 100)
}

const fetchPoll = async () => {
  try {
    const { data } = await axios.get('/api/poll/active')
    poll.value = data
    if (data?.has_voted) selected.value = -1 // already voted
  } catch { poll.value = null }
  finally { fetching.value = false }
}

const submitVote = async () => {
  if (selected.value === null || selected.value < 0 || voting.value) return
  voting.value = true
  voteError.value = ''
  try {
    const { data } = await axios.post(`/api/poll/${poll.value.id}/vote`, {
      option_index: selected.value,
    })
    poll.value = { ...poll.value, ...data }
  } catch (err) {
    voteError.value = err.response?.data?.message ?? 'Could not submit vote.'
  } finally {
    voting.value = false
  }
}

onMounted(fetchPoll)
</script>

<template>
  <!-- Only render when poll is enabled in settings AND an active poll exists -->
  <div
    v-if="pollEnabled && !fetching && poll"
    class="bg-white rounded-xl shadow-sm overflow-hidden"
  >
    <!-- Header -->
    <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100">
      <BarChart2 class="w-4 h-4 text-secondary" />
      <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">Quick Poll</h3>
    </div>

    <div class="px-5 py-4">
      <!-- Question -->
      <p class="text-sm font-bold text-gray-800 mb-4 leading-snug">{{ poll.question }}</p>

      <!-- Results view (after voting) -->
      <div v-if="poll.has_voted" class="space-y-2.5">
        <div v-for="(opt, i) in poll.options" :key="i">
          <div class="flex justify-between text-xs mb-1">
            <span class="font-medium text-gray-700 truncate max-w-[70%]">{{ opt.option }}</span>
            <span class="font-bold text-gray-500">{{ percent(opt.votes) }}%</span>
          </div>
          <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
            <div
              class="h-full rounded-full bg-gradient-to-r from-secondary to-secondary-dark transition-all duration-700"
              :style="{ width: percent(opt.votes) + '%' }"
            />
          </div>
        </div>
        <p class="text-[11px] text-gray-400 mt-3 flex items-center gap-1">
          <CheckCircle2 class="w-3 h-3 text-emerald-500" />
          {{ totalVotes }} {{ totalVotes === 1 ? 'vote' : 'votes' }} total
        </p>
      </div>

      <!-- Voting form -->
      <div v-else class="space-y-2">
        <label
          v-for="(opt, i) in poll.options"
          :key="i"
          :class="[
            'flex items-center gap-3 px-3 py-2.5 rounded-lg cursor-pointer border transition-all duration-150',
            selected === i
              ? 'border-secondary bg-secondary/5'
              : 'border-gray-100 bg-gray-50 hover:border-gray-200 hover:bg-gray-100'
          ]"
        >
          <input type="radio" :value="i" v-model="selected" class="accent-secondary" />
          <span class="text-sm text-gray-700 leading-snug">{{ opt.option }}</span>
        </label>

        <p v-if="voteError" class="text-xs text-red-500 px-1">{{ voteError }}</p>

        <button
          @click="submitVote"
          :disabled="selected === null || voting"
          class="mt-1 w-full flex items-center justify-center gap-2 py-2 bg-secondary hover:bg-secondary-dark disabled:opacity-50 text-white text-xs font-bold rounded-lg transition-colors"
        >
          <Loader2 v-if="voting" class="w-3.5 h-3.5 animate-spin" />
          {{ voting ? 'Submitting…' : 'Vote Now' }}
        </button>
        <p class="text-[11px] text-center text-gray-400">{{ totalVotes }} votes so far</p>
      </div>
    </div>
  </div>
</template>
