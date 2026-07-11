<template>
  <DashboardLayout>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-lg font-black text-gray-900">Tags</h2>
        <p class="text-sm text-gray-400">{{ tags.length }} tag{{ tags.length === 1 ? '' : 's' }}</p>
      </div>
      <button @click="openCreate" class="flex items-center gap-2 bg-[#0D47A1] hover:bg-[#0a2f6e] text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm">
        <Plus :size="15" /> Create Tag
      </button>
    </div>

    <!-- Search -->
    <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-3 py-2 mb-4 max-w-xs">
      <Search :size="14" class="text-gray-400 shrink-0" />
      <input v-model="search" type="text" placeholder="Search tags…" class="text-sm text-gray-700 placeholder-gray-400 outline-none w-full bg-transparent" />
    </div>

    <!-- Toast -->
    <Transition name="fade-up">
      <div v-if="toast" class="fixed bottom-6 right-6 z-50 bg-gray-900 text-white text-sm font-medium px-4 py-3 rounded-xl shadow-lg">{{ toast }}</div>
    </Transition>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
              <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Name</th>
              <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Slug</th>
              <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Articles</th>
              <th class="px-4 py-3 w-20"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="loading"><td colspan="4" class="text-center py-12 text-gray-400 text-sm">Loading…</td></tr>
            <tr v-else-if="filtered.length === 0"><td colspan="4" class="text-center py-12 text-gray-400 text-sm">No tags found.</td></tr>
            <tr v-for="tag in filtered" :key="tag.id" class="hover:bg-gray-50 transition-colors group">
              <td class="px-5 py-3.5">
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-primary/10 text-primary">
                  <Tags :size="11" /> {{ tag.name }}
                </span>
              </td>
              <td class="px-4 py-3.5 text-xs text-gray-400 font-mono hidden sm:table-cell">{{ tag.slug }}</td>
              <td class="px-4 py-3.5">
                <a :href="`/tag/${tag.slug}`" target="_blank" class="text-xs text-gray-500 hover:text-primary transition-colors">
                  {{ tag.articles_count }} article{{ tag.articles_count === 1 ? '' : 's' }}
                </a>
              </td>
              <td class="px-4 py-3.5">
                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity justify-end">
                  <button @click="openEdit(tag)" class="p-1.5 text-gray-400 hover:text-[#0D47A1] hover:bg-blue-50 rounded-lg transition-colors"><Pencil :size="14" /></button>
                  <button @click="confirmDelete(tag)" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"><Trash2 :size="14" /></button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create / Edit Modal -->
    <Transition name="modal">
      <div v-if="modal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="closeModal" />
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
          <h3 class="font-black text-gray-900 text-base mb-5">{{ editId ? 'Edit Tag' : 'Create Tag' }}</h3>

          <div class="space-y-4">
            <div>
              <label class="label">Tag Name *</label>
              <input
                v-model="form.name"
                @keydown.enter.prevent="save"
                ref="nameInput"
                type="text"
                :class="field(errors.name)"
                placeholder="e.g. IPL, Virat Kohli, T20 World Cup"
              />
              <p v-if="errors.name" class="text-xs text-red-500 mt-1">{{ errors.name }}</p>
              <p v-if="form.name" class="text-xs text-gray-400 mt-1">Slug: {{ slugPreview }}</p>
            </div>
          </div>

          <p v-if="errors.general" class="text-xs text-red-500 bg-red-50 px-3 py-2 rounded-lg mt-4">{{ errors.general }}</p>

          <div class="flex gap-3 mt-6">
            <button @click="closeModal" class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
            <button @click="save" :disabled="saving" class="flex-1 bg-[#0D47A1] hover:bg-[#0a2f6e] disabled:opacity-50 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors">
              {{ saving ? 'Saving…' : (editId ? 'Save Changes' : 'Create Tag') }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Delete Confirm -->
    <Transition name="modal">
      <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="deleteTarget = null" />
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
          <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4"><Trash2 :size="20" class="text-red-500" /></div>
          <h3 class="font-black text-gray-900 mb-2">Delete Tag?</h3>
          <p class="text-sm text-gray-500 mb-1">"{{ deleteTarget?.name }}"</p>
          <p class="text-xs text-gray-400 mb-5">This will remove the tag from all {{ deleteTarget?.articles_count }} article(s).</p>
          <div class="flex gap-3">
            <button @click="deleteTarget = null" class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50">Cancel</button>
            <button @click="doDelete" class="flex-1 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors">Delete</button>
          </div>
        </div>
      </div>
    </Transition>

  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import axios from 'axios'
import { Plus, Search, Pencil, Trash2, Tags } from 'lucide-vue-next'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

// ── State ─────────────────────────────────────────────────────────────────
const tags        = ref([])
const loading     = ref(false)
const search      = ref('')
const modal       = ref(false)
const editId      = ref(null)
const saving      = ref(false)
const errors      = ref({})
const deleteTarget = ref(null)
const toast       = ref('')
const nameInput   = ref(null)
const form        = ref({ name: '' })

// ── Computed ──────────────────────────────────────────────────────────────
const slugPreview = computed(() =>
  form.value.name.trim().toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '').replace(/-+/g, '-').replace(/^-|-$/g, '')
)

