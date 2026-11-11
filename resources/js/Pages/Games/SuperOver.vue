<template>
  <Head>
    <title>Super Over Challenge – CricZone</title>
  </Head>

  <MainLayout>
    <div class="max-w-lg mx-auto px-4 py-8 select-none">

      <!-- Back link -->
      <a href="/games" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-primary transition-colors mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        All Games
      </a>

      <!-- Game Card -->
      <div class="bg-gradient-to-b from-[#0a2342] to-[#0d3060] rounded-3xl overflow-hidden shadow-2xl">

        <!-- Header -->
        <div class="px-6 pt-6 pb-4 border-b border-white/10">
          <div class="flex items-center justify-between mb-1">
            <h1 class="text-white font-black text-lg tracking-tight">🏏 Super Over Challenge</h1>
            <button @click="resetGame"
              class="text-white/40 hover:text-white/70 transition-colors text-xs font-bold uppercase tracking-wider">
              Restart
            </button>
          </div>
          <p class="text-blue-300 text-xs">Chase the target in 6 balls · 1 wicket in hand</p>
        </div>

        <!-- Scoreboard -->
        <div class="grid grid-cols-3 divide-x divide-white/10 border-b border-white/10">
          <div class="px-4 py-3 text-center">
            <p class="text-blue-300 text-[10px] font-bold uppercase tracking-wider mb-0.5">Need</p>
            <p class="text-white text-2xl font-black leading-none">{{ runsNeeded }}</p>
            <p class="text-blue-300 text-[10px] mt-0.5">off {{ ballsLeft }} balls</p>
          </div>
          <div class="px-4 py-3 text-center">
            <p class="text-blue-300 text-[10px] font-bold uppercase tracking-wider mb-0.5">Score</p>
            <p class="text-white text-2xl font-black leading-none">{{ score }}<span class="text-base font-normal text-white/40">/{{ wicketsLost }}</span></p>
            <p class="text-blue-300 text-[10px] mt-0.5">Target {{ target }}</p>
          </div>
          <div class="px-4 py-3 text-center">
            <p class="text-blue-300 text-[10px] font-bold uppercase tracking-wider mb-0.5">Ball</p>
            <div class="flex items-center justify-center gap-1 mt-1">
              <span v-for="b in 6" :key="b"
                :class="['w-4 h-4 rounded-full border text-[9px] flex items-center justify-center font-bold',
                  b <= ballsPlayed
                    ? ballResults[b-1]?.out ? 'bg-red-500 border-red-400 text-white' : 'bg-secondary border-secondary text-white'
                    : 'border-white/20 text-white/20'
                ]">
                {{ b <= ballsPlayed ? (ballResults[b-1]?.out ? '✕' : ballResults[b-1]?.runs) : '' }}
              </span>
            </div>
          </div>
        </div>

        <!-- Pitch area -->
        <div class="relative mx-6 my-5 h-52 rounded-2xl overflow-hidden bg-[#1a4a1a] flex flex-col items-center justify-between py-4"
          @click="onScreenTap" @keydown.space.prevent="onScreenTap">

          <!-- Pitch strip -->
          <div class="absolute inset-y-0 left-1/2 -translate-x-1/2 w-14 bg-[#c8a96e]/30 rounded-sm" />

          <!-- Stumps at batsman end (bottom) -->
          <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex items-end gap-1">
            <div class="w-0.5 h-6 bg-[#e8d5a3] rounded-full" />
            <div class="w-0.5 h-7 bg-[#e8d5a3] rounded-full" />
            <div class="w-0.5 h-6 bg-[#e8d5a3] rounded-full" />
            <div class="absolute -top-0.5 left-1/2 -translate-x-1/2 flex gap-0.5">
              <div class="w-1 h-0.5 bg-[#e8d5a3] rounded-full" />
              <div class="w-1 h-0.5 bg-[#e8d5a3] rounded-full" />
            </div>
          </div>

          <!-- Stumps at bowler end (top) -->
          <div class="absolute top-3 left-1/2 -translate-x-1/2 flex items-start gap-1">
            <div class="w-0.5 h-5 bg-[#e8d5a3]/50 rounded-full" />
            <div class="w-0.5 h-6 bg-[#e8d5a3]/50 rounded-full" />
            <div class="w-0.5 h-5 bg-[#e8d5a3]/50 rounded-full" />
          </div>

          <!-- Ball -->
          <div
            :class="['absolute left-1/2 -translate-x-1/2 w-5 h-5 rounded-full bg-red-500 shadow-lg shadow-red-500/40 transition-none z-10 flex items-center justify-center',
              phase === 'bowling' ? 'ball-bounce' : '']"
            :style="ballStyle"
          >
            <div class="w-full h-full rounded-full border border-red-300/40" />
          </div>

          <!-- Result flash -->
          <Transition
            enter-active-class="transition-all duration-150"
            enter-from-class="opacity-0 scale-50"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition-all duration-300"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-150"
          >
            <div v-if="showResult && lastResult"
              :class="['absolute inset-0 flex flex-col items-center justify-center z-20 pointer-events-none']"
            >
              <div :class="['text-4xl font-black px-6 py-3 rounded-2xl backdrop-blur-sm', resultStyle.bg, resultStyle.text]">
                {{ lastResult.label }}
              </div>
              <div v-if="lastResult.runs > 0" class="text-white/70 text-sm font-bold mt-2">
                +{{ lastResult.runs }} {{ lastResult.runs === 1 ? 'run' : 'runs' }}
              </div>
            </div>
          </Transition>

          <!-- Idle overlay -->
          <div v-if="phase === 'idle' && !gameOver"
            class="absolute inset-0 flex items-center justify-center bg-black/40 backdrop-blur-[2px] rounded-2xl z-20">
            <p class="text-white/60 text-sm font-semibold">Press BOWL to start</p>
          </div>
        </div>

        <!-- Timing bar -->
        <div class="mx-6 mb-5">
          <div class="relative h-8 rounded-full overflow-hidden bg-[#0a1f3a]">
            <!-- Zone gradient -->
            <div class="absolute inset-0 flex">
              <div class="flex-[1] bg-red-600/80" />
              <div class="flex-[1] bg-orange-500/80" />
              <div class="flex-[1] bg-yellow-500/80" />
              <div class="flex-[1.2] bg-green-500/80" />
              <div class="flex-[1.5] bg-emerald-400/90" />
              <div class="flex-[1.2] bg-green-500/80" />
              <div class="flex-[1] bg-yellow-500/80" />
              <div class="flex-[1] bg-orange-500/80" />
              <div class="flex-[1] bg-red-600/80" />
            </div>
            <!-- Zone labels -->
            <div class="absolute inset-0 flex items-center justify-around px-1 pointer-events-none">
              <span class="text-white/70 text-[9px] font-black">OUT</span>
              <span class="text-white/70 text-[9px] font-black">1</span>
              <span class="text-white/70 text-[9px] font-black">2</span>
              <span class="text-white/80 text-[9px] font-black">4</span>
              <span class="text-white text-[10px] font-black">SIX</span>
              <span class="text-white/80 text-[9px] font-black">4</span>
              <span class="text-white/70 text-[9px] font-black">2</span>
              <span class="text-white/70 text-[9px] font-black">1</span>
              <span class="text-white/70 text-[9px] font-black">OUT</span>
            </div>
            <!-- Moving marker -->
            <div
              class="absolute top-0 bottom-0 w-1.5 bg-white rounded-full shadow-lg shadow-white/50 transition-none"
              :style="{ left: `calc(${markerPos * 100}% - 3px)` }"
            />
            <!-- Frozen marker indicator -->
            <div v-if="frozenPos !== null"
              class="absolute top-0 bottom-0 w-1 bg-yellow-300 rounded-full"
              :style="{ left: `calc(${frozenPos * 100}% - 2px)` }"
            />
          </div>
          <div class="flex justify-between text-[9px] text-white/20 mt-1 px-1">
            <span>← TOO EARLY</span>
            <span>SWEET SPOT</span>
            <span>TOO LATE →</span>
          </div>
        </div>

        <!-- Action button -->
        <div class="px-6 pb-6">
          <button
            @click="onActionBtn"
            :disabled="phase === 'showing' || gameOver"
            :class="['w-full py-4 rounded-2xl font-black text-lg uppercase tracking-widest transition-all duration-150 active:scale-95',
              gameOver
                ? 'bg-white/10 text-white/40 cursor-not-allowed'
                : phase === 'idle'
                  ? 'bg-secondary hover:bg-secondary-dark text-white shadow-lg shadow-secondary/40'
                  : phase === 'bowling'
                    ? 'bg-white text-primary shadow-lg'
                    : 'bg-white/10 text-white/40 cursor-not-allowed'
            ]"
          >
            {{ actionLabel }}
          </button>
          <p class="text-center text-white/25 text-xs mt-2">or tap anywhere on the pitch / press Space</p>
        </div>

        <!-- Game Over overlay -->
        <Transition
          enter-active-class="transition-all duration-400 ease-out"
          enter-from-class="opacity-0 translate-y-4"
          enter-to-class="opacity-100 translate-y-0"
        >
          <div v-if="gameOver" class="mx-4 mb-6 rounded-2xl overflow-hidden">
            <div :class="['p-6 text-center', won ? 'bg-emerald-500/20 border border-emerald-500/30' : 'bg-red-500/20 border border-red-500/30']">
              <div class="text-5xl mb-3">{{ won ? '🏆' : '💔' }}</div>
              <h2 :class="['text-2xl font-black mb-1', won ? 'text-emerald-400' : 'text-red-400']">
                {{ won ? 'You Won!' : 'Game Over' }}
              </h2>
              <p class="text-white/60 text-sm mb-1">
                {{ won
                  ? `Chased ${target} in ${ballsPlayed} ball${ballsPlayed === 1 ? '' : 's'}!`
                  : `Needed ${runsNeeded} more from ${ballsLeft} ball${ballsLeft === 1 ? '' : 's'}` }}
              </p>
              <p class="text-white/40 text-xs mb-5">Score: {{ score }}/{{ wicketsLost }} · Target: {{ target }}</p>

              <!-- Action buttons -->
              <div class="flex gap-3">
                <button @click="shareScore"
                  class="flex-1 py-2.5 bg-white/10 hover:bg-white/20 text-white text-sm font-bold rounded-xl transition-colors flex items-center justify-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                  Share
                </button>
                <button @click="resetGame"
                  class="flex-1 py-2.5 bg-secondary hover:bg-secondary-dark text-white text-sm font-bold rounded-xl transition-colors">
                  Play Again
                </button>
              </div>
            </div>
          </div>
        </Transition>

      </div>

      <!-- How to play -->
      <div class="mt-6 bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm">
        <h3 class="font-black text-gray-800 dark:text-slate-200 mb-3 text-sm uppercase tracking-wider">How to Play</h3>
        <ol class="space-y-2 text-sm text-gray-500 dark:text-slate-400">
          <li class="flex gap-2.5"><span class="font-bold text-primary">1.</span> Press <strong>BOWL</strong> to start the delivery</li>
          <li class="flex gap-2.5"><span class="font-bold text-primary">2.</span> Watch the timing bar marker move left and right</li>
          <li class="flex gap-2.5"><span class="font-bold text-primary">3.</span> Press <strong>BAT!</strong> (or tap the pitch / Space) when marker is in the <span class="text-emerald-500 font-bold">green zone</span> for a six!</li>
          <li class="flex gap-2.5"><span class="font-bold text-primary">4.</span> Avoid the <span class="text-red-500 font-bold">red zones</span> — that's a wicket!</li>
        </ol>
      </div>

    </div>
  </MainLayout>
