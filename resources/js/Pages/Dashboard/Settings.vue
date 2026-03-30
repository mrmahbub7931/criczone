<template>
  <DashboardLayout>
    <div class="max-w-4xl">

      <!-- Page header -->
      <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">Settings</h1>
        <p class="text-sm text-gray-500 mt-0.5">Manage your site configuration, social links, SEO and more.</p>
      </div>

      <!-- Tab bar -->
      <div class="flex flex-wrap gap-1 mb-6 bg-gray-100 rounded-xl p-1">
        <button v-for="tab in tabs" :key="tab.key" type="button"
          @click="activeTab = tab.key"
          :class="['flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-all',
                   activeTab === tab.key ? 'bg-white text-[#0D47A1] shadow-sm' : 'text-gray-500 hover:text-gray-700']">
          <span>{{ tab.icon }}</span> {{ tab.label }}
        </button>
      </div>

      <!-- Loading skeleton -->
      <div v-if="loading" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
        <div v-for="i in 6" :key="i" class="h-10 bg-gray-100 rounded-lg animate-pulse" />
      </div>

      <template v-else>

        <!-- ══ GENERAL ══════════════════════════════════════════════════════════ -->
        <div v-show="activeTab === 'general'" class="space-y-6">

          <!-- Site Identity -->
          <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-bold text-gray-900 mb-5">Site Identity</h2>

            <!-- Logo + Favicon -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">

              <!-- Logo -->
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-2">Site Logo</label>
                <div class="flex items-center gap-3">
                  <div class="w-16 h-16 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0">
                    <img v-if="form.general.site_logo" :src="form.general.site_logo" class="w-full h-full object-contain" alt="Logo" />
                    <span v-else class="text-2xl select-none">🖼️</span>
                  </div>
                  <div class="flex-1 min-w-0">
                    <label class="cursor-pointer">
                      <input type="file" accept="image/*" class="hidden" @change="uploadFile($event,'logo')" :disabled="uploadingLogo" />
                      <span :class="['inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-lg transition-colors',
                                     uploadingLogo ? 'opacity-50 cursor-not-allowed border-gray-200 text-gray-400' : 'border-[#0D47A1] text-[#0D47A1] hover:bg-blue-50']">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        {{ uploadingLogo ? 'Uploading…' : 'Upload Logo' }}
                      </span>
                    </label>
                    <p class="text-[11px] text-gray-400 mt-1">PNG / SVG — max 2 MB</p>
                    <input v-model="form.general.site_logo" type="url" placeholder="or paste URL…"
                      class="mt-1 w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs outline-none focus:border-[#0D47A1]" />
                  </div>
                </div>
              </div>

              <!-- Favicon -->
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-2">Favicon</label>
                <div class="flex items-center gap-3">
                  <div class="w-16 h-16 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0">
                    <img v-if="form.general.site_favicon" :src="form.general.site_favicon" class="w-10 h-10 object-contain" alt="Favicon" />
                    <span v-else class="text-2xl select-none">🔖</span>
                  </div>
                  <div class="flex-1 min-w-0">
                    <label class="cursor-pointer">
                      <input type="file" accept="image/*" class="hidden" @change="uploadFile($event,'favicon')" :disabled="uploadingFavicon" />
                      <span :class="['inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-lg transition-colors',
                                     uploadingFavicon ? 'opacity-50 cursor-not-allowed border-gray-200 text-gray-400' : 'border-[#0D47A1] text-[#0D47A1] hover:bg-blue-50']">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        {{ uploadingFavicon ? 'Uploading…' : 'Upload Favicon' }}
                      </span>
                    </label>
                    <p class="text-[11px] text-gray-400 mt-1">ICO / PNG 32×32 — max 2 MB</p>
                    <input v-model="form.general.site_favicon" type="url" placeholder="or paste URL…"
                      class="mt-1 w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs outline-none focus:border-[#0D47A1]" />
                  </div>
                </div>
              </div>
            </div>

            <!-- Text fields -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Site Name <span class="text-red-500">*</span></label>
                <input v-model="form.general.site_name" type="text" placeholder="CricZone" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Tagline</label>
                <input v-model="form.general.site_tagline" type="text" placeholder="Your #1 Cricket News Portal" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
              </div>
            </div>
            <div class="mt-4">
              <label class="block text-xs font-semibold text-gray-600 mb-1">Site Description</label>
              <textarea v-model="form.general.site_description" rows="3" placeholder="A short description shown in search engines and social previews…"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1] resize-none" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Articles Per Page</label>
                <select v-model="form.general.articles_per_page" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]">
                  <option value="5">5</option>
                  <option value="10">10</option>
                  <option value="15">15</option>
                  <option value="20">20</option>
                  <option value="25">25</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Timezone</label>
                <select v-model="form.general.timezone" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]">
                  <option v-for="tz in timezones" :key="tz.value" :value="tz.value">{{ tz.label }}</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Behaviour toggles -->
          <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-bold text-gray-900 mb-4">Site Behaviour</h2>
            <div class="divide-y divide-gray-100">
              <!-- Live ticker -->
              <div class="flex items-center justify-between py-3">
                <div>
                  <p class="text-sm font-medium text-gray-700">Live Score Ticker</p>
                  <p class="text-xs text-gray-400">Show live match scores in the public navigation bar</p>
                </div>
                <button type="button" @click="toggleField('general','show_ticker')"
                  :class="['relative w-11 h-6 rounded-full transition-colors',
                           isTruthy(form.general.show_ticker) ? 'bg-[#0D47A1]' : 'bg-gray-200']">
                  <span :class="['absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform',
                                 isTruthy(form.general.show_ticker) ? 'translate-x-6' : 'translate-x-1']" />
                </button>
              </div>
              <!-- Maintenance -->
              <div class="flex items-center justify-between py-3">
                <div>
                  <p class="text-sm font-medium text-gray-700">Maintenance Mode</p>
                  <p class="text-xs text-gray-400">Replace the public site with a maintenance page</p>
                </div>
                <button type="button" @click="toggleField('general','maintenance_mode')"
                  :class="['relative w-11 h-6 rounded-full transition-colors',
                           isTruthy(form.general.maintenance_mode) ? 'bg-red-500' : 'bg-gray-200']">
                  <span :class="['absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform',
                                 isTruthy(form.general.maintenance_mode) ? 'translate-x-6' : 'translate-x-1']" />
                </button>
              </div>
            </div>
          </div>

          <div class="flex justify-end">
            <button @click="save('general')" :disabled="saving" class="save-btn">
              <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              {{ saving ? 'Saving…' : 'Save Changes' }}
            </button>
          </div>
        </div>

        <!-- ══ CONTACT ══════════════════════════════════════════════════════════ -->
        <div v-show="activeTab === 'contact'" class="space-y-6">
          <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-bold text-gray-900 mb-5">Contact Information</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Contact Email</label>
                <input v-model="form.contact.contact_email" type="email" placeholder="info@criczone.com" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Phone Number</label>
                <input v-model="form.contact.contact_phone" type="tel" placeholder="+1 234 567 8900" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">City</label>
                <input v-model="form.contact.contact_city" type="text" placeholder="Mumbai" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Country</label>
                <input v-model="form.contact.contact_country" type="text" placeholder="India" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
              </div>
            </div>
            <div class="mt-4">
              <label class="block text-xs font-semibold text-gray-600 mb-1">Street Address</label>
              <input v-model="form.contact.contact_address" type="text" placeholder="123 Cricket Street, Stadium Road" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
            </div>
          </div>
          <div class="flex justify-end">
            <button @click="save('contact')" :disabled="saving" class="save-btn">
              <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              {{ saving ? 'Saving…' : 'Save Changes' }}
            </button>
          </div>
        </div>

        <!-- ══ SOCIAL ══════════════════════════════════════════════════════════ -->
        <div v-show="activeTab === 'social'" class="space-y-6">
          <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-bold text-gray-900 mb-2">Social Media Links</h2>
            <p class="text-xs text-gray-500 mb-5">Enter full URLs. Leave blank to hide the icon from the site.</p>
            <div class="space-y-4">
              <div v-for="s in socialFields" :key="s.key" class="flex items-center gap-3">
                <div :class="['w-10 h-10 rounded-xl flex items-center justify-center text-lg flex-shrink-0', s.color]">
                  {{ s.icon }}
                </div>
                <div class="flex-1">
                  <label class="block text-xs font-semibold text-gray-500 mb-1">{{ s.label }}</label>
                  <input v-model="form.social[s.key]" type="url" :placeholder="s.placeholder"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
                </div>
              </div>
            </div>
          </div>
          <div class="flex justify-end">
            <button @click="save('social')" :disabled="saving" class="save-btn">
              <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              {{ saving ? 'Saving…' : 'Save Changes' }}
            </button>
          </div>
        </div>

        <!-- ══ SEO ══════════════════════════════════════════════════════════════ -->
        <div v-show="activeTab === 'seo'" class="space-y-6">

          <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-bold text-gray-900 mb-2">Default Meta Tags</h2>
            <p class="text-xs text-gray-500 mb-5">Fallback values used when individual pages don't define their own meta tags.</p>
            <div class="space-y-4">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Meta Title</label>
                <input v-model="form.seo.meta_title" type="text" placeholder="CricZone – Cricket News & Live Scores" maxlength="70"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
                <p :class="['text-[11px] mt-1 text-right', charColor(form.seo.meta_title, 70)]">
                  {{ (form.seo.meta_title || '').length }}/70
                </p>
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Meta Description</label>
                <textarea v-model="form.seo.meta_description" rows="3" maxlength="160"
                  placeholder="Stay up to date with the latest cricket news, live scores and match analysis on CricZone."
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1] resize-none" />
                <p :class="['text-[11px] mt-1 text-right', charColor(form.seo.meta_description, 160)]">
                  {{ (form.seo.meta_description || '').length }}/160
                </p>
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Meta Keywords</label>
                <input v-model="form.seo.meta_keywords" type="text" placeholder="cricket, news, live scores, IPL, T20"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
                <p class="text-[11px] text-gray-400 mt-1">Comma-separated keywords</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-bold text-gray-900 mb-5">Analytics & Tag Manager</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Google Analytics ID</label>
                <input v-model="form.seo.google_analytics_id" type="text" placeholder="G-XXXXXXXXXX"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Google Tag Manager ID</label>
                <input v-model="form.seo.google_tag_manager_id" type="text" placeholder="GTM-XXXXXXX"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-bold text-gray-900 mb-2">Robots.txt</h2>
            <p class="text-xs text-gray-500 mb-3">Controls how search engine crawlers access your site.</p>
            <textarea v-model="form.seo.robots_txt" rows="7"
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm font-mono outline-none focus:border-[#0D47A1] resize-none" />
          </div>

          <div class="flex justify-end">
            <button @click="save('seo')" :disabled="saving" class="save-btn">
              <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              {{ saving ? 'Saving…' : 'Save Changes' }}
            </button>
          </div>
        </div>

        <!-- ══ HOMEPAGE ══════════════════════════════════════════════════════════ -->
        <div v-show="activeTab === 'homepage'" class="space-y-6">

          <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-bold text-gray-900 mb-5">Hero Section</h2>
            <div class="space-y-4">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Hero Title</label>
                <input v-model="form.homepage.homepage_hero_title" type="text" placeholder="Welcome to CricZone"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Hero Subtitle</label>
                <input v-model="form.homepage.homepage_hero_subtitle" type="text" placeholder="Your #1 Source for Cricket News & Live Scores"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-bold text-gray-900 mb-5">Articles Display</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Featured Articles Count</label>
                <select v-model="form.homepage.featured_articles_count" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]">
                  <option value="3">3</option>
                  <option value="4">4</option>
                  <option value="5">5</option>
                  <option value="6">6</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Default Layout</label>
                <select v-model="form.homepage.articles_layout" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]">
                  <option value="grid">Grid</option>
                  <option value="list">List</option>
                  <option value="magazine">Magazine</option>
                </select>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-bold text-gray-900 mb-4">Breaking News Ticker</h2>
            <div class="flex items-center justify-between py-2 mb-4">
              <div>
                <p class="text-sm font-medium text-gray-700">Enable Breaking News Ticker</p>
                <p class="text-xs text-gray-400">Show a scrolling ticker at the top of the homepage</p>
              </div>
              <button type="button" @click="toggleField('homepage','breaking_news_enabled')"
                :class="['relative w-11 h-6 rounded-full transition-colors',
                         isTruthy(form.homepage.breaking_news_enabled) ? 'bg-[#0D47A1]' : 'bg-gray-200']">
                <span :class="['absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform',
                               isTruthy(form.homepage.breaking_news_enabled) ? 'translate-x-6' : 'translate-x-1']" />
              </button>
            </div>
            <div :class="{ 'opacity-40 pointer-events-none': !isTruthy(form.homepage.breaking_news_enabled) }">
              <label class="block text-xs font-semibold text-gray-600 mb-1">Ticker Text</label>
              <input v-model="form.homepage.breaking_news_text" type="text"
                placeholder="BREAKING: India wins the T20 World Cup…"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
            </div>
          </div>

          <div class="flex justify-end">
            <button @click="save('homepage')" :disabled="saving" class="save-btn">
              <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              {{ saving ? 'Saving…' : 'Save Changes' }}
            </button>
          </div>
        </div>

        <!-- ══ FOOTER ══════════════════════════════════════════════════════════ -->
        <div v-show="activeTab === 'footer'" class="space-y-6">
          <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-bold text-gray-900 mb-5">Footer Content</h2>
            <div class="space-y-4">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Footer Description</label>
                <textarea v-model="form.footer.footer_description" rows="3"
                  placeholder="A short tagline shown in the footer…"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1] resize-none" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Copyright Text</label>
                <input v-model="form.footer.footer_copyright" type="text"
                  placeholder="© 2025 CricZone. All rights reserved."
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-gray-600 mb-1">Footer Email</label>
                  <input v-model="form.footer.footer_email" type="email" placeholder="info@criczone.com"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-600 mb-1">Footer Phone</label>
                  <input v-model="form.footer.footer_phone" type="tel" placeholder="+1 234 567 8900"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
                </div>
              </div>
            </div>
          </div>
          <div class="flex justify-end">
            <button @click="save('footer')" :disabled="saving" class="save-btn">
              <svg v-if="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              {{ saving ? 'Saving…' : 'Save Changes' }}
            </button>
          </div>
        </div>

        <!-- ══ PROFILE ══════════════════════════════════════════════════════════ -->
        <div v-show="activeTab === 'profile'" class="space-y-6">

          <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-bold text-gray-900 mb-5">Your Profile</h2>
            <!-- Avatar row -->
            <div class="flex items-center gap-4 mb-6">
              <div class="w-16 h-16 rounded-full bg-[#0D47A1] flex items-center justify-center text-white text-2xl font-black flex-shrink-0">
                {{ (profile.name || '?').charAt(0).toUpperCase() }}
              </div>
              <div>
                <p class="font-semibold text-gray-800">{{ profile.name }}</p>
                <p class="text-xs text-gray-500">{{ profile.email }}</p>
                <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-bold uppercase bg-blue-100 text-[#0D47A1] rounded-full">
                  {{ authRoles[0] ?? 'admin' }}
                </span>
              </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Display Name</label>
                <input v-model="profile.name" type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Email Address</label>
                <input v-model="profile.email" type="email" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-bold text-gray-900 mb-5">Change Password</h2>
            <div class="space-y-4">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Current Password</label>
                <input v-model="profile.current_password" type="password" autocomplete="current-password"
                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-gray-600 mb-1">New Password</label>
                  <input v-model="profile.new_password" type="password" autocomplete="new-password"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-600 mb-1">Confirm New Password</label>
                  <input v-model="profile.confirm_password" type="password" autocomplete="new-password"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#0D47A1]" />
                </div>
              </div>
              <p v-if="passwordError" class="text-xs text-red-500">{{ passwordError }}</p>
            </div>
          </div>

          <div class="flex justify-end">
            <button @click="saveProfile" :disabled="savingProfile" class="save-btn">
              <svg v-if="savingProfile" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              {{ savingProfile ? 'Saving…' : 'Update Profile' }}
            </button>
          </div>
        </div>

      </template>
    </div>

    <!-- Toast notification -->
    <Transition name="fade-up">
      <div v-if="toast.show"
        :class="['fixed bottom-6 right-6 flex items-center gap-2 text-sm font-medium px-4 py-3 rounded-xl shadow-lg z-50',
                 toast.error ? 'bg-red-600 text-white' : 'bg-gray-900 text-white']">
        <span>{{ toast.error ? '✕' : '✓' }}</span>
        {{ toast.message }}
      </div>
    </Transition>

  </DashboardLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

