<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAds } from '@/composables/useAds.js'
import { useSettings } from '@/composables/useSettings.js'

const { slotFor, fetchPromotions } = useAds()
const { get: setting } = useSettings()

const visible = ref(false)

onMounted(async () => {
  await fetchPromotions()
  if (sessionStorage.getItem('cz-popup-shown')) return
  const slot = slotFor('popup')
  if (!slot.type) return
  const delay = parseInt(setting('ads', 'ad_popup_delay', '5')) * 1000
  setTimeout(() => { visible.value = true }, delay)
})

const slot = computed(() => slotFor('popup'))

const close = () => {
  visible.value = false
  sessionStorage.setItem('cz-popup-shown', '1')
}
</script>

<template>
  <Transition name="fade">
    <div v-if="visible && slot.type" class="fixed inset-0 z-[200] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="close" />
      <div class="relative max-w-lg w-full">
        <button @click="close"
          class="absolute -top-3 -right-3 z-10 w-8 h-8 rounded-full bg-white shadow-lg flex items-center justify-center text-gray-600 hover:text-gray-900 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <!-- Promo image -->
        <a v-if="slot.type === 'promo'"
          :href="slot.promo.link_url || '#'"
          :target="slot.promo.new_tab ? '_blank' : '_self'"
          rel="noopener noreferrer sponsored"
          @click="close"
        >
          <img :src="slot.promo.image_url" :alt="slot.promo.title" class="w-full h-auto rounded-xl shadow-2xl" />
        </a>

        <!-- AdSense -->
        <div v-else-if="slot.type === 'adsense'" v-html="slot.code" class="bg-white rounded-xl p-4 shadow-2xl" />
      </div>
    </div>
  </Transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
