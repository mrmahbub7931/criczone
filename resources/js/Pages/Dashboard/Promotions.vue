<template>
  <DashboardLayout>

    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-xl font-bold text-gray-900">Promotions & Ads</h1>
        <p class="text-sm text-gray-500 mt-0.5">Manage paid promotions and Google AdSense codes for each position.</p>
      </div>
      <button @click="openCreate"
        class="inline-flex items-center gap-2 px-4 py-2 bg-[#0D47A1] hover:bg-[#0a2f6e] text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Add Promotion
      </button>
    </div>

    <!-- Tabs: Promotions | Google Ads -->
    <div class="flex gap-1 mb-6 bg-gray-100 rounded-xl p-1 w-fit">
      <button v-for="t in ['Promotions','Google Ads']" :key="t" @click="activeTab = t"
        :class="['px-4 py-1.5 text-xs font-semibold rounded-lg transition-all',
                 activeTab === t ? 'bg-white text-[#0D47A1] shadow-sm' : 'text-gray-500 hover:text-gray-700']">
        {{ t }}
      </button>
    </div>

    <!-- ══ PROMOTIONS TAB ══ -->
    <div v-if="activeTab === 'Promotions'">

      <!-- Loading -->
      <div v-if="loading" class="space-y-3">
        <div v-for="i in 4" :key="i" class="h-20 bg-gray-100 rounded-xl animate-pulse" />
      </div>

      <!-- Empty -->
      <div v-else-if="promotions.length === 0" class="text-center py-16 bg-white rounded-xl border border-gray-100">
        <p class="text-4xl mb-3">📢</p>
        <p class="text-gray-500 text-sm">No promotions yet. Add your first paid promotion.</p>
      </div>

      <!-- List -->
      <div v-else class="space-y-3">
        <div v-for="promo in promotions" :key="promo.id"
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-4">

          <!-- Thumbnail -->
          <img :src="promo.image_url" :alt="promo.title"
            class="w-24 h-16 object-cover rounded-lg flex-shrink-0 bg-gray-100" />

          <!-- Info -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
              <span class="font-semibold text-gray-900 text-sm">{{ promo.title }}</span>
              <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 uppercase tracking-wider">
                {{ POSITIONS[promo.position] ?? promo.position }}
              </span>
              <span :class="['text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider',
                promo.is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500']">
                {{ promo.is_active ? 'Active' : 'Inactive' }}
              </span>
            </div>
            <div class="flex items-center gap-4 mt-1 text-xs text-gray-400">
              <span v-if="promo.starts_at">From {{ fmtDate(promo.starts_at) }}</span>
              <span v-if="promo.ends_at">Until {{ fmtDate(promo.ends_at) }}</span>
              <span>Priority {{ promo.priority }}</span>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-2 shrink-0">
            <button @click="toggleActive(promo)"
              :class="['px-3 py-1.5 text-xs font-semibold rounded-lg border transition-colors',
                promo.is_active
                  ? 'border-orange-200 text-orange-600 hover:bg-orange-50'
                  : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50']">
              {{ promo.is_active ? 'Disable' : 'Enable' }}
            </button>
            <button @click="openEdit(promo)"
              class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
              Edit
            </button>
            <button @click="deletePromo(promo)"
              class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition-colors">
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ══ GOOGLE ADS TAB ══ -->
    <div v-if="activeTab === 'Google Ads'" class="space-y-4">
      <div v-if="adsLoading" class="space-y-3">
        <div v-for="i in 6" :key="i" class="h-28 bg-gray-100 rounded-xl animate-pulse" />
      </div>
      <template v-else>
        <div v-for="slot in adSlots" :key="slot.key" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
          <div class="flex items-center justify-between mb-3">
            <div>
              <p class="font-semibold text-gray-900 text-sm">{{ slot.label }}</p>
              <p class="text-xs text-gray-400 mt-0.5">Position: <code class="bg-gray-100 px-1 rounded">{{ slot.key }}</code></p>
            </div>
            <!-- Enable toggle -->
            <label class="flex items-center gap-2 cursor-pointer">
              <span class="text-xs text-gray-500">{{ adsForm[slot.enabledKey] === '1' ? 'Enabled' : 'Disabled' }}</span>
              <div @click="adsForm[slot.enabledKey] = adsForm[slot.enabledKey] === '1' ? '0' : '1'"
                :class="['w-9 h-5 rounded-full transition-colors relative cursor-pointer',
                         adsForm[slot.enabledKey] === '1' ? 'bg-[#0D47A1]' : 'bg-gray-300']">
                <span :class="['absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform',
                               adsForm[slot.enabledKey] === '1' ? 'translate-x-4' : 'translate-x-0.5']" />
              </div>
            </label>
          </div>
          <!-- Special: popup delay -->
          <div v-if="slot.key === 'popup'" class="mb-3">
            <label class="block text-xs font-semibold text-gray-600 mb-1">Delay (seconds before popup appears)</label>
            <input v-model="adsForm.ad_popup_delay" type="number" min="0" max="60"
              class="w-24 border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs outline-none focus:border-[#0D47A1]" />
          </div>
          <textarea v-model="adsForm[slot.codeKey]" rows="4"
            :placeholder="`Paste Google AdSense code for ${slot.label}…`"
            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs font-mono outline-none focus:border-[#0D47A1] resize-none"
          />
        </div>
        <div class="flex justify-end">
          <button @click="saveAds" :disabled="adsSaving"
            class="px-5 py-2.5 bg-[#0D47A1] hover:bg-[#0a2f6e] disabled:opacity-60 text-white text-sm font-semibold rounded-xl transition-colors">
            {{ adsSaving ? 'Saving…' : 'Save Ad Codes' }}
          </button>
        </div>
      </template>
    </div>

    <!-- ══ CREATE / EDIT MODAL ══ -->
    <Transition name="modal">
      <div v-if="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closeModal" />
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-y-auto" style="max-height:90vh">

          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-900">{{ editId ? 'Edit Promotion' : 'Add Promotion' }}</h3>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>

          <div class="px-6 py-5 space-y-4">

            <!-- Title -->
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Title (internal)</label>
              <input v-model="form.title" type="text" placeholder="e.g. Grameenphone Banner Nov"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
            </div>

            <!-- Image -->
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Banner Image</label>
              <div class="flex items-center gap-3">
                <img v-if="form.image_url" :src="form.image_url" class="w-20 h-14 object-cover rounded-lg bg-gray-100" />
                <div class="flex-1 space-y-1.5">
                  <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border border-[#0D47A1] text-[#0D47A1] hover:bg-blue-50 rounded-lg transition-colors">
                    <input type="file" class="hidden" accept="image/*" @change="uploadBanner" :disabled="uploading" />
                    {{ uploading ? 'Uploading…' : 'Upload Image' }}
                  </label>
                  <input v-model="form.image_url" type="url" placeholder="or paste image URL…"
                    class="w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs outline-none focus:border-[#0D47A1]" />
                </div>
              </div>
            </div>

            <!-- Link URL -->
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Link URL (optional)</label>
              <input v-model="form.link_url" type="url" placeholder="https://…"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
            </div>

            <!-- Position -->
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Position</label>
              <select v-model="form.position"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1] bg-white">
                <option value="" disabled>Select a position…</option>
                <option v-for="(label, key) in POSITIONS" :key="key" :value="key">{{ label }}</option>
              </select>
            </div>

            <!-- Date range -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Start Date (optional)</label>
                <input v-model="form.starts_at" type="datetime-local"
                  class="w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs outline-none focus:border-[#0D47A1]" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">End Date (optional)</label>
                <input v-model="form.ends_at" type="datetime-local"
                  class="w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs outline-none focus:border-[#0D47A1]" />
              </div>
            </div>

            <!-- Priority + options row -->
            <div class="flex items-center gap-4">
              <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Priority (higher = shown first)</label>
                <input v-model.number="form.priority" type="number" min="0"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
              </div>
              <div class="flex flex-col gap-2 mt-4">
                <label class="flex items-center gap-2 cursor-pointer text-xs text-gray-600">
                  <input type="checkbox" v-model="form.new_tab" class="rounded" /> Open in new tab
                </label>
                <label class="flex items-center gap-2 cursor-pointer text-xs text-gray-600">
                  <input type="checkbox" v-model="form.is_active" class="rounded" /> Active
                </label>
              </div>
            </div>
          </div>

          <div class="flex gap-3 px-6 pb-6">
            <button @click="closeModal"
              class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-xl hover:bg-gray-50 transition-colors">
              Cancel
            </button>
            <button @click="savePromo" :disabled="saving"
              class="flex-1 bg-[#0D47A1] hover:bg-[#0a2f6e] disabled:opacity-60 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors">
              {{ saving ? 'Saving…' : (editId ? 'Update' : 'Create') }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const activeTab = ref('Promotions')

// ── Positions list (mirrors backend) ─────────────────────────────────────────
const POSITIONS = {
  topbar:              'Top Bar (above navbar)',
  after_ticker:        'After Breaking News Ticker',
  home_after_featured: 'Home — After Featured News',
  home_sidebar_top:    'Home Sidebar — Top',
  home_sidebar_mid:    'Home Sidebar — Middle',
  home_sidebar_bottom: 'Home Sidebar — Bottom (below newsletter)',
  article_below_title: 'Article — Below Title',
  article_middle:      'Article — Middle (after 2nd paragraph)',
  article_bottom:      'Article — Bottom',
  footer_top:          'Above Footer',
  popup:               'Popup (center screen, once per session)',
  floating:            'Floating (bottom-right corner)',
}

// ── Promotions CRUD ───────────────────────────────────────────────────────────
const promotions = ref([])
const loading    = ref(true)

const fetchPromotions = async () => {
  loading.value = true
  try {
    const { data } = await axios.get('/api/admin/promotions')
    promotions.value = data
  } finally {
    loading.value = false
  }
}

onMounted(fetchPromotions)

const fmtDate = (d) => d ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : ''

// ── Modal state ───────────────────────────────────────────────────────────────
const modalOpen = ref(false)
const editId    = ref(null)
const saving    = ref(false)
const uploading = ref(false)

const blankForm = () => ({
  title: '', image_url: '', link_url: '', position: '',
  new_tab: true, is_active: true, priority: 0,
  starts_at: '', ends_at: '',
})

const form = ref(blankForm())

const openCreate = () => { form.value = blankForm(); editId.value = null; modalOpen.value = true }
const openEdit   = (p) => {
  form.value = {
    title: p.title, image_url: p.image_url, link_url: p.link_url || '',
    position: p.position, new_tab: p.new_tab, is_active: p.is_active,
    priority: p.priority,
    starts_at: p.starts_at ? p.starts_at.slice(0, 16) : '',
    ends_at:   p.ends_at   ? p.ends_at.slice(0, 16)   : '',
  }
  editId.value = p.id
  modalOpen.value = true
}
const closeModal = () => { modalOpen.value = false }

const uploadBanner = async (e) => {
  const file = e.target.files[0]
  if (!file) return
  uploading.value = true
  try {
    const fd = new FormData()
    fd.append('image', file)
    const { data } = await axios.post('/api/upload-image', fd)
    form.value.image_url = data.url
  } finally {
    uploading.value = false
  }
}

const savePromo = async () => {
  if (!form.value.title || !form.value.image_url || !form.value.position) return
  saving.value = true
  try {
    if (editId.value) {
      const { data } = await axios.put(`/api/admin/promotions/${editId.value}`, form.value)
      const idx = promotions.value.findIndex(p => p.id === editId.value)
      if (idx !== -1) promotions.value[idx] = data
    } else {
      const { data } = await axios.post('/api/admin/promotions', form.value)
      promotions.value.unshift(data)
    }
    closeModal()
  } finally {
    saving.value = false
  }
}

const toggleActive = async (promo) => {
  const { data } = await axios.put(`/api/admin/promotions/${promo.id}`, { is_active: !promo.is_active })
  const idx = promotions.value.findIndex(p => p.id === promo.id)
  if (idx !== -1) promotions.value[idx] = data
}

const deletePromo = async (promo) => {
  if (!confirm(`Delete "${promo.title}"?`)) return
  await axios.delete(`/api/admin/promotions/${promo.id}`)
  promotions.value = promotions.value.filter(p => p.id !== promo.id)
}

// ── Google Ads settings ───────────────────────────────────────────────────────
const adsLoading = ref(true)
const adsSaving  = ref(false)

const adSlots = [
  { key: 'topbar',              label: 'Top Bar (above navbar)',                    codeKey: 'ad_topbar',              enabledKey: 'ad_topbar_enabled' },
  { key: 'after_ticker',        label: 'After Breaking News Ticker',                codeKey: 'ad_after_ticker',        enabledKey: 'ad_after_ticker_enabled' },
  { key: 'home_after_featured', label: 'Home — After Featured News',                codeKey: 'ad_home_after_featured', enabledKey: 'ad_home_after_featured_enabled' },
  { key: 'home_sidebar_top',    label: 'Home Sidebar — Top',                        codeKey: 'ad_home_sidebar_top',    enabledKey: 'ad_home_sidebar_top_enabled' },
  { key: 'home_sidebar_mid',    label: 'Home Sidebar — Middle',                     codeKey: 'ad_home_sidebar_mid',    enabledKey: 'ad_home_sidebar_mid_enabled' },
  { key: 'home_sidebar_bottom', label: 'Home Sidebar — Bottom (below newsletter)',  codeKey: 'ad_home_sidebar_bottom', enabledKey: 'ad_home_sidebar_bottom_enabled' },
  { key: 'article_below_title', label: 'Article — Below Title',                    codeKey: 'ad_article_below_title', enabledKey: 'ad_article_below_title_enabled' },
  { key: 'article_middle',      label: 'Article — Middle (after 2nd paragraph)',    codeKey: 'ad_article_middle',      enabledKey: 'ad_article_middle_enabled' },
  { key: 'article_bottom',      label: 'Article — Bottom',                          codeKey: 'ad_article_bottom',      enabledKey: 'ad_article_bottom_enabled' },
  { key: 'footer_top',          label: 'Above Footer',                              codeKey: 'ad_footer_top',          enabledKey: 'ad_footer_top_enabled' },
  { key: 'popup',               label: 'Popup (center screen)',                     codeKey: 'ad_popup',               enabledKey: 'ad_popup_enabled' },
  { key: 'floating',            label: 'Floating (bottom-right corner)',             codeKey: 'ad_floating',            enabledKey: 'ad_floating_enabled' },
]

const adsForm = ref({
  ad_popup_delay: '5',
  ...Object.fromEntries(adSlots.flatMap(s => [[s.codeKey, ''], [s.enabledKey, '1']])),
})

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/settings')
    const ads = data.ads ?? {}
    Object.keys(adsForm.value).forEach(k => {
      if (ads[k] !== undefined) adsForm.value[k] = ads[k]
    })
  } finally {
    adsLoading.value = false
  }
})

const saveAds = async () => {
  adsSaving.value = true
  try {
    await axios.put('/api/settings', { ads: adsForm.value })
  } finally {
    adsSaving.value = false
  }
}
</script>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