// ── Auth ──────────────────────────────────────────────────────────────────────
const page      = usePage()
const authUser  = computed(() => page.props.auth?.user  ?? {})
const authRoles = computed(() => page.props.auth?.roles ?? [])

// ── Tabs ──────────────────────────────────────────────────────────────────────
const tabs = [
  { key: 'general',  label: 'General',      icon: '⚙️'  },
  { key: 'contact',  label: 'Contact',       icon: '📞'  },
  { key: 'social',   label: 'Social Links',  icon: '🔗'  },
  { key: 'seo',      label: 'SEO',           icon: '🔍'  },
  { key: 'homepage', label: 'Homepage',      icon: '🏠'  },
  { key: 'footer',   label: 'Footer',        icon: '📄'  },
  { key: 'profile',  label: 'My Profile',    icon: '👤'  },
]
const activeTab = ref('general')

// ── Social fields config ──────────────────────────────────────────────────────
const socialFields = [
  { key: 'social_facebook',  label: 'Facebook',    icon: '📘', color: 'bg-blue-100',  placeholder: 'https://facebook.com/yourpage' },
  { key: 'social_twitter',   label: 'X (Twitter)', icon: '✖️', color: 'bg-gray-100',  placeholder: 'https://x.com/yourhandle' },
  { key: 'social_instagram', label: 'Instagram',   icon: '📸', color: 'bg-pink-100',  placeholder: 'https://instagram.com/yourhandle' },
  { key: 'social_youtube',   label: 'YouTube',     icon: '▶️', color: 'bg-red-100',   placeholder: 'https://youtube.com/@yourchannel' },
  { key: 'social_linkedin',  label: 'LinkedIn',    icon: '💼', color: 'bg-blue-100',  placeholder: 'https://linkedin.com/company/yourpage' },
  { key: 'social_whatsapp',  label: 'WhatsApp',    icon: '💬', color: 'bg-green-100', placeholder: 'https://wa.me/1234567890' },
  { key: 'social_telegram',  label: 'Telegram',    icon: '✈️', color: 'bg-sky-100',   placeholder: 'https://t.me/yourchannel' },
  { key: 'social_tiktok',    label: 'TikTok',      icon: '🎵', color: 'bg-gray-100',  placeholder: 'https://tiktok.com/@yourhandle' },
]

