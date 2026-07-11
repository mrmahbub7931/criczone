<template>
  <DashboardLayout>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-lg font-black text-gray-900">Categories</h2>
        <p class="text-sm text-gray-400">{{ meta.total ?? categories.length }} categories</p>
      </div>
      <button @click="openAdd" class="flex items-center gap-2 bg-[#0D47A1] hover:bg-[#0a2f6e] text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm">
        <Plus :size="16" /> Add Category
      </button>
    </div>

    <!-- Search -->
    <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-3 py-2 mb-5 w-full max-w-sm">
      <Search :size="14" class="text-gray-400 shrink-0" />
      <input v-model="search" type="text" placeholder="Search categories…"
        class="text-sm text-gray-700 placeholder-gray-400 outline-none w-full bg-transparent" />
    </div>

    <!-- Toast -->
    <Transition name="fade-up">
      <div v-if="toast" class="fixed bottom-6 right-6 z-50 bg-gray-900 text-white text-sm font-medium px-4 py-3 rounded-xl shadow-lg">
        {{ toast }}
      </div>
    </Transition>

    <!-- Grid -->
    <div v-if="loading" class="text-sm text-gray-400 py-12 text-center">Loading…</div>

    <div v-else-if="categories.length === 0" class="text-sm text-gray-400 py-12 text-center">No categories found.</div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div
        v-for="cat in categories" :key="cat.id"
        class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 group hover:shadow-md transition-shadow"
      >
        <div class="flex items-start justify-between mb-4">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" :style="{ backgroundColor: (cat.color || '#0D47A1') + '20' }">
              <Tag :size="18" :style="{ color: cat.color || '#0D47A1' }" />
            </div>
            <div>
              <div class="flex items-center gap-1.5">
                <h3 class="font-bold text-gray-900 text-sm">{{ cat.name }}</h3>
                <span v-if="cat.is_pinned" class="text-amber-400" title="Pinned">📌</span>
              </div>
              <p class="text-xs text-gray-400 font-mono">{{ cat.slug }}</p>
            </div>
          </div>
          <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
            <button @click="toggleFilter(cat)"
              :title="cat.show_in_filter !== false ? 'Hide from Latest News filter' : 'Show in Latest News filter'"
              :class="['p-1.5 rounded-lg transition-colors', cat.show_in_filter !== false ? 'text-blue-500 hover:bg-blue-50' : 'text-gray-300 hover:text-blue-500 hover:bg-blue-50']">
              <Filter :size="13" />
            </button>
            <button @click="togglePin(cat)"
              :title="cat.is_pinned ? 'Unpin' : 'Pin to top'"
              :class="['p-1.5 rounded-lg transition-colors', cat.is_pinned ? 'text-amber-500 hover:bg-amber-50' : 'text-gray-400 hover:text-amber-500 hover:bg-amber-50']">
              <component :is="cat.is_pinned ? PinOff : Pin" :size="13" />
            </button>
            <button @click="openEdit(cat)" class="p-1.5 text-gray-400 hover:text-[#0D47A1] hover:bg-blue-50 rounded-lg transition-colors">
              <Pencil :size="13" />
            </button>
            <button @click="confirmDelete(cat)" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
              <Trash2 :size="13" />
            </button>
          </div>
        </div>
        <p class="text-xs text-gray-500 mb-4 line-clamp-2 min-h-[2rem]">{{ cat.description || '—' }}</p>
        <div class="flex items-center gap-2">
          <div class="w-3 h-3 rounded-full shrink-0 border border-white shadow-sm" :style="{ backgroundColor: cat.color || '#0D47A1' }" />
          <span class="text-xs text-gray-400 font-mono">{{ cat.color || 'no color' }}</span>
          <span v-if="cat.sort_order" class="text-xs text-gray-300 font-mono">· #{{ cat.sort_order }}</span>
          <span v-if="cat.show_in_filter === false"
            class="ml-1 text-[10px] font-bold text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded-full">Hidden from filter</span>
          <span class="ml-auto text-xs text-gray-400">{{ cat.created_at ? new Date(cat.created_at).toLocaleDateString() : '' }}</span>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="meta.last_page > 1" class="flex items-center justify-between mt-5">
      <span class="text-xs text-gray-400">Page {{ meta.current_page }} of {{ meta.last_page }}</span>
      <div class="flex gap-1">
        <button @click="page--" :disabled="page === 1"
          class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-40 transition-colors">Prev</button>
        <button @click="page++" :disabled="page === meta.last_page"
          class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-40 transition-colors">Next</button>
      </div>
    </div>

    <!-- Add / Edit Modal -->
    <Transition name="modal">
      <div v-if="modal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="closeModal" />
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
          <h3 class="font-black text-gray-900 text-base mb-5">{{ editId ? 'Edit Category' : 'Add Category' }}</h3>

          <div class="space-y-4">
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Name *</label>
              <input v-model="form.name" @input="autoSlug" type="text" placeholder="Category name"
                :class="fieldClass(errors.name)" />
              <p v-if="errors.name" class="text-xs text-red-500 mt-1">{{ errors.name }}</p>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Slug *</label>
              <input v-model="form.slug" type="text" placeholder="category-slug" class="font-mono"
                :class="fieldClass(errors.slug)" />
              <p v-if="errors.slug" class="text-xs text-red-500 mt-1">{{ errors.slug }}</p>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
              <textarea v-model="form.description" rows="2" placeholder="Short description…"
                :class="fieldClass(errors.description) + ' resize-none'" />
              <p v-if="errors.description" class="text-xs text-red-500 mt-1">{{ errors.description }}</p>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Color</label>
              <div class="flex items-center gap-3">
                <input v-model="form.color" type="color" class="w-10 h-10 rounded-lg border border-gray-200 cursor-pointer p-0.5 shrink-0" />
                <input v-model="form.color" type="text" placeholder="#0D47A1"
                  :class="fieldClass(errors.color) + ' font-mono'" />
              </div>
              <p v-if="errors.color" class="text-xs text-red-500 mt-1">{{ errors.color }}</p>
            </div>

            <!-- Sort order + Pin row -->
            <div class="flex gap-3">
              <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Sort Order</label>
                <input v-model.number="form.sort_order" type="number" min="0" max="999" placeholder="0"
                  :class="fieldClass(errors.sort_order)" />
                <p class="text-[10px] text-gray-400 mt-0.5">Lower = appears first</p>
              </div>
              <div class="flex flex-col gap-2 justify-center pt-4">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                  <div
                    @click="form.is_pinned = !form.is_pinned"
                    :class="['w-9 h-5 rounded-full transition-colors relative', form.is_pinned ? 'bg-amber-400' : 'bg-gray-200']"
                  >
                    <span :class="['absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform', form.is_pinned ? 'translate-x-4' : '']" />
                  </div>
                  <span class="text-xs font-semibold text-gray-600">📌 Pinned</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer select-none">
                  <div
                    @click="form.show_in_filter = !form.show_in_filter"
                    :class="['w-9 h-5 rounded-full transition-colors relative', form.show_in_filter ? 'bg-blue-500' : 'bg-gray-200']"
                  >
                    <span :class="['absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform', form.show_in_filter ? 'translate-x-4' : '']" />
                  </div>
                  <span class="text-xs font-semibold text-gray-600">🔍 Show in filter</span>
                </label>
              </div>
            </div>

            <p v-if="errors.general" class="text-xs text-red-500 bg-red-50 px-3 py-2 rounded-lg">{{ errors.general }}</p>
          </div>

          <div class="flex gap-3 mt-6">
            <button @click="closeModal" class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
            <button @click="saveCategory" :disabled="saving || !form.name || !form.slug"
              class="flex-1 bg-[#0D47A1] hover:bg-[#0a2f6e] disabled:opacity-50 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors">
              {{ saving ? 'Saving…' : (editId ? 'Save Changes' : 'Add Category') }}
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
          <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <Trash2 :size="20" class="text-red-500" />
          </div>
          <h3 class="font-black text-gray-900 mb-2">Delete Category?</h3>
          <p class="text-sm text-gray-500 mb-5">"{{ deleteTarget?.name }}"</p>
          <div class="flex gap-3">
            <button @click="deleteTarget = null" class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
            <button @click="doDelete" class="flex-1 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors">Delete</button>
          </div>
        </div>
      </div>
    </Transition>

  </DashboardLayout>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import axios from 'axios'
