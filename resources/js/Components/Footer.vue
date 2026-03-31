<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Mail, Send, Phone, ArrowRight, CheckCircle2, AlertCircle, Loader2 } from 'lucide-vue-next'
import axios from 'axios'
import { useMenu }     from '@/composables/useMenu.js'
import { useSettings } from '@/composables/useSettings.js'

const { get: setting } = useSettings()

// ── Settings ──────────────────────────────────────────────────────────────────
const logo              = computed(() => setting('general',    'site_logo',          `${window.location.origin}/images/criczone.png`))
const description       = computed(() => setting('footer',     'footer_description', 'Your premier destination for cricket news, live scores, match analysis, and everything cricket.'))
const footerEmail       = computed(() => setting('footer',     'footer_email',       ''))
const footerPhone       = computed(() => setting('footer',     'footer_phone',       ''))
const copyright         = computed(() => setting('footer',     'footer_copyright',   `© ${new Date().getFullYear()} CricZone. All rights reserved.`))
const newsletterEnabled = computed(() => setting('newsletter', 'newsletter_enabled', '1') === '1')

// ── Social links — only entries with a non-empty value are rendered ───────────
const socialLinks = computed(() => [
  { key: 'social_facebook',  label: 'Facebook',    icon: 'fb' },
  { key: 'social_twitter',   label: 'X / Twitter', icon: 'tw' },
  { key: 'social_instagram', label: 'Instagram',   icon: 'ig' },
  { key: 'social_youtube',   label: 'YouTube',     icon: 'yt' },
  { key: 'social_linkedin',  label: 'LinkedIn',    icon: 'li' },
  { key: 'social_whatsapp',  label: 'WhatsApp',    icon: 'wa' },
  { key: 'social_telegram',  label: 'Telegram',    icon: 'tg' },
  { key: 'social_tiktok',    label: 'TikTok',      icon: 'tk' },
].filter(s => setting('social', s.key, '').trim() !== '')
 .map(s => ({ ...s, url: setting('social', s.key, '') })))

// ── Dynamic menus ─────────────────────────────────────────────────────────────
const { items: quickLinks }   = useMenu('footer_quick')
const { items: categories }   = useMenu('footer_categories')
const { items: legalLinks }   = useMenu('footer_legal')

const fallbackQuickLinks = [
  { url: '/',              label: 'Home' },
  { url: '/category/news', label: 'Latest News' },
  { url: '/live-scores',   label: 'Live Scores' },
  { url: '/search',        label: 'Search Articles' },
  { url: '/category/ipl',  label: 'IPL' },
]
const fallbackCategories = [
  { url: '#', label: 'International' },
  { url: '#', label: 'IPL' },
  { url: '#', label: 'T20 World Cup' },
  { url: '#', label: 'Test Cricket' },
  { url: '#', label: 'Domestic' },
]
const fallbackLegal = [
  { url: '/pages/privacy-policy',   label: 'Privacy Policy' },
  { url: '/pages/terms-of-service', label: 'Terms of Service' },
  { url: '/pages/cookie-policy',    label: 'Cookie Policy' },
]

// ── Newsletter subscribe ───────────────────────────────────────────────────────
const email       = ref('')
const submitting  = ref(false)
const subStatus   = ref(null)   // null | 'success' | 'error'
const subMessage  = ref('')

const subscribe = async () => {
  if (!email.value.trim() || submitting.value) return
  submitting.value = true
  subStatus.value  = null
  try {
    const { data } = await axios.post('/api/newsletter/subscribe', {
      email: email.value.trim(),
    })
    subStatus.value  = 'success'
    subMessage.value = data.message ?? 'Successfully subscribed!'
    email.value      = ''
  } catch (err) {
    subStatus.value  = 'error'
    subMessage.value = err.response?.data?.message
      ?? err.response?.data?.errors?.email?.[0]
      ?? 'Failed to subscribe. Please try again.'
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <footer class="bg-gradient-to-b from-primary-dark to-[#061f4a] text-white">
    <!-- Main Footer -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-12 pb-8">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">

        <!-- Brand -->
        <div>
          <Link href="/" class="flex items-center gap-2.5 mb-4">
            <img :src="logo" alt="CricZone" class="h-10 w-auto" />
          </Link>
          <p class="text-white/50 text-sm leading-relaxed mb-5">{{ description }}</p>
          <div class="space-y-2.5 text-sm text-white/40">
            <div v-if="footerEmail" class="flex items-center gap-2">
              <Mail class="w-4 h-4 text-secondary/60" />
              <span>{{ footerEmail }}</span>
            </div>
            <div v-if="footerPhone" class="flex items-center gap-2">
              <Phone class="w-4 h-4 text-secondary/60" />
              <span>{{ footerPhone }}</span>
            </div>
          </div>

          <!-- Social icons — only rendered for configured platforms -->
          <div v-if="socialLinks.length" class="flex flex-wrap gap-2.5 mt-5">
            <a
              v-for="social in socialLinks"
              :key="social.key"
              :href="social.url"
              :aria-label="social.label"
              target="_blank"
              rel="noopener noreferrer"
              class="w-8 h-8 rounded-lg bg-white/10 hover:bg-secondary/80 flex items-center justify-center transition-colors duration-150"
              :title="social.label"
            >
              <!-- Facebook -->
              <svg v-if="social.icon === 'fb'" class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
              <!-- X / Twitter -->
              <svg v-else-if="social.icon === 'tw'" class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
              <!-- Instagram -->
              <svg v-else-if="social.icon === 'ig'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5" stroke-linecap="round"/></svg>
              <!-- YouTube -->
              <svg v-else-if="social.icon === 'yt'" class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M22.54 6.42a2.78 2.78 0 00-1.94-1.96C18.88 4 12 4 12 4s-6.88 0-8.6.46A2.78 2.78 0 001.46 6.42 29 29 0 001 12a29 29 0 00.46 5.58 2.78 2.78 0 001.94 1.96C5.12 20 12 20 12 20s6.88 0 8.6-.46a2.78 2.78 0 001.94-1.96A29 29 0 0023 12a29 29 0 00-.46-5.58z"/><polygon fill="#061f4a" points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"/></svg>
              <!-- LinkedIn -->
              <svg v-else-if="social.icon === 'li'" class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>
              <!-- WhatsApp -->
              <svg v-else-if="social.icon === 'wa'" class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.553 4.115 1.523 5.842L0 24l6.313-1.496A11.954 11.954 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.851 0-3.584-.5-5.082-1.375l-.363-.215-3.748.888.944-3.652-.236-.375A9.96 9.96 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
              <!-- Telegram -->
              <svg v-else-if="social.icon === 'tg'" class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.447 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.136-.953l11.57-4.461c.537-.194 1.006.131.833.941z"/></svg>
              <!-- TikTok -->
              <svg v-else-if="social.icon === 'tk'" class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.3 6.3 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.78 1.52V6.76a4.85 4.85 0 01-1.01-.07z"/></svg>
            </a>
          </div>
        </div>

        <!-- Quick Links -->
        <div>
          <h3 class="font-bold text-sm uppercase tracking-wider mb-4">Quick Links</h3>
          <ul class="space-y-2.5">
            <li
              v-for="link in (quickLinks.length ? quickLinks : fallbackQuickLinks)"
              :key="link.label ?? link.id"
            >
              <a
                :href="link.url"
                :target="link.target ?? '_self'"
                class="flex items-center gap-2 text-sm text-white/50 hover:text-secondary transition-colors duration-150 group"
              >
                <ArrowRight class="w-3 h-3 text-secondary/0 group-hover:text-secondary transition-colors duration-150" />
                {{ link.label }}
              </a>
            </li>
          </ul>
        </div>

        <!-- Categories -->
        <div>
          <h3 class="font-bold text-sm uppercase tracking-wider mb-4">Categories</h3>
          <ul class="space-y-2.5">
            <li
              v-for="cat in (categories.length ? categories : fallbackCategories)"
              :key="cat.label ?? cat.id"
            >
              <a
                :href="cat.url"
                :target="cat.target ?? '_self'"
                class="flex items-center gap-2 text-sm text-white/50 hover:text-secondary transition-colors duration-150 group"
              >
                <ArrowRight class="w-3 h-3 text-secondary/0 group-hover:text-secondary transition-colors duration-150" />
                {{ cat.label }}
              </a>
            </li>
          </ul>
        </div>

        <!-- Newsletter (hidden when disabled) -->
        <div v-if="newsletterEnabled">
          <h3 class="font-bold text-sm uppercase tracking-wider mb-4">Stay Updated</h3>
          <p class="text-white/50 text-sm mb-4 leading-relaxed">
            Subscribe to our newsletter for daily cricket updates and exclusive content.
          </p>

          <!-- Success state -->
          <div v-if="subStatus === 'success'" class="flex items-start gap-2.5 bg-white/10 rounded-lg p-3.5">
            <CheckCircle2 class="w-4 h-4 text-secondary-light shrink-0 mt-0.5" />
            <p class="text-xs text-white/90 leading-relaxed">{{ subMessage }}</p>
          </div>

          <!-- Form -->
          <div v-else class="space-y-2.5">
            <input
              v-model="email"
              type="email"
              placeholder="Your email address"
              class="w-full px-4 py-2.5 rounded-lg text-sm text-gray-900 placeholder-gray-400 bg-white/90 focus:bg-white focus:outline-none focus:ring-2 focus:ring-secondary transition-all duration-200"
              @keydown.enter="subscribe"
            />
            <div v-if="subStatus === 'error'" class="flex items-start gap-2 bg-red-500/20 rounded-lg px-3 py-2">
              <AlertCircle class="w-3.5 h-3.5 text-red-300 shrink-0 mt-0.5" />
              <p class="text-xs text-red-200 leading-relaxed">{{ subMessage }}</p>
            </div>
            <button
              @click="subscribe"
              :disabled="submitting || !email.trim()"
              class="w-full flex items-center justify-center gap-2 bg-secondary hover:bg-secondary-dark disabled:opacity-60 text-white py-2.5 rounded-lg font-bold text-sm transition-colors duration-200"
            >
              <Loader2 v-if="submitting" class="w-3.5 h-3.5 animate-spin" />
              <Send v-else class="w-3.5 h-3.5" />
              {{ submitting ? 'Subscribing…' : 'Subscribe' }}
            </button>
          </div>
        </div>

        <!-- Empty column placeholder when newsletter is disabled -->
        <div v-else />

      </div>
    </div>

    <!-- Bottom Bar -->
    <div class="border-t border-white/10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 py-5 flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-white/30 text-xs">{{ copyright }}</p>
        <div class="flex items-center gap-6 text-xs text-white/30">
          <a
            v-for="link in (legalLinks.length ? legalLinks : fallbackLegal)"
            :key="link.label ?? link.id"
            :href="link.url"
            :target="link.target ?? '_self'"
            class="hover:text-white/60 transition-colors duration-150"
          >{{ link.label }}</a>
        </div>
      </div>
    </div>
  </footer>
</template>
