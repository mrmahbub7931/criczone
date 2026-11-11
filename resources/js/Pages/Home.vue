<template>
  <MainLayout>

    <!-- Hero Section -->
    <!-- <Hero /> -->

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">

      <!-- Featured Story -->
      <section class="mb-12">
        <div class="flex items-center gap-3 mb-6">
          <div class="h-8 w-1 bg-secondary rounded-full" />
          <h2 class="text-2xl font-black text-gray-900 dark:text-slate-100 uppercase tracking-tight">
            {{ t('home.featuredNews') }}
          </h2>
        </div>

        <!-- Skeleton -->
        <div v-if="featuredLoading">
          <div class="rounded-2xl bg-gray-200 animate-pulse h-[400px] md:h-[450px] w-full" />
        </div>

        <!-- ── MAGAZINE layout: first card big, rest small ── -->
        <div v-else-if="featuredLayout === 'magazine'" class="grid grid-cols-1 lg:grid-cols-3 gap-5">
          <!-- Hero card -->
          <div class="lg:col-span-2 cursor-pointer" @click="handleArticleClick(displayedCards[0])">
            <NewsCard :news="displayedCards[0]" variant="featured" />
          </div>
          <!-- Side stack -->
          <div class="flex flex-col gap-4">
            <div
              v-for="card in displayedCards.slice(1, 5)"
              :key="card.id"
              class="cursor-pointer"
              @click="handleArticleClick(card)"
            >
              <div class="group flex gap-3 bg-white dark:bg-slate-800 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-200 p-3">
                <img
                  :src="card.imageUrl"
                  :alt="card.title"
                  class="w-24 h-20 object-cover object-top rounded-lg flex-shrink-0 group-hover:scale-105 transition-transform duration-300"
                />
                <div class="flex-1 min-w-0">
                  <span class="text-[10px] font-bold text-secondary uppercase tracking-wider">{{ card.category }}</span>
                  <h3 class="text-sm font-bold text-gray-900 dark:text-slate-100 leading-snug mt-0.5 line-clamp-2 group-hover:text-primary transition-colors">{{ card.title }}</h3>
                  <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">{{ card.date }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ── LIST layout: vertical stack ── -->
        <div v-else-if="featuredLayout === 'list'" class="space-y-4">
          <div
            v-for="card in displayedCards"
            :key="card.id"
            class="cursor-pointer group flex gap-4 bg-white dark:bg-slate-800 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-200"
            @click="handleArticleClick(card)"
          >
            <img
              :src="card.imageUrl"
              :alt="card.title"
              class="w-40 sm:w-52 h-32 object-cover object-top flex-shrink-0 group-hover:scale-105 transition-transform duration-300"
            />
            <div class="flex-1 min-w-0 py-4 pr-4">
              <span
                class="inline-block text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider mb-2"
                :style="{ backgroundColor: '#0D47A1', color: '#fff' }"
              >{{ card.category }}</span>
              <h3 class="font-black text-gray-900 dark:text-slate-100 text-base leading-snug mb-2 line-clamp-2 group-hover:text-primary transition-colors">{{ card.title }}</h3>
              <p class="text-sm text-gray-500 dark:text-slate-400 line-clamp-2 mb-3">{{ card.excerpt }}</p>
              <div class="flex items-center gap-4 text-xs text-gray-400 dark:text-slate-500">
                <span>{{ card.date }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- ── GRID layout (default): card grid ── -->
        <div v-else>
          <!-- First card full-width featured -->
          <div class="cursor-pointer mb-5" @click="handleArticleClick(displayedCards[0])">
            <NewsCard :news="displayedCards[0]" variant="featured" />
          </div>
          <!-- Rest in grid -->
          <div v-if="displayedCards.length > 1" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mt-5">
            <div
              v-for="card in displayedCards.slice(1)"
              :key="card.id"
              class="cursor-pointer"
              @click="handleArticleClick(card)"
            >
              <NewsCard :news="card" />
            </div>
          </div>
        </div>

      </section>

      <!-- Home after featured ad -->
      <AdSlot position="home_after_featured" />

      <!-- ── ফটো ফিচার (Photo Galleries) ─────────────────────────────────────── -->
      <section v-if="photoOfDay.length" class="mb-12">
        <!-- Section header -->
        <div class="flex items-center justify-between mb-5">
          <div class="flex items-center gap-3">
            <div class="h-8 w-1 bg-amber-500 rounded-full" />
            <h2 class="text-2xl font-black text-gray-900 dark:text-slate-100 uppercase tracking-tight">
              {{ t('home.photoOfDay') }}
            </h2>
          </div>
          <a href="/photo-of-day" class="text-xs font-bold text-amber-500 hover:text-amber-600 flex items-center gap-1 transition-colors">
            {{ t('home.viewAllPhotos') }} <ChevronRight :size="13" />
          </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-5">

          <!-- Left: Featured gallery with preview slideshow -->
          <div v-if="photoOfDay[0]" class="group">
            <div class="relative overflow-hidden bg-gray-100 rounded-sm">
              <!-- Auto-slide progress bar -->
              <div v-if="featuredGalleryDetail?.photos?.length > 1" class="absolute top-0 left-0 right-0 z-20 h-[3px] bg-black/20">
                <div
                  class="h-full bg-red-500"
                  :style="{
                    width: sliderProgress + '%',
                    transition: (sliderPaused || sliderProgress === 0) ? 'none' : ('width ' + SLIDE_MS + 'ms linear')
                  }"
                />
              </div>
              <!-- Counter + controls -->
              <div v-if="featuredGalleryDetail?.photos?.length" class="absolute top-3 left-3 z-10 flex items-center gap-2">
                <div class="bg-black/60 text-white text-xs font-bold px-2.5 py-1 rounded-sm">
                  {{ featuredPhotoIdx + 1 }} / {{ featuredGalleryDetail.photos.length }}
                </div>
              </div>
              <!-- Prev / Pause / Next controls (top-right) -->
              <div v-if="featuredGalleryDetail?.photos?.length > 1" class="absolute top-3 right-3 z-10 flex items-center gap-1.5">
                <button @click.stop="prevSlide"
                  class="w-7 h-7 rounded-full bg-black/50 hover:bg-black/70 flex items-center justify-center text-white transition-colors">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button @click.stop="sliderPaused = !sliderPaused"
                  class="w-7 h-7 rounded-full bg-black/50 hover:bg-black/70 flex items-center justify-center text-white transition-colors">
                  <svg v-if="!sliderPaused" class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M6 4h4v16H6zm8 0h4v16h-4z"/></svg>
                  <svg v-else class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </button>
                <button @click.stop="nextSlide"
                  class="w-7 h-7 rounded-full bg-black/50 hover:bg-black/70 flex items-center justify-center text-white transition-colors">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                </button>
              </div>
              <!-- Image -->
              <a :href="`/photo-of-day/${photoOfDay[0].slug}`">
                <img
                  :src="featuredGalleryPhoto || photoOfDay[0].cover_image || 'https://placehold.co/800x500/f59e0b/white?text=📸'"
                  :alt="photoOfDay[0].title"
                  class="w-full aspect-[16/10] object-cover transition-opacity duration-300"
                />
                <!-- Caption bar -->
                <div v-if="featuredCaption" class="bg-gray-100 dark:bg-slate-800 px-3 py-2 border-t border-gray-200 dark:border-slate-700">
                  <p class="text-xs text-gray-500 dark:text-slate-400 line-clamp-1">{{ featuredCaption }}</p>
                </div>
              </a>
            </div>
            <!-- Title + date -->
            <a :href="`/photo-of-day/${photoOfDay[0].slug}`" class="block mt-3 hover:text-primary transition-colors">
              <h3 class="text-lg font-black text-gray-900 dark:text-slate-100 leading-snug line-clamp-2">{{ (locale === 'en' && photoOfDay[0].title_en) ? photoOfDay[0].title_en : photoOfDay[0].title }}</h3>
              <p class="text-xs text-gray-400 mt-1">{{ formatGalleryDate(photoOfDay[0].published_at) }}</p>
            </a>
          </div>

          <!-- Right: 2×2 grid of remaining galleries -->
          <div class="grid grid-cols-2 lg:grid-cols-1 gap-4 lg:gap-3">
            <a
              v-for="gallery in photoOfDay.slice(1, 5)"
              :key="gallery.id"
              :href="`/photo-of-day/${gallery.slug}`"
              class="group flex lg:flex-row gap-3 items-start"
            >
              <!-- Thumbnail -->
              <div class="relative overflow-hidden rounded-sm flex-shrink-0 w-full lg:w-28 aspect-[4/3] lg:aspect-square bg-gray-100">
                <img
                  :src="gallery.cover_image || 'https://placehold.co/200x200/f59e0b/white?text=📸'"
                  :alt="gallery.title"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
                <!-- Camera icon badge -->
                <div class="absolute top-1.5 left-1.5 w-6 h-6 rounded-full bg-black/50 flex items-center justify-center">
                  <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 15.2A3.2 3.2 0 1015.2 12 3.2 3.2 0 0012 15.2zm0 0M20 4h-3.17L15 2H9L7.17 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2zm-8 13a5 5 0 110-10 5 5 0 010 10z"/></svg>
                </div>
              </div>
              <!-- Hidden on mobile, shown on lg -->
              <div class="hidden lg:block flex-1 min-w-0">
                <h4 class="text-sm font-black text-gray-800 dark:text-slate-200 leading-snug line-clamp-2 group-hover:text-primary transition-colors">{{ (locale === 'en' && gallery.title_en) ? gallery.title_en : gallery.title }}</h4>
                <p class="text-[11px] text-gray-400 mt-1">{{ formatGalleryDate(gallery.published_at) }}</p>
              </div>
              <!-- Mobile: title below image (shown as column) -->
              <div class="lg:hidden mt-1.5">
                <h4 class="text-xs font-black text-gray-800 dark:text-slate-200 leading-snug line-clamp-2">{{ (locale === 'en' && gallery.title_en) ? gallery.title_en : gallery.title }}</h4>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ formatGalleryDate(gallery.published_at) }}</p>
              </div>
            </a>
          </div>

        </div>
      </section>

      <!-- ── Video News ──────────────────────────────────────────────────────── -->
      <section v-if="homeVideosLoading || homeVideos.length > 0" class="mb-12">

        <!-- Section header -->
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-3">
            <div class="h-8 w-1 bg-secondary rounded-full" />
            <h2 class="text-2xl font-black text-gray-900 dark:text-slate-100 uppercase tracking-tight">{{ t('home.videos') }}</h2>
          </div>
          <a href="/videos" class="text-xs font-bold text-secondary hover:text-secondary-dark flex items-center gap-1 transition-colors">
            {{ t('home.viewAllVideos') }} <ChevronRight :size="13" />
          </a>
        </div>

        <!-- Skeleton -->
        <div v-if="homeVideosLoading" class="grid grid-cols-1 lg:grid-cols-3 gap-5">
          <div class="lg:col-span-2 bg-gray-200 animate-pulse rounded-2xl h-72" />
          <div class="flex flex-col gap-4">
            <div v-for="i in 3" :key="i" class="flex gap-3 animate-pulse">
              <div class="w-28 h-20 rounded-xl bg-gray-200 shrink-0" />
              <div class="flex-1 space-y-2 py-1">
                <div class="h-3.5 bg-gray-200 rounded w-full" />
                <div class="h-3.5 bg-gray-200 rounded w-3/4" />
                <div class="h-3 bg-gray-100 rounded w-1/3" />
              </div>
            </div>
          </div>
        </div>

        <!-- Loaded -->
        <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-5">

          <!-- ── Featured video (left, large) ── -->
          <a
            :href="`/article/${homeVideos[0].slug}`"
            class="lg:col-span-2 group relative overflow-hidden rounded-2xl bg-gray-900 shadow-lg block"
            style="min-height: 280px;"
          >
            <img
              v-if="homeVideos[0].featured_image"
              :src="homeVideos[0].featured_image"
              :alt="homeVideos[0].title"
              class="w-full h-full object-cover object-top absolute inset-0 opacity-75 group-hover:opacity-60 group-hover:scale-105 transition-all duration-500"
            />
            <div v-else class="absolute inset-0 bg-gradient-to-br from-gray-800 to-gray-900" />

            <!-- Dark gradient overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent" />

            <!-- Play button -->
            <div class="absolute inset-0 flex items-center justify-center">
              <div class="w-16 h-16 rounded-full bg-secondary/90 backdrop-blur-sm flex items-center justify-center group-hover:scale-110 group-hover:bg-secondary transition-all duration-300 shadow-xl">
                <svg class="w-7 h-7 text-white fill-current ml-1" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
              </div>
            </div>

            <!-- Category badge -->
            <span
              v-if="homeVideos[0].category"
              class="absolute top-4 left-4 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider text-white"
              :style="{ backgroundColor: homeVideos[0].category.color || '#0D47A1' }"
            >{{ homeVideos[0].category.name }}</span>

            <!-- Video badge -->
            <span class="absolute top-4 right-4 bg-secondary text-white text-[10px] font-black px-2.5 py-1 rounded-full flex items-center gap-1 uppercase tracking-wider">
              <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
              Video
            </span>

            <!-- Text content -->
            <div class="absolute bottom-0 left-0 right-0 p-5">
              <h3 class="text-white font-black text-lg sm:text-xl leading-snug line-clamp-2 mb-2 group-hover:text-secondary-light transition-colors">
                {{ homeVideos[0].title }}
              </h3>
              <p v-if="homeVideos[0].excerpt" class="text-white/70 text-sm line-clamp-1 mb-3 hidden sm:block">
                {{ homeVideos[0].excerpt }}
              </p>
              <div class="flex items-center gap-4 text-white/60 text-xs">
                <span>{{ formatVideoDate(homeVideos[0].published_at) }}</span>
              </div>
            </div>
          </a>

          <!-- ── Shelf cards (right column) ── -->
          <div class="flex flex-col gap-4 lg:overflow-y-auto">
            <!-- On mobile: horizontal scroll row -->
            <div class="flex gap-4 overflow-x-auto no-scrollbar lg:hidden pb-1">
              <a
                v-for="video in homeVideos.slice(1)"
                :key="video.id"
                :href="`/article/${video.slug}`"
                class="group shrink-0 w-56 bg-white dark:bg-slate-800 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300"
              >
                <div class="relative h-32 bg-gray-900 overflow-hidden">
                  <img v-if="video.featured_image" :src="video.featured_image" :alt="video.title"
                    class="w-full h-full object-cover object-top opacity-80 group-hover:opacity-70 group-hover:scale-105 transition-all duration-500" />
                  <div v-else class="w-full h-full bg-gradient-to-br from-gray-800 to-gray-900" />
                  <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-9 h-9 rounded-full bg-white/20 border border-white/60 flex items-center justify-center group-hover:bg-white/30 transition-all">
                      <svg class="w-4 h-4 text-white fill-current ml-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                  </div>
                </div>
                <div class="p-3">
                  <p class="text-xs font-bold text-gray-800 dark:text-slate-200 line-clamp-2 group-hover:text-primary transition-colors leading-snug">{{ video.title }}</p>
                  <p class="text-[11px] text-gray-400 dark:text-slate-500 mt-1.5">{{ formatVideoDate(video.published_at) }}</p>
                </div>
              </a>
            </div>

            <!-- On desktop: vertical stack -->
            <a
              v-for="video in homeVideos.slice(1)"
              :key="'d' + video.id"
              :href="`/article/${video.slug}`"
              class="group hidden lg:flex items-start gap-3 bg-white dark:bg-slate-800 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 p-0"
            >
              <!-- Thumbnail -->
              <div class="relative w-32 shrink-0 h-[84px] bg-gray-900 overflow-hidden rounded-l-xl">
                <img v-if="video.featured_image" :src="video.featured_image" :alt="video.title"
                  class="w-full h-full object-cover object-top opacity-80 group-hover:opacity-70 group-hover:scale-105 transition-all duration-500" />
                <div v-else class="w-full h-full bg-gradient-to-br from-gray-800 to-gray-900" />
                <div class="absolute inset-0 flex items-center justify-center">
                  <div class="w-8 h-8 rounded-full bg-white/20 border border-white/60 flex items-center justify-center group-hover:bg-white/30 transition-all">
                    <svg class="w-3.5 h-3.5 text-white fill-current ml-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                  </div>
                </div>
              </div>
              <!-- Info -->
              <div class="flex-1 min-w-0 py-2.5 pr-3">
                <p class="text-sm font-bold text-gray-800 dark:text-slate-200 line-clamp-2 group-hover:text-primary transition-colors leading-snug">{{ video.title }}</p>
                <div class="flex items-center gap-3 mt-1.5 text-xs text-gray-400 dark:text-slate-500">
                  <span>{{ formatVideoDate(video.published_at) }}</span>
                </div>
              </div>
            </a>
          </div>

        </div>
      </section>

      <!-- Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

        <!-- Latest News -->
        <div class="lg:col-span-2">
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
              <div class="h-8 w-1 bg-primary rounded-full" />
              <h2 class="text-2xl font-black text-gray-900 dark:text-slate-100 uppercase tracking-tight">
                {{ t('home.latestNews') }}
              </h2>
            </div>
          </div>

          <!-- Category Filter Strip -->
          <div class="relative mb-8 -mx-1">
            <!-- Left arrow -->
            <button
              v-show="canScrollLeft"
              @click="scrollCats(-1)"
              class="absolute left-0 top-1/2 -translate-y-1/2 z-10 w-7 h-7 flex items-center justify-center bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-full shadow-md hover:shadow-lg text-gray-500 dark:text-slate-300 hover:text-primary transition-all"
            >
              <ChevronLeft :size="14" />
            </button>

            <!-- Scrollable strip -->
            <div
              ref="catStripRef"
              @scroll="updateScrollArrows"
              class="flex items-center gap-1.5 overflow-x-auto no-scrollbar px-1 py-1"
            >
              <button
                v-for="cat in categories"
                :key="cat.id ?? cat.name ?? 'all'"
                @click="onCategoryChange(cat.name)"
                class="shrink-0 flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold transition-all duration-200 whitespace-nowrap"
                :class="selectedCategory === cat.name
                  ? 'bg-primary text-white shadow-md'
                  : 'bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 border border-gray-200 dark:border-slate-600'"
              >
                <span
                  v-if="cat.color"
                  class="w-2 h-2 rounded-full shrink-0"
                  :style="{ backgroundColor: selectedCategory === cat.name ? '#fff' : (cat.color || '#0D47A1') }"
                />
                {{ cat.name ?? t('home.allCategories') }}
              </button>

              <!-- Browse all link (shown when there are many categories) -->
              <a v-if="allCategoriesCount > 12" href="/categories"
                class="shrink-0 flex items-center gap-1 px-3.5 py-1.5 rounded-full text-xs font-bold whitespace-nowrap bg-gray-50 text-primary border border-primary/20 hover:bg-primary hover:text-white transition-all duration-200">
                {{ t('home.viewAll') }}
                <ChevronRight :size="12" />
              </a>
            </div>

            <!-- Right arrow -->
            <button
              v-show="canScrollRight"
              @click="scrollCats(1)"
              class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-7 h-7 flex items-center justify-center bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-full shadow-md hover:shadow-lg text-gray-500 dark:text-slate-300 hover:text-primary transition-all"
            >
              <ChevronRight :size="14" />
            </button>
          </div>

          <!-- Skeleton -->
          <div v-if="latestLoading" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div v-for="i in 6" :key="i" class="bg-white rounded-xl overflow-hidden shadow-sm animate-pulse">
              <div class="h-48 bg-gray-200" />
              <div class="p-4 space-y-2">
                <div class="h-4 bg-gray-200 rounded w-3/4" />
                <div class="h-3 bg-gray-100 rounded w-full" />
                <div class="h-3 bg-gray-100 rounded w-2/3" />
              </div>
            </div>
          </div>

          <!-- News Grid -->
          <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div
              v-for="news in latestNews"
              :key="news.id"
              class="cursor-pointer"
              @click="handleArticleClick(news)"
            >
              <NewsCard :news="news" />
            </div>
          </div>

          <!-- Empty state -->
          <div v-if="!latestLoading && latestNews.length === 0" class="text-center py-16 bg-white dark:bg-slate-800 rounded-xl">
            <p class="text-gray-400 text-sm">{{ t('home.noArticles') }}</p>
          </div>

          <!-- Load More -->
          <div v-if="!latestLoading && hasMorePages" class="flex justify-center mt-8">
            <button
              @click="loadMore"
              :disabled="loadingMore"
              class="inline-flex items-center gap-2 px-8 py-3 bg-primary hover:bg-primary-dark text-white text-sm font-bold rounded-full transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-60"
            >
              <svg v-if="loadingMore" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
              </svg>
              {{ loadingMore ? t('common.loading') : t('home.loadMore') }}
            </button>
          </div>
        </div>

        <!-- Sidebar -->
        <Sidebar />

      </div>
    </div>

  </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import axios from 'axios'
