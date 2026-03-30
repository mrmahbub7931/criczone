<template>
  <DashboardLayout>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-lg font-black text-gray-900">Live Scores</h2>
        <p class="text-sm text-gray-400">{{ liveScores.length }} active matches</p>
      </div>
      <button
        @click="openAdd"
        class="flex items-center gap-2 bg-secondary hover:bg-secondary-dark text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm"
      >
        <Plus :size="16" /> Add Match
      </button>
    </div>

    <!-- Match cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <div
        v-for="match in liveScores"
        :key="match.id"
        class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden group hover:shadow-md transition-shadow"
      >
        <!-- Live badge + status -->
        <div class="flex items-center justify-between px-5 py-3 bg-gray-50 border-b border-gray-100">
          <div class="flex items-center gap-2">
            <span class="flex items-center gap-1.5 text-[11px] font-bold text-secondary uppercase tracking-wide">
              <span class="w-1.5 h-1.5 rounded-full bg-secondary animate-pulse" />
              Live
            </span>
            <span class="text-xs text-gray-500">{{ match.status }}</span>
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
              <p class="text-2xl font-black text-gray-900 font-mono">{{ match.team1 }}</p>
              <p class="text-lg font-bold text-primary font-mono mt-1">{{ match.score1 }}</p>
            </div>
            <div class="text-center">
              <span class="text-xs font-bold text-gray-400 bg-gray-100 px-3 py-1 rounded-full">VS</span>
              <p class="text-xs text-gray-400 mt-1">{{ match.overs }} ov</p>
            </div>
            <div class="text-center flex-1">
              <p class="text-2xl font-black text-gray-900 font-mono">{{ match.team2 }}</p>
              <p class="text-lg font-bold text-gray-400 font-mono mt-1">{{ match.score2 }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div v-if="liveScores.length === 0" class="bg-white rounded-xl border border-gray-100 shadow-sm text-center py-16">
      <Radio :size="40" class="text-gray-200 mx-auto mb-3" />
      <p class="text-gray-400 text-sm">No live matches. Add one to get started.</p>
    </div>

    <!-- ── Add / Edit Modal ── -->
    <Transition name="modal">
      <div v-if="modal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="modal = false" />
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
          <h3 class="font-black text-gray-900 text-base mb-5">{{ editId ? 'Edit Match' : 'Add Match' }}</h3>

          <div class="space-y-4">
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Team 1 *</label>
                <input v-model="form.team1" type="text" maxlength="10" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary font-mono uppercase" placeholder="IND" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Team 2 *</label>
                <input v-model="form.team2" type="text" maxlength="10" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary font-mono uppercase" placeholder="AUS" />
              </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Score 1</label>
                <input v-model="form.score1" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary font-mono" placeholder="287/4" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Score 2</label>
                <input v-model="form.score2" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary font-mono" placeholder="---" />
              </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                <input v-model="form.status" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary" placeholder="Day 2 - Session 2" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Overs</label>
                <input v-model="form.overs" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary font-mono" placeholder="78.3" />
              </div>
            </div>
          </div>

          <div class="flex gap-3 mt-6">
            <button @click="modal = false" class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
            <button @click="saveMatch" :disabled="!form.team1 || !form.team2" class="flex-1 bg-secondary hover:bg-secondary-dark disabled:opacity-50 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors">
              {{ editId ? 'Save Changes' : 'Add Match' }}
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
          <p class="text-sm text-gray-500 mb-5">{{ removeTarget?.team1 }} vs {{ removeTarget?.team2 }}</p>
          <div class="flex gap-3">
            <button @click="removeTarget = null" class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
            <button @click="doRemove" class="flex-1 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors">Remove</button>
          </div>
        </div>
      </div>
    </Transition>

  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Plus, Pencil, Trash2, Radio } from 'lucide-vue-next'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { useDashboard } from '@/composables/useDashboard.js'

const { liveScores, addLiveScore, updateLiveScore, removeLiveScore } = useDashboard()

const modal        = ref(false)
const editId       = ref(null)
const removeTarget = ref(null)

const blankForm = () => ({ team1: '', team2: '', score1: '---', score2: '---', status: '', overs: '0.0' })
const form = ref(blankForm())

const openAdd = () => {
  editId.value = null
  form.value = blankForm()
  modal.value = true
}

const openEdit = (match) => {
  editId.value = match.id
  form.value = { team1: match.team1, team2: match.team2, score1: match.score1, score2: match.score2, status: match.status, overs: match.overs }
  modal.value = true
}

const saveMatch = () => {
  if (editId.value) {
    updateLiveScore(editId.value, { ...form.value })
  } else {
    addLiveScore({ ...form.value })
  }
  modal.value = false
}

const confirmRemove = (match) => { removeTarget.value = match }
const doRemove = () => {
  removeLiveScore(removeTarget.value.id)
  removeTarget.value = null
}
</script>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
