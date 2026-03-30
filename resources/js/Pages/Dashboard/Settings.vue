<template>
  <DashboardLayout>

    <div class="max-w-2xl space-y-6">

      <!-- Site Settings -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h2 class="font-bold text-gray-900 mb-5">Site Settings</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Site Name</label>
            <input v-model="site.name" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Tagline</label>
            <input v-model="site.tagline" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Articles per page</label>
            <select v-model="site.perPage" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary">
              <option :value="6">6</option>
              <option :value="12">12</option>
              <option :value="24">24</option>
            </select>
          </div>
          <div class="flex items-center justify-between pt-2">
            <div>
              <p class="text-sm font-medium text-gray-700">Show live score ticker</p>
              <p class="text-xs text-gray-400">Display live scores in the public navbar</p>
            </div>
            <button
              @click="site.showTicker = !site.showTicker"
              :class="['relative w-10 h-5 rounded-full transition-colors', site.showTicker ? 'bg-primary' : 'bg-gray-200']"
            >
              <span :class="['absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform', site.showTicker ? 'translate-x-5' : 'translate-x-0.5']" />
            </button>
          </div>
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-700">Maintenance mode</p>
              <p class="text-xs text-gray-400">Show maintenance page to public visitors</p>
            </div>
            <button
              @click="site.maintenance = !site.maintenance"
              :class="['relative w-10 h-5 rounded-full transition-colors', site.maintenance ? 'bg-secondary' : 'bg-gray-200']"
            >
              <span :class="['absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform', site.maintenance ? 'translate-x-5' : 'translate-x-0.5']" />
            </button>
          </div>
        </div>
        <button @click="saveSite" class="mt-5 bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors">
          Save Settings
        </button>
      </div>

      <!-- Profile Settings -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h2 class="font-bold text-gray-900 mb-5">Profile</h2>
        <div class="flex items-center gap-4 mb-5">
          <div class="w-16 h-16 rounded-full bg-primary flex items-center justify-center text-white text-xl font-black">
            {{ profile.name.charAt(0) }}
          </div>
          <div>
            <p class="font-semibold text-gray-800">{{ profile.name }}</p>
            <p class="text-sm text-gray-400">{{ profile.role }}</p>
          </div>
        </div>
        <div class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Display Name</label>
            <input v-model="profile.name" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Email</label>
            <input v-model="profile.email" type="email" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary" />
          </div>
        </div>
        <button @click="saveProfile" class="mt-5 bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors">
          Update Profile
        </button>
      </div>

      <!-- Danger Zone -->
      <div class="bg-white rounded-xl border border-red-100 shadow-sm p-6">
        <h2 class="font-bold text-red-600 mb-2">Danger Zone</h2>
        <p class="text-sm text-gray-500 mb-4">These actions are irreversible. Please proceed with caution.</p>
        <button class="border border-red-300 text-red-600 hover:bg-red-50 text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
          Clear All Draft Articles
        </button>
      </div>
    </div>

    <!-- Toast notification -->
    <Transition name="fade-up">
      <div v-if="toast" class="fixed bottom-6 right-6 bg-gray-900 text-white text-sm font-medium px-4 py-3 rounded-xl shadow-lg z-50">
        {{ toast }}
      </div>
    </Transition>

  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const site = ref({
  name:        'CricZone',
  tagline:     'Your #1 Source for Cricket News',
  perPage:     12,
  showTicker:  true,
  maintenance: false,
})

const profile = ref({
  name:  'Admin',
  email: 'admin@criczone.com',
  role:  'Super Admin',
})

const toast = ref('')

const showToast = (msg) => {
  toast.value = msg
  setTimeout(() => { toast.value = '' }, 2500)
}

const saveSite    = () => showToast('Site settings saved!')
const saveProfile = () => showToast('Profile updated!')
</script>

<style scoped>
.fade-up-enter-active, .fade-up-leave-active { transition: opacity 0.2s, transform 0.2s; }
.fade-up-enter-from, .fade-up-leave-to { opacity: 0; transform: translateY(8px); }
</style>
