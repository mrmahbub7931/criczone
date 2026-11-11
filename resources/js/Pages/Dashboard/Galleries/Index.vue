<template>
  <DashboardLayout>
    <div class="space-y-6">

      <!-- Header -->
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
          <h2 class="text-xl font-black text-gray-800">Photo Galleries</h2>
          <p class="text-sm text-gray-400 mt-0.5">Manage photo galleries for the Photo of the Day section</p>
        </div>
        <button @click="openCreate"
          class="inline-flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary-dark text-white text-sm font-bold rounded-xl transition-colors">
          <Plus :size="15" /> New Gallery
        </button>
      </div>

      <!-- Search -->
      <div class="relative max-w-sm">
        <Search :size="15" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
        <input v-model="search" @input="debouncedFetch" type="text" placeholder="Search galleries…"
          class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20" />
      </div>

      <!-- Grid -->
      <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <div v-for="i in 6" :key="i" class="rounded-2xl bg-gray-100 animate-pulse h-56" />
      </div>

      <div v-else-if="!galleries.length" class="text-center py-16 text-gray-400">
        <div class="text-5xl mb-3">📸</div>
        <p class="font-semibold">No galleries yet</p>
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <div v-for="g in galleries" :key="g.id"
          class="group bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow">

          <!-- Cover -->
          <div class="relative h-40 bg-gray-100 overflow-hidden">
            <img v-if="g.cover_image" :src="g.cover_image" :alt="g.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
            <div v-else class="w-full h-full flex items-center justify-center text-4xl text-gray-300">📷</div>
            <!-- Status badge -->
            <span :class="['absolute top-3 left-3 text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider',
              g.status === 'published' ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-600']">
              {{ g.status }}
            </span>
            <!-- Photo count -->
            <span class="absolute top-3 right-3 bg-black/50 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
              {{ g.photos_count }} photos
            </span>
          </div>

          <div class="p-4">
            <h3 class="font-black text-gray-800 text-sm line-clamp-1 mb-1">{{ g.title }}</h3>
            <p v-if="g.description" class="text-xs text-gray-400 line-clamp-1 mb-3">{{ g.description }}</p>
            <p v-else class="text-xs text-gray-300 mb-3">No description</p>

            <div class="flex items-center gap-2">
              <button @click="openEdit(g)"
                class="flex-1 py-1.5 text-xs font-bold bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors flex items-center justify-center gap-1.5">
                <Pencil :size="11" /> Edit
              </button>
              <button @click="openPhotos(g)"
                class="flex-1 py-1.5 text-xs font-bold bg-primary/10 hover:bg-primary/20 text-primary rounded-lg transition-colors flex items-center justify-center gap-1.5">
                <Images :size="11" /> Photos
              </button>
              <button @click="confirmDelete(g)"
                class="w-8 h-8 flex items-center justify-center text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                <Trash2 :size="13" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="meta.last_page > 1" class="flex items-center justify-center gap-2">
        <button @click="changePage(page - 1)" :disabled="page === 1"
          class="px-3 py-1.5 text-xs font-bold border border-gray-200 rounded-lg disabled:opacity-40">← Prev</button>
        <span class="text-sm text-gray-500">{{ page }} / {{ meta.last_page }}</span>
        <button @click="changePage(page + 1)" :disabled="page === meta.last_page"
          class="px-3 py-1.5 text-xs font-bold border border-gray-200 rounded-lg disabled:opacity-40">Next →</button>
      </div>

    </div>

    <!-- ── Create / Edit Gallery Modal ── -->
    <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100"
      leave-active-class="transition duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
      <div v-if="galleryModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="galleryModal = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-black text-gray-800">{{ editingGallery ? 'Edit Gallery' : 'New Gallery' }}</h3>
            <button @click="galleryModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:bg-gray-100">
              <X :size="15" />
            </button>
          </div>
          <div class="p-6 space-y-4">
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Title * <span class="text-gray-300 font-normal normal-case">(বাংলা)</span></label>
              <input v-model="galleryForm.title" type="text" placeholder="Gallery title…"
                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20" />
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Title EN <span class="text-gray-300 font-normal normal-case">(English — auto-translated or manual)</span></label>
              <input v-model="galleryForm.title_en" type="text" placeholder="English title…"
                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400/20 text-sky-700" />
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Description</label>
              <textarea v-model="galleryForm.description" rows="2" placeholder="Short description…"
                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 resize-none" />
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Status</label>
              <select v-model="galleryForm.status"
                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 bg-white">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
              </select>
            </div>
          </div>
          <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50">
            <button @click="galleryModal = false" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-xl">Cancel</button>
            <button @click="saveGallery" :disabled="gallerySaving || !galleryForm.title"
              class="px-5 py-2 bg-primary hover:bg-primary-dark disabled:opacity-50 text-white text-sm font-bold rounded-xl transition-colors inline-flex items-center gap-2">
              <svg v-if="gallerySaving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              {{ gallerySaving ? 'Saving…' : 'Save' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- ── Photos Manager Modal ── -->
    <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100"
      leave-active-class="transition duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
      <div v-if="photosModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="photosModal = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden">

          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 shrink-0">
            <div>
              <h3 class="font-black text-gray-800">Photos — {{ activeGallery?.title }}</h3>
              <p class="text-xs text-gray-400 mt-0.5">{{ photosList.length }} photos</p>
            </div>
            <div class="flex items-center gap-2">
              <!-- Translate captions button -->
              <button @click="translateCaptions" :disabled="translating || !photosList.some(p => p.caption)"
                :class="['inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg transition-all',
                  translating ? 'bg-amber-100 text-amber-500 cursor-wait' : 'bg-amber-50 hover:bg-amber-100 text-amber-600 border border-amber-200 disabled:opacity-40 disabled:cursor-not-allowed']">
                <svg v-if="translating" class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
                <svg v-else class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                </svg>
                {{ translating ? 'Translating…' : 'Translate EN' }}
              </button>
              <button @click="photosModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:bg-gray-100">
                <X :size="15" />
              </button>
            </div>
          </div>

          <!-- Upload area -->
          <div class="px-6 pt-4 shrink-0">
            <label class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-gray-200 hover:border-primary/40 rounded-xl p-5 cursor-pointer transition-colors bg-gray-50 hover:bg-blue-50/30">
              <Upload :size="20" class="text-gray-400" />
              <span class="text-sm text-gray-500 font-medium">Click or drag photos to upload</span>
              <span class="text-xs text-gray-400">JPG, PNG, WebP · max 10MB each</span>
              <input type="file" multiple accept="image/*" class="hidden" @change="handleUpload" />
            </label>
            <div v-if="uploadProgress.length" class="mt-3 space-y-1.5">
              <div v-for="(u, i) in uploadProgress" :key="i" class="space-y-0.5">
                <div class="flex items-center gap-2 text-xs text-gray-500">
                  <div class="flex-1 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                    <div :style="{ width: u.pct + '%' }" :class="['h-full rounded-full transition-all', u.error ? 'bg-red-400' : 'bg-primary']" />
                  </div>
                  <span class="shrink-0 w-24 truncate">{{ u.name }}</span>
                  <span v-if="u.error" class="text-red-500">✕</span>
                  <span v-else-if="u.done" class="text-emerald-500">✓</span>
                </div>
                <p v-if="u.error && u.errorMsg" class="text-[10px] text-red-400 pl-0 truncate">{{ u.errorMsg }}</p>
              </div>
            </div>
          </div>

          <!-- Photos grid -->
          <div class="flex-1 overflow-y-auto px-6 py-4">
            <div v-if="!photosList.length" class="text-center py-10 text-gray-300">
              <div class="text-4xl mb-2">📷</div>
              <p class="text-sm">No photos yet — upload some above</p>
            </div>
            <div v-else class="grid grid-cols-2 sm:grid-cols-3 gap-3">
              <div v-for="(photo, idx) in photosList" :key="photo.id"
                class="group relative rounded-xl overflow-hidden bg-gray-100">
                <div class="aspect-square overflow-hidden">
                  <img :src="photo.image_path" :alt="photo.caption || ''" class="w-full h-full object-cover" />
                </div>
                <!-- Caption + photographer inputs -->
                <div class="bg-black/80 px-2 pt-1.5 pb-2 space-y-1">
                  <input
                    :value="photo.caption"
                    @change="updateCaption(photo, $event.target.value)"
                    type="text"
                    placeholder="বাংলা ক্যাপশন…"
                    class="w-full bg-transparent text-white text-[11px] placeholder-white/40 outline-none border-b border-white/20 focus:border-white/60 pb-0.5"
                  />
                  <div class="flex items-center gap-1">
                    <span class="text-sky-400/60 text-[9px] shrink-0 font-bold">EN</span>
                    <input
                      :value="photo.caption_en"
                      @change="updateCaptionEn(photo, $event.target.value)"
                      type="text"
                      placeholder="English caption…"
                      :class="['flex-1 bg-transparent text-[11px] placeholder-white/30 outline-none border-b focus:border-white/40 pb-0.5',
                        photo.caption_en ? 'text-sky-300 border-sky-500/30' : 'text-white/40 border-white/10']"
                    />
                  </div>
                  <div class="flex items-center gap-1">
                    <span class="text-white/40 text-[10px] shrink-0">ছবি:</span>
                    <input
                      :value="photo.photographer"
                      @change="updatePhotographer(photo, $event.target.value)"
                      type="text"
                      placeholder="photographer name…"
                      class="flex-1 bg-transparent text-amber-300 text-[10px] italic placeholder-white/30 outline-none border-b border-white/10 focus:border-white/40 pb-0.5"
                    />
                  </div>
                </div>
                <!-- Remove button -->
                <button @click="removePhoto(photo)"
                  class="absolute top-2 right-2 w-6 h-6 rounded-full bg-black/60 hover:bg-red-600 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                  <X :size="11" />
                </button>
                <!-- Set as cover button -->
                <button @click="setCover(photo)"
                  :title="activeGallery?.cover_image === photo.image_path ? 'Current cover' : 'Set as cover'"
                  :class="['absolute top-2 left-7 w-6 h-6 rounded-full flex items-center justify-center transition-all text-[11px]',
                    activeGallery?.cover_image === photo.image_path
                      ? 'bg-amber-400 text-white'
                      : 'bg-black/60 text-white/60 opacity-0 group-hover:opacity-100 hover:bg-amber-500 hover:text-white']">
                  ★
                </button>
                <!-- Order badge -->
                <div class="absolute top-2 left-2 w-5 h-5 rounded-full bg-black/50 text-white text-[9px] font-bold flex items-center justify-center">
                  {{ idx + 1 }}
                </div>
              </div>
            </div>
          </div>

          <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 shrink-0 flex justify-end">
            <button @click="photosModal = false" class="px-5 py-2 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary-dark transition-colors">Done</button>
          </div>
        </div>
      </div>
    </Transition>

  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'