const filtered = computed(() => {
  const q = search.value.trim().toLowerCase()
  return q ? tags.value.filter(t => t.name.toLowerCase().includes(q) || t.slug.includes(q)) : tags.value
})

// ── Fetch ─────────────────────────────────────────────────────────────────
const fetchTags = async () => {
  loading.value = true
  const { data } = await axios.get('/api/tags')
  tags.value = data
  loading.value = false
}

onMounted(fetchTags)

// ── Helpers ───────────────────────────────────────────────────────────────
const showToast = (msg) => { toast.value = msg; setTimeout(() => { toast.value = '' }, 2500) }
const field = (err) =>
  `w-full border rounded-lg px-3 py-2 text-sm outline-none transition-colors ${err ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-[#0D47A1]'}`

// ── CRUD ──────────────────────────────────────────────────────────────────
const openCreate = async () => {
  editId.value = null; form.value = { name: '' }; errors.value = {}; modal.value = true
  await nextTick(); nameInput.value?.focus()
}

const openEdit = async (tag) => {
  editId.value = tag.id; form.value = { name: tag.name }; errors.value = {}; modal.value = true
  await nextTick(); nameInput.value?.focus()
}

const closeModal = () => { modal.value = false }

const save = async () => {
  if (!form.value.name.trim()) { errors.value.name = 'Name is required.'; return }
  saving.value = true; errors.value = {}
  try {
    editId.value
      ? await axios.put(`/api/tags/${editId.value}`, form.value)
      : await axios.post('/api/tags', form.value)
    modal.value = false
    showToast(editId.value ? 'Tag updated.' : 'Tag created.')
    fetchTags()
  } catch (e) {
    const data = e.response?.data
    if (data?.errors) Object.keys(data.errors).forEach(k => { errors.value[k] = data.errors[k][0] })
    else errors.value.general = data?.message ?? 'Something went wrong.'
  } finally {
    saving.value = false
  }
}

const confirmDelete = (tag) => { deleteTarget.value = tag }
const doDelete = async () => {
  await axios.delete(`/api/tags/${deleteTarget.value.id}`)
  deleteTarget.value = null
  showToast('Tag deleted.')
  fetchTags()
}
</script>

<style scoped>
.label { @apply block text-xs font-semibold text-gray-600 mb-1; }
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
.fade-up-enter-active, .fade-up-leave-active { transition: opacity 0.2s, transform 0.2s; }
.fade-up-enter-from, .fade-up-leave-to { opacity: 0; transform: translateY(8px); }
</style>
