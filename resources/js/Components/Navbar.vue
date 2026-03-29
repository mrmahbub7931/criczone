<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import {
  Menu,
  X,
  Search,
  ChevronRight,
  Radio,
} from 'lucide-vue-next'
import { LIVE_SCORES } from '@/data.js'

const open = ref(false)
const scrolled = ref(false)
const searchOpen = ref(false)
const searchQuery = ref('')

const logo = `${window.location.origin}/images/criczone.jpg`

const navLinks = [
  { href: '/', label: 'Home' },
  { href: '/category/news', label: 'News' },
  { href: '/category/matches', label: 'Matches' },
  { href: '/category/players', label: 'Players' },
  { href: '/category/series', label: 'Series' },
  { href: '/category/rankings', label: 'Rankings' },
]

const handleScroll = () => {
  scrolled.value = window.scrollY > 10
}

const closeMenu = () => {
  open.value = false
}

const toggleSearch = () => {
  searchOpen.value = !searchOpen.value
  if (!searchOpen.value) searchQuery.value = ''
}

onMounted(() => window.addEventListener('scroll', handleScroll))
onUnmounted(() => window.removeEventListener('scroll', handleScroll))
</script>

<template>
  <!-- Live Score Ticker -->
  <div class="bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
      <div class="flex items-center gap-4 py-1.5 overflow-x-auto scrollbar-hide text-xs">
        <div class="flex items-center gap-1.5 text-secondary font-bold uppercase tracking-wider flex-shrink-0 pr-3 border-r border-gray-700">
          <Radio class="w-3 h-3" />
          <span class="relative flex h-1.5 w-1.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-secondary opacity-75"></span>
            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-secondary"></span>
          </span>
          Live
        </div>
        <div
          v-for="match in LIVE_SCORES"
          :key="match.id"
          class="flex items-center gap-2.5 flex-shrink-0 cursor-pointer hover:text-secondary transition-colors"
        >
          <span class="font-semibold">{{ match.team1 }}</span>
          <span class="text-gray-400">{{ match.score1 }}</span>
          <span class="text-secondary/70">v</span>
          <span class="font-semibold">{{ match.team2 }}</span>
          <span class="text-gray-400">{{ match.score2 }}</span>
          <span class="text-[10px] text-gray-500 pl-1.5 border-l border-gray-700">{{ match.status }}</span>
          <span class="text-gray-700 last:hidden">|</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Navbar -->
  <header
    :class="[
      'sticky top-0 z-50 transition-all duration-300',
      scrolled
        ? 'bg-primary-dark/95 backdrop-blur-md shadow-lg'
        : 'bg-primary shadow-md'
    ]"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
      <div class="flex items-center justify-between h-14">

        <!-- Logo -->
        <Link href="/" class="flex items-center gap-2 group flex-shrink-0" @click="closeMenu">
          <img
            :src="logo"
            alt="CricZone"
            class="h-8 w-auto rounded-full ring-2 ring-white/20 group-hover:ring-secondary/50 transition-all duration-300"
          />
          <span class="text-white font-extrabold text-lg tracking-tight">
            Cric<span class="text-secondary">Zone</span>
          </span>
        </Link>

        <!-- Desktop Nav -->
        <nav class="hidden lg:flex items-center gap-0.5">
          <Link
            v-for="link in navLinks"
            :key="link.href"
            :href="link.href"
            class="px-3 py-1.5 text-[13px] font-semibold text-white/75 hover:text-white rounded-md hover:bg-white/10 transition-all duration-150 uppercase tracking-wide"
          >
            {{ link.label }}
          </Link>
        </nav>

        <!-- Right side -->
        <div class="flex items-center gap-2">
          <!-- Search toggle -->
          <button
            @click="toggleSearch"
            class="flex items-center justify-center w-9 h-9 rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-all duration-150"
            aria-label="Search"
          >
            <Search class="w-4 h-4" />
          </button>

          <!-- Live Scores CTA -->
          <a
            href="#"
            class="hidden md:inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-secondary hover:bg-secondary-dark text-white text-xs font-bold rounded-md shadow-md transition-all duration-200 uppercase tracking-wide"
          >
            Live Scores
            <span class="relative flex h-2 w-2">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
            </span>
          </a>

          <!-- Mobile hamburger -->
          <button
            @click="open = !open"
            class="lg:hidden flex items-center justify-center w-9 h-9 rounded-lg text-white hover:bg-white/10 transition-all duration-150 focus:outline-none"
            :aria-expanded="open"
            aria-label="Toggle menu"
          >
            <Transition
              enter-active-class="transition-all duration-200"
              enter-from-class="opacity-0 rotate-90 scale-75"
              enter-to-class="opacity-100 rotate-0 scale-100"
              leave-active-class="transition-all duration-150"
              leave-from-class="opacity-100 rotate-0 scale-100"
              leave-to-class="opacity-0 -rotate-90 scale-75"
              mode="out-in"
            >
              <X v-if="open" class="w-5 h-5" />
              <Menu v-else class="w-5 h-5" />
            </Transition>
          </button>
        </div>
      </div>
    </div>

    <!-- Search Bar (expandable) -->
    <Transition
      enter-active-class="transition-all duration-200 ease-out"
      enter-from-class="opacity-0 -translate-y-1"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition-all duration-150 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-1"
    >
      <div v-show="searchOpen" class="border-t border-white/10 bg-primary-dark/95 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3">
          <div class="relative">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search news, players, matches..."
              class="w-full pl-10 pr-4 py-2.5 bg-white/10 border border-white/10 rounded-lg text-sm text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-secondary/50 focus:border-transparent"
              autofocus
            />
          </div>
        </div>
      </div>
    </Transition>

    <!-- Mobile Menu -->
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0 max-h-0"
      enter-to-class="opacity-100 max-h-[500px]"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100 max-h-[500px]"
      leave-to-class="opacity-0 max-h-0"
    >
      <div
        v-show="open"
        class="lg:hidden overflow-hidden border-t border-white/10 bg-primary-dark/98 backdrop-blur-md"
      >
        <nav class="max-w-7xl mx-auto px-4 py-2 flex flex-col">
          <Link
            v-for="link in navLinks"
            :key="link.href"
            :href="link.href"
            @click="closeMenu"
            class="flex items-center justify-between px-3 py-3 text-white/80 hover:text-white text-sm font-medium hover:bg-white/5 rounded-lg transition-all duration-150"
          >
            {{ link.label }}
            <ChevronRight class="w-4 h-4 text-white/30" />
          </Link>

          <!-- Mobile Live Scores -->
          <div class="pt-2 pb-2 mt-1 border-t border-white/10">
            <a
              href="#"
              class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-secondary hover:bg-secondary-dark text-white text-sm font-bold rounded-lg transition-all duration-200"
              @click="closeMenu"
            >
              <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
              </span>
              Live Scores
            </a>
          </div>
        </nav>
      </div>
    </Transition>
  </header>
</template>
