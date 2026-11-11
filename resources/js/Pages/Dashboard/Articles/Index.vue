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
      <select v-model="filterStatus" class="bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 outline-none">
        <option value="">All Status</option>
        <option value="published">Published</option>
        <option value="draft">Draft</option>
      </select>
      <select v-model="filterCategory" class="bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 outline-none">
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
                <div class="flex items-center gap-3">
                  <img v-if="a.featured_image" :src="a.featured_image" class="w-10 h-10 rounded-lg object-cover shrink-0 bg-gray-100" />
                  <div>
                    <p class="font-medium text-gray-800 truncate">{{ a.title }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ a.published_at ? new Date(a.published_at).toLocaleDateString() : 'Not published' }}</p>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3.5 hidden md:table-cell">
                <span class="text-xs bg-gray-100 text-gray-600 font-medium px-2 py-0.5 rounded-full">{{ a.category?.name ?? '—' }}</span>
              </td>
              <td class="px-4 py-3.5 text-gray-500 text-xs hidden lg:table-cell">{{ a.author?.name ?? '—' }}</td>
              <td class="px-4 py-3.5 text-gray-500 text-xs hidden lg:table-cell">{{ a.views?.toLocaleString() }}</td>
              <td class="px-4 py-3.5">
                <div class="flex flex-col gap-1">
                  <span :class="['text-[11px] font-semibold px-2 py-0.5 rounded-full w-fit', a.status === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700']">{{ a.status }}</span>
                  <span :class="['text-[10px] font-bold px-2 py-0.5 rounded-full w-fit', a.language === 'en' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700']">{{ a.language === 'en' ? 'EN' : 'বাং' }}</span>
                </div>
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
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-3xl p-6 max-h-[92vh] overflow-y-auto">
          <h3 class="font-black text-gray-900 text-base mb-5">{{ editId ? 'Edit Article' : 'Create Article' }}</h3>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Title -->
            <div class="sm:col-span-2">
              <label class="label">Title *</label>
              <input v-model="form.title" @input="autoSlug" type="text" :class="field(errors.title)" placeholder="Article title" />
              <Err :e="errors.title" />
            </div>
            <!-- Slug -->
            <div class="sm:col-span-2">
              <label class="label">Slug *</label>
              <input v-model="form.slug" type="text" :class="field(errors.slug) + ' font-mono'" placeholder="article-slug" />
              <Err :e="errors.slug" />
            </div>
            <!-- Excerpt -->
            <div class="sm:col-span-2">
              <label class="label">Excerpt</label>
              <textarea v-model="form.excerpt" rows="2" :class="field(errors.excerpt) + ' resize-none'" placeholder="Short description…" />
              <Err :e="errors.excerpt" />
            </div>
            <!-- Content — TipTap -->
            <div class="sm:col-span-2">
              <label class="label">Content *</label>
              <RichEditor v-model="form.content" :has-error="!!errors.content" />
              <Err :e="errors.content" />
            </div>
            <!-- Category (primary) -->
            <div>
              <label class="label">Primary Category *</label>
              <select v-model="form.category_id" :class="field(errors.category_id)">
                <option value="">Select category…</option>
                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
              <Err :e="errors.category_id" />
            </div>
            <!-- Extra categories -->
            <div>
              <label class="label">Additional Categories</label>
              <!-- Selected pills -->
              <div v-if="selectedExtraCategories.length" class="flex flex-wrap gap-1.5 mb-2">
                <span
                  v-for="c in selectedExtraCategories"
                  :key="c.id"
                  class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-100 text-blue-700"
                >
                  {{ c.name }}
                  <button type="button" @click="removeExtraCategory(c.id)" class="ml-0.5 text-blue-400 hover:text-blue-700 transition-colors leading-none font-bold">&times;</button>
                </span>
              </div>
              <!-- Search input + dropdown -->
              <div class="relative">
                <input
                  v-model="catInput"
                  @input="fetchCatSuggestions"
                  @blur="hideCatSuggestions"
                  @focus="fetchCatSuggestions"
                  type="text"
                  :class="field(null)"
                  placeholder="Search and select categories…"
                />
                <ul
                  v-if="catSuggestions.length"
                  class="absolute z-30 left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden max-h-48 overflow-y-auto"
                >
                  <li
                    v-for="c in catSuggestions"
                    :key="c.id"
                    @mousedown.prevent="selectCatSuggestion(c)"
                    class="flex items-center justify-between px-3 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 cursor-pointer"
                  >
                    <span>{{ c.name }}</span>
                  </li>
                </ul>
              </div>
              <p class="text-[11px] text-gray-400 mt-1">Primary category is always included automatically.</p>
            </div>
            <!-- Status -->
            <div>
              <label class="label">Status *</label>
              <select v-model="form.status" :class="field(errors.status)">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
              </select>
              <Err :e="errors.status" />
            </div>
            <!-- Language -->
            <div>
              <label class="label">Language *</label>
              <select v-model="form.language" :class="field(errors.language)">
                <option value="bn">বাংলা (Bangla)</option>
                <option value="en">English</option>
              </select>
              <Err :e="errors.language" />
            </div>
            <!-- Featured Image upload -->
            <div class="sm:col-span-2">
              <label class="label">Featured Image</label>
              <div class="flex items-start gap-4">
                <!-- Preview -->
                <div class="shrink-0">
                  <img
                    v-if="form.featured_image"
                    :src="form.featured_image"
                    class="w-24 h-24 rounded-xl object-cover border border-gray-200 bg-gray-50"
                  />
                  <div v-else class="w-24 h-24 rounded-xl border-2 border-dashed border-gray-200 flex items-center justify-center bg-gray-50">
                    <ImageIcon :size="20" class="text-gray-300" />
                  </div>
                </div>
                <!-- Input -->
                <div class="flex-1 space-y-2">
                  <label
                    class="flex items-center gap-2 px-4 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-600 cursor-pointer hover:bg-gray-50 transition-colors w-full"
                    :class="{ 'opacity-60 cursor-not-allowed': uploading }"
                  >
                    <Upload :size="15" class="shrink-0" />
                    <span>{{ uploading ? 'Uploading…' : 'Choose & Crop Image' }}</span>
                    <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" :disabled="uploading" @change="uploadImage" />
                  </label>
                  <p class="text-xs text-gray-400">JPG or PNG — max 4 MB. You can crop and position after selecting.</p>
                  <Err :e="errors.featured_image" />
                </div>
              </div>
            </div>
            <!-- Video URL -->
            <div class="sm:col-span-2">
              <label class="label">Video URL <span class="font-normal text-gray-400">(YouTube, Vimeo, Dailymotion — optional)</span></label>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M10 15l5.19-3L10 9v6m11.56-7.83c.13.47.22 1.1.28 1.9.07.8.1 1.49.1 2.09L22 12c0 2.19-.16 3.8-.44 4.83-.25.9-.83 1.48-1.73 1.73-.47.13-1.33.22-2.65.28-1.3.07-2.49.1-3.59.1L12 19c-4.19 0-6.8-.16-7.83-.44-.9-.25-1.48-.83-1.73-1.73-.13-.47-.22-1.1-.28-1.9-.07-.8-.1-1.49-.1-2.09L2 12c0-2.19.16-3.8.44-4.83.25-.9.83-1.48 1.73-1.73.47-.13 1.33-.22 2.65-.28 1.3-.07 2.49-.1 3.59-.1L12 5c4.19 0 6.8.16 7.83.44.9.25 1.48.83 1.73 1.73z"/></svg>
                <input v-model="form.video_url" type="text" :class="field(errors.video_url)" placeholder="https://youtube.com/watch?v=… or youtu.be/… or vimeo.com/…" />
              </div>
              <Err :e="errors.video_url" />
              <!-- Preview embed -->
              <div v-if="videoEmbedPreview" class="mt-3 rounded-xl overflow-hidden border border-gray-200" style="padding-top:0">
                <div class="relative w-full" style="padding-top:56.25%">
                  <iframe :src="videoEmbedPreview" class="absolute inset-0 w-full h-full rounded-xl" frameborder="0" allowfullscreen />
                </div>
              </div>
              <p v-else-if="form.video_url && !videoEmbedPreview" class="text-xs text-amber-500 mt-1">URL not recognised — supported: YouTube, Vimeo, Dailymotion</p>
            </div>
            <!-- Tags -->
            <div class="sm:col-span-2">
              <label class="label">Tags <span class="font-normal text-gray-400">(Enter or comma to add)</span></label>
              <!-- Selected pills -->
              <div v-if="form.tags.length" class="flex flex-wrap gap-1.5 mb-2">
                <span
                  v-for="(tag, i) in form.tags"
                  :key="i"
                  class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-100 text-blue-700"
                >
                  {{ tag }}
                  <button type="button" @click="removeTag(i)" class="ml-0.5 text-blue-400 hover:text-blue-700 transition-colors leading-none font-bold">&times;</button>
                </span>
              </div>
              <!-- Input + dropdown suggestions -->
              <div class="relative">
                <input
                  v-model="tagInput"
                  @keydown="onTagKeydown"
                  @input="fetchTagSuggestions"
                  @blur="hideSuggestions"
                  @focus="fetchTagSuggestions"
                  type="text"
                  :class="field(null)"
                  placeholder="e.g. IPL, Virat Kohli, T20…"
                />
                <ul
                  v-if="tagSuggestions.length"
                  class="absolute z-30 left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden max-h-48 overflow-y-auto"
                >
                  <li
                    v-for="s in tagSuggestions"
                    :key="s.id"
                    @mousedown.prevent="selectSuggestion(s.name)"
                    class="flex items-center justify-between px-3 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 cursor-pointer"
                  >
                    <span>{{ s.name }}</span>
                    <span class="text-xs text-gray-400">{{ s.articles_count }} articles</span>
                  </li>
                </ul>
              </div>
            </div>

            <!-- Flags row -->
            <div class="flex flex-wrap items-center gap-4">
              <div class="flex items-center gap-2">
                <input v-model="form.is_featured" type="checkbox" id="is_featured" class="w-4 h-4 accent-[#0D47A1] rounded" />
                <label for="is_featured" class="text-sm text-gray-700 cursor-pointer">Mark as featured</label>
              </div>
              <div class="flex items-center gap-2">
                <input v-model="form.is_photo_day" type="checkbox" id="is_photo_day" class="w-4 h-4 accent-amber-500 rounded" />
                <label for="is_photo_day" class="text-sm text-gray-700 cursor-pointer flex items-center gap-1">
                  <span>📸</span> দিনের সেরা ছবি
                </label>
              </div>
            </div>
          </div>

          <p v-if="errors.general" class="text-xs text-red-500 bg-red-50 px-3 py-2 rounded-lg mt-4">{{ errors.general }}</p>

          <div class="flex gap-3 mt-6">
            <button @click="closeModal" class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
            <button @click="save" :disabled="saving || uploading" class="flex-1 bg-[#0D47A1] hover:bg-[#0a2f6e] disabled:opacity-50 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors">
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

    <!-- Image Cropper Modal -->
    <ImageCropper
      :show="cropperShow"
      :file="cropperFile"
      @apply="onCropApply"
      @cancel="onCropCancel"
    />

  </DashboardLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted, defineComponent, h } from 'vue'
