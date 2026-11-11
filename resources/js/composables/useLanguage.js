import { useLocale } from '@/composables/useLocale.js'

export function useLanguage() {
    const { locale: lang, setLocale } = useLocale()

    function setLang(value) {
        setLocale(value)
    }

    function toggleLang() {
        setLocale(lang.value === 'bn' ? 'en' : 'bn')
    }

    return { lang, setLang, toggleLang }
}
