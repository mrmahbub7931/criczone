<script setup>
import { Calendar, Clock, User } from 'lucide-vue-next'

defineProps({
  news: Object,
  variant: {
    type: String,
    default: 'default',
    validator: (v) => ['default', 'featured', 'horizontal', 'compact', 'headline'].includes(v)
  }
})
</script>

<template>

  <!-- ===== FEATURED: Big overlay card for top story ===== -->
  <article
    v-if="variant === 'featured'"
    class="group relative overflow-hidden cursor-pointer bg-gray-900 rounded-2xl"
  >
    <div class="relative aspect-[4/3] sm:aspect-[16/9] lg:aspect-[16/10]">
      <img
        :src="news.imageUrl"
        :alt="news.title"
        class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-[800ms] ease-out"
      />
      <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-black/10" />

      <!-- Content -->
      <div class="absolute bottom-0 left-0 right-0 p-5 sm:p-8">
        <div class="flex items-center gap-2 mb-3">
          <span class="bg-secondary text-white text-[10px] font-black px-3 py-1 rounded-sm uppercase tracking-widest">
            {{ news.category }}
          </span>
          <span class="bg-white/20 backdrop-blur-sm text-white text-[10px] font-semibold px-2.5 py-1 rounded-sm">
            {{ news.readTime }}
          </span>
        </div>
        <h2 class="text-xl sm:text-2xl lg:text-3xl font-black text-white leading-[1.2] mb-3 group-hover:text-secondary transition-colors duration-300">
          {{ news.title }}
        </h2>
        <p class="text-white/50 text-sm leading-relaxed line-clamp-2 max-w-xl hidden sm:block mb-4">
          {{ news.excerpt }}
        </p>
        <div class="flex items-center gap-3 text-white/40 text-xs">
          <span class="flex items-center gap-1">
            <User class="w-3 h-3" />
            {{ news.author }}
          </span>
          <span class="text-white/20">|</span>
          <span class="flex items-center gap-1">
            <Calendar class="w-3 h-3" />
            {{ news.date }}
          </span>
        </div>
      </div>
    </div>
  </article>

  <!-- ===== HEADLINE: Smaller overlay card (for top section grid) ===== -->
  <article
    v-else-if="variant === 'headline'"
    class="group relative overflow-hidden cursor-pointer bg-gray-900 rounded-xl h-full"
  >
    <img
      :src="news.imageUrl"
      :alt="news.title"
      class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
    />
    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent" />
    <div class="absolute bottom-0 left-0 right-0 p-4">
      <span class="inline-block bg-secondary text-white text-[9px] font-black px-2 py-0.5 rounded-sm uppercase tracking-widest mb-2">
        {{ news.category }}
      </span>
      <h3 class="text-sm sm:text-base font-bold text-white leading-snug line-clamp-2 group-hover:text-secondary transition-colors duration-200">
        {{ news.title }}
      </h3>
      <span class="text-[11px] text-white/40 mt-1.5 block">{{ news.author }} &middot; {{ news.readTime }}</span>
    </div>
  </article>

  <!-- ===== HORIZONTAL: Side-by-side (image left, text right) ===== -->
  <article
    v-else-if="variant === 'horizontal'"
    class="group flex bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md cursor-pointer transition-all duration-200 border border-gray-100"
  >
    <div class="relative w-32 sm:w-40 flex-shrink-0 overflow-hidden">
      <img
        :src="news.imageUrl"
        :alt="news.title"
        class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
      />
    </div>
    <div class="flex flex-col justify-center py-3 px-4 min-w-0 flex-1">
      <span class="text-secondary text-[10px] font-bold uppercase tracking-widest mb-1">{{ news.category }}</span>
      <h3 class="font-bold text-gray-900 text-[13px] leading-snug line-clamp-2 group-hover:text-primary transition-colors duration-150 mb-1.5">
        {{ news.title }}
      </h3>
      <div class="flex items-center gap-2 text-[11px] text-gray-400 mt-auto">
        <span>{{ news.author }}</span>
        <span class="w-0.5 h-0.5 rounded-full bg-gray-300" />
        <span>{{ news.readTime }}</span>
      </div>
    </div>
  </article>

  <!-- ===== COMPACT: Tiny row for sidebar lists ===== -->
  <article
    v-else-if="variant === 'compact'"
    class="group flex items-start gap-3 cursor-pointer"
  >
    <div class="w-[72px] h-[54px] rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
      <img
        :src="news.imageUrl"
        :alt="news.title"
        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
      />
    </div>
    <div class="min-w-0 flex-1 py-0.5">
      <h4 class="text-[13px] font-semibold text-gray-800 leading-snug line-clamp-2 group-hover:text-primary transition-colors duration-150">
        {{ news.title }}
      </h4>
      <span class="text-[10px] text-gray-400 mt-1 block">{{ news.readTime }}</span>
    </div>
  </article>

  <!-- ===== DEFAULT: Standard vertical card ===== -->
  <article
    v-else
    class="group bg-white rounded-xl overflow-hidden border border-gray-100 hover:shadow-xl cursor-pointer transition-all duration-300 hover:-translate-y-1 flex flex-col"
  >
    <div class="relative aspect-[16/10] overflow-hidden bg-gray-100">
      <img
        :src="news.imageUrl"
        :alt="news.title"
        class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
      />
      <div class="absolute top-0 left-0 m-3">
        <span class="bg-secondary text-white text-[9px] font-black px-2.5 py-1 rounded-sm uppercase tracking-widest">
          {{ news.category }}
        </span>
      </div>
    </div>
    <div class="flex flex-col flex-1 p-4">
      <h3 class="font-bold text-gray-900 leading-snug mb-2 line-clamp-2 group-hover:text-primary transition-colors duration-200 text-[15px]">
        {{ news.title }}
      </h3>
      <p class="text-[13px] text-gray-500 line-clamp-2 mb-4 flex-1 leading-relaxed">
        {{ news.excerpt }}
      </p>
      <div class="flex items-center gap-3 text-[11px] text-gray-400 pt-3 border-t border-gray-50">
        <span class="flex items-center gap-1">
          <User class="w-3 h-3" />
          {{ news.author }}
        </span>
        <span class="ml-auto flex items-center gap-1">
          <Clock class="w-3 h-3" />
          {{ news.readTime }}
        </span>
      </div>
    </div>
  </article>

</template>
