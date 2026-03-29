<script setup>
import { Link } from '@inertiajs/vue3'
import { Send, Facebook, Twitter, Instagram, Youtube } from 'lucide-vue-next'
import { ref } from 'vue'

const logo = `${window.location.origin}/images/criczone.png`
const email = ref('')

const quickLinks = [
  { label: 'Home', href: '/' },
  { label: 'Latest News', href: '/category/news' },
  { label: 'Live Scores', href: '#' },
  { label: 'Match Schedule', href: '/category/matches' },
  { label: 'Player Rankings', href: '/category/rankings' },
]

const categories = [
  { label: 'International', href: '#' },
  { label: 'IPL 2026', href: '#' },
  { label: 'T20 World Cup', href: '#' },
  { label: 'Test Cricket', href: '#' },
  { label: 'Domestic', href: '#' },
  { label: 'Analysis', href: '#' },
]

const socialLinks = [
  { icon: Facebook, href: '#', label: 'Facebook' },
  { icon: Twitter, href: '#', label: 'Twitter' },
  { icon: Instagram, href: '#', label: 'Instagram' },
  { icon: Youtube, href: '#', label: 'YouTube' },
]
</script>

<template>
  <footer class="bg-primary text-white">

    <!-- Top accent -->
    <div class="h-1 bg-secondary" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-12 pb-8">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-10">

        <!-- Brand -->
        <div class="lg:col-span-4">
          <Link href="/" class="inline-block mb-5">
            <img :src="logo" alt="CricZone" class="h-14 w-auto drop-shadow-lg" />
          </Link>
          <p class="text-white/50 text-sm leading-relaxed mb-6 max-w-xs">
            Your trusted source for cricket news, live scores, expert analysis, and comprehensive match coverage worldwide.
          </p>
          <div class="flex items-center gap-2">
            <a
              v-for="social in socialLinks"
              :key="social.label"
              :href="social.href"
              :aria-label="social.label"
              class="w-9 h-9 rounded-full bg-white/10 hover:bg-secondary flex items-center justify-center text-white/60 hover:text-white transition-all duration-200"
            >
              <component :is="social.icon" class="w-4 h-4" />
            </a>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="lg:col-span-2">
          <h4 class="text-sm font-bold uppercase tracking-widest mb-4 text-white/80">Quick Links</h4>
          <ul class="space-y-2.5">
            <li v-for="link in quickLinks" :key="link.label">
              <a :href="link.href" class="text-sm text-white/40 hover:text-secondary transition-colors duration-150">
                {{ link.label }}
              </a>
            </li>
          </ul>
        </div>

        <!-- Categories -->
        <div class="lg:col-span-2">
          <h4 class="text-sm font-bold uppercase tracking-widest mb-4 text-white/80">Categories</h4>
          <ul class="space-y-2.5">
            <li v-for="cat in categories" :key="cat.label">
              <a :href="cat.href" class="text-sm text-white/40 hover:text-secondary transition-colors duration-150">
                {{ cat.label }}
              </a>
            </li>
          </ul>
        </div>

        <!-- Newsletter -->
        <div class="lg:col-span-4">
          <h4 class="text-sm font-bold uppercase tracking-widest mb-4 text-white/80">Newsletter</h4>
          <p class="text-white/40 text-sm mb-4 leading-relaxed">
            Get daily cricket updates delivered to your inbox.
          </p>
          <div class="flex gap-2">
            <input
              v-model="email"
              type="email"
              placeholder="Enter your email"
              class="flex-1 min-w-0 px-4 py-2.5 bg-white/10 border border-white/10 rounded-lg text-sm text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-secondary/50 focus:bg-white/15"
            />
            <button class="flex-shrink-0 px-5 py-2.5 bg-secondary hover:bg-secondary-dark rounded-lg font-bold text-sm transition-colors duration-200 flex items-center gap-1.5">
              <Send class="w-3.5 h-3.5" />
              Join
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- Bottom -->
    <div class="border-t border-white/10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-white/30">
        <span>&copy; 2026 CricZone. All rights reserved.</span>
        <div class="flex items-center gap-5">
          <a href="#" class="hover:text-white/60 transition-colors">Privacy</a>
          <a href="#" class="hover:text-white/60 transition-colors">Terms</a>
          <a href="#" class="hover:text-white/60 transition-colors">Contact</a>
        </div>
      </div>
    </div>
  </footer>
</template>