import MainLayout from '@/Layouts/MainLayout.vue'
import Hero from '@/Components/Hero.vue'
import NewsCard from '@/Components/NewsCard.vue'
import Sidebar from '@/Components/Sidebar.vue'
import AdSlot from '@/Components/AdSlot.vue'
import { useSettings } from '@/composables/useSettings.js'
import { useLocale }   from '@/composables/useLocale.js'
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'

const { locale, t } = useLocale()

const handleArticleClick = (article) => {
  if (article?.slug && article.slug !== '#') window.location.href = `/article/${article.slug}`
}

// ── Latest News (dynamic) ────────────────────────────────────────────────────
const { get: setting, loaded: settingsLoaded } = useSettings()

// Per-page driven by settings; falls back to 6 until settings load
const latestPerPage = computed(() => {
  const v = parseInt(setting('homepage', 'latest_per_page', '6'))
  return (v > 0 && v <= 48) ? v : 6
})

const latestNews       = ref([])
const latestLoading    = ref(true)
const loadingMore      = ref(false)
const currentPage      = ref(1)
const hasMorePages     = ref(false)
const selectedCategory   = ref(null)   // null = All
const categories         = ref([])
const allCategoriesCount = ref(0)   // total from API, used to show "Browse All" link
const catStripRef      = ref(null)
const canScrollLeft    = ref(false)
const canScrollRight   = ref(false)

