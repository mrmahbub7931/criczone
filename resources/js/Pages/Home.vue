<template>
  <MainLayout>

    <!-- Hero Section -->
    <!-- <Hero /> -->

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">

      <!-- Featured Story -->
      <section class="mb-12">
        <div class="flex items-center gap-3 mb-6">
          <div class="h-8 w-1 bg-secondary rounded-full" />
          <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight">
            Featured <span class="text-secondary">Story</span>
          </h2>
        </div>

        <!-- Skeleton -->
        <div v-if="featuredLoading">
          <div class="rounded-2xl bg-gray-200 animate-pulse h-[400px] md:h-[450px] w-full" />
        </div>

        <!-- ── MAGAZINE layout: first card big, rest small ── -->
        <div v-else-if="featuredLayout === 'magazine'" class="grid grid-cols-1 lg:grid-cols-3 gap-5">
          <!-- Hero card -->
          <div class="lg:col-span-2 cursor-pointer" @click="handleArticleClick(displayedCards[0])">
            <NewsCard :news="displayedCards[0]" variant="featured" />
          </div>
          <!-- Side stack -->
          <div class="flex flex-col gap-4">
            <div
              v-for="card in displayedCards.slice(1, 4)"
              :key="card.id"
              class="cursor-pointer"
              @click="handleArticleClick(card)"
            >
              <div class="group flex gap-3 bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-200 p-3">
                <img
                  :src="card.imageUrl"
                  :alt="card.title"
                  class="w-24 h-20 object-cover rounded-lg flex-shrink-0 group-hover:scale-105 transition-transform duration-300"
                />
                <div class="flex-1 min-w-0">
                  <span class="text-[10px] font-bold text-secondary uppercase tracking-wider">{{ card.category }}</span>
                  <h3 class="text-sm font-bold text-gray-900 leading-snug mt-0.5 line-clamp-2 group-hover:text-primary transition-colors">{{ card.title }}</h3>
                  <p class="text-xs text-gray-400 mt-1">{{ card.author }} · {{ card.readTime }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ── LIST layout: vertical stack ── -->
        <div v-else-if="featuredLayout === 'list'" class="space-y-4">
          <div
            v-for="card in displayedCards"
            :key="card.id"
            class="cursor-pointer group flex gap-4 bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-200"
            @click="handleArticleClick(card)"
          >
            <img
              :src="card.imageUrl"
              :alt="card.title"
              class="w-40 sm:w-52 h-32 object-cover flex-shrink-0 group-hover:scale-105 transition-transform duration-300"
            />
            <div class="flex-1 min-w-0 py-4 pr-4">
              <span
                class="inline-block text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider mb-2"
                :style="{ backgroundColor: '#0D47A1', color: '#fff' }"
              >{{ card.category }}</span>
              <h3 class="font-black text-gray-900 text-base leading-snug mb-2 line-clamp-2 group-hover:text-primary transition-colors">{{ card.title }}</h3>
              <p class="text-sm text-gray-500 line-clamp-2 mb-3">{{ card.excerpt }}</p>
              <div class="flex items-center gap-4 text-xs text-gray-400">
                <span>{{ card.author }}</span>
                <span>{{ card.readTime }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- ── GRID layout (default): card grid ── -->
        <div v-else>
          <!-- First card full-width featured -->
          <div class="cursor-pointer mb-5" @click="handleArticleClick(displayedCards[0])">
            <NewsCard :news="displayedCards[0]" variant="featured" />
          </div>
          <!-- Rest in grid -->
          <div v-if="displayedCards.length > 1" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mt-5">
            <div
              v-for="card in displayedCards.slice(1)"
              :key="card.id"
              class="cursor-pointer"
              @click="handleArticleClick(card)"
            >
              <NewsCard :news="card" />
            </div>
          </div>
        </div>

      </section>

      <!-- Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

        <!-- Latest News -->
        <div class="lg:col-span-2">
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
              <div class="h-8 w-1 bg-primary rounded-full" />
              <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight">
                Latest <span class="text-primary">News</span>
              </h2>
            </div>
          </div>

          <!-- Category Filter -->
          <div class="flex flex-wrap gap-2 mb-8">
            <button
              v-for="cat in categories"
              :key="cat.name"
              @click="onCategoryChange(cat.name)"
              class="px-4 py-1.5 rounded-full text-xs font-bold transition-all duration-200"
              :class="selectedCategory === cat.name
                ? 'bg-primary text-white shadow-md'
                : 'bg-white text-gray-500 hover:bg-gray-100 border border-gray-200'"
            >
              {{ cat.name }}
            </button>
          </div>

          <!-- Skeleton -->
          <div v-if="latestLoading" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div v-for="i in 6" :key="i" class="bg-white rounded-xl overflow-hidden shadow-sm animate-pulse">
              <div class="h-48 bg-gray-200" />
              <div class="p-4 space-y-2">
                <div class="h-4 bg-gray-200 rounded w-3/4" />
                <div class="h-3 bg-gray-100 rounded w-full" />
                <div class="h-3 bg-gray-100 rounded w-2/3" />
              </div>
            </div>
          </div>

          <!-- News Grid -->
          <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div
              v-for="news in latestNews"
              :key="news.id"
              class="cursor-pointer"
              @click="handleArticleClick(news)"
            >
              <NewsCard :news="news" />
            </div>
          </div>

          <!-- Empty state -->
          <div v-if="!latestLoading && latestNews.length === 0" class="text-center py-16 bg-white rounded-xl">
            <p class="text-gray-400 text-sm">No articles found in this category.</p>
          </div>

          <!-- Load More -->
          <div v-if="!latestLoading && hasMorePages" class="flex justify-center mt-8">
            <button
              @click="loadMore"
              :disabled="loadingMore"
              class="inline-flex items-center gap-2 px-8 py-3 bg-primary hover:bg-primary-dark text-white text-sm font-bold rounded-full transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-60"
            >
              <svg v-if="loadingMore" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
              </svg>
              {{ loadingMore ? 'Loading…' : 'Load More' }}
            </button>
          </div>
        </div>

        <!-- Sidebar -->
        <Sidebar />

      </div>
    </div>

  </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import MainLayout from '@/Layouts/MainLayout.vue'
import Hero from '@/Components/Hero.vue'
import NewsCard from '@/Components/NewsCard.vue'
import Sidebar from '@/Components/Sidebar.vue'
import { useSettings } from '@/composables/useSettings.js'

const handleArticleClick = (article) => {
  if (article?.slug && article.slug !== '#') window.location.href = `/article/${article.slug}`
}

// ── Latest News (dynamic) ────────────────────────────────────────────────────
const PER_PAGE = 6

const latestNews      = ref([])
const latestLoading   = ref(true)
const loadingMore     = ref(false)
const currentPage     = ref(1)
const hasMorePages    = ref(false)
const selectedCategory = ref('All')
const categories      = ref([{ id: null, name: 'All' }])

const toCardLatest = (a) => ({
  id:       a.id,
  slug:     a.slug,
  title:    a.title,
  excerpt:  a.excerpt || '',
  imageUrl: a.featured_image || `https://placehold.co/800x450/0D47A1/white?text=${encodeURIComponent(a.title?.slice(0, 20) || 'CricZone')}`,
  category: a.category?.name  || 'Cricket',
  author:   a.author?.name    || 'Staff Writer',
  readTime: a.excerpt ? `${Math.max(1, Math.ceil(a.excerpt.split(' ').length / 200))} min read` : '3 min read',
})

const fetchLatest = async (page = 1, append = false) => {
  if (page === 1) latestLoading.value = true
  else            loadingMore.value   = true

  try {
    const params = { page, per_page: PER_PAGE }
    if (selectedCategory.value !== 'All') params.category = selectedCategory.value

    const { data } = await axios.get('/api/articles/latest', { params })
    const mapped = (data.data ?? []).map(toCardLatest)

    latestNews.value  = append ? [...latestNews.value, ...mapped] : mapped
    currentPage.value = data.current_page
    hasMorePages.value = data.current_page < data.last_page
  } catch {
    if (!append) latestNews.value = []
  } finally {
    latestLoading.value = false
    loadingMore.value   = false
  }
}

const onCategoryChange = (cat) => {
  selectedCategory.value = cat
  currentPage.value = 1
  fetchLatest(1, false)
}

const loadMore = () => {
  fetchLatest(currentPage.value + 1, true)
}

onMounted(async () => {
  // Load categories for filter tabs
  try {
    const { data } = await axios.get('/api/categories/public')
    categories.value = [{ id: null, name: 'All' }, ...data]
  } catch { /* keep default */ }

  fetchLatest(1)
})

// ── Featured Story (dynamic) ────────────────────────────────────────────────
const { get: setting, loaded: settingsLoaded } = useSettings()

const featuredArticles  = ref([])
const featuredLoading   = ref(true)

// Map API article → NewsCard-compatible shape
const toCard = (a) => ({
  id:        a.id,
  slug:      a.slug,
  title:     a.title,
  excerpt:   a.excerpt || '',
  imageUrl:  a.featured_image || `https://placehold.co/800x450/0D47A1/white?text=${encodeURIComponent(a.title?.slice(0, 30) || 'CricZone')}`,
  category:  a.category?.name  || 'Cricket',
  author:    a.author?.name    || 'Staff Writer',
  readTime:  a.excerpt ? `${Math.max(1, Math.ceil(a.excerpt.split(' ').length / 200))} min read` : '3 min read',
  isFeatured: a.is_featured,
})

// Fallback demo cards shown when DB is empty
const demoCards = [
  { id: 'd1', slug: '#', title: 'India Clinches Historic Series Win Against Australia', excerpt: 'A stunning final-day performance sealed the remarkable comeback in one of the greatest Test series of the decade.', imageUrl: 'https://placehold.co/800x450/0D47A1/white?text=Featured+Story', category: 'Test Cricket', author: 'Staff Writer', readTime: '4 min read', isFeatured: true },
  { id: 'd2', slug: '#', title: 'IPL 2026 Auction: Top Buys Revealed', excerpt: 'The mega auction saw record-breaking bids as franchises scrambled to build their dream squads.', imageUrl: 'https://placehold.co/800x450/1565C0/white?text=IPL+News', category: 'IPL', author: 'Reporter', readTime: '3 min read', isFeatured: false },
  { id: 'd3', slug: '#', title: 'T20 World Cup 2026 Schedule Announced', excerpt: 'The ICC officially confirmed the full schedule for the upcoming T20 World Cup with 20 participating nations.', imageUrl: 'https://placehold.co/800x450/0277BD/white?text=T20+WC', category: 'T20', author: 'Desk', readTime: '2 min read', isFeatured: false },
]

const featuredLayout = computed(() => setting('homepage', 'articles_layout', 'grid'))

const displayedCards = computed(() =>
  featuredArticles.value.length ? featuredArticles.value : demoCards
)

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/articles/featured')
    featuredArticles.value = data.map(toCard)
  } catch {
    featuredArticles.value = []
  } finally {
    featuredLoading.value = false
  }
})
</script>