</template>

<script setup>
import { ref, computed, onBeforeUnmount } from 'vue'
import { Head } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'

// ── Game constants ─────────────────────────────────────────────────────────────
const ZONE_MAP = [
  { max: 0.08,  runs: 0,  out: true,  label: 'BOWLED! 💀' },
  { max: 0.18,  runs: 1,  out: false, label: 'ONE RUN' },
  { max: 0.30,  runs: 2,  out: false, label: 'TWO RUNS' },
  { max: 0.42,  runs: 4,  out: false, label: 'FOUR! 🏃' },
  { max: 0.58,  runs: 6,  out: false, label: 'MAXIMUM! 🎉' },
  { max: 0.70,  runs: 4,  out: false, label: 'FOUR! 🏃' },
  { max: 0.82,  runs: 2,  out: false, label: 'TWO RUNS' },
  { max: 0.92,  runs: 1,  out: false, label: 'ONE RUN' },
  { max: 1.00,  runs: 0,  out: true,  label: 'CAUGHT! 🙈' },
]

function getZoneResult(pos) {
  return ZONE_MAP.find(z => pos <= z.max) ?? ZONE_MAP[ZONE_MAP.length - 1]
}

function randomTarget() {
  return 12 + Math.floor(Math.random() * 9)  // 12–20
}

