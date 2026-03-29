<script setup>
import { Clock, User, ArrowRight } from 'lucide-vue-next'

defineProps({
  news: Object,
  variant: {
    type: String,
    default: 'default',
    validator: (v) => ['default', 'featured', 'horizontal', 'compact'].includes(v)
  }
})
</script>

<template>

  <!-- FEATURED: Large overlay card for the top story -->
  <article
    v-if="variant === 'featured'"
    class="group relative rounded-xl overflow-hidden cursor-pointer bg-gray-900"
  >
    <div class="relative aspect-[16/8] sm:aspect-[16/7]">
      <img
        :src="news.imageUrl"
        :alt="news.title"
        class="absolute inset-0 w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-700 ease-out"
      />
      <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/50 to-transparent" />

      <div class="absolute bottom-0 left-0 right-0 p-5 sm:p-7">
        <span class="inline-block bg-secondary text-white text-[10px] font-bold px-2.5 py-1 rounded uppercase tracking-widest mb-3">
          {{ news.category }}
        </span>
        <h2 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-white leading-tight mb-2 group-hover:text-secondary transition-colors duration-200 line-clamp-2">
          {{ news.title }}
        </h2>
        <p class="text-white/60 text-sm mb-3 line-clamp-2 max-w-2xl hidden sm:block">
          {{ news.excerpt }}
        </p>
        <div class="flex items-center gap-3 text-white/40 text-xs">
          <span class="flex items-center gap-1">
            <User class="w-3 h-3" />
            {{ news.author }}
          </span>
          <span class="w-0.5 h-0.5 rounded-full bg-white/30" />
          <span class="flex items-center gap-1">
            <Clock class="w-3 h-3" />
            {{ news.readTime }}
          </span>
          <span class="w-0.5 h-0.5 rounded-full bg-white/30" />
          <span>{{ news.date }}</span>
        </div>
      </div>
    </div>
  </article>

  <!-- HORIZONTAL: Side-by-side image + text (for secondary stories) -->
  <article
    v-else-if="variant === 'horizontal'"
    class="group flex gap-4 bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md cursor-pointer transition-all duration-200"
  >
    <div class="relative w-40 sm:w-48 flex-shrink-0 overflow-hidden">
      <img
        :src="news.imageUrl"
        :alt="news.title"
        class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
      />
      <span class="absolute top-2 left-2 bg-primary/90 text-white text-[9px] font-bold px-2 py-0.5 rounded uppercase tracking-wider backdrop-blur-sm">
        {{ news.category }}
      </span>
    </div>
    <div class="flex flex-col justify-center py-3 pr-4 min-w-0">
      <h3 class="font-bold text-gray-900 text-sm leading-snug mb-1.5 line-clamp-2 group-hover:text-primary transition-colors duration-150">
        {{ news.title }}
      </h3>
      <p class="text-xs text-gray-500 line-clamp-2 mb-2 hidden sm:block">
        {{ news.excerpt }}
      </p>
      <div class="flex items-center gap-2 text-[11px] text-gray-400">
        <span>{{ news.author }}</span>
        <span class="w-0.5 h-0.5 rounded-full bg-gray-300" />
        <span>{{ news.readTime }}</span>
      </div>
    </div>
  </article>

  <!-- COMPACT: Minimal row for sidebar / lists -->
  <article
    v-else-if="variant === 'compact'"
    class="group flex items-start gap-3 cursor-pointer py-3"
  >
    <div class="relative w-16 h-16 rounded-lg overflow-hidden flex-shrink-0">
      <img
        :src="news.imageUrl"
        :alt="news.title"
        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
      />
    </div>
    <div class="min-w-0 flex-1">
      <h4 class="text-sm font-semibold text-gray-800 leading-snug line-clamp-2 group-hover:text-primary transition-colors duration-150">
        {{ news.title }}
      </h4>
      <span class="text-[11px] text-gray-400 mt-1 block">{{ news.readTime }}</span>
    </div>
  </article>

  <!-- DEFAULT: Standard vertical card -->
  <article
    v-else
    class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg cursor-pointer transition-all duration-300 hover:-translate-y-0.5 flex flex-col"
  >
    <div class="relative aspect-[16/10] overflow-hidden">
      <img
        :src="news.imageUrl"
        :alt="news.title"
        class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
      />
      <span class="absolute top-3 left-3 bg-primary/90 text-white text-[10px] font-bold px-2.5 py-0.5 rounded uppercase tracking-wider backdrop-blur-sm">
        {{ news.category }}
      </span>
    </div>
    <div class="flex flex-col flex-1 p-4">
      <h3 class="font-bold text-gray-900 leading-snug mb-2 line-clamp-2 group-hover:text-primary transition-colors duration-200">
        {{ news.title }}
      </h3>
      <p class="text-sm text-gray-500 line-clamp-2 mb-3 flex-1">
        {{ news.excerpt }}
      </p>
      <div class="flex items-center justify-between text-xs text-gray-400 pt-3 border-t border-gray-100">
        <span class="flex items-center gap-1">
          <User class="w-3 h-3" />
          {{ news.author }}
        </span>
        <span class="flex items-center gap-1">
          <Clock class="w-3 h-3" />
          {{ news.readTime }}
        </span>
      </div>
    </div>
  </article>

</template>
