import { ref } from 'vue'
import axios from 'axios'

const cache = {}

/**
 * Fetch a menu by location from the public API.
 * Results are cached for the lifetime of the page.
 */
export function useMenu(location) {
  const items   = ref([])
  const loading = ref(true)

  const load = async () => {
    if (cache[location]) {
      items.value = cache[location]
      loading.value = false
      return
    }
    try {
      const { data } = await axios.get(`/api/menus/${location}/items`)
      items.value = data.items ?? []
      cache[location] = items.value
    } catch {
      items.value = []
    } finally {
      loading.value = false
    }
  }

  load()

  return { items, loading }
}
