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
            {{ t('home.featuredNews') }}
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
              v-for="card in displayedCards.slice(1, 5)"
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

      <!-- ── দিনের সেরা ছবি (Photo of the Day) ──────────────────────────────── -->
      <section v-if="photoOfDay.length" class="mb-12">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-3">
            <div class="h-8 w-1 bg-amber-400 rounded-full" />
            <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight font-bangla">
              {{ t('home.photoOfDay') }}
            </h2>
          </div>
          <a href="/photo-of-day" class="text-xs font-bold text-amber-500 hover:text-amber-600 flex items-center gap-1 transition-colors">
            {{ t('home.viewAllPhotos') }} <ChevronRight :size="13" />
          </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
          <a
            v-for="photo in photoOfDay.slice(0, 5)"
            :key="photo.id"
            :href="`/article/${photo.slug}`"
            class="group relative overflow-hidden rounded-xl aspect-square bg-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5"
          >
            <img
              :src="photo.featured_image || 'https://placehold.co/400x400/f59e0b/white?text=📸'"
              :alt="photo.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
            <!-- Gradient overlay + title -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent" />
            <div class="absolute bottom-0 left-0 right-0 p-2.5">
              <p class="text-white text-[11px] font-bold leading-snug line-clamp-2 font-bangla">{{ photo.title }}</p>
            </div>
            <!-- Badge -->
            <div class="absolute top-2 left-2 bg-amber-400 text-white text-[9px] font-black px-1.5 py-0.5 rounded-full uppercase tracking-wider">📸</div>
          </a>
        </div>
      </section>

      <!-- ── Video News ──────────────────────────────────────────────────────── -->
      <section v-if="homeVideosLoading || homeVideos.length > 0" class="mb-12">

        <!-- Section header -->
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-3">
            <div class="h-8 w-1 bg-secondary rounded-full" />
            <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight">{{ t('home.videos') }}</h2>
          </div>
          <a href="/videos" class="text-xs font-bold text-secondary hover:text-secondary-dark flex items-center gap-1 transition-colors">
            {{ t('home.viewAllVideos') }} <ChevronRight :size="13" />
          </a>
        </div>

        <!-- Skeleton -->
        <div v-if="homeVideosLoading" class="grid grid-cols-1 lg:grid-cols-3 gap-5">
          <div class="lg:col-span-2 bg-gray-200 animate-pulse rounded-2xl h-72" />
          <div class="flex flex-col gap-4">
            <div v-for="i in 3" :key="i" class="flex gap-3 animate-pulse">
              <div class="w-28 h-20 rounded-xl bg-gray-200 shrink-0" />
              <div class="flex-1 space-y-2 py-1">
                <div class="h-3.5 bg-gray-200 rounded w-full" />
                <div class="h-3.5 bg-gray-200 rounded w-3/4" />
                <div class="h-3 bg-gray-100 rounded w-1/3" />
              </div>
            </div>
          </div>
        </div>

        <!-- Loaded -->
        <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-5">

          <!-- ── Featured video (left, large) ── -->
          <a
            :href="`/article/${homeVideos[0].slug}`"
            class="lg:col-span-2 group relative overflow-hidden rounded-2xl bg-gray-900 shadow-lg block"
            style="min-height: 280px;"
          >
            <img
              v-if="homeVideos[0].featured_image"
              :src="homeVideos[0].featured_image"
              :alt="homeVideos[0].title"
              class="w-full h-full object-cover absolute inset-0 opacity-75 group-hover:opacity-60 group-hover:scale-105 transition-all duration-500"
            />
            <div v-else class="absolute inset-0 bg-gradient-to-br from-gray-800 to-gray-900" />

            <!-- Dark gradient overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent" />

            <!-- Play button -->
            <div class="absolute inset-0 flex items-center justify-center">
              <div class="w-16 h-16 rounded-full bg-secondary/90 backdrop-blur-sm flex items-center justify-center group-hover:scale-110 group-hover:bg-secondary transition-all duration-300 shadow-xl">
                <svg class="w-7 h-7 text-white fill-current ml-1" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
              </div>
            </div>

            <!-- Category badge -->
            <span
              v-if="homeVideos[0].category"
              class="absolute top-4 left-4 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider text-white"
              :style="{ backgroundColor: homeVideos[0].category.color || '#0D47A1' }"
            >{{ homeVideos[0].category.name }}</span>

            <!-- Video badge -->
            <span class="absolute top-4 right-4 bg-secondary text-white text-[10px] font-black px-2.5 py-1 rounded-full flex items-center gap-1 uppercase tracking-wider">
              <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
              Video
            </span>

            <!-- Text content -->
            <div class="absolute bottom-0 left-0 right-0 p-5">
              <h3 class="text-white font-black text-lg sm:text-xl leading-snug line-clamp-2 mb-2 group-hover:text-secondary-light transition-colors">
                {{ homeVideos[0].title }}
              </h3>
              <p v-if="homeVideos[0].excerpt" class="text-white/70 text-sm line-clamp-1 mb-3 hidden sm:block">
                {{ homeVideos[0].excerpt }}
              </p>
              <div class="flex items-center gap-4 text-white/60 text-xs">
                <span class="flex items-center gap-1">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  {{ fmtViews(homeVideos[0].views) }} views
                </span>
                <span>{{ formatVideoDate(homeVideos[0].published_at) }}</span>
              </div>
            </div>
          </a>

          <!-- ── Shelf cards (right column) ── -->
          <div class="flex flex-col gap-4 lg:overflow-y-auto">
            <!-- On mobile: horizontal scroll row -->
            <div class="flex gap-4 overflow-x-auto no-scrollbar lg:hidden pb-1">
              <a
                v-for="video in homeVideos.slice(1)"
                :key="video.id"
                :href="`/article/${video.slug}`"
                class="group shrink-0 w-56 bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300"
              >
                <div class="relative h-32 bg-gray-900 overflow-hidden">
                  <img v-if="video.featured_image" :src="video.featured_image" :alt="video.title"
                    class="w-full h-full object-cover opacity-80 group-hover:opacity-70 group-hover:scale-105 transition-all duration-500" />
                  <div v-else class="w-full h-full bg-gradient-to-br from-gray-800 to-gray-900" />
                  <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-9 h-9 rounded-full bg-white/20 border border-white/60 flex items-center justify-center group-hover:bg-white/30 transition-all">
                      <svg class="w-4 h-4 text-white fill-current ml-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                  </div>
                </div>
                <div class="p-3">
                  <p class="text-xs font-bold text-gray-800 line-clamp-2 group-hover:text-primary transition-colors leading-snug">{{ video.title }}</p>
                  <p class="text-[11px] text-gray-400 mt-1.5">{{ fmtViews(video.views) }} views · {{ formatVideoDate(video.published_at) }}</p>
                </div>
              </a>
            </div>

            <!-- On desktop: vertical stack -->
            <a
              v-for="video in homeVideos.slice(1)"
              :key="'d' + video.id"
              :href="`/article/${video.slug}`"
              class="group hidden lg:flex items-start gap-3 bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 p-0"
            >
              <!-- Thumbnail -->
              <div class="relative w-32 shrink-0 h-[84px] bg-gray-900 overflow-hidden rounded-l-xl">
                <img v-if="video.featured_image" :src="video.featured_image" :alt="video.title"
                  class="w-full h-full object-cover opacity-80 group-hover:opacity-70 group-hover:scale-105 transition-all duration-500" />
                <div v-else class="w-full h-full bg-gradient-to-br from-gray-800 to-gray-900" />
                <div class="absolute inset-0 flex items-center justify-center">
                  <div class="w-8 h-8 rounded-full bg-white/20 border border-white/60 flex items-center justify-center group-hover:bg-white/30 transition-all">
                    <svg class="w-3.5 h-3.5 text-white fill-current ml-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                  </div>
                </div>
              </div>
              <!-- Info -->
              <div class="flex-1 min-w-0 py-2.5 pr-3">
                <p class="text-sm font-bold text-gray-800 line-clamp-2 group-hover:text-primary transition-colors leading-snug">{{ video.title }}</p>
                <div class="flex items-center gap-3 mt-1.5 text-xs text-gray-400">
                  <span class="flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    {{ fmtViews(video.views) }}
                  </span>
                  <span>{{ formatVideoDate(video.published_at) }}</span>
                </div>
              </div>
            </a>
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
                {{ t('home.latestNews') }}
              </h2>
            </div>
          </div>

          <!-- Category Filter Strip -->
          <div class="relative mb-8 -mx-1">
            <!-- Left arrow -->
            <button
              v-show="canScrollLeft"
              @click="scrollCats(-1)"
              class="absolute left-0 top-1/2 -translate-y-1/2 z-10 w-7 h-7 flex items-center justify-center bg-white border border-gray-200 rounded-full shadow-md hover:shadow-lg text-gray-500 hover:text-primary transition-all"
            >
              <ChevronLeft :size="14" />
            </button>

            <!-- Scrollable strip -->
            <div
              ref="catStripRef"
              @scroll="updateScrollArrows"
              class="flex items-center gap-1.5 overflow-x-auto no-scrollbar px-1 py-1"
            >
              <button
                v-for="cat in categories"
                :key="cat.id ?? cat.name ?? 'all'"
                @click="onCategoryChange(cat.name)"
                class="shrink-0 flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold transition-all duration-200 whitespace-nowrap"
                :class="selectedCategory === cat.name
                  ? 'bg-primary text-white shadow-md'
                  : 'bg-white text-gray-500 hover:bg-gray-100 border border-gray-200'"
              >
                <span
                  v-if="cat.color"
                  class="w-2 h-2 rounded-full shrink-0"
                  :style="{ backgroundColor: selectedCategory === cat.name ? '#fff' : (cat.color || '#0D47A1') }"
                />
                {{ cat.name ?? t('home.allCategories') }}
              </button>

              <!-- Browse all link (shown when there are many categories) -->
              <a v-if="allCategoriesCount > 12" href="/categories"
                class="shrink-0 flex items-center gap-1 px-3.5 py-1.5 rounded-full text-xs font-bold whitespace-nowrap bg-gray-50 text-primary border border-primary/20 hover:bg-primary hover:text-white transition-all duration-200">
                {{ t('home.viewAll') }}
                <ChevronRight :size="12" />
              </a>
            </div>

            <!-- Right arrow -->
            <button
              v-show="canScrollRight"
              @click="scrollCats(1)"
              class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-7 h-7 flex items-center justify-center bg-white border border-gray-200 rounded-full shadow-md hover:shadow-lg text-gray-500 hover:text-primary transition-all"
            >
              <ChevronRight :size="14" />
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
            <p class="text-gray-400 text-sm">{{ t('home.noArticles') }}</p>
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
              {{ loadingMore ? t('common.loading') : t('home.loadMore') }}
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
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import axios from 'axios'
import MainLayout from '@/Layouts/MainLayout.vue'
import Hero from '@/Components/Hero.vue'
import NewsCard from '@/Components/NewsCard.vue'
import Sidebar from '@/Components/Sidebar.vue'
import { useSettings } from '@/composables/useSettings.js'
import { useLocale }   from '@/composables/useLocale.js'
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'

