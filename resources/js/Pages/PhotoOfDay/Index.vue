<template>
  <Head>
    <title>{{ locale === 'en' ? 'Photo of the Day – CricZone' : 'দিনের সেরা ছবি – CricZone' }}</title>
    <meta name="description" :content="locale === 'en' ? 'Best cricket moments in photos by CricZone.' : 'CricZone-এর দিনের সেরা ক্রিকেট ছবি।'" />
  </Head>

  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">

      <!-- Header -->
      <div class="flex items-center gap-3 mb-8">
        <div class="h-9 w-1.5 bg-amber-400 rounded-full" />
        <div>
          <h1 class="text-3xl font-black text-gray-900 dark:text-slate-100">
            {{ t('photoOfDay.title') }}
          </h1>
          <p class="text-sm text-gray-500 mt-0.5">{{ locale === 'en' ? 'Best cricket moments in photos' : 'ক্রিকেটের সেরা মুহূর্তের ছবি' }}</p>
        </div>
      </div>

      <!-- Skeleton -->
      <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="i in 6" :key="i" class="rounded-2xl bg-gray-200 animate-pulse h-64" />
      </div>

      <!-- Empty -->
      <div v-else-if="!galleries.length" class="text-center py-24">
        <div class="text-5xl mb-4">📸</div>
        <p class="font-bold text-gray-600 text-lg">{{ t('photoOfDay.noPhotos') }}</p>
        <p class="text-sm text-gray-400 mt-1">শীঘ্রই আসছে</p>
      </div>

      <!-- Gallery grid -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <a
          v-for="gallery in galleries"
          :key="gallery.id"
          :href="`/photo-of-day/${gallery.slug}`"
          class="group relative overflow-hidden rounded-2xl bg-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5"
        >
          <!-- Cover image -->
          <div class="aspect-[4/3] overflow-hidden">
            <img
              :src="gallery.cover_image || 'https://placehold.co/600x450/f59e0b/white?text=📸'"
              :alt="gallery.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
          </div>

          <!-- Overlay -->
          <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/15 to-transparent" />

          <!-- Photo count badge -->
          <div class="absolute top-3 right-3 flex items-center gap-1 bg-black/50 backdrop-blur-sm text-white text-[10px] font-bold px-2.5 py-1 rounded-full">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
            {{ gallery.photos_count }}
          </div>

          <!-- Bottom info -->
          <div class="absolute bottom-0 left-0 right-0 p-4">
            <p class="text-amber-400 text-[10px] font-black uppercase tracking-widest mb-1">📸 Photo Gallery</p>
            <h2 class="text-white font-black text-base leading-snug line-clamp-2">{{ (locale === 'en' && gallery.title_en) ? gallery.title_en : gallery.title }}</h2>
            <p v-if="gallery.description" class="text-white/60 text-xs mt-1 line-clamp-1">{{ gallery.description }}</p>
            <p class="text-white/40 text-[11px] mt-1.5">{{ formatDate(gallery.published_at) }}</p>
          </div>
        </a>
      </div>

      <!-- Load More -->
      <div v-if="hasMore" class="flex justify-center mt-10">
        <button @click="loadMore" :disabled="loadingMore"
          class="inline-flex items-center gap-2 px-8 py-3 bg-amber-400 hover:bg-amber-500 text-white text-sm font-bold rounded-full transition-all shadow-md disabled:opacity-60">
          <svg v-if="loadingMore" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
          </svg>
          {{ loadingMore ? t('common.loading') : t('photoOfDay.loadMore') }}
        </button>
      </div>

    </div>
  </MainLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'
import { Head } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import { useLocale } from '@/composables/useLocale.js'

const { locale, t } = useLocale()

const galleries   = ref([])
const loading     = ref(true)
const loadingMore = ref(false)
const currentPage = ref(1)
const lastPage    = ref(1)
const hasMore     = ref(false)

const formatDate = (d) => d
  ? new Date(d).toLocaleDateString(locale.value === 'bn' ? 'bn-BD' : 'en-US', { year: 'numeric', month: 'long', day: 'numeric' })
  : ''

async function fetchGalleries(page = 1, append = false) {
  if (page === 1) loading.value = true
  else            loadingMore.value = true
  try {
    const { data } = await axios.get('/api/galleries', { params: { page, per_page: 12 } })
    const items = data.data ?? []
    galleries.value   = append ? [...galleries.value, ...items] : items
    currentPage.value = data.current_page
    lastPage.value    = data.last_page
    hasMore.value     = data.current_page < data.last_page
  } catch { /* silent */ } finally {
    loading.value     = false
    loadingMore.value = false
  }
}

const loadMore = () => fetchGalleries(currentPage.value + 1, true)

fetchGalleries(1)
watch(locale, () => { currentPage.value = 1; fetchGalleries(1) })
</script>
