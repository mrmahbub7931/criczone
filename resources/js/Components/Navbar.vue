<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Search, Menu, X, ChevronRight, ChevronDown, LayoutGrid } from 'lucide-vue-next'
import { useMenu }      from '@/composables/useMenu.js'
import { useSettings }  from '@/composables/useSettings.js'
import { useLocale }    from '@/composables/useLocale.js'
import { useTheme }     from '@/composables/useTheme.js'
import { useLanguage }  from '@/composables/useLanguage.js'
import NotificationBell from '@/Components/NotificationBell.vue'
import axios            from 'axios'

const mobileOpen  = ref(false)
const scrolled    = ref(false)
const catOpen     = ref(false)
const mobileCatOpen = ref(false)
const catDropRef  = ref(null)

const { t } = useLocale()
const { dark, toggle: toggleTheme } = useTheme()
const { lang, toggleLang } = useLanguage()

const { get: setting } = useSettings()
const logo = computed(() =>
  setting('general', 'site_logo', `${window.location.origin}/images/criczone.png`)
)

const { items: menuItems, loading: menuLoading } = useMenu('header')

const fallbackLinks = [
  { url: '/',                  label: 'Home' },
  { url: '/category/news',     label: 'News' },
  { url: '/category/matches',  label: 'Matches' },
  { url: '/category/players',  label: 'Players' },
  { url: '/category/series',   label: 'Series' },
  { url: '/category/rankings', label: 'Rankings' },
  { url: '/games',             label: 'Games' },
]

// ── Categories ────────────────────────────────────────────────────────────────
const allCategories = ref([])
const pinnedCats    = computed(() => allCategories.value.filter(c => c.is_pinned))
const otherCats     = computed(() => allCategories.value.filter(c => !c.is_pinned))

// ── Scroll / outside click ────────────────────────────────────────────────────
const onScroll = () => { scrolled.value = window.scrollY > 20 }

const handleOutsideClick = (e) => {
  if (catDropRef.value && !catDropRef.value.contains(e.target)) {
    catOpen.value = false
  }
}

onMounted(async () => {
  window.addEventListener('scroll', onScroll)
  document.addEventListener('click', handleOutsideClick)
  try {
    const { data } = await axios.get('/api/categories/public')
    allCategories.value = data
  } catch { /* silent */ }
})

onUnmounted(() => {
  window.removeEventListener('scroll', onScroll)
  document.removeEventListener('click', handleOutsideClick)
})
</script>

<template>
  <nav
    :class="[
      'sticky top-0 z-50 transition-all duration-300',
      scrolled ? 'bg-primary shadow-lg shadow-primary/20' : 'bg-primary',
    ]"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 h-[64px] sm:h-[72px] flex items-center justify-between gap-4">

      <!-- Logo -->
      <Link href="/" class="flex-shrink-0 hover:opacity-90 transition-opacity">
        <img :src="logo" alt="CricZone" class="h-10 sm:h-[52px] w-auto" />
      </Link>

      <!-- Desktop Links -->
      <div class="hidden lg:flex items-center gap-1 flex-1 justify-center">

        <!-- Dynamic / fallback menu items -->
        <template v-if="!menuLoading && menuItems.length">
          <template v-for="link in menuItems" :key="link.id">
            <div v-if="link.children && link.children.length" class="relative group">
              <button class="px-3 py-1.5 text-[13px] font-semibold text-white/70 hover:text-white uppercase tracking-wide rounded-md hover:bg-white/10 transition-all duration-150 flex items-center gap-1">
                {{ link.label }}
                <svg class="w-3 h-3 opacity-60 group-hover:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
              </button>
              <div class="absolute top-full left-0 mt-1 min-w-[160px] bg-white rounded-xl shadow-xl border border-gray-100 py-1.5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-10">
                <a v-for="child in link.children" :key="child.id" :href="child.url" :target="child.target"
                  class="block px-4 py-2 text-[13px] font-medium text-gray-700 hover:text-[#0D47A1] hover:bg-blue-50 transition-colors">
                  {{ child.label }}
                </a>
              </div>
            </div>
            <a v-else :href="link.url" :target="link.target"
              class="px-3 py-1.5 text-[13px] font-semibold text-white/70 hover:text-white uppercase tracking-wide rounded-md hover:bg-white/10 transition-all duration-150">
              {{ link.label }}
            </a>
          </template>
        </template>
        <template v-else>
          <Link v-for="link in fallbackLinks" :key="link.url" :href="link.url"
            class="px-3 py-1.5 text-[13px] font-semibold text-white/70 hover:text-white uppercase tracking-wide rounded-md hover:bg-white/10 transition-all duration-150">
            {{ link.label }}
          </Link>
        </template>

        <!-- ── More / Categories mega dropdown ── -->
        <div v-if="allCategories.length" ref="catDropRef" class="relative">
          <button
            @click.stop="catOpen = !catOpen"
            :class="['px-3 py-1.5 text-[13px] font-semibold uppercase tracking-wide rounded-md transition-all duration-150 flex items-center gap-1',
              catOpen ? 'bg-white/20 text-white' : 'text-white/70 hover:text-white hover:bg-white/10']"
          >
            <LayoutGrid :size="14" />
            {{ t('nav.more') }}
            <ChevronDown :size="13" :class="['opacity-70 transition-transform duration-200', catOpen ? 'rotate-180' : '']" />
          </button>

          <!-- Mega dropdown -->
          <Transition
            enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="opacity-0 translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition-all duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0 translate-y-1"
          >
            <div v-if="catOpen" class="absolute top-full right-0 mt-2 w-[420px] bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-slate-700 overflow-hidden z-50">

              <!-- Pinned section -->
              <div v-if="pinnedCats.length" class="px-4 pt-4 pb-3">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2.5 flex items-center gap-1.5">
                  <span class="text-amber-400">📌</span> {{ t('categories.pinned') }}
                </p>
                <div class="grid grid-cols-2 gap-1.5">
                  <a v-for="cat in pinnedCats" :key="cat.id" :href="`/category/${cat.slug}`"
                    @click="catOpen = false"
                    class="flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors group">
                    <span class="w-2.5 h-2.5 rounded-full shrink-0 ring-2 ring-white shadow-sm" :style="{ backgroundColor: cat.color || '#0D47A1' }" />
                    <span class="text-sm font-bold text-gray-800 dark:text-slate-200 group-hover:text-primary truncate">{{ cat.name }}</span>
                  </a>
                </div>
              </div>

              <!-- Divider -->
              <div v-if="pinnedCats.length && otherCats.length" class="mx-4 border-t border-gray-100" />

              <!-- All other categories -->
              <div v-if="otherCats.length" class="px-4 pt-3 pb-4">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2.5">{{ t('categories.allCats') }}</p>
                <div class="grid grid-cols-2 gap-1 max-h-52 overflow-y-auto pr-1 custom-scrollbar">
                  <a v-for="cat in otherCats" :key="cat.id" :href="`/category/${cat.slug}`"
                    @click="catOpen = false"
                    class="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors group">
                    <span class="w-2 h-2 rounded-full shrink-0" :style="{ backgroundColor: cat.color || '#0D47A1' }" />
                    <span class="text-sm text-gray-600 dark:text-slate-300 group-hover:text-primary truncate">{{ cat.name }}</span>
                  </a>
                </div>
              </div>

              <!-- Footer -->
              <div class="border-t border-gray-100 dark:border-slate-700 px-4 py-2.5 bg-gray-50 dark:bg-slate-900/50">
                <a href="/categories" @click="catOpen = false"
                  class="flex items-center justify-center gap-1.5 text-xs font-bold text-primary hover:text-primary-dark transition-colors">
                  {{ t('nav.viewAll') }}
                  <ChevronRight :size="12" />
                </a>
              </div>
            </div>
          </Transition>
        </div>

      </div>

      <!-- Right Actions -->
      <div class="flex items-center gap-1">
        <NotificationBell />

        <!-- Day / Night toggle -->
        <button
          @click="toggleTheme"
          class="w-9 h-9 rounded-full flex items-center justify-center text-white/60 hover:text-white hover:bg-white/10 transition-all"
          :aria-label="dark ? 'Switch to day mode' : 'Switch to night mode'"
        >
          <!-- Moon icon — shown in day mode -->
          <svg v-if="!dark" xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z"/>
          </svg>
          <!-- Sun icon — shown in dark mode -->
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="5"/>
            <path stroke-linecap="round" d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
          </svg>
        </button>

        <!-- Language toggle pill -->
        <button
          @click="toggleLang"
          class="hidden sm:flex items-center p-0.5 rounded-full bg-white/10 border border-white/15 hover:border-white/30 transition-all"
          :aria-label="lang === 'bn' ? 'Switch to English' : 'বাংলায় পড়ুন'"
        >
          <span :class="['px-2.5 py-1 rounded-full text-[11px] font-bold transition-all duration-200', lang === 'bn' ? 'bg-white text-primary shadow' : 'text-white/50 hover:text-white/80']">বাং</span>
          <span :class="['px-2.5 py-1 rounded-full text-[11px] font-bold transition-all duration-200', lang === 'en' ? 'bg-white text-primary shadow' : 'text-white/50 hover:text-white/80']">EN</span>
        </button>

        <a href="/search" class="w-9 h-9 rounded-full flex items-center justify-center text-white/60 hover:text-white hover:bg-white/10 transition-all" aria-label="Search">
          <Search class="w-[18px] h-[18px]" />
        </a>
        <a href="/games" class="hidden sm:flex items-center gap-1.5 px-3 py-2 bg-white/10 hover:bg-white/20 text-white text-[11px] font-bold uppercase tracking-wider rounded-full transition-all">
          🎮 Games
        </a>
        <button @click="mobileOpen = !mobileOpen"
          class="lg:hidden w-9 h-9 rounded-full flex items-center justify-center text-white hover:bg-white/10 transition-all">
          <component :is="mobileOpen ? X : Menu" class="w-5 h-5" />
        </button>
      </div>
    </div>

    <!-- Bottom accent line -->
    <!-- <div class="h-[3px] bg-gradient-to-r from-secondary via-secondary-light to-secondary" /> -->

    <!-- ── Category strip (desktop: below nav) ── -->
    <!-- <div v-if="allCategories.length" class="hidden lg:block bg-primary/90 border-t border-white/10">
      <div class="max-w-7xl mx-auto px-6 flex items-center gap-0.5 overflow-x-auto no-scrollbar py-1.5">
        <a href="/" class="shrink-0 px-3 py-1 text-[11px] font-bold text-white/50 hover:text-white hover:bg-white/10 rounded-md transition-colors uppercase tracking-wide">All</a>
        <a v-for="cat in allCategories" :key="cat.id"
          :href="`/category/${cat.slug}`"
          class="shrink-0 flex items-center gap-1.5 px-3 py-1 text-[11px] font-semibold text-white/55 hover:text-white hover:bg-white/10 rounded-md transition-colors uppercase tracking-wide whitespace-nowrap">
          <span class="w-1.5 h-1.5 rounded-full" :style="{ backgroundColor: cat.color || '#EF5350' }" />
          {{ cat.name }}
        </a>
      </div>
    </div> -->

    <!-- Mobile Menu -->
    <Transition
      enter-active-class="transition-all duration-250 ease-out"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div v-show="mobileOpen" class="lg:hidden bg-primary-dark max-h-[80vh] overflow-y-auto">
        <div class="max-w-7xl mx-auto px-4 py-2 space-y-0.5">

          <!-- Main menu items -->
          <template v-if="!menuLoading && menuItems.length">
            <template v-for="link in menuItems" :key="link.id">
              <a :href="link.url" :target="link.target" @click="mobileOpen = false"
                class="flex items-center justify-between px-4 py-3 text-white/70 hover:text-white text-sm font-medium hover:bg-white/5 rounded-lg transition-all">
                {{ link.label }}
                <ChevronRight class="w-4 h-4 text-white/20" />
              </a>
              <a v-for="child in link.children" :key="child.id" :href="child.url" :target="child.target" @click="mobileOpen = false"
                class="flex items-center justify-between pl-8 pr-4 py-2.5 text-white/50 hover:text-white text-sm hover:bg-white/5 rounded-lg transition-all">
                {{ child.label }}
                <ChevronRight class="w-3 h-3 text-white/20" />
              </a>
            </template>
          </template>
          <template v-else>
            <Link v-for="link in fallbackLinks" :key="link.url" :href="link.url" @click="mobileOpen = false"
              class="flex items-center justify-between px-4 py-3 text-white/70 hover:text-white text-sm font-medium hover:bg-white/5 rounded-lg transition-all">
              {{ link.label }}
              <ChevronRight class="w-4 h-4 text-white/20" />
            </Link>
          </template>

          <!-- Categories expandable section -->
          <div v-if="allCategories.length" class="border-t border-white/10 pt-1 mt-1">
            <button @click="mobileCatOpen = !mobileCatOpen"
              class="w-full flex items-center justify-between px-4 py-3 text-white/70 hover:text-white text-sm font-medium hover:bg-white/5 rounded-lg transition-all">
              <span class="flex items-center gap-2">
                <LayoutGrid :size="15" />
                {{ t('nav.categories') }}
              </span>
              <ChevronDown :size="15" :class="['text-white/30 transition-transform', mobileCatOpen ? 'rotate-180' : '']" />
            </button>
            <div v-if="mobileCatOpen" class="grid grid-cols-2 gap-0.5 px-2 pb-2">
              <a v-for="cat in allCategories" :key="cat.id" :href="`/category/${cat.slug}`"
                @click="mobileOpen = false"
                class="flex items-center gap-2 px-3 py-2 rounded-lg text-white/60 hover:text-white hover:bg-white/5 text-sm transition-all">
                <span class="w-2 h-2 rounded-full shrink-0" :style="{ backgroundColor: cat.color || '#EF5350' }" />
                <span class="truncate">{{ cat.name }}</span>
              </a>
            </div>
          </div>

          <div class="flex gap-2 mx-4 mt-3 mb-2">
            <a href="/games" @click="mobileOpen = false"
              class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-white/10 text-white text-sm font-bold rounded-lg">
              🎮 Games
            </a>
          </div>
        </div>
      </div>
    </Transition>
  </nav>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 9999px; }
</style>