// ── Timezones ─────────────────────────────────────────────────────────────────
const timezones = [
  { value: 'UTC',                 label: 'UTC – Coordinated Universal Time'           },
  { value: 'Asia/Kolkata',        label: 'IST – India Standard Time (UTC+5:30)'       },
  { value: 'Asia/Karachi',        label: 'PKT – Pakistan Standard Time (UTC+5)'       },
  { value: 'Asia/Dhaka',          label: 'BST – Bangladesh Standard Time (UTC+6)'     },
  { value: 'Asia/Colombo',        label: 'SLST – Sri Lanka Time (UTC+5:30)'           },
  { value: 'Asia/Kabul',          label: 'AFT – Afghanistan Time (UTC+4:30)'          },
  { value: 'Australia/Sydney',    label: 'AEDT – Australian Eastern Time (UTC+11)'    },
  { value: 'Pacific/Auckland',    label: 'NZDT – New Zealand Time (UTC+13)'           },
  { value: 'Africa/Johannesburg', label: 'SAST – South Africa Standard Time (UTC+2)' },
  { value: 'America/New_York',    label: 'EST – Eastern Time (UTC-5)'                 },
  { value: 'America/Chicago',     label: 'CST – Central Time (UTC-6)'                 },
  { value: 'America/Los_Angeles', label: 'PST – Pacific Time (UTC-8)'                 },
  { value: 'Europe/London',       label: 'GMT – Greenwich Mean Time (UTC+0)'          },
  { value: 'Europe/Paris',        label: 'CET – Central European Time (UTC+1)'        },
  { value: 'Asia/Dubai',          label: 'GST – Gulf Standard Time (UTC+4)'           },
]

