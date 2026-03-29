<template>
  <div class="bg-white rounded-xl overflow-hidden shadow cursor-pointer hover:translate-y-[-2px] transition">

    <div v-if="variant === 'featured'" class="relative h-[400px]">
      <img :src="news.imageUrl" class="w-full h-full object-cover" />
      <div class="absolute bottom-0 p-6 text-white">
        <h2 class="text-2xl font-black">{{ news.title }}</h2>
      </div>
    </div>

    <div v-else class="p-4">
      <img :src="news.imageUrl" class="h-40 w-full object-cover rounded" />
      <h3 class="font-bold mt-2">{{ news.title }}</h3>
      <p class="text-sm text-gray-500">{{ news.excerpt }}</p>
    </div>

  </div>
</template>

<script setup>
defineProps({
  news: Object,
  variant: {
    type: String,
    default: 'vertical'
  }
})
</script>