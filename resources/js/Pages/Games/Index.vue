<template>
  <Head>
    <title>Games – CricZone</title>
    <meta name="description" content="Play cricket games on CricZone — Super Over Challenge, quizzes and more." />
  </Head>

  <MainLayout>
    <!-- Stadium Hero Section -->
    <div class="relative overflow-hidden bg-[#0a1628] min-h-[340px] flex flex-col items-center justify-center py-14 px-4">

      <!-- Pitch SVG background -->
      <svg class="absolute inset-0 w-full h-full opacity-[0.07]" viewBox="0 0 800 340" preserveAspectRatio="xMidYMid slice" fill="none" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="400" cy="170" rx="340" ry="150" stroke="white" stroke-width="2"/>
        <ellipse cx="400" cy="170" rx="160" ry="90" stroke="white" stroke-width="1.5" stroke-dasharray="6 4"/>
        <rect x="370" y="60" width="60" height="220" rx="4" stroke="white" stroke-width="1.5"/>
        <line x1="362" y1="110" x2="438" y2="110" stroke="white" stroke-width="1.5"/>
        <line x1="362" y1="230" x2="438" y2="230" stroke="white" stroke-width="1.5"/>
        <line x1="390" y1="80"  x2="390" y2="108" stroke="white" stroke-width="1.5"/>
        <line x1="400" y1="78"  x2="400" y2="108" stroke="white" stroke-width="2"/>
        <line x1="410" y1="80"  x2="410" y2="108" stroke="white" stroke-width="1.5"/>
        <line x1="390" y1="232" x2="390" y2="258" stroke="white" stroke-width="1.5"/>
        <line x1="400" y1="232" x2="400" y2="260" stroke="white" stroke-width="2"/>
        <line x1="410" y1="232" x2="410" y2="258" stroke="white" stroke-width="1.5"/>
        <circle cx="60"  cy="170" r="3" fill="white"/>
        <circle cx="740" cy="170" r="3" fill="white"/>
        <circle cx="400" cy="20"  r="3" fill="white"/>
        <circle cx="400" cy="320" r="3" fill="white"/>
        <circle cx="150" cy="60"  r="2" fill="white"/>
        <circle cx="650" cy="60"  r="2" fill="white"/>
        <circle cx="150" cy="280" r="2" fill="white"/>
        <circle cx="650" cy="280" r="2" fill="white"/>
      </svg>

      <!-- ── Flying cricket balls ── -->
      <div class="ball-container absolute inset-0 overflow-hidden pointer-events-none">
        <!-- Ball 1 — slow arc left-to-right -->
        <div class="flying-ball ball-1">
          <div class="ball-inner">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="12" cy="12" r="11" fill="#c0392b" stroke="#8b1a1a" stroke-width="0.5"/>
              <path d="M12 2 Q16 7 16 12 Q16 17 12 22" stroke="rgba(255,255,255,0.4)" stroke-width="1.2" fill="none"/>
              <path d="M12 2 Q8 7 8 12 Q8 17 12 22"  stroke="rgba(255,255,255,0.4)" stroke-width="1.2" fill="none"/>
            </svg>
          </div>
        </div>
        <!-- Ball 2 — faster, different arc -->
        <div class="flying-ball ball-2">
          <div class="ball-inner">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="12" cy="12" r="11" fill="#c0392b" stroke="#8b1a1a" stroke-width="0.5"/>
              <path d="M12 2 Q16 7 16 12 Q16 17 12 22" stroke="rgba(255,255,255,0.4)" stroke-width="1.2" fill="none"/>
              <path d="M12 2 Q8 7 8 12 Q8 17 12 22"  stroke="rgba(255,255,255,0.4)" stroke-width="1.2" fill="none"/>
            </svg>
          </div>
        </div>
        <!-- Ball 3 — right-to-left -->
        <div class="flying-ball ball-3">
          <div class="ball-inner">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="12" cy="12" r="11" fill="#c0392b" stroke="#8b1a1a" stroke-width="0.5"/>
              <path d="M12 2 Q16 7 16 12 Q16 17 12 22" stroke="rgba(255,255,255,0.4)" stroke-width="1.2" fill="none"/>
              <path d="M12 2 Q8 7 8 12 Q8 17 12 22"  stroke="rgba(255,255,255,0.4)" stroke-width="1.2" fill="none"/>
            </svg>
          </div>
        </div>
        <!-- Ball 4 — high arc, tiny -->
        <div class="flying-ball ball-4">
          <div class="ball-inner">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="12" cy="12" r="11" fill="#c0392b" stroke="#8b1a1a" stroke-width="0.5"/>
              <path d="M12 2 Q16 7 16 12 Q16 17 12 22" stroke="rgba(255,255,255,0.4)" stroke-width="1.2" fill="none"/>
              <path d="M12 2 Q8 7 8 12 Q8 17 12 22"  stroke="rgba(255,255,255,0.4)" stroke-width="1.2" fill="none"/>
            </svg>
          </div>
        </div>
      </div>

      <!-- Floodlight glow blobs -->
      <div class="absolute top-0 left-1/4 w-72 h-40 bg-yellow-400/5 rounded-full blur-3xl pointer-events-none" />
      <div class="absolute top-0 right-1/4 w-72 h-40 bg-yellow-400/5 rounded-full blur-3xl pointer-events-none" />
      <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-96 h-32 bg-primary/20 rounded-full blur-3xl pointer-events-none" />

      <!-- Header text -->
      <div class="relative z-10 text-center">
        <p class="text-yellow-400/80 text-xs font-black uppercase tracking-[0.3em] mb-3">🏟️ CricZone Arena</p>
        <h1 class="text-4xl sm:text-5xl font-black text-white leading-none tracking-tight mb-3">
          Cricket<span class="text-secondary"> Games</span>
        </h1>
        <p class="text-white/40 text-sm max-w-sm mx-auto">Step onto the virtual pitch. Test your skills, beat the target.</p>
      </div>
    </div>

    <!-- Games Section -->
    <div class="bg-[#0d1f36] pb-16 px-4">
      <div class="max-w-5xl mx-auto">

        <!-- Featured game — Super Over -->
        <div class="relative -mt-10 mb-10">
          <a href="/games/super-over" class="group relative block rounded-3xl overflow-hidden shadow-2xl shadow-black/40 cursor-pointer">

            <!-- Grass texture background -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#1a4a1a] via-[#1e5520] to-[#0f3010]" />

            <!-- Pitch strip -->
            <div class="absolute inset-y-0 left-1/2 -translate-x-1/2 w-28 bg-[#c8a96e]/15 backdrop-blur-[1px]" />

            <!-- Floodlight rays -->
            <div class="absolute -top-10 left-1/4 w-40 h-72 bg-yellow-200/5 rotate-12 blur-2xl pointer-events-none" />
            <div class="absolute -top-10 right-1/4 w-40 h-72 bg-yellow-200/5 -rotate-12 blur-2xl pointer-events-none" />

            <!-- Boundary rope line -->
            <div class="absolute bottom-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-white/20 to-transparent" />

            <!-- ── Stumps (bottom-right corner) ── -->
            <div class="stumps-wrap absolute bottom-5 right-6 sm:bottom-7 sm:right-10 w-14 h-20 pointer-events-none">
              <!-- Bail left -->
              <div class="bail bail-left absolute top-[6px] left-[3px] w-[18px] h-[3px] bg-[#e8d5a3] rounded-full origin-left" />
              <!-- Bail right -->
              <div class="bail bail-right absolute top-[6px] left-[16px] w-[18px] h-[3px] bg-[#e8d5a3] rounded-full origin-right" />
              <!-- Stump left -->
              <div class="stump stump-left  absolute top-[8px] left-[4px]  w-[4px] h-[52px] bg-[#e8d5a3] rounded-full origin-bottom" />
              <!-- Stump mid -->
              <div class="stump stump-mid   absolute top-[6px] left-[21px] w-[4px] h-[54px] bg-[#e8d5a3] rounded-full origin-bottom" />
              <!-- Stump right -->
              <div class="stump stump-right absolute top-[8px] left-[38px] w-[4px] h-[52px] bg-[#e8d5a3] rounded-full origin-bottom" />
              <!-- Impact flash (hidden, shows on break) -->
              <div class="impact-flash absolute -inset-2 rounded-full bg-yellow-300/0 pointer-events-none" />
            </div>

            <div class="relative z-10 flex flex-col sm:flex-row items-center gap-6 p-7 sm:p-10">

              <!-- Left: Game info -->
              <div class="flex-1 text-center sm:text-left">
                <div class="inline-flex items-center gap-2 bg-secondary/90 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest mb-4 shadow">
                  <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse" />
                  Live &amp; Playable
                </div>
                <h2 class="text-3xl sm:text-4xl font-black text-white leading-tight mb-3 drop-shadow">
                  Super Over<br/>
                  <span class="text-yellow-300">Challenge</span>
                </h2>
                <p class="text-green-200/70 text-sm mb-5 max-w-sm">
                  Chase a target in 6 balls with 1 wicket in hand. Time your shots — nail the sweet spot for a maximum!
                </p>
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 mb-6">
                  <span class="flex items-center gap-1.5 bg-white/10 text-white/70 text-xs font-bold px-3 py-1.5 rounded-full">⚡ 6 Balls</span>
                  <span class="flex items-center gap-1.5 bg-white/10 text-white/70 text-xs font-bold px-3 py-1.5 rounded-full">🎯 Timing Based</span>
                  <span class="flex items-center gap-1.5 bg-white/10 text-white/70 text-xs font-bold px-3 py-1.5 rounded-full">📱 Mobile Friendly</span>
                </div>
                <div class="inline-flex items-center gap-2.5 bg-secondary hover:bg-secondary-dark text-white font-black text-sm px-7 py-3.5 rounded-2xl shadow-lg shadow-secondary/40 transition-all group-hover:scale-105 group-hover:shadow-secondary/60">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                  Play Now
                </div>
              </div>

              <!-- Right: Cricket ball visual -->
              <div class="relative flex-shrink-0 w-40 h-40 sm:w-48 sm:h-48">
                <div class="absolute inset-0 rounded-full bg-red-500/20 blur-2xl scale-110" />
                <div class="relative w-full h-full rounded-full bg-gradient-to-br from-red-500 via-red-600 to-red-800 shadow-2xl shadow-red-500/40 flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
                  <svg class="absolute inset-0 w-full h-full" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M50 10 Q70 30 70 50 Q70 70 50 90" stroke="rgba(255,255,255,0.35)" stroke-width="2" fill="none"/>
                    <path d="M50 10 Q30 30 30 50 Q30 70 50 90" stroke="rgba(255,255,255,0.35)" stroke-width="2" fill="none"/>
                    <path d="M55 15 Q72 20 75 30" stroke="rgba(255,255,255,0.2)" stroke-width="1.2" fill="none"/>
                    <path d="M55 85 Q72 80 75 70" stroke="rgba(255,255,255,0.2)" stroke-width="1.2" fill="none"/>
                    <path d="M45 15 Q28 20 25 30" stroke="rgba(255,255,255,0.2)" stroke-width="1.2" fill="none"/>
                    <path d="M45 85 Q28 80 25 70" stroke="rgba(255,255,255,0.2)" stroke-width="1.2" fill="none"/>
                  </svg>
                  <span class="text-5xl select-none drop-shadow-lg">🏏</span>
                </div>
              </div>

            </div>
          </a>
        </div>

        <!-- Coming Soon heading -->
        <div class="flex items-center gap-4 mb-6">
          <div class="h-px flex-1 bg-white/10" />
          <p class="text-white/30 text-xs font-black uppercase tracking-widest">More Games Coming</p>
          <div class="h-px flex-1 bg-white/10" />
        </div>

        <!-- Coming Soon grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div v-for="game in comingSoon" :key="game.name"
            class="relative rounded-2xl overflow-hidden border border-white/8 bg-white/[0.03] backdrop-blur-sm cursor-not-allowed">
            <div class="absolute top-4 right-4 w-7 h-7 rounded-full bg-white/8 flex items-center justify-center">
              <svg class="w-3.5 h-3.5 text-white/30" fill="currentColor" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z"/></svg>
            </div>
            <div class="p-6">
              <div class="text-4xl mb-3 opacity-50">{{ game.emoji }}</div>
              <h3 class="text-base font-black text-white/50 mb-1">{{ game.name }}</h3>
              <p class="text-white/25 text-xs leading-relaxed">{{ game.desc }}</p>
              <div class="mt-4 inline-block bg-white/5 text-white/25 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-widest">Coming Soon</div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'

