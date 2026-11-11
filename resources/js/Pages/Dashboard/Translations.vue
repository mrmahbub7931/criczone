<template>
  <DashboardLayout>
    <div class="space-y-6">

      <!-- Header -->
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
          <h2 class="text-xl font-black text-gray-800">AI Translations</h2>
          <p class="text-sm text-gray-400 mt-0.5">Translate articles between Bengali and English using AI</p>
        </div>
        <!-- Translate All Untranslated -->
        <button
          @click="translateAll"
          :disabled="translatingAll"
          class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white text-sm font-semibold rounded-lg transition-colors"
        >
          <Zap :size="15" />
          {{ translatingAll ? 'Queuing all…' : 'Translate All Untranslated' }}
        </button>
      </div>

      <!-- Provider banner -->
      <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-100 rounded-xl text-sm">
        <Bot :size="16" class="text-blue-500 shrink-0" />
        <span class="text-blue-700">Provider: <strong>{{ providerLabel }}</strong></span>
        <a href="/dashboard/settings" class="ml-auto text-xs text-blue-500 hover:underline">Change in Settings →</a>
      </div>

      <!-- Search + Stats + Per page -->
      <div class="flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-[200px] max-w-sm">
          <Search :size="15" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
          <input
            v-model="search"
            type="text"
            placeholder="Search articles…"
            class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20"
            @input="debouncedFetch"
          />
        </div>

        <!-- Per page selector -->
        <div class="flex items-center gap-2">
          <label class="text-xs text-gray-500 whitespace-nowrap">Per page:</label>
          <select
            v-model="perPage"
            @change="page = 1; fetchArticles()"
            class="text-sm border border-gray-200 rounded-lg px-2 py-1.5 outline-none focus:border-primary bg-white"
          >
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>

        <div class="flex items-center gap-4 text-sm text-gray-500 ml-auto">
          <span>Total: <strong class="text-gray-800">{{ meta.total }}</strong></span>
          <span>✓ Translated: <strong class="text-emerald-600">{{ meta.translated_count ?? 0 }}</strong></span>
          <span>⏳ Pending: <strong class="text-orange-500">{{ (meta.total ?? 0) - (meta.translated_count ?? 0) }}</strong></span>
        </div>
      </div>

      <!-- Bulk action bar (shows when items selected) -->
      <Transition
        enter-active-class="transition-all duration-200"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition-all duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0 -translate-y-2"
      >
        <div v-if="selected.length" class="flex items-center gap-3 px-4 py-3 bg-indigo-50 border border-indigo-200 rounded-xl">
          <span class="text-sm font-semibold text-indigo-700">{{ selected.length }} selected</span>
          <button
            @click="translateBatch"
            :disabled="batching"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-xs font-bold rounded-lg transition-colors"
          >
            <Zap :size="12" />
            {{ batching ? 'Queuing…' : 'Translate Selected' }}
          </button>
          <button @click="selected = []" class="text-xs text-indigo-500 hover:underline ml-auto">Clear selection</button>
        </div>
      </Transition>

      <!-- Table -->
      <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
              <tr>
                <th class="px-4 py-3 text-left w-8">
                  <input type="checkbox" @change="togglePage" :checked="pageAllSelected" class="rounded" title="Select all on this page" />
                </th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Article</th>
                <th class="px-4 py-3 text-center font-semibold text-gray-600 w-20">Lang</th>
                <th class="px-4 py-3 text-center font-semibold text-gray-600 w-40">Translation</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600 w-48 hidden lg:table-cell">EN Slug</th>
                <th class="px-4 py-3 text-center font-semibold text-gray-600 w-28">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-if="loading">
                <td colspan="6" class="px-4 py-10 text-center text-gray-400">Loading…</td>
              </tr>
              <tr v-else-if="!articles.length">
                <td colspan="6" class="px-4 py-10 text-center text-gray-400">No articles found.</td>
              </tr>
              <tr v-for="art in articles" :key="art.id" class="hover:bg-gray-50/50 transition-colors">
                <td class="px-4 py-3">
                  <input type="checkbox" :value="art.id" v-model="selected" class="rounded" />
                </td>
                <td class="px-4 py-3">
                  <p class="font-medium text-gray-800 line-clamp-1">{{ art.title }}</p>
                  <p class="text-xs text-gray-400 mt-0.5">{{ formatDate(art.published_at) }}</p>
                </td>
                <td class="px-4 py-3 text-center">
                  <span :class="['inline-block text-[11px] font-bold px-2 py-0.5 rounded-full', art.language === 'en' ? 'bg-blue-50 text-blue-700' : 'bg-orange-50 text-orange-700']">
                    {{ art.language === 'en' ? 'EN' : 'বাং' }}
                  </span>
                </td>
                <td class="px-4 py-3 text-center">
                  <span v-if="!getTranslation(art)" class="inline-block text-[11px] font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-400">No translation</span>
                  <span v-else-if="getTranslation(art).status === 'done'" class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ targetLangLabel(art) }} Done
                  </span>
                  <span v-else-if="getTranslation(art).status === 'processing'" class="inline-flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-1 rounded-full bg-yellow-50 text-yellow-700">
                    <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Processing…
                  </span>
                  <span v-else-if="getTranslation(art).status === 'failed'" class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1 rounded-full bg-red-50 text-red-700">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Failed
                  </span>
                  <span v-else class="inline-block text-[11px] font-semibold px-2.5 py-1 rounded-full bg-blue-50 text-blue-600">Pending</span>
                </td>
                <td class="px-4 py-3 hidden lg:table-cell">
                  <span v-if="getTranslation(art)?.en_slug" class="text-xs font-mono text-gray-500 truncate block max-w-[180px]" :title="getTranslation(art).en_slug">
                    {{ getTranslation(art).en_slug }}
                  </span>
                  <span v-else class="text-xs text-gray-300">—</span>
                </td>
                <td class="px-4 py-3 text-center">
                  <div class="inline-flex items-center gap-1.5">
                    <!-- Edit button — only when done -->
                    <button
                      v-if="getTranslation(art)?.status === 'done'"
                      @click="openEdit(art)"
                      class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition-colors"
                      title="Edit translation"
                    >
                      <Pencil :size="11" />
                      Edit
                    </button>
                    <!-- Translate / Re-translate -->
                    <button
                      @click="translateOne(art)"
                      :disabled="isQueuing(art.id)"
                      class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors disabled:opacity-50"
                      :class="getTranslation(art)?.status === 'done' ? 'bg-gray-100 hover:bg-gray-200 text-gray-600' : 'bg-primary hover:bg-primary-dark text-white'"
                    >
                      <Zap :size="12" />
                      {{ isQueuing(art.id) ? 'Queued' : (getTranslation(art)?.status === 'done' ? 'Re-translate' : 'Translate') }}
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination footer -->
        <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 border-t border-gray-100 bg-gray-50">
          <span class="text-xs text-gray-500">
            Showing {{ from }}–{{ to }} of {{ meta.total }} articles
            <span v-if="selected.length" class="ml-2 text-indigo-600 font-semibold">({{ selected.length }} selected)</span>
          </span>

          <div v-if="meta.last_page > 1" class="flex items-center gap-1">
            <button @click="changePage(page - 1)" :disabled="page === 1"
              class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
              ← Prev
            </button>
            <template v-for="p in pageWindow" :key="p">
              <span v-if="p === '…'" class="px-2 text-gray-400 text-xs">…</span>
              <button v-else @click="changePage(p)"
                :class="['w-8 h-8 text-xs font-semibold rounded-lg transition-colors', page === p ? 'bg-primary text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-100']">
                {{ p }}
              </button>
            </template>
            <button @click="changePage(page + 1)" :disabled="page === meta.last_page"
              class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
              Next →
            </button>
          </div>
          <span v-else class="text-xs text-gray-400">Page 1 of 1</span>
        </div>
      </div>

    </div>

    <!-- ── Edit Translation Modal ── -->
    <Transition
      enter-active-class="transition-all duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-all duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="editModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="closeEdit">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden">

          <!-- Modal header -->
          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
              <h3 class="text-lg font-black text-gray-800">Edit Translation</h3>
              <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ editArt?.title }}</p>
            </div>
            <button @click="closeEdit" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition-colors">
              <X :size="16" />
            </button>
          </div>

          <!-- Modal body -->
          <div class="overflow-y-auto flex-1 px-6 py-5 space-y-4">

            <!-- Title -->
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Title</label>
              <input
                v-model="editForm.title"
                type="text"
                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                placeholder="Translated title…"
              />
            </div>

            <!-- Excerpt -->
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Excerpt</label>
              <textarea
                v-model="editForm.excerpt"
                rows="2"
                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none"
                placeholder="Short description…"
              />
            </div>

            <!-- EN Slug -->
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">EN Slug</label>
              <input
                v-model="editForm.en_slug"
                type="text"
                @input="slugManualEdit = true"
                class="w-full px-4 py-2.5 text-sm font-mono border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                placeholder="url-friendly-slug"
              />
              <p class="text-[11px] text-gray-400 mt-1">/article/<span class="text-primary font-mono">{{ editForm.en_slug || '…' }}</span></p>
            </div>

            <!-- Content -->
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Content</label>
              <RichEditor
                v-model="editForm.content"
                class="min-h-[320px]"
              />
            </div>

          </div>

          <!-- Modal footer -->
          <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50">
            <button @click="closeEdit" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
              Cancel
            </button>
            <button
              @click="saveEdit"
              :disabled="editSaving || !editForm.title || !editForm.content"
              class="inline-flex items-center gap-2 px-5 py-2 bg-primary hover:bg-primary-dark disabled:opacity-50 text-white text-sm font-bold rounded-xl transition-colors"
            >
              <svg v-if="editSaving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              {{ editSaving ? 'Saving…' : 'Save Changes' }}
            </button>
          </div>

        </div>
      </div>
    </Transition>

  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import axios from 'axios'
