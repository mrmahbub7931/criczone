<template>
  <DashboardLayout>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-lg font-black text-gray-900">Pages</h2>
        <p class="text-sm text-gray-400">{{ meta.total ?? pages.length }} pages</p>
      </div>
      <button @click="openCreate" class="flex items-center gap-2 bg-[#0D47A1] hover:bg-[#0a2f6e] text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm">
        <Plus :size="15" /> New Page
      </button>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-2 mb-5">
      <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-3 py-2 flex-1 min-w-48">
        <Search :size="14" class="text-gray-400 shrink-0" />
        <input v-model="search" type="text" placeholder="Search pages…" class="text-sm text-gray-700 placeholder-gray-400 outline-none w-full bg-transparent" />
      </div>
      <select v-model="filterStatus" class="bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 outline-none">
        <option value="">All Status</option>
        <option value="published">Published</option>
        <option value="draft">Draft</option>
      </select>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
      <div v-if="loading" class="divide-y divide-gray-50">
        <div v-for="i in 5" :key="i" class="flex items-center gap-4 px-5 py-4">
          <div class="flex-1 space-y-1.5">
            <div class="h-3.5 bg-gray-100 rounded animate-pulse w-2/5" />
            <div class="h-3 bg-gray-100 rounded animate-pulse w-1/5" />
          </div>
          <div class="h-5 w-16 bg-gray-100 rounded-full animate-pulse" />
        </div>
      </div>

      <div v-else-if="!pages.length" class="py-16 text-center text-sm text-gray-400">
        No pages yet. Create your first page.
      </div>

      <table v-else class="w-full text-sm">
        <thead class="border-b border-gray-100 bg-gray-50/60">
          <tr>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Title</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Slug</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Updated</th>
            <th class="px-5 py-3" />
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr v-for="p in pages" :key="p.id" class="hover:bg-gray-50/60 transition-colors">
            <td class="px-5 py-3.5 font-medium text-gray-900">{{ p.title }}</td>
            <td class="px-5 py-3.5 text-gray-400 font-mono text-xs hidden sm:table-cell">/pages/{{ p.slug }}</td>
            <td class="px-5 py-3.5">
              <span :class="['text-[11px] font-semibold px-2 py-0.5 rounded-full', p.status === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700']">
                {{ p.status }}
              </span>
            </td>
            <td class="px-5 py-3.5 text-xs text-gray-400 hidden md:table-cell">{{ fmtDate(p.updated_at) }}</td>
            <td class="px-5 py-3.5">
              <div class="flex items-center justify-end gap-1">
                <a :href="`/pages/${p.slug}`" target="_blank"
                  class="p-1.5 text-gray-400 hover:text-[#0D47A1] hover:bg-blue-50 rounded-lg transition-colors" title="View">
                  <ExternalLink :size="15" />
                </a>
                <button @click="openEdit(p)" class="p-1.5 text-gray-400 hover:text-[#0D47A1] hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                  <Pencil :size="15" />
                </button>
                <button @click="confirmDelete(p)" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                  <Trash2 :size="15" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div v-if="meta.last_page > 1" class="flex items-center justify-between px-5 py-3 border-t border-gray-100 text-xs text-gray-500">
        <span>Page {{ meta.current_page }} of {{ meta.last_page }}</span>
        <div class="flex gap-1">
          <button :disabled="meta.current_page === 1" @click="page--"
            class="px-2.5 py-1 rounded-lg border border-gray-200 disabled:opacity-40 hover:bg-gray-50">‹</button>
          <button :disabled="meta.current_page === meta.last_page" @click="page++"
            class="px-2.5 py-1 rounded-lg border border-gray-200 disabled:opacity-40 hover:bg-gray-50">›</button>
        </div>
      </div>
    </div>

    <!-- Create / Edit Modal -->
    <Transition name="modal">
      <div v-if="modal" class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-black/40 backdrop-blur-sm overflow-y-auto" @click.self="modal = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl">

          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-900">{{ editing ? 'Edit Page' : 'New Page' }}</h3>
            <button @click="modal = false" class="text-gray-400 hover:text-gray-600 p-1"><X :size="18" /></button>
          </div>

          <div class="p-6 space-y-4">

            <!-- Title -->
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Title <span class="text-red-500">*</span></label>
              <input v-model="form.title" @input="maybeSlug" type="text" placeholder="e.g. Privacy Policy"
                class="w-full border rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]"
                :class="errors.title ? 'border-red-400' : 'border-gray-200'" />
              <p v-if="errors.title" class="text-xs text-red-500 mt-1">{{ errors.title }}</p>
            </div>

            <!-- Slug -->
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Slug <span class="text-red-500">*</span></label>
              <div class="flex items-center border rounded-lg overflow-hidden" :class="errors.slug ? 'border-red-400' : 'border-gray-200'">
                <span class="px-3 py-2 text-xs text-gray-400 bg-gray-50 border-r border-gray-200">/pages/</span>
                <input v-model="form.slug" type="text" placeholder="privacy-policy"
                  class="flex-1 px-3 py-2 text-sm outline-none" />
              </div>
              <p v-if="errors.slug" class="text-xs text-red-500 mt-1">{{ errors.slug }}</p>
            </div>

            <!-- Status -->
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
              <select v-model="form.status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
              </select>
            </div>

            <!-- Content -->
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Content</label>
              <RichEditor v-model="form.content" :has-error="!!errors.content" />
              <p v-if="errors.content" class="text-xs text-red-500 mt-1">{{ errors.content }}</p>
            </div>

            <!-- SEO -->
            <div class="border border-gray-100 rounded-xl p-4 space-y-3 bg-gray-50/40">
              <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">SEO</p>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Meta Title</label>
                <input v-model="form.meta_title" type="text" placeholder="Defaults to page title"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
                <p class="text-[11px] text-right mt-1" :class="charColor(form.meta_title, 70)">
                  {{ (form.meta_title || '').length }}/70
                </p>
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Meta Description</label>
                <textarea v-model="form.meta_description" rows="2" placeholder="Short description for search engines"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1] resize-none" />
                <p class="text-[11px] text-right mt-1" :class="charColor(form.meta_description, 160)">
                  {{ (form.meta_description || '').length }}/160
                </p>
              </div>
            </div>

          </div>

          <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100">
            <button @click="modal = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 font-medium">Cancel</button>
            <button @click="submit" :disabled="saving"
              class="flex items-center gap-2 bg-[#0D47A1] hover:bg-[#0a2f6e] disabled:opacity-60 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-colors">
              <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              {{ saving ? 'Saving…' : (editing ? 'Save Changes' : 'Create Page') }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Delete Confirm -->
    <Transition name="modal">
      <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm" @click.self="deleteTarget = null">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
          <h3 class="font-bold text-gray-900 mb-2">Delete Page</h3>
          <p class="text-sm text-gray-500 mb-6">
            Delete "<strong>{{ deleteTarget.title }}</strong>"? This cannot be undone.
          </p>
          <div class="flex gap-3 justify-end">
            <button @click="deleteTarget = null" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Cancel</button>
            <button @click="doDelete" :disabled="saving"
              class="px-4 py-2 text-sm font-semibold bg-red-600 hover:bg-red-700 disabled:opacity-60 text-white rounded-lg transition-colors">
              {{ saving ? 'Deleting…' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Toast -->
    <Transition name="fade-up">
      <div v-if="toast.show"
        :class="['fixed bottom-6 right-6 flex items-center gap-2 text-sm font-medium px-4 py-3 rounded-xl shadow-lg z-50',
                 toast.error ? 'bg-red-600 text-white' : 'bg-gray-900 text-white']">
        {{ toast.message }}
      </div>
    </Transition>

  </DashboardLayout>
</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue'
import axios from 'axios'
import { Plus, Search, Pencil, Trash2, X, ExternalLink } from 'lucide-vue-next'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import RichEditor from '@/Components/RichEditor.vue'

// ── State ─────────────────────────────────────────────────────────────────────
const pages        = ref([])
const meta         = ref({})
const loading      = ref(false)
const saving       = ref(false)
const modal        = ref(false)
const editing      = ref(null)       // page object when editing
const deleteTarget = ref(null)
const search       = ref('')
const filterStatus = ref('')
const page         = ref(1)
const errors       = ref({})
const toast        = reactive({ show: false, message: '', error: false })
let toastTimer = null

const blankForm = () => ({ title: '', slug: '', content: '', meta_title: '', meta_description: '', status: 'draft' })
const form = reactive(blankForm())

// ── Helpers ───────────────────────────────────────────────────────────────────
const toSlug = (s) => s.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '')

const maybeSlug = () => {
  if (!editing.value) form.slug = toSlug(form.title)
}

const fmtDate = (d) => d ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—'

const charColor = (v, max) => {
  const pct = (v || '').length / max
  return pct > 0.9 ? 'text-red-500' : pct > 0.75 ? 'text-amber-500' : 'text-gray-400'
}

const showToast = (message, error = false) => {
  clearTimeout(toastTimer)
  Object.assign(toast, { show: true, message, error })
  toastTimer = setTimeout(() => { toast.show = false }, 3000)
}

// ── API ───────────────────────────────────────────────────────────────────────
const fetchPages = async () => {
  loading.value = true
  try {
    const { data } = await axios.get('/api/pages', {
      params: { search: search.value || undefined, status: filterStatus.value || undefined, page: page.value },
    })
    pages.value = data.data
    meta.value  = { total: data.total, current_page: data.current_page, last_page: data.last_page }
  } catch {
    showToast('Failed to load pages.', true)
  } finally {
    loading.value = false
  }
}

let searchTimer = null
watch([search, filterStatus], () => {
  page.value = 1
  clearTimeout(searchTimer)
  searchTimer = setTimeout(fetchPages, 300)
})
watch(page, fetchPages)
onMounted(fetchPages)

// ── Modal ─────────────────────────────────────────────────────────────────────
const openCreate = () => {
  editing.value = null
  errors.value  = {}
  Object.assign(form, blankForm())
  modal.value = true
}

const openEdit = (p) => {
  editing.value = p
  errors.value  = {}
  Object.assign(form, {
    title:            p.title,
    slug:             p.slug,
    content:          p.content ?? '',
    meta_title:       p.meta_title ?? '',
    meta_description: p.meta_description ?? '',
    status:           p.status,
  })
  modal.value = true
}

const submit = async () => {
  errors.value = {}
  saving.value = true
  try {
    if (editing.value) {
      const { data } = await axios.put(`/api/pages/${editing.value.id}`, { ...form })
      const idx = pages.value.findIndex(p => p.id === editing.value.id)
      if (idx !== -1) pages.value[idx] = data
      showToast('Page updated.')
    } else {
      const { data } = await axios.post('/api/pages', { ...form })
      pages.value.unshift(data)
      if (meta.value.total !== undefined) meta.value.total++
      showToast('Page created.')
    }
    modal.value = false
  } catch (err) {
    if (err.response?.status === 422) {
      const e = err.response.data.errors ?? {}
      errors.value = Object.fromEntries(Object.entries(e).map(([k, v]) => [k, Array.isArray(v) ? v[0] : v]))
    } else {
      showToast(err.response?.data?.message ?? 'Failed to save page.', true)
    }
  } finally {
    saving.value = false
  }
}

const confirmDelete = (p) => { deleteTarget.value = p }

const doDelete = async () => {
  saving.value = true
  try {
    await axios.delete(`/api/pages/${deleteTarget.value.id}`)
    pages.value = pages.value.filter(p => p.id !== deleteTarget.value.id)
    if (meta.value.total) meta.value.total--
    deleteTarget.value = null
    showToast('Page deleted.')
  } catch {
    showToast('Failed to delete page.', true)
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s; }
.modal-enter-from, .modal-leave-to       { opacity: 0; }
.fade-up-enter-active, .fade-up-leave-active { transition: opacity 0.25s, transform 0.25s; }
.fade-up-enter-from, .fade-up-leave-to       { opacity: 0; transform: translateY(8px); }
</style>
