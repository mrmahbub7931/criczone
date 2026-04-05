<template>
  <DashboardLayout>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-lg font-black text-gray-900">Video News</h2>
        <p class="text-sm text-gray-400">{{ meta.total ?? 0 }} video articles</p>
      </div>
      <button
        @click="openCreate"
        class="flex items-center gap-2 bg-[#0D47A1] hover:bg-[#0a2f6e] text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm"
      >
        <Plus :size="15" />
        Add Video News
      </button>
    </div>

    <!-- Toast -->
    <Transition name="fade-up">
      <div v-if="toast" class="fixed bottom-6 right-6 z-50 bg-gray-900 text-white text-sm font-medium px-4 py-3 rounded-xl shadow-lg">{{ toast }}</div>
    </Transition>

    <!-- Skeleton -->
    <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
      <div v-for="i in 6" :key="i" class="bg-white rounded-xl overflow-hidden shadow-sm animate-pulse">
        <div class="h-44 bg-gray-200" />
        <div class="p-4 space-y-2">
          <div class="h-3 bg-gray-100 rounded w-1/3" />
          <div class="h-4 bg-gray-200 rounded w-full" />
        </div>
      </div>
    </div>

    <!-- Empty -->
    <div v-else-if="videos.length === 0" class="text-center py-24">
      <div class="text-5xl mb-4">🎬</div>
      <p class="font-bold text-gray-600 text-lg mb-1">No video articles yet</p>
      <p class="text-sm text-gray-400 mb-6">Add your first video news article to get started.</p>
      <button @click="openCreate" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#0D47A1] hover:bg-[#0a2f6e] text-white text-sm font-semibold rounded-lg transition-colors">
        <Plus :size="15" /> Add Video News
      </button>
    </div>

    <!-- Video grid -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
      <div
        v-for="v in videos"
        :key="v.id"
        class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-100 group"
      >
        <!-- Thumbnail -->
        <div class="relative h-44 bg-gray-900 overflow-hidden">
          <img
            v-if="v.featured_image"
            :src="v.featured_image"
            :alt="v.title"
            class="w-full h-full object-cover opacity-80"
          />
          <div v-else class="w-full h-full bg-gradient-to-br from-gray-800 to-gray-700 flex items-center justify-center">
            <svg class="w-10 h-10 text-white/30" fill="currentColor" viewBox="0 0 24 24"><path d="M10 15l5.19-3L10 9v6m11.56-7.83c.13.47.22 1.1.28 1.9.07.8.1 1.49.1 2.09L22 12c0 2.19-.16 3.8-.44 4.83-.25.9-.83 1.48-1.73 1.73-.47.13-1.33.22-2.65.28-1.3.07-2.49.1-3.59.1L12 19c-4.19 0-6.8-.16-7.83-.44-.9-.25-1.48-.83-1.73-1.73-.13-.47-.22-1.1-.28-1.9-.07-.8-.1-1.49-.1-2.09L2 12c0-2.19.16-3.8.44-4.83.25-.9.83-1.48 1.73-1.73.47-.13 1.33-.22 2.65-.28 1.3-.07 2.49-.1 3.59-.1L12 5c4.19 0 6.8.16 7.83.44.9.25 1.48.83 1.73 1.73z"/></svg>
          </div>
          <!-- Play badge -->
          <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm border-2 border-white/70 flex items-center justify-center">
              <svg class="w-5 h-5 text-white fill-current ml-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
            </div>
          </div>
          <!-- Status badge -->
          <span
            :class="['absolute top-2 right-2 text-[10px] font-bold px-2 py-0.5 rounded-full', v.status === 'published' ? 'bg-green-500 text-white' : 'bg-yellow-400 text-gray-800']"
          >{{ v.status }}</span>
        </div>

        <!-- Info -->
        <div class="p-4">
          <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ v.category?.name ?? '—' }}</span>
          <h3 class="font-bold text-gray-800 text-sm leading-snug mt-0.5 line-clamp-2">{{ v.title }}</h3>
          <p class="text-xs text-gray-400 mt-1">{{ v.published_at ? new Date(v.published_at).toLocaleDateString() : 'Draft' }} · {{ v.views?.toLocaleString() }} views</p>
          <!-- Actions -->
          <div class="flex gap-2 mt-3 pt-3 border-t border-gray-100">
            <button @click="openEdit(v)" class="flex-1 flex items-center justify-center gap-1.5 text-xs font-semibold text-gray-600 hover:text-[#0D47A1] hover:bg-blue-50 py-1.5 rounded-lg transition-colors">
              <Pencil :size="13" /> Edit
            </button>
            <a :href="`/article/${v.slug}`" target="_blank" class="flex-1 flex items-center justify-center gap-1.5 text-xs font-semibold text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 py-1.5 rounded-lg transition-colors">
              <ExternalLink :size="13" /> View
            </a>
            <button @click="confirmDelete(v)" class="flex items-center justify-center px-3 text-xs font-semibold text-gray-400 hover:text-red-500 hover:bg-red-50 py-1.5 rounded-lg transition-colors">
              <Trash2 :size="13" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="meta.last_page > 1" class="flex items-center justify-between mt-6">
      <span class="text-xs text-gray-400">Page {{ meta.current_page }} of {{ meta.last_page }}</span>
      <div class="flex gap-1">
        <button @click="page--" :disabled="page === 1" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-40 transition-colors">Prev</button>
        <button @click="page++" :disabled="page === meta.last_page" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-40 transition-colors">Next</button>
      </div>
    </div>

    <!-- ── Create / Edit Modal ── -->
    <Transition name="modal">
      <div v-if="modal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="closeModal" />
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 max-h-[92vh] overflow-y-auto">

          <h3 class="font-black text-gray-900 text-base mb-1">{{ editId ? 'Edit Video Article' : 'Add Video News' }}</h3>
          <p class="text-xs text-gray-400 mb-5">Paste a YouTube, Vimeo or Dailymotion link — the video will be embedded automatically.</p>

          <div class="space-y-4">

            <!-- Video URL -->
            <div>
              <label class="label">Video URL *</label>
              <input
                v-model="form.video_url"
                type="text"
                :class="field(errors.video_url)"
                placeholder="https://youtube.com/watch?v=… or youtu.be/… or vimeo.com/…"
                @input="autoTitle"
              />
              <Err :e="errors.video_url" />
              <!-- Live embed preview -->
              <div v-if="embedPreview" class="mt-3 rounded-xl overflow-hidden border border-gray-200">
                <div class="relative w-full" style="padding-top:56.25%">
                  <iframe :src="embedPreview" class="absolute inset-0 w-full h-full" frameborder="0" allowfullscreen />
                </div>
              </div>
              <p v-else-if="form.video_url && !embedPreview" class="text-xs text-amber-500 mt-1">
                URL not recognised — supported platforms: YouTube, Vimeo, Dailymotion
              </p>
            </div>

            <!-- Title -->
            <div>
              <label class="label">Title *</label>
              <input v-model="form.title" @input="autoSlug" type="text" :class="field(errors.title)" placeholder="Video title" />
              <Err :e="errors.title" />
            </div>

            <!-- Slug -->
            <div>
              <label class="label">Slug *</label>
              <input v-model="form.slug" type="text" :class="field(errors.slug) + ' font-mono text-xs'" placeholder="video-slug" />
              <Err :e="errors.slug" />
            </div>

            <!-- Excerpt -->
            <div>
              <label class="label">Description</label>
              <textarea v-model="form.excerpt" rows="2" :class="field(errors.excerpt) + ' resize-none'" placeholder="Brief description of the video…" />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <!-- Category -->
              <div>
                <label class="label">Category *</label>
                <select v-model="form.category_id" :class="field(errors.category_id)">
                  <option value="">Select…</option>
                  <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
                <Err :e="errors.category_id" />
              </div>
              <!-- Status -->
              <div>
                <label class="label">Status</label>
                <select v-model="form.status" :class="field(errors.status)">
                  <option value="draft">Draft</option>
                  <option value="published">Published</option>
                </select>
              </div>
            </div>

            <!-- Thumbnail -->
            <div>
              <label class="label">Thumbnail Image <span class="font-normal text-gray-400">(optional)</span></label>
              <div class="flex items-center gap-4">
                <img v-if="form.featured_image" :src="form.featured_image" class="w-20 h-14 rounded-lg object-cover border border-gray-200 bg-gray-50 shrink-0" />
                <div v-else class="w-20 h-14 rounded-lg border-2 border-dashed border-gray-200 flex items-center justify-center bg-gray-50 shrink-0">
                  <ImageIcon :size="16" class="text-gray-300" />
                </div>
                <label class="flex items-center gap-2 px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 cursor-pointer hover:bg-gray-50 transition-colors" :class="{ 'opacity-60': uploading }">
                  <Upload :size="14" />
                  {{ uploading ? 'Uploading…' : 'Choose image' }}
                  <input type="file" accept="image/*" class="hidden" :disabled="uploading" @change="uploadImage" />
                </label>
              </div>
              <Err :e="errors.featured_image" />
            </div>

            <!-- Featured toggle -->
            <div class="flex items-center gap-2">
              <input v-model="form.is_featured" type="checkbox" id="vid_featured" class="w-4 h-4 accent-[#0D47A1] rounded" />
              <label for="vid_featured" class="text-sm text-gray-700 cursor-pointer">Mark as featured</label>
            </div>
          </div>

          <p v-if="errors.general" class="text-xs text-red-500 bg-red-50 px-3 py-2 rounded-lg mt-4">{{ errors.general }}</p>

          <div class="flex gap-3 mt-6">
            <button @click="closeModal" class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
            <button @click="save" :disabled="saving || uploading" class="flex-1 bg-[#0D47A1] hover:bg-[#0a2f6e] disabled:opacity-50 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors">
              {{ saving ? 'Saving…' : (editId ? 'Save Changes' : 'Publish Video') }}
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
          <h3 class="font-black text-gray-900 mb-2">Delete Video?</h3>
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
import { ref, computed, watch, onMounted, defineComponent, h } from 'vue'
import axios from 'axios'
import { Plus, Pencil, Trash2, Upload, ExternalLink, Image as ImageIcon } from 'lucide-vue-next'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const Err = defineComponent({ props: ['e'], render() { return this.e ? h('p', { class: 'text-xs text-red-500 mt-1' }, this.e) : null } })