// ── State ──────────────────────────────────────────────────────────────────────
const target       = ref(randomTarget())
const score        = ref(0)
const wicketsLost  = ref(0)
const ballsPlayed  = ref(0)
const ballResults  = ref([])
const phase        = ref('idle')   // 'idle' | 'bowling' | 'showing'
const gameOver     = ref(false)
const won          = ref(false)
const lastResult   = ref(null)
const showResult   = ref(false)
const markerPos    = ref(0.5)
const frozenPos    = ref(null)

// Ball position (0 = bowler end, 1 = batsman end)
const ballProgress = ref(0)

const ballsLeft    = computed(() => 6 - ballsPlayed.value)
const runsNeeded   = computed(() => Math.max(0, target.value + 1 - score.value))

const actionLabel  = computed(() => {
  if (gameOver.value) return won.value ? '🏆 Won!' : '💔 Game Over'
  if (phase.value === 'idle')    return '🎳 Bowl'
  if (phase.value === 'bowling') return '🏏 BAT!'
  return '...'
})

const ballStyle = computed(() => ({
  top: `${8 + ballProgress.value * 72}%`,
}))

const resultStyle = computed(() => {
  if (!lastResult.value) return {}
  if (lastResult.value.out) return { bg: 'bg-red-600/90',     text: 'text-white' }
  if (lastResult.value.runs === 6) return { bg: 'bg-emerald-500/90', text: 'text-white' }
  if (lastResult.value.runs === 4) return { bg: 'bg-blue-500/90',    text: 'text-white' }
  return { bg: 'bg-white/80', text: 'text-gray-800' }
})