const comingSoon = [
  { emoji: '🧠', name: 'Cricket Quiz',     desc: 'Daily trivia — test your cricket knowledge with timed questions.' },
  { emoji: '🔮', name: 'Match Predictor',  desc: 'Predict match outcomes before the first ball is bowled.' },
  { emoji: '👤', name: 'Guess the Player', desc: 'Identify cricketers from career stats and hints.' },
]
</script>

<style scoped>
/* ═══════════════════════════════════════════
   FLYING CRICKET BALLS
═══════════════════════════════════════════ */
.flying-ball {
  position: absolute;
  pointer-events: none;
}

/* Each ball has two layers:
   - outer  → moves horizontally (translateX)
   - inner  → adds the vertical arc (translateY) + spins
   The combination produces a natural parabolic path. */

.ball-inner {
  width: 28px;
  height: 28px;
  opacity: 0;
  animation: ball-spin 1.5s linear infinite, ball-fade 1s ease-in-out infinite;
}

/* Ball 1 — left to right, mid height */
.ball-1 {
  bottom: 30%;
  left: -40px;
  animation: arc-x-ltr 9s ease-in-out infinite;
  animation-delay: 0s;
}
.ball-1 .ball-inner {
  animation: ball-spin 9s linear infinite, ball-fade-ltr 9s ease-in-out infinite;
  animation-delay: 0s;
}