const updateScrollArrows = () => {
  const el = catStripRef.value
  if (!el) return
  canScrollLeft.value  = el.scrollLeft > 4
  canScrollRight.value = el.scrollLeft + el.clientWidth < el.scrollWidth - 4
}

const scrollCats = (dir) => {
  const el = catStripRef.value
  if (!el) return
  el.scrollBy({ left: dir * 200, behavior: 'smooth' })
  setTimeout(updateScrollArrows, 300)
}

const toCardLatest = (a) => ({
  id:       a.id,
  slug:     a.slug,
  title:    a.title,
  excerpt:  a.excerpt || '',
  imageUrl: a.featured_image || `https://placehold.co/800x450/0D47A1/white?text=${encodeURIComponent(a.title?.slice(0, 20) || 'CricZone')}`,
  category: a.category?.name  || 'Cricket',
  date:     a.published_at ? new Date(a.published_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '',
})

const fetchLatest = async (page = 1, append = false) => {
  if (page === 1) latestLoading.value = true
  else            loadingMore.value   = true

  try {
    const params = { page, per_page: latestPerPage.value, lang: locale.value }
    if (selectedCategory.value) params.category = selectedCategory.value

    const { data } = await axios.get('/api/articles/latest', { params })
    const mapped = (data.data ?? []).map(toCardLatest)

    latestNews.value   = append ? [...latestNews.value, ...mapped] : mapped
    currentPage.value  = data.current_page
    hasMorePages.value = data.current_page < data.last_page
  } catch {
    if (!append) latestNews.value = []
  } finally {
    latestLoading.value = false
    loadingMore.value   = false
  }
}

// cat is null for "All", or the category name string
const onCategoryChange = (cat) => {
  selectedCategory.value = cat
  currentPage.value = 1
  fetchLatest(1, false)
}

const loadMore = () => {
  fetchLatest(currentPage.value + 1, true)
}

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/categories/public')
    allCategoriesCount.value = data.length
    // Only show categories where show_in_filter is not explicitly false; top 12
    const filterable = data.filter(c => c.show_in_filter !== false)
    // id: null → the "All" sentinel; name will be rendered via t()
    categories.value = [{ id: null, name: null }, ...filterable.slice(0, 12)]
    // wait for DOM to render, then check scroll arrows
    setTimeout(updateScrollArrows, 100)
  } catch {
    categories.value = [{ id: null, name: null }]
  }

  fetchLatest(1)
})

