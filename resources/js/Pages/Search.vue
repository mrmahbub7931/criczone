<template>
  <MainLayout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-10">

      <!-- Search bar -->
      <div class="mb-8">
        <div class="flex items-center gap-3 mb-6">
          <div class="h-8 w-1 bg-primary rounded-full" />
          <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tight">
            Search <span class="text-primary">Articles</span>
          </h1>
        </div>
        <div class="relative">
          <SearchIcon :size="18" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" />
          <input
            ref="inputRef"
            v-model="query"
            type="search"
            placeholder="Search by title, content…"
            class="w-full pl-11 pr-12 py-3.5 text-base border-2 border-gray-200 focus:border-primary rounded-xl outline-none transition-colors bg-white shadow-sm"
            @input="onInput"
            @keydown.escape="query = ''; results = []"
          />
          <button v-if="query" @click="query = ''; results = []; inputRef.focus()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700">
            <X :size="16" />
          </button>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="searching" class="space-y-4">
        <div v-for="i in 4" :key="i" class="bg-white rounded-xl p-4 shadow-sm animate-pulse flex gap-4">
          <div class="w-24 h-20 bg-gray-200 rounded-lg shrink-0" />
          <div class="flex-1 space-y-2">
            <div class="h-4 bg-gray-200 rounded w-3/4" />
            <div class="h-3 bg-gray-100 rounded w-full" />
            <div class="h-3 bg-gray-100 rounded w-1/2" />
          </div>
        </div>
      </div>

      <!-- Results -->
      <div v-else-if="results.length" class="space-y-1">
        <p class="text-xs text-gray-400 mb-3">{{ results.length }} result{{ results.length !== 1 ? 's' : '' }} for "<strong class="text-gray-700">{{ lastQuery }}</strong>"</p>
        <a
          v-for="article in results"
          :key="article.id"
          :href="`/article/${article.slug}`"
          class="flex gap-4 bg-white rounded-xl p-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group"
        >
          <img
            v-if="article.featured_image"
            :src="article.featured_image"
            :alt="article.title"
            class="w-24 h-20 object-cover rounded-lg shrink-0 group-hover:scale-105 transition-transform duration-300"
          />
          <div v-else class="w-24 h-20 rounded-lg shrink-0 flex items-center justify-center"
            :style="{ backgroundColor: (article.category?.color || '#0D47A1') + '20' }">
            <Newspaper :size="24" :style="{ color: article.category?.color || '#0D47A1' }" />
          </div>
          <div class="flex-1 min-w-0">
            <span
              class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider text-white mb-1.5"
              :style="{ backgroundColor: article.category?.color || '#0D47A1' }"
            >{{ article.category?.name || 'Cricket' }}</span>
            <h3 class="font-bold text-gray-900 line-clamp-2 group-hover:text-primary transition-colors text-sm leading-snug mb-1.5">{{ article.title }}</h3>
            <p class="text-xs text-gray-500 line-clamp-2">{{ article.excerpt }}</p>
            <p class="text-xs text-gray-400 mt-2">{{ article.author?.name }} · {{ formatDate(article.published_at) }}</p>
          </div>
        </a>
      </div>

      <!-- No results -->
      <div v-else-if="searched && !results.length" class="text-center py-16 bg-white rounded-xl shadow-sm">
        <SearchIcon :size="40" class="text-gray-200 mx-auto mb-3" />
        <p class="font-bold text-gray-600 mb-1">No results for "{{ lastQuery }}"</p>
        <p class="text-sm text-gray-400">Try different keywords or check your spelling.</p>
      </div>

      <!-- Initial state -->
      <div v-else-if="!query" class="text-center py-16 text-gray-400">
        <SearchIcon :size="40" class="mx-auto mb-3 opacity-30" />
        <p class="text-sm">Start typing to search articles…</p>
      </div>

    </div>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import MainLayout from '@/Layouts/MainLayout.vue'
import { Search as SearchIcon, X, Newspaper } from 'lucide-vue-next'

const query     = ref('')
const results   = ref([])
const searching = ref(false)
const searched  = ref(false)
const lastQuery = ref('')
const inputRef  = ref(null)

let debounceTimer = null

const formatDate = (d) => d
  ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
  : ''

const doSearch = async (q) => {
  if (!q || q.length < 2) { results.value = []; searched.value = false; return }
  searching.value = true
  searched.value  = false
  try {
    const { data } = await axios.get('/api/search', { params: { q } })
    results.value = data.data ?? []
    lastQuery.value = q
    searched.value = true
  } catch {
    results.value = []
  } finally {
    searching.value = false
  }
}

const onInput = () => {
  clearTimeout(debounceTimer)
  if (!query.value.trim()) { results.value = []; searched.value = false; return }
  debounceTimer = setTimeout(() => doSearch(query.value.trim()), 350)
}

// Pre-fill from URL ?q=
onMounted(() => {
  inputRef.value?.focus()
  const params = new URLSearchParams(window.location.search)
  const q = params.get('q')
  if (q) { query.value = q; doSearch(q) }
})
</script>