/* Ball 2 — left to right, higher + faster */
.ball-2 {
  bottom: 20%;
  left: -40px;
  animation: arc-x-ltr 6s ease-in-out infinite;
  animation-delay: 3s;
}
.ball-2 .ball-inner {
  animation: ball-spin 6s linear infinite, ball-fade-ltr 6s ease-in-out infinite;
  animation-delay: 3s;
  width: 20px;
  height: 20px;
}

/* Ball 3 — right to left */
.ball-3 {
  bottom: 40%;
  right: -40px;
  animation: arc-x-rtl 11s ease-in-out infinite;
  animation-delay: 1.5s;
}
.ball-3 .ball-inner {
  animation: ball-spin 11s linear infinite, ball-fade-rtl 11s ease-in-out infinite;
  animation-delay: 1.5s;
  width: 22px;
  height: 22px;
}

/* Ball 4 — left to right, tiny, very high arc */
.ball-4 {
  bottom: 15%;
  left: -30px;
  animation: arc-x-ltr 14s ease-in-out infinite;
  animation-delay: 6s;
}
.ball-4 .ball-inner {
  animation: ball-spin 14s linear infinite, ball-fade-ltr 14s ease-in-out infinite;
  animation-delay: 6s;
  width: 16px;
  height: 16px;
}

