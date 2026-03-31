<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { LIVE_SCORES } from '@/data.js'
import { Zap, TrendingUp, Radio } from 'lucide-vue-next'

const currentSlide = ref(0)
const slides = [
  {
    title: 'Your Ultimate Cricket Destination',
    subtitle: 'Breaking news, live scores, expert analysis, and in-depth coverage of cricket from around the world.',
    cta: 'Explore News',
    ctaLink: '/category/news',
    image: 'https://images.unsplash.com/photo-1531415074968-036ba1b575da?w=1400&h=600&fit=crop',
  },
  {
    title: 'IPL 2026 Is Here',
    subtitle: 'Follow every match, every run, every wicket. Complete coverage of the biggest cricket league in the world.',
    cta: 'IPL Coverage',
    ctaLink: '/category/news',
    image: 'https://images.unsplash.com/photo-1540747913346-19e32dc3e97e?w=1400&h=600&fit=crop',
  },
  {
    title: 'T20 World Cup 2026',
    subtitle: 'Squads announced, venues confirmed. Get ready for the biggest T20 event of the year.',
    cta: 'Read More',
    ctaLink: '/category/news',
    image: 'https://images.unsplash.com/photo-1624526267942-ab0ff8a3e972?w=1400&h=600&fit=crop',
  },
]

let interval = null
onMounted(() => {
  interval = setInterval(() => {
    currentSlide.value = (currentSlide.value + 1) % slides.length
  }, 6000)
})
onUnmounted(() => clearInterval(interval))

const goToSlide = (i) => {
  currentSlide.value = i
  clearInterval(interval)
  interval = setInterval(() => {
    currentSlide.value = (currentSlide.value + 1) % slides.length
  }, 6000)
}
</script>

<template>
  <section class="relative">
    <!-- Hero Slider -->
    <div class="relative h-[520px] md:h-[560px] overflow-hidden">
      <TransitionGroup
        enter-active-class="transition-opacity duration-700 ease-in-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-700 ease-in-out absolute inset-0"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-for="(slide, index) in slides"
          v-show="currentSlide === index"
          :key="index"
          class="absolute inset-0"
        >
          <img
            :src="slide.image"
            :alt="slide.title"
            class="w-full h-full object-cover"
          />
          <!-- Gradient overlay -->
          <div class="absolute inset-0 bg-gradient-to-r from-primary-dark/90 via-primary/70 to-transparent" />
          <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent" />
        </div>
      </TransitionGroup>

      <!-- Content -->
      <div class="relative z-10 h-full max-w-7xl mx-auto px-4 sm:px-6 flex items-center">
        <div class="max-w-2xl">
          <div class="inline-flex items-center gap-2 bg-secondary/90 text-white text-xs font-bold px-3 py-1.5 rounded-full mb-5 uppercase tracking-wider">
            <Zap class="w-3.5 h-3.5" />
            Breaking News
          </div>
          <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-tight mb-4 drop-shadow-lg">
            {{ slides[currentSlide].title }}
          </h1>
          <p class="text-base md:text-lg text-white/80 mb-8 leading-relaxed max-w-xl">
            {{ slides[currentSlide].subtitle }}
          </p>
          <div class="flex items-center gap-4">
            <a
              :href="slides[currentSlide].ctaLink"
              class="inline-flex items-center gap-2 px-6 py-3 bg-secondary hover:bg-secondary-dark text-white font-bold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200"
            >
              {{ slides[currentSlide].cta }}
              <TrendingUp class="w-4 h-4" />
            </a>
            <a
              href="#"
              class="inline-flex items-center gap-2 px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-lg backdrop-blur-sm border border-white/20 transition-all duration-200"
            >
              <Radio class="w-4 h-4 text-secondary" />
              Live Scores
            </a>
          </div>
        </div>
      </div>

      <!-- Slide indicators -->
      <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-10 flex items-center gap-2">
        <button
          v-for="(slide, i) in slides"
          :key="i"
          @click="goToSlide(i)"
          class="group relative h-2 rounded-full transition-all duration-300"
          :class="currentSlide === i ? 'w-8 bg-secondary' : 'w-2 bg-white/40 hover:bg-white/60'"
          :aria-label="`Go to slide ${i + 1}`"
        />
      </div>
    </div>

    <!-- Live Score Ticker -->
    <div class="bg-primary-dark border-t border-white/10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex items-center gap-6 py-3 overflow-x-auto scrollbar-hide">
          <div class="flex items-center gap-2 text-secondary font-bold text-xs uppercase tracking-wider flex-shrink-0">
            <span class="w-2 h-2 rounded-full bg-secondary animate-pulse" />
            Live
          </div>
          <div
            v-for="match in LIVE_SCORES"
            :key="match.id"
            class="flex items-center gap-4 text-white text-sm flex-shrink-0 bg-white/5 rounded-lg px-4 py-2"
          >
            <span class="font-bold">{{ match.team1 }}</span>
            <span class="text-white/60 text-xs">{{ match.score1 }}</span>
            <span class="text-secondary text-xs font-medium">vs</span>
            <span class="font-bold">{{ match.team2 }}</span>
            <span class="text-white/60 text-xs">{{ match.score2 }}</span>
            <span class="text-xs text-white/40 border-l border-white/10 pl-3">{{ match.status }}</span>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
