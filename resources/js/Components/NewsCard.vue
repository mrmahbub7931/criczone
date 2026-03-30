<script setup>
import { Clock, User } from 'lucide-vue-next'

defineProps({
  news: Object,
  variant: {
    type: String,
    default: 'default'
  }
})
</script>

<template>

  <!-- ── SPOTLIGHT: Large featured card ── -->
  <article v-if="variant === 'spotlight'" class="group relative rounded-2xl overflow-hidden cursor-pointer bg-gray-900 h-full">
    <img
      :src="news.imageUrl"
      :alt="news.title"
      class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
    />
    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent" />
    <div class="relative h-full flex flex-col justify-end p-6 sm:p-8">
      <div class="flex gap-2 mb-3">
        <span class="px-2.5 py-1 bg-secondary text-white text-[10px] font-black uppercase tracking-widest rounded">
          {{ news.category }}
        </span>
        <span class="px-2.5 py-1 bg-white/15 backdrop-blur-sm text-white/80 text-[10px] font-semibold rounded">
          {{ news.readTime }} read
        </span>
      </div>
      <h2 class="text-xl sm:text-2xl lg:text-3xl font-black text-white leading-tight mb-2 group-hover:text-secondary-light transition-colors duration-300 line-clamp-3">
        {{ news.title }}
      </h2>
      <p class="text-white/50 text-sm line-clamp-2 mb-3 max-w-lg hidden sm:block">{{ news.excerpt }}</p>
      <div class="flex items-center gap-3 text-white/40 text-[11px]">
        <span class="flex items-center gap-1"><User class="w-3 h-3" />{{ news.author }}</span>
        <span class="text-white/20">|</span>
        <span>{{ news.date }}</span>
      </div>
    </div>
  </article>

  <!-- ── SIDE: Smaller overlay card ── -->
  <article v-else-if="variant === 'side'" class="group relative rounded-xl overflow-hidden cursor-pointer bg-gray-800 h-full min-h-[180px]">
    <img
      :src="news.imageUrl"
      :alt="news.title"
      class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-600"
    />
    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent" />
    <div class="relative h-full flex flex-col justify-end p-4">
      <span class="self-start px-2 py-0.5 bg-secondary text-white text-[9px] font-bold uppercase tracking-wider rounded mb-2">
        {{ news.category }}
      </span>
      <h3 class="text-[15px] font-bold text-white leading-snug line-clamp-2 group-hover:text-secondary-light transition-colors">
        {{ news.title }}
      </h3>
      <span class="text-[10px] text-white/40 mt-1">{{ news.date }}</span>
    </div>
  </article>

  <!-- ── ROW: Horizontal card ── -->
  <article v-else-if="variant === 'row'" class="group flex bg-surface rounded-xl border border-gray-100 overflow-hidden hover:shadow-md cursor-pointer transition-all duration-200">
    <div class="w-28 sm:w-36 flex-shrink-0 relative overflow-hidden">
      <img :src="news.imageUrl" :alt="news.title" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
    </div>
    <div class="flex-1 p-3 sm:p-4 flex flex-col justify-center min-w-0">
      <span class="text-secondary text-[10px] font-bold uppercase tracking-wider mb-0.5">{{ news.category }}</span>
      <h3 class="text-[13px] font-bold text-gray-900 leading-snug line-clamp-2 group-hover:text-primary transition-colors mb-1">{{ news.title }}</h3>
      <span class="text-[11px] text-gray-400">{{ news.author }} &middot; {{ news.readTime }} read</span>
    </div>
  </article>

  <!-- ── DEFAULT: Vertical card ── -->
  <article v-else class="group bg-surface rounded-xl border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 cursor-pointer transition-all duration-300 flex flex-col h-full">
    <div class="relative aspect-[16/10] overflow-hidden">
      <img :src="news.imageUrl" :alt="news.title" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
      <span class="absolute top-3 left-3 px-2 py-0.5 bg-secondary text-white text-[9px] font-black uppercase tracking-widest rounded">
        {{ news.category }}
      </span>
    </div>
    <div class="flex-1 p-4 flex flex-col">
      <h3 class="font-bold text-gray-900 text-[15px] leading-snug line-clamp-2 group-hover:text-primary transition-colors mb-2">
        {{ news.title }}
      </h3>
      <p class="text-[13px] text-gray-500 line-clamp-2 flex-1 leading-relaxed mb-3">{{ news.excerpt }}</p>
      <div class="flex items-center justify-between text-[11px] text-gray-400 pt-3 border-t border-gray-100">
        <span class="flex items-center gap-1"><User class="w-3 h-3" />{{ news.author }}</span>
        <span class="flex items-center gap-1"><Clock class="w-3 h-3" />{{ news.readTime }}</span>
      </div>
    </div>
  </article>

</template>
