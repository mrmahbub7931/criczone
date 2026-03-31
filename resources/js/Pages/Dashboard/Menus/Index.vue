<template>
  <DashboardLayout title="Menus">

    <div class="flex gap-6 min-h-0">

      <!-- ── Left panel: menu list ──────────────────────────────────────── -->
      <div class="w-72 shrink-0 flex flex-col gap-4">

        <!-- Header -->
        <div class="flex items-center justify-between">
          <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Menus</h2>
          <button
            @click="openMenuModal()"
            class="inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-[#0D47A1] hover:bg-[#0a2f6e] px-3 py-1.5 rounded-lg transition-colors"
          >
            <Plus :size="13" /> New Menu
          </button>
        </div>

        <!-- Menu list -->
        <div class="space-y-1.5">
          <div
            v-if="menusLoading"
            v-for="i in 4" :key="i"
            class="h-14 bg-gray-100 rounded-xl animate-pulse"
          />
          <button
            v-else
            v-for="m in menus" :key="m.id"
            @click="selectMenu(m)"
            :class="[
              'w-full text-left px-4 py-3 rounded-xl border-2 transition-all',
              activeMenu?.id === m.id
                ? 'border-[#0D47A1] bg-[#0D47A1]/5'
                : 'border-transparent bg-white hover:border-gray-200 hover:bg-gray-50',
            ]"
          >
            <div class="flex items-center justify-between">
              <span class="font-semibold text-sm text-gray-800">{{ m.name }}</span>
              <span class="text-[10px] font-bold text-white bg-gray-400 rounded-full px-2 py-0.5">{{ m.all_items_count ?? 0 }}</span>
            </div>
            <span class="text-xs text-gray-400 font-mono">{{ m.location }}</span>
          </button>
          <p v-if="!menusLoading && menus.length === 0" class="text-sm text-gray-400 text-center py-6">
            No menus yet.<br/>Create your first menu.
          </p>
        </div>
      </div>

      <!-- ── Right panel: items editor ─────────────────────────────────── -->
      <div class="flex-1 flex flex-col gap-4 min-w-0">

        <!-- Empty state -->
        <div v-if="!activeMenu" class="flex-1 flex items-center justify-center">
          <div class="text-center">
            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
              <MenuIcon :size="28" class="text-gray-300" />
            </div>
            <p class="text-sm font-medium text-gray-500">Select a menu to manage its items</p>
          </div>
        </div>

        <template v-else>
          <!-- Panel header -->
          <div class="flex items-center justify-between">
            <div>
              <h2 class="font-bold text-gray-900">{{ activeMenu.name }}</h2>
              <p class="text-xs text-gray-400 font-mono">location: {{ activeMenu.location }}</p>
            </div>
            <div class="flex items-center gap-2">
              <button
                @click="openMenuModal(activeMenu)"
                class="text-xs font-medium text-gray-500 hover:text-gray-800 border border-gray-200 hover:border-gray-400 px-3 py-1.5 rounded-lg transition-colors"
              >
                <Pencil :size="12" class="inline -mt-0.5 mr-1" />Edit
              </button>
              <button
                @click="confirmDeleteMenu(activeMenu)"
                class="text-xs font-medium text-red-500 hover:text-red-700 border border-red-200 hover:border-red-400 px-3 py-1.5 rounded-lg transition-colors"
              >
                <Trash2 :size="12" class="inline -mt-0.5 mr-1" />Delete
              </button>
              <button
                @click="openItemModal()"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-[#0D47A1] hover:bg-[#0a2f6e] px-3 py-1.5 rounded-lg transition-colors"
              >
                <Plus :size="13" /> Add Item
              </button>
            </div>
          </div>

          <!-- Items list -->
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div v-if="itemsLoading" class="p-4 space-y-3">
              <div v-for="i in 4" :key="i" class="h-12 bg-gray-100 rounded-lg animate-pulse" />
            </div>

            <div v-else-if="items.length === 0" class="py-12 text-center text-sm text-gray-400">
              No items in this menu yet. Click "Add Item" to get started.
            </div>

            <div v-else class="divide-y divide-gray-50">
              <!-- Top-level items -->
              <template v-for="item in rootItems" :key="item.id">
                <MenuItemRow
                  :item="item"
                  :depth="0"
                  @edit="openItemModal"
                  @delete="confirmDeleteItem"
                  @move-up="moveItem(item, 'up')"
                  @move-down="moveItem(item, 'down')"
                />
                <!-- Children -->
                <MenuItemRow
                  v-for="child in childrenOf(item.id)"
                  :key="child.id"
                  :item="child"
                  :depth="1"
                  @edit="openItemModal"
                  @delete="confirmDeleteItem"
                  @move-up="moveItem(child, 'up')"
                  @move-down="moveItem(child, 'down')"
                />
              </template>
            </div>
          </div>
        </template>
      </div>
    </div>

    <!-- ── Toast ──────────────────────────────────────────────────────────── -->
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0 translate-y-2"
    >
      <div
        v-if="toast.show"
        :class="[
          'fixed bottom-6 right-6 z-50 flex items-center gap-3 px-5 py-3 rounded-xl shadow-xl text-sm font-semibold text-white max-w-sm',
          toast.type === 'success' ? 'bg-emerald-600' : 'bg-red-600'
        ]"
      >
        <CheckCircle2 v-if="toast.type === 'success'" :size="16" />
        <XCircle v-else :size="16" />
        {{ toast.message }}
      </div>
    </Transition>

    <!-- ── Menu Modal ─────────────────────────────────────────────────────── -->
    <Transition enter-active-class="transition duration-150" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
      <div v-if="menuModal.open" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="menuModal.open = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
          <h3 class="text-lg font-bold text-gray-900 mb-5">{{ menuModal.editing ? 'Edit Menu' : 'New Menu' }}</h3>

          <div class="space-y-4">
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Menu Name</label>
              <input v-model="menuForm.name" type="text" placeholder="e.g. Header Navigation" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/30 focus:border-[#0D47A1]" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Location <span class="text-gray-400 normal-case font-normal">(unique key)</span></label>
              <input v-model="menuForm.location" type="text" placeholder="e.g. header" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/30 focus:border-[#0D47A1]" :disabled="!!menuModal.editing" />
              <p class="text-xs text-gray-400 mt-1">Use: <code class="bg-gray-100 px-1 rounded">header</code>, <code class="bg-gray-100 px-1 rounded">footer_quick</code>, <code class="bg-gray-100 px-1 rounded">footer_categories</code>, <code class="bg-gray-100 px-1 rounded">footer_legal</code></p>
            </div>
          </div>

          <div class="flex justify-end gap-3 mt-6">
            <button @click="menuModal.open = false" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 border border-gray-200 hover:border-gray-400 rounded-lg transition-colors">Cancel</button>
            <button @click="saveMenu" :disabled="menuSaving" class="px-5 py-2 text-sm font-semibold text-white bg-[#0D47A1] hover:bg-[#0a2f6e] rounded-lg transition-colors disabled:opacity-60">
              {{ menuSaving ? 'Saving…' : 'Save' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- ── Item Modal ─────────────────────────────────────────────────────── -->
    <Transition enter-active-class="transition duration-150" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
      <div v-if="itemModal.open" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="itemModal.open = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
          <h3 class="text-lg font-bold text-gray-900 mb-5">{{ itemModal.editing ? 'Edit Menu Item' : 'Add Menu Item' }}</h3>

          <div class="space-y-4">
            <!-- Label -->
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Label</label>
              <input v-model="itemForm.label" type="text" placeholder="e.g. Home" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/30 focus:border-[#0D47A1]" />
            </div>

            <!-- Type -->
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Link Type</label>
              <div class="flex gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" v-model="itemForm.type" value="custom" class="accent-[#0D47A1]" />
                  <span class="text-sm text-gray-700">Custom URL</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" v-model="itemForm.type" value="page" class="accent-[#0D47A1]" />
                  <span class="text-sm text-gray-700">Page</span>
                </label>
              </div>
            </div>

            <!-- URL (custom) -->
            <div v-if="itemForm.type === 'custom'">
              <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">URL</label>
              <input v-model="itemForm.url" type="text" placeholder="e.g. / or /category/news or https://…" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/30 focus:border-[#0D47A1]" />
            </div>

            <!-- Page picker -->
            <div v-else>
              <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Page</label>
              <select v-model="itemForm.page_id" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/30 focus:border-[#0D47A1] bg-white">
                <option :value="null">— Select a page —</option>
                <option v-for="p in pages" :key="p.id" :value="p.id">{{ p.title }}</option>
              </select>
            </div>

            <!-- Parent -->
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Parent Item <span class="text-gray-400 normal-case font-normal">(optional sub-item)</span></label>
              <select v-model="itemForm.parent_id" class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0D47A1]/30 focus:border-[#0D47A1] bg-white">
                <option :value="null">— Top level —</option>
                <option v-for="it in parentableItems" :key="it.id" :value="it.id">{{ it.label }}</option>
              </select>
            </div>

            <!-- Target -->
            <div class="flex items-center gap-3">
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" v-model="openInNewTab" class="accent-[#0D47A1]" />
                <span class="text-sm text-gray-700">Open in new tab</span>
              </label>
            </div>
          </div>

          <div class="flex justify-end gap-3 mt-6">
            <button @click="itemModal.open = false" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 border border-gray-200 hover:border-gray-400 rounded-lg transition-colors">Cancel</button>
            <button @click="saveItem" :disabled="itemSaving" class="px-5 py-2 text-sm font-semibold text-white bg-[#0D47A1] hover:bg-[#0a2f6e] rounded-lg transition-colors disabled:opacity-60">
              {{ itemSaving ? 'Saving…' : 'Save Item' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- ── Delete confirm ─────────────────────────────────────────────────── -->
    <Transition enter-active-class="transition duration-150" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
      <div v-if="deleteModal.open" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="deleteModal.open = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
          <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
            <Trash2 :size="22" class="text-red-500" />
          </div>
          <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Delete {{ deleteModal.type === 'menu' ? 'Menu' : 'Item' }}</h3>
          <p class="text-sm text-gray-500 text-center mb-6">
            <template v-if="deleteModal.type === 'menu'">
              Delete "<strong>{{ deleteModal.target?.name }}</strong>"? All menu items will be removed.
            </template>
            <template v-else>
              Delete "<strong>{{ deleteModal.target?.label }}</strong>"? Child items will also be removed.
            </template>
          </p>
          <div class="flex gap-3">
            <button @click="deleteModal.open = false" class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">Cancel</button>
            <button @click="executeDelete" :disabled="deleteModal.loading" class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl transition-colors disabled:opacity-60">
              {{ deleteModal.loading ? 'Deleting…' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue'
import axios from 'axios'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import {
  Plus, Pencil, Trash2, CheckCircle2, XCircle, Menu as MenuIcon,
} from 'lucide-vue-next'

// ── Sub-component: single item row ──────────────────────────────────────────
const MenuItemRow = {
  props: ['item', 'depth'],
  emits: ['edit', 'delete', 'move-up', 'move-down'],
  setup(props, { emit }) {
    return { emit }
  },
  render() {
    const { item, depth } = this
    const indent = depth > 0 ? 'pl-12' : 'pl-4'
    const resolvedUrl = item.type === 'page' && item.page
      ? `/pages/${item.page.slug}`
      : (item.url || '#')

    return h('div', {
      class: `flex items-center gap-3 pr-4 py-3 hover:bg-gray-50 group ${indent}`,
    }, [
      // Drag handle / depth indicator
      depth > 0
        ? h('div', { class: 'w-4 h-4 border-l-2 border-b-2 border-gray-200 rounded-bl-md shrink-0 -mt-2 mr-1' })
        : h('div', { class: 'w-1.5 h-4 rounded-full bg-[#0D47A1]/20 shrink-0' }),

      h('div', { class: 'flex-1 min-w-0' }, [
        h('p', { class: 'text-sm font-semibold text-gray-800 truncate' }, item.label),
        h('p', { class: 'text-xs text-gray-400 font-mono truncate' }, resolvedUrl),
      ]),

      // Type badge
      h('span', {
        class: `text-[10px] font-bold uppercase px-2 py-0.5 rounded-full ${item.type === 'page' ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-500'}`,
      }, item.type),

      // Target badge
      item.target === '_blank'
        ? h('span', { class: 'text-[10px] text-gray-400 hidden group-hover:inline' }, '↗ new tab')
        : null,

      // Actions
      h('div', { class: 'flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity' }, [
        h('button', {
          onClick: () => this.emit('move-up'),
          class: 'w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-200 transition-colors text-xs font-bold',
          title: 'Move up',
        }, '↑'),
        h('button', {
          onClick: () => this.emit('move-down'),
          class: 'w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-200 transition-colors text-xs font-bold',
          title: 'Move down',
        }, '↓'),
        h('button', {
          onClick: () => this.emit('edit', item),
          class: 'w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-[#0D47A1] hover:bg-blue-50 transition-colors',
        }, h(resolveComponent('Pencil') || 'span', { size: 13 })),
        h('button', {
          onClick: () => this.emit('delete', item),
          class: 'w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors',
        }, h(resolveComponent('Trash2') || 'span', { size: 13 })),
      ]),
    ])
  },
}

// ── State ────────────────────────────────────────────────────────────────────
const menus       = ref([])
const menusLoading = ref(true)
const activeMenu  = ref(null)
const items       = ref([])
const itemsLoading = ref(false)
const pages       = ref([])

// Toast
const toast = reactive({ show: false, message: '', type: 'success' })
const showToast = (message, type = 'success') => {
  toast.message = message; toast.type = type; toast.show = true
  setTimeout(() => { toast.show = false }, 3500)
}

// Menu modal
const menuModal = reactive({ open: false, editing: null })
const menuForm  = reactive({ name: '', location: '' })
const menuSaving = ref(false)

// Item modal
const itemModal  = reactive({ open: false, editing: null })
const itemForm   = reactive({ label: '', type: 'custom', url: '', page_id: null, parent_id: null, target: '_self' })
const itemSaving  = ref(false)
const openInNewTab = computed({
  get: () => itemForm.target === '_blank',
  set: (v) => { itemForm.target = v ? '_blank' : '_self' },
})

// Delete modal
const deleteModal = reactive({ open: false, type: '', target: null, loading: false })

// ── Computed ─────────────────────────────────────────────────────────────────
const rootItems = computed(() => items.value.filter(i => !i.parent_id).sort((a, b) => a.order - b.order))
const childrenOf = (parentId) => items.value.filter(i => i.parent_id === parentId).sort((a, b) => a.order - b.order)
const parentableItems = computed(() => {
  const editId = itemModal.editing?.id
  return items.value.filter(i => !i.parent_id && i.id !== editId)
})

// ── Data loading ─────────────────────────────────────────────────────────────
const loadMenus = async () => {
  menusLoading.value = true
  try {
    const { data } = await axios.get('/api/menus')
    menus.value = data
  } catch {
    showToast('Failed to load menus', 'error')
  } finally {
    menusLoading.value = false
  }
}

const loadPages = async () => {
  try {
    const { data } = await axios.get('/api/pages')
    pages.value = data.data ?? data
  } catch { /* non-critical */ }
}

const selectMenu = async (menu) => {
  activeMenu.value = menu
  itemsLoading.value = true
  try {
    const { data } = await axios.get(`/api/menus/${menu.id}/list`)
    items.value = data
  } catch {
    showToast('Failed to load items', 'error')
  } finally {
    itemsLoading.value = false
  }
}

onMounted(() => { loadMenus(); loadPages() })

// ── Menu CRUD ─────────────────────────────────────────────────────────────────
const openMenuModal = (menu = null) => {
  menuModal.editing = menu
  menuForm.name     = menu?.name ?? ''
  menuForm.location = menu?.location ?? ''
  menuModal.open    = true
}

const saveMenu = async () => {
  if (!menuForm.name.trim() || !menuForm.location.trim()) return
  menuSaving.value = true
  try {
    if (menuModal.editing) {
      const { data } = await axios.put(`/api/menus/${menuModal.editing.id}`, { name: menuForm.name })
      const idx = menus.value.findIndex(m => m.id === data.id)
      if (idx !== -1) menus.value[idx] = { ...menus.value[idx], ...data }
      if (activeMenu.value?.id === data.id) activeMenu.value = { ...activeMenu.value, name: data.name }
    } else {
      const { data } = await axios.post('/api/menus', { ...menuForm })
      menus.value.push({ ...data, all_items_count: 0 })
    }
    menuModal.open = false
    showToast(menuModal.editing ? 'Menu updated' : 'Menu created')
  } catch (err) {
    showToast(err.response?.data?.message ?? 'Failed to save menu', 'error')
  } finally {
    menuSaving.value = false
  }
}

const confirmDeleteMenu = (menu) => {
  deleteModal.type = 'menu'; deleteModal.target = menu; deleteModal.open = true
}

// ── Item CRUD ─────────────────────────────────────────────────────────────────
const openItemModal = (item = null) => {
  itemModal.editing    = item
  itemForm.label       = item?.label ?? ''
  itemForm.type        = item?.type ?? 'custom'
  itemForm.url         = item?.url ?? ''
  itemForm.page_id     = item?.page_id ?? null
  itemForm.parent_id   = item?.parent_id ?? null
  itemForm.target      = item?.target ?? '_self'
  itemModal.open       = true
}

const saveItem = async () => {
  if (!itemForm.label.trim()) return
  itemSaving.value = true
  try {
    const payload = {
      label:     itemForm.label,
      type:      itemForm.type,
      url:       itemForm.type === 'custom' ? itemForm.url : null,
      page_id:   itemForm.type === 'page'   ? itemForm.page_id : null,
      parent_id: itemForm.parent_id,
      target:    itemForm.target,
    }

    if (itemModal.editing) {
      const { data } = await axios.put(`/api/menu-items/${itemModal.editing.id}`, payload)
      const idx = items.value.findIndex(i => i.id === data.id)
      if (idx !== -1) items.value[idx] = data
    } else {
      payload.menu_id = activeMenu.value.id
      payload.order   = items.value.filter(i => !i.parent_id).length
      const { data } = await axios.post('/api/menu-items', payload)
      items.value.push(data)
      // Update count badge
      const m = menus.value.find(m => m.id === activeMenu.value.id)
      if (m) m.all_items_count = (m.all_items_count ?? 0) + 1
    }

    itemModal.open = false
    showToast(itemModal.editing ? 'Item updated' : 'Item added')
  } catch (err) {
    showToast(err.response?.data?.message ?? 'Failed to save item', 'error')
  } finally {
    itemSaving.value = false
  }
}

const confirmDeleteItem = (item) => {
  deleteModal.type = 'item'; deleteModal.target = item; deleteModal.open = true
}

const executeDelete = async () => {
  deleteModal.loading = true
  try {
    if (deleteModal.type === 'menu') {
      await axios.delete(`/api/menus/${deleteModal.target.id}`)
      menus.value = menus.value.filter(m => m.id !== deleteModal.target.id)
      if (activeMenu.value?.id === deleteModal.target.id) {
        activeMenu.value = null; items.value = []
      }
      showToast('Menu deleted')
    } else {
      await axios.delete(`/api/menu-items/${deleteModal.target.id}`)
      // Remove item and its children
      const childIds = items.value.filter(i => i.parent_id === deleteModal.target.id).map(i => i.id)
      items.value = items.value.filter(i => i.id !== deleteModal.target.id && !childIds.includes(i.id))
      // Update count
      const m = menus.value.find(m => m.id === activeMenu.value?.id)
      if (m) m.all_items_count = Math.max(0, (m.all_items_count ?? 1) - 1 - childIds.length)
      showToast('Item deleted')
    }
    deleteModal.open = false
  } catch (err) {
    showToast(err.response?.data?.message ?? 'Failed to delete', 'error')
  } finally {
    deleteModal.loading = false
  }
}

// ── Move items ────────────────────────────────────────────────────────────────
const moveItem = async (item, dir) => {
  const siblings = items.value
    .filter(i => (i.parent_id ?? null) === (item.parent_id ?? null))
    .sort((a, b) => a.order - b.order)

  const idx = siblings.findIndex(i => i.id === item.id)
  if (dir === 'up' && idx === 0) return
  if (dir === 'down' && idx === siblings.length - 1) return

  const swap = dir === 'up' ? siblings[idx - 1] : siblings[idx + 1]

  // Swap order values locally first
  const newOrder = swap.order
  swap.order = item.order
  item.order = newOrder

  try {
    await axios.post('/api/menu-items/reorder', {
      items: [
        { id: item.id, order: item.order, parent_id: item.parent_id ?? null },
        { id: swap.id, order: swap.order, parent_id: swap.parent_id ?? null },
      ],
    })
  } catch {
    showToast('Failed to reorder', 'error')
    // revert
    item.order = newOrder
    swap.order = item.order
  }
}
</script>
