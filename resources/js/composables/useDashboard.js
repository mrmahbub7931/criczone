import { ref, computed, reactive } from 'vue'
import axios from 'axios'

// ─────────────────────────────────────────────────────────────────────────────
// LIVE DATA  (fetched from API — replaces all mock values)
// ─────────────────────────────────────────────────────────────────────────────

const loading = ref(false)

// Articles: populated by fetchDashboard()
const articles = ref([])

// Stats: populated by fetchDashboard()
const stats = ref({
  totalArticles:  0,
  publishedCount: 0,
  draftCount:     0,
  featuredCount:  0,
  totalViews:     0,
  categories:     0,
  users:          null,
})

// Chart: populated by fetchDashboard()
const chartData = ref([
  { day: 'Mon', date: '', articles: 0, views: 0 },
  { day: 'Tue', date: '', articles: 0, views: 0 },
  { day: 'Wed', date: '', articles: 0, views: 0 },
  { day: 'Thu', date: '', articles: 0, views: 0 },
  { day: 'Fri', date: '', articles: 0, views: 0 },
  { day: 'Sat', date: '', articles: 0, views: 0 },
  { day: 'Sun', date: '', articles: 0, views: 0 },
])

// Categories: populated by fetchDashboard()
const categoriesData = ref([])

// ─────────────────────────────────────────────────────────────────────────────
// STATIC / UI STATE
// ─────────────────────────────────────────────────────────────────────────────

const liveScores = ref([])

const notifications = ref([
  { id: 1, message: 'New comment on "India Clinches Historic Series Win"', time: '2m ago',  read: false, type: 'comment'  },
  { id: 2, message: 'Article "IPL Auction" is trending with 45K views',    time: '15m ago', read: false, type: 'trending' },
  { id: 3, message: 'Live score update pushed for IND vs AUS',              time: '1h ago',  read: true,  type: 'score'    },
  { id: 4, message: 'Draft "T20 World Cup Analysis" needs review',          time: '2h ago',  read: true,  type: 'draft'    },
])

const unreadCount = computed(() => notifications.value.filter(n => !n.read).length)

const sidebarCollapsed = ref(true)

// ─────────────────────────────────────────────────────────────────────────────
// API FETCH
// ─────────────────────────────────────────────────────────────────────────────

let fetched = false  // guard — only fetch once per page lifecycle

const fetchDashboard = async () => {
  if (fetched || loading.value) return
  loading.value = true
  try {
    const { data } = await axios.get('/api/dashboard')

    // Stats
    stats.value = data.stats

    // Recent articles — normalise shape so the template works unchanged
    articles.value = (data.recentArticles ?? []).map(a => ({
      id:       a.id,
      title:    a.title,
      slug:     a.slug,
      status:   a.status,
      views:    a.views ?? 0,
      category: a.category?.name  ?? '—',
      author:   a.author?.name    ?? '—',
      color:    a.category?.color ?? '#0D47A1',
      date:     a.published_at
                  ? new Date(a.published_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
                  : 'Draft',
    }))

    // Chart data
    if (Array.isArray(data.chartData) && data.chartData.length) {
      chartData.value = data.chartData
    }

    // Categories
    if (Array.isArray(data.categories)) {
      categoriesData.value = data.categories.map(c => ({
        ...c,
        articleCount: c.articles_count ?? 0,
      }))
    }

    fetched = true
  } catch (err) {
    console.error('[useDashboard] fetchDashboard failed', err?.response?.status, err?.response?.data)
  } finally {
    loading.value = false
  }
}

// Reset guard when navigating away (Inertia SPA)
if (typeof window !== 'undefined') {
  document.addEventListener('inertia:navigate', () => { fetched = false })
}

// ─────────────────────────────────────────────────────────────────────────────
// ACTIONS  (kept for LiveScores.vue + legacy pages)
// ─────────────────────────────────────────────────────────────────────────────

const deleteArticle      = (id) => { articles.value = articles.value.filter(a => a.id !== id) }
const toggleArticleStatus = (id) => {
  const a = articles.value.find(a => a.id === id)
  if (a) a.status = a.status === 'published' ? 'draft' : 'published'
}
const toggleFeatured     = (id) => {
  articles.value.forEach(a => { a.isFeatured = false })
  const a = articles.value.find(a => a.id === id)
  if (a) a.isFeatured = true
}
const addArticle         = (article) => {
  articles.value.unshift({ ...article, id: Date.now(), views: 0, status: 'draft' })
}
const updateArticle      = (id, updates) => {
  const a = articles.value.find(a => a.id === id)
  if (a) Object.assign(a, updates)
}

const fetchLiveScores = async () => {
  try {
    const { data } = await axios.get('/api/admin/live-scores')
    liveScores.value = data
  } catch { /* non-critical */ }
}

const updateLiveScore = async (id, updates) => {
  try {
    const { data } = await axios.put(`/api/admin/live-scores/${id}`, updates)
    const m = liveScores.value.find(m => m.id === id)
    if (m) Object.assign(m, data)
  } catch (e) { throw e }
}
const addLiveScore = async (match) => {
  try {
    const { data } = await axios.post('/api/admin/live-scores', match)
    liveScores.value.unshift(data)
  } catch (e) { throw e }
}
const removeLiveScore = async (id) => {
  try {
    await axios.delete(`/api/admin/live-scores/${id}`)
    liveScores.value = liveScores.value.filter(m => m.id !== id)
  } catch (e) { throw e }
}

const markAllRead        = () => { notifications.value.forEach(n => { n.read = true }) }

const addCategory        = (cat) => { categoriesData.value.push({ ...cat, articleCount: 0 }) }
const deleteCategory     = (id)  => { categoriesData.value = categoriesData.value.filter(c => c.id !== id) }
const updateCategory     = (id, updates) => {
  const c = categoriesData.value.find(c => c.id === id)
  if (c) Object.assign(c, updates)
}

const toggleSidebar      = () => { sidebarCollapsed.value = !sidebarCollapsed.value }

// ─────────────────────────────────────────────────────────────────────────────
export function useDashboard() {
  return {
    // state
    loading,
    articles,
    stats,
    chartData,
    categoriesData,
    liveScores,
    notifications,
    unreadCount,
    sidebarCollapsed,
    // api
    fetchDashboard,
    fetchLiveScores,
    // actions
    deleteArticle,
    toggleArticleStatus,
    toggleFeatured,
    addArticle,
    updateArticle,
    updateLiveScore,
    addLiveScore,
    removeLiveScore,
    markAllRead,
    addCategory,
    updateCategory,
    deleteCategory,
    toggleSidebar,
  }
}
