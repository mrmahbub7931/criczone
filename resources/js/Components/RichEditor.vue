<template>
  <div :class="['border rounded-xl overflow-hidden bg-white', hasError ? 'border-red-400' : 'border-gray-200 focus-within:border-[#0D47A1]']">

    <!-- ── Toolbar ── -->
    <div v-if="e" class="flex flex-wrap items-center gap-0.5 px-2 py-1.5 bg-gray-50 border-b border-gray-100">

      <!-- Text style -->
      <button type="button" @click="e.chain().focus().toggleBold().run()"      :class="tb(e.isActive('bold'))"      title="Bold (Ctrl+B)"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 4h8a4 4 0 010 8H6V4zm0 8h9a4 4 0 010 8H6v-8z"/></svg></button>
      <button type="button" @click="e.chain().focus().toggleItalic().run()"    :class="tb(e.isActive('italic'))"    title="Italic (Ctrl+I)"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="19" y1="4" x2="10" y2="4"/><line x1="14" y1="20" x2="5" y2="20"/><line x1="15" y1="4" x2="9" y2="20"/></svg></button>
      <button type="button" @click="e.chain().focus().toggleUnderline().run()" :class="tb(e.isActive('underline'))" title="Underline (Ctrl+U)"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 3v7a6 6 0 0012 0V3h2v7A8 8 0 014 10V3h2zm-2 16h16v2H4v-2z"/></svg></button>
      <button type="button" @click="e.chain().focus().toggleStrike().run()"    :class="tb(e.isActive('strike'))"    title="Strikethrough"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.154 14c.23.516.346 1.09.346 1.72 0 1.342-.524 2.392-1.571 3.147C14.88 19.622 13.433 20 11.586 20c-1.64 0-3.263-.381-4.87-1.144V16.6c1.52.877 3.075 1.316 4.666 1.316 2.551 0 3.83-.732 3.839-2.195a2.321 2.321 0 00-.648-1.747H17.154zM11.586 6c-1.895 0-2.958.53-3.189 1.589l-1.975-.496C6.71 5.161 8.614 4 11.586 4c1.5 0 2.836.365 4.006 1.097L14.28 6.84A5.963 5.963 0 0011.586 6zM3 13h18v-2H3v2z"/></svg></button>

      <div class="w-px h-5 bg-gray-200 mx-0.5" />

      <!-- Headings -->
      <button type="button" @click="e.chain().focus().toggleHeading({ level: 2 }).run()" :class="tb(e.isActive('heading', { level: 2 }))" title="Heading 2"><span class="text-[11px] font-black">H2</span></button>
      <button type="button" @click="e.chain().focus().toggleHeading({ level: 3 }).run()" :class="tb(e.isActive('heading', { level: 3 }))" title="Heading 3"><span class="text-[11px] font-black">H3</span></button>
      <button type="button" @click="e.chain().focus().toggleHeading({ level: 4 }).run()" :class="tb(e.isActive('heading', { level: 4 }))" title="Heading 4"><span class="text-[11px] font-black">H4</span></button>

      <div class="w-px h-5 bg-gray-200 mx-0.5" />

      <!-- Lists -->
      <button type="button" @click="e.chain().focus().toggleBulletList().run()"  :class="tb(e.isActive('bulletList'))"  title="Bullet list"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="9" y1="6" x2="20" y2="6"/><line x1="9" y1="12" x2="20" y2="12"/><line x1="9" y1="18" x2="20" y2="18"/><circle cx="4" cy="6" r="1" fill="currentColor"/><circle cx="4" cy="12" r="1" fill="currentColor"/><circle cx="4" cy="18" r="1" fill="currentColor"/></svg></button>
      <button type="button" @click="e.chain().focus().toggleOrderedList().run()" :class="tb(e.isActive('orderedList'))" title="Ordered list"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M2.5 6.5h1v-3h-1V2h2v4.5H6V8H2.5V6.5zM2 12h3.5v1.5H3.5v.5h1.5v1H3.5v.5H5.5V17H2v-1.5h2v-.5H2V12zm1.5 8H5.5v1.5H2V20l1.5-1.5v-.5H2V17h3.5v1.5l-1.5 1v.5zM8 5h14v2H8V5zm0 7h14v2H8v-2zm0 7h14v2H8v-2z"/></svg></button>
      <button type="button" @click="e.chain().focus().toggleBlockquote().run()"  :class="tb(e.isActive('blockquote'))"  title="Blockquote"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M4.583 17.321C3.553 16.227 3 15 3 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311 1.804.167 3.226 1.648 3.226 3.489a3.5 3.5 0 01-3.5 3.5c-1.073 0-2.099-.49-2.748-1.179zm10 0C13.553 16.227 13 15 13 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311 1.804.167 3.226 1.648 3.226 3.489a3.5 3.5 0 01-3.5 3.5c-1.073 0-2.099-.49-2.748-1.179z"/></svg></button>

      <div class="w-px h-5 bg-gray-200 mx-0.5" />

      <!-- Link -->
      <button type="button" @click="toggleLinkDialog" :class="tb(e.isActive('link'))" title="Insert link"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg></button>
      <button type="button" v-if="e.isActive('link')" @click="e.chain().focus().unsetLink().run()" :class="tb(false)" title="Remove link"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/><line x1="2" y1="2" x2="22" y2="22"/></svg></button>

      <div class="w-px h-5 bg-gray-200 mx-0.5" />

      <!-- Misc -->
      <button type="button" @click="e.chain().focus().setHorizontalRule().run()" :class="tb(false)" title="Horizontal divider"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="3" y1="12" x2="21" y2="12"/></svg></button>
      <button type="button" @click="clearFormatting" :class="tb(false)" title="Clear formatting"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.68 13.31a16 16 0 003.41 2.6l1.27-1.27a2 2 0 012.11-.45 4.48 4.48 0 016 6 2 2 0 01-2.11.45"/><path d="M16.01 17.22A4.5 4.5 0 0110 21a4.5 4.5 0 01-3.18-1.32L3 16l4.5-4.5"/><line x1="2" y1="2" x2="22" y2="22"/></svg></button>

      <div class="w-px h-5 bg-gray-200 mx-0.5" />

      <!-- History -->
      <button type="button" @click="e.chain().focus().undo().run()" :disabled="!e.can().undo()" :class="tb(false, !e.can().undo())" title="Undo (Ctrl+Z)"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 14 4 9 9 4"/><path d="M20 20v-7a4 4 0 00-4-4H4"/></svg></button>
      <button type="button" @click="e.chain().focus().redo().run()" :disabled="!e.can().redo()" :class="tb(false, !e.can().redo())" title="Redo (Ctrl+Shift+Z)"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 14 20 9 15 4"/><path d="M4 20v-7a4 4 0 014-4h12"/></svg></button>

      <!-- Spacer + Source toggle -->
      <div class="flex-1" />
      <button type="button" @click="sourceMode = !sourceMode"
        :class="['px-2 py-1 text-[11px] font-bold rounded transition-colors', sourceMode ? 'bg-[#0D47A1] text-white' : 'text-gray-400 hover:bg-gray-200 hover:text-gray-600']"
        title="Toggle HTML source view">&lt;/&gt;</button>
    </div>

    <!-- Skeleton toolbar -->
    <div v-else class="h-9 bg-gray-50 border-b border-gray-100 animate-pulse" />

    <!-- ── Link dialog ── -->
    <Transition enter-active-class="transition-all duration-150" enter-from-class="opacity-0 -translate-y-1" enter-to-class="opacity-100 translate-y-0">
      <div v-if="linkDialog" class="flex items-center gap-2 px-3 py-2 bg-blue-50 border-b border-blue-100">
        <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
        <input
          ref="linkInputRef"
          v-model="linkUrl"
          type="url"
          placeholder="https://example.com"
          class="flex-1 text-sm bg-transparent outline-none text-gray-700 placeholder-gray-400"
          @keydown.enter.prevent="applyLink"
          @keydown.escape.prevent="linkDialog = false"
        />
        <button type="button" @click="applyLink"      class="px-2.5 py-1 bg-[#0D47A1] text-white text-xs font-semibold rounded-lg">Apply</button>
        <button type="button" @click="linkDialog = false" class="px-2.5 py-1 bg-gray-200 text-gray-600 text-xs font-semibold rounded-lg">Cancel</button>
      </div>
    </Transition>

    <!-- ── Rich text content ── -->
    <EditorContent
      v-show="!sourceMode"
      :editor="e"
      class="prose prose-sm max-w-none min-h-[280px] px-4 py-3 text-sm text-gray-800 [&_.tiptap]:outline-none [&_.tiptap]:min-h-[280px]"
    />

    <!-- ── HTML source view ── -->
    <div v-if="sourceMode" class="relative">
      <textarea
        v-model="sourceHtml"
        spellcheck="false"
        class="w-full min-h-[280px] px-4 py-3 font-mono text-xs text-gray-700 bg-gray-50 outline-none resize-y leading-relaxed"
        placeholder="<p>Write HTML here…</p>"
        @input="syncFromSource"
      />
      <span class="absolute bottom-2 right-3 text-[10px] text-gray-300 select-none pointer-events-none">HTML source</span>
    </div>

    <!-- ── Footer ── -->
    <div class="flex items-center justify-between px-3 py-1.5 bg-gray-50 border-t border-gray-100 text-[11px] text-gray-400">
      <span>{{ wordCount }} words</span>
      <span v-if="charCount > 0">{{ charCount.toLocaleString() }} characters</span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onBeforeUnmount } from 'vue'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit  from '@tiptap/starter-kit'
