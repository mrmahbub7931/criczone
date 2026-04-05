<template>
  <div class="lg:grid lg:grid-cols-[16rem_1fr]">

    <!-- Sidebar -->
    <aside class="hidden lg:flex flex-col w-64 sticky top-0 h-screen shrink-0 inset-y-0 left-0 bg-[#f7f7f7] border-r border-white/10 overflow-y-auto">
      <div class="px-6 py-5 border-b border-white/5">
        <div class="flex items-center gap-2.5">
          <div class="w-7 h-7 rounded-lg bg-orange-500 flex items-center justify-center text-white font-black text-sm">C</div>
          <div>
            <p class="text-white font-black text-sm leading-none">CricZone</p>
            <p class="text-slate-500 text-[10px] mt-0.5">Developer Docs</p>
          </div>
        </div>
      </div>
      <nav class="flex-1 px-3 py-4 space-y-0.5 text-sm">
        <a v-for="s in sections" :key="s.id" :href="`#${s.id}`"
          class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition-colors"
          :class="{ 'text-orange-400 bg-white/5': activeSection === s.id }">
          <span class="text-base">{{ s.icon }}</span>
          <span class="font-medium">{{ s.label }}</span>
        </a>
      </nav>
      <div class="px-6 py-4 border-t border-white/5 text-[11px] text-slate-600">
        Last updated: {{ lastUpdated }}
      </div>
    </aside>

    <!-- Main -->
    <main class="flex-1 min-w-0">
    <div class="max-w-5xl mx-auto px-6 lg:px-12 py-12 space-y-20">

      <!-- Hero -->
      <div class="border-b border-white/5 pb-10">
        <div class="inline-flex items-center gap-2 bg-orange-500/10 text-orange-400 text-xs font-bold px-3 py-1 rounded-full mb-4 border border-orange-500/20">
          🏏 CricZone CMS
        </div>
        <h1 class="text-4xl font-black text-white mb-3">Developer Documentation</h1>
        <p class="text-slate-400 text-lg leading-relaxed max-w-2xl">
          Complete reference for building, extending and deploying the CricZone cricket news CMS.
          Read this before making any changes to the codebase.
        </p>
        <div class="flex flex-wrap gap-3 mt-6">
          <span v-for="b in badges" :key="b.label" :class="`text-xs font-bold px-3 py-1 rounded-full border ${b.cls}`">{{ b.label }}</span>
        </div>
      </div>

      <!-- 1. Overview -->
      <section id="overview" class="scroll-mt-8">
        <SectionHead icon="🗺" title="1. Overview" />
        <p class="text-slate-400 leading-relaxed mb-6">CricZone is a full-stack cricket news CMS with a Laravel 13 API backend and a Vue 3 SPA frontend connected via Inertia.js. There is no separate API consumer — Inertia bridges server-side Laravel and client-side Vue seamlessly.</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <InfoCard v-for="c in overviewCards" :key="c.title" :title="c.title" :color="c.color" :text="c.text" />
        </div>
      </section>

      <!-- 2. Setup -->
      <section id="setup" class="scroll-mt-8">
        <SectionHead icon="⚙️" title="2. Local Setup" />
        <ol class="space-y-2 mb-6">
          <StepItem v-for="(s, i) in setupSteps" :key="i" :n="i+1" :text="s" />
        </ol>
        <DocAlert type="warning">Never run <code>php artisan storage:link</code> on Hostinger — it is not supported. All uploads write directly to <code>public/uploads/</code> instead.</DocAlert>
      </section>

      <!-- 3. Structure -->
      <section id="structure" class="scroll-mt-8">
        <SectionHead icon="📁" title="3. Directory Structure" />
        <pre class="bg-[#0d1117] border border-white/5 rounded-xl px-5 py-4 text-xs text-green-300 font-mono leading-relaxed overflow-x-auto mb-4 whitespace-pre">{{ structureCode }}</pre>
      </section>

      <!-- 4. Database -->
      <section id="database" class="scroll-mt-8">
        <SectionHead icon="🗄" title="4. Database Schema" />
        <p class="text-slate-400 leading-relaxed mb-4">Core tables and their key columns:</p>
        <DocTable :cols="dbCols" :rows="dbRows" />
        <DocAlert type="info">All schema changes go through migrations — never edit the DB directly. Create a new migration with <code>php artisan make:migration add_X_to_Y_table</code>.</DocAlert>
      </section>

      <!-- 5. API -->
      <section id="api" class="scroll-mt-8">
        <SectionHead icon="🔌" title="5. API Reference" />
        <p class="text-slate-400 leading-relaxed mb-4">All routes are prefixed <code>/api/</code> and use session-based auth (no tokens needed).</p>

        <h3 class="text-base font-black text-white mt-6 mb-3">Public Routes (no auth)</h3>
        <DocTable :cols="apiCols" :rows="publicApiRows" />

        <h3 class="text-base font-black text-white mt-6 mb-3">Authenticated — Admin + Editor</h3>
        <DocTable :cols="apiCols" :rows="authApiRows" />

        <h3 class="text-base font-black text-white mt-6 mb-3">Admin-only Routes</h3>
        <DocTable :cols="apiCols" :rows="adminApiRows" />
      </section>

      <!-- 6. Frontend -->
      <section id="frontend" class="scroll-mt-8">
        <SectionHead icon="🖥" title="6. Frontend Architecture" />

        <h3 class="text-base font-black text-white mt-2 mb-3">Composables</h3>
        <DocTable :cols="composableCols" :rows="composableRows" />

        <h3 class="text-base font-black text-white mt-6 mb-3">usePermission</h3>
        <pre class="bg-[#0d1117] border border-white/5 rounded-xl px-5 py-4 text-xs text-green-300 font-mono leading-relaxed overflow-x-auto mb-4 whitespace-pre">{{ permissionCode }}</pre>

        <h3 class="text-base font-black text-white mt-6 mb-3">useSettings</h3>
        <pre class="bg-[#0d1117] border border-white/5 rounded-xl px-5 py-4 text-xs text-green-300 font-mono leading-relaxed overflow-x-auto mb-4 whitespace-pre">{{ settingsCode }}</pre>

        <h3 class="text-base font-black text-white mt-6 mb-3">RichEditor (Tiptap v3)</h3>
        <p class="text-slate-400 leading-relaxed mb-3">Drop the editor in any form with v-model:</p>
        <pre class="bg-[#0d1117] border border-white/5 rounded-xl px-5 py-4 text-xs text-green-300 font-mono leading-relaxed overflow-x-auto mb-4 whitespace-pre">{{ richEditorCode }}</pre>
        <p class="text-slate-400 leading-relaxed mb-2">Supported features: Bold, Italic, Underline, Strike, Text colour, H2/H3/H4, Align left/center/right, Bullet list, Ordered list, Blockquote, Link, Inline image upload, Inline video embed (YouTube/Vimeo/Dailymotion), HR, Undo/Redo, HTML source view.</p>
        <DocAlert type="warning">CodeBlock and Code extensions are intentionally disabled — they caused content to be wrapped in pre/code tags when pasting. Do not re-enable them.</DocAlert>
      </section>

      <!-- 7. Permissions -->
      <section id="permissions" class="scroll-mt-8">
        <SectionHead icon="🔐" title="7. Permission System" />
        <p class="text-slate-400 leading-relaxed mb-4">Permissions follow a <strong class="text-white">module.action</strong> naming convention (e.g. <code>articles.create</code>). All permissions are defined in <code>config/permissions.php</code> and synced to the DB.</p>

        <h3 class="text-base font-black text-white mt-2 mb-3">Adding a New Permission</h3>
        <ol class="space-y-2 mb-6">
          <StepItem v-for="(s, i) in permissionSteps" :key="i" :n="i+1" :text="s" />
        </ol>

        <h3 class="text-base font-black text-white mt-6 mb-3">Backend Middleware Usage</h3>
        <pre class="bg-[#0d1117] border border-white/5 rounded-xl px-5 py-4 text-xs text-green-300 font-mono leading-relaxed overflow-x-auto mb-4 whitespace-pre">{{ middlewareCode }}</pre>

        <h3 class="text-base font-black text-white mt-6 mb-3">Admin Bypass</h3>
        <p class="text-slate-400 leading-relaxed mb-4">The <code>admin</code> role always passes all permission checks. On the backend <code>User::hasPermission()</code> returns true if user is admin. On the frontend the permissions prop is <code>['*']</code> so <code>can()</code> always returns true.</p>
      </section>

      <!-- 8. Settings -->
      <section id="settings" class="scroll-mt-8">
        <SectionHead icon="⚙" title="8. Settings System" />
        <p class="text-slate-400 leading-relaxed mb-4">All site configuration is stored in the <code>settings</code> table as key-value pairs grouped by module.</p>
        <DocTable :cols="settingsCols" :rows="settingsRows" />

        <h3 class="text-base font-black text-white mt-6 mb-3">Adding a New Setting</h3>
        <ol class="space-y-2 mb-4">
          <StepItem v-for="(s, i) in settingSteps" :key="i" :n="i+1" :text="s" />
        </ol>
        <DocAlert type="info">The public endpoint <code>GET /api/settings/public</code> excludes the <code>smtp</code> group. Never store secrets outside the smtp group.</DocAlert>
      </section>

      <!-- 9. Uploads -->
      <section id="uploads" class="scroll-mt-8">
        <SectionHead icon="🖼" title="9. Image Upload System" />
        <p class="text-slate-400 leading-relaxed mb-4">All images are stored directly in <code>public/</code> — no symlinks required.</p>
        <DocTable :cols="uploadCols" :rows="uploadRows" />

        <h3 class="text-base font-black text-white mt-6 mb-3">Upload from Vue</h3>
        <pre class="bg-[#0d1117] border border-white/5 rounded-xl px-5 py-4 text-xs text-green-300 font-mono leading-relaxed overflow-x-auto mb-4 whitespace-pre">{{ uploadCode }}</pre>
      </section>

      <!-- 10. Video -->
      <section id="video" class="scroll-mt-8">
        <SectionHead icon="🎬" title="10. Video System" />
        <p class="text-slate-400 leading-relaxed mb-4">Videos are embed-based — no self-hosting. Any article can have a <code>video_url</code>. The model auto-converts it to an embed URL via the <code>video_embed_url</code> accessor.</p>
        <DocTable :cols="videoCols" :rows="videoRows" />
        <p class="text-slate-400 leading-relaxed mt-4">On the article page, when <code>video_embed_url</code> is present it renders a responsive 16:9 iframe instead of the featured image. In the rich editor, videos can also be embedded inline anywhere in the content.</p>
      </section>

      <!-- 11. New Module -->
      <section id="new-module" class="scroll-mt-8">
        <SectionHead icon="🧩" title="11. Adding a New Module" />
        <p class="text-slate-400 leading-relaxed mb-4">Follow this checklist every time you add a new feature module (e.g. Ads, Tournaments):</p>
        <ol class="space-y-2 mb-6">
          <StepItem v-for="(s, i) in newModuleSteps" :key="i" :n="i+1" :text="s" />
        </ol>
        <DocAlert type="success">Tip: Copy Dashboard/Videos/Index.vue as your starting template — it has full CRUD, grid view, modal form, image upload, toast, delete confirm and pagination already wired up.</DocAlert>
      </section>

      <!-- 12. Slugs -->
      <section id="slugs" class="scroll-mt-8">
        <SectionHead icon="🔗" title="12. Slug Generation" />
        <p class="text-slate-400 leading-relaxed mb-4">Slugs support any Unicode script (Bangla, Arabic, Hindi, etc.) thanks to the <code>\p{L}\p{M}\p{N}</code> regex pattern with the <code>u</code> flag.</p>
        <pre class="bg-[#0d1117] border border-white/5 rounded-xl px-5 py-4 text-xs text-green-300 font-mono leading-relaxed overflow-x-auto mb-4 whitespace-pre">{{ slugCode }}</pre>
        <DocAlert type="warning"><code>\p{M}</code> is critical for Bangla — vowel signs (া, ি, ে, ু) are Unicode Mark category, not Letter. Without it they get stripped.</DocAlert>
      </section>

      <!-- 13. Maintenance -->
      <section id="maintenance" class="scroll-mt-8">
        <SectionHead icon="🔧" title="13. Maintenance Mode" />
        <p class="text-slate-400 leading-relaxed mb-4">Toggle from Dashboard → Settings → General → Maintenance Mode. No server restart needed — reads from DB on every request.</p>
        <DocTable :cols="maintCols" :rows="maintRows" />
      </section>

      <!-- 14. Deployment -->
      <section id="deployment" class="scroll-mt-8">
        <SectionHead icon="🚀" title="14. Deployment (Hostinger)" />
        <ol class="space-y-2 mb-6">
          <StepItem v-for="(s, i) in deploySteps" :key="i" :n="i+1" :text="s" />
        </ol>
        <DocAlert type="warning">Never commit <code>.env</code> to Git. Always set <code>APP_KEY</code> on the server.</DocAlert>

        <h3 class="text-base font-black text-white mt-6 mb-3">Pre-launch Checklist</h3>
        <pre class="bg-[#0d1117] border border-white/5 rounded-xl px-5 py-4 text-xs text-green-300 font-mono leading-relaxed overflow-x-auto mb-4 whitespace-pre">{{ deployChecklist }}</pre>
      </section>

      <!-- Footer -->
      <div class="border-t border-white/5 pt-10 text-center text-slate-600 text-sm">
        CricZone Developer Docs · Laravel 13 + Vue 3 + Inertia.js
        <span class="mx-2">·</span>
        <a href="/dashboard" class="text-orange-400 hover:underline">Back to Dashboard</a>
      </div>

    </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, defineComponent, h } from 'vue'

