<template>
  <div :class="['border rounded-xl overflow-hidden bg-white', hasError ? 'border-red-400' : 'border-gray-200 focus-within:border-[#0D47A1]']">

    <!-- ── Toolbar ─────────────────────────────────────────────────────────── -->
    <div v-if="e" class="flex flex-wrap items-center gap-0.5 px-2 py-1.5 bg-gray-50 border-b border-gray-100">

      <!-- Text style -->
      <button type="button" @click="e.chain().focus().toggleBold().run()"      :class="tb(e.isActive('bold'))"      title="Bold (Ctrl+B)">
        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 4h8a4 4 0 010 8H6V4zm0 8h9a4 4 0 010 8H6v-8z"/></svg>
      </button>
      <button type="button" @click="e.chain().focus().toggleItalic().run()"    :class="tb(e.isActive('italic'))"    title="Italic (Ctrl+I)">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="19" y1="4" x2="10" y2="4"/><line x1="14" y1="20" x2="5" y2="20"/><line x1="15" y1="4" x2="9" y2="20"/></svg>
      </button>
      <button type="button" @click="e.chain().focus().toggleUnderline().run()" :class="tb(e.isActive('underline'))" title="Underline (Ctrl+U)">
        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 3v7a6 6 0 0012 0V3h2v7A8 8 0 014 10V3h2zm-2 16h16v2H4v-2z"/></svg>
      </button>
      <button type="button" @click="e.chain().focus().toggleStrike().run()"    :class="tb(e.isActive('strike'))"    title="Strikethrough">
        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.154 14c.23.516.346 1.09.346 1.72 0 1.342-.524 2.392-1.571 3.147C14.88 19.622 13.433 20 11.586 20c-1.64 0-3.263-.381-4.87-1.144V16.6c1.52.877 3.075 1.316 4.666 1.316 2.551 0 3.83-.732 3.839-2.195a2.321 2.321 0 00-.648-1.747H17.154zM11.586 6c-1.895 0-2.958.53-3.189 1.589l-1.975-.496C6.71 5.161 8.614 4 11.586 4c1.5 0 2.836.365 4.006 1.097L14.28 6.84A5.963 5.963 0 0011.586 6zM3 13h18v-2H3v2z"/></svg>
      </button>

      <!-- Text colour -->
      <div class="relative" title="Text colour">
        <button type="button" :class="tb(false)" @click="colorPickerRef.click()">
          <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15v-4H7l5-8v4h4l-5 8z" opacity=".3"/><path d="M11 17v-4H7l5-8v4h4l-5 8z"/></svg>
        </button>
        <div class="absolute bottom-0 left-1 right-1 h-1 rounded-sm pointer-events-none" :style="{ backgroundColor: activeColor }" />
        <input ref="colorPickerRef" type="color" class="absolute opacity-0 w-0 h-0" :value="activeColor"
          @change="e.chain().focus().setColor($event.target.value).run()" />
      </div>

      <div class="w-px h-5 bg-gray-200 mx-0.5" />

      <!-- Headings -->
      <button type="button" @click="e.chain().focus().toggleHeading({ level: 2 }).run()" :class="tb(e.isActive('heading', { level: 2 }))" title="Heading 2"><span class="text-[11px] font-black">H2</span></button>
      <button type="button" @click="e.chain().focus().toggleHeading({ level: 3 }).run()" :class="tb(e.isActive('heading', { level: 3 }))" title="Heading 3"><span class="text-[11px] font-black">H3</span></button>
      <button type="button" @click="e.chain().focus().toggleHeading({ level: 4 }).run()" :class="tb(e.isActive('heading', { level: 4 }))" title="Heading 4"><span class="text-[11px] font-black">H4</span></button>

      <div class="w-px h-5 bg-gray-200 mx-0.5" />

      <!-- Text alignment -->
      <button type="button" @click="e.chain().focus().setTextAlign('left').run()"    :class="tb(e.isActive({ textAlign: 'left' }))"    title="Align left">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="15" y2="12"/><line x1="3" y1="18" x2="18" y2="18"/></svg>
      </button>
      <button type="button" @click="e.chain().focus().setTextAlign('center').run()"  :class="tb(e.isActive({ textAlign: 'center' }))"  title="Align center">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="6" y1="12" x2="18" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
      </button>
      <button type="button" @click="e.chain().focus().setTextAlign('right').run()"   :class="tb(e.isActive({ textAlign: 'right' }))"   title="Align right">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="9" y1="12" x2="21" y2="12"/><line x1="6" y1="18" x2="21" y2="18"/></svg>
      </button>

      <div class="w-px h-5 bg-gray-200 mx-0.5" />

      <!-- Lists & quote -->
      <button type="button" @click="e.chain().focus().toggleBulletList().run()"  :class="tb(e.isActive('bulletList'))"  title="Bullet list">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="9" y1="6" x2="20" y2="6"/><line x1="9" y1="12" x2="20" y2="12"/><line x1="9" y1="18" x2="20" y2="18"/><circle cx="4" cy="6" r="1" fill="currentColor"/><circle cx="4" cy="12" r="1" fill="currentColor"/><circle cx="4" cy="18" r="1" fill="currentColor"/></svg>
      </button>
      <button type="button" @click="e.chain().focus().toggleOrderedList().run()" :class="tb(e.isActive('orderedList'))" title="Ordered list">
        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M2.5 6.5h1v-3h-1V2h2v4.5H6V8H2.5V6.5zM2 12h3.5v1.5H3.5v.5h1.5v1H3.5v.5H5.5V17H2v-1.5h2v-.5H2V12zm1.5 8H5.5v1.5H2V20l1.5-1.5v-.5H2V17h3.5v1.5l-1.5 1v.5zM8 5h14v2H8V5zm0 7h14v2H8v-2zm0 7h14v2H8v-2z"/></svg>
      </button>
      <button type="button" @click="e.chain().focus().toggleBlockquote().run()"  :class="tb(e.isActive('blockquote'))"  title="Blockquote">
        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M4.583 17.321C3.553 16.227 3 15 3 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311 1.804.167 3.226 1.648 3.226 3.489a3.5 3.5 0 01-3.5 3.5c-1.073 0-2.099-.49-2.748-1.179zm10 0C13.553 16.227 13 15 13 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311 1.804.167 3.226 1.648 3.226 3.489a3.5 3.5 0 01-3.5 3.5c-1.073 0-2.099-.49-2.748-1.179z"/></svg>
      </button>

      <div class="w-px h-5 bg-gray-200 mx-0.5" />

      <!-- Link -->
      <button type="button" @click="toggleLinkDialog" :class="tb(e.isActive('link'))" title="Insert / edit link">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
      </button>
      <button type="button" v-if="e.isActive('link')" @click="e.chain().focus().unsetLink().run()" :class="tb(false)" title="Remove link">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
      </button>

      <div class="w-px h-5 bg-gray-200 mx-0.5" />

      <!-- Image upload -->
      <button type="button" @click="imgInputRef.click()" :class="tb(false, imgUploading)" :disabled="imgUploading" title="Insert image">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
      </button>
      <input ref="imgInputRef" type="file" accept="image/*" class="hidden" @change="handleImageUpload" />

      <!-- Video embed -->
      <button type="button" @click="openVideoDialog" :class="tb(false)" title="Embed video (YouTube / Vimeo / Dailymotion)">
        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M10 15l5.19-3L10 9v6m11.56-7.83c.13.47.22 1.1.28 1.9.07.8.1 1.49.1 2.09L22 12c0 2.19-.16 3.8-.44 4.83-.25.9-.83 1.48-1.73 1.73-.47.13-1.33.22-2.65.28-1.3.07-2.49.1-3.59.1L12 19c-4.19 0-6.8-.16-7.83-.44-.9-.25-1.48-.83-1.73-1.73-.13-.47-.22-1.1-.28-1.9-.07-.8-.1-1.49-.1-2.09L2 12c0-2.19.16-3.8.44-4.83.25-.9.83-1.48 1.73-1.73.47-.13 1.33-.22 2.65-.28 1.3-.07 2.49-.1 3.59-.1L12 5c4.19 0 6.8.16 7.83.44.9.25 1.48.83 1.73 1.73z"/></svg>
      </button>

      <div class="w-px h-5 bg-gray-200 mx-0.5" />

      <!-- Misc -->
      <button type="button" @click="e.chain().focus().setHorizontalRule().run()" :class="tb(false)" title="Horizontal divider">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="3" y1="12" x2="21" y2="12"/></svg>
      </button>
      <button type="button" @click="clearFormatting" :class="tb(false)" title="Clear formatting">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 6l12 12M6 12h6m-3 6h9M3 6h18"/></svg>
      </button>

      <div class="w-px h-5 bg-gray-200 mx-0.5" />

      <!-- Undo / Redo -->
      <button type="button" @click="e.chain().focus().undo().run()" :disabled="!e.can().undo()" :class="tb(false, !e.can().undo())" title="Undo (Ctrl+Z)">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 14 4 9 9 4"/><path d="M20 20v-7a4 4 0 00-4-4H4"/></svg>
      </button>
      <button type="button" @click="e.chain().focus().redo().run()" :disabled="!e.can().redo()" :class="tb(false, !e.can().redo())" title="Redo (Ctrl+Shift+Z)">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 14 20 9 15 4"/><path d="M4 20v-7a4 4 0 014-4h12"/></svg>
      </button>

      <!-- Source toggle (right-aligned) -->
      <div class="flex-1" />
      <button type="button" @click="sourceMode = !sourceMode"
        :class="['px-2 py-1 text-[11px] font-bold rounded transition-colors', sourceMode ? 'bg-[#0D47A1] text-white' : 'text-gray-400 hover:bg-gray-200 hover:text-gray-600']"
        title="Toggle HTML source">&lt;/&gt;
      </button>
    </div>

    <!-- Skeleton toolbar -->
    <div v-else class="h-10 bg-gray-50 border-b border-gray-100 animate-pulse" />

    <!-- ── Image uploading indicator ───────────────────────────────────────── -->
    <Transition enter-active-class="transition-all duration-150" enter-from-class="opacity-0 -translate-y-1" enter-to-class="opacity-100 translate-y-0">
      <div v-if="imgUploading" class="flex items-center gap-2 px-3 py-2 bg-blue-50 border-b border-blue-100 text-xs text-blue-600 font-medium">
        <span class="w-3 h-3 border-2 border-blue-300 border-t-blue-600 rounded-full animate-spin shrink-0" />
        Uploading image…
      </div>
    </Transition>

    <!-- ── Link dialog ─────────────────────────────────────────────────────── -->
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
        <button type="button" @click="applyLink"         class="px-2.5 py-1 bg-[#0D47A1] text-white text-xs font-semibold rounded-lg">Apply</button>
        <button type="button" @click="linkDialog = false" class="px-2.5 py-1 bg-gray-200 text-gray-600 text-xs font-semibold rounded-lg">Cancel</button>
      </div>
    </Transition>

    <!-- ── Video dialog ────────────────────────────────────────────────────── -->
    <Transition enter-active-class="transition-all duration-150" enter-from-class="opacity-0 -translate-y-1" enter-to-class="opacity-100 translate-y-0">
      <div v-if="videoDialog" class="border-b border-purple-100 bg-purple-50">
        <div class="flex items-center gap-2 px-3 py-2">
          <svg class="w-4 h-4 text-purple-400 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M10 15l5.19-3L10 9v6m11.56-7.83c.13.47.22 1.1.28 1.9.07.8.1 1.49.1 2.09L22 12c0 2.19-.16 3.8-.44 4.83-.25.9-.83 1.48-1.73 1.73-.47.13-1.33.22-2.65.28-1.3.07-2.49.1-3.59.1L12 19c-4.19 0-6.8-.16-7.83-.44-.9-.25-1.48-.83-1.73-1.73-.13-.47-.22-1.1-.28-1.9-.07-.8-.1-1.49-.1-2.09L2 12c0-2.19.16-3.8.44-4.83.25-.9.83-1.48 1.73-1.73.47-.13 1.33-.22 2.65-.28 1.3-.07 2.49-.1 3.59-.1L12 5c4.19 0 6.8.16 7.83.44.9.25 1.48.83 1.73 1.73z"/></svg>
          <input
            ref="videoInputRef"
            v-model="videoUrl"
            type="text"
            placeholder="Paste YouTube, Vimeo or Dailymotion URL…"
            class="flex-1 text-sm bg-transparent outline-none text-gray-700 placeholder-gray-400"
            @keydown.enter.prevent="insertVideo"
            @keydown.escape.prevent="videoDialog = false"
          />
          <button type="button" @click="insertVideo"        class="px-2.5 py-1 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-lg transition-colors">Embed</button>
          <button type="button" @click="videoDialog = false" class="px-2.5 py-1 bg-gray-200 text-gray-600 text-xs font-semibold rounded-lg">Cancel</button>
        </div>
        <p v-if="videoUrlError" class="text-[11px] text-red-500 px-3 pb-2">{{ videoUrlError }}</p>
      </div>
    </Transition>

    <!-- ── Rich text content ───────────────────────────────────────────────── -->
    <EditorContent
      v-show="!sourceMode"
      :editor="e"
      class="prose prose-sm max-w-none min-h-[320px] px-4 py-3 text-sm text-gray-800 [&_.tiptap]:outline-none [&_.tiptap]:min-h-[320px]"
    />

    <!-- ── HTML source ─────────────────────────────────────────────────────── -->
    <div v-if="sourceMode" class="relative">
      <textarea
        v-model="sourceHtml"
        spellcheck="false"
        class="w-full min-h-[320px] px-4 py-3 font-mono text-xs text-gray-700 bg-gray-50 outline-none resize-y leading-relaxed"
        placeholder="<p>Write HTML here…</p>"
        @input="syncFromSource"
      />
      <span class="absolute bottom-2 right-3 text-[10px] text-gray-300 select-none pointer-events-none">HTML source</span>
    </div>

    <!-- ── Footer ─────────────────────────────────────────────────────────── -->
    <div class="flex items-center justify-between px-3 py-1.5 bg-gray-50 border-t border-gray-100 text-[11px] text-gray-400">
      <span>{{ wordCount }} words</span>
      <span v-if="charCount > 0">{{ charCount.toLocaleString() }} characters</span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onBeforeUnmount } from 'vue'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import { Node, mergeAttributes }    from '@tiptap/core'
