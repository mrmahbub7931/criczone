<template>
  <DashboardLayout>

    <!-- Header row -->
    <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-6">
      <div class="flex-1">
        <h2 class="text-lg font-black text-gray-900">Articles</h2>
        <p class="text-sm text-gray-400">{{ filtered.length }} articles</p>
      </div>
      <button
        @click="openAdd"
        class="flex items-center gap-2 bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm"
      >
        <Plus :size="16" /> New Article
      </button>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-2 mb-4">
      <input
        v-model="search"
        type="text"
        placeholder="Search articles..."
        class="flex-1 min-w-48 bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-primary"
      />
      <select v-model="filterCat" class="bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 outline-none focus:border-primary">
        <option value="">All Categories</option>
        <option v-for="c in categoryNames" :key="c" :value="c">{{ c }}</option>
      </select>
      <select v-model="filterStatus" class="bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 outline-none focus:border-primary">
        <option value="">All Status</option>
        <option value="published">Published</option>
        <option value="draft">Draft</option>
      </select>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
              <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Title</th>
              <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Category</th>
              <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Author</th>
              <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">Views</th>
              <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
              <th class="px-4 py-3"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="art in paginated" :key="art.id" class="hover:bg-gray-50 transition-colors group">
              <td class="px-5 py-3.5">
                <div class="flex items-center gap-2">
                  <span v-if="art.isFeatured" class="shrink-0 w-1.5 h-1.5 rounded-full bg-secondary" title="Featured" />
                  <span class="font-medium text-gray-800 line-clamp-1">{{ art.title }}</span>
                </div>
                <span class="text-xs text-gray-400">{{ art.date }}</span>
              </td>
              <td class="px-4 py-3.5 hidden md:table-cell">
                <span class="text-xs font-medium bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ art.category }}</span>
              </td>
              <td class="px-4 py-3.5 text-gray-500 hidden lg:table-cell">{{ art.author }}</td>
              <td class="px-4 py-3.5 text-gray-500 hidden lg:table-cell">{{ (art.views / 1000).toFixed(1) }}k</td>
              <td class="px-4 py-3.5">
                <button
                  @click="toggleArticleStatus(art.id)"
                  :class="['text-[11px] font-semibold px-2.5 py-0.5 rounded-full transition-colors cursor-pointer', statusClass(art.status)]"
                >
                  {{ art.status }}
                </button>
              </td>
              <td class="px-4 py-3.5">
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                  <button
                    @click="openEdit(art)"
                    class="p-1.5 text-gray-400 hover:text-primary hover:bg-blue-50 rounded-lg transition-colors"
                    title="Edit"
                  >
                    <Pencil :size="14" />
                  </button>
                  <button
                    @click="toggleFeatured(art.id)"
                    :class="['p-1.5 rounded-lg transition-colors', art.isFeatured ? 'text-secondary' : 'text-gray-400 hover:text-secondary hover:bg-red-50']"
                    title="Feature"
                  >
                    <Star :size="14" />
                  </button>
                  <button
                    @click="confirmDelete(art)"
                    class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                    title="Delete"
                  >
                    <Trash2 :size="14" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Empty -->
      <div v-if="filtered.length === 0" class="text-center py-16 text-gray-400 text-sm">
        No articles found.
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="flex items-center justify-between px-5 py-3 border-t border-gray-100">
        <span class="text-xs text-gray-400">Page {{ currentPage }} of {{ totalPages }}</span>
        <div class="flex gap-1">
          <button
            @click="currentPage--"
            :disabled="currentPage === 1"
            class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
          >Prev</button>
          <button
            @click="currentPage++"
            :disabled="currentPage === totalPages"
            class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
          >Next</button>
        </div>
      </div>
    </div>

    <!-- ── Add / Edit Modal ── -->
    <Transition name="modal">
      <div v-if="modal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="modal = false" />
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
          <h3 class="font-black text-gray-900 text-base mb-5">{{ editId ? 'Edit Article' : 'New Article' }}</h3>

          <div class="space-y-4">
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Title *</label>
              <input v-model="form.title" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary" placeholder="Article title" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Excerpt</label>
              <textarea v-model="form.excerpt" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary resize-none" placeholder="Short description..." />
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Category *</label>
                <select v-model="form.category" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary">
                  <option value="">Select...</option>
                  <option v-for="c in categoryNames" :key="c" :value="c">{{ c }}</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Author</label>
                <input v-model="form.author" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary" placeholder="Author name" />
              </div>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Image URL</label>
              <input v-model="form.imageUrl" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary" placeholder="https://..." />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Read Time</label>
              <input v-model="form.readTime" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary" placeholder="5 min read" />
            </div>
          </div>

          <div class="flex gap-3 mt-6">
            <button @click="modal = false" class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
            <button @click="saveArticle" :disabled="!form.title || !form.category" class="flex-1 bg-primary hover:bg-primary-dark disabled:opacity-50 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors">
              {{ editId ? 'Save Changes' : 'Add Article' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- ── Delete Confirm ── -->
    <Transition name="modal">
      <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="deleteTarget = null" />
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
          <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <Trash2 :size="20" class="text-red-500" />
          </div>
          <h3 class="font-black text-gray-900 mb-2">Delete Article?</h3>
          <p class="text-sm text-gray-500 mb-5 line-clamp-2">{{ deleteTarget?.title }}</p>
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
import { ref, computed, watch } from 'vue'
import { Plus, Pencil, Trash2, Star } from 'lucide-vue-next'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { useDashboard } from '@/composables/useDashboard.js'

const { articles, categoriesData, deleteArticle, toggleArticleStatus, toggleFeatured, addArticle, updateArticle } = useDashboard()

const search       = ref('')
const filterCat    = ref('')
const filterStatus = ref('')
const currentPage  = ref(1)
const perPage      = 8

const modal        = ref(false)
const editId       = ref(null)
const deleteTarget = ref(null)

const blankForm = () => ({ title: '', excerpt: '', category: '', author: '', imageUrl: '', readTime: '' })
const form = ref(blankForm())

const categoryNames = computed(() => categoriesData.map(c => c.name))

const filtered = computed(() => {
  let list = articles.value
  if (search.value)       list = list.filter(a => a.title.toLowerCase().includes(search.value.toLowerCase()))
  if (filterCat.value)    list = list.filter(a => a.category === filterCat.value)
  if (filterStatus.value) list = list.filter(a => a.status === filterStatus.value)
  return list
})

watch(filtered, () => { currentPage.value = 1 })

const totalPages = computed(() => Math.ceil(filtered.value.length / perPage))
const paginated  = computed(() => filtered.value.slice((currentPage.value - 1) * perPage, currentPage.value * perPage))

const statusClass = (s) => s === 'published' ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200'

const openAdd = () => {
  editId.value = null
  form.value = blankForm()
  modal.value = true
}

const openEdit = (art) => {
  editId.value = art.id
  form.value = { title: art.title, excerpt: art.excerpt, category: art.category, author: art.author, imageUrl: art.imageUrl, readTime: art.readTime }
  modal.value = true
}

const saveArticle = () => {
  if (editId.value) {
    updateArticle(editId.value, { ...form.value })
  } else {
    addArticle({ ...form.value })
  }
  modal.value = false
}

const confirmDelete = (art) => { deleteTarget.value = art }
const doDelete = () => {
  deleteArticle(deleteTarget.value.id)
  deleteTarget.value = null
}
</script>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