// ── Inline sub-components ─────────────────────────────────────────────────────

const SectionHead = defineComponent({
  props: { icon: String, title: String },
  setup(props) {
    return () => h('div', { class: 'flex items-center gap-3 mb-6' }, [
      h('span', { class: 'text-2xl' }, props.icon),
      h('h2', { class: 'text-2xl font-black text-white' }, props.title),
    ])
  },
})

const InfoCard = defineComponent({
  props: { title: String, color: String, text: String },
  setup(props) {
    const borders = { blue: 'border-blue-500/30', green: 'border-green-500/30', orange: 'border-orange-500/30' }
    const texts   = { blue: 'text-blue-400',      green: 'text-green-400',      orange: 'text-orange-400'      }
    return () => h('div', { class: `bg-white/5 border ${borders[props.color] ?? 'border-white/10'} rounded-xl p-5` }, [
      h('p', { class: `text-sm font-black mb-2 ${texts[props.color] ?? 'text-white'}` }, props.title),
      h('p', { class: 'text-sm text-slate-400 leading-relaxed' }, props.text),
    ])
  },
})

const StepItem = defineComponent({
  props: { n: Number, text: String },
  setup(props) {
    return () => h('li', { class: 'flex items-start gap-3 text-sm text-slate-400' }, [
      h('span', { class: 'shrink-0 w-6 h-6 rounded-full bg-orange-500/20 text-orange-400 text-xs font-black flex items-center justify-center mt-0.5' }, String(props.n)),
      h('span', { class: 'leading-relaxed', innerHTML: props.text }),
    ])
  },
})