import axios from 'axios'
import { Plus, Search, Pencil, Trash2, Upload, Image as ImageIcon } from 'lucide-vue-next'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import RichEditor from '@/Components/RichEditor.vue'
import ImageCropper from '@/Components/ImageCropper.vue'

const Err = defineComponent({ props: ['e'], render() { return this.e ? h('p', { class: 'text-xs text-red-500 mt-1' }, this.e) : null } })

// ── State ─────────────────────────────────────────────────────────────────
const articles       = ref([])
const categories     = ref([])
const meta           = ref({})
const loading        = ref(false)
const page           = ref(1)
const search         = ref('')
const filterStatus   = ref('')
const filterCategory = ref('')

const modal        = ref(false)
const editId       = ref(null)
const saving       = ref(false)
const uploading    = ref(false)
const errors       = ref({})
const deleteTarget = ref(null)
const toast        = ref('')

const cropperShow = ref(false)
const cropperFile = ref(null)

const blankForm = () => ({ title: '', slug: '', excerpt: '', content: '', featured_image: '', video_url: '', category_id: '', category_ids: [], status: 'draft', language: 'bn', is_featured: false, is_photo_day: false, tags: [] })
const form = ref(blankForm())

// ── Tag input ─────────────────────────────────────────────────────────────
const tagInput       = ref('')
const tagSuggestions = ref([])
let suggestTimer     = null

