import { computed } from 'vue'
import { usePage }  from '@inertiajs/vue3'

/**
 * usePermission()
 *
 * Usage:
 *   const { can, isAdmin } = usePermission()
 *   if (can('articles.create')) { ... }
 *   v-if="can('users.delete')"
 */
export function usePermission() {
    const page = usePage()

    const permissions = computed(() => page.props.auth?.permissions ?? [])
    const roles       = computed(() => page.props.auth?.roles ?? [])

    const isAdmin = computed(() => roles.value.includes('admin'))

    /**
     * Check one or more permissions (user needs at least one).
     * Admin wildcard '*' always returns true.
     */
    const can = (...perms) => {
        if (permissions.value.includes('*')) return true          // admin
        return perms.some(p => permissions.value.includes(p))
    }

    return { can, isAdmin, permissions, roles }
}
