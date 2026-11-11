import { ref, watch } from 'vue'

const STORAGE_KEY = 'cz-theme'

// Shared reactive state
const dark = ref(false)

const apply = (isDark) => {
  document.documentElement.classList.toggle('dark', isDark)
}

const init = () => {
  const saved = localStorage.getItem(STORAGE_KEY)
  dark.value = saved === 'dark'
  apply(dark.value)
}

const toggle = () => {
  dark.value = !dark.value
}

watch(dark, (val) => {
  apply(val)
  localStorage.setItem(STORAGE_KEY, val ? 'dark' : 'light')
})

export function useTheme() {
  return { dark, toggle, init }
}