const addTag = (name) => {
  const clean = name.trim().replace(/,+$/, '').trim()
  if (!clean || form.value.tags.includes(clean)) return
  form.value.tags.push(clean)
}

const addTagFromInput = () => {
  addTag(tagInput.value)
  tagInput.value = ''
  tagSuggestions.value = []
}

const removeTag = (i) => form.value.tags.splice(i, 1)

const selectSuggestion = (name) => {
  addTag(name)
  tagInput.value = ''
  tagSuggestions.value = []
}

const hideSuggestions = () => setTimeout(() => { tagSuggestions.value = [] }, 200)

const onTagKeydown = (e) => {
  if (e.key === 'Enter' || e.key === ',') {
    e.preventDefault()
    addTagFromInput()
  } else if (e.key === 'Backspace' && !tagInput.value && form.value.tags.length) {
    form.value.tags.pop()
  }
}

const fetchTagSuggestions = () => {
  clearTimeout(suggestTimer)
  const q = tagInput.value.trim()
  if (!q) { tagSuggestions.value = []; return }
  suggestTimer = setTimeout(async () => {
    const { data } = await axios.get('/api/tags', { params: { search: q } })
    tagSuggestions.value = data.filter(t => !form.value.tags.includes(t.name))
  }, 200)
}

// ── Fetch ─────────────────────────────────────────────────────────────────
const fetchArticles = async () => {
  loading.value = true
  const { data } = await axios.get('/api/articles', {
    params: { page: page.value, search: search.value, status: filterStatus.value, category_id: filterCategory.value, per_page: 12 },
  })
  articles.value = data.data
  // meta.value     = data.meta ?? {}
  meta.value = {
    total: data.total,
    current_page: data.current_page,
    last_page: data.last_page
  }
  loading.value  = false
}