// ── Featured Story (dynamic) ────────────────────────────────────────────────

const featuredArticles  = ref([])
const featuredLoading   = ref(true)

// Map API article → NewsCard-compatible shape
const toCard = (a) => ({
  id:        a.id,
  slug:      a.slug,
  title:     a.title,
  excerpt:   a.excerpt || '',
  imageUrl:  a.featured_image || `https://placehold.co/800x450/0D47A1/white?text=${encodeURIComponent(a.title?.slice(0, 30) || 'CricZone')}`,
  category:  a.category?.name  || 'Cricket',
  isFeatured: a.is_featured,
})

// Fallback demo cards shown when DB is empty
const demoCards = [
  { id: 'd1', slug: '#', title: 'India Clinches Historic Series Win Against Australia', excerpt: 'A stunning final-day performance sealed the remarkable comeback in one of the greatest Test series of the decade.', imageUrl: 'https://placehold.co/800x450/0D47A1/white?text=Featured+Story', category: 'Test Cricket', isFeatured: true },
  { id: 'd2', slug: '#', title: 'IPL 2026 Auction: Top Buys Revealed', excerpt: 'The mega auction saw record-breaking bids as franchises scrambled to build their dream squads.', imageUrl: 'https://placehold.co/800x450/1565C0/white?text=IPL+News', category: 'IPL', isFeatured: false },
  { id: 'd3', slug: '#', title: 'T20 World Cup 2026 Schedule Announced', excerpt: 'The ICC officially confirmed the full schedule for the upcoming T20 World Cup with 20 participating nations.', imageUrl: 'https://placehold.co/800x450/0277BD/white?text=T20+WC', category: 'T20', isFeatured: false },
]