/* Horizontal travel */
@keyframes arc-x-ltr {
  0%   { transform: translateX(0); }
  100% { transform: translateX(calc(100vw + 60px)); }
}
@keyframes arc-x-rtl {
  0%   { transform: translateX(0); }
  100% { transform: translateX(calc(-100vw - 60px)); }
}

/* Vertical arc — peaks at mid travel */
@keyframes arc-y {
  0%   { transform: translateY(0) rotate(0deg); }
  50%  { transform: translateY(-90px) rotate(180deg); }
  100% { transform: translateY(20px) rotate(360deg); }
}

/* Spin */
@keyframes ball-spin {
  from { transform: rotate(0deg); }
  to   { transform: rotate(720deg); }
}

/* Fade in → visible → fade out for LTR */
@keyframes ball-fade-ltr {
  0%   { opacity: 0;    transform: translateY(0px) rotate(0deg); }
  8%   { opacity: 0.55; }
  50%  { opacity: 0.55; transform: translateY(-80px) rotate(360deg); }
  92%  { opacity: 0.55; }
  100% { opacity: 0;    transform: translateY(15px) rotate(720deg); }
}

@keyframes ball-fade-rtl {
  0%   { opacity: 0;    transform: translateY(0px) rotate(0deg); }
  8%   { opacity: 0.45; }
  50%  { opacity: 0.45; transform: translateY(-60px) rotate(-360deg); }
  92%  { opacity: 0.45; }
  100% { opacity: 0;    transform: translateY(10px) rotate(-720deg); }
}

