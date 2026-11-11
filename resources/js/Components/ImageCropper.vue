<script setup>
import { ref, watch, onBeforeUnmount, nextTick } from 'vue'
import Cropper from 'cropperjs'
import 'cropperjs/dist/cropper.css'

const props = defineProps({
  show: Boolean,
  file: Object, // File object
})

const emit = defineEmits(['apply', 'cancel'])

// ── State ─────────────────────────────────────────────────────────────────
const imgEl      = ref(null)
const cropper    = ref(null)
const objectUrl  = ref('')
const ratio      = ref(16 / 9)
const zoom       = ref(0)

const RATIOS = [
  { label: '16:9', value: 16 / 9 },
  { label: '4:3',  value: 4 / 3  },
  { label: '1:1',  value: 1      },
  { label: 'Free', value: NaN    },
]

// ── Init cropper when file changes ────────────────────────────────────────
watch(() => props.file, async (file) => {
  if (!file || !props.show) return
  destroyCropper()
  objectUrl.value = URL.createObjectURL(file)
  await nextTick()
  initCropper()
})

watch(() => props.show, async (val) => {
  if (val && props.file) {
    objectUrl.value = URL.createObjectURL(props.file)
    await nextTick()
    initCropper()
  } else {
    destroyCropper()
  }
})

const initCropper = () => {
  if (!imgEl.value) return
  const img = imgEl.value
  const doInit = () => {
    if (cropper.value) return // already initialised (double-fire guard)
    cropper.value = new Cropper(img, {
      aspectRatio: ratio.value,
      viewMode: 1,
      autoCropArea: 0.9,
      responsive: true,
      restore: false,
      guides: true,
      center: true,
      highlight: true,
      cropBoxMovable: true,
      cropBoxResizable: true,
      toggleDragModeOnDblclick: false,
      zoom(e) {
        zoom.value = Math.round(e.detail.ratio * 100)
      },
    })
  }
  // Wait for the image to fully load before initialising
  if (img.complete && img.naturalWidth > 0) {
    doInit()
  } else {
    img.addEventListener('load', doInit, { once: true })
  }
}

const destroyCropper = () => {
  if (cropper.value) { cropper.value.destroy(); cropper.value = null }
  if (objectUrl.value) { URL.revokeObjectURL(objectUrl.value); objectUrl.value = '' }
  zoom.value = 0
}

onBeforeUnmount(destroyCropper)

// ── Controls ──────────────────────────────────────────────────────────────
const setRatio = (r) => {
  ratio.value = r
  cropper.value?.setAspectRatio(isNaN(r) ? NaN : r)
}

const zoomBy = (delta) => cropper.value?.zoom(delta)

const rotateBy = (deg) => cropper.value?.rotate(deg)

const flipH = () => {
  if (!cropper.value) return
  const d = cropper.value.getData()
  cropper.value.scaleX(d && d.scaleX === -1 ? 1 : -1)
}

// ── Apply ─────────────────────────────────────────────────────────────────
const applying = ref(false)

const apply = () => {
  if (!cropper.value) return
  applying.value = true

  const canvas = cropper.value.getCroppedCanvas({
    maxWidth:  1920,
    maxHeight: 1920,
    imageSmoothingEnabled: true,
    imageSmoothingQuality: 'high',
  })

  canvas.toBlob((blob) => {
    applying.value = false
    const croppedFile = new File([blob], props.file.name.replace(/\.[^.]+$/, '.jpg'), {
      type: 'image/jpeg',
    })
    emit('apply', croppedFile)
    destroyCropper()
  }, 'image/jpeg', 0.9)
}

const cancel = () => {
  destroyCropper()
  emit('cancel')
}
</script>

<template>
  <Transition name="modal">
    <div v-if="show" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="cancel" />

      <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl flex flex-col overflow-hidden" style="max-height:90vh">

        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
          <h3 class="font-black text-gray-900 text-base">Crop & Position Image</h3>
          <button @click="cancel" class="text-gray-400 hover:text-gray-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

        <!-- Cropper area -->
        <div class="flex-1 overflow-hidden bg-gray-900 min-h-0" style="max-height:50vh">
          <img
            v-if="objectUrl"
            ref="imgEl"
            :src="objectUrl"
            class="block max-w-full"
            style="max-height:50vh"
          />
        </div>

        <!-- Controls -->
        <div class="px-5 py-4 border-t border-gray-100 space-y-3">

          <!-- Aspect ratio -->
          <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-gray-500 w-20 shrink-0">Aspect Ratio</span>
            <div class="flex gap-1.5">
              <button
                v-for="r in RATIOS" :key="r.label"
                @click="setRatio(r.value)"
                :class="['px-3 py-1.5 text-xs font-bold rounded-lg border transition-colors',
                  (isNaN(r.value) ? isNaN(ratio) : ratio === r.value)
                    ? 'bg-[#0D47A1] text-white border-[#0D47A1]'
                    : 'bg-white text-gray-600 border-gray-200 hover:border-[#0D47A1]']"
              >{{ r.label }}</button>
            </div>
          </div>

          <!-- Zoom + Rotate + Flip -->
          <div class="flex items-center gap-4">
            <span class="text-xs font-semibold text-gray-500 w-20 shrink-0">Zoom {{ zoom }}%</span>
            <div class="flex gap-1.5">
              <button @click="zoomBy(-0.1)" class="ctrl-btn" title="Zoom out">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35M8 11h6"/></svg>
              </button>
              <button @click="zoomBy(0.1)" class="ctrl-btn" title="Zoom in">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35M11 8v6M8 11h6"/></svg>
              </button>
              <div class="w-px bg-gray-200 mx-1" />
              <button @click="rotateBy(-90)" class="ctrl-btn" title="Rotate left">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12a9 9 0 109-9 9.75 9.75 0 00-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
              </button>
              <button @click="rotateBy(90)" class="ctrl-btn" title="Rotate right">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 12a9 9 0 11-9-9 9.75 9.75 0 016.74 2.74L21 8"/><path d="M21 3v5h-5"/></svg>
              </button>
              <button @click="flipH" class="ctrl-btn" title="Flip horizontal">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 3H5a2 2 0 00-2 2v14a2 2 0 002 2h3"/><path d="M16 3h3a2 2 0 012 2v14a2 2 0 01-2 2h-3"/><path d="M12 20V4"/></svg>
              </button>
            </div>
          </div>

          <p class="text-[11px] text-gray-400">Drag to reposition · Pinch or scroll to zoom · Drag corners to resize crop area</p>
        </div>

        <!-- Footer -->
        <div class="flex gap-3 px-5 pb-5">
          <button @click="cancel"
            class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-xl hover:bg-gray-50 transition-colors">
            Cancel
          </button>
          <button @click="apply" :disabled="applying"
            class="flex-1 bg-[#0D47A1] hover:bg-[#0a2f6e] disabled:opacity-60 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
            <svg v-if="applying" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            {{ applying ? 'Applying…' : 'Apply Crop' }}
          </button>
        </div>

      </div>
    </div>
  </Transition>
</template>

<style scoped>
.ctrl-btn {
  @apply p-2 rounded-lg border border-gray-200 text-gray-600 hover:border-[#0D47A1] hover:text-[#0D47A1] transition-colors bg-white;
}
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
