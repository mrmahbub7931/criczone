<template>
  <DashboardLayout>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-lg font-black text-gray-900">Roles</h2>
        <p class="text-sm text-gray-400">{{ roles.length }} roles</p>
      </div>
      <button @click="openCreate" class="flex items-center gap-2 bg-[#0D47A1] hover:bg-[#0a2f6e] text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm">
        <Plus :size="15" /> Create Role
      </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-100">
            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Slug</th>
            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Display Name</th>
            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Description</th>
            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Users</th>
            <th class="px-4 py-3 w-20"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr v-if="loading"><td colspan="5" class="text-center py-12 text-gray-400 text-sm">Loading…</td></tr>
          <tr v-else-if="roles.length === 0"><td colspan="5" class="text-center py-12 text-gray-400 text-sm">No roles found.</td></tr>
          <tr v-for="r in roles" :key="r.id" class="hover:bg-gray-50 transition-colors group">
            <td class="px-5 py-3.5">
              <span class="font-mono text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded-md">{{ r.name }}</span>
            </td>
            <td class="px-4 py-3.5 font-medium text-gray-800">{{ r.display_name }}</td>
            <td class="px-4 py-3.5 text-gray-400 hidden md:table-cell max-w-xs truncate">{{ r.description || '—' }}</td>
            <td class="px-4 py-3.5 hidden sm:table-cell">
              <span class="text-xs font-semibold bg-blue-50 text-[#0D47A1] px-2 py-0.5 rounded-full">{{ r.users_count ?? 0 }}</span>
            </td>
            <td class="px-4 py-3.5">
              <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity justify-end">
                <button @click="openEdit(r)" class="p-1.5 text-gray-400 hover:text-[#0D47A1] hover:bg-blue-50 rounded-lg transition-colors">
                  <Pencil :size="14" />
                </button>
                <button @click="confirmDelete(r)" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                  <Trash2 :size="14" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Create / Edit Modal -->
    <Transition name="modal">
      <div v-if="modal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="closeModal" />
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
          <h3 class="font-black text-gray-900 text-base mb-5">{{ editId ? 'Edit Role' : 'Create Role' }}</h3>

          <div class="space-y-4">
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Slug <span class="text-gray-400 font-normal">(lowercase, no spaces)</span></label>
              <input v-model="form.name" type="text" placeholder="e.g. editor"
                :class="['w-full border rounded-lg px-3 py-2 text-sm font-mono outline-none transition-colors', errors.name ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-[#0D47A1]']" />
              <p v-if="errors.name" class="text-xs text-red-500 mt-1">{{ errors.name }}</p>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Display Name</label>
              <input v-model="form.display_name" type="text" placeholder="e.g. Editor"
                :class="['w-full border rounded-lg px-3 py-2 text-sm outline-none transition-colors', errors.display_name ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-[#0D47A1]']" />
              <p v-if="errors.display_name" class="text-xs text-red-500 mt-1">{{ errors.display_name }}</p>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
              <textarea v-model="form.description" rows="2" placeholder="Short description…"
                :class="['w-full border rounded-lg px-3 py-2 text-sm outline-none resize-none transition-colors', errors.description ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-[#0D47A1]']" />
              <p v-if="errors.description" class="text-xs text-red-500 mt-1">{{ errors.description }}</p>
            </div>

            <p v-if="errors.general" class="text-xs text-red-500 bg-red-50 px-3 py-2 rounded-lg">{{ errors.general }}</p>
          </div>

          <div class="flex gap-3 mt-6">
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
          <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <Trash2 :size="20" class="text-red-500" />
          </div>
          <h3 class="font-black text-gray-900 mb-1">Delete Role?</h3>
          <p class="text-sm text-gray-500 mb-1">
            <span class="font-mono text-xs bg-gray-100 px-1.5 py-0.5 rounded">{{ deleteTarget?.name }}</span>
          </p>
          <p v-if="deleteTarget?.users_count" class="text-xs text-orange-500 mb-4">
            This role is assigned to {{ deleteTarget.users_count }} user(s).
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
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { Plus, Pencil, Trash2 } from 'lucide-vue-next'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const roles   = ref([])
const loading = ref(false)
const modal   = ref(false)
const editId  = ref(null)
const saving  = ref(false)
const errors  = ref({})
const deleteTarget = ref(null)

const blankForm = () => ({ name: '', display_name: '', description: '' })
const form = ref(blankForm())

const fetchRoles = async () => {
  loading.value = true
  const { data } = await axios.get('/api/roles')
  roles.value = data
  loading.value = false
}

onMounted(fetchRoles)

const openCreate = () => { editId.value = null; form.value = blankForm(); errors.value = {}; modal.value = true }
const openEdit   = (r) => { editId.value = r.id; form.value = { name: r.name, display_name: r.display_name, description: r.description ?? '' }; errors.value = {}; modal.value = true }
const closeModal = () => { modal.value = false }

const save = async () => {
  saving.value = true; errors.value = {}
  try {
    editId.value
      ? await axios.put(`/api/roles/${editId.value}`, form.value)
      : await axios.post('/api/roles', form.value)
    modal.value = false
    fetchRoles()
  } catch (e) {
    const data = e.response?.data
    if (data?.errors) {
      Object.keys(data.errors).forEach(k => { errors.value[k] = data.errors[k][0] })
    } else {
      errors.value.general = data?.message ?? 'Something went wrong.'
    }
  } finally {
    saving.value = false
  }
}

const confirmDelete = (r) => { deleteTarget.value = r }
const doDelete = async () => {
  await axios.delete(`/api/roles/${deleteTarget.value.id}`)
  deleteTarget.value = null
  fetchRoles()
}
</script>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