import StarterKit   from '@tiptap/starter-kit'
import Underline    from '@tiptap/extension-underline'
import Link         from '@tiptap/extension-link'
import Placeholder  from '@tiptap/extension-placeholder'
import Image        from '@tiptap/extension-image'
import TextAlign    from '@tiptap/extension-text-align'
import { TextStyle } from '@tiptap/extension-text-style'
import { Color }     from '@tiptap/extension-color'
import axios        from 'axios'

// ── Custom VideoEmbed node ────────────────────────────────────────────────────
const VideoEmbed = Node.create({
  name: 'videoEmbed',
  group: 'block',
  atom: true,

  addAttributes() {
    return { src: { default: null } }
  },

  parseHTML() {
    return [{ tag: 'div[data-video-embed]' }]
  },

  renderHTML({ HTMLAttributes }) {
    return [
      'div',
      mergeAttributes({ 'data-video-embed': '', class: 'video-embed-wrapper' }),
      [
        'iframe',
        mergeAttributes(
          { src: HTMLAttributes.src },
          {
            frameborder: '0',
            allowfullscreen: 'true',
            allow: 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture',
          }
        ),
      ],
    ]
  },

  addNodeView() {
    return ({ node }) => {
      const wrapper = document.createElement('div')
      wrapper.setAttribute('data-video-embed', '')
      wrapper.className = 'video-embed-wrapper'

      const iframe = document.createElement('iframe')
      iframe.src = node.attrs.src
      iframe.frameBorder = '0'
      iframe.allowFullscreen = true
      iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture'

      wrapper.appendChild(iframe)
      return { dom: wrapper }
    }
  },

  addCommands() {
    return {
      insertVideoEmbed: (attrs) => ({ commands }) =>
        commands.insertContent({ type: this.name, attrs }),
    }
  },
})