const { locale, t } = useLocale()

const handleArticleClick = (article) => {
  if (article?.slug && article.slug !== '#') window.location.href = `/article/${article.slug}`
}

// ── Latest News (dynamic) ────────────────────────────────────────────────────
const { get: setting, loaded: settingsLoaded } = useSettings()

// Per-page driven by settings; falls back to 6 until settings load
const latestPerPage = computed(() => {
  const v = parseInt(setting('homepage', 'latest_per_page', '6'))
  return (v > 0 && v <= 48) ? v : 6
})

const latestNews       = ref([])
const latestLoading    = ref(true)
const loadingMore      = ref(false)
const currentPage      = ref(1)
const hasMorePages     = ref(false)
const selectedCategory   = ref(null)   // null = All
const categories         = ref([])
const allCategoriesCount = ref(0)   // total from API, used to show "Browse All" link
const catStripRef      = ref(null)
const canScrollLeft    = ref(false)
const canScrollRight   = ref(false)

const updateScrollArrows = () => {
  const el = catStripRef.value
  if (!el) return
  canScrollLeft.value  = el.scrollLeft > 4
  canScrollRight.value = el.scrollLeft + el.clientWidth < el.scrollWidth - 4
}

const scrollCats = (dir) => {
  const el = catStripRef.value
  if (!el) return
  el.scrollBy({ left: dir * 200, behavior: 'smooth' })
  setTimeout(updateScrollArrows, 300)
}

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
    const params = { page, per_page: latestPerPage.value, lang: locale.value }
    if (selectedCategory.value) params.category = selectedCategory.value

    const { data } = await axios.get('/api/articles/latest', { params })
    const mapped = (data.data ?? []).map(toCardLatest)

    latestNews.value   = append ? [...latestNews.value, ...mapped] : mapped
    currentPage.value  = data.current_page
    hasMorePages.value = data.current_page < data.last_page
  } catch {
    if (!append) latestNews.value = []
  } finally {
    latestLoading.value = false
    loadingMore.value   = false
  }
}

