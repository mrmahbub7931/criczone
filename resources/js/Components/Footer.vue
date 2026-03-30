<script setup>
import { Link } from '@inertiajs/vue3'
import { Mail, Send, MapPin, Phone, ArrowRight } from 'lucide-vue-next'
import { ref } from 'vue'

const logo = `${window.location.origin}/images/criczone.png`
const email = ref('')

const quickLinks = [
  { label: 'Home', href: '/' },
  { label: 'Latest News', href: '/category/news' },
  { label: 'Live Scores', href: '#' },
  { label: 'Match Schedule', href: '/category/matches' },
  { label: 'Player Rankings', href: '/category/players' },
]

const categories = [
  { label: 'International', href: '#' },
  { label: 'IPL', href: '#' },
  { label: 'T20 World Cup', href: '#' },
  { label: 'Test Cricket', href: '#' },
  { label: 'Domestic', href: '#' },
]
</script>

<template>
  <footer class="bg-gradient-to-b from-primary-dark to-[#061f4a] text-white">
    <!-- Main Footer -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-12 pb-8">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">

        <!-- Brand -->
        <div>
          <Link href="/" class="flex items-center gap-2.5 mb-4">
            <img :src="logo" alt="CricZone" class="h-10 w-auto" />
            <!-- <span class="text-xl font-bold">Cric<span class="text-secondary">Zone</span></span> -->
          </Link>
          <p class="text-white/50 text-sm leading-relaxed mb-5">
            Your premier destination for cricket news, live scores, match analysis, and everything cricket.
          </p>
          <div class="space-y-2.5 text-sm text-white/40">
            <div class="flex items-center gap-2">
              <Mail class="w-4 h-4 text-secondary/60" />
              <span>contact@criczone.com</span>
            </div>
            <div class="flex items-center gap-2">
              <MapPin class="w-4 h-4 text-secondary/60" />
              <span>Mumbai, India</span>
            </div>
          </div>
        </div>

        <!-- Quick Links -->
        <div>
          <h3 class="font-bold text-sm uppercase tracking-wider mb-4">Quick Links</h3>
          <ul class="space-y-2.5">
            <li v-for="link in quickLinks" :key="link.label">
              <a :href="link.href" class="flex items-center gap-2 text-sm text-white/50 hover:text-secondary transition-colors duration-150 group">
                <ArrowRight class="w-3 h-3 text-secondary/0 group-hover:text-secondary transition-colors duration-150" />
                {{ link.label }}
              </a>
            </li>
          </ul>
        </div>

        <!-- Categories -->
        <div>
          <h3 class="font-bold text-sm uppercase tracking-wider mb-4">Categories</h3>
          <ul class="space-y-2.5">
            <li v-for="cat in categories" :key="cat.label">
              <a :href="cat.href" class="flex items-center gap-2 text-sm text-white/50 hover:text-secondary transition-colors duration-150 group">
                <ArrowRight class="w-3 h-3 text-secondary/0 group-hover:text-secondary transition-colors duration-150" />
                {{ cat.label }}
              </a>
            </li>
          </ul>
        </div>

        <!-- Newsletter -->
        <div>
          <h3 class="font-bold text-sm uppercase tracking-wider mb-4">Stay Updated</h3>
          <p class="text-white/50 text-sm mb-4 leading-relaxed">
            Subscribe to our newsletter for daily cricket updates and exclusive content.
          </p>
          <div class="space-y-2.5">
            <input
              v-model="email"
              type="email"
              placeholder="Your email address"
              class="w-full px-4 py-2.5 rounded-lg text-sm text-gray-900 placeholder-gray-400 bg-white/90 focus:bg-white focus:outline-none focus:ring-2 focus:ring-secondary transition-all duration-200"
            />
            <button class="w-full flex items-center justify-center gap-2 bg-secondary hover:bg-secondary-dark text-white py-2.5 rounded-lg font-bold text-sm transition-colors duration-200">
              <Send class="w-3.5 h-3.5" />
              Subscribe
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- Bottom Bar -->
    <div class="border-t border-white/10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 py-5 flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-white/30 text-xs">
          &copy; 2026 CricZone. All rights reserved.
        </p>
        <div class="flex items-center gap-6 text-xs text-white/30">
          <a href="#" class="hover:text-white/60 transition-colors duration-150">Privacy Policy</a>
          <a href="#" class="hover:text-white/60 transition-colors duration-150">Terms of Service</a>
          <a href="#" class="hover:text-white/60 transition-colors duration-150">Cookie Policy</a>
        </div>
      </div>
    </div>
  </footer>
</template>
