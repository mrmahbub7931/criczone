<script setup>
import { TrendingUp, Mail, Send, Eye } from 'lucide-vue-next'
import { TRENDING_TOPICS } from '@/data.js'
import { ref } from 'vue'

const email = ref('')
</script>

<template>
  <aside class="space-y-6">

    <!-- Trending Section -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100">
        <TrendingUp class="w-4 h-4 text-secondary" />
        <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">Trending Now</h3>
      </div>
      <ul class="divide-y divide-gray-50">
        <li
          v-for="(topic, i) in TRENDING_TOPICS"
          :key="topic.id"
          class="flex items-start gap-3 px-5 py-3 hover:bg-gray-50 cursor-pointer transition-colors duration-150 group"
        >
          <span
            class="flex-shrink-0 w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold mt-0.5"
            :class="i < 3 ? 'bg-secondary/10 text-secondary' : 'bg-gray-100 text-gray-400'"
          >
            {{ i + 1 }}
          </span>
          <div class="min-w-0">
            <p class="text-sm font-semibold text-gray-800 group-hover:text-primary transition-colors duration-150 leading-snug">
              {{ topic.title }}
            </p>
            <span class="flex items-center gap-1 text-xs text-gray-400 mt-1">
              <Eye class="w-3 h-3" />
              {{ topic.views }} views
            </span>
          </div>
        </li>
      </ul>
    </div>

    <!-- Newsletter Section -->
    <div class="bg-gradient-to-br from-primary to-primary-dark rounded-xl p-5 text-white shadow-lg">
      <div class="flex items-center gap-2 mb-2">
        <Mail class="w-5 h-5 text-secondary-light" />
        <h3 class="font-bold text-sm uppercase tracking-wider">Newsletter</h3>
      </div>
      <p class="text-white/70 text-xs mb-4 leading-relaxed">
        Get the latest cricket updates delivered straight to your inbox. No spam, we promise.
      </p>
      <div class="space-y-2.5">
        <input
          v-model="email"
          type="email"
          placeholder="Enter your email"
          class="w-full px-4 py-2.5 rounded-lg text-sm text-gray-900 placeholder-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-secondary"
        />
        <button class="w-full flex items-center justify-center gap-2 bg-secondary hover:bg-secondary-dark text-white py-2.5 rounded-lg font-bold text-sm transition-colors duration-200 shadow-md">
          <Send class="w-3.5 h-3.5" />
          Subscribe
        </button>
      </div>
    </div>

    <!-- Quick Links -->
    <div class="bg-white rounded-xl shadow-sm p-5">
      <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider mb-3">Quick Links</h3>
      <div class="grid grid-cols-2 gap-2">
        <a
          v-for="link in ['Live Scores', 'Schedule', 'Rankings', 'Stats', 'Photos', 'Videos']"
          :key="link"
          href="#"
          class="text-xs font-medium text-gray-500 hover:text-primary bg-gray-50 hover:bg-primary/5 rounded-lg px-3 py-2 text-center transition-colors duration-150"
        >
          {{ link }}
        </a>
      </div>
    </div>

  </aside>
</template>