import { Plus, Search, Pencil, Trash2, X, Images, Upload } from 'lucide-vue-next'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

// ── List state ────────────────────────────────────────────────────────────────
const galleries   = ref([])
const loading     = ref(true)
const search      = ref('')
const page        = ref(1)
const meta        = ref({ last_page: 1, total: 0 })

async function fetchGalleries() {
  loading.value = true
  try {
    const { data } = await axios.get('/api/admin/galleries', {
      params: { search: search.value, page: page.value, per_page: 12 }
    })
    galleries.value = data.data
    meta.value      = { last_page: data.last_page, total: data.total }
  } finally {
    loading.value = false
  }
}

let debounceTimer = null
const debouncedFetch = () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => { page.value = 1; fetchGalleries() }, 400)
}

function changePage(p) {
  if (p < 1 || p > meta.value.last_page) return
  page.value = p; fetchGalleries()
}

fetchGalleries()

// ── Gallery create / edit ─────────────────────────────────────────────────────
const galleryModal   = ref(false)
const gallerySaving  = ref(false)
const editingGallery = ref(null)
const galleryForm    = ref({ title: '', title_en: '', description: '', status: 'draft' })

function openCreate() {
  editingGallery.value = null
  galleryForm.value    = { title: '', title_en: '', description: '', status: 'draft' }
  galleryModal.value   = true
}

