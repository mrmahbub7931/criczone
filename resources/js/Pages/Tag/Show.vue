<template>
  <Head v-if="tag">
    <title>{{ tag.name }} – CricZone</title>
    <meta name="description" :content="`All articles tagged with ${tag.name} on CricZone`" />
    <meta property="og:title" :content="`${tag.name} – CricZone`" />
  </Head>

  <MainLayout>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">

      <!-- Skeleton -->
      <div v-if="loading" class="animate-pulse space-y-6">
        <div class="h-8 w-48 bg-gray-200 rounded-lg" />
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
          <div v-for="i in 6" :key="i" class="h-64 bg-gray-100 rounded-2xl" />
        </div>
      </div>

      <!-- Not found -->
      <div v-else-if="notFound" class="text-center py-24">
        <p class="text-6xl font-black text-gray-200 mb-4">404</p>
        <p class="text-xl font-bold text-gray-700 mb-6">Tag not found</p>
        <a href="/" class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary text-white text-sm font-semibold rounded-full hover:bg-primary-dark transition-colors">← Home</a>
      </div>

      <!-- Content -->
      <template v-else-if="tag">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center gap-2 text-xs text-gray-400 mb-4">
            <a href="/" class="hover:text-primary transition-colors">Home</a>
            <span>/</span>
            <span class="text-gray-600">{{ tag.name }}</span>
          </div>
          <div class="flex items-center gap-3 flex-wrap">
            <span class="inline-flex items-center gap-1.5 bg-primary/10 text-primary text-sm font-bold px-4 py-1.5 rounded-full">
              <Tag :size="14" />
              {{ tag.name }}
            </span>
            <span class="text-sm text-gray-400">{{ articles.total }} article{{ articles.total === 1 ? '' : 's' }}</span>
          </div>
        </div>

        <!-- Grid -->
        <div v-if="articles.data?.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
          <a
            v-for="a in articles.data"
            :key="a.id"
            :href="`/article/${a.slug}`"
            class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5"
          >
            <div class="h-44 overflow-hidden bg-gray-100">
              <img
                v-if="a.featured_image"
                :src="a.featured_image"
                :alt="a.title"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              />
              <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary/10 to-secondary/10">
                <span class="text-3xl">🏏</span>
              </div>
            </div>
            <div class="p-4">
              <span
                v-if="a.category"
                class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider text-white"
                :style="{ backgroundColor: a.category.color || '#0D47A1' }"
              >{{ a.category.name }}</span>
              <h3 class="mt-2 text-sm font-bold text-gray-800 group-hover:text-primary transition-colors leading-snug line-clamp-2">{{ a.title }}</h3>
              <p class="mt-1.5 text-xs text-gray-400">{{ formatDate(a.published_at) }}</p>
            </div>
          </a>
        </div>
        <p v-else class="text-center text-gray-400 py-16 text-sm">No published articles for this tag yet.</p>

        <!-- Pagination -->
        <div v-if="articles.last_page > 1" class="flex items-center justify-center gap-2 mt-10">
          <button
            @click="page--"
            :disabled="page === 1"
            class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-40 transition-colors"
          >Prev</button>
          <span class="text-sm text-gray-400">{{ page }} / {{ articles.last_page }}</span>
          <button
            @click="page++"
            :disabled="page === articles.last_page"
            class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-40 transition-colors"
          >Next</button>
        </div>
      </template>

    </div>
  </MainLayout>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import axios from 'axios'
import { Head } from '@inertiajs/vue3'
import { Tag } from 'lucide-vue-next'
import MainLayout from '@/Layouts/MainLayout.vue'
import { useLocale } from '@/composables/useLocale.js'

const props = defineProps({ slug: { type: String, required: true } })

const { locale } = useLocale()

const tag      = ref(null)
const articles = ref({})
const loading  = ref(true)
const notFound = ref(false)
const page     = ref(1)

const formatDate = (d) => d
  ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
  : ''

const fetchData = async () => {
  loading.value = true
  try {
    const { data } = await axios.get(`/api/tags/${props.slug}/articles`, {
      params: { page: page.value, lang: locale.value },
    })
    tag.value      = data.tag
    articles.value = data.articles
  } catch {
    notFound.value = true
  } finally {
    loading.value = false
  }
}

onMounted(fetchData)
watch(page, fetchData)
watch(locale, () => { page.value = 1; fetchData() })
</script>
