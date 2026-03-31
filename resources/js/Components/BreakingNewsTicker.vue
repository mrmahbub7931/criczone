<script setup>
import { computed } from 'vue'
import { Zap } from 'lucide-vue-next'
import { useSettings } from '@/composables/useSettings.js'

const { get: setting, loaded } = useSettings()

const enabled = computed(() => setting('homepage', 'breaking_news_enabled', '0') === '1')
const text    = computed(() => setting('homepage', 'breaking_news_text', '').trim())

// Only show when enabled AND there is text content
const visible = computed(() => loaded.value && enabled.value && text.value.length > 0)
</script>

<template>
  <Transition
    enter-active-class="transition-all duration-300 ease-out"
    enter-from-class="opacity-0 -translate-y-full"
    enter-to-class="opacity-100 translate-y-0"
    leave-active-class="transition-all duration-200 ease-in"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0 -translate-y-full"
  >
    <div v-if="visible" class="bg-secondary text-white text-xs font-semibold overflow-hidden">
      <div class="max-w-7xl mx-auto flex items-center">

        <!-- Label badge -->
        <div class="flex-shrink-0 flex items-center gap-1.5 bg-red-600 px-3 py-2 z-10">
          <Zap class="w-3 h-3 fill-current" />
          <span class="uppercase tracking-widest text-[10px] font-black whitespace-nowrap">Breaking</span>
        </div>

        <!-- Scrolling text -->
        <div class="flex-1 overflow-hidden relative">
          <div class="ticker-track flex items-center py-2 px-4 whitespace-nowrap">
            <span>{{ text }}</span>
            <!-- Repeat for seamless loop -->
            <span class="mx-16 text-white/30">●</span>
            <span>{{ text }}</span>
            <span class="mx-16 text-white/30">●</span>
          </div>
        </div>

      </div>
    </div>
  </Transition>
</template>

<style scoped>
.ticker-track {
  animation: ticker-scroll 30s linear infinite;
  display: inline-flex;
}

.ticker-track:hover {
  animation-play-state: paused;
}

@keyframes ticker-scroll {
  0%   { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}
</style>
