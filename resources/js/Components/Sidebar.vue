<script setup>
import { TrendingUp, Eye, Mail, Send, ChevronRight, Tag } from 'lucide-vue-next'
import { TRENDING_TOPICS, MOCK_NEWS } from '@/data.js'
import { ref } from 'vue'

const email = ref('')

const sidebarCategories = [
  { label: 'International', count: 24, color: 'bg-blue-500' },
  { label: 'IPL', count: 18, color: 'bg-orange-500' },
  { label: 'T20', count: 12, color: 'bg-green-500' },
  { label: 'Test Cricket', count: 9, color: 'bg-purple-500' },
  { label: 'Domestic', count: 7, color: 'bg-yellow-500' },
  { label: 'Analysis', count: 15, color: 'bg-red-500' },
]

const editorPicks = MOCK_NEWS.filter(n => [3, 5, 7].includes(n.id))
</script>

<template>
  <aside class="space-y-6">

    <!-- Trending Section -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div class="flex items-center gap-2 px-4 py-3.5 bg-gray-50 border-b border-gray-100">
        <TrendingUp class="w-4 h-4 text-secondary" />
        <h3 class="font-bold text-gray-900 text-xs uppercase tracking-widest">Trending Now</h3>
      </div>
      <ul class="divide-y divide-gray-50">
        <li
          v-for="(topic, i) in TRENDING_TOPICS"
          :key="topic.id"
          class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer transition-colors duration-100 group"
        >
          <span
            class="flex-shrink-0 w-6 h-6 rounded flex items-center justify-center text-[10px] font-bold"
            :class="i < 3 ? 'bg-secondary text-white' : 'bg-gray-100 text-gray-400'"
          >
            {{ i + 1 }}
          </span>
          <div class="min-w-0 flex-1">
            <p class="text-sm font-medium text-gray-700 group-hover:text-primary transition-colors duration-100 truncate">
              {{ topic.title }}
            </p>
          </div>
          <span class="flex items-center gap-0.5 text-[10px] text-gray-400 flex-shrink-0">
            <Eye class="w-3 h-3" />
            {{ topic.views }}
          </span>
        </li>
      </ul>
    </div>

    <!-- Categories -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div class="flex items-center gap-2 px-4 py-3.5 bg-gray-50 border-b border-gray-100">
        <Tag class="w-4 h-4 text-primary" />
        <h3 class="font-bold text-gray-900 text-xs uppercase tracking-widest">Categories</h3>
      </div>
      <ul class="divide-y divide-gray-50">
        <li
          v-for="cat in sidebarCategories"
          :key="cat.label"
          class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 cursor-pointer transition-colors duration-100 group"
        >
          <span :class="[cat.color, 'w-2 h-2 rounded-full flex-shrink-0']" />
          <span class="text-sm text-gray-600 group-hover:text-primary transition-colors flex-1">{{ cat.label }}</span>
          <span class="text-[10px] text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded-full font-medium">{{ cat.count }}</span>
          <ChevronRight class="w-3 h-3 text-gray-300 group-hover:text-primary transition-colors" />
        </li>
      </ul>
    </div>

    <!-- Editor's Picks -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div class="px-4 py-3.5 bg-gray-50 border-b border-gray-100">
        <h3 class="font-bold text-gray-900 text-xs uppercase tracking-widest">Editor's Picks</h3>
      </div>
      <div class="divide-y divide-gray-50">
        <div
          v-for="news in editorPicks"
          :key="news.id"
          class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer transition-colors duration-100 group"
        >
          <div class="w-14 h-14 rounded-lg overflow-hidden flex-shrink-0">
            <img
              :src="news.imageUrl"
              :alt="news.title"
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
            />
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-sm font-medium text-gray-700 leading-snug line-clamp-2 group-hover:text-primary transition-colors duration-100">
              {{ news.title }}
            </p>
            <span class="text-[10px] text-gray-400 mt-1 block">{{ news.readTime }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Newsletter -->
    <div class="bg-primary rounded-xl p-5 text-white shadow-sm">
      <div class="flex items-center gap-2 mb-1.5">
        <Mail class="w-4 h-4 text-secondary" />
        <h3 class="font-bold text-sm">Stay Updated</h3>
      </div>
      <p class="text-white/60 text-xs mb-4 leading-relaxed">
        Get the latest cricket updates delivered to your inbox every morning.
      </p>
      <input
        v-model="email"
        type="email"
        placeholder="Your email"
        class="w-full px-3 py-2 rounded-lg text-sm text-gray-900 placeholder-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-secondary mb-2"
      />
      <button class="w-full flex items-center justify-center gap-1.5 bg-secondary hover:bg-secondary-dark text-white py-2 rounded-lg font-bold text-sm transition-colors duration-150">
        <Send class="w-3.5 h-3.5" />
        Subscribe
      </button>
    </div>

  </aside>
</template>
