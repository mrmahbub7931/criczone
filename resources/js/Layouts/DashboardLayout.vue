<template>
  <div class="min-h-screen bg-gray-50 flex">

    <!-- ── Sidebar ── -->
    <aside
      :class="[
        'fixed inset-y-0 left-0 z-30 flex flex-col bg-primary transition-all duration-300',
        sidebarCollapsed ? 'w-16' : 'w-64',
        mobileOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'
      ]"
    >
      <!-- Logo -->
      <div class="flex items-center h-16 px-4 border-b border-primary-light shrink-0">
        <img :src="logoUrl" alt="CricZone" class="h-8 w-auto object-contain rounded-full" />
        <span v-if="!sidebarCollapsed" class="ml-3 text-white font-black text-lg tracking-tight">CricZone</span>
      </div>

      <!-- Nav items -->
      <nav class="flex-1 py-4 overflow-y-auto">
        <Link
          v-for="item in navItems"
          :key="item.route"
          :href="item.href"
          :class="[
            'flex items-center gap-3 px-4 py-2.5 mx-2 rounded-lg text-sm font-medium transition-all duration-150 group',
            isActive(item.href)
              ? 'bg-white/20 text-white'
              : 'text-white/70 hover:bg-white/10 hover:text-white'
          ]"
        >
          <component :is="item.icon" :size="18" class="shrink-0" />
          <span v-if="!sidebarCollapsed" class="truncate">{{ item.label }}</span>
          <span
            v-if="!sidebarCollapsed && item.badge"
            class="ml-auto bg-secondary text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none"
          >{{ item.badge }}</span>
        </Link>
      </nav>

      <!-- Collapse toggle (desktop) -->
      <button
        @click="toggleSidebar"
        class="hidden md:flex items-center justify-center h-12 border-t border-primary-light text-white/60 hover:text-white hover:bg-white/10 transition-colors"
      >
        <ChevronLeft :size="16" :class="['transition-transform duration-300', sidebarCollapsed ? 'rotate-180' : '']" />
      </button>
    </aside>

    <!-- Mobile overlay -->
    <div
      v-if="mobileOpen"
      @click="mobileOpen = false"
      class="fixed inset-0 z-20 bg-black/50 md:hidden"
    />

    <!-- ── Main area ── -->
    <div :class="['flex-1 flex flex-col transition-all duration-300', sidebarCollapsed ? 'md:ml-16' : 'md:ml-64']">

      <!-- Topbar -->
      <header class="sticky top-0 z-10 h-16 bg-white border-b border-gray-200 flex items-center gap-4 px-4 sm:px-6">
        <!-- Mobile menu -->
        <button @click="mobileOpen = !mobileOpen" class="md:hidden text-gray-500 hover:text-gray-700">
          <Menu :size="20" />
        </button>

        <!-- Page title -->
        <h1 class="font-bold text-gray-800 text-base truncate flex-1">{{ pageTitle }}</h1>

        <!-- Search -->
        <div class="hidden sm:flex items-center gap-2 bg-gray-100 rounded-lg px-3 py-1.5 w-52">
          <Search :size="14" class="text-gray-400 shrink-0" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search..."
            class="bg-transparent text-sm text-gray-700 placeholder-gray-400 outline-none w-full"
          />
        </div>

        <!-- Notifications -->
        <div class="relative">
          <button
            @click="notifOpen = !notifOpen"
            class="relative p-2 text-gray-500 hover:text-primary hover:bg-gray-100 rounded-lg transition-colors"
          >
            <Bell :size="18" />
            <span
              v-if="unreadCount > 0"
              class="absolute top-1 right-1 w-4 h-4 bg-secondary text-white text-[9px] font-bold rounded-full flex items-center justify-center leading-none"
            >{{ unreadCount }}</span>
          </button>

          <!-- Notification dropdown -->
          <Transition name="fade-down">
            <div
              v-if="notifOpen"
              class="absolute right-0 top-12 w-80 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden z-50"
            >
              <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                <span class="font-bold text-sm text-gray-800">Notifications</span>
                <button @click="markAllRead(); notifOpen = false" class="text-xs text-primary hover:underline">
                  Mark all read
                </button>
              </div>
              <div class="max-h-72 overflow-y-auto divide-y divide-gray-50">
                <div
                  v-for="n in notifications"
                  :key="n.id"
                  :class="['px-4 py-3 text-sm', !n.read ? 'bg-blue-50/50' : '']"
                >
                  <p :class="['text-gray-700 leading-snug', !n.read ? 'font-medium' : '']">{{ n.message }}</p>
                  <p class="text-gray-400 text-xs mt-0.5">{{ n.time }}</p>
                </div>
              </div>
            </div>
          </Transition>
        </div>

        <!-- User + logout -->
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white text-xs font-bold shrink-0">
            {{ userInitials }}
          </div>
          <div class="hidden sm:block">
            <p class="text-sm font-medium text-gray-700 leading-none">{{ auth.user?.name }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5 capitalize">{{ auth.roles?.join(', ') }}</p>
          </div>
          <Link
            href="/logout"
            method="post"
            as="button"
            class="ml-1 p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
            title="Log out"
          >
            <LogOut :size="16" />
          </Link>
        </div>
      </header>

      <!-- Page content -->
      <main class="flex-1 p-4 sm:p-6 overflow-auto">
        <slot />
      </main>
    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import {
  LayoutDashboard, Newspaper, Tag, Radio, Settings, ChevronLeft, Bell, Search, Menu, LogOut,
} from 'lucide-vue-next'
import { useDashboard } from '@/composables/useDashboard.js'

const { sidebarCollapsed, toggleSidebar, notifications, unreadCount, markAllRead } = useDashboard()

const logoUrl = `${window.location.origin}/images/criczone.jpg`

const mobileOpen = ref(false)
const notifOpen  = ref(false)
const searchQuery = ref('')

const page = usePage()
const auth = computed(() => page.props.auth)
const userInitials = computed(() => {
  const name = auth.value?.user?.name || ''
  return name.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase() || '?'
})

const navItems = [
  { label: 'Overview',    href: '/dashboard',             icon: LayoutDashboard },
  { label: 'Articles',    href: '/dashboard/articles',    icon: Newspaper       },
  { label: 'Categories',  href: '/dashboard/categories',  icon: Tag             },
  { label: 'Live Scores', href: '/dashboard/live-scores', icon: Radio           },
  { label: 'Settings',    href: '/dashboard/settings',    icon: Settings        },
]

const isActive = (href) => {
  const url = page.url
  if (href === '/dashboard') return url === '/dashboard' || url === '/dashboard/'
  return url.startsWith(href)
}

const titleMap = {
  '/dashboard':             'Overview',
  '/dashboard/articles':    'Articles',
  '/dashboard/categories':  'Categories',
  '/dashboard/live-scores': 'Live Scores',
  '/dashboard/settings':    'Settings',
}

const pageTitle = computed(() => {
  for (const [path, title] of Object.entries(titleMap)) {
    if (page.url === path || page.url.startsWith(path + '/')) return title
  }
  return 'Dashboard'
})
</script>

<style scoped>
.fade-down-enter-active,
.fade-down-leave-active {
  transition: opacity 0.15s, transform 0.15s;
}
.fade-down-enter-from,
.fade-down-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}
</style>
