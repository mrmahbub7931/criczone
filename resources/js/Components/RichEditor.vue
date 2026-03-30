<template>
  <div :class="['border rounded-lg overflow-hidden', hasError ? 'border-red-400' : 'border-gray-200 focus-within:border-[#0D47A1]']">
    <!-- Toolbar -->
    <div class="flex flex-wrap gap-0.5 px-2 py-1.5 bg-gray-50 border-b border-gray-200">
      <button type="button" @click="e.chain().focus().toggleBold().run()" :class="btn(e.isActive('bold'))">B</button>
      <button type="button" @click="e.chain().focus().toggleItalic().run()" :class="btn(e.isActive('italic'))" class="italic">I</button>
      <button type="button" @click="e.chain().focus().toggleStrike().run()" :class="btn(e.isActive('strike'))" class="line-through">S</button>
      <div class="w-px bg-gray-200 mx-1" />
      <button type="button" @click="e.chain().focus().toggleHeading({ level: 2 }).run()" :class="btn(e.isActive('heading', { level: 2 }))">H2</button>
      <button type="button" @click="e.chain().focus().toggleHeading({ level: 3 }).run()" :class="btn(e.isActive('heading', { level: 3 }))">H3</button>
      <div class="w-px bg-gray-200 mx-1" />
      <button type="button" @click="e.chain().focus().toggleBulletList().run()" :class="btn(e.isActive('bulletList'))">• List</button>
      <button type="button" @click="e.chain().focus().toggleOrderedList().run()" :class="btn(e.isActive('orderedList'))">1. List</button>
      <div class="w-px bg-gray-200 mx-1" />
      <button type="button" @click="e.chain().focus().toggleBlockquote().run()" :class="btn(e.isActive('blockquote'))">❝</button>
      <button type="button" @click="e.chain().focus().setHorizontalRule().run()" :class="btn(false)">—</button>
      <div class="w-px bg-gray-200 mx-1" />
      <button type="button" @click="e.chain().focus().undo().run()" :class="btn(false)">↩</button>
      <button type="button" @click="e.chain().focus().redo().run()" :class="btn(false)">↪</button>
    </div>

    <!-- Editor -->
    <EditorContent :editor="e" class="prose prose-sm max-w-none min-h-[200px] px-4 py-3 text-sm text-gray-800 focus:outline-none" />
  </div>
</template>

<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Placeholder from '@tiptap/extension-placeholder'
import { watch, onBeforeUnmount } from 'vue'

const props = defineProps({
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: 'Write your article content…' },
  hasError: { type: Boolean, default: false },
})
const emit = defineEmits(['update:modelValue'])

const e = useEditor({
  content: props.modelValue,
  extensions: [
    StarterKit,
    Placeholder.configure({ placeholder: props.placeholder }),
  ],
  onUpdate: ({ editor }) => emit('update:modelValue', editor.getHTML()),
})

// Sync external modelValue changes (e.g. open edit modal)
watch(() => props.modelValue, (val) => {
  if (e.value && e.value.getHTML() !== val) {
    e.value.commands.setContent(val, false)
  }
})

onBeforeUnmount(() => e.value?.destroy())

const btn = (active) =>
  `px-2 py-1 text-xs font-semibold rounded transition-colors ${active ? 'bg-[#0D47A1] text-white' : 'text-gray-600 hover:bg-gray-200'}`
</script>

<style>
/* TipTap placeholder */
.tiptap p.is-editor-empty:first-child::before {
  content: attr(data-placeholder);
  float: left;
  color: #9ca3af;
  pointer-events: none;
  height: 0;
}
</style>
