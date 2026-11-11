<script setup>
import { computed, onMounted } from 'vue'
import { useAds } from '@/composables/useAds.js'

const props = defineProps({
  position: { type: String, required: true },
})

const { slotFor, fetchPromotions } = useAds()
onMounted(fetchPromotions)

const slot = computed(() => slotFor(props.position))
</script>

<template>
  <div v-if="slot.type" class="ad-slot w-full flex justify-center my-2">

    <!-- Paid Promotion -->
    <a
      v-if="slot.type === 'promo'"
      :href="slot.promo.link_url || '#'"
      :target="slot.promo.new_tab ? '_blank' : '_self'"
      rel="noopener noreferrer sponsored"
      class="block max-w-full"
    >
      <img
        :src="slot.promo.image_url"
        :alt="slot.promo.title"
        class="max-w-full h-auto rounded-lg"
      />
    </a>

    <!-- Google AdSense -->
    <div
      v-else-if="slot.type === 'adsense'"
      class="w-full"
      v-html="slot.code"
    />

  </div>
</template>
