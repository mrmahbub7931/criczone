<script setup>
import { Link } from '@inertiajs/vue3'
import { Mail, MapPin, ExternalLink, Send, Facebook, Twitter, Instagram, Youtube } from 'lucide-vue-next'
import { ref } from 'vue'

const logo = `${window.location.origin}/images/criczone.jpg`
const email = ref('')

const quickLinks = [
  { label: 'Home', href: '/' },
  { label: 'Latest News', href: '/category/news' },
  { label: 'Live Scores', href: '#' },
  { label: 'Match Schedule', href: '/category/matches' },
  { label: 'Player Rankings', href: '/category/rankings' },
  { label: 'Photo Gallery', href: '#' },
]

const categories = [
  { label: 'International', href: '#' },
  { label: 'IPL 2026', href: '#' },
  { label: 'T20 World Cup', href: '#' },
  { label: 'Test Cricket', href: '#' },
  { label: 'Domestic', href: '#' },
  { label: 'Analysis & Opinion', href: '#' },
]

const socialLinks = [
  { icon: Facebook, href: '#', label: 'Facebook' },
  { icon: Twitter, href: '#', label: 'Twitter' },
  { icon: Instagram, href: '#', label: 'Instagram' },
  { icon: Youtube, href: '#', label: 'YouTube' },
]
</script>

<template>
  <footer class="bg-gray-900 text-white">

    <!-- Main Footer -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-14 pb-10">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">

        <!-- Brand Column -->
        <div class="sm:col-span-2 lg:col-span-1">
          <Link href="/" class="inline-flex items-center gap-2 mb-4">
            <img :src="logo" alt="CricZone" class="h-9 w-auto rounded-full ring-2 ring-white/10" />
            <span class="text-lg font-extrabold tracking-tight">Cric<span class="text-secondary">Zone</span></span>
          </Link>
          <p class="text-gray-400 text-sm leading-relaxed mb-6 max-w-xs">
            Your trusted source for cricket news, live scores, expert analysis, and comprehensive coverage from around the world.
          </p>
          <div class="space-y-2 text-sm text-gray-500">
            <div class="flex items-center gap-2">
              <Mail class="w-3.5 h-3.5 text-secondary/50" />
              <span>contact@criczone.com</span>
            </div>
            <div class="flex items-center gap-2">
              <MapPin class="w-3.5 h-3.5 text-secondary/50" />
              <span>Mumbai, India</span>
            </div>
          </div>
        </div>

        <!-- Quick Links -->
        <div>
          <h4 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">Quick Links</h4>
          <ul class="space-y-2.5">
            <li v-for="link in quickLinks" :key="link.label">
              <a
                :href="link.href"
                class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-secondary transition-colors duration-150"
              >
                <ExternalLink class="w-3 h-3 opacity-0 group-hover:opacity-100" />
                {{ link.label }}
              </a>
            </li>
          </ul>
        </div>

        <!-- Categories -->
        <div>
          <h4 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">Categories</h4>
          <ul class="space-y-2.5">
            <li v-for="cat in categories" :key="cat.label">
              <a
                :href="cat.href"
                class="text-sm text-gray-500 hover:text-secondary transition-colors duration-150"
              >
                {{ cat.label }}
              </a>
            </li>
          </ul>
        </div>

        <!-- Newsletter -->
        <div>
          <h4 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">Newsletter</h4>
          <p class="text-gray-500 text-sm mb-4 leading-relaxed">
            Get daily cricket updates delivered to your inbox. No spam ever.
          </p>
          <div class="flex gap-2">
            <input
              v-model="email"
              type="email"
              placeholder="you@email.com"
              class="flex-1 min-w-0 px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-secondary/50 focus:border-transparent"
            />
            <button class="flex-shrink-0 flex items-center justify-center w-10 h-10 bg-secondary hover:bg-secondary-dark rounded-lg transition-colors duration-150">
              <Send class="w-4 h-4 text-white" />
            </button>
          </div>

          <!-- Social Links -->
          <div class="mt-6">
            <h4 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Follow Us</h4>
            <div class="flex items-center gap-2">
              <a
                v-for="social in socialLinks"
                :key="social.label"
                :href="social.href"
                :aria-label="social.label"
                class="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-800 hover:bg-secondary/20 text-gray-500 hover:text-secondary transition-all duration-150"
              >
                <component :is="social.icon" class="w-4 h-4" />
              </a>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Bottom Bar -->
    <div class="border-t border-gray-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-gray-600 text-xs">
          &copy; 2026 CricZone. All rights reserved.
        </p>
        <div class="flex items-center gap-5 text-xs text-gray-600">
          <a href="#" class="hover:text-gray-400 transition-colors">Privacy Policy</a>
          <a href="#" class="hover:text-gray-400 transition-colors">Terms</a>
          <a href="#" class="hover:text-gray-400 transition-colors">Cookie Policy</a>
          <a href="#" class="hover:text-gray-400 transition-colors">Sitemap</a>
        </div>
      </div>
    </div>
  </footer>
</template>
