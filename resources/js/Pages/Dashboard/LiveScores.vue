<template>
  <DashboardLayout>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-lg font-black text-gray-900">Live Scores</h2>
        <p class="text-sm text-gray-400">{{ liveScores.length }} matches total</p>
      </div>
      <button
        @click="openAdd"
        class="flex items-center gap-2 bg-secondary hover:bg-secondary-dark text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm"
      >
        <Plus :size="16" /> Add Match
      </button>
    </div>

    <!-- Skeleton -->
    <div v-if="pageLoading" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <div v-for="i in 2" :key="i" class="bg-white rounded-xl border border-gray-100 shadow-sm h-32 animate-pulse" />
    </div>

    <!-- Match cards -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <div
        v-for="match in liveScores"
        :key="match.id"
        class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden group hover:shadow-md transition-shadow"
      >
        <!-- Header row -->
        <div class="flex items-center justify-between px-5 py-3 bg-gray-50 border-b border-gray-100">
          <div class="flex items-center gap-2.5">
            <span v-if="match.is_live" class="flex items-center gap-1.5 text-[11px] font-bold text-secondary uppercase tracking-wide">
              <span class="w-1.5 h-1.5 rounded-full bg-secondary animate-pulse" /> Live
            </span>
            <span v-else class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Upcoming</span>
            <span class="text-xs text-gray-400 font-mono">{{ match.match_type }}</span>
            <span class="text-xs text-gray-500">· {{ match.status }}</span>
          </div>
          <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
            <button @click="openEdit(match)" class="p-1.5 text-gray-400 hover:text-primary hover:bg-blue-50 rounded-lg transition-colors">
              <Pencil :size="13" />
            </button>
            <button @click="confirmRemove(match)" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
              <Trash2 :size="13" />
            </button>
          </div>
        </div>

        <!-- Score display -->
        <div class="px-5 py-4">
          <div class="flex items-center justify-between gap-4">
            <div class="text-center flex-1">
              <p class="text-2xl font-black text-gray-900 font-mono">{{ match.team_a }}</p>
              <p class="text-lg font-bold text-primary font-mono mt-1">{{ match.score_a }}</p>
            </div>
            <div class="text-center">
              <span class="text-xs font-bold text-gray-400 bg-gray-100 px-3 py-1 rounded-full">VS</span>
              <p v-if="match.overs" class="text-xs text-gray-400 mt-1">{{ match.overs }} ov</p>
            </div>
            <div class="text-center flex-1">
              <p class="text-2xl font-black text-gray-900 font-mono">{{ match.team_b }}</p>
              <p class="text-lg font-bold text-gray-400 font-mono mt-1">{{ match.score_b }}</p>
            </div>
          </div>
          <p v-if="match.venue" class="text-center text-xs text-gray-400 mt-3">📍 {{ match.venue }}</p>
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div v-if="!pageLoading && liveScores.length === 0" class="bg-white rounded-xl border border-gray-100 shadow-sm text-center py-16">
      <Radio :size="40" class="text-gray-200 mx-auto mb-3" />
      <p class="text-gray-400 text-sm">No matches yet. Add one to get started.</p>
    </div>

    <!-- ── Add / Edit Modal ── -->
    <Transition name="modal">
      <div v-if="modal" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="modal = false">
        <div class="absolute inset-0 bg-black/50" @click="modal = false" />
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
          <h3 class="font-black text-gray-900 text-base mb-5">{{ editId ? 'Edit Match' : 'Add Match' }}</h3>

          <div class="space-y-3">
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Team A *</label>
                <input v-model="form.team_a" type="text" maxlength="10" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary font-mono uppercase" placeholder="IND" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Team B *</label>
                <input v-model="form.team_b" type="text" maxlength="10" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary font-mono uppercase" placeholder="AUS" />
              </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Score A</label>
                <input v-model="form.score_a" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary font-mono" placeholder="287/4" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Score B</label>
                <input v-model="form.score_b" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary font-mono" placeholder="—" />
              </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Match Type</label>
                <select v-model="form.match_type" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary bg-white">
                  <option>T20</option><option>T20I</option><option>ODI</option><option>Test</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Overs</label>
                <input v-model="form.overs" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary font-mono" placeholder="78.3" />
              </div>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
              <input v-model="form.status" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary" placeholder="Day 2 – Session 2" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Venue</label>
              <input v-model="form.venue" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary" placeholder="MCG, Melbourne" />
            </div>
            <label class="flex items-center gap-2 cursor-pointer pt-1">
              <input type="checkbox" v-model="form.is_live" class="w-4 h-4 accent-secondary" />
              <span class="text-sm text-gray-700 font-medium">Mark as Live</span>
            </label>
          </div>

          <div class="flex gap-3 mt-6">
            <button @click="modal = false" class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
            <button @click="saveMatch" :disabled="!form.team_a || !form.team_b || saving" class="flex-1 bg-secondary hover:bg-secondary-dark disabled:opacity-50 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors">
              {{ saving ? 'Saving…' : (editId ? 'Save Changes' : 'Add Match') }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- ── Remove Confirm ── -->
    <Transition name="modal">
      <div v-if="removeTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="removeTarget = null" />
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
          <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <Trash2 :size="20" class="text-red-500" />
          </div>
          <h3 class="font-black text-gray-900 mb-2">Remove Match?</h3>
          <p class="text-sm text-gray-500 mb-5">{{ removeTarget?.team_a }} vs {{ removeTarget?.team_b }}</p>
          <div class="flex gap-3">
            <button @click="removeTarget = null" class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
            <button @click="doRemove" :disabled="saving" class="flex-1 bg-red-500 hover:bg-red-600 disabled:opacity-50 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors">Remove</button>
          </div>
        </div>
      </div>
    </Transition>

  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Plus, Pencil, Trash2, Radio } from 'lucide-vue-next'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { useDashboard } from '@/composables/useDashboard.js'

const { liveScores, fetchLiveScores, addLiveScore, updateLiveScore, removeLiveScore } = useDashboard()

const pageLoading  = ref(true)
const saving       = ref(false)
const modal        = ref(false)
const editId       = ref(null)
const removeTarget = ref(null)

const blankForm = () => ({
  team_a: '', team_b: '', score_a: '—', score_b: '—',
  overs: '', status: '', match_type: 'T20', venue: '', is_live: true,
})
const form = ref(blankForm())

onMounted(async () => {
  await fetchLiveScores()
  pageLoading.value = false
})

const openAdd = () => {
  editId.value = null
  form.value = blankForm()
  modal.value = true
}

const openEdit = (match) => {
  editId.value = match.id
  form.value = {
    team_a: match.team_a, team_b: match.team_b,
    score_a: match.score_a, score_b: match.score_b,
    overs: match.overs ?? '', status: match.status ?? '',
    match_type: match.match_type ?? 'T20', venue: match.venue ?? '',
    is_live: match.is_live,
  }
  modal.value = true
}

const saveMatch = async () => {
  saving.value = true
  try {
    if (editId.value) {
      await updateLiveScore(editId.value, { ...form.value })
    } else {
      await addLiveScore({ ...form.value })
    }
    modal.value = false
  } catch { /* errors handled in composable */ }
  finally { saving.value = false }
}

const confirmRemove = (match) => { removeTarget.value = match }
const doRemove = async () => {
  saving.value = true
  try {
    await removeLiveScore(removeTarget.value.id)
    removeTarget.value = null
  } finally { saving.value = false }
}
</script>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
