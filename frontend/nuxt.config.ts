import { vueRouterDevtoolsNullGuard } from './build/vue-router-devtools-null-guard.mjs'
export default defineNuxtConfig({
  compatibilityDate: '2026-07-22',

  devtools: {
    enabled: true,
  },

  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt',
  ],

  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE,
    },
  },

  vite: {
    plugins: [vueRouterDevtoolsNullGuard()],
  },
})
