<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
const logo = `${window.location.origin}/images/criczone.jpg`
</script>
<template>
  <footer class="bg-[#0D47A1] text-white pt-10 pb-6">
    <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-4 gap-8">

      <div>
        <Link href="/" class="flex items-center space-x-2">
        <img :src="logo" class="h-10 w-auto" />
      </Link>
        <p class="text-gray-400 text-sm mt-3">
          Cricket news & analysis platform.
        </p>
      </div>

      <div>
        <h3 class="font-bold mb-3">Links</h3>
        <ul class="space-y-2 text-gray-400 text-sm">
          <li>About</li>
          <li>Contact</li>
          <li>Privacy</li>
        </ul>
      </div>

      <div>
        <h3 class="font-bold mb-3">Categories</h3>
        <ul class="space-y-2 text-gray-400 text-sm">
          <li>IPL</li>
          <li>International</li>
          <li>Test</li>
        </ul>
      </div>

      <div>
        <h3 class="font-bold mb-3">Newsletter</h3>
        <input type="email" placeholder="Email" class="w-full p-2 rounded text-black" />
        <button class="mt-2 w-full bg-accent py-2 rounded font-bold">
          Subscribe
        </button>
      </div>

    </div>

    <div class="text-center text-gray-500 text-xs mt-10">
      © 2026 CricPulse
    </div>
  </footer>
</template>