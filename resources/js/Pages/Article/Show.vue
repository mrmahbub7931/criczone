<template>
  <Head v-if="article">
    <title>{{ article.title }} – CricZone</title>
    <meta name="description" :content="article.excerpt || article.title" />
    <meta property="og:title" :content="article.title" />
    <meta property="og:description" :content="article.excerpt || article.title" />
    <meta v-if="article.featured_image" property="og:image" :content="article.featured_image" />
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

        <!-- Video embed (takes priority over featured image when present) -->
        <div v-if="article.video_embed_url" class="mb-8 rounded-2xl overflow-hidden shadow-lg">
          <div class="relative w-full" style="padding-top: 56.25%">
            <iframe
              :src="article.video_embed_url"
              class="absolute inset-0 w-full h-full"
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              allowfullscreen
            />
          </div>
        </div>

        <!-- Featured image (shown only when no video) -->
        <div v-else-if="article.featured_image" class="mb-8 rounded-2xl overflow-hidden shadow-lg">
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

        <!-- ── Share Buttons ──────────────────────────────────────────────── -->
        <div class="mt-10 pt-6 border-t border-gray-100">
          <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Share this article</p>
          <div class="flex flex-wrap items-center gap-2">
            <!-- Facebook -->
            <a
              :href="`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(articleUrl)}`"
              target="_blank" rel="noopener noreferrer"
              class="inline-flex items-center gap-2 px-4 py-2 bg-[#1877F2] hover:bg-[#166FE5] text-white text-xs font-bold rounded-lg transition-colors"
            >
              <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
              Facebook
            </a>
            <!-- X / Twitter -->
            <a
              :href="`https://twitter.com/intent/tweet?text=${encodeURIComponent(article.title)}&url=${encodeURIComponent(articleUrl)}`"
              target="_blank" rel="noopener noreferrer"
              class="inline-flex items-center gap-2 px-4 py-2 bg-black hover:bg-gray-800 text-white text-xs font-bold rounded-lg transition-colors"
            >
              <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
              X / Twitter
            </a>
            <!-- WhatsApp -->
            <a
              :href="`https://wa.me/?text=${encodeURIComponent(article.title + ' ' + articleUrl)}`"
              target="_blank" rel="noopener noreferrer"
              class="inline-flex items-center gap-2 px-4 py-2 bg-[#25D366] hover:bg-[#1EBE5C] text-white text-xs font-bold rounded-lg transition-colors"
            >
              <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.553 4.115 1.523 5.842L0 24l6.313-1.496A11.954 11.954 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.851 0-3.584-.5-5.082-1.375l-.363-.215-3.748.888.944-3.652-.236-.375A9.96 9.96 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
              WhatsApp
            </a>
            <!-- Copy Link -->
            <button
              @click="copyLink"
              class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-colors"
              :class="copied
                ? 'bg-emerald-500 text-white'
                : 'bg-gray-100 hover:bg-gray-200 text-gray-600'"
            >
              <svg v-if="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="13" height="13" x="9" y="9" rx="2" ry="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
              <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
              {{ copied ? 'Copied!' : 'Copy Link' }}
            </button>
          </div>
        </div>

        <!-- Author card -->
        <div class="mt-10 p-6 bg-gray-50 rounded-2xl border border-gray-100 flex items-center gap-4">
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
        <div class="mt-8">
          <a
            href="/"
            class="inline-flex items-center gap-2 text-sm font-semibold text-primary hover:text-primary-dark transition-colors"
          >
            <ChevronLeft :size="16" />
            Back to Latest News
          </a>
        </div>

      </article>

      <!-- ── Related Articles ─────────────────────────────────────────────── -->
      <section v-if="related.length" class="mt-14">
        <div class="flex items-center gap-3 mb-6">
          <div class="h-6 w-1 bg-secondary rounded-full" />
          <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight">
            You May Also <span class="text-secondary">Like</span>
          </h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
          <a
            v-for="rel in related"
            :key="rel.id"
            :href="`/article/${rel.slug}`"
            class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5"
          >
            <div class="h-40 overflow-hidden bg-gray-100">
              <img
                v-if="rel.featured_image"
                :src="rel.featured_image"
                :alt="rel.title"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              />
              <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary/10 to-secondary/10">
                <span class="text-3xl">🏏</span>
              </div>
            </div>
            <div class="p-4">
              <span
                v-if="rel.category"
                class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider text-white"
                :style="{ backgroundColor: rel.category.color || '#0D47A1' }"
              >{{ rel.category.name }}</span>
              <h3 class="mt-2 text-sm font-bold text-gray-800 group-hover:text-primary transition-colors leading-snug line-clamp-2">
                {{ rel.title }}
              </h3>
              <p class="mt-1.5 text-xs text-gray-400">{{ formatDate(rel.published_at) }}</p>
            </div>
          </a>
        </div>
      </section>

    </div>
  </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, watch } from 'vue'
