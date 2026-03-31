import { ref } from 'vue'
import axios from 'axios'

// Module-level cache — shared across all component instances
const settings = ref({})
const loaded   = ref(false)
const loading  = ref(false)

export function useSettings() {
  if (!loaded.value && !loading.value) {
    loading.value = true
    axios.get('/api/settings')
      .then(({ data }) => { settings.value = data; loaded.value = true })
      .catch(() => {})
      .finally(() => { loading.value = false })
  }

  const get = (group, key, fallback = '') => {
    return settings.value[group]?.[key] || fallback
  }

  return { settings, get, loaded }
}
