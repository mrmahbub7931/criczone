<template>
  <DashboardLayout>
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-lg font-black text-gray-900">Users</h2>
        <p class="text-sm text-gray-400">{{ meta.total ?? 0 }} total</p>
      </div>
      <button @click="openCreate" class="flex items-center gap-2 bg-[#0D47A1] hover:bg-[#0a2f6e] text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm">
        <Plus :size="15" /> Create User
      </button>
    </div>

    <!-- Search -->
    <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-3 py-2 mb-4 w-full max-w-sm">
      <Search :size="14" class="text-gray-400 shrink-0" />
      <input v-model="search" type="text" placeholder="Search name or email…"
        class="text-sm text-gray-700 placeholder-gray-400 outline-none w-full bg-transparent" />
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
              <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">User</th>
              <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Email</th>
              <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Roles</th>
              <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Status</th>
              <th class="px-4 py-3 w-20"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="loading"><td colspan="5" class="text-center py-12 text-gray-400 text-sm">Loading…</td></tr>
            <tr v-else-if="users.length === 0"><td colspan="5" class="text-center py-12 text-gray-400 text-sm">No users found.</td></tr>
            <tr v-for="u in users" :key="u.id" class="hover:bg-gray-50 transition-colors group">
              <td class="px-5 py-3.5">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-[#0D47A1] flex items-center justify-center text-white text-xs font-bold shrink-0">
                    {{ initials(u.name) }}
                  </div>
                  <span class="font-medium text-gray-800">{{ u.name }}</span>
                </div>
              </td>
              <td class="px-4 py-3.5 text-gray-500 hidden md:table-cell">{{ u.email }}</td>
              <td class="px-4 py-3.5">
                <div class="flex flex-wrap gap-1">
                  <span v-if="u.roles?.length === 0" class="text-xs text-gray-300">—</span>
                  <span v-for="r in u.roles" :key="r.id"
                    :class="['text-[11px] font-semibold px-2 py-0.5 rounded-full', roleClass(r.name)]">
                    {{ r.name }}
                  </span>
                </div>
              </td>
              <td class="px-4 py-3.5 hidden sm:table-cell">
                <span :class="['text-[11px] font-semibold px-2 py-0.5 rounded-full', statusClass(u.status)]">
                  {{ u.status }}
                </span>
              </td>
              <td class="px-4 py-3.5">
                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity justify-end">
                  <button @click="openEdit(u)" class="p-1.5 text-gray-400 hover:text-[#0D47A1] hover:bg-blue-50 rounded-lg transition-colors">
                    <Pencil :size="14" />
                  </button>
                  <button @click="confirmDelete(u)" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                    <Trash2 :size="14" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="meta.last_page > 1" class="flex items-center justify-between px-5 py-3 border-t border-gray-100">
        <span class="text-xs text-gray-400">Page {{ meta.current_page }} of {{ meta.last_page }}</span>
        <div class="flex gap-1">
          <button @click="page--" :disabled="page === 1"
            class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-40 transition-colors">Prev</button>
          <button @click="page++" :disabled="page === meta.last_page"
            class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-40 transition-colors">Next</button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <Transition name="modal">
      <div v-if="modal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="modal = false" />
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
          <h3 class="font-black text-gray-900 text-base mb-5">{{ editId ? 'Edit User' : 'Create User' }}</h3>
          <div class="space-y-4">
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Name *</label>
              <input v-model="form.name" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Email *</label>
              <input v-model="form.email" type="email" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">{{ editId ? 'New Password (leave blank to keep)' : 'Password *' }}</label>
              <input v-model="form.password" type="password" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
              <select v-model="form.status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="banned">Banned</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Roles</label>
              <div class="flex flex-wrap gap-2 mt-1">
                <label v-for="r in allRoles" :key="r.id" class="flex items-center gap-1.5 text-sm text-gray-700 cursor-pointer">
                  <input type="checkbox" :value="r.id" v-model="form.roles" class="accent-[#0D47A1]" />
                  {{ r.display_name }}
                </label>
              </div>
            </div>
            <p v-if="error" class="text-xs text-red-500">{{ error }}</p>
          </div>
          <div class="flex gap-3 mt-6">
            <button @click="modal = false" class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
            <button @click="save" :disabled="saving" class="flex-1 bg-[#0D47A1] hover:bg-[#0a2f6e] disabled:opacity-50 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors">
              {{ saving ? 'Saving…' : (editId ? 'Save Changes' : 'Create User') }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Delete Confirm -->
    <Transition name="modal">
      <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="deleteTarget = null" />
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
          <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <Trash2 :size="20" class="text-red-500" />
          </div>
          <h3 class="font-black text-gray-900 mb-2">Delete User?</h3>
          <p class="text-sm text-gray-500 mb-5">{{ deleteTarget?.name }}</p>
          <div class="flex gap-3">
            <button @click="deleteTarget = null" class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50">Cancel</button>
            <button @click="doDelete" class="flex-1 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors">Delete</button>
          </div>
        </div>
      </div>
    </Transition>

  </DashboardLayout>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import axios from 'axios'
import { Plus, Search, Pencil, Trash2 } from 'lucide-vue-next'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const users   = ref([])
const meta    = ref({})
const allRoles = ref([])
const loading = ref(false)
const page    = ref(1)
const search  = ref('')

const modal       = ref(false)
const editId      = ref(null)
const saving      = ref(false)
const error       = ref('')
const deleteTarget = ref(null)

const blankForm = () => ({ name: '', email: '', password: '', status: 'active', roles: [] })
const form = ref(blankForm())

const fetchUsers = async () => {
  loading.value = true
  const { data } = await axios.get('/api/users', { params: { page: page.value, search: search.value, per_page: 10 } })
  users.value = data.data
  meta.value  = data.meta ?? data
  loading.value = false
}

const fetchRoles = async () => {
  const { data } = await axios.get('/api/roles')
  allRoles.value = data
}

onMounted(() => { fetchUsers(); fetchRoles() })
watch([page, search], fetchUsers)
watch(search, () => { page.value = 1 })

const openCreate = () => { editId.value = null; form.value = blankForm(); error.value = ''; modal.value = true }
const openEdit   = (u) => {
  editId.value = u.id
  form.value   = { name: u.name, email: u.email, password: '', status: u.status, roles: u.roles.map(r => r.id) }
  error.value  = ''
  modal.value  = true
}

const save = async () => {
  saving.value = true; error.value = ''
  try {
    const payload = { ...form.value }
    if (editId.value) {
      await axios.put(`/api/users/${editId.value}`, payload)
    } else {
      await axios.post('/api/users', payload)
    }
    modal.value = false
    fetchUsers()
  } catch (e) {
    error.value = e.response?.data?.message ?? Object.values(e.response?.data?.errors ?? {})[0]?.[0] ?? 'Error saving user.'
  } finally {
    saving.value = false
  }
}

const confirmDelete = (u) => { deleteTarget.value = u }
const doDelete = async () => {
  await axios.delete(`/api/users/${deleteTarget.value.id}`)
  deleteTarget.value = null
  fetchUsers()
}

const initials = (name) => name?.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase() || '?'

const roleClass = (name) => ({
  admin:  'bg-blue-100 text-blue-700',
  editor: 'bg-green-100 text-green-700',
  viewer: 'bg-gray-100 text-gray-600',
})[name] ?? 'bg-purple-100 text-purple-700'

const statusClass = (s) => ({
  active:   'bg-green-100 text-green-700',
  inactive: 'bg-gray-100 text-gray-500',
  banned:   'bg-red-100 text-red-600',
})[s] ?? 'bg-gray-100 text-gray-500'
</script>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
