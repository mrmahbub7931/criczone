<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6">

      <!-- ===== Top Stories ===== -->
      <section class="py-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
          <!-- Lead story (large) -->
          <div class="lg:col-span-7" @click="handleArticleClick(featuredNews)">
            <NewsCard :news="featuredNews" variant="featured" />
          </div>
          <!-- Side stories (stacked) -->
          <div class="lg:col-span-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-4">
            <div
              v-for="news in headlineNews"
              :key="news.id"
              @click="handleArticleClick(news)"
              class="h-48 sm:h-44 lg:h-auto"
            >
              <NewsCard :news="news" variant="headline" />
            </div>
          </div>
        </div>
      </section>

      <!-- ===== Category Tabs ===== -->
      <div class="border-b border-gray-200 mb-6">
        <div class="flex items-center gap-1 -mb-px overflow-x-auto scrollbar-hide">
          <button
            v-for="cat in categories"
            :key="cat"
            @click="selectedCategory = cat"
            class="px-4 py-3 text-xs font-bold uppercase tracking-wider whitespace-nowrap border-b-2 transition-all duration-150"
            :class="selectedCategory === cat
              ? 'border-secondary text-secondary'
              : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300'"
          >
            {{ cat }}
          </button>
        </div>
      </div>

      <!-- ===== Main Content: News Grid + Sidebar ===== -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 pb-12">

        <!-- News Grid -->
        <div class="lg:col-span-8">
          <div class="flex items-center justify-between mb-5">
            <h2 class="flex items-center gap-2.5 text-lg font-black text-gray-900 uppercase tracking-wide">
              <span class="w-1 h-6 bg-secondary rounded-full" />
              Latest News
            </h2>
            <span class="text-xs text-gray-400 font-medium">
              {{ filteredLatestNews.length }} article{{ filteredLatestNews.length !== 1 ? 's' : '' }}
            </span>
          </div>

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
          <div v-if="filteredLatestNews.length === 0" class="text-center py-20 bg-white rounded-xl border border-gray-100">
            <p class="text-gray-400 text-sm">No articles in this category yet.</p>
            <button
              @click="selectedCategory = 'All'"
              class="mt-2 text-xs font-bold text-primary hover:text-secondary transition-colors"
            >
              Show all articles
            </button>
          </div>

          <!-- Load More -->
          <div v-if="filteredLatestNews.length >= 4" class="mt-8 text-center">
            <button class="px-8 py-2.5 bg-primary hover:bg-primary-dark text-white rounded-lg text-sm font-bold transition-colors duration-150 shadow-md shadow-primary/20">
              Load More
            </button>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-4">
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

const featuredNews = MOCK_NEWS.find(n => n.isFeatured) || MOCK_NEWS[0]

// Two headline cards beside the featured
const headlineNews = MOCK_NEWS
  .filter(n => n.id !== featuredNews.id)
  .slice(0, 2)

const topStoryIds = new Set([featuredNews.id, ...headlineNews.map(n => n.id)])
const remainingNews = MOCK_NEWS.filter(n => !topStoryIds.has(n.id))

const filteredLatestNews = computed(() => {
  if (selectedCategory.value === 'All') return remainingNews
  return remainingNews.filter(n => n.category === selectedCategory.value)
})

const handleArticleClick = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>