import Underline   from '@tiptap/extension-underline'
import Link        from '@tiptap/extension-link'
import Placeholder from '@tiptap/extension-placeholder'

const props = defineProps({
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: 'Write your article content here…' },
  hasError: { type: Boolean, default: false },
})
const emit = defineEmits(['update:modelValue'])

// ── Source mode ───────────────────────────────────────────────────────────────
const sourceMode = ref(false)
const sourceHtml = ref('')

watch(sourceMode, (on) => {
  if (on) {
    sourceHtml.value = e.value?.getHTML() ?? ''
  } else if (e.value && sourceHtml.value !== e.value.getHTML()) {
    e.value.commands.setContent(sourceHtml.value, false)
    emit('update:modelValue', sourceHtml.value)
  }
})

const syncFromSource = () => {
  emit('update:modelValue', sourceHtml.value)
  // Debounce setting into the editor to avoid cursor-jump while typing HTML
  clearTimeout(syncTimer)
  syncTimer = setTimeout(() => {
    if (e.value) e.value.commands.setContent(sourceHtml.value, false)
  }, 400)
}
let syncTimer = null

// ── Link dialog ───────────────────────────────────────────────────────────────
const linkDialog   = ref(false)
const linkUrl      = ref('')
const linkInputRef = ref(null)

