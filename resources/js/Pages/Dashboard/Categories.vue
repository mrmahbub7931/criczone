<template>
  <DashboardLayout>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-lg font-black text-gray-900">Categories</h2>
        <p class="text-sm text-gray-400">{{ categoriesData.length }} categories</p>
      </div>
      <button
        @click="openAdd"
        class="flex items-center gap-2 bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm"
      >
        <Plus :size="16" /> Add Category
      </button>
    </div>

    <!-- Categories grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div
        v-for="cat in categoriesData"
        :key="cat.id"
        class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 group hover:shadow-md transition-shadow"
      >
        <div class="flex items-start justify-between mb-4">
          <div class="flex items-center gap-3">
            <div
              class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
              :style="{ backgroundColor: cat.color + '20' }"
            >
              <Tag :size="18" :style="{ color: cat.color }" />
            </div>
            <div>
              <h3 class="font-bold text-gray-900 text-sm">{{ cat.name }}</h3>
              <p class="text-xs text-gray-400">{{ cat.slug }}</p>
            </div>
          </div>
          <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
            <button @click="openEdit(cat)" class="p-1.5 text-gray-400 hover:text-primary hover:bg-blue-50 rounded-lg transition-colors">
              <Pencil :size="13" />
            </button>
            <button @click="confirmDelete(cat)" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
              <Trash2 :size="13" />
            </button>
          </div>
        </div>

        <p class="text-xs text-gray-500 mb-4 line-clamp-2">{{ cat.description }}</p>

        <div class="flex items-center justify-between">
          <span class="text-xs font-semibold text-gray-400">{{ cat.articleCount }} articles</span>
          <div class="h-1.5 flex-1 mx-3 bg-gray-100 rounded-full overflow-hidden">
            <div
              class="h-full rounded-full transition-all duration-500"
              :style="{ width: barWidth(cat.articleCount) + '%', backgroundColor: cat.color }"
            />
          </div>
          <div class="w-3 h-3 rounded-full border-2 border-white shadow-sm shrink-0" :style="{ backgroundColor: cat.color }" />
        </div>
      </div>
    </div>

    <!-- ── Add / Edit Modal ── -->
    <Transition name="modal">
      <div v-if="modal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="modal = false" />
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
          <h3 class="font-black text-gray-900 text-base mb-5">{{ editId ? 'Edit Category' : 'Add Category' }}</h3>

          <div class="space-y-4">
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Name *</label>
              <input v-model="form.name" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary" placeholder="Category name" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Slug *</label>
              <input v-model="form.slug" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary" placeholder="category-slug" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
              <textarea v-model="form.description" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary resize-none" placeholder="Short description..." />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Color</label>
              <div class="flex items-center gap-3">
                <input v-model="form.color" type="color" class="w-10 h-10 rounded-lg border border-gray-200 cursor-pointer p-0.5" />
                <input v-model="form.color" type="text" class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm font-mono outline-none focus:border-primary" placeholder="#000000" />
              </div>
            </div>
          </div>

          <div class="flex gap-3 mt-6">
            <button @click="modal = false" class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
            <button @click="saveCategory" :disabled="!form.name || !form.slug" class="flex-1 bg-primary hover:bg-primary-dark disabled:opacity-50 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors">
              {{ editId ? 'Save Changes' : 'Add Category' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- ── Delete Confirm ── -->
    <Transition name="modal">
      <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="deleteTarget = null" />
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
          <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <Trash2 :size="20" class="text-red-500" />
          </div>
          <h3 class="font-black text-gray-900 mb-2">Delete Category?</h3>
          <p class="text-sm text-gray-500 mb-5">
            "{{ deleteTarget?.name }}" has {{ deleteTarget?.articleCount }} articles. This won't delete the articles.
          </p>
          <div class="flex gap-3">
            <button @click="deleteTarget = null" class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
            <button @click="doDelete" class="flex-1 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors">Delete</button>
          </div>
        </div>
      </div>
    </Transition>

  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Plus, Pencil, Trash2, Tag } from 'lucide-vue-next'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { useDashboard } from '@/composables/useDashboard.js'

const { categoriesData, addCategory, updateCategory, deleteCategory } = useDashboard()

const modal        = ref(false)
const editId       = ref(null)
const deleteTarget = ref(null)

const blankForm = () => ({ name: '', slug: '', description: '', color: '#0D47A1' })
const form = ref(blankForm())

const maxCount = computed(() => Math.max(...categoriesData.map(c => c.articleCount), 1))
const barWidth = (count) => Math.round((count / maxCount.value) * 100)

const openAdd = () => {
  editId.value = null
  form.value = blankForm()
  modal.value = true
}

const openEdit = (cat) => {
  editId.value = cat.id
  form.value = { name: cat.name, slug: cat.slug, description: cat.description, color: cat.color }
  modal.value = true
}

const saveCategory = () => {
  if (editId.value) {
    updateCategory(editId.value, { ...form.value })
  } else {
    addCategory({ ...form.value })
  }
  modal.value = false
}

const confirmDelete = (cat) => { deleteTarget.value = cat }
const doDelete = () => {
  deleteCategory(deleteTarget.value.id)
  deleteTarget.value = null
}
</script>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
