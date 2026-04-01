<template>
  <aside :class="[
    'fixed inset-y-0 left-0 z-30 flex flex-col bg-[#0D47A1] transition-all duration-300',
    collapsed ? 'w-16' : 'w-64',
    open ? 'translate-x-0' : '-translate-x-full md:translate-x-0',
  ]">
    <!-- Logo -->
    <div class="flex items-center h-16 px-4 border-b border-white/10 shrink-0 gap-3">
      <img :src="logoUrl" alt="CricZone" class="h-8 w-8 rounded-full object-cover shrink-0" />
      <span v-if="!collapsed" class="text-white font-black text-base tracking-tight truncate">CricZone</span>
    </div>

    <!-- Nav -->
    <nav class="flex-1 py-3 overflow-y-auto overflow-x-visible space-y-0.5">
      <template v-for="item in visibleItems" :key="item.href">
        <Link :href="item.href" :class="[
          'relative group flex items-center gap-3 mx-2 px-3 py-2.5 rounded-lg text-sm font-medium transition-all',
          isActive(item.href)
            ? 'bg-white/20 text-white'
            : 'text-white/65 hover:bg-white/10 hover:text-white',
        ]">
          <component :is="item.icon" :size="18" class="shrink-0" />
          <span v-if="!collapsed" class="truncate">{{ item.label }}</span>
          <span v-if="collapsed"
            class="fixed ml-1 px-2 py-1 text-xs bg-black text-white rounded opacity-0 group-hover:opacity-100 whitespace-nowrap transition-opacity duration-200 z-50 left-[calc(100%)]">
            {{ item.label }}
          </span>
        </Link>
      </template>
    </nav>

    <!-- Role badge -->
    <div v-if="!collapsed" class="px-4 py-3 border-t border-white/10">
      <span v-for="role in roles" :key="role"
        class="inline-block text-[10px] font-bold uppercase tracking-wider bg-white/15 text-white/80 px-2 py-0.5 rounded-full mr-1">{{
          role }}</span>
    </div>

    <!-- Collapse toggle -->
    <button @click="$emit('toggle-collapse')"
      class="hidden md:flex items-center justify-center h-11 border-t border-white/10 text-white/50 hover:text-white hover:bg-white/10 transition-colors shrink-0">
      <ChevronLeft :size="16" :class="['transition-transform duration-300', collapsed ? 'rotate-180' : '']" />
    </button>
  </aside>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import {
  LayoutDashboard, Newspaper, Users, ShieldCheck, Tag, Radio, Settings, ChevronLeft, FileText, Menu,
} from 'lucide-vue-next'

defineProps({
  collapsed: Boolean,
  open: Boolean,
})
defineEmits(['toggle-collapse'])

const logoUrl = `${window.location.origin}/images/criczone.png`

const page = usePage()
const roles = computed(() => page.props.auth?.roles ?? [])

const allItems = [
  { href: '/dashboard', label: 'Dashboard', icon: LayoutDashboard, roles: ['admin', 'editor', 'viewer'] },
  { href: '/dashboard/articles', label: 'Articles', icon: Newspaper, roles: ['admin', 'editor'] },
  { href: '/dashboard/pages', label: 'Pages', icon: FileText, roles: ['admin', 'editor'] },
  { href: '/dashboard/categories', label: 'Categories', icon: Tag, roles: ['admin', 'editor'] },
  { href: '/dashboard/menus', label: 'Menus', icon: Menu, roles: ['admin'] },
  { href: '/dashboard/live-scores', label: 'Live Scores', icon: Radio, roles: ['admin'] },
  { href: '/dashboard/users', label: 'Users', icon: Users, roles: ['admin'] },
  { href: '/dashboard/roles', label: 'Roles', icon: ShieldCheck, roles: ['admin'] },
  { href: '/dashboard/settings', label: 'Settings', icon: Settings, roles: ['admin'] },
]

const visibleItems = computed(() =>
  allItems.filter(item => item.roles.some(r => roles.value.includes(r)))
)

const isActive = (href) => {
  const url = page.url
  if (href === '/dashboard') return url === '/dashboard' || url === '/dashboard/'
  return url.startsWith(href)
}
</script>