/* ═══════════════════════════════════════════
   STUMPS BREAKING ON HOVER
═══════════════════════════════════════════ */
.stumps-wrap {
  opacity: 0.55;
  transition: opacity 0.2s;
}
.group:hover .stumps-wrap {
  opacity: 1;
}

/* Bail animations — fly off on hover */
.bail { transition: none; }
.group:hover .bail-left {
  animation: bail-fly-left 0.5s ease-out 0.05s forwards;
}
.group:hover .bail-right {
  animation: bail-fly-right 0.5s ease-out 0.05s forwards;
}

/* Stump animations — kick outward on hover */
.stump { transition: none; }
.group:hover .stump-left {
  animation: stump-fall-left  0.5s ease-out 0.05s forwards;
}
.group:hover .stump-mid {
  animation: stump-fall-back  0.4s ease-out 0.08s forwards;
}
.group:hover .stump-right {
  animation: stump-fall-right 0.5s ease-out 0.05s forwards;
}

/* Impact flash */
.group:hover .impact-flash {
  animation: impact 0.35s ease-out 0.04s forwards;
}

@keyframes bail-fly-left {
  0%   { transform: rotate(0deg) translateY(0); opacity: 1; }
  100% { transform: rotate(-60deg) translateY(-18px) translateX(-10px); opacity: 0; }
}
@keyframes bail-fly-right {
  0%   { transform: rotate(0deg) translateY(0); opacity: 1; }
  100% { transform: rotate(60deg) translateY(-18px) translateX(10px); opacity: 0; }
}

@keyframes stump-fall-left {
  0%   { transform: rotate(0deg); }
  40%  { transform: rotate(-28deg); }
  100% { transform: rotate(-38deg); opacity: 0.3; }
}
@keyframes stump-fall-back {
  0%   { transform: scaleY(1) translateY(0); }
  100% { transform: scaleY(0.6) translateY(10px); opacity: 0.2; }
}
@keyframes stump-fall-right {
  0%   { transform: rotate(0deg); }
  40%  { transform: rotate(28deg); }
  100% { transform: rotate(38deg); opacity: 0.3; }
}

@keyframes impact {
  0%   { background-color: rgba(253,224,71,0); }
  30%  { background-color: rgba(253,224,71,0.25); }
  100% { background-color: rgba(253,224,71,0); }
}
</style>
