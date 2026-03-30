import { ref, computed, reactive } from 'vue'
import { MOCK_NEWS, LIVE_SCORES } from '@/data.js'

// ─── Reactive Articles Store ───────────────────────────────────────────────
const articles = ref(
  MOCK_NEWS.map(n => ({
    ...n,
    status: n.isFeatured ? 'published' : (n.id % 3 === 0 ? 'draft' : 'published'),
    views: Math.floor(Math.random() * 90000) + 10000,
  }))
)

// ─── Reactive Live Scores Store ────────────────────────────────────────────
const liveScores = ref(
  LIVE_MATCHES.map(m => ({ overs: '0.0', ...m }))
)

// ─── Reactive Categories Store ─────────────────────────────────────────────
const categoriesData = reactive([
  { id: 1, name: 'International', slug: 'international', color: '#0D47A1', articleCount: 0, description: 'Test, ODI, T20I international matches' },
  { id: 2, name: 'IPL',           slug: 'ipl',           color: '#FF8F00', articleCount: 0, description: 'Indian Premier League coverage' },
  { id: 3, name: 'T20',           slug: 't20',           color: '#2E7D32', articleCount: 0, description: 'T20 leagues and tournaments' },
  { id: 4, name: 'Test Cricket',  slug: 'test-cricket',  color: '#6A1B9A', articleCount: 0, description: 'Five-day Test match coverage' },
  { id: 5, name: 'Domestic',      slug: 'domestic',      color: '#AD1457', articleCount: 0, description: 'Domestic tournaments worldwide' },
  { id: 6, name: 'Analysis',      slug: 'analysis',      color: '#00838F', articleCount: 0, description: 'Expert analysis and opinion' },
])

// sync article counts
const syncCounts = () => {
  categoriesData.forEach(cat => {
    cat.articleCount = articles.value.filter(a => a.category === cat.name).length
  })
}
syncCounts()

// ─── Notifications ─────────────────────────────────────────────────────────
const notifications = ref([
  { id: 1, message: 'New comment on "India Clinches Historic Series Win"', time: '2m ago', read: false, type: 'comment' },
  { id: 2, message: 'Article "IPL Auction" is trending with 45K views',    time: '15m ago', read: false, type: 'trending' },
  { id: 3, message: 'Live score update pushed for IND vs AUS',              time: '1h ago', read: true,  type: 'score' },
  { id: 4, message: 'Draft "T20 World Cup Analysis" needs review',          time: '2h ago', read: true,  type: 'draft' },
])

const unreadCount = computed(() => notifications.value.filter(n => !n.read).length)

// ─── Stats ─────────────────────────────────────────────────────────────────
const stats = computed(() => ({
  totalArticles:  articles.value.length,
  publishedCount: articles.value.filter(a => a.status === 'published').length,
  draftCount:     articles.value.filter(a => a.status === 'draft').length,
  totalViews:     articles.value.reduce((s, a) => s + a.views, 0),
  liveMatches:    liveScores.value.length,
  categories:     categoriesData.length,
}))

// ─── Chart Data (last 7 days views) ────────────────────────────────────────
const chartData = ref([
  { day: 'Mon', views: 12400, articles: 2 },
  { day: 'Tue', views: 18700, articles: 3 },
  { day: 'Wed', views: 15300, articles: 1 },
  { day: 'Thu', views: 23100, articles: 4 },
  { day: 'Fri', views: 31500, articles: 3 },
  { day: 'Sat', views: 28900, articles: 2 },
  { day: 'Sun', views: 41200, articles: 5 },
])

// ─── Actions ───────────────────────────────────────────────────────────────
const deleteArticle = (id) => {
  articles.value = articles.value.filter(a => a.id !== id)
  syncCounts()
}

const toggleArticleStatus = (id) => {
  const art = articles.value.find(a => a.id === id)
  if (art) art.status = art.status === 'published' ? 'draft' : 'published'
}

const toggleFeatured = (id) => {
  articles.value.forEach(a => { a.isFeatured = false })
  const art = articles.value.find(a => a.id === id)
  if (art) art.isFeatured = true
}

const addArticle = (article) => {
  const newId = Math.max(...articles.value.map(a => a.id)) + 1
  articles.value.unshift({
    ...article,
    id: newId,
    views: 0,
    date: new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }),
    status: 'draft',
    isFeatured: false,
  })
  syncCounts()
}

const updateArticle = (id, updates) => {
  const art = articles.value.find(a => a.id === id)
  if (art) Object.assign(art, updates)
  syncCounts()
}

const updateLiveScore = (id, updates) => {
  const match = liveScores.value.find(m => m.id === id)
  if (match) Object.assign(match, updates)
}

const addLiveScore = (match) => {
  const newId = Math.max(...liveScores.value.map(m => m.id), 0) + 1
  liveScores.value.push({ ...match, id: newId })
}

const removeLiveScore = (id) => {
  liveScores.value = liveScores.value.filter(m => m.id !== id)
}

const markAllRead = () => {
  notifications.value.forEach(n => { n.read = true })
}

const addCategory = (cat) => {
  const newId = Math.max(...categoriesData.map(c => c.id)) + 1
  categoriesData.push({ ...cat, id: newId, articleCount: 0 })
}

const deleteCategory = (id) => {
  const idx = categoriesData.findIndex(c => c.id === id)
  if (idx !== -1) categoriesData.splice(idx, 1)
}

const updateCategory = (id, updates) => {
  const cat = categoriesData.find(c => c.id === id)
  if (cat) Object.assign(cat, updates)
}

// ─── Sidebar Collapsed State ───────────────────────────────────────────────
const sidebarCollapsed = ref(false)
const toggleSidebar = () => { sidebarCollapsed.value = !sidebarCollapsed.value }

export function useDashboard() {
  return {
    // state
    articles,
    liveScores,
    categoriesData,
    notifications,
    chartData,
    stats,
    unreadCount,
    sidebarCollapsed,
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