const featuredLayout = computed(() => setting('homepage', 'articles_layout', 'grid'))

const displayedCards = computed(() =>
  featuredArticles.value.length ? featuredArticles.value : demoCards
)

const fetchFeatured = async () => {
  featuredLoading.value = true
  try {
    const { data } = await axios.get('/api/articles/featured', { params: { lang: locale.value } })
    featuredArticles.value = data.map(toCard)
  } catch {
    featuredArticles.value = []
  } finally {
    featuredLoading.value = false
  }
}

onMounted(fetchFeatured)

// ── Photo of the Day ─────────────────────────────────────────────────────────
const photoOfDay            = ref([])
const featuredPhotoIdx      = ref(0)
const featuredGalleryDetail = ref(null)
const sliderPaused          = ref(false)
const sliderProgress        = ref(0)
const SLIDE_MS              = 4000

let sliderTimer   = null
let progressTimer = null

const featuredGalleryPhoto = computed(() => {
  if (featuredGalleryDetail.value?.photos?.length) {
    return featuredGalleryDetail.value.photos[featuredPhotoIdx.value]?.image_path
  }
  return photoOfDay.value[0]?.cover_image ?? null
})

const featuredCaption = computed(() => {
  const photo = featuredGalleryDetail.value?.photos?.[featuredPhotoIdx.value]
  if (!photo) return ''
  if (locale.value === 'en' && photo.caption_en) return photo.caption_en
  return photo.caption ?? ''
})