const DocAlert = defineComponent({
  props: { type: String },
  setup(props, { slots }) {
    const map = {
      warning: { bg: 'bg-yellow-500/10 border-yellow-500/30', text: 'text-yellow-300', icon: '⚠️' },
      info:    { bg: 'bg-blue-500/10 border-blue-500/30',     text: 'text-blue-300',   icon: 'ℹ️' },
      success: { bg: 'bg-green-500/10 border-green-500/30',   text: 'text-green-300',  icon: '✅' },
    }
    const s = map[props.type] ?? map.info
    return () => h('div', { class: `flex items-start gap-3 border rounded-xl px-4 py-3 mt-4 mb-4 text-sm ${s.bg} ${s.text}` }, [
      h('span', { class: 'shrink-0 mt-0.5' }, s.icon),
      h('span', { class: 'leading-relaxed' }, slots.default?.()),
    ])
  },
})

const DocTable = defineComponent({
  props: { cols: Array, rows: Array },
  setup(props) {
    return () => h('div', { class: 'overflow-x-auto mb-6' },
      h('table', { class: 'w-full text-sm border-collapse' }, [
        h('thead', {}, h('tr', {}, props.cols.map(c =>
          h('th', { class: 'text-left px-4 py-2.5 text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-white/5 bg-white/3' }, c)
        ))),
        h('tbody', {}, props.rows.map((row, ri) =>
          h('tr', { class: ri % 2 === 0 ? 'bg-white/[0.02]' : '' }, row.map(cell =>
            h('td', { class: 'px-4 py-2.5 text-slate-400 border-b border-white/5 align-top text-xs leading-relaxed font-mono', innerHTML: cell })
          ))
        )),
      ])
    )
  },
})

