<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6">

      <!-- ── Top Stories ── -->
      <section class="pt-6 pb-4">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 auto-rows-[minmax(0,1fr)]">
          <!-- Main spotlight -->
          <div class="lg:col-span-7 min-h-[320px] sm:min-h-[380px]" @click="go(featured)">
            <NewsCard :news="featured" variant="spotlight" />
          </div>
          <!-- Side stories -->
          <div class="lg:col-span-5 grid grid-rows-2 gap-4">
            <div v-for="n in sideStories" :key="n.id" @click="go(n)">
              <NewsCard :news="n" variant="side" />
            </div>
          </div>
        </div>
      </section>

      <!-- ── Category Bar ── -->
      <div class="flex items-center gap-1.5 py-4 border-y border-gray-200 mb-6 overflow-x-auto scrollbar-hide">
        <button
          v-for="c in categories"
          :key="c"
          @click="activeCategory = c"
          class="px-4 py-1.5 text-[12px] font-bold uppercase tracking-wider whitespace-nowrap rounded-full transition-all duration-150"
          :class="activeCategory === c
            ? 'bg-primary text-white shadow-md shadow-primary/20'
            : 'text-gray-400 hover:text-gray-600 hover:bg-gray-100'"
        >
          {{ c }}
        </button>
      </div>

      <!-- ── Content Area ── -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 pb-14">

        <!-- News grid -->
        <div class="lg:col-span-8">
          <div class="flex items-center justify-between mb-5">
            <h2 class="text-base font-black text-gray-900 uppercase tracking-wide flex items-center gap-2">
              <span class="w-1 h-5 rounded-full bg-secondary" />
              Latest News
            </h2>
            <span class="text-[11px] text-gray-400">{{ gridNews.length }} articles</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div v-for="n in gridNews" :key="n.id" @click="go(n)">
              <NewsCard :news="n" />
            </div>
          </div>

          <!-- Empty -->
          <div v-if="gridNews.length === 0" class="text-center py-20 bg-surface rounded-xl border border-gray-100">
            <p class="text-gray-400 text-sm mb-2">No articles in this category.</p>
            <button @click="activeCategory = 'All'" class="text-xs font-bold text-primary hover:text-secondary transition-colors">Show all</button>
          </div>

          <!-- Load more -->
          <div v-if="gridNews.length >= 4" class="mt-8 text-center">
            <button class="px-8 py-2.5 bg-primary hover:bg-primary-dark text-white rounded-full text-[12px] font-bold uppercase tracking-wider transition-all shadow-lg shadow-primary/20">
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

const activeCategory = ref('All')

const categories = ['All', 'International', 'IPL', 'Domestic', 'Test Cricket', 'T20', 'Analysis']

const featured = MOCK_NEWS.find(n => n.isFeatured) || MOCK_NEWS[0]
const sideStories = MOCK_NEWS.filter(n => n.id !== featured.id).slice(0, 2)

const usedIds = new Set([featured.id, ...sideStories.map(n => n.id)])
const remaining = MOCK_NEWS.filter(n => !usedIds.has(n.id))

const gridNews = computed(() => {
  if (activeCategory.value === 'All') return remaining
  return remaining.filter(n => n.category === activeCategory.value)
})

const go = () => window.scrollTo({ top: 0, behavior: 'smooth' })
</script>