import { Search, Zap, Bot, Pencil, X } from 'lucide-vue-next'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import RichEditor from '@/Components/RichEditor.vue'

const articles      = ref([])
const loading       = ref(true)
const search        = ref('')
const page          = ref(1)
const perPage       = ref(10)
const meta          = ref({ last_page: 1, total: 0, translated_count: 0 })
const selected      = ref([])
const queuingIds    = ref(new Set())
const batching      = ref(false)
const translatingAll = ref(false)
const provider      = ref('claude')

// Edit modal
const editModal      = ref(false)
const editArt        = ref(null)
const editSaving     = ref(false)
const slugManualEdit = ref(false)
const editForm       = ref({ title: '', excerpt: '', content: '', en_slug: '' })

function toSlug(str) {
  return str
    .toLowerCase()
    .replace(/[^\w\s-]/g, '')
    .trim()
    .replace(/[\s_]+/g, '-')
    .replace(/-+/g, '-')
    .slice(0, 120)
}

watch(() => editForm.value.title, (val) => {
  if (!slugManualEdit.value) {
    editForm.value.en_slug = toSlug(val)
  }
})

function openEdit(art) {
  const trans = getTranslation(art)
  if (!trans) return
  editArt.value       = art
  slugManualEdit.value = false
  editForm.value = {
    title:   trans.title   ?? '',
    excerpt: trans.excerpt ?? '',
    content: trans.content ?? '',
    en_slug: trans.en_slug ?? '',
  }
  editModal.value = true
}

