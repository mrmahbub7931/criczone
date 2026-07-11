<template>
  <Head>
    <title>All Categories – CricZone</title>
    <meta name="description" content="Browse all cricket news categories on CricZone." />
  </Head>

  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">

      <!-- Page header -->
      <div class="flex items-center gap-3 mb-8">
        <div class="h-9 w-1.5 bg-secondary rounded-full" />
        <div>
          <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tight">
            {{ t('categories.title') }}
          </h1>
          <p class="text-sm text-gray-500 mt-0.5">Browse cricket news by topic</p>
        </div>
      </div>

      <!-- Skeleton -->
      <div v-if="loading" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        <div v-for="i in 15" :key="i" class="bg-white rounded-2xl h-28 animate-pulse" />
      </div>

      <!-- Pinned section -->
      <template v-else>
        <div v-if="pinnedCats.length" class="mb-8">
          <div class="flex items-center gap-2 mb-4">
            <span class="text-base">📌</span>
            <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest">{{ t('categories.pinned') }}</h2>
          </div>
          <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
            <a
              v-for="cat in pinnedCats" :key="cat.id"
              :href="`/category/${cat.slug}`"
              class="group relative flex flex-col justify-between bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 overflow-hidden p-4"
            >
              <!-- color bar -->
              <div class="absolute top-0 left-0 right-0 h-1 rounded-t-2xl" :style="{ backgroundColor: cat.color || '#0D47A1' }" />
              <div class="flex items-center gap-2.5 mt-1">
                <span class="w-3 h-3 rounded-full shrink-0" :style="{ backgroundColor: cat.color || '#0D47A1' }" />
                <span class="text-sm font-bold text-gray-800 group-hover:text-primary transition-colors truncate">{{ cat.name }}</span>
              </div>
              <div class="flex items-center justify-between mt-3">
                <span class="text-xs text-gray-400">{{ cat.articles_count ?? 0 }} {{ t('categories.articles') }}</span>
                <ChevronRight :size="13" class="text-gray-300 group-hover:text-primary transition-colors" />
              </div>
            </a>
          </div>
        </div>

        <!-- Divider -->
        <div v-if="pinnedCats.length && otherCats.length" class="border-t border-gray-100 mb-8" />

        <!-- All other categories -->
        <div v-if="otherCats.length">
          <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">{{ t('categories.allCats') }}</h2>
          <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
            <a
              v-for="cat in otherCats" :key="cat.id"
              :href="`/category/${cat.slug}`"
              class="group relative flex flex-col justify-between bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 overflow-hidden p-4"
            >
              <div class="absolute top-0 left-0 right-0 h-1 rounded-t-2xl" :style="{ backgroundColor: cat.color || '#0D47A1' }" />
              <div class="flex items-center gap-2.5 mt-1">
                <span class="w-2.5 h-2.5 rounded-full shrink-0" :style="{ backgroundColor: cat.color || '#0D47A1' }" />
                <span class="text-sm font-semibold text-gray-700 group-hover:text-primary transition-colors truncate">{{ cat.name }}</span>
              </div>
              <div class="flex items-center justify-between mt-3">
                <span class="text-xs text-gray-400">{{ cat.articles_count ?? 0 }} {{ t('categories.articles') }}</span>
                <ChevronRight :size="13" class="text-gray-300 group-hover:text-primary transition-colors" />
              </div>
            </a>
          </div>
        </div>

        <!-- Empty -->
        <div v-if="!pinnedCats.length && !otherCats.length" class="text-center py-24">
          <p class="text-gray-400">{{ t('categories.noArticles') }}</p>
        </div>
      </template>

    </div>
  </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { Head } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import { ChevronRight } from 'lucide-vue-next'
import { useLocale } from '@/composables/useLocale.js'

const { t } = useLocale()

const allCategories = ref([])
const loading       = ref(true)

const pinnedCats = computed(() => allCategories.value.filter(c => c.is_pinned))
const otherCats  = computed(() => allCategories.value.filter(c => !c.is_pinned))

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/categories/public')
    allCategories.value = data
  } catch { /* silent */ } finally {
    loading.value = false
  }
})
</script>
