<template>
  <MainLayout>
    
    <!-- Home Content -->

    <div class="space-y-12">

      <!-- Featured -->
      <section>
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-black uppercase tracking-tighter border-l-4 border-accent pl-3">
            Featured <span class="text-accent">Stories</span>
          </h2>
        </div>

        <div @click="handleArticleClick(featuredNews)">
          <NewsCard :news="featuredNews" variant="featured" />
        </div>
      </section>

      <!-- Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

        <!-- News -->
        <div class="lg:col-span-2 space-y-8">

          <!-- Categories -->
          <div class="flex flex-wrap gap-2 border-b pb-4">
            <button
              v-for="cat in categories"
              :key="cat"
              @click="selectedCategory = cat"
              class="px-3 py-1 rounded-full text-xs font-bold"
              :class="selectedCategory === cat ? 'bg-primary text-white' : 'bg-white text-gray-600'"
            >
              {{ cat }}
            </button>
          </div>

          <!-- News List -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div
              v-for="news in latestNews"
              :key="news.id"
              @click="handleArticleClick(news)"
            >
              <NewsCard :news="news" />
            </div>
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
import NewsCard from '@/Components/NewsCard.vue'
import Sidebar from '@/Components/Sidebar.vue'
import { MOCK_NEWS } from '@/data.js'

const selectedCategory = ref('All')
const selectedArticle = ref(null)

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
  selectedArticle.value = article
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>