// ── Props / emits ─────────────────────────────────────────────────────────────
const props = defineProps({
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: 'Write your article content here…' },
  hasError: { type: Boolean, default: false },
})
const emit = defineEmits(['update:modelValue'])

// ── Refs ──────────────────────────────────────────────────────────────────────
const imgInputRef   = ref(null)
const colorPickerRef = ref(null)
const linkInputRef  = ref(null)
const videoInputRef = ref(null)

const imgUploading = ref(false)
const linkDialog   = ref(false)
const linkUrl      = ref('')
const videoDialog  = ref(false)
const videoUrl     = ref('')
const videoUrlError = ref('')
const sourceMode   = ref(false)
const sourceHtml   = ref('')

// ── Colour helper ─────────────────────────────────────────────────────────────
const activeColor = computed(() => e.value?.getAttributes('textStyle')?.color ?? '#000000')

// ── Source mode ───────────────────────────────────────────────────────────────
watch(sourceMode, (on) => {
  if (on) {
    sourceHtml.value = e.value?.getHTML() ?? ''
  } else if (e.value && sourceHtml.value !== e.value.getHTML()) {
    e.value.commands.setContent(sourceHtml.value, false)
    emit('update:modelValue', sourceHtml.value)
  }
})

let syncTimer = null
const syncFromSource = () => {
  emit('update:modelValue', sourceHtml.value)
  clearTimeout(syncTimer)
  syncTimer = setTimeout(() => {
    if (e.value) e.value.commands.setContent(sourceHtml.value, false)
  }, 400)
}

