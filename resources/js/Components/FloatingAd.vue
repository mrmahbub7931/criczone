<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAds } from '@/composables/useAds.js'

const { slotFor, fetchPromotions } = useAds()

onMounted(fetchPromotions)

const dismissed = ref(sessionStorage.getItem('cz-float-dismissed') === '1')
const slot = computed(() => slotFor('floating'))

const dismiss = () => {
  dismissed.value = true
  sessionStorage.setItem('cz-float-dismissed', '1')
}
</script>

<template>
  <Transition name="slide-up">
    <div v-if="!dismissed && slot.type"
      class="fixed bottom-4 right-4 z-[150] max-w-[300px] shadow-2xl rounded-xl overflow-hidden">

      <button @click="dismiss"
        class="absolute top-1.5 right-1.5 z-10 w-6 h-6 rounded-full bg-black/50 hover:bg-black/70 flex items-center justify-center text-white transition-colors">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
      </button>

      <a v-if="slot.type === 'promo'"
        :href="slot.promo.link_url || '#'"
        :target="slot.promo.new_tab ? '_blank' : '_self'"
        rel="noopener noreferrer sponsored"
      >
        <img :src="slot.promo.image_url" :alt="slot.promo.title" class="w-full h-auto block" />
      </a>

      <div v-else-if="slot.type === 'adsense'" v-html="slot.code" />
    </div>
  </Transition>
</template>

<style scoped>
.slide-up-enter-active, .slide-up-leave-active { transition: all 0.3s ease; }
.slide-up-enter-from, .slide-up-leave-to { opacity: 0; transform: translateY(20px); }
</style>
