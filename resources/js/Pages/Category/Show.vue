<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">

      <!-- Skeleton -->
      <div v-if="loading" class="animate-pulse space-y-8">
        <div class="h-8 w-48 bg-gray-200 rounded-full" />
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="i in 6" :key="i" class="bg-white rounded-xl overflow-hidden shadow-sm">
            <div class="h-48 bg-gray-200" />
            <div class="p-4 space-y-2">
              <div class="h-4 bg-gray-200 rounded w-3/4" />
              <div class="h-3 bg-gray-100 rounded w-full" />
            </div>
          </div>
        </div>
      </div>

      <!-- 404 -->
      <div v-else-if="notFound" class="text-center py-24">
        <p class="text-6xl font-black text-gray-200 mb-4">404</p>
        <p class="text-xl font-bold text-gray-700 mb-2">Category Not Found</p>
        <a href="/" class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary text-white text-sm font-semibold rounded-full hover:bg-primary-dark transition-colors mt-6">
          ← Back to Home
        </a>
      </div>

      <!-- Content -->
      <template v-else>

        <!-- Category header -->
        <div class="flex items-center gap-4 mb-8">
          <div class="flex items-center gap-3">
            <div class="h-10 w-1.5 rounded-full" :style="{ backgroundColor: category.color || '#0D47A1' }" />
            <div>
              <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tight">{{ category.name }}</h1>
              <p v-if="category.description" class="text-sm text-gray-500 mt-0.5">{{ category.description }}</p>
            </div>
          </div>
          <span class="ml-auto text-sm font-semibold text-gray-400 bg-gray-100 px-3 py-1 rounded-full">
            {{ totalArticles }} articles
          </span>
        </div>

        <!-- Articles grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="article in articles"
            :key="article.id"
            class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg cursor-pointer transition-all duration-300 hover:-translate-y-1"
            @click="$router.push ? null : (window.location.href = `/article/${article.slug}`)"
          >
            <a :href="`/article/${article.slug}`" class="block">
              <div class="relative h-48 overflow-hidden">
                <img
                  :src="article.featured_image || `https://placehold.co/800x450/${(category.color||'#0D47A1').replace('#','')}/white?text=${encodeURIComponent(category.name)}`"
                  :alt="article.title"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                />
                <span
                  class="absolute top-3 left-3 text-white text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider backdrop-blur-sm"
                  :style="{ backgroundColor: category.color || '#0D47A1' }"
                >{{ category.name }}</span>
              </div>
              <div class="p-4">
                <h3 class="font-bold text-gray-900 leading-snug mb-2 line-clamp-2 group-hover:text-primary transition-colors duration-200">
                  {{ article.title }}
                </h3>
                <p class="text-sm text-gray-500 line-clamp-2 mb-3">{{ article.excerpt }}</p>
                <div class="flex items-center justify-between text-xs text-gray-400 pt-3 border-t border-gray-100">
                  <span class="flex items-center gap-1.5">
                    <User :size="12" /> {{ article.author?.name || 'Staff Writer' }}
                  </span>
                  <span>{{ formatDate(article.published_at) }}</span>
                </div>
              </div>
            </a>
          </div>
        </div>

        <!-- Empty state -->
        <div v-if="articles.length === 0" class="text-center py-16 bg-white rounded-xl">
          <p class="text-gray-400 text-sm">No published articles in this category yet.</p>
        </div>

        <!-- Load More -->
        <div v-if="hasMorePages" class="flex justify-center mt-10">
          <button
            @click="loadMore"
            :disabled="loadingMore"
            class="inline-flex items-center gap-2 px-8 py-3 bg-primary hover:bg-primary-dark text-white text-sm font-bold rounded-full transition-all duration-200 shadow-md disabled:opacity-60"
          >
            <svg v-if="loadingMore" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
            </svg>
            {{ loadingMore ? 'Loading…' : 'Load More' }}
          </button>
        </div>

      </template>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import MainLayout from '@/Layouts/MainLayout.vue'
import { User } from 'lucide-vue-next'

const props = defineProps({ slug: { type: String, required: true } })

const category     = ref({})
const articles     = ref([])
const loading      = ref(true)
const loadingMore  = ref(false)
const notFound     = ref(false)
const currentPage  = ref(1)
const lastPage     = ref(1)
const totalArticles = ref(0)

const hasMorePages = computed(() => currentPage.value < lastPage.value)

const formatDate = (d) => d
  ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
  : ''

const fetchArticles = async (page = 1, append = false) => {
  if (page === 1) loading.value = true
  else            loadingMore.value = true

  try {
    const { data } = await axios.get(`/api/categories/${props.slug}/articles`, {
      params: { page, per_page: 9 },
    })
    category.value      = data.category
    totalArticles.value = data.articles.total
    currentPage.value   = data.articles.current_page
    lastPage.value      = data.articles.last_page
    articles.value      = append
      ? [...articles.value, ...data.articles.data]
      : data.articles.data
  } catch (err) {
    if (err.response?.status === 404) notFound.value = true
  } finally {
    loading.value    = false
    loadingMore.value = false
  }
}

const loadMore = () => fetchArticles(currentPage.value + 1, true)

onMounted(() => fetchArticles(1))
</script>