function closeEdit() {
  editModal.value = false
  editArt.value   = null
}

async function saveEdit() {
  if (!editArt.value) return
  const targetLang = editArt.value.language === 'bn' ? 'en' : 'bn'
  editSaving.value = true
  try {
    await axios.put(`/api/admin/translations/${editArt.value.id}/${targetLang}`, editForm.value)
    // Reflect locally so table updates without full reload
    const trans = getTranslation(editArt.value)
    if (trans) Object.assign(trans, editForm.value, { status: 'done' })
    closeEdit()
  } catch (e) {
    alert(e.response?.data?.message ?? 'Save failed.')
  } finally {
    editSaving.value = false
  }
}

const providerLabel = computed(() =>
  provider.value === 'openai' ? 'GPT-4.1 mini (OpenAI)' : 'Claude Haiku 4.5 (Anthropic)'
)

const pageAllSelected = computed(() =>
  articles.value.length > 0 && articles.value.every(a => selected.value.includes(a.id))
)

const from = computed(() => meta.value.total === 0 ? 0 : (page.value - 1) * perPage.value + 1)
const to   = computed(() => Math.min(page.value * perPage.value, meta.value.total))

const pageWindow = computed(() => {
  const last = meta.value.last_page
  const cur  = page.value
  if (last <= 7) return Array.from({ length: last }, (_, i) => i + 1)
  const pages = new Set([1, last, cur])
  if (cur > 1)    pages.add(cur - 1)
  if (cur < last) pages.add(cur + 1)
  const sorted = [...pages].sort((a, b) => a - b)
  const result = []
  let prev = 0
  for (const p of sorted) {
    if (p - prev > 1) result.push('…')
    result.push(p)
    prev = p
  }
  return result
})

