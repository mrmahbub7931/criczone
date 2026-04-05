<template>
  <Head>
    <title>Video News – CricZone</title>
    <meta name="description" content="Watch the latest cricket video news, highlights, interviews and analysis on CricZone." />
  </Head>

  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">

      <!-- Page header -->
      <div class="flex items-center gap-3 mb-8">
        <div class="h-8 w-1 bg-secondary rounded-full" />
        <div>
          <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tight">
            Video <span class="text-secondary">News</span>
          </h1>
          <p class="text-sm text-gray-400 mt-0.5">Latest cricket videos, highlights &amp; interviews</p>
        </div>
      </div>

      <!-- Skeleton -->
      <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="i in 6" :key="i" class="bg-white rounded-xl overflow-hidden shadow-sm animate-pulse">
          <div class="h-48 bg-gray-200" />
          <div class="p-4 space-y-2">
            <div class="h-3 bg-gray-100 rounded w-1/3" />
            <div class="h-4 bg-gray-200 rounded w-full" />
            <div class="h-4 bg-gray-200 rounded w-2/3" />
          </div>
        </div>
      </div>

      <!-- Empty state -->
      <div v-else-if="!loading && videos.length === 0" class="text-center py-24 text-gray-400">
        <div class="text-5xl mb-4">🎬</div>
        <p class="font-bold text-gray-600 text-lg mb-1">No videos yet</p>
        <p class="text-sm">Check back soon for video news and highlights.</p>
      </div>

      <!-- Video grid -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <a
          v-for="video in videos"
          :key="video.id"
          :href="`/article/${video.slug}`"
          class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5"
        >
          <!-- Thumbnail with play overlay -->
          <div class="relative h-48 bg-gray-900 overflow-hidden">
            <img
              v-if="video.featured_image"
              :src="video.featured_image"
              :alt="video.title"
              class="w-full h-full object-cover opacity-80 group-hover:opacity-70 group-hover:scale-105 transition-all duration-500"
            />
            <div v-else class="w-full h-full bg-gradient-to-br from-gray-800 to-gray-900 flex items-center justify-center">
              <span class="text-4xl">🏏</span>
            </div>

            <!-- Play button -->
            <div class="absolute inset-0 flex items-center justify-center">
              <div class="w-14 h-14 rounded-full bg-white/20 backdrop-blur-sm border-2 border-white/80 flex items-center justify-center group-hover:scale-110 group-hover:bg-white/30 transition-all duration-300">
                <svg class="w-6 h-6 text-white fill-current ml-1" viewBox="0 0 24 24">
                  <path d="M8 5v14l11-7z"/>
                </svg>
              </div>
            </div>

            <!-- Category badge -->
            <span
              v-if="video.category"
              class="absolute top-3 left-3 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider text-white"
              :style="{ backgroundColor: video.category.color || '#0D47A1' }"
            >{{ video.category.name }}</span>

            <!-- Duration-style badge -->
            <span class="absolute bottom-3 right-3 bg-black/70 text-white text-[10px] font-bold px-2 py-0.5 rounded backdrop-blur-sm flex items-center gap-1">
              <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
              Video
            </span>
          </div>

          <!-- Card body -->
          <div class="p-4">
            <h3 class="font-bold text-gray-900 text-sm leading-snug mb-2 line-clamp-2 group-hover:text-primary transition-colors">
              {{ video.title }}
            </h3>
            <p v-if="video.excerpt" class="text-xs text-gray-500 line-clamp-2 mb-3">
              {{ video.excerpt }}
            </p>
            <div class="flex items-center justify-between text-xs text-gray-400">
              <span>{{ video.author?.name || 'Staff' }}</span>
              <span>{{ formatDate(video.published_at) }}</span>
            </div>
          </div>
        </a>
      </div>

      <!-- Load More -->
      <div v-if="hasMore && !loading" class="mt-10 text-center">
        <button
          @click="loadMore"
          :disabled="loadingMore"
          class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary hover:bg-primary-dark disabled:opacity-50 text-white text-sm font-semibold rounded-full transition-colors"
        >
          <span v-if="loadingMore" class="w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin" />
          {{ loadingMore ? 'Loading...' : 'Load More Videos' }}
        </button>
      </div>

    </div>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { Head } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'

const videos     = ref([])
const loading    = ref(true)
const loadingMore = ref(false)
const page       = ref(1)
const hasMore    = ref(false)

const formatDate = (d) => d
  ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
  : ''

const fetchVideos = async (pageNum = 1) => {
  const { data } = await axios.get('/api/articles/videos', { params: { page: pageNum, per_page: 12 } })
  return data
}

onMounted(async () => {
  try {
    const data = await fetchVideos(1)
    videos.value = data.data ?? []
    hasMore.value = (data.current_page ?? 1) < (data.last_page ?? 1)
  } finally {
    loading.value = false
  }
})

const loadMore = async () => {
  loadingMore.value = true
  try {
    page.value++
    const data = await fetchVideos(page.value)
    videos.value.push(...(data.data ?? []))
    hasMore.value = (data.current_page ?? 1) < (data.last_page ?? 1)
  } finally {
    loadingMore.value = false
  }
}
</script>
