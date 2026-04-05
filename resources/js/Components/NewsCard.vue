<script setup>
import { Clock, User } from 'lucide-vue-next'

defineProps({
  news: Object,
  variant: {
    type: String,
    default: 'vertical'
  }
})
</script>

<template>
  <!-- Featured variant -->
  <div v-if="variant === 'featured'" class="group relative rounded-2xl overflow-hidden shadow-xl cursor-pointer">
    <div class="relative h-[400px] md:h-[450px]">
      <img
        :src="news.imageUrl"
        :alt="news.title"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
      />
      <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent" />
      <div class="absolute bottom-0 left-0 right-0 p-6 md:p-8">
        <span class="inline-block bg-secondary text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-3">
          {{ news.category }}
        </span>
        <h2 class="text-2xl md:text-3xl font-black text-white leading-tight mb-3 group-hover:text-secondary-light transition-colors duration-200">
          {{ news.title }}
        </h2>
        <p class="text-white/70 text-sm md:text-base mb-4 line-clamp-2">
          {{ news.excerpt }}
        </p>
        <div class="flex items-center gap-4 text-white/50 text-xs">
          <span class="flex items-center gap-1.5">
            <User class="w-3.5 h-3.5" />
            {{ news.author }}
          </span>
          <span class="flex items-center gap-1.5">
            <Clock class="w-3.5 h-3.5" />
            {{ news.readTime }}
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- Default card variant -->
  <div v-else class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg cursor-pointer transition-all duration-300 hover:-translate-y-1">
    <div class="relative h-48 overflow-hidden">
      <img
        :src="news.imageUrl"
        :alt="news.title"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
      />
      <span class="absolute top-3 left-3 bg-primary/90 text-white text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider backdrop-blur-sm">
        {{ news.category }}
      </span>
      <!-- Play badge for video articles -->
      <div v-if="news.video_url" class="absolute inset-0 flex items-center justify-center">
        <div class="w-12 h-12 rounded-full bg-black/60 backdrop-blur-sm flex items-center justify-center border-2 border-white/70 group-hover:scale-110 transition-transform">
          <svg class="w-5 h-5 text-white fill-current ml-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
        </div>
      </div>
    </div>
    <div class="p-4">
      <h3 class="font-bold text-gray-900 leading-snug mb-2 line-clamp-2 group-hover:text-primary transition-colors duration-200">
        {{ news.title }}
      </h3>
      <p class="text-sm text-gray-500 line-clamp-2 mb-3">
        {{ news.excerpt }}
      </p>
      <div class="flex items-center justify-between text-xs text-gray-400 pt-3 border-t border-gray-100">
        <span class="flex items-center gap-1.5">
          <User class="w-3 h-3" />
          {{ news.author }}
        </span>
        <span class="flex items-center gap-1.5">
          <Clock class="w-3 h-3" />
          {{ news.readTime }}
        </span>
      </div>
    </div>
  </div>
</template>
