<template>
  <Head>
    <title>{{ gallery ? gallery.title + ' – CricZone' : 'Photo Gallery – CricZone' }}</title>
  </Head>

  <MainLayout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">

      <!-- Back -->
      <a href="/photo-of-day" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-primary transition-colors mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        {{ locale === 'en' ? 'All Galleries' : 'সব গ্যালারি' }}
      </a>

      <!-- Loading -->
      <div v-if="loading" class="space-y-4">
        <div class="h-8 w-64 bg-gray-200 animate-pulse rounded-lg" />
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
          <div v-for="i in 6" :key="i" class="aspect-[4/3] rounded-xl bg-gray-200 animate-pulse" />
        </div>
      </div>

      <template v-else-if="gallery">

        <!-- Gallery header -->
        <div class="mb-8 pb-6 border-b border-gray-100 dark:border-slate-700">
          <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-slate-100 leading-snug mb-3">{{ displayTitle }}</h1>
          <p v-if="gallery.description" class="text-gray-500 dark:text-slate-400 text-sm leading-relaxed mb-3">{{ gallery.description }}</p>
          <span class="text-xs text-gray-400">{{ formatDate(gallery.published_at) }}</span>
        </div>

        <!-- Sequential photo list -->
        <div class="space-y-10">
          <div
            v-for="(photo, idx) in gallery.photos"
            :key="photo.id"
          >
            <!-- Number badge -->
            <div class="flex items-center gap-2 mb-2.5">
              <span class="inline-flex items-center border border-gray-300 dark:border-slate-600 text-gray-500 dark:text-slate-400 text-xs font-bold px-2.5 py-0.5 rounded-sm">
                {{ idx + 1 }} / {{ gallery.photos.length }}
              </span>
            </div>

            <!-- Photo -->
            <div class="cursor-zoom-in" @click="openLightbox(idx)">
              <img
                :src="photo.image_path"
                :alt="photo.caption || ''"
                class="w-full block rounded-sm shadow-sm hover:shadow-md transition-shadow"
                loading="lazy"
              />
            </div>

            <!-- Caption + photographer -->
            <div v-if="displayCaption(photo) || photo.photographer" class="mt-2 pb-6">
              <p v-if="displayCaption(photo)" class="flex items-start gap-1.5 text-xs text-gray-500 dark:text-slate-400 italic leading-relaxed mb-1.5">
                <span class="shrink-0 mt-0.5 text-red-500 not-italic font-black text-sm leading-none">▎</span>
                {{ displayCaption(photo) }}
              </p>
              <span v-if="photo.photographer"
                class="inline-flex items-center gap-1 border border-gray-300 dark:border-slate-600 text-gray-500 dark:text-slate-400 text-[11px] italic px-2 py-0.5 rounded-sm">
                {{ locale === 'en' ? 'Photo:' : 'ছবি:' }} {{ photo.photographer }}
              </span>
            </div>
          </div>
        </div>

      </template>

      <!-- 404 -->
      <div v-else class="text-center py-24">
        <div class="text-5xl mb-4">📷</div>
        <p class="text-gray-500 font-semibold">Gallery not found.</p>
      </div>

    </div>

    <!-- ── Lightbox ── -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="lightboxOpen" class="fixed inset-0 z-50 bg-black/95 flex flex-col" @click.self="lightboxOpen = false">

        <!-- Top bar -->
        <div class="flex items-center justify-between px-4 py-3 shrink-0">
          <span class="text-white/50 text-sm font-bold">{{ lightboxIdx + 1 }} / {{ gallery.photos.length }}</span>
          <button @click="lightboxOpen = false" class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
          </button>
        </div>

        <!-- Image -->
        <div class="flex-1 flex items-center justify-center px-4 relative min-h-0">
          <!-- Prev -->
          <button v-if="lightboxIdx > 0" @click.stop="lightboxIdx--"
            class="absolute left-2 sm:left-6 w-10 h-10 rounded-full bg-white/10 hover:bg-white/25 flex items-center justify-center text-white transition-colors z-10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
          </button>

          <img
            :src="currentPhoto.image_path"
            :alt="currentPhoto.caption || ''"
            class="max-h-full max-w-full object-contain rounded-lg shadow-2xl select-none"
            :key="lightboxIdx"
          />

          <!-- Next -->
          <button v-if="lightboxIdx < gallery.photos.length - 1" @click.stop="lightboxIdx++"
            class="absolute right-2 sm:right-6 w-10 h-10 rounded-full bg-white/10 hover:bg-white/25 flex items-center justify-center text-white transition-colors z-10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
          </button>
        </div>

        <!-- Caption + thumbnails -->
        <div class="shrink-0 pb-4">
          <p v-if="displayCaption(currentPhoto)" class="text-center text-white/70 text-sm px-4 py-2">{{ displayCaption(currentPhoto) }}</p>
          <!-- Thumbnail strip -->
          <div class="flex gap-2 overflow-x-auto px-4 py-2 no-scrollbar">
            <button
              v-for="(photo, idx) in gallery.photos"
              :key="photo.id"
              @click.stop="lightboxIdx = idx"
              :class="['shrink-0 w-14 h-14 rounded-lg overflow-hidden border-2 transition-all',
                idx === lightboxIdx ? 'border-amber-400 scale-105' : 'border-transparent opacity-50 hover:opacity-80']"
            >
              <img :src="photo.image_path" :alt="''" class="w-full h-full object-cover" />
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'
import { Head } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import { useLocale } from '@/composables/useLocale.js'

const props = defineProps({ slug: String })
const { locale } = useLocale()

const gallery      = ref(null)
const loading      = ref(true)
const lightboxOpen = ref(false)
const lightboxIdx  = ref(0)

const currentPhoto = computed(() => gallery.value?.photos?.[lightboxIdx.value] ?? {})

const displayCaption = (photo) => {
  if (locale.value === 'en' && photo.caption_en) return photo.caption_en
  return photo.caption || ''
}

const displayTitle = computed(() => {
  if (!gallery.value) return ''
  if (locale.value === 'en' && gallery.value.title_en) return gallery.value.title_en
  return gallery.value.title
})

const formatDate = (d) => d
  ? new Date(d).toLocaleDateString(locale.value === 'bn' ? 'bn-BD' : 'en-US', { year: 'numeric', month: 'long', day: 'numeric' })
  : ''

function openLightbox(idx) {
  lightboxIdx.value = idx
  lightboxOpen.value = true
}

function onKeydown(e) {
  if (!lightboxOpen.value) return
  if (e.key === 'ArrowRight' && lightboxIdx.value < (gallery.value?.photos?.length ?? 0) - 1) lightboxIdx.value++
  if (e.key === 'ArrowLeft'  && lightboxIdx.value > 0) lightboxIdx.value--
  if (e.key === 'Escape') lightboxOpen.value = false
}

onMounted(async () => {
  window.addEventListener('keydown', onKeydown)
  try {
    const { data } = await axios.get(`/api/galleries/${props.slug}`)
    gallery.value = data
  } catch { /* 404 */ } finally {
    loading.value = false
  }
})

onBeforeUnmount(() => window.removeEventListener('keydown', onKeydown))
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