const formatGalleryDate = (d) => {
  if (!d) return ''
  return new Date(d).toLocaleDateString(locale.value === 'bn' ? 'bn-BD' : 'en-US', { year: 'numeric', month: 'long', day: 'numeric' })
}

function startSlider() {
  stopSlider()
  const total = featuredGalleryDetail.value?.photos?.length ?? 0
  if (total < 2) return
  // Reset to 0 (no transition), then animate to 100 on next frame
  sliderProgress.value = 0
  requestAnimationFrame(() => {
    requestAnimationFrame(() => { sliderProgress.value = 100 })
  })
  sliderTimer = setTimeout(() => {
    if (!sliderPaused.value) nextSlide()
  }, SLIDE_MS)
}

function stopSlider() {
  clearTimeout(sliderTimer)
  sliderTimer = null
}

function nextSlide() {
  const total = featuredGalleryDetail.value?.photos?.length ?? 0
  featuredPhotoIdx.value = (featuredPhotoIdx.value + 1) % total
  if (!sliderPaused.value) startSlider()
}

function prevSlide() {
  const total = featuredGalleryDetail.value?.photos?.length ?? 0
  featuredPhotoIdx.value = (featuredPhotoIdx.value - 1 + total) % total
  if (!sliderPaused.value) startSlider()
}