import { Plus, Pencil, Trash2, Tag, Search, Pin, PinOff, Filter } from 'lucide-vue-next'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const categories  = ref([])
const meta        = ref({})
const loading     = ref(false)
const page        = ref(1)
const search      = ref('')

const modal       = ref(false)
const editId      = ref(null)
const saving      = ref(false)
const errors      = ref({})
const deleteTarget = ref(null)
const toast       = ref('')

const blankForm = () => ({ name: '', slug: '', description: '', color: '#0D47A1', is_pinned: false, sort_order: 0, show_in_filter: true })
const form = ref(blankForm())

// ── API ──────────────────────────────────────────────────────────────────────
const fetchCategories = async () => {
  loading.value = true
  const { data } = await axios.get('/api/categories', { params: { page: page.value, search: search.value, per_page: 12 } })
  categories.value = data.data ?? data
  meta.value = data.meta ?? {}
  loading.value = false
}

onMounted(fetchCategories)
watch([page, search], fetchCategories)
watch(search, () => { page.value = 1 })

// ── Helpers ───────────────────────────────────────────────────────────────────
const autoSlug = () => {
  if (!editId.value) {
    form.value.slug = form.value.name.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '')
  }
}

const showToast = (msg) => { toast.value = msg; setTimeout(() => { toast.value = '' }, 2500) }