function openEdit(g) {
  editingGallery.value = g
  galleryForm.value    = { title: g.title, title_en: g.title_en ?? '', description: g.description ?? '', status: g.status }
  galleryModal.value   = true
}

async function saveGallery() {
  gallerySaving.value = true
  try {
    if (editingGallery.value) {
      await axios.put(`/api/admin/galleries/${editingGallery.value.id}`, galleryForm.value)
    } else {
      await axios.post('/api/admin/galleries', galleryForm.value)
    }
    galleryModal.value = false
    fetchGalleries()
  } catch (e) {
    alert(e.response?.data?.message ?? 'Error saving gallery.')
  } finally {
    gallerySaving.value = false
  }
}

async function confirmDelete(g) {
  if (!confirm(`Delete "${g.title}" and all its photos?`)) return
  await axios.delete(`/api/admin/galleries/${g.id}`)
  fetchGalleries()
}

// ── Photos manager ────────────────────────────────────────────────────────────
const photosModal    = ref(false)
const activeGallery  = ref(null)
const photosList     = ref([])
const uploadProgress = ref([])
const translating    = ref(false)

async function openPhotos(g) {
  activeGallery.value  = g
  photosList.value     = [...(g.photos ?? [])]
  uploadProgress.value = []
  photosModal.value    = true
  // Fetch all photos (adminIndex only loads 1 for thumbnail); also auto-fixes stale cover_image
  try {
    const { data } = await axios.get(`/api/admin/galleries/${g.id}`)
    photosList.value    = data.photos ?? []
    activeGallery.value = { ...g, ...data }
    // Update the gallery card in the grid if cover changed
    const idx = galleries.value.findIndex(x => x.id === g.id)
    if (idx !== -1 && data.cover_image !== g.cover_image) {
      galleries.value.splice(idx, 1, { ...galleries.value[idx], cover_image: data.cover_image })
    }
  } catch { /* keep the 1 photo we already had */ }
}