// ── Link dialog ───────────────────────────────────────────────────────────────
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

// ── Image upload ──────────────────────────────────────────────────────────────
const handleImageUpload = async (evt) => {
  const file = evt.target.files?.[0]
  if (!file) return
  imgUploading.value = true
  try {
    const fd = new FormData()
    fd.append('image', file)
    const { data } = await axios.post('/api/upload-image', fd, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    e.value?.chain().focus().setImage({ src: data.url, alt: file.name }).run()
  } catch {
    // silent — could add a toast here
  } finally {
    imgUploading.value = false
    evt.target.value   = ''
  }
}

// ── Video embed ───────────────────────────────────────────────────────────────
const openVideoDialog = () => {
  videoUrl.value     = ''
  videoUrlError.value = ''
  videoDialog.value  = true
  nextTick(() => videoInputRef.value?.focus())
}

function resolveEmbedUrl(url) {
  if (!url) return null
  if (url.includes('youtube.com/embed/') || url.includes('player.vimeo.com') || url.includes('dailymotion.com/embed/')) return url
  let m
  if ((m = url.match(/(?:youtube\.com\/(?:watch\?v=|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/)))
    return `https://www.youtube.com/embed/${m[1]}?rel=0&modestbranding=1`
  if ((m = url.match(/vimeo\.com\/(\d+)/)))
    return `https://player.vimeo.com/video/${m[1]}`
  if ((m = url.match(/dailymotion\.com\/video\/([a-zA-Z0-9]+)/)))
    return `https://www.dailymotion.com/embed/video/${m[1]}`
  return null
}

const insertVideo = () => {
  const embedSrc = resolveEmbedUrl(videoUrl.value.trim())
  if (!embedSrc) {
    videoUrlError.value = 'URL not recognised. Supported: YouTube, Vimeo, Dailymotion.'
    return
  }
  e.value?.chain().focus().insertVideoEmbed({ src: embedSrc }).run()
  videoDialog.value = false
}

// ── Editor ────────────────────────────────────────────────────────────────────
const e = useEditor({
  content: props.modelValue,
  extensions: [
    StarterKit.configure({ codeBlock: false, code: false }),
    Underline,
    Link.configure({
      openOnClick: false,
      HTMLAttributes: { rel: 'noopener noreferrer', target: '_blank' },
    }),
    Placeholder.configure({ placeholder: props.placeholder }),
    Image.configure({ inline: false, allowBase64: false }),
    TextAlign.configure({ types: ['heading', 'paragraph'] }),
    TextStyle,
    Color,
    VideoEmbed,
  ],
  onUpdate: ({ editor }) => {
    const html = editor.getHTML()
    emit('update:modelValue', html)
    if (sourceMode.value) sourceHtml.value = html
  },
  editorProps: {
    transformPastedHTML: (html) =>
      html
        .replace(/<pre[^>]*><code[^>]*>([\s\S]*?)<\/code><\/pre>/gi, '<p>$1</p>')
        .replace(/<code>([\s\S]*?)<\/code>/gi, '$1'),
  },
})

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
const wordCount = computed(() => { const t = plainText.value.trim(); return t ? t.split(/\s+/).length : 0 })
const charCount = computed(() => plainText.value.replace(/\s+/g, '').length)

// ── Toolbar button class ──────────────────────────────────────────────────────
const clearFormatting = () =>
  e.value?.chain().focus()
    .unsetBold().unsetItalic().unsetUnderline().unsetStrike()
    .unsetLink().clearNodes().unsetColor()
    .run()

const tb = (active, disabled = false) => [
  'w-7 h-7 flex items-center justify-center rounded transition-colors shrink-0',
  disabled ? 'opacity-30 cursor-not-allowed'
  : active  ? 'bg-[#0D47A1] text-white shadow-sm'
            : 'text-gray-500 hover:bg-gray-200 hover:text-gray-700',
].join(' ')
</script>

<style>
/* ── Editor content styles ────────────────────────────────────────────── */
.tiptap { outline: none; }
.tiptap p.is-editor-empty:first-child::before {
  content: attr(data-placeholder);
  float: left; color: #9ca3af; pointer-events: none; height: 0;
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

/* ── Inline images ────────────────────────────────────────────────────── */
.tiptap img {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
  margin: 0.75em 0;
  display: block;
  cursor: default;
}
.tiptap img.ProseMirror-selectednode {
  outline: 3px solid #0D47A1;
  outline-offset: 2px;
}

/* ── Embedded video ───────────────────────────────────────────────────── */
.video-embed-wrapper {
  position: relative;
  padding-top: 56.25%;   /* 16:9 */
  margin: 1em 0;
  border-radius: 10px;
  overflow: hidden;
  background: #000;
}
.video-embed-wrapper iframe {
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  border: none;
}

/* ── Article Show page — render saved video embeds correctly ──────────── */
.prose .video-embed-wrapper {
  position: relative;
  padding-top: 56.25%;
  margin: 1.5em 0;
  border-radius: 10px;
  overflow: hidden;
}
.prose .video-embed-wrapper iframe {
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  border: none;
}
</style>