// ── Data ──────────────────────────────────────────────────────────────────────

const lastUpdated = new Date().toLocaleDateString('en-US', { month: 'long', year: 'numeric' })

const sections = [
  { id: 'overview',    label: 'Overview',            icon: '🗺'  },
  { id: 'setup',       label: 'Local Setup',          icon: '⚙️'  },
  { id: 'structure',   label: 'Directory Structure',  icon: '📁'  },
  { id: 'database',    label: 'Database Schema',      icon: '🗄'  },
  { id: 'api',         label: 'API Reference',        icon: '🔌'  },
  { id: 'frontend',    label: 'Frontend',             icon: '🖥'  },
  { id: 'permissions', label: 'Permissions',          icon: '🔐'  },
  { id: 'settings',    label: 'Settings',             icon: '⚙'  },
  { id: 'uploads',     label: 'Image Uploads',        icon: '🖼'  },
  { id: 'video',       label: 'Video System',         icon: '🎬'  },
  { id: 'new-module',  label: 'New Module Guide',     icon: '🧩'  },
  { id: 'slugs',       label: 'Slug Generation',      icon: '🔗'  },
  { id: 'maintenance', label: 'Maintenance Mode',     icon: '🔧'  },
  { id: 'deployment',  label: 'Deployment',           icon: '🚀'  },
]