async function handleUpload(e) {
  const files = Array.from(e.target.files)
  if (!files.length || !activeGallery.value) return
  e.target.value = ''

  // Build progress entries as separate reactive refs using splice so Vue detects changes
  uploadProgress.value = []
  files.forEach(f => uploadProgress.value.push({ name: f.name, pct: 0, error: false, done: false }))

  const galleryId = activeGallery.value.id

  for (let i = 0; i < files.length; i++) {
    const file = files[i]
    const name = file.name
    const setProgress = (patch) => {
      uploadProgress.value.splice(i, 1, { ...uploadProgress.value[i], ...patch })
    }
    try {
      const fd = new FormData()
      fd.append('image', file)

      const { data: upload } = await axios.post('/api/upload-image', fd, {
        headers: { 'Content-Type': 'multipart/form-data' },
        onUploadProgress: (ev) => {
          if (ev.total) setProgress({ pct: Math.round((ev.loaded / ev.total) * 85) })
        },
      })

      const { data: photo } = await axios.post(
        `/api/admin/galleries/${galleryId}/photos`,
        { image_path: upload.url, caption: '' }
      )

      photosList.value.push(photo)
      setProgress({ pct: 100, done: true })
    } catch (err) {
      const msg = err?.response?.data?.message || err?.response?.data?.errors?.image?.[0] || err?.message || 'Upload failed'
      console.error(`Upload failed for ${name}:`, msg)
      setProgress({ pct: 100, error: true, errorMsg: msg })
    }
  }

  fetchGalleries()
}

async function updateCaption(photo, caption) {
  photo.caption = caption
  await axios.put(
    `/api/admin/galleries/${activeGallery.value.id}/photos/${photo.id}`,
    { caption }
  ).catch(() => {})
}

async function setCover(photo) {
  const { data } = await axios.post(`/api/admin/galleries/${activeGallery.value.id}/set-cover`, { image_path: photo.image_path })
  activeGallery.value = { ...activeGallery.value, cover_image: data.cover_image }
  const idx = galleries.value.findIndex(g => g.id === activeGallery.value.id)
  if (idx !== -1) galleries.value.splice(idx, 1, { ...galleries.value[idx], cover_image: data.cover_image })
}

async function translateCaptions() {
  if (translating.value || !activeGallery.value) return
  translating.value = true
  try {
    const { data } = await axios.post(`/api/admin/galleries/${activeGallery.value.id}/translate-captions`)
    if (data.photos) photosList.value = data.photos
    if (data.title_en) {
      activeGallery.value = { ...activeGallery.value, title_en: data.title_en }
      const idx = galleries.value.findIndex(g => g.id === activeGallery.value.id)
      if (idx !== -1) galleries.value.splice(idx, 1, { ...galleries.value[idx], title_en: data.title_en })
    }
  } catch (err) {
    alert(err?.response?.data?.error || 'Translation failed')
  } finally {
    translating.value = false
  }
}

async function updateCaptionEn(photo, caption_en) {
  photo.caption_en = caption_en
  await axios.put(
    `/api/admin/galleries/${activeGallery.value.id}/photos/${photo.id}`,
    { caption_en }
  ).catch(() => {})
}

async function updatePhotographer(photo, photographer) {
  photo.photographer = photographer
  await axios.put(
    `/api/admin/galleries/${activeGallery.value.id}/photos/${photo.id}`,
    { photographer }
  ).catch(() => {})
}

async function removePhoto(photo) {
  if (!confirm('Remove this photo?')) return
  const { data } = await axios.delete(`/api/admin/galleries/${activeGallery.value.id}/photos/${photo.id}`)
  photosList.value = photosList.value.filter(p => p.id !== photo.id)
  // Use splice for Vue reactivity (index assignment doesn't trigger updates)
  if (data.cover_image !== undefined) {
    activeGallery.value = { ...activeGallery.value, cover_image: data.cover_image }
    const idx = galleries.value.findIndex(g => g.id === activeGallery.value.id)
    if (idx !== -1) galleries.value.splice(idx, 1, { ...galleries.value[idx], cover_image: data.cover_image })
  }
  fetchGalleries()
}
</script>
