<template>
  <DashboardLayout>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-lg font-black text-gray-900">Articles</h2>
        <p class="text-sm text-gray-400">{{ meta.total ?? 0 }} articles</p>
      </div>
      <button @click="openCreate" class="flex items-center gap-2 bg-[#0D47A1] hover:bg-[#0a2f6e] text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm">
        <Plus :size="15" /> Create Article
      </button>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-2 mb-4">
      <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-3 py-2 flex-1 min-w-48">
        <Search :size="14" class="text-gray-400 shrink-0" />
        <input v-model="search" type="text" placeholder="Search articles…" class="text-sm text-gray-700 placeholder-gray-400 outline-none w-full bg-transparent" />
      </div>
      <select v-model="filterStatus" class="bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 outline-none focus:border-[#0D47A1]">
        <option value="">All Status</option>
        <option value="published">Published</option>
        <option value="draft">Draft</option>
      </select>
      <select v-model="filterCategory" class="bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 outline-none focus:border-[#0D47A1]">
        <option value="">All Categories</option>
        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
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
              <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Title</th>
              <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Category</th>
              <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Author</th>
              <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Views</th>
              <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
              <th class="px-4 py-3 w-20"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="loading"><td colspan="6" class="text-center py-12 text-gray-400 text-sm">Loading…</td></tr>
            <tr v-else-if="articles.length === 0"><td colspan="6" class="text-center py-12 text-gray-400 text-sm">No articles found.</td></tr>
            <tr v-for="a in articles" :key="a.id" class="hover:bg-gray-50 transition-colors group">
              <td class="px-5 py-3.5 max-w-xs">
                <p class="font-medium text-gray-800 truncate">{{ a.title }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ a.published_at ? new Date(a.published_at).toLocaleDateString() : 'Not published' }}</p>
              </td>
              <td class="px-4 py-3.5 hidden md:table-cell">
                <span class="text-xs bg-gray-100 text-gray-600 font-medium px-2 py-0.5 rounded-full">{{ a.category?.name ?? '—' }}</span>
              </td>
              <td class="px-4 py-3.5 text-gray-500 text-xs hidden lg:table-cell">{{ a.author?.name ?? '—' }}</td>
              <td class="px-4 py-3.5 text-gray-500 text-xs hidden lg:table-cell">{{ a.views?.toLocaleString() }}</td>
              <td class="px-4 py-3.5">
                <span :class="['text-[11px] font-semibold px-2 py-0.5 rounded-full', a.status === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700']">
                  {{ a.status }}
                </span>
              </td>
              <td class="px-4 py-3.5">
                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity justify-end">
                  <button @click="openEdit(a)" class="p-1.5 text-gray-400 hover:text-[#0D47A1] hover:bg-blue-50 rounded-lg transition-colors"><Pencil :size="14" /></button>
                  <button @click="confirmDelete(a)" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"><Trash2 :size="14" /></button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="meta.last_page > 1" class="flex items-center justify-between px-5 py-3 border-t border-gray-100">
        <span class="text-xs text-gray-400">Page {{ meta.current_page }} of {{ meta.last_page }}</span>
        <div class="flex gap-1">
          <button @click="page--" :disabled="page === 1" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-40 transition-colors">Prev</button>
          <button @click="page++" :disabled="page === meta.last_page" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-40 transition-colors">Next</button>
        </div>
      </div>
    </div>

    <!-- Create / Edit Modal -->
    <Transition name="modal">
      <div v-if="modal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="closeModal" />
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
          <h3 class="font-black text-gray-900 text-base mb-5">{{ editId ? 'Edit Article' : 'Create Article' }}</h3>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Title -->
            <div class="sm:col-span-2">
              <label class="label">Title *</label>
              <input v-model="form.title" @input="autoSlug" type="text" :class="field(errors.title)" placeholder="Article title" />
              <err :e="errors.title" />
            </div>
            <!-- Slug -->
            <div class="sm:col-span-2">
              <label class="label">Slug *</label>
              <input v-model="form.slug" type="text" :class="field(errors.slug) + ' font-mono'" placeholder="article-slug" />
              <err :e="errors.slug" />
            </div>
            <!-- Excerpt -->
            <div class="sm:col-span-2">
              <label class="label">Excerpt</label>
              <textarea v-model="form.excerpt" rows="2" :class="field(errors.excerpt) + ' resize-none'" placeholder="Short description…" />
              <err :e="errors.excerpt" />
            </div>
            <!-- Content -->
            <div class="sm:col-span-2">
              <label class="label">Content *</label>
              <textarea v-model="form.content" rows="6" :class="field(errors.content) + ' resize-y'" placeholder="Article content…" />
              <err :e="errors.content" />
            </div>
            <!-- Category -->
            <div>
              <label class="label">Category *</label>
              <select v-model="form.category_id" :class="field(errors.category_id)">
                <option value="">Select category…</option>
                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
              <err :e="errors.category_id" />
            </div>
            <!-- Status -->
            <div>
              <label class="label">Status *</label>
              <select v-model="form.status" :class="field(errors.status)">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
              </select>
              <err :e="errors.status" />
            </div>
            <!-- Featured Image -->
            <div class="sm:col-span-2">
              <label class="label">Featured Image URL</label>
              <input v-model="form.featured_image" type="text" :class="field(errors.featured_image)" placeholder="https://…" />
              <err :e="errors.featured_image" />
            </div>
            <!-- Is Featured -->
            <div class="flex items-center gap-2">
              <input v-model="form.is_featured" type="checkbox" id="is_featured" class="w-4 h-4 accent-[#0D47A1] rounded" />
              <label for="is_featured" class="text-sm text-gray-700 cursor-pointer">Mark as featured</label>
            </div>
          </div>

          <p v-if="errors.general" class="text-xs text-red-500 bg-red-50 px-3 py-2 rounded-lg mt-4">{{ errors.general }}</p>

          <div class="flex gap-3 mt-6">
            <button @click="closeModal" class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
            <button @click="save" :disabled="saving" class="flex-1 bg-[#0D47A1] hover:bg-[#0a2f6e] disabled:opacity-50 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors">
              {{ saving ? 'Saving…' : (editId ? 'Save Changes' : 'Create Article') }}
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
          <h3 class="font-black text-gray-900 mb-2">Delete Article?</h3>
          <p class="text-sm text-gray-500 mb-5 line-clamp-2">{{ deleteTarget?.title }}</p>
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
import { ref, watch, onMounted } from 'vue'
import axios from 'axios'
import { Plus, Search, Pencil, Trash2 } from 'lucide-vue-next'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

// ── State ─────────────────────────────────────────────────────────────────
const articles       = ref([])
const categories     = ref([])
const meta           = ref({})
const loading        = ref(false)
const page           = ref(1)
const search         = ref('')
const filterStatus   = ref('')
const filterCategory = ref('')

const modal       = ref(false)
const editId      = ref(null)
const saving      = ref(false)
const errors      = ref({})
const deleteTarget = ref(null)
const toast       = ref('')

const blankForm = () => ({ title: '', slug: '', excerpt: '', content: '', featured_image: '', category_id: '', status: 'draft', is_featured: false })
const form = ref(blankForm())

// ── Fetch ─────────────────────────────────────────────────────────────────
const fetchArticles = async () => {
  loading.value = true
  const { data } = await axios.get('/api/articles', {
    params: { page: page.value, search: search.value, status: filterStatus.value, category_id: filterCategory.value, per_page: 12 },
  })
  articles.value = data.data
  meta.value     = data.meta ?? {}
  loading.value  = false
}

const fetchCategories = async () => {
  const { data } = await axios.get('/api/categories', { params: { per_page: 100 } })
  categories.value = data.data ?? data
}

onMounted(() => { fetchArticles(); fetchCategories() })
watch([page, search, filterStatus, filterCategory], fetchArticles)
watch([search, filterStatus, filterCategory], () => { page.value = 1 })

// ── Helpers ───────────────────────────────────────────────────────────────
const autoSlug = () => {
  if (!editId.value) {
    form.value.slug = form.value.title.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '')
  }
}

const showToast = (msg) => { toast.value = msg; setTimeout(() => { toast.value = '' }, 2500) }

const field = (err) => `w-full border rounded-lg px-3 py-2 text-sm outline-none transition-colors ${err ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-[#0D47A1]'}`

// ── CRUD ──────────────────────────────────────────────────────────────────
const openCreate = () => { editId.value = null; form.value = blankForm(); errors.value = {}; modal.value = true }

const openEdit = (a) => {
  editId.value = a.id
  form.value   = { title: a.title, slug: a.slug, excerpt: a.excerpt ?? '', content: a.content ?? '', featured_image: a.featured_image ?? '', category_id: a.category_id, status: a.status, is_featured: a.is_featured }
  errors.value = {}
  modal.value  = true
}

const closeModal = () => { modal.value = false }

const save = async () => {
  saving.value = true; errors.value = {}
  try {
    editId.value
      ? await axios.put(`/api/articles/${editId.value}`, form.value)
      : await axios.post('/api/articles', form.value)
    modal.value = false
    showToast(editId.value ? 'Article updated.' : 'Article created.')
    fetchArticles()
  } catch (e) {
    const data = e.response?.data
    if (data?.errors) Object.keys(data.errors).forEach(k => { errors.value[k] = data.errors[k][0] })
    else errors.value.general = data?.message ?? 'Something went wrong.'
  } finally {
    saving.value = false
  }
}

const confirmDelete = (a) => { deleteTarget.value = a }
const doDelete = async () => {
  await axios.delete(`/api/articles/${deleteTarget.value.id}`)
  deleteTarget.value = null
  showToast('Article deleted.')
  fetchArticles()
}
</script>

<!-- Inline reusable sub-components -->
<script>
import { h, defineComponent } from 'vue'
const err = defineComponent({ props: ['e'], render() { return this.e ? h('p', { class: 'text-xs text-red-500 mt-1' }, this.e) : null } })
export default { components: { err } }
</script>

<style scoped>
.label { @apply block text-xs font-semibold text-gray-600 mb-1; }
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
.fade-up-enter-active, .fade-up-leave-active { transition: opacity 0.2s, transform 0.2s; }
.fade-up-enter-from, .fade-up-leave-to { opacity: 0; transform: translateY(8px); }
</style>