const badges = [
  { label: 'Laravel 13',    cls: 'bg-red-500/10 text-red-400 border-red-500/20' },
  { label: 'Vue 3',         cls: 'bg-green-500/10 text-green-400 border-green-500/20' },
  { label: 'Inertia.js v3', cls: 'bg-purple-500/10 text-purple-400 border-purple-500/20' },
  { label: 'Tiptap v3',     cls: 'bg-orange-500/10 text-orange-400 border-orange-500/20' },
  { label: 'Tailwind v4',   cls: 'bg-blue-500/10 text-blue-400 border-blue-500/20' },
  { label: 'MySQL',         cls: 'bg-slate-500/10 text-slate-400 border-slate-500/20' },
]

const overviewCards = [
  { title: 'Backend', color: 'blue', text: 'Laravel 13 · MySQL · Eloquent ORM · Custom middleware for auth, roles, permissions, maintenance mode' },
  { title: 'Frontend', color: 'green', text: 'Vue 3 Composition API · Inertia.js v3 · Tailwind CSS v4 · Tiptap v3 rich editor · Lucide icons' },
  { title: 'Storage', color: 'orange', text: 'All uploads go to public/uploads/ — no storage:link required (Hostinger-safe)' },
]

const setupSteps = [
  'Clone the repository into your web root (MAMP htdocs / Herd / etc.)',
  'Copy <code>.env.example</code> to <code>.env</code> and fill DB credentials, APP_URL, APP_KEY',
  'Run: <code>composer install</code>',
  'Run: <code>php artisan key:generate</code>',
  'Run: <code>php artisan migrate</code>',
  'Run: <code>php artisan permissions:sync</code> — seeds all 28 permissions from config',
  'Run: <code>php artisan db:seed</code> (optional — seeds demo roles and admin user)',
  'Run: <code>npm install &amp;&amp; npm run dev</code>',
  'Visit <code>http://localhost</code> and <code>/login</code> for dashboard access',
]

const structureCode = `app/
  Console/Commands/
    SyncPermissions.php          ← php artisan permissions:sync
  Http/
    Controllers/                 ← one controller per resource
    Middleware/
      CheckMaintenanceMode.php
      EnsureHasRole.php          ← middleware('role:admin,editor')
      EnsureHasPermission.php    ← middleware('permission:articles.create')
      HandleInertiaRequests.php  ← shares auth.permissions to all pages
  Models/
    Article.php  Category.php  User.php  Role.php
    Permission.php  Setting.php  Poll.php  PollVote.php

config/
  permissions.php   ← SINGLE SOURCE OF TRUTH for all permissions

resources/js/
  Components/
    BreakingNewsTicker.vue
    DashboardSidebar.vue
    NewsCard.vue
    NotificationBell.vue
    PollWidget.vue
    RichEditor.vue               ← Tiptap v3 full editor
  composables/
    useDashboard.js
    useMenu.js
    usePermission.js             ← can('articles.create')
    useSettings.js               ← setting('general','site_name')
  Pages/
    Home.vue  Search.vue  Videos.vue  DevDocs.vue
    Article/Show.vue
    Category/Show.vue
    Dashboard/
      Articles/Index.vue         ← real API CRUD with RichEditor
      Videos/Index.vue
      Roles/Index.vue            ← permission checkboxes
      Settings.vue               ← all site settings tabs

routes/
  web.php    ← Inertia page routes
  api.php    ← all JSON API routes

public/
  uploads/
    articles/   ← article images
    settings/   ← logo + favicon`