// ── Form state ────────────────────────────────────────────────────────────────
const form = reactive({
  general: {
    site_name: '', site_tagline: '', site_description: '',
    site_logo: '', site_favicon: '',
    articles_per_page: '10', timezone: 'UTC',
    show_ticker: '1', maintenance_mode: '0',
  },
  contact: {
    contact_email: '', contact_phone: '',
    contact_address: '', contact_city: '', contact_country: '',
  },
  social: {
    social_facebook: '', social_twitter: '', social_instagram: '',
    social_youtube: '', social_linkedin: '', social_whatsapp: '',
    social_telegram: '', social_tiktok: '',
  },
  seo: {
    meta_title: '', meta_description: '', meta_keywords: '',
    google_analytics_id: '', google_tag_manager_id: '', robots_txt: '',
  },
  homepage: {
    homepage_hero_title: '', homepage_hero_subtitle: '',
    featured_articles_count: '5', breaking_news_enabled: '1',
    breaking_news_text: '', articles_layout: 'grid',
  },
  footer: {
    footer_description: '', footer_copyright: '',
    footer_email: '', footer_phone: '',
  },
})

const profile = reactive({
  name: '', email: '',
  current_password: '', new_password: '', confirm_password: '',
})
const passwordError = ref('')

// ── Flags ─────────────────────────────────────────────────────────────────────
const loading         = ref(true)
const saving          = ref(false)
const savingProfile   = ref(false)
const uploadingLogo   = ref(false)
const uploadingFavicon = ref(false)
const toast = reactive({ show: false, message: '', error: false })
let toastTimer = null