watch(sliderPaused, (paused) => {
  if (paused) stopSlider()
  else startSlider()
})

watch(featuredGalleryDetail, (detail) => {
  if (detail?.photos?.length > 1) startSlider()
})

const fetchPhotoOfDay = async () => {
  try {
    const { data } = await axios.get('/api/galleries', { params: { per_page: 5 } })
    photoOfDay.value = data.data ?? []
    featuredPhotoIdx.value = 0
    featuredGalleryDetail.value = null
    // load featured gallery photos for the preview slider
    if (photoOfDay.value[0]?.slug) {
      const { data: detail } = await axios.get(`/api/galleries/${photoOfDay.value[0].slug}`)
      featuredGalleryDetail.value = detail
    }
  } catch { /* silent */ }
}

onMounted(fetchPhotoOfDay)
onUnmounted(stopSlider)

// ── Videos section ───────────────────────────────────────────────────────────
const homeVideos        = ref([])
const homeVideosLoading = ref(true)

const fetchHomeVideos = async () => {
  homeVideosLoading.value = true
  try {
    const { data } = await axios.get('/api/articles/videos', { params: { per_page: 4, lang: locale.value } })
    homeVideos.value = data.data ?? []
  } catch { homeVideos.value = [] }
  finally { homeVideosLoading.value = false }
}

onMounted(fetchHomeVideos)

const formatVideoDate = (d) => d
  ? new Date(d).toLocaleDateString(locale.value === 'bn' ? 'bn-BD' : 'en-US', { month: 'short', day: 'numeric', year: 'numeric' })
  : ''


// ── Reload all data when locale changes ──────────────────────────────────────
watch(locale, () => {
  selectedCategory.value = null
  fetchLatest(1, false)
  fetchFeatured()
  fetchPhotoOfDay()
  fetchHomeVideos()
})
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