// ── State ─────────────────────────────────────────────────────────────────
const videos     = ref([])
const categories = ref([])
const meta       = ref({})
const loading    = ref(false)
const page       = ref(1)

const modal        = ref(false)
const editId       = ref(null)
const saving       = ref(false)
const uploading    = ref(false)
const errors       = ref({})
const deleteTarget = ref(null)
const toast        = ref('')

const blankForm = () => ({
  title: '', slug: '', excerpt: '', content: '',
  featured_image: '', video_url: '',
  category_id: '', status: 'published', is_featured: false,
})
const form = ref(blankForm())

// ── Embed preview ─────────────────────────────────────────────────────────
const embedPreview = computed(() => resolveEmbed(form.value.video_url))

function resolveEmbed(url) {
  if (!url) return null
  if (url.includes('youtube.com/embed/') || url.includes('player.vimeo.com') || url.includes('dailymotion.com/embed/')) return url
  let m
  if ((m = url.match(/(?:youtube\.com\/(?:watch\?v=|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/))) return `https://www.youtube.com/embed/${m[1]}`
  if ((m = url.match(/vimeo\.com\/(\d+)/))) return `https://player.vimeo.com/video/${m[1]}`
  if ((m = url.match(/dailymotion\.com\/video\/([a-zA-Z0-9]+)/))) return `https://www.dailymotion.com/embed/video/${m[1]}`
  return null
}

// ── Fetch ─────────────────────────────────────────────────────────────────
const fetchVideos = async () => {
  loading.value = true
  const { data } = await axios.get('/api/articles', {
    params: { page: page.value, per_page: 12, has_video: 1 },
  })
  // Filter client-side to video_url articles (server already filters when has_video is passed)
  videos.value = (data.data ?? []).filter(a => a.video_url)
  meta.value = { total: data.total, current_page: data.current_page, last_page: data.last_page }
  loading.value = false
}

const fetchCategories = async () => {
  const { data } = await axios.get('/api/categories', { params: { per_page: 100 } })
  categories.value = data.data ?? data
}

onMounted(() => { fetchVideos(); fetchCategories() })
watch(page, fetchVideos)

// ── Helpers ───────────────────────────────────────────────────────────────
const showToast = (msg) => { toast.value = msg; setTimeout(() => { toast.value = '' }, 2500) }
const field = (err) => `w-full border rounded-lg px-3 py-2 text-sm outline-none transition-colors ${err ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-[#0D47A1]'}`

const autoSlug = () => {
  form.value.slug = form.value.title
    .trim()
    .toLowerCase()
    .replace(/[\s]+/g, '-')
    .replace(/[^\p{L}\p{M}\p{N}-]/gu, '')
    .replace(/-{2,}/g, '-')
    .replace(/^-|-$/g, '')
}

// Fill title placeholder from URL domain when title is empty
const autoTitle = () => { /* user types their own title */ }

// ── Image upload ──────────────────────────────────────────────────────────
const uploadImage = async (e) => {
  const file = e.target.files?.[0]
  if (!file) return
  uploading.value = true
  try {
    const fd = new FormData()
    fd.append('image', file)
    const { data } = await axios.post('/api/upload-image', fd, { headers: { 'Content-Type': 'multipart/form-data' } })
    form.value.featured_image = data.url
  } catch (err) {
    errors.value.featured_image = err.response?.data?.message ?? 'Upload failed.'
  } finally {
    uploading.value = false
    e.target.value = ''
  }
}

// ── CRUD ──────────────────────────────────────────────────────────────────
const openCreate = () => { editId.value = null; form.value = blankForm(); errors.value = {}; modal.value = true }

const openEdit = (v) => {
  editId.value = v.id
  form.value = {
    title: v.title, slug: v.slug, excerpt: v.excerpt ?? '',
    content: v.content ?? '', featured_image: v.featured_image ?? '',
    video_url: v.video_url ?? '', category_id: v.category_id,
    status: v.status, is_featured: v.is_featured,
  }
  errors.value = {}
  modal.value = true
}

const closeModal = () => { modal.value = false }

const save = async () => {
  errors.value = {}
  if (!form.value.video_url?.trim()) { errors.value.video_url = 'Video URL is required.'; return }
  saving.value = true
  try {
    editId.value
      ? await axios.put(`/api/articles/${editId.value}`, form.value)
      : await axios.post('/api/articles', form.value)
    modal.value = false
    showToast(editId.value ? 'Video article updated.' : 'Video article published.')
    fetchVideos()
  } catch (e) {
    const data = e.response?.data
    if (data?.errors) Object.keys(data.errors).forEach(k => { errors.value[k] = data.errors[k][0] })
    else errors.value.general = data?.message ?? 'Something went wrong.'
  } finally {
    saving.value = false
  }
}

const confirmDelete = (v) => { deleteTarget.value = v }
const doDelete = async () => {
  await axios.delete(`/api/articles/${deleteTarget.value.id}`)
  deleteTarget.value = null
  showToast('Video article deleted.')
  fetchVideos()
}
</script>

<style scoped>
.label { @apply block text-xs font-semibold text-gray-600 mb-1; }
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
.fade-up-enter-active, .fade-up-leave-active { transition: opacity 0.2s, transform 0.2s; }
.fade-up-enter-from, .fade-up-leave-to { opacity: 0; transform: translateY(8px); }
</style>