import axios from 'axios'
import { Head } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import { User, Calendar, Eye, Clock, ChevronLeft } from 'lucide-vue-next'

const props = defineProps({ slug: { type: String, required: true } })

const article  = ref(null)
const loading  = ref(true)
const notFound = ref(false)
const related  = ref([])
const copied   = ref(false)

const articleUrl = computed(() => window.location.href)

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

const formatDate = (d) => d
  ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
  : ''

// ── Social embed processing ───────────────────────────────────────────────────
const loadedScripts = new Set()

const loadScript = (src) => {
  if (loadedScripts.has(src)) {
    if (src.includes('twitter') && window.twttr)   window.twttr.widgets.load()
    if (src.includes('instagram') && window.instgrm) window.instgrm.Embeds.process()
    return
  }
  loadedScripts.add(src)
  const s = document.createElement('script')
  s.src = src; s.async = true
  document.body.appendChild(s)
}

const processSocialEmbeds = () => {
  nextTick(() => {
    document.querySelectorAll('[data-social-embed]:not([data-processed])').forEach(el => {
      const platform = el.getAttribute('data-social-embed')
      const url      = el.getAttribute('data-social-url')
      if (!url) return
      el.setAttribute('data-processed', '1')

      if (platform === 'twitter') {
        el.innerHTML = `<blockquote class="twitter-tweet" data-dnt="true"><a href="${url}"></a></blockquote>`
        loadScript('https://platform.twitter.com/widgets.js')

      } else if (platform === 'instagram') {
        el.innerHTML = `<blockquote class="instagram-media" data-instgrm-captioned data-instgrm-permalink="${url}" data-instgrm-version="14" style="max-width:540px;width:100%;margin:0 auto"></blockquote>`
        loadScript('https://www.instagram.com/embed.js')

      } else if (platform === 'facebook') {
        // Use Facebook's plugin iframe — no SDK needed, no console errors
        const encoded = encodeURIComponent(url)
        el.innerHTML = `<div style="max-width:500px;margin:0 auto"><iframe src="https://www.facebook.com/plugins/post.php?href=${encoded}&show_text=true&width=500" width="500" height="400" style="border:none;overflow:hidden;width:100%;border-radius:8px" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay;clipboard-write;encrypted-media;picture-in-picture;web-share"></iframe></div>`

      } else if (platform === 'tiktok') {
        const m = url.match(/\/video\/(\d+)/)
        el.innerHTML = `<blockquote class="tiktok-embed" cite="${url}" data-video-id="${m?.[1] ?? ''}" style="max-width:605px;min-width:325px;margin:0 auto"><section></section></blockquote>`
        loadScript('https://www.tiktok.com/embed.js')

      } else if (platform === 'linkedin') {
        el.innerHTML = `<a href="${url}" target="_blank" rel="noopener noreferrer" style="display:flex;align-items:center;gap:12px;padding:14px 18px;border:1px solid #e5e7eb;border-radius:12px;text-decoration:none;background:#f8fafc;max-width:540px;"><div style="width:36px;height:36px;background:#0A66C2;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><svg width="20" height="20" fill="white" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg></div><div style="flex:1;min-width:0"><div style="font-size:13px;font-weight:700;color:#0A66C2">View on LinkedIn</div><div style="font-size:11px;color:#6b7280;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-top:2px">${url}</div></div></a>`
      }
    })
  })
}

watch(article, () => processSocialEmbeds())

const copyLink = async () => {
  try {
    await navigator.clipboard.writeText(articleUrl.value)
    copied.value = true
    setTimeout(() => { copied.value = false }, 2500)
  } catch {
    // Fallback for older browsers
    const el = document.createElement('input')
    el.value = articleUrl.value
    document.body.appendChild(el)
    el.select()
    document.execCommand('copy')
    document.body.removeChild(el)
    copied.value = true
    setTimeout(() => { copied.value = false }, 2500)
  }
}

onMounted(async () => {
  try {
    const [artRes, relRes] = await Promise.allSettled([
      axios.get(`/api/articles/${props.slug}`),
      axios.get(`/api/articles/${props.slug}/related`),
    ])
    if (artRes.status === 'fulfilled') {
      article.value = artRes.value.data
      processSocialEmbeds()
    } else {
      notFound.value = true
    }
    if (relRes.status === 'fulfilled') {
      related.value = relRes.value.data ?? []
    }
  } finally {
    loading.value = false
  }
})
</script>