const fieldClass = (err) =>
  `w-full border rounded-lg px-3 py-2 text-sm outline-none transition-colors ${err ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-[#0D47A1]'}`

// ── CRUD ─────────────────────────────────────────────────────────────────────
const openAdd  = () => { editId.value = null; form.value = blankForm(); errors.value = {}; modal.value = true }
const openEdit = (cat) => { editId.value = cat.id; form.value = { name: cat.name, slug: cat.slug, description: cat.description ?? '', color: cat.color ?? '#0D47A1', is_pinned: !!cat.is_pinned, sort_order: cat.sort_order ?? 0, show_in_filter: cat.show_in_filter !== false }; errors.value = {}; modal.value = true }
const closeModal = () => { modal.value = false }

const saveCategory = async () => {
  saving.value = true; errors.value = {}
  try {
    editId.value
      ? await axios.put(`/api/categories/${editId.value}`, form.value)
      : await axios.post('/api/categories', form.value)
    modal.value = false
    showToast(editId.value ? 'Category updated.' : 'Category created.')
    fetchCategories()
  } catch (e) {
    const data = e.response?.data
    if (data?.errors) {
      Object.keys(data.errors).forEach(k => { errors.value[k] = data.errors[k][0] })
    } else {
      errors.value.general = data?.message ?? 'Something went wrong.'
    }
  } finally {
    saving.value = false
  }
}

const togglePin = async (cat) => {
  const newVal = !cat.is_pinned
  cat.is_pinned = newVal
  try {
    await axios.put(`/api/categories/${cat.id}`, { is_pinned: newVal })
    showToast(newVal ? 'Category pinned.' : 'Category unpinned.')
  } catch {
    cat.is_pinned = !newVal
    showToast('Failed to update pin status.')
  }
}

const toggleFilter = async (cat) => {
  const newVal = !cat.show_in_filter
  cat.show_in_filter = newVal
  try {
    await axios.put(`/api/categories/${cat.id}`, { show_in_filter: newVal })
    showToast(newVal ? 'Shown in Latest News filter.' : 'Hidden from Latest News filter.')
  } catch {
    cat.show_in_filter = !newVal
    showToast('Failed to update filter visibility.')
  }
}

const confirmDelete = (cat) => { deleteTarget.value = cat }
const doDelete = async () => {
  await axios.delete(`/api/categories/${deleteTarget.value.id}`)
  deleteTarget.value = null
  showToast('Category deleted.')
  fetchCategories()
}
</script>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
.fade-up-enter-active, .fade-up-leave-active { transition: opacity 0.2s, transform 0.2s; }
.fade-up-enter-from, .fade-up-leave-to { opacity: 0; transform: translateY(8px); }
</style>