// ── Animation RAF handles ──────────────────────────────────────────────────────
let markerRaf   = null
let ballRaf     = null
let markerDir   = 1
let markerSpeed = 0.008
let ballStartTime = null

function startMarker() {
  markerDir   = Math.random() > 0.5 ? 1 : -1
  markerSpeed = 0.007 + (ballsPlayed.value * 0.0008)  // slightly faster each ball

  function tick() {
    markerPos.value += markerDir * markerSpeed
    if (markerPos.value >= 1) { markerPos.value = 1; markerDir = -1 }
    if (markerPos.value <= 0) { markerPos.value = 0; markerDir = 1 }
    markerRaf = requestAnimationFrame(tick)
  }
  markerRaf = requestAnimationFrame(tick)
}

function stopMarker() {
  if (markerRaf) { cancelAnimationFrame(markerRaf); markerRaf = null }
}

function startBallAnimation(onComplete) {
  ballProgress.value = 0
  ballStartTime = null
  const duration = 1400  // ms

  function tick(ts) {
    if (!ballStartTime) ballStartTime = ts
    ballProgress.value = Math.min((ts - ballStartTime) / duration, 1)
    if (ballProgress.value < 1) {
      ballRaf = requestAnimationFrame(tick)
    } else {
      onComplete()
    }
  }
  ballRaf = requestAnimationFrame(tick)
}

