<template>
  <Head>
    <title>CricZone - Dashboard</title>
  </Head>
  <div class="min-h-screen bg-gray-50 flex">

    <!-- Sidebar -->
    <DashboardSidebar
      :collapsed="sidebarCollapsed"
      :open="mobileOpen"
      @toggle-collapse="toggleSidebar"
    />

    <!-- Mobile overlay -->
    <div
      v-if="mobileOpen"
      class="fixed inset-0 z-20 bg-black/50 md:hidden"
      @click="mobileOpen = false"
    />

    <!-- Main -->
    <div :class="['flex-1 flex flex-col min-w-0 transition-all duration-300', sidebarCollapsed ? 'md:ml-16' : 'md:ml-64']">

      <!-- Topbar -->
      <header class="sticky top-0 z-10 h-16 bg-white border-b border-gray-200 flex items-center gap-4 px-4 sm:px-6 shrink-0">

        <!-- Mobile hamburger -->
        <button @click="mobileOpen = !mobileOpen" class="md:hidden text-gray-500 hover:text-gray-700 p-1">
          <Menu :size="20" />
        </button>

        <!-- Page title -->
        <h1 class="font-bold text-gray-800 text-base truncate flex-1">{{ pageTitle }}</h1>

        <!-- Notifications -->
        <div class="relative">
          <button
            @click="notifOpen = !notifOpen"
            class="relative p-2 text-gray-400 hover:text-[#0D47A1] hover:bg-gray-100 rounded-lg transition-colors"
          >
            <Bell :size="18" />
            <span
              v-if="unreadCount > 0"
              class="absolute top-1 right-1 w-4 h-4 bg-[#EF5350] text-white text-[9px] font-bold rounded-full flex items-center justify-center"
            >{{ unreadCount }}</span>
          </button>

          <Transition name="fade-down">
            <div
              v-if="notifOpen"
              class="absolute right-0 top-12 w-80 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden z-50"
            >
              <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                <span class="font-bold text-sm text-gray-800">Notifications</span>
                <button @click="markAllRead(); notifOpen = false" class="text-xs text-[#0D47A1] hover:underline">
                  Mark all read
                </button>
              </div>
              <div class="max-h-64 overflow-y-auto divide-y divide-gray-50">
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

        <!-- User info + logout -->
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-full bg-[#0D47A1] flex items-center justify-center text-white text-xs font-bold shrink-0">
            {{ userInitials }}
          </div>
          <div class="hidden sm:block leading-tight">
            <p class="text-sm font-semibold text-gray-800">{{ auth.user?.name }}</p>
            <p class="text-[11px] text-gray-400 capitalize">{{ auth.roles?.join(', ') }}</p>
          </div>
          <Link
            href="/logout"
            method="post"
            as="button"
            class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
            title="Log out"
          >
            <LogOut :size="16" />
          </Link>
        </div>
      </header>

      <!-- Content -->
      <main class="flex-1 p-4 sm:p-6 overflow-auto">
        <slot />
      </main>
    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { Bell, Menu, LogOut } from 'lucide-vue-next'
import DashboardSidebar from '@/Components/DashboardSidebar.vue'
import { useDashboard } from '@/composables/useDashboard.js'

const { sidebarCollapsed, toggleSidebar, notifications, unreadCount, markAllRead } = useDashboard()

const mobileOpen = ref(false)
const notifOpen  = ref(false)

const page = usePage()
const auth = computed(() => page.props.auth ?? {})

const userInitials = computed(() => {
  const name = auth.value?.user?.name ?? ''
  return name.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase() || '?'
})

const titleMap = {
  '/dashboard':             'Overview',
  '/dashboard/articles':    'Articles',
  '/dashboard/categories':  'Categories',
  '/dashboard/live-scores': 'Live Scores',
  '/dashboard/users':       'Users',
  '/dashboard/roles':       'Roles',
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
.fade-down-enter-active, .fade-down-leave-active { transition: opacity 0.15s, transform 0.15s; }
.fade-down-enter-from, .fade-down-leave-to { opacity: 0; transform: translateY(-6px); }
</style>