function getTranslation(art) {
  const targetLang = art.language === 'bn' ? 'en' : 'bn'
  return art.translations?.find(t => t.language === targetLang) ?? null
}

function targetLangLabel(art) {
  return art.language === 'bn' ? 'EN' : 'বাং'
}

const isQueuing = (id) => queuingIds.value.has(id)

// Auto-poll every 3s while any article is processing/pending
const hasProcessing = computed(() =>
  articles.value.some(a => {
    const t = getTranslation(a)
    return t && (t.status === 'processing' || t.status === 'pending')
  })
)

let pollTimer = null
watch(hasProcessing, (val) => {
  if (val && !pollTimer) {
    pollTimer = setInterval(() => fetchArticles(), 3000)
  } else if (!val && pollTimer) {
    clearInterval(pollTimer)
    pollTimer = null
  }
})
onUnmounted(() => { if (pollTimer) clearInterval(pollTimer) })

function togglePage(e) {
  const pageIds = articles.value.map(a => a.id)
  if (e.target.checked) {
    selected.value = [...new Set([...selected.value, ...pageIds])]
  } else {
    selected.value = selected.value.filter(id => !pageIds.includes(id))
  }
}

function formatDate(d) {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

let debounceTimer = null
const debouncedFetch = () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => { page.value = 1; fetchArticles() }, 400)
}

function changePage(p) {
  if (p < 1 || p > meta.value.last_page) return
  page.value = p
  fetchArticles()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

async function fetchArticles() {
  loading.value = true
  try {
    const { data } = await axios.get('/api/admin/translations', {
      params: { search: search.value, page: page.value, per_page: perPage.value }
    })
    articles.value = data.data
    meta.value = {
      last_page:        data.last_page,
      total:            data.total,
      translated_count: data.translated_count ?? 0,
    }
  } finally {
    loading.value = false
  }
}

async function fetchProvider() {
  try {
    const { data } = await axios.get('/api/settings')
    provider.value = data?.ai?.translation_provider ?? 'claude'
  } catch { /* silent */ }
}

async function translateOne(art) {
  queuingIds.value = new Set([...queuingIds.value, art.id])

  // Optimistically mark as processing so the poll watcher starts immediately
  const targetLang = art.language === 'bn' ? 'en' : 'bn'
  const existing = art.translations?.find(t => t.language === targetLang)
  if (existing) {
    existing.status = 'processing'
  } else {
    if (!art.translations) art.translations = []
    art.translations.push({ language: targetLang, status: 'processing' })
  }

  try {
    await axios.post(`/api/admin/translations/${art.id}/translate`)
    fetchArticles()
  } catch (e) {
    alert(e.response?.data?.message ?? 'Failed to queue translation.')
    fetchArticles()
  } finally {
    const next = new Set(queuingIds.value)
    next.delete(art.id)
    queuingIds.value = next
  }
}

async function translateBatch() {
  batching.value = true
  try {
    const { data } = await axios.post('/api/admin/translations/batch', { ids: selected.value })
    alert(data.message)
    selected.value = []
    fetchArticles()
  } finally {
    batching.value = false
  }
}

async function translateAll() {
  if (!confirm('Queue translation for ALL untranslated articles? This may take a while.')) return
  translatingAll.value = true
  try {
    // Fetch all article IDs without translation
    const { data } = await axios.get('/api/admin/translations', {
      params: { page: 1, per_page: 9999 }
    })
    const untranslated = data.data
      .filter(a => !getTranslation(a) || getTranslation(a)?.status === 'failed')
      .map(a => a.id)

    if (!untranslated.length) {
      alert('All articles are already translated!')
      return
    }

    const res = await axios.post('/api/admin/translations/batch', { ids: untranslated })
    alert(res.data.message)
    fetchArticles()
  } catch (e) {
    alert(e.response?.data?.message ?? 'Error occurred.')
  } finally {
    translatingAll.value = false
  }
}

onMounted(() => {
  fetchArticles()
  fetchProvider()
})
</script>
