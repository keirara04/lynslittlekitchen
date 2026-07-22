import { vueRouterDevtoolsNullGuard } from './build/vue-router-devtools-null-guard.mjs'
export default defineNuxtConfig({
  compatibilityDate: '2026-07-22',

  css: ['~/assets/css/main.css'],

  devtools: {
    enabled: true,
  },

  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt',
  ],

  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://127.0.0.1:8000/api',
    },
  },

  app: {
    head: {
      htmlAttrs: { lang: 'en-MY' },
      meta: [
        { name: 'theme-color', content: '#fbf5ee' },
        { name: 'description', content: "Small-batch cookies, baked with love in Jasin, Melaka." },
      ],
    },
  },

  vite: {
    plugins: [vueRouterDevtoolsNullGuard()],
  },
})