const fetchCategories = async () => {
  const { data } = await axios.get('/api/categories', { params: { per_page: 100 } })
  categories.value = data.data ?? data
}

onMounted(() => { fetchArticles(); fetchCategories() })
watch([page, search, filterStatus, filterCategory], fetchArticles)
watch([search, filterStatus, filterCategory], () => { page.value = 1 })

// ── Video embed preview ───────────────────────────────────────────────────
const videoEmbedPreview = computed(() => {
  const url = form.value.video_url?.trim()
  if (!url) return null
  if (url.includes('youtube.com/embed/') || url.includes('player.vimeo.com') || url.includes('dailymotion.com/embed/')) return url
  let m
  if ((m = url.match(/(?:youtube\.com\/(?:watch\?v=|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/))) return `https://www.youtube.com/embed/${m[1]}`
  if ((m = url.match(/vimeo\.com\/(\d+)/))) return `https://player.vimeo.com/video/${m[1]}`
  if ((m = url.match(/dailymotion\.com\/video\/([a-zA-Z0-9]+)/))) return `https://www.dailymotion.com/embed/video/${m[1]}`
  return null
})

// ── Helpers ───────────────────────────────────────────────────────────────
const autoSlug = () => {
  form.value.slug = form.value.title
    .trim()
    .toLowerCase()
    .replace(/[\s]+/g, '-')
    .replace(/[^\p{L}\p{M}\p{N}-]/gu, '')
    .replace(/-{2,}/g, '-')
    .replace(/^-|-$/g, '')
}

const showToast = (msg) => { toast.value = msg; setTimeout(() => { toast.value = '' }, 2500) }

const field = (err) =>
  `w-full border rounded-lg px-3 py-2 text-sm outline-none transition-colors ${err ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-[#0D47A1]'}`

// ── Image upload with cropper ─────────────────────────────────────────────
const uploadImage = (e) => {
  const file = e.target.files?.[0]
  if (!file) return
  cropperFile.value = file
  cropperShow.value = true
  e.target.value = ''
}

const onCropApply = async (croppedFile) => {
  cropperShow.value = false
  uploading.value = true
  errors.value.featured_image = null
  try {
    const fd = new FormData()
    fd.append('image', croppedFile)
    const { data } = await axios.post('/api/upload-image', fd, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    form.value.featured_image = data.url
  } catch (err) {
    errors.value.featured_image = err.response?.data?.message ?? 'Upload failed.'
  } finally {
    uploading.value = false
  }
}

const onCropCancel = () => {
  cropperShow.value = false
  cropperFile.value = null
}

// ── CRUD ──────────────────────────────────────────────────────────────────
const openCreate = () => { editId.value = null; form.value = blankForm(); tagInput.value = ''; tagSuggestions.value = []; catInput.value = ''; catSuggestions.value = []; errors.value = {}; modal.value = true }

// ── Extra category search ─────────────────────────────────────────────────
const catInput       = ref('')
const catSuggestions = ref([])
let catSuggestTimer  = null

const selectedExtraCategories = computed(() =>
  categories.value.filter(c => form.value.category_ids.includes(c.id))
)

const removeExtraCategory = (id) => {
  form.value.category_ids = form.value.category_ids.filter(i => i !== id)
}

const selectCatSuggestion = (cat) => {
  if (!form.value.category_ids.includes(cat.id)) {
    form.value.category_ids.push(cat.id)
  }
  catInput.value = ''
  catSuggestions.value = []
}

const hideCatSuggestions = () => setTimeout(() => { catSuggestions.value = [] }, 200)

const fetchCatSuggestions = () => {
  clearTimeout(catSuggestTimer)
  const q = catInput.value.trim().toLowerCase()
  catSuggestTimer = setTimeout(() => {
    catSuggestions.value = categories.value.filter(c =>
      (!q || c.name.toLowerCase().includes(q)) &&
      !form.value.category_ids.includes(c.id)
    ).slice(0, 20)
  }, 150)
}

const openEdit = (a) => {
  editId.value = a.id
  form.value   = { title: a.title, slug: a.slug, excerpt: a.excerpt ?? '', content: a.content ?? '', featured_image: a.featured_image ?? '', video_url: a.video_url ?? '', category_id: a.category_id, category_ids: (a.categories ?? []).map(c => c.id), status: a.status, language: a.language ?? 'bn', is_featured: a.is_featured, is_photo_day: a.is_photo_day ?? false, tags: (a.tags ?? []).map(t => t.name) }
  tagInput.value = ''
  tagSuggestions.value = []
  errors.value = {}
  modal.value  = true
}

const closeModal = () => { modal.value = false; tagInput.value = ''; tagSuggestions.value = []; catInput.value = ''; catSuggestions.value = [] }

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

<style scoped>
.label { @apply block text-xs font-semibold text-gray-600 mb-1; }
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
.fade-up-enter-active, .fade-up-leave-active { transition: opacity 0.2s, transform 0.2s; }
.fade-up-enter-from, .fade-up-leave-to { opacity: 0; transform: translateY(8px); }
</style>