const dbCols = ['Table', 'Key Columns', 'Notes']
const dbRows = [
  ['users', 'id, name, email, password, avatar, status', 'status: active/inactive'],
  ['roles', 'id, name (slug), display_name, description', 'name must be lowercase-slug'],
  ['role_user', 'role_id, user_id', 'pivot many-to-many'],
  ['permissions', 'id, name, display_name, group', 'name format: module.action'],
  ['permission_role', 'permission_id, role_id', 'pivot many-to-many'],
  ['articles', 'id, title, slug, excerpt, content, featured_image, video_url, category_id, author_id, status, is_featured, views, published_at', 'status: draft/published'],
  ['categories', 'id, name, slug, description, color, is_active', 'color is hex string'],
  ['settings', 'id, key, value, group', 'all site config stored here'],
  ['polls', 'id, question, options (JSON), is_active, ends_at', 'options: array of strings'],
  ['poll_votes', 'id, poll_id, option_index, voter_id', 'voter_id: sha256(IP+UA)'],
  ['menus', 'id, name, location', 'location: header / footer_quick etc'],
  ['menu_items', 'id, menu_id, parent_id, label, url, order', 'supports nested items'],
  ['pages', 'id, title, slug, content, status', 'CMS static pages'],
  ['live_scores', 'id, team1, team2, score, status, match_type', 'status: live/upcoming/completed'],
  ['newsletter_subscribers', 'id, email, token, subscribed_at, unsubscribed_at', 'token used for unsubscribe link'],
]

const apiCols = ['Method', 'Endpoint', 'Controller@Method / Notes']
const publicApiRows = [
  ['GET', '/api/settings/public', 'SettingController@publicIndex — all groups except smtp'],
  ['GET', '/api/articles/featured', 'ArticleController@featured'],
  ['GET', '/api/articles/latest', 'ArticleController@latest — supports ?per_page='],
  ['GET', '/api/articles/trending', 'ArticleController@trending'],
  ['GET', '/api/articles/most-read-week', 'ArticleController@mostReadWeek'],
  ['GET', '/api/articles/videos', 'ArticleController@videos — articles with video_url'],
  ['GET', '/api/articles/{slug}', 'ArticleController@showBySlug — increments views'],
  ['GET', '/api/articles/{slug}/related', 'ArticleController@related — same category, limit 3'],
  ['GET', '/api/categories/public', 'CategoryController@publicList'],
  ['GET', '/api/categories/{slug}/articles', 'CategoryController@categoryArticles'],
  ['GET', '/api/menus/{location}/items', 'MenuController@publicMenu'],
  ['GET', '/api/live-scores', 'LiveScoreController@publicList'],
  ['GET', '/api/search', 'ArticleController@search — ?q= param'],
  ['GET', '/api/poll/active', 'PollController@active'],
  ['POST', '/api/poll/{poll}/vote', 'PollController@vote — deduped by IP+UA hash'],
  ['POST', '/api/newsletter/subscribe', 'NewsletterController@subscribe'],
  ['GET', '/api/pages/{slug}', 'PageController@show'],
]
const authApiRows = [
  ['POST', '/api/upload-image', 'Returns { url } — saved to public/uploads/articles/'],
  ['GET/POST/PUT/DELETE', '/api/articles', 'Full CRUD — video_url accepted'],
  ['GET/POST/PUT/DELETE', '/api/categories', 'Category management'],
  ['GET/POST/PUT/DELETE', '/api/pages', 'CMS static pages'],
]
const adminApiRows = [
  ['GET/PUT', '/api/settings', 'Read/write all settings'],
  ['POST', '/api/settings/upload-logo', 'Logo and favicon — saved to public/uploads/settings/'],
  ['GET/POST/PUT/DELETE', '/api/roles', 'Role CRUD + users listing'],
  ['PUT', '/api/roles/{role}/permissions', 'Sync permission IDs array to a role'],
  ['GET', '/api/permissions', 'All permissions grouped by module'],
  ['GET/POST/PUT/DELETE', '/api/users', 'User management + role assignment'],
  ['GET/POST/PUT/DELETE', '/api/admin/live-scores', 'Live score management'],
  ['GET/POST/PUT/DELETE', '/api/admin/polls', 'Poll management'],
  ['GET/DELETE/POST', '/api/admin/newsletter/...', 'Subscribers list, delete, send newsletter'],
  ['GET/POST/PUT/DELETE', '/api/menus', 'Menu + menu-item management'],
]

const composableCols = ['File', 'Usage', 'Returns']
const composableRows = [
  ['useSettings.js', 'Fetches /api/settings/public once, cached module-level', 'setting(group, key, default)'],
  ['usePermission.js', 'Reads auth.permissions from Inertia shared props', 'can(...perms), isAdmin'],
  ['useMenu.js', 'Fetches a menu by location slug', '{ items, loading }'],
  ['useDashboard.js', 'Dashboard stats, articles, live scores state', 'fetchDashboard(), stats, articles, ...'],
]

