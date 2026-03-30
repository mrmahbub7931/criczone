<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import {
  Menu,
  X,
  Home,
  Newspaper,
  Trophy,
  Users,
  BarChart2,
  ChevronDown
} from 'lucide-vue-next'

const open = ref(false)
const scrolled = ref(false)

const logo = `${window.location.origin}/images/criczone.jpg`

const navLinks = [
  { href: '/',                  label: 'Home',    icon: Home },
  { href: '/category/news',     label: 'News',    icon: Newspaper },
  { href: '/category/matches',  label: 'Matches', icon: Trophy },
  { href: '/category/players',  label: 'Players', icon: Users },
  { href: '/category/series',   label: 'Series',  icon: BarChart2 },
]

const handleScroll = () => {
  scrolled.value = window.scrollY > 10
}

const closeMenu = () => {
  open.value = false
}

onMounted(() => window.addEventListener('scroll', handleScroll))
onUnmounted(() => window.removeEventListener('scroll', handleScroll))
</script>

<template>
  <header
    :class="[
      'sticky top-0 z-50 transition-all duration-300',
      scrolled
        ? 'bg-[#0a2f6e]/95 backdrop-blur-md shadow-[0_4px_24px_rgba(0,0,0,0.35)]'
        : 'bg-[#0D47A1] shadow-md'
    ]"
  >
    <!-- Top accent line -->
    <div class="h-[3px] bg-gradient-to-r from-red-500 via-red-400 to-orange-400" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6">
      <div class="flex items-center justify-between h-16">

        <!-- Logo -->
        <Link href="/" class="flex items-center gap-2.5 group flex-shrink-0" @click="closeMenu">
          <div class="relative">
            <div class="absolute -inset-1 rounded-full bg-white/10 scale-0 group-hover:scale-100 transition-transform duration-300" />
            <img
              :src="logo"
              alt="CricZone"
              class="h-9 w-auto rounded-full ring-2 ring-white/20 group-hover:ring-red-400/60 transition-all duration-300 relative"
            />
          </div>
          <span class="text-white font-bold text-xl tracking-tight hidden sm:block">
            Cric<span class="text-red-400">Zone</span>
          </span>
        </Link>

        <!-- Desktop Nav -->
        <nav class="hidden md:flex items-center gap-1">
          <Link
            v-for="link in navLinks"
            :key="link.href"
            :href="link.href"
            class="
              group flex items-center gap-1.5 px-3.5 py-2 rounded-lg
              text-sm font-medium text-white/80 hover:text-white
              hover:bg-white/10 active:bg-white/20
              transition-all duration-200 relative overflow-hidden
            "
          >
            <!-- Hover glow -->
            <span class="absolute inset-0 bg-gradient-to-r from-red-500/0 via-red-500/0 to-red-500/0 group-hover:from-red-500/5 group-hover:via-red-400/5 transition-all duration-300 rounded-lg" />
            <component
              :is="link.icon"
              class="w-3.5 h-3.5 text-red-400/70 group-hover:text-red-400 transition-colors duration-200 relative"
            />
            <span class="relative">{{ link.label }}</span>
          </Link>
        </nav>

        <!-- Right side: CTA + Hamburger -->
        <div class="flex items-center gap-3">
          <!-- Desktop CTA -->
          <a
            href="#"
            class="
              hidden md:inline-flex items-center gap-1.5 px-4 py-2
              bg-[#E53935] hover:bg-[#EF5350] active:bg-[#C62828]
              text-white text-sm font-semibold rounded-lg
              shadow-[0_2px_12px_rgba(239,68,68,0.35)]
              hover:shadow-[0_4px_18px_rgba(239,68,68,0.5)]
              transition-all duration-200
            "
          >
            Live Scores
            <span class="w-2 h-2 rounded-full bg-white animate-pulse" />
          </a>

          <!-- Hamburger -->
          <button
            @click="open = !open"
            class="
              lg:hidden flex items-center justify-center
              w-10 h-10 rounded-lg
              text-white hover:bg-white/10 active:bg-white/20
              transition-all duration-200 focus:outline-none
              focus-visible:ring-2 focus-visible:ring-red-400
            "
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

    <!-- Mobile Menu -->
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0 -translate-y-2 max-h-0"
      enter-to-class="opacity-100 translate-y-0 max-h-[400px]"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100 translate-y-0 max-h-[400px]"
      leave-to-class="opacity-0 -translate-y-2 max-h-0"
    >
      <div
        v-show="open"
        class="md:hidden overflow-hidden border-t border-white/10 bg-[#0a2f6e]/98 backdrop-blur-md"
      >
        <nav class="max-w-7xl mx-auto px-4 py-3 flex flex-col gap-1">
          <Link
            v-for="link in navLinks"
            :key="link.href"
            :href="link.href"
            @click="closeMenu"
            class="
              flex items-center gap-3 px-4 py-3 rounded-xl
              text-white/80 hover:text-white text-sm font-medium
              hover:bg-white/10 active:bg-white/20
              transition-all duration-200 group
            "
          >
            <span class="
              flex items-center justify-center w-8 h-8 rounded-lg
              bg-white/5 group-hover:bg-red-500/20
              transition-colors duration-200
            ">
              <component
                :is="link.icon"
                class="w-4 h-4 text-red-400/60 group-hover:text-red-400 transition-colors duration-200"
              />
            </span>
            {{ link.label }}
            <ChevronDown class="w-3.5 h-3.5 ml-auto -rotate-90 text-white/30 group-hover:text-white/60 transition-colors duration-200" />
          </Link>

          <!-- Mobile CTA -->
          <div class="pt-2 pb-1">
            <a
              href="#"
              class="
                flex items-center justify-center gap-2 w-full px-4 py-3
                bg-[#E53935] hover:bg-[#EF5350] active:bg-[#C62828]
                text-white text-sm font-semibold rounded-xl
                shadow-[0_2px_12px_rgba(239,68,68,0.3)]
                transition-all duration-200
              "
              @click="closeMenu"
            >
              <span class="w-2 h-2 rounded-full bg-white animate-pulse" />
              Live Scores
            </a>
          </div>
        </nav>
      </div>
    </Transition>
  </header>
</template>