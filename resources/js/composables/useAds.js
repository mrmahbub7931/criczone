import { ref, computed } from 'vue'
import axios from 'axios'
import { useSettings } from '@/composables/useSettings.js'

const promotions = ref({})   // keyed by position → promo object
const loaded     = ref(false)

const fetchPromotions = async () => {
  if (loaded.value) return
  try {
    const { data } = await axios.get('/api/ads/public')
    promotions.value = data
  } catch { /* silent */ }
  loaded.value = true
}

export function useAds() {
  const { get: setting } = useSettings()

  /**
   * Returns { type: 'promo'|'adsense'|null, promo, code }
   * for a given position slug.
   */
  const slotFor = (position) => {
    const promo = promotions.value[position]
    if (promo) return { type: 'promo', promo, code: null }

    const code    = setting('ads', `ad_${position}`, '')
    const enabled = setting('ads', `ad_${position}_enabled`, '1') === '1'
    if (code && enabled) return { type: 'adsense', promo: null, code }

    return { type: null, promo: null, code: null }
  }

  return { promotions, loaded, fetchPromotions, slotFor }
}