// cat is null for "All", or the category name string
const onCategoryChange = (cat) => {
  selectedCategory.value = cat
  currentPage.value = 1
  fetchLatest(1, false)
}

const loadMore = () => {
  fetchLatest(currentPage.value + 1, true)
}

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/categories/public')
    allCategoriesCount.value = data.length
    // Only show categories where show_in_filter is not explicitly false; top 12
    const filterable = data.filter(c => c.show_in_filter !== false)
    // id: null → the "All" sentinel; name will be rendered via t()
    categories.value = [{ id: null, name: null }, ...filterable.slice(0, 12)]
    // wait for DOM to render, then check scroll arrows
    setTimeout(updateScrollArrows, 100)
  } catch {
    categories.value = [{ id: null, name: null }]
  }

  fetchLatest(1)
})

// ── Featured Story (dynamic) ────────────────────────────────────────────────

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

const fetchFeatured = async () => {
  featuredLoading.value = true
  try {
    const { data } = await axios.get('/api/articles/featured', { params: { lang: locale.value } })
    featuredArticles.value = data.map(toCard)
  } catch {
    featuredArticles.value = []
  } finally {
    featuredLoading.value = false
  }
}

onMounted(fetchFeatured)

// ── Photo of the Day ─────────────────────────────────────────────────────────
const photoOfDay = ref([])

