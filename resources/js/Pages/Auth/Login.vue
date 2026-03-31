<template>
  <Head>
    <title>CricZone - Login</title>
  </Head>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="w-full max-w-sm">

      <!-- Logo -->
      <div class="text-center mb-8">
        <img :src="logoUrl" alt="CricZone" class="h-14 w-14 rounded-full object-contain mx-auto mb-3" />
        <h1 class="text-2xl font-black text-gray-900">Dashboard Login</h1>
        <p class="text-sm text-gray-400 mt-1">Sign in to manage CricZone</p>
      </div>

      <!-- Card -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form @submit.prevent="submit" class="space-y-4">

          <!-- Global error -->
          <div v-if="form.errors.email" class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3">
            {{ form.errors.email }}
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email</label>
            <input
              v-model="form.email"
              type="email"
              autocomplete="email"
              required
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 outline-none focus:border-primary transition-colors"
              placeholder="admin@criczone.com"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Password</label>
            <input
              v-model="form.password"
              type="password"
              autocomplete="current-password"
              required
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 outline-none focus:border-primary transition-colors"
              placeholder="••••••••"
            />
          </div>

          <div class="flex items-center gap-2">
            <input id="remember" v-model="form.remember" type="checkbox" class="w-4 h-4 accent-primary rounded" />
            <label for="remember" class="text-sm text-gray-600">Remember me</label>
          </div>

          <button
            type="submit"
            :disabled="form.processing"
            class="w-full bg-primary hover:bg-primary-dark disabled:opacity-60 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors mt-2"
          >
            <span v-if="form.processing">Signing in…</span>
            <span v-else>Sign In</span>
          </button>
        </form>
      </div>

      <p class="text-center text-xs text-gray-400 mt-6">
        CricZone Admin Panel &mdash; Restricted Access
      </p>
    </div>
  </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'

const logoUrl = `${window.location.origin}/images/criczone.png`

const form = useForm({
  email:    '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post('/login', {
    onFinish: () => form.reset('password'),
  })
}
</script>