// ── Helpers ───────────────────────────────────────────────────────────────────
const isTruthy = (v) => v === true || v === '1' || v === 1

const toggleField = (group, key) => {
  form[group][key] = isTruthy(form[group][key]) ? '0' : '1'
}

const charColor = (val, max) => {
  const len = (val || '').length
  const pct = len / max
  return pct > 0.9 ? 'text-red-500' : pct > 0.75 ? 'text-amber-500' : 'text-gray-400'
}

const showToast = (message, error = false) => {
  clearTimeout(toastTimer)
  Object.assign(toast, { show: true, message, error })
  toastTimer = setTimeout(() => { toast.show = false }, 3000)
}

// ── Fetch settings ────────────────────────────────────────────────────────────
const fetchSettings = async () => {
  loading.value = true
  try {
    const { data } = await axios.get('/api/settings')
    // Merge each group into form
    for (const group of Object.keys(form)) {
      if (data[group]) Object.assign(form[group], data[group])
    }
    // Populate profile from auth
    profile.name  = authUser.value.name  ?? ''
    profile.email = authUser.value.email ?? ''
  } catch {
    showToast('Failed to load settings.', true)
  } finally {
    loading.value = false
  }
}

// ── Save a group ──────────────────────────────────────────────────────────────
const save = async (group) => {
  saving.value = true
  try {
    // Spread reactive Proxy to a plain object before JSON serialisation
    await axios.put('/api/settings', { [group]: { ...form[group] } })
    showToast('Settings saved successfully.')
  } catch (err) {
    const status = err.response?.status
    const msg    = err.response?.data?.message
                   ?? err.response?.data?.error
                   ?? `Error ${status ?? ''}: Failed to save settings.`
    showToast(msg, true)
    console.error('[Settings save]', status, err.response?.data)
  } finally {
    saving.value = false
  }
}