const toggleLinkDialog = () => {
  linkUrl.value    = e.value?.getAttributes('link').href ?? ''
  linkDialog.value = !linkDialog.value
  if (linkDialog.value) nextTick(() => linkInputRef.value?.focus())
}

const applyLink = () => {
  const raw = linkUrl.value.trim()
  if (!raw) {
    e.value?.chain().focus().unsetLink().run()
  } else {
    const href = /^https?:\/\//i.test(raw) ? raw : `https://${raw}`
    e.value?.chain().focus().setLink({ href, target: '_blank' }).run()
  }
  linkDialog.value = false
}

// ── Editor instance ───────────────────────────────────────────────────────────
const e = useEditor({
  content: props.modelValue,
  extensions: [
    StarterKit.configure({
      // Disable code blocks — prevents accidental wrapping of normal text in <pre><code>
      // This was causing the reported issue where article content was stored as code blocks
      codeBlock: false,
      code:      false,
    }),
    Underline,
    Link.configure({
      openOnClick: false,
      HTMLAttributes: { rel: 'noopener noreferrer', target: '_blank' },
    }),
    Placeholder.configure({ placeholder: props.placeholder }),
  ],
  onUpdate: ({ editor }) => {
    const html = editor.getHTML()
    emit('update:modelValue', html)
    if (sourceMode.value) sourceHtml.value = html
  },
  editorProps: {
    // Strip out any <pre><code> blocks pasted from external sources
    transformPastedHTML: (html) => {
      return html
        .replace(/<pre[^>]*><code[^>]*>([\s\S]*?)<\/code><\/pre>/gi, '<p>$1</p>')
        .replace(/<code>([\s\S]*?)<\/code>/gi, '$1')
    },
  },
})

// Sync when parent sets a new value (e.g. opening edit modal)
watch(() => props.modelValue, (val) => {
  if (e.value && !sourceMode.value && e.value.getHTML() !== val) {
    e.value.commands.setContent(val ?? '', false)
  }
})

onBeforeUnmount(() => {
  clearTimeout(syncTimer)
  e.value?.destroy()
})

// ── Stats ─────────────────────────────────────────────────────────────────────
const plainText = computed(() => e.value?.getText() ?? '')
const wordCount = computed(() => {
  const t = plainText.value.trim()
  return t ? t.split(/\s+/).length : 0
})
const charCount = computed(() => plainText.value.replace(/\s+/g, '').length)

// ── Helpers ───────────────────────────────────────────────────────────────────
const clearFormatting = () =>
  e.value?.chain().focus()
    .unsetBold().unsetItalic().unsetUnderline().unsetStrike()
    .unsetLink().clearNodes()
    .run()

const tb = (active, disabled = false) => [
  'w-7 h-7 flex items-center justify-center rounded transition-colors',
  disabled  ? 'opacity-30 cursor-not-allowed'
  : active  ? 'bg-[#0D47A1] text-white shadow-sm'
            : 'text-gray-500 hover:bg-gray-200 hover:text-gray-700',
].join(' ')
</script>

<style>
.tiptap { outline: none; }
.tiptap p.is-editor-empty:first-child::before {
  content: attr(data-placeholder);
  float: left;
  color: #9ca3af;
  pointer-events: none;
  height: 0;
}
.tiptap h2 { font-size: 1.4em; font-weight: 800; margin: 1em 0 0.4em; }
.tiptap h3 { font-size: 1.2em; font-weight: 700; margin: 0.9em 0 0.35em; }
.tiptap h4 { font-size: 1.05em; font-weight: 700; margin: 0.8em 0 0.3em; }
.tiptap p  { margin: 0.5em 0; line-height: 1.7; }
.tiptap ul, .tiptap ol { padding-left: 1.4em; margin: 0.5em 0; }
.tiptap li { margin: 0.2em 0; }
.tiptap blockquote { border-left: 3px solid #f59e0b; padding-left: 1em; color: #6b7280; font-style: italic; margin: 0.75em 0; }
.tiptap hr { border: none; border-top: 2px solid #e5e7eb; margin: 1.2em 0; }
.tiptap a  { color: #0D47A1; text-decoration: underline; cursor: pointer; }
</style>