const permissionCode = `import { usePermission } from '@/composables/usePermission.js'

const { can, isAdmin } = usePermission()

// In template:
// <button v-if="can('articles.delete')">Delete</button>
// <div v-if="isAdmin">Admin-only section</div>

// Check multiple — user needs ANY one:
// can('articles.create', 'articles.edit')`

const settingsCode = `import { useSettings } from '@/composables/useSettings.js'

const { get: setting } = useSettings()

const siteName = computed(() => setting('general', 'site_name', 'CricZone'))
const gaId     = computed(() => setting('seo', 'google_analytics_id', ''))`

const richEditorCode = `<RichEditor
  v-model="form.content"
  placeholder="Write here..."
  :has-error="!!errors.content"
/>`

const middlewareCode = `// Role-based (coarse — whole group of routes):
Route::middleware('role:admin,editor')->group(function () { ... });

// Permission-based (fine — specific action):
Route::middleware('permission:articles.delete')
  ->delete('/api/articles/{id}', [ArticleController::class, 'destroy']);

// Multiple — user needs ANY one:
Route::middleware('permission:articles.create,articles.edit')->...`

const permissionSteps = [
  'Open <code>config/permissions.php</code>',
  'Add the new module/action — e.g. <code>"ads" => ["create", "edit", "delete"]</code>',
  'Run: <code>php artisan permissions:sync</code>',
  'Go to Dashboard → Roles → assign the permission to the relevant role via checkboxes',
  'Protect the API route: <code>middleware("permission:ads.create")</code>',
  'Guard the UI: <code>v-if="can(\'ads.create\')"</code>',
]

const settingsCols = ['Group', 'Keys', 'Notes']
const settingsRows = [
  ['general', 'site_name, site_tagline, site_logo, site_favicon, articles_per_page, maintenance_mode', 'Core site config'],
  ['homepage', 'breaking_news_enabled, breaking_news_text, latest_per_page, category_per_page, trending_count, most_read_count, featured_articles_count', 'Feed limits and home layout'],
  ['seo', 'meta_title, meta_description, meta_keywords, google_analytics_id, google_tag_manager_id, robots_txt', 'SEO and analytics'],
  ['social', 'social_facebook, social_twitter, social_instagram, social_youtube, social_linkedin, social_whatsapp', 'Footer social links'],
  ['contact', 'contact_email, contact_phone, contact_address, contact_city, contact_country', 'Contact info'],
  ['footer', 'footer_description, footer_copyright, footer_email, footer_phone', 'Footer text'],
  ['smtp', 'smtp_host, smtp_port, smtp_username, smtp_password, smtp_encryption, smtp_from_email', 'Email — EXCLUDED from public API'],
  ['newsletter', 'newsletter_enabled, newsletter_subject_prefix, newsletter_welcome_subject, newsletter_welcome_body', 'Newsletter config'],
  ['poll', 'poll_enabled', 'Poll feature toggle'],
]

const settingSteps = [
  'Add the key to the GROUPS whitelist in <code>SettingController.php</code>',
  'Add the UI input to the correct tab in <code>Dashboard/Settings.vue</code>',
  'Read it in frontend: <code>setting("group", "key", "default")</code>',
  'Read it in backend: <code>Setting::get("key", "default")</code>',
]

const uploadCols = ['Type', 'Endpoint', 'Saved To', 'URL']
const uploadRows = [
  ['Article images (editor + featured)', 'POST /api/upload-image', 'public/uploads/articles/{uuid}.ext', '/uploads/articles/filename.jpg'],
  ['Site logo', 'POST /api/settings/upload-logo (type=logo)', 'public/uploads/settings/{uuid}.ext', '/uploads/settings/filename.jpg'],
  ['Site favicon', 'POST /api/settings/upload-logo (type=favicon)', 'public/uploads/settings/{uuid}.ext', '/uploads/settings/filename.ico'],
]

const uploadCode = `const fd = new FormData()
fd.append('image', file)
const { data } = await axios.post('/api/upload-image', fd, {
  headers: { 'Content-Type': 'multipart/form-data' }
})
form.featured_image = data.url   // e.g. /uploads/articles/uuid.jpg`

const videoCols = ['Platform', 'Input URL Format', 'Converted Embed URL']
const videoRows = [
  ['YouTube', 'youtube.com/watch?v=ID or youtu.be/ID or youtube.com/shorts/ID', 'youtube.com/embed/ID'],
  ['Vimeo', 'vimeo.com/ID', 'player.vimeo.com/video/ID'],
  ['Dailymotion', 'dailymotion.com/video/ID', 'dailymotion.com/embed/video/ID'],
]

const newModuleSteps = [
  'Migration: <code>php artisan make:migration create_ads_table</code> — add columns, run migrate',
  'Model: <code>app/Models/Ad.php</code> — add $fillable, relationships, accessors',
  'Controller: <code>app/Http/Controllers/AdController.php</code> — CRUD + public endpoints',
  'API Routes: add to <code>routes/api.php</code> under correct middleware group',
  'Permissions: add <code>"ads" => ["create","edit","delete"]</code> to <code>config/permissions.php</code>, run <code>php artisan permissions:sync</code>',
  'Web Route: add <code>GET /dashboard/ads</code> in <code>routes/web.php</code>',
  'Sidebar: add entry to <code>DashboardSidebar.vue</code> allItems array',
  'Dashboard Page: create <code>resources/js/Pages/Dashboard/Ads/Index.vue</code> — copy from Videos/Index.vue',
  'Public Page (if needed): create <code>resources/js/Pages/Ads.vue</code> + public web route',
  'Permission guards: wrap delete/edit buttons with <code>v-if="can(\'ads.delete\')"</code>',
]

const slugCode = `// Used in all dashboard article/video forms:
const autoSlug = () => {
  form.value.slug = form.value.title
    .trim()
    .toLowerCase()
    .replace(/[\\s]+/g, '-')              // spaces → hyphens
    .replace(/[^\\p{L}\\p{M}\\p{N}-]/gu, '') // keep any-lang letters + marks + numbers
    .replace(/-{2,}/g, '-')              // collapse double hyphens
    .replace(/^-|-$/g, '')               // trim leading/trailing hyphens
}

// Bangla example:
// "২০২৬ সালে কিভাবে শুরু করবেন?" → "২০২৬-সালে-কিভাবে-শুরু-করবেন"`

const maintCols = ['Who', 'Access']
const maintRows = [
  ['Visitors (logged out)', 'Redirected to /maintenance page'],
  ['Admin / Editor (logged in)', 'Full site access — they can review while site is down'],
  ['/login, /dashboard, /api/*', 'Always bypassed so admin can log in and manage'],
]

const deploySteps = [
  'Upload all files via FTP or Git — point domain to the <code>/public</code> folder',
  'Set <code>APP_ENV=production</code>, <code>APP_DEBUG=false</code> in <code>.env</code>',
  'Set correct DB credentials in <code>.env</code>',
  'Run: <code>php artisan migrate --force</code>',
  'Run: <code>php artisan permissions:sync</code>',
  'Run: <code>php artisan config:cache &amp;&amp; php artisan route:cache</code>',
  'Build frontend: <code>npm install &amp;&amp; npm run build</code> (or build locally and upload <code>public/build/</code>)',
  'Ensure <code>public/uploads/</code> directory exists and is writable (chmod 775)',
  'Do NOT run <code>php artisan storage:link</code> — not needed and not supported on Hostinger',
]

const deployChecklist = `□ APP_ENV=production
□ APP_DEBUG=false
□ DB credentials correct and tested
□ php artisan migrate --force
□ php artisan permissions:sync
□ php artisan config:cache
□ npm run build (assets compiled into public/build/)
□ public/uploads/ directory is writable
□ Site logo + favicon uploaded in Settings
□ Google Analytics ID set in Settings → SEO
□ Maintenance mode is OFF`

// ── Active section tracking (IntersectionObserver) ────────────────────────────
const activeSection = ref('overview')
let observer = null

onMounted(() => {
  observer = new IntersectionObserver(
    (entries) => entries.forEach(e => { if (e.isIntersecting) activeSection.value = e.target.id }),
    { rootMargin: '-20% 0px -70% 0px', threshold: 0 }
  )
  sections.forEach(s => {
    const el = document.getElementById(s.id)
    if (el) observer.observe(el)
  })
})

onUnmounted(() => observer?.disconnect())
</script>

<style scoped>
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
code {
  background: rgba(255,255,255,0.08);
  color: #fb923c;
  padding: 1px 6px;
  border-radius: 4px;
  font-size: 0.82em;
  font-family: monospace;
}
</style>

<style>
body { background-color: #0f172a !important; }
</style>
