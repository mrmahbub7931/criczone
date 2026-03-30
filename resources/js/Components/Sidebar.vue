<script setup>
import { Flame, Eye, Bookmark, Mail, Send, ChevronRight } from 'lucide-vue-next'
import { TRENDING, MOCK_NEWS } from '@/data.js'
import { ref } from 'vue'

const email = ref('')
const picks = MOCK_NEWS.filter(n => [4, 7, 9].includes(n.id))

const cats = [
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

    <!-- ── Trending ── -->
    <section class="bg-surface rounded-xl border border-gray-100 overflow-hidden">
      <header class="px-4 py-3 bg-primary flex items-center gap-2">
        <Flame class="w-4 h-4 text-secondary" />
        <h3 class="text-[11px] font-bold text-white uppercase tracking-widest">Trending</h3>
      </header>
      <ol class="divide-y divide-gray-50">
        <li
          v-for="(t, i) in TRENDING"
          :key="t.id"
          class="flex items-center gap-3 px-4 py-3 hover:bg-surface-alt cursor-pointer transition-colors group"
        >
          <span
            class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black flex-shrink-0"
            :class="i < 3 ? 'bg-secondary text-white' : 'bg-gray-100 text-gray-400'"
          >{{ i + 1 }}</span>
          <span class="text-[13px] font-medium text-gray-700 group-hover:text-primary transition-colors truncate flex-1">{{ t.title }}</span>
          <span class="text-[10px] text-gray-400 flex items-center gap-0.5 flex-shrink-0"><Eye class="w-3 h-3" />{{ t.count }}</span>
        </li>
      </ol>
    </section>

    <!-- ── Editor Picks ── -->
    <section class="bg-surface rounded-xl border border-gray-100 overflow-hidden">
      <header class="px-4 py-3 bg-primary flex items-center gap-2">
        <Bookmark class="w-4 h-4 text-secondary" />
        <h3 class="text-[11px] font-bold text-white uppercase tracking-widest">Editor's Picks</h3>
      </header>
      <div class="divide-y divide-gray-50">
        <article
          v-for="p in picks"
          :key="p.id"
          class="flex items-start gap-3 px-4 py-3 hover:bg-surface-alt cursor-pointer transition-colors group"
        >
          <div class="w-16 h-12 rounded-lg overflow-hidden flex-shrink-0">
            <img :src="p.imageUrl" :alt="p.title" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" />
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-[13px] font-semibold text-gray-700 leading-snug line-clamp-2 group-hover:text-primary transition-colors">{{ p.title }}</p>
            <span class="text-[10px] text-gray-400 mt-0.5 block">{{ p.date }}</span>
          </div>
        </article>
      </div>
    </section>

    <!-- ── Categories ── -->
    <section class="bg-surface rounded-xl border border-gray-100 overflow-hidden">
      <header class="px-4 py-3 border-b border-gray-100">
        <h3 class="text-[11px] font-bold text-gray-900 uppercase tracking-widest">Categories</h3>
      </header>
      <ul class="divide-y divide-gray-50">
        <li
          v-for="c in cats"
          :key="c.label"
          class="flex items-center gap-3 px-4 py-2.5 hover:bg-surface-alt cursor-pointer transition-colors group"
        >
          <span class="text-[13px] text-gray-600 group-hover:text-primary transition-colors flex-1 font-medium">{{ c.label }}</span>
          <span class="text-[10px] text-gray-400 bg-gray-100 group-hover:bg-primary group-hover:text-white px-2 py-0.5 rounded-full font-bold transition-all">{{ c.count }}</span>
          <ChevronRight class="w-3.5 h-3.5 text-gray-300 group-hover:text-primary transition-colors" />
        </li>
      </ul>
    </section>

    <!-- ── Newsletter ── -->
    <section class="rounded-xl overflow-hidden bg-gradient-to-br from-primary via-primary-dark to-surface-dark p-5">
      <div class="flex items-center gap-2 mb-2">
        <Mail class="w-4 h-4 text-secondary" />
        <h3 class="font-bold text-white text-sm">Stay in the game</h3>
      </div>
      <p class="text-white/50 text-xs mb-4 leading-relaxed">Cricket updates every morning. No spam.</p>
      <input
        v-model="email"
        type="email"
        placeholder="your@email.com"
        class="w-full px-3.5 py-2.5 rounded-lg text-sm text-gray-900 placeholder-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-secondary mb-2.5"
      />
      <button class="w-full flex items-center justify-center gap-1.5 bg-secondary hover:bg-secondary-dark text-white py-2.5 rounded-lg font-bold text-sm transition-colors">
        <Send class="w-3.5 h-3.5" />
        Subscribe Free
      </button>
    </section>

  </aside>
</template>
