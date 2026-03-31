<template>
  <Head>
    <title>{{ pageTitle }}</title>
    <meta name="description" :content="pageDescription" />
    <meta v-if="pageKeywords" name="keywords" :content="pageKeywords" />
    <meta property="og:title" :content="pageTitle" />
    <meta property="og:description" :content="pageDescription" />
    <meta property="og:type" content="website" />
    <!-- Dynamic favicon — updates as soon as settings load -->
    <link v-if="favicon" rel="icon" :href="favicon" />
  </Head>

  <div class="min-h-screen flex flex-col bg-gray-50">
    <Navbar />
    <BreakingNewsTicker />

    <main class="flex-grow">
      <slot />
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { computed, watchEffect } from 'vue'
import { Head } from '@inertiajs/vue3'
import Navbar              from '@/Components/Navbar.vue'
import Footer              from '@/Components/Footer.vue'
import BreakingNewsTicker  from '@/Components/BreakingNewsTicker.vue'
import { useSettings }     from '@/composables/useSettings.js'

const { get: setting, loaded } = useSettings()

const pageTitle       = computed(() => setting('seo', 'meta_title',       'CricZone – Cricket News & Live Scores'))
const pageDescription = computed(() => setting('seo', 'meta_description', 'Your premier destination for cricket news, live scores and match analysis.'))
const pageKeywords    = computed(() => setting('seo', 'meta_keywords',    ''))
const favicon         = computed(() => setting('general', 'site_favicon', ''))

// Inject GA / GTM once settings are loaded — idempotent
watchEffect(() => {
  if (!loaded.value) return

  const gaId  = setting('seo', 'google_analytics_id',   '')
  const gtmId = setting('seo', 'google_tag_manager_id', '')

  if (gaId && !document.querySelector(`script[data-ga="${gaId}"]`)) {
    const s1 = document.createElement('script')
    s1.async = true
    s1.src   = `https://www.googletagmanager.com/gtag/js?id=${gaId}`
    s1.setAttribute('data-ga', gaId)
    document.head.appendChild(s1)

    const s2 = document.createElement('script')
    s2.setAttribute('data-ga-inline', gaId)
    s2.textContent = `window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','${gaId}');`
    document.head.appendChild(s2)
  }

  if (gtmId && !document.querySelector(`script[data-gtm="${gtmId}"]`)) {
    const s = document.createElement('script')
    s.setAttribute('data-gtm', gtmId)
    s.textContent = `(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','${gtmId}');`
    document.head.appendChild(s)
  }
})
</script>
