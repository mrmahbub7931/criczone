<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Menu, X, Search, Bell, ChevronRight } from 'lucide-vue-next'
import { LIVE_SCORES } from '@/data.js'

const open = ref(false)
const scrolled = ref(false)
const searchOpen = ref(false)
const searchQuery = ref('')

const logo = `${window.location.origin}/images/criczone.png`

const navLinks = [
  { href: '/', label: 'Home' },
  { href: '/category/news', label: 'News' },
  { href: '/category/matches', label: 'Matches' },
  { href: '/category/players', label: 'Players' },
  { href: '/category/series', label: 'Series' },
  { href: '/category/rankings', label: 'Rankings' },
]

const handleScroll = () => { scrolled.value = window.scrollY > 20 }
const closeMenu = () => { open.value = false }
const toggleSearch = () => {
  searchOpen.value = !searchOpen.value
  if (!searchOpen.value) searchQuery.value = ''
}

onMounted(() => window.addEventListener('scroll', handleScroll))
onUnmounted(() => window.removeEventListener('scroll', handleScroll))
</script>

<template>
  <!-- ===== Top Ticker Bar ===== -->
  <div class="bg-[#051c3a] text-white text-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center h-8 gap-5 overflow-x-auto scrollbar-hide">
      <span class="flex items-center gap-1.5 text-secondary font-bold uppercase tracking-widest flex-shrink-0">
        <span class="relative flex h-1.5 w-1.5">
          <span class="animate-ping absolute h-full w-full rounded-full bg-secondary opacity-75" />
          <span class="relative rounded-full h-1.5 w-1.5 bg-secondary" />
        </span>
        Live
      </span>
      <div class="h-3 w-px bg-white/10 flex-shrink-0" />
      <div
        v-for="(match, i) in LIVE_SCORES"
        :key="match.id"
        class="flex items-center gap-2 flex-shrink-0 text-white/70 hover:text-white cursor-pointer transition-colors"
      >
        <span class="font-semibold text-white/90">{{ match.team1 }}</span>
        <span class="text-white/40">{{ match.score1 }}</span>
        <span class="text-secondary text-[10px]">vs</span>
        <span class="font-semibold text-white/90">{{ match.team2 }}</span>
        <span class="text-white/40">{{ match.score2 }}</span>
        <span class="text-[10px] text-white/30 ml-1">{{ match.status }}</span>
        <div v-if="i < LIVE_SCORES.length - 1" class="h-3 w-px bg-white/10 ml-2 flex-shrink-0" />
      </div>
    </div>
  </div>

  <!-- ===== Main Header ===== -->
  <header
    :class="[
      'sticky top-0 z-50 transition-all duration-300 border-b-[3px] border-secondary',
      scrolled ? 'bg-primary/95 backdrop-blur-lg shadow-xl' : 'bg-primary'
    ]"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
      <div class="flex items-center justify-between h-16">

        <!-- Logo (image only) -->
        <Link href="/" class="flex-shrink-0" @click="closeMenu">
          <img
            :src="logo"
            alt="CricZone"
            class="h-11 w-auto drop-shadow-md hover:scale-105 transition-transform duration-200"
          />
        </Link>

        <!-- Desktop Navigation -->
        <nav class="hidden lg:flex items-center">
          <Link
            v-for="link in navLinks"
            :key="link.href"
            :href="link.href"
            class="relative px-4 py-5 text-[13px] font-bold text-white/80 hover:text-white uppercase tracking-wider transition-colors duration-150 group"
          >
            {{ link.label }}
            <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-0 h-[3px] bg-secondary group-hover:w-3/4 transition-all duration-200 rounded-t" />
          </Link>
        </nav>

        <!-- Right Actions -->
        <div class="flex items-center gap-1">
          <button
            @click="toggleSearch"
            class="w-10 h-10 rounded-full flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 transition-all duration-150"
            aria-label="Search"
          >
            <Search class="w-[18px] h-[18px]" />
          </button>
          <button
            class="hidden sm:flex w-10 h-10 rounded-full items-center justify-center text-white/70 hover:text-white hover:bg-white/10 transition-all duration-150 relative"
            aria-label="Notifications"
          >
            <Bell class="w-[18px] h-[18px]" />
            <span class="absolute top-2 right-2 w-2 h-2 bg-secondary rounded-full" />
          </button>

          <a
            href="#"
            class="hidden md:flex items-center gap-2 ml-3 px-4 py-2 bg-secondary hover:bg-secondary-dark text-white text-xs font-bold rounded-full transition-all duration-200 shadow-lg shadow-secondary/25"
          >
            <span class="relative flex h-2 w-2">
              <span class="animate-ping absolute h-full w-full rounded-full bg-white opacity-75" />
              <span class="relative rounded-full h-2 w-2 bg-white" />
            </span>
            LIVE SCORES
          </a>

          <!-- Mobile Toggle -->
          <button
            @click="open = !open"
            class="lg:hidden w-10 h-10 rounded-full flex items-center justify-center text-white hover:bg-white/10 transition-all duration-150 ml-1"
            :aria-expanded="open"
            aria-label="Menu"
          >
            <component :is="open ? X : Menu" class="w-5 h-5" />
          </button>
        </div>
      </div>
    </div>

    <!-- Search Overlay -->
    <Transition
      enter-active-class="transition-all duration-200"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition-all duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div v-show="searchOpen" class="absolute inset-x-0 top-full bg-primary-dark shadow-2xl z-50">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 py-4">
          <div class="relative">
            <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-white/30" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search news, players, matches..."
              class="w-full pl-12 pr-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 text-sm focus:outline-none focus:ring-2 focus:ring-secondary/40 focus:bg-white/10"
            />
          </div>
        </div>
      </div>
    </Transition>

    <!-- Mobile Drawer -->
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0 -translate-y-3"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-3"
    >
      <div v-show="open" class="lg:hidden bg-primary-dark border-t border-white/5">
        <nav class="max-w-7xl mx-auto px-4 py-3 space-y-0.5">
          <Link
            v-for="link in navLinks"
            :key="link.href"
            :href="link.href"
            @click="closeMenu"
            class="flex items-center justify-between px-4 py-3.5 text-white/80 hover:text-white text-sm font-semibold hover:bg-white/5 rounded-xl transition-all duration-150"
          >
            {{ link.label }}
            <ChevronRight class="w-4 h-4 text-white/20" />
          </Link>
          <div class="pt-3 pb-1">
            <a
              href="#"
              class="flex items-center justify-center gap-2 w-full py-3 bg-secondary hover:bg-secondary-dark text-white text-sm font-bold rounded-xl transition-all duration-200"
              @click="closeMenu"
            >
              <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute h-full w-full rounded-full bg-white opacity-75" />
                <span class="relative rounded-full h-2 w-2 bg-white" />
              </span>
              LIVE SCORES
            </a>
          </div>
        </nav>
      </div>
    </Transition>
  </header>
</template>
