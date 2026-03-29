<script setup>
import { Flame, Eye, Mail, Send, ChevronRight, Bookmark } from 'lucide-vue-next'
import { TRENDING_TOPICS, MOCK_NEWS } from '@/data.js'
import { ref } from 'vue'

const email = ref('')

const editorPicks = MOCK_NEWS.filter(n => [4, 7, 9].includes(n.id))

const sidebarCategories = [
  { label: 'International', count: 24 },
  { label: 'IPL', count: 18 },
  { label: 'T20', count: 12 },
  { label: 'Test Cricket', count: 9 },
  { label: 'Domestic', count: 7 },
  { label: 'Analysis', count: 15 },
]
</script>

<template>
  <aside class="space-y-5">

    <!-- ===== Trending ===== -->
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
      <div class="px-4 py-3 bg-primary flex items-center gap-2">
        <Flame class="w-4 h-4 text-secondary" />
        <h3 class="font-bold text-white text-xs uppercase tracking-widest">Trending</h3>
      </div>
      <ul>
        <li
          v-for="(topic, i) in TRENDING_TOPICS"
          :key="topic.id"
          class="flex items-center gap-3 px-4 py-3 border-b border-gray-50 last:border-0 hover:bg-gray-50 cursor-pointer transition-colors group"
        >
          <span
            class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-black"
            :class="i < 3 ? 'bg-secondary text-white' : 'bg-gray-100 text-gray-400'"
          >
            {{ i + 1 }}
          </span>
          <p class="text-[13px] font-semibold text-gray-700 group-hover:text-primary transition-colors truncate flex-1">
            {{ topic.title }}
          </p>
          <span class="text-[10px] text-gray-400 flex items-center gap-0.5 flex-shrink-0">
            <Eye class="w-3 h-3" />
            {{ topic.views }}
          </span>
        </li>
      </ul>
    </div>

    <!-- ===== Editor's Picks ===== -->
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
      <div class="px-4 py-3 bg-primary flex items-center gap-2">
        <Bookmark class="w-4 h-4 text-secondary" />
        <h3 class="font-bold text-white text-xs uppercase tracking-widest">Editor's Picks</h3>
      </div>
      <div>
        <div
          v-for="news in editorPicks"
          :key="news.id"
          class="flex items-start gap-3 px-4 py-3 border-b border-gray-50 last:border-0 hover:bg-gray-50 cursor-pointer transition-colors group"
        >
          <div class="w-16 h-12 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
            <img
              :src="news.imageUrl"
              :alt="news.title"
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
            />
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-[13px] font-semibold text-gray-700 leading-snug line-clamp-2 group-hover:text-primary transition-colors">
              {{ news.title }}
            </p>
            <span class="text-[10px] text-gray-400 mt-1 block">{{ news.date }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== Categories ===== -->
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
      <div class="px-4 py-3 border-b border-gray-100">
        <h3 class="font-bold text-gray-900 text-xs uppercase tracking-widest">Categories</h3>
      </div>
      <ul>
        <li
          v-for="cat in sidebarCategories"
          :key="cat.label"
          class="flex items-center gap-3 px-4 py-2.5 border-b border-gray-50 last:border-0 hover:bg-gray-50 cursor-pointer transition-colors group"
        >
          <span class="text-[13px] text-gray-600 group-hover:text-primary transition-colors flex-1 font-medium">{{ cat.label }}</span>
          <span class="text-[10px] text-gray-400 bg-gray-100 group-hover:bg-primary group-hover:text-white px-2 py-0.5 rounded-full font-bold transition-all">{{ cat.count }}</span>
          <ChevronRight class="w-3.5 h-3.5 text-gray-300 group-hover:text-primary transition-colors" />
        </li>
      </ul>
    </div>

    <!-- ===== Newsletter ===== -->
    <div class="rounded-xl overflow-hidden">
      <div class="bg-gradient-to-br from-primary via-primary-dark to-[#051c3a] p-5">
        <div class="flex items-center gap-2 mb-2">
          <Mail class="w-4 h-4 text-secondary" />
          <h3 class="font-bold text-white text-sm">Stay in the game</h3>
        </div>
        <p class="text-white/50 text-xs mb-4 leading-relaxed">
          Cricket updates straight to your inbox, every morning.
        </p>
        <input
          v-model="email"
          type="email"
          placeholder="Your email address"
          class="w-full px-3.5 py-2.5 rounded-lg text-sm text-gray-900 placeholder-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-secondary mb-2.5"
        />
        <button class="w-full flex items-center justify-center gap-1.5 bg-secondary hover:bg-secondary-dark text-white py-2.5 rounded-lg font-bold text-sm transition-colors duration-150">
          <Send class="w-3.5 h-3.5" />
          Subscribe Free
        </button>
      </div>
    </div>

  </aside>
</template>
