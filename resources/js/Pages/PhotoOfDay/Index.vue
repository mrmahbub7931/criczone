<template>
  <Head>
    <title>দিনের সেরা ছবি – CricZone</title>
    <meta name="description" content="CricZone-এর দিনের সেরা ক্রিকেট ছবি।" />
  </Head>

  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">

      <!-- Header -->
      <div class="flex items-center gap-3 mb-8">
        <div class="h-9 w-1.5 bg-amber-400 rounded-full" />
        <div>
          <h1 class="text-3xl font-black text-gray-900 font-bangla">
            {{ t('photoOfDay.title') }}
          </h1>
          <p class="text-sm text-gray-500 mt-0.5 font-bangla">ক্রিকেটের সেরা মুহূর্তের ছবি</p>
        </div>
      </div>

      <!-- Skeleton -->
      <div v-if="loading" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        <div v-for="i in 8" :key="i" class="aspect-square rounded-xl bg-gray-200 animate-pulse" />
      </div>

      <!-- Empty -->
      <div v-else-if="photos.length === 0" class="text-center py-24">
        <div class="text-5xl mb-4">📸</div>
        <p class="font-bold text-gray-600 text-lg font-bangla">{{ t('photoOfDay.noPhotos') }}</p>
        <p class="text-sm text-gray-400 mt-1 font-bangla">শীঘ্রই আসছে</p>
      </div>

      <!-- Grid -->
      <template v-else>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
          <a
            v-for="photo in photos"
            :key="photo.id"
            :href="`/article/${photo.slug}`"
            class="group relative overflow-hidden rounded-xl aspect-square bg-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5"
          >
            <img
              :src="photo.featured_image || 'https://placehold.co/400x400/f59e0b/white?text=📸'"
              :alt="photo.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/10 to-transparent" />
            <div class="absolute bottom-0 left-0 right-0 p-3">
              <p class="text-white text-xs font-bold leading-snug line-clamp-2 font-bangla">{{ photo.title }}</p>
              <p class="text-white/60 text-[10px] mt-1">{{ formatDate(photo.published_at) }}</p>
            </div>
            <div class="absolute top-2.5 left-2.5 bg-amber-400 text-white text-[9px] font-black px-1.5 py-0.5 rounded-full tracking-wider">📸</div>
          </a>
        </div>

        <!-- Load More -->
        <div v-if="hasMore" class="flex justify-center mt-10">
          <button
            @click="loadMore"
            :disabled="loadingMore"
            class="inline-flex items-center gap-2 px-8 py-3 bg-amber-400 hover:bg-amber-500 text-white text-sm font-bold rounded-full transition-all shadow-md disabled:opacity-60"
          >
            <svg v-if="loadingMore" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
            </svg>
            {{ loadingMore ? t('common.loading') : t('photoOfDay.loadMore') }}
          </button>
        </div>
      </template>

    </div>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import { Head } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import { useLocale } from '@/composables/useLocale.js'

const { locale, t } = useLocale()

const photos      = ref([])
const loading     = ref(true)
const loadingMore = ref(false)
const currentPage = ref(1)
const lastPage    = ref(1)
const hasMore     = ref(false)

const formatDate = (d) => d
  ? new Date(d).toLocaleDateString(locale.value === 'bn' ? 'bn-BD' : 'en-US', { year: 'numeric', month: 'long', day: 'numeric' })
  : ''

const fetchPhotos = async (page = 1, append = false) => {
  if (page === 1) loading.value = true
  else            loadingMore.value = true

  try {
    const { data } = await axios.get('/api/articles/photo-of-day', { params: { page, per_page: 12, lang: locale.value } })
    const items = data.data ?? []
    photos.value      = append ? [...photos.value, ...items] : items
    currentPage.value = data.current_page
    lastPage.value    = data.last_page
    hasMore.value     = data.current_page < data.last_page
  } catch { /* silent */ } finally {
    loading.value     = false
    loadingMore.value = false
  }
}

const loadMore = () => fetchPhotos(currentPage.value + 1, true)

onMounted(() => fetchPhotos(1))

watch(locale, () => { currentPage.value = 1; fetchPhotos(1) })
</script>
