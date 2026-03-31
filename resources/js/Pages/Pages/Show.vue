<template>
  <div class="min-h-screen bg-gray-50">

    <!-- Navbar slot — reuse existing Navbar -->
    <Navbar />

    <main class="max-w-3xl mx-auto px-4 py-12">

      <!-- Loading -->
      <div v-if="loading" class="space-y-4 animate-pulse">
        <div class="h-8 bg-gray-200 rounded w-2/3" />
        <div class="h-4 bg-gray-200 rounded w-1/3" />
        <div class="space-y-2 mt-8">
          <div v-for="i in 8" :key="i" class="h-4 bg-gray-200 rounded" :class="i % 4 === 0 ? 'w-3/5' : 'w-full'" />
        </div>
      </div>

      <!-- 404 -->
      <div v-else-if="notFound" class="text-center py-24">
        <p class="text-6xl font-black text-gray-200 mb-4">404</p>
        <h1 class="text-xl font-bold text-gray-700 mb-2">Page Not Found</h1>
        <p class="text-sm text-gray-500 mb-6">The page you're looking for doesn't exist or has been removed.</p>
        <a href="/" class="inline-flex items-center gap-2 bg-[#0D47A1] text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:bg-[#0a2f6e] transition-colors">
          ← Back to Home
        </a>
      </div>

      <!-- Content -->
      <article v-else-if="page">
        <header class="mb-8">
          <h1 class="text-3xl font-black text-gray-900 leading-tight">{{ page.title }}</h1>
          <p class="text-xs text-gray-400 mt-2">
            Last updated {{ fmtDate(page.updated_at) }}
          </p>
        </header>

        <div
          class="prose prose-sm sm:prose max-w-none text-gray-700
                 prose-headings:font-bold prose-headings:text-gray-900
                 prose-a:text-[#0D47A1] prose-a:no-underline hover:prose-a:underline
                 prose-blockquote:border-[#0D47A1] prose-blockquote:text-gray-500
                 prose-code:bg-gray-100 prose-code:px-1 prose-code:rounded
                 prose-img:rounded-xl"
          v-html="page.content"
        />
      </article>

    </main>

    <!-- Footer slot -->
    <Footer />

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { usePage as useInertiaPage, Head } from '@inertiajs/vue3'
import axios from 'axios'
import Navbar from '@/Components/Navbar.vue'
import Footer from '@/Components/Footer.vue'

const props   = defineProps({ slug: { type: String, required: true } })
const loading = ref(true)
const notFound = ref(false)
const page    = ref(null)

const fmtDate = (d) => d
  ? new Date(d).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })
  : ''

onMounted(async () => {
  try {
    const { data } = await axios.get(`/api/pages/${props.slug}`)
    page.value = data
  } catch (err) {
    notFound.value = true
  } finally {
    loading.value = false
  }
})
</script>
