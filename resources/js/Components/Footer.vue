<script setup>
import { Link } from '@inertiajs/vue3'
import { Send, Facebook, Twitter, Instagram, Youtube } from 'lucide-vue-next'
import { ref } from 'vue'

const logo = `${window.location.origin}/images/criczone.png`
const email = ref('')

const links = {
  explore: [
    { label: 'Latest News', href: '/category/news' },
    { label: 'Live Scores', href: '#' },
    { label: 'Match Schedule', href: '/category/matches' },
    { label: 'Player Rankings', href: '/category/rankings' },
    { label: 'Photo Gallery', href: '#' },
  ],
  categories: [
    { label: 'International', href: '#' },
    { label: 'IPL 2026', href: '#' },
    { label: 'T20 World Cup', href: '#' },
    { label: 'Test Cricket', href: '#' },
    { label: 'Domestic', href: '#' },
  ],
}

const socials = [
  { icon: Facebook, href: '#' },
  { icon: Twitter, href: '#' },
  { icon: Instagram, href: '#' },
  { icon: Youtube, href: '#' },
]
</script>

<template>
  <!-- Top border -->
  <div class="h-1 bg-gradient-to-r from-primary via-secondary to-primary" />

  <footer class="bg-primary text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-14">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-10">

        <!-- Brand -->
        <div class="col-span-2 md:col-span-1">
          <Link href="/" class="inline-block mb-5">
            <img :src="logo" alt="CricZone" class="h-12 w-auto" />
          </Link>
          <p class="text-white/40 text-sm leading-relaxed mb-5 max-w-[260px]">
            Your trusted source for cricket news, live scores, and expert analysis from around the world.
          </p>
          <div class="flex gap-2">
            <a
              v-for="s in socials"
              :key="s.href"
              :href="s.href"
              class="w-8 h-8 rounded-full bg-white/10 hover:bg-secondary flex items-center justify-center text-white/50 hover:text-white transition-all duration-200"
            >
              <component :is="s.icon" class="w-3.5 h-3.5" />
            </a>
          </div>
        </div>

        <!-- Explore -->
        <div>
          <h4 class="text-[11px] font-bold uppercase tracking-[.15em] text-white/50 mb-4">Explore</h4>
          <ul class="space-y-2.5">
            <li v-for="l in links.explore" :key="l.label">
              <a :href="l.href" class="text-sm text-white/40 hover:text-secondary transition-colors">{{ l.label }}</a>
            </li>
          </ul>
        </div>

        <!-- Categories -->
        <div>
          <h4 class="text-[11px] font-bold uppercase tracking-[.15em] text-white/50 mb-4">Categories</h4>
          <ul class="space-y-2.5">
            <li v-for="c in links.categories" :key="c.label">
              <a :href="c.href" class="text-sm text-white/40 hover:text-secondary transition-colors">{{ c.label }}</a>
            </li>
          </ul>
        </div>

        <!-- Newsletter -->
        <div>
          <h4 class="text-[11px] font-bold uppercase tracking-[.15em] text-white/50 mb-4">Newsletter</h4>
          <p class="text-white/40 text-sm mb-4">Daily cricket updates in your inbox.</p>
          <div class="flex">
            <input
              v-model="email"
              type="email"
              placeholder="you@email.com"
              class="flex-1 min-w-0 px-3 py-2.5 bg-white/10 border border-white/10 rounded-l-lg text-sm text-white placeholder-white/30 focus:outline-none focus:ring-1 focus:ring-secondary focus:bg-white/15"
            />
            <button class="px-4 bg-secondary hover:bg-secondary-dark rounded-r-lg transition-colors">
              <Send class="w-4 h-4" />
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- Bottom -->
    <div class="border-t border-white/10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px] text-white/25">
        <span>&copy; 2026 CricZone. All rights reserved.</span>
        <div class="flex gap-4">
          <a href="#" class="hover:text-white/50 transition-colors">Privacy</a>
          <a href="#" class="hover:text-white/50 transition-colors">Terms</a>
          <a href="#" class="hover:text-white/50 transition-colors">Contact</a>
        </div>
      </div>
    </div>
  </footer>
</template>
