/**
 * useLocale — lightweight i18n composable
 *
 * Usage:
 *   import { useLocale } from '@/composables/useLocale'
 *   const { locale, t, setLocale } = useLocale()
 *
 *   t('nav.home')          → 'হোম'  (in Bangla mode)
 *   t('home.latestNews')   → 'Latest News' (in English mode)
 */

import { ref, computed } from 'vue'
import bn from '@/lang/bn.js'
import en from '@/lang/en.js'

const STORAGE_KEY = 'criczone_locale'
const SUPPORTED   = ['bn', 'en']

// ── Shared reactive state (module-level singleton) ────────────────────────────
const locale = ref(
  SUPPORTED.includes(localStorage.getItem(STORAGE_KEY))
    ? localStorage.getItem(STORAGE_KEY)
    : 'bn'
)

const messages = { bn, en }

/**
 * Resolve a dot-notation key against the current locale's message tree.
 * Falls back to the key itself if not found.
 */
function t(key) {
  const parts  = key.split('.')
  let   node   = messages[locale.value]
  for (const part of parts) {
    if (node == null || typeof node !== 'object') return key
    node = node[part]
  }
  return node ?? key
}

function setLocale(lang) {
  if (!SUPPORTED.includes(lang)) return
  locale.value = lang
  localStorage.setItem(STORAGE_KEY, lang)
  // Emit a custom DOM event so non-Vue code (e.g. API layers) can react
  window.dispatchEvent(new CustomEvent('locale-changed', { detail: lang }))
}

export function useLocale() {
  return { locale, t, setLocale }
}