const fetchPhotoOfDay = async () => {
  try {
    const { data } = await axios.get('/api/articles/photo-of-day', { params: { per_page: 5, lang: locale.value } })
    photoOfDay.value = data.data ?? []
  } catch { /* silent */ }
}

onMounted(fetchPhotoOfDay)

// ── Videos section ───────────────────────────────────────────────────────────
const homeVideos        = ref([])
const homeVideosLoading = ref(true)

const fetchHomeVideos = async () => {
  homeVideosLoading.value = true
  try {
    const { data } = await axios.get('/api/articles/videos', { params: { per_page: 4, lang: locale.value } })
    homeVideos.value = data.data ?? []
  } catch { homeVideos.value = [] }
  finally { homeVideosLoading.value = false }
}

onMounted(fetchHomeVideos)

const formatVideoDate = (d) => d
  ? new Date(d).toLocaleDateString(locale.value === 'bn' ? 'bn-BD' : 'en-US', { month: 'short', day: 'numeric', year: 'numeric' })
  : ''

const fmtViews = (n) => {
  if (!n) return '0'
  if (n >= 1_000_000) return (n / 1_000_000).toFixed(1).replace(/\.0$/, '') + 'M'
  if (n >= 1_000)     return (n / 1_000).toFixed(1).replace(/\.0$/, '') + 'K'
  return String(n)
}

// ── Reload all data when locale changes ──────────────────────────────────────
watch(locale, () => {
  selectedCategory.value = null
  fetchLatest(1, false)
  fetchFeatured()
  fetchPhotoOfDay()
  fetchHomeVideos()
})
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