// ── Logo / favicon upload ─────────────────────────────────────────────────────
const uploadFile = async (event, type) => {
  const file = event.target.files?.[0]
  if (!file) return
  const flagRef = type === 'logo' ? uploadingLogo : uploadingFavicon
  flagRef.value = true
  try {
    const fd = new FormData()
    fd.append('file', file)
    fd.append('type', type)
    // ⚠️ Do NOT set Content-Type manually for FormData — the browser must
    //    add the multipart boundary automatically, otherwise the server
    //    cannot parse the body.
    const { data } = await axios.post('/api/settings/upload-logo', fd)
    if (type === 'logo')    form.general.site_logo    = data.url
    if (type === 'favicon') form.general.site_favicon = data.url
    showToast(`${type.charAt(0).toUpperCase() + type.slice(1)} uploaded successfully.`)
  } catch (err) {
    const status = err.response?.status
    const msg    = err.response?.data?.message
                   ?? err.response?.data?.error
                   ?? `Error ${status ?? ''}: Failed to upload ${type}.`
    showToast(msg, true)
    console.error('[Settings upload]', status, err.response?.data)
  } finally {
    flagRef.value = false
    event.target.value = ''
  }
}

// ── Profile save ──────────────────────────────────────────────────────────────
const saveProfile = async () => {
  passwordError.value = ''
  if (profile.new_password && profile.new_password !== profile.confirm_password) {
    passwordError.value = 'New passwords do not match.'
    return
  }
  if (profile.new_password && profile.new_password.length < 8) {
    passwordError.value = 'Password must be at least 8 characters.'
    return
  }
  savingProfile.value = true
  try {
    const payload = { name: profile.name, email: profile.email }
    if (profile.new_password) {
      payload.current_password        = profile.current_password
      payload.password                = profile.new_password
      payload.password_confirmation   = profile.confirm_password
    }
    await axios.put(`/api/users/${authUser.value.id}`, payload)
    profile.current_password = profile.new_password = profile.confirm_password = ''
    showToast('Profile updated successfully.')
  } catch (err) {
    const errors = err.response?.data?.errors
    if (errors) {
      const first = Object.values(errors)[0]
      showToast(Array.isArray(first) ? first[0] : first, true)
    } else {
      showToast(err.response?.data?.message ?? 'Failed to update profile.', true)
    }
  } finally {
    savingProfile.value = false
  }
}

onMounted(fetchSettings)
</script>

<style scoped>
.save-btn {
  @apply bg-[#0D47A1] hover:bg-blue-800 disabled:opacity-60 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors flex items-center gap-2;
}
.fade-up-enter-active, .fade-up-leave-active { transition: opacity 0.25s, transform 0.25s; }
.fade-up-enter-from, .fade-up-leave-to { opacity: 0; transform: translateY(8px); }
</style>
