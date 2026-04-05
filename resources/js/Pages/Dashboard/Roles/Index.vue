<template>
  <DashboardLayout>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-lg font-black text-gray-900">Roles & Permissions</h2>
        <p class="text-sm text-gray-400">{{ roles.length }} roles · {{ totalPermissions }} permissions defined</p>
      </div>
      <button @click="openCreate" class="flex items-center gap-2 bg-[#0D47A1] hover:bg-[#0a2f6e] text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm">
        <Plus :size="15" /> Create Role
      </button>
    </div>

    <!-- Toast -->
    <Transition name="fade-up">
      <div v-if="toast" class="fixed bottom-6 right-6 z-50 bg-gray-900 text-white text-sm font-medium px-4 py-3 rounded-xl shadow-lg">{{ toast }}</div>
    </Transition>

    <!-- Roles table -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mb-8">
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-100">
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Role</th>
            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Description</th>
            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Permissions</th>
            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Users</th>
            <th class="px-4 py-3 w-24"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr v-if="loading"><td colspan="5" class="text-center py-12 text-gray-400 text-sm">Loading…</td></tr>
          <tr v-else-if="roles.length === 0"><td colspan="5" class="text-center py-12 text-gray-400 text-sm">No roles found.</td></tr>
          <tr v-for="r in roles" :key="r.id" class="hover:bg-gray-50 transition-colors group">
            <td class="px-5 py-3.5">
              <div>
                <span class="font-mono text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded-md">{{ r.name }}</span>
                <p class="text-xs text-gray-500 mt-0.5">{{ r.display_name }}</p>
              </div>
            </td>
            <td class="px-4 py-3.5 text-gray-400 text-xs hidden md:table-cell max-w-xs truncate">{{ r.description || '—' }}</td>
            <td class="px-4 py-3.5">
              <div class="flex flex-wrap gap-1 max-w-xs">
                <span v-if="r.name === 'admin'" class="text-[10px] font-bold bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">All permissions</span>
                <template v-else-if="r.permissions?.length">
                  <span v-for="p in r.permissions.slice(0, 4)" :key="p.id"
                    class="text-[10px] font-medium bg-blue-50 text-[#0D47A1] px-1.5 py-0.5 rounded-full">
                    {{ p.name }}
                  </span>
                  <span v-if="r.permissions.length > 4"
                    class="text-[10px] font-medium bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded-full">
                    +{{ r.permissions.length - 4 }} more
                  </span>
                </template>
                <span v-else class="text-[10px] text-gray-300 italic">No permissions</span>
              </div>
            </td>
            <td class="px-4 py-3.5 hidden sm:table-cell">
              <span class="text-xs font-semibold bg-blue-50 text-[#0D47A1] px-2 py-0.5 rounded-full">{{ r.users_count ?? 0 }}</span>
            </td>
            <td class="px-4 py-3.5">
              <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity justify-end">
                <button @click="openEdit(r)" class="p-1.5 text-gray-400 hover:text-[#0D47A1] hover:bg-blue-50 rounded-lg transition-colors" title="Edit role & permissions">
                  <Pencil :size="14" />
                </button>
                <button @click="confirmDelete(r)" :disabled="r.name === 'admin'" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors disabled:opacity-30 disabled:cursor-not-allowed" title="Delete role">
                  <Trash2 :size="14" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- All permissions reference -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
      <div class="px-5 py-3 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
        <h3 class="text-sm font-black text-gray-700">All Permissions</h3>
        <!-- <span class="text-xs text-gray-400">Defined in <code class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-600">config/permissions.php</code> → run <code class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-600">php artisan permissions:sync</code> to add new ones</span> -->
      </div>
      <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="(perms, group) in allPermissions" :key="group" class="bg-gray-50 rounded-xl p-4">
          <p class="text-xs font-black text-gray-500 uppercase tracking-wider mb-2">{{ group.replace('_', ' ') }}</p>
          <div class="flex flex-wrap gap-1.5">
            <span v-for="p in perms" :key="p.id"
              class="text-[11px] font-medium bg-white border border-gray-200 text-gray-600 px-2 py-0.5 rounded-full">
              {{ p.name }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- ── Create / Edit Modal ── -->
    <Transition name="modal">
      <div v-if="modal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="closeModal" />
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[92vh] overflow-y-auto">

          <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between z-10">
            <h3 class="font-black text-gray-900 text-base">{{ editId ? 'Edit Role' : 'Create Role' }}</h3>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600 p-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>

          <div class="p-6 space-y-5">

            <!-- Role details -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="label">Slug <span class="text-gray-400 font-normal">(lowercase, no spaces)</span></label>
                <input v-model="form.name" type="text" placeholder="e.g. editor"
                  :disabled="editId && form.name === 'admin'"
                  :class="['w-full border rounded-lg px-3 py-2 text-sm font-mono outline-none transition-colors disabled:bg-gray-50', errors.name ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-[#0D47A1]']" />
                <p v-if="errors.name" class="text-xs text-red-500 mt-1">{{ errors.name }}</p>
              </div>
              <div>
                <label class="label">Display Name</label>
                <input v-model="form.display_name" type="text" placeholder="e.g. Editor"
                  :class="['w-full border rounded-lg px-3 py-2 text-sm outline-none transition-colors', errors.display_name ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-[#0D47A1]']" />
                <p v-if="errors.display_name" class="text-xs text-red-500 mt-1">{{ errors.display_name }}</p>
              </div>
              <div class="sm:col-span-2">
                <label class="label">Description</label>
                <textarea v-model="form.description" rows="2" placeholder="Short description…"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none resize-none focus:border-[#0D47A1]" />
              </div>
            </div>

            <!-- Permissions -->
            <div>
              <div class="flex items-center justify-between mb-3">
                <label class="text-xs font-black text-gray-700 uppercase tracking-wider">Permissions</label>
                <div class="flex gap-2">
                  <button type="button" @click="selectAll" class="text-xs text-[#0D47A1] hover:underline">Select all</button>
                  <span class="text-gray-300">·</span>
                  <button type="button" @click="clearAll" class="text-xs text-gray-400 hover:underline">Clear all</button>
                </div>
              </div>

              <div v-if="form.name === 'admin'" class="p-3 bg-purple-50 border border-purple-100 rounded-xl text-xs text-purple-600 font-medium">
                The <strong>admin</strong> role automatically has all permissions — no need to assign them individually.
              </div>

              <div v-else-if="permissionsLoading" class="text-center py-6 text-gray-400 text-sm">Loading permissions…</div>

              <div v-else class="space-y-4">
                <div v-for="(perms, group) in allPermissions" :key="group" class="border border-gray-100 rounded-xl overflow-hidden">
                  <!-- Group header -->
                  <div class="flex items-center justify-between px-4 py-2.5 bg-gray-50 border-b border-gray-100">
                    <span class="text-xs font-black text-gray-600 uppercase tracking-wider">{{ group.replace('_', ' ') }}</span>
                    <button type="button" @click="toggleGroup(perms)" class="text-[10px] text-[#0D47A1] hover:underline font-medium">
                      {{ groupAllSelected(perms) ? 'Deselect all' : 'Select all' }}
                    </button>
                  </div>
                  <!-- Permission checkboxes -->
                  <div class="grid grid-cols-2 sm:grid-cols-3 gap-0 divide-x divide-y divide-gray-50">
                    <label v-for="p in perms" :key="p.id"
                      class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-blue-50/50 cursor-pointer transition-colors">
                      <input
                        type="checkbox"
                        :value="p.id"
                        v-model="form.permissions"
                        class="w-3.5 h-3.5 accent-[#0D47A1] rounded"
                      />
                      <div>
                        <p class="text-xs font-semibold text-gray-700">{{ p.name.split('.')[1] }}</p>
                        <p class="text-[10px] text-gray-400">{{ p.name }}</p>
                      </div>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <p v-if="errors.general" class="text-xs text-red-500 bg-red-50 px-3 py-2 rounded-lg">{{ errors.general }}</p>
          </div>

          <div class="sticky bottom-0 bg-white border-t border-gray-100 px-6 py-4 flex gap-3">
            <button @click="closeModal" class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
            <button @click="save" :disabled="saving"
              class="flex-1 bg-[#0D47A1] hover:bg-[#0a2f6e] disabled:opacity-50 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors">
              {{ saving ? 'Saving…' : (editId ? 'Save Changes' : 'Create Role') }}
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
          <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4"><Trash2 :size="20" class="text-red-500" /></div>
          <h3 class="font-black text-gray-900 mb-1">Delete Role?</h3>
          <p class="text-sm text-gray-500 mb-1">
            <span class="font-mono text-xs bg-gray-100 px-1.5 py-0.5 rounded">{{ deleteTarget?.name }}</span>
          </p>
          <p v-if="deleteTarget?.users_count" class="text-xs text-orange-500 mb-4">
            ⚠ This role is assigned to {{ deleteTarget.users_count }} user(s).
          </p>
          <p v-else class="mb-4" />
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
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { Plus, Pencil, Trash2 } from 'lucide-vue-next'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

// ── State ──────────────────────────────────────────────────────────────────────
const roles              = ref([])
const allPermissions     = ref({})    // { group: [Permission, ...] }
const loading            = ref(false)
const permissionsLoading = ref(false)
const modal              = ref(false)
const editId             = ref(null)
const saving             = ref(false)
const errors             = ref({})
const deleteTarget       = ref(null)
const toast              = ref('')

const blankForm = () => ({ name: '', display_name: '', description: '', permissions: [] })
const form = ref(blankForm())

// ── Computed ───────────────────────────────────────────────────────────────────
const totalPermissions = computed(() =>
  Object.values(allPermissions.value).reduce((sum, arr) => sum + arr.length, 0)
)

// ── Fetch ──────────────────────────────────────────────────────────────────────
const fetchRoles = async () => {
  loading.value = true
  const { data } = await axios.get('/api/roles')
  roles.value = data
  loading.value = false
}

const fetchPermissions = async () => {
  permissionsLoading.value = true
  const { data } = await axios.get('/api/permissions')
  allPermissions.value = data
  permissionsLoading.value = false
}

onMounted(() => { fetchRoles(); fetchPermissions() })

// ── Permission checkbox helpers ────────────────────────────────────────────────
const groupAllSelected = (perms) => perms.every(p => form.value.permissions.includes(p.id))

const toggleGroup = (perms) => {
  if (groupAllSelected(perms)) {
    form.value.permissions = form.value.permissions.filter(id => !perms.map(p => p.id).includes(id))
  } else {
    const ids = perms.map(p => p.id)
    form.value.permissions = [...new Set([...form.value.permissions, ...ids])]
  }
}

const selectAll = () => {
  form.value.permissions = Object.values(allPermissions.value).flat().map(p => p.id)
}

const clearAll = () => { form.value.permissions = [] }

// ── Modal ──────────────────────────────────────────────────────────────────────
const openCreate = () => {
  editId.value = null
  form.value   = blankForm()
  errors.value = {}
  modal.value  = true
}

const openEdit = (r) => {
  editId.value = r.id
  form.value   = {
    name:        r.name,
    display_name: r.display_name,
    description: r.description ?? '',
    permissions: r.permissions?.map(p => p.id) ?? [],
  }
  errors.value = {}
  modal.value  = true
}

const closeModal = () => { modal.value = false }

// ── Save ───────────────────────────────────────────────────────────────────────
const showToast = (msg) => { toast.value = msg; setTimeout(() => { toast.value = '' }, 2500) }

const save = async () => {
  saving.value = true
  errors.value = {}
  try {
    if (editId.value) {
      await axios.put(`/api/roles/${editId.value}`, {
        name: form.value.name, display_name: form.value.display_name, description: form.value.description,
      })
      await axios.put(`/api/roles/${editId.value}/permissions`, { permissions: form.value.permissions })
    } else {
      const { data: role } = await axios.post('/api/roles', {
        name: form.value.name, display_name: form.value.display_name, description: form.value.description,
      })
      await axios.put(`/api/roles/${role.id}/permissions`, { permissions: form.value.permissions })
    }
    modal.value = false
    showToast(editId.value ? 'Role updated.' : 'Role created.')
    fetchRoles()
  } catch (e) {
    const data = e.response?.data
    if (data?.errors) Object.keys(data.errors).forEach(k => { errors.value[k] = data.errors[k][0] })
    else errors.value.general = data?.message ?? 'Something went wrong.'
  } finally {
    saving.value = false
  }
}

// ── Delete ─────────────────────────────────────────────────────────────────────
const confirmDelete = (r) => { deleteTarget.value = r }
const doDelete = async () => {
  await axios.delete(`/api/roles/${deleteTarget.value.id}`)
  deleteTarget.value = null
  showToast('Role deleted.')
  fetchRoles()
}
</script>

<style scoped>
.label { @apply block text-xs font-semibold text-gray-600 mb-1; }
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
.fade-up-enter-active, .fade-up-leave-active { transition: opacity 0.2s, transform 0.2s; }
.fade-up-enter-from, .fade-up-leave-to { opacity: 0; transform: translateY(8px); }
</style>
