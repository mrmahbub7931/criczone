<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Search, Menu, X, ChevronRight } from 'lucide-vue-next'
import { LIVE_MATCHES } from '@/data.js'

const mobileOpen = ref(false)
const scrolled = ref(false)

const logo = `${window.location.origin}/images/criczone.png`

const links = [
  { href: '/', label: 'Home' },
  { href: '/category/news', label: 'News' },
  { href: '/category/matches', label: 'Matches' },
  { href: '/category/players', label: 'Players' },
  { href: '/category/series', label: 'Series' },
  { href: '/category/rankings', label: 'Rankings' },
]

const onScroll = () => { scrolled.value = window.scrollY > 20 }
onMounted(() => window.addEventListener('scroll', onScroll))
onUnmounted(() => window.removeEventListener('scroll', onScroll))
</script>

<template>
  <!-- ── Scores Ticker ── -->
  <!-- <div class="bg-surface-dark text-white/70 text-[11px] font-medium">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 h-8 flex items-center gap-4 overflow-x-auto scrollbar-hide">
      <span class="text-secondary font-bold uppercase tracking-widest flex items-center gap-1.5 flex-shrink-0">
        <span class="w-1.5 h-1.5 rounded-full bg-secondary animate-pulse" />
        Live
      </span>
      <span class="w-px h-3.5 bg-white/10 flex-shrink-0" />
      <template v-for="(m, i) in LIVE_MATCHES" :key="m.id">
        <span class="flex items-center gap-2 flex-shrink-0 hover:text-white cursor-pointer transition-colors">
          <b class="text-white/90">{{ m.team1 }}</b>
          <span>{{ m.score1 }}</span>
          <span class="text-secondary/60 text-[10px]">vs</span>
          <b class="text-white/90">{{ m.team2 }}</b>
          <span>{{ m.score2 }}</span>
          <span class="text-white/30 text-[10px]">{{ m.status }}</span>
        </span>
        <span v-if="i < LIVE_MATCHES.length - 1" class="w-px h-3.5 bg-white/10 flex-shrink-0" />
      </template>
    </div>
  </div> -->

  <!-- ── Main Nav ── -->
  <nav
    :class="[
      'sticky top-0 z-50 transition-all duration-300',
      scrolled ? 'bg-primary shadow-lg shadow-primary/20' : 'bg-primary'
    ]"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 h-[60px] flex items-center justify-between gap-4">

      <!-- Logo -->
      <Link href="/" class="flex-shrink-0 hover:opacity-90 transition-opacity">
        <img :src="logo" alt="CricZone" class="h-10 w-auto" />
      </Link>

      <!-- Desktop Links -->
      <div class="hidden lg:flex items-center gap-1 flex-1 justify-center">
        <Link
          v-for="link in links"
          :key="link.href"
          :href="link.href"
          class="px-3 py-1.5 text-[13px] font-semibold text-white/70 hover:text-white uppercase tracking-wide rounded-md hover:bg-white/10 transition-all duration-150"
        >
          {{ link.label }}
        </Link>
      </div>

      <!-- Right Actions -->
      <div class="flex items-center gap-2">
        <button class="w-9 h-9 rounded-full flex items-center justify-center text-white/60 hover:text-white hover:bg-white/10 transition-all" aria-label="Search">
          <Search class="w-[18px] h-[18px]" />
        </button>

        <a href="#" class="hidden sm:flex items-center gap-2 px-4 py-2 bg-secondary hover:bg-secondary-dark text-white text-[11px] font-bold uppercase tracking-wider rounded-full transition-all shadow-lg shadow-secondary/30">
          <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse" />
          Live Scores
        </a>

        <!-- Mobile toggle -->
        <button
          @click="mobileOpen = !mobileOpen"
          class="lg:hidden w-9 h-9 rounded-full flex items-center justify-center text-white hover:bg-white/10 transition-all"
        >
          <component :is="mobileOpen ? X : Menu" class="w-5 h-5" />
        </button>
      </div>
    </div>

    <!-- Bottom accent line -->
    <div class="h-[3px] bg-gradient-to-r from-secondary via-secondary-light to-secondary" />

    <!-- Mobile Menu -->
    <Transition
      enter-active-class="transition-all duration-250 ease-out"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div v-show="mobileOpen" class="lg:hidden bg-primary-dark">
        <div class="max-w-7xl mx-auto px-4 py-2 space-y-0.5">
          <Link
            v-for="link in links"
            :key="link.href"
            :href="link.href"
            @click="mobileOpen = false"
            class="flex items-center justify-between px-4 py-3 text-white/70 hover:text-white text-sm font-medium hover:bg-white/5 rounded-lg transition-all"
          >
            {{ link.label }}
            <ChevronRight class="w-4 h-4 text-white/20" />
          </Link>
          <a
            href="#"
            class="flex items-center justify-center gap-2 mx-4 mt-3 mb-2 py-2.5 bg-secondary text-white text-sm font-bold rounded-lg"
            @click="mobileOpen = false"
          >
            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse" />
            Live Scores
          </a>
        </div>
      </div>
    </Transition>
  </nav>
</template>
