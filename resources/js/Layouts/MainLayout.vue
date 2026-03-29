<template>
  <div class="min-h-screen flex flex-col">
    <Header />

    <main class="flex-grow bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 py-8">
        <slot />
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import Header from '@/components/Header.vue'
import Footer from '@/components/Footer.vue'
</script>