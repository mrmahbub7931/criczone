<template>
  <Head v-if="article">
    <title>{{ article.title }} – CricZone</title>
    <meta name="description" :content="article.excerpt || article.title" />
    <meta property="og:title" :content="article.title" />
    <meta property="og:description" :content="article.excerpt || article.title" />
    <meta v-if="article.image" property="og:image" :content="article.image" />
    <meta property="og:type" content="article" />
  </Head>

  <MainLayout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-10">

      <!-- Skeleton -->
      <div v-if="loading" class="animate-pulse space-y-5">
        <div class="h-5 w-24 bg-gray-200 rounded-full" />
        <div class="h-10 bg-gray-200 rounded-lg w-3/4" />
        <div class="h-4 bg-gray-100 rounded w-1/3" />
        <div class="h-[420px] bg-gray-200 rounded-2xl" />
        <div class="space-y-3 pt-4">
          <div v-for="i in 8" :key="i" :class="['h-4 bg-gray-100 rounded', i % 5 === 0 ? 'w-2/3' : 'w-full']" />
        </div>
      </div>

      <!-- 404 state -->
      <div v-else-if="notFound" class="text-center py-24">
        <p class="text-6xl font-black text-gray-200 mb-4">404</p>
        <p class="text-xl font-bold text-gray-700 mb-2">Article Not Found</p>
        <p class="text-gray-400 text-sm mb-8">The article you're looking for doesn't exist or has been removed.</p>
        <a href="/" class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary text-white text-sm font-semibold rounded-full hover:bg-primary-dark transition-colors">
          ← Back to Home
        </a>
      </div>

      <!-- Article -->
      <article v-else-if="article">

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-gray-400 mb-6">
          <a href="/" class="hover:text-primary transition-colors">Home</a>
          <span>/</span>
          <a
            v-if="article.category"
            :href="`/category/${article.category.slug ?? ''}`"
            class="hover:text-primary transition-colors"
          >{{ article.category.name }}</a>
          <span v-if="article.category">/</span>
          <span class="text-gray-600 line-clamp-1">{{ article.title }}</span>
        </nav>

        <!-- Category badge -->
        <div class="mb-4">
          <span
            class="inline-block text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider text-white"
            :style="{ backgroundColor: article.category?.color || '#0D47A1' }"
          >{{ article.category?.name || 'Cricket' }}</span>
        </div>

        <!-- Title -->
        <h1 class="text-3xl sm:text-4xl font-black text-gray-900 leading-tight mb-5">
          {{ article.title }}
        </h1>

        <!-- Meta row -->
        <div class="flex flex-wrap items-center gap-5 text-sm text-gray-500 mb-8 pb-6 border-b border-gray-100">
          <span class="flex items-center gap-1.5">
            <User :size="15" class="text-gray-400" />
            <span class="font-medium text-gray-700">{{ article.author?.name || 'Staff Writer' }}</span>
          </span>
          <span class="flex items-center gap-1.5">
            <Calendar :size="15" class="text-gray-400" />
            {{ formattedDate }}
          </span>
          <span class="flex items-center gap-1.5">
            <Eye :size="15" class="text-gray-400" />
            {{ article.views.toLocaleString() }} views
          </span>
          <span class="flex items-center gap-1.5">
            <Clock :size="15" class="text-gray-400" />
            {{ readTime }}
          </span>
        </div>

        <!-- Featured image -->
        <div v-if="article.featured_image" class="mb-8 rounded-2xl overflow-hidden shadow-lg">
          <img
            :src="article.featured_image"
            :alt="article.title"
            class="w-full h-[350px] sm:h-[420px] object-cover"
          />
        </div>

        <!-- Excerpt -->
        <p v-if="article.excerpt" class="text-lg text-gray-600 leading-relaxed mb-8 font-medium border-l-4 border-secondary pl-5 italic">
          {{ article.excerpt }}
        </p>

        <!-- Content -->
        <div
          class="prose prose-lg max-w-none prose-headings:font-black prose-headings:text-gray-900 prose-p:text-gray-700 prose-p:leading-relaxed prose-a:text-primary prose-a:no-underline hover:prose-a:underline prose-img:rounded-xl prose-blockquote:border-secondary prose-blockquote:text-gray-600"
          v-html="article.content"
        />

        <!-- Author card -->
        <div class="mt-12 p-6 bg-gray-50 rounded-2xl border border-gray-100 flex items-center gap-4">
          <div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
            <User :size="26" class="text-primary" />
          </div>
          <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-0.5">Written by</p>
            <p class="font-bold text-gray-900 text-base">{{ article.author?.name || 'Staff Writer' }}</p>
            <p class="text-sm text-gray-500">CricZone Editorial Team</p>
          </div>
        </div>

        <!-- Back link -->
        <div class="mt-10">
          <a
            href="/"
            class="inline-flex items-center gap-2 text-sm font-semibold text-primary hover:text-primary-dark transition-colors"
          >
            <ChevronLeft :size="16" />
            Back to Latest News
          </a>
        </div>

      </article>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { Head } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import { User, Calendar, Eye, Clock, ChevronLeft } from 'lucide-vue-next'

const props = defineProps({ slug: { type: String, required: true } })

const article   = ref(null)
const loading   = ref(true)
const notFound  = ref(false)

const formattedDate = computed(() => {
  if (!article.value?.published_at) return ''
  return new Date(article.value.published_at).toLocaleDateString('en-US', {
    year: 'numeric', month: 'long', day: 'numeric',
  })
})

const readTime = computed(() => {
  if (!article.value?.content) return '3 min read'
  const words = article.value.content.replace(/<[^>]+>/g, '').split(/\s+/).length
  return `${Math.max(1, Math.ceil(words / 200))} min read`
})

onMounted(async () => {
  try {
    const { data } = await axios.get(`/api/articles/${props.slug}`)
    article.value = data
  } catch (err) {
    notFound.value = true
  } finally {
    loading.value = false
  }
})
</script>
