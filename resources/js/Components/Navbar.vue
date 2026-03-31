<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Search, Menu, X, ChevronRight } from 'lucide-vue-next'
import { useMenu } from '@/composables/useMenu.js'
import { useSettings } from '@/composables/useSettings.js'

const mobileOpen = ref(false)
const scrolled   = ref(false)

const { get: setting } = useSettings()
const logo = computed(() =>
  setting('general', 'site_logo', `${window.location.origin}/images/criczone.png`)
)

// Dynamic menu items from API
const { items: menuItems, loading: menuLoading } = useMenu('header')

// Fallback links shown while loading or if menu is empty
const fallbackLinks = [
  { url: '/',                 label: 'Home' },
  { url: '/category/news',    label: 'News' },
  { url: '/category/matches', label: 'Matches' },
  { url: '/category/players', label: 'Players' },
  { url: '/category/series',  label: 'Series' },
  { url: '/category/rankings',label: 'Rankings' },
]

const onScroll = () => { scrolled.value = window.scrollY > 20 }
onMounted(() => window.addEventListener('scroll', onScroll))
onUnmounted(() => window.removeEventListener('scroll', onScroll))
</script>

<template>
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
        <!-- Dynamic items -->
        <template v-if="!menuLoading && menuItems.length">
          <template v-for="link in menuItems" :key="link.id">
            <!-- Item with children = dropdown -->
            <div v-if="link.children && link.children.length" class="relative group">
              <button class="px-3 py-1.5 text-[13px] font-semibold text-white/70 hover:text-white uppercase tracking-wide rounded-md hover:bg-white/10 transition-all duration-150 flex items-center gap-1">
                {{ link.label }}
                <svg class="w-3 h-3 opacity-60 group-hover:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
              </button>
              <!-- Dropdown -->
              <div class="absolute top-full left-0 mt-1 min-w-[160px] bg-white rounded-xl shadow-xl border border-gray-100 py-1.5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-10">
                <a
                  v-for="child in link.children" :key="child.id"
                  :href="child.url"
                  :target="child.target"
                  class="block px-4 py-2 text-[13px] font-medium text-gray-700 hover:text-[#0D47A1] hover:bg-blue-50 transition-colors"
                >{{ child.label }}</a>
              </div>
            </div>
            <!-- Simple link -->
            <a
              v-else
              :href="link.url"
              :target="link.target"
              class="px-3 py-1.5 text-[13px] font-semibold text-white/70 hover:text-white uppercase tracking-wide rounded-md hover:bg-white/10 transition-all duration-150"
            >{{ link.label }}</a>
          </template>
        </template>

        <!-- Fallback while loading or empty -->
        <template v-else>
          <Link
            v-for="link in fallbackLinks"
            :key="link.url"
            :href="link.url"
            class="px-3 py-1.5 text-[13px] font-semibold text-white/70 hover:text-white uppercase tracking-wide rounded-md hover:bg-white/10 transition-all duration-150"
          >{{ link.label }}</Link>
        </template>
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
          <!-- Dynamic items -->
          <template v-if="!menuLoading && menuItems.length">
            <template v-for="link in menuItems" :key="link.id">
              <a
                :href="link.url"
                :target="link.target"
                @click="mobileOpen = false"
                class="flex items-center justify-between px-4 py-3 text-white/70 hover:text-white text-sm font-medium hover:bg-white/5 rounded-lg transition-all"
              >
                {{ link.label }}
                <ChevronRight class="w-4 h-4 text-white/20" />
              </a>
              <!-- Children -->
              <a
                v-for="child in link.children" :key="child.id"
                :href="child.url"
                :target="child.target"
                @click="mobileOpen = false"
                class="flex items-center justify-between pl-8 pr-4 py-2.5 text-white/50 hover:text-white text-sm hover:bg-white/5 rounded-lg transition-all"
              >
                {{ child.label }}
                <ChevronRight class="w-3 h-3 text-white/20" />
              </a>
            </template>
          </template>
          <!-- Fallback -->
          <template v-else>
            <Link
              v-for="link in fallbackLinks"
              :key="link.url"
              :href="link.url"
              @click="mobileOpen = false"
              class="flex items-center justify-between px-4 py-3 text-white/70 hover:text-white text-sm font-medium hover:bg-white/5 rounded-lg transition-all"
            >
              {{ link.label }}
              <ChevronRight class="w-4 h-4 text-white/20" />
            </Link>
          </template>

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
