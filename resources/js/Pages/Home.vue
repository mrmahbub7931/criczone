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
        <div @click="handleArticleClick(featuredNews)">
          <NewsCard :news="featuredNews" variant="featured" />
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
              :key="cat"
              @click="selectedCategory = cat"
              class="px-4 py-1.5 rounded-full text-xs font-bold transition-all duration-200"
              :class="selectedCategory === cat
                ? 'bg-primary text-white shadow-md'
                : 'bg-white text-gray-500 hover:bg-gray-100 border border-gray-200'"
            >
              {{ cat }}
            </button>
          </div>

          <!-- News Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div
              v-for="news in latestNews"
              :key="news.id"
              @click="handleArticleClick(news)"
            >
              <NewsCard :news="news" />
            </div>
          </div>

          <!-- Empty state -->
          <div v-if="latestNews.length === 0" class="text-center py-16 bg-white rounded-xl">
            <p class="text-gray-400 text-sm">No articles found in this category.</p>
          </div>
        </div>

        <!-- Sidebar -->
        <Sidebar />

      </div>
    </div>

  </MainLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import MainLayout from '@/Layouts/MainLayout.vue'
import Hero from '@/Components/Hero.vue'
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
  'Analysis'
]

const featuredNews = MOCK_NEWS.find(n => n.isFeatured) || MOCK_NEWS[0]

const filteredNews = computed(() => {
  if (selectedCategory.value === 'All') return MOCK_NEWS
  return MOCK_NEWS.filter(n => n.category === selectedCategory.value)
})

const latestNews = computed(() =>
  filteredNews.value.filter(n => n.id !== featuredNews.id)
)

const handleArticleClick = (article) => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>