function stopBallAnimation() {
  if (ballRaf) { cancelAnimationFrame(ballRaf); ballRaf = null }
}

// ── Game logic ─────────────────────────────────────────────────────────────────
function bowl() {
  if (phase.value !== 'idle' || gameOver.value) return
  phase.value   = 'bowling'
  frozenPos.value = null

  startMarker()
  startBallAnimation(() => {
    // Ball reached stumps without a hit
    stopMarker()
    frozenPos.value = markerPos.value
    recordResult({ runs: 0, out: true, label: 'BOWLED! 💀' })
  })
}

function hit() {
  if (phase.value !== 'bowling') return
  stopBallAnimation()
  stopMarker()

  const pos = markerPos.value
  frozenPos.value = pos
  const result = getZoneResult(pos)
  recordResult(result)
}

function recordResult(result) {
  phase.value     = 'showing'
  lastResult.value = result
  showResult.value = true

  ballResults.value.push({ runs: result.runs, out: result.out })
  ballsPlayed.value++

  if (!result.out) {
    score.value += result.runs
  } else {
    wicketsLost.value++
  }

  // Check win/loss
  const won_now  = score.value > target.value
  const lost_now = result.out || ballsPlayed.value >= 6

  setTimeout(() => {
    showResult.value = false
    if (won_now) {
      won.value      = true
      gameOver.value = true
      phase.value    = 'idle'
    } else if (lost_now) {
      won.value      = runsNeeded.value <= 0
      gameOver.value = true
      phase.value    = 'idle'
    } else {
      phase.value     = 'idle'
      ballProgress.value = 0
    }
  }, 1600)
}

function onActionBtn() {
  if (phase.value === 'idle') bowl()
  else if (phase.value === 'bowling') hit()
}

function onScreenTap() {
  if (phase.value === 'bowling') hit()
}

function resetGame() {
  stopMarker()
  stopBallAnimation()
  target.value       = randomTarget()
  score.value        = 0
  wicketsLost.value  = 0
  ballsPlayed.value  = 0
  ballResults.value  = []
  phase.value        = 'idle'
  gameOver.value     = false
  won.value          = false
  lastResult.value   = null
  showResult.value   = false
  markerPos.value    = 0.5
  frozenPos.value    = null
  ballProgress.value = 0
}

function shareScore() {
  const msg = won.value
    ? `🏆 I chased ${target.value} in ${ballsPlayed.value} balls on CricZone Super Over Challenge! Score: ${score.value}/${wicketsLost.value}. Can you beat it? 🏏 criczone.com/games/super-over`
    : `🏏 I scored ${score.value}/${wicketsLost.value} chasing ${target.value} on CricZone Super Over Challenge. Try beating me! criczone.com/games/super-over`

  if (navigator.share) {
    navigator.share({ text: msg }).catch(() => {})
  } else {
    navigator.clipboard?.writeText(msg).then(() => alert('Score copied to clipboard!'))
  }
}

// Global keyboard listener
function onKeydown(e) {
  if (e.code === 'Space') { e.preventDefault(); onActionBtn() }
}
window.addEventListener('keydown', onKeydown)
onBeforeUnmount(() => {
  window.removeEventListener('keydown', onKeydown)
  stopMarker()
  stopBallAnimation()
})
</script>

<style scoped>
.ball-bounce {
  filter: drop-shadow(0 0 6px rgba(239, 68, 68, 0.8));
}
</style>
