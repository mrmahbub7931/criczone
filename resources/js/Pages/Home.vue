<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6">

      <!-- Top Stories Section -->
      <section class="mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

          <!-- Featured / Lead Story (left, spans 3 cols) -->
          <div class="lg:col-span-3" @click="handleArticleClick(featuredNews)">
            <NewsCard :news="featuredNews" variant="featured" />
          </div>

          <!-- Secondary Stories (right, spans 2 cols) -->
          <div class="lg:col-span-2 flex flex-col gap-4">
            <div
              v-for="news in secondaryNews"
              :key="news.id"
              @click="handleArticleClick(news)"
            >
              <NewsCard :news="news" variant="horizontal" />
            </div>
          </div>

        </div>
      </section>

      <!-- Category Filter Bar -->
      <div class="flex items-center gap-3 mb-6 overflow-x-auto scrollbar-hide pb-1">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest flex-shrink-0">Filter:</span>
        <div class="flex gap-1.5">
          <button
            v-for="cat in categories"
            :key="cat"
            @click="selectedCategory = cat"
            class="px-3.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all duration-150"
            :class="selectedCategory === cat
              ? 'bg-primary text-white shadow-sm'
              : 'bg-white text-gray-500 hover:bg-gray-50 border border-gray-200 hover:border-gray-300'"
          >
            {{ cat }}
          </button>
        </div>
      </div>

      <!-- Main Content Grid: News + Sidebar -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Latest News (2 cols) -->
        <div class="lg:col-span-2">
          <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-extrabold text-gray-900 uppercase tracking-wide flex items-center gap-2">
              <span class="w-1 h-5 bg-secondary rounded-full inline-block" />
              Latest News
            </h2>
            <span class="text-xs text-gray-400">
              {{ filteredLatestNews.length }} articles
            </span>
          </div>

          <!-- News Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div
              v-for="news in filteredLatestNews"
              :key="news.id"
              @click="handleArticleClick(news)"
            >
              <NewsCard :news="news" />
            </div>
          </div>

          <!-- Empty state -->
          <div v-if="filteredLatestNews.length === 0" class="text-center py-20 bg-white rounded-xl shadow-sm">
            <div class="text-gray-300 text-4xl mb-3">&#9776;</div>
            <p class="text-gray-400 text-sm font-medium">No articles found in this category.</p>
            <button
              @click="selectedCategory = 'All'"
              class="mt-3 text-xs font-semibold text-primary hover:text-secondary transition-colors"
            >
              View all articles
            </button>
          </div>

          <!-- Load More -->
          <div v-if="filteredLatestNews.length > 0" class="mt-8 text-center">
            <button class="px-6 py-2.5 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition-all duration-150 shadow-sm">
              Load More Articles
            </button>
          </div>
        </div>

        <!-- Sidebar (1 col) -->
        <div class="lg:col-span-1">
          <Sidebar />
        </div>

      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import MainLayout from '@/Layouts/MainLayout.vue'
import NewsCard from '@/Components/NewsCard.vue'
import Sidebar from '@/Components/Sidebar.vue'
import { MOCK_NEWS } from '@/data.js'

const selectedCategory = ref('All')

const categories = [
  'All',
  'International',
  'IPL',
  'Domestic',
  'Test Cricket',
  'T20',
  'Analysis',
]

// Lead / featured story
const featuredNews = MOCK_NEWS.find(n => n.isFeatured) || MOCK_NEWS[0]

// Secondary stories: next 3 after featured
const secondaryNews = MOCK_NEWS
  .filter(n => n.id !== featuredNews.id)
  .slice(0, 3)

// IDs already shown in the top stories section
const topStoryIds = new Set([featuredNews.id, ...secondaryNews.map(n => n.id)])

// Remaining news for the grid (exclude top stories)
const remainingNews = MOCK_NEWS.filter(n => !topStoryIds.has(n.id))

const filteredLatestNews = computed(() => {
  if (selectedCategory.value === 'All') return remainingNews
  return remainingNews.filter(n => n.category === selectedCategory.value)
})

const handleArticleClick = (article) => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>
