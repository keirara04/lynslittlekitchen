import { vueRouterDevtoolsNullGuard } from './build/vue-router-devtools-null-guard.mjs'
export default defineNuxtConfig({
  compatibilityDate: '2026-07-22',

  css: ['~/assets/css/main.css', '~/assets/css/admin.css'],

  devtools: {
    enabled: true,
  },

  modules: ['@nuxtjs/tailwindcss', '@pinia/nuxt', '@nuxtjs/i18n', '@nuxtjs/sitemap', '@nuxt/eslint', '@nuxt/image'],

  site: {
    url: process.env.NUXT_PUBLIC_SITE_URL || 'https://lynslittlekitchen.vercel.app',
  },

  sitemap: {
    exclude: ['/admin/**'],
  },

  routeRules: {
    '/': { swr: 3600 },
    '/about': { swr: 3600 },
    '/how-to-order': { swr: 3600 },
  },

  i18n: {
    locales: [
      { code: 'en', language: 'en-MY', name: 'English', file: 'en.json' },
      { code: 'ms', language: 'ms-MY', name: 'Bahasa Melayu', file: 'ms.json' },
    ],
    defaultLocale: 'en',
    langDir: 'locales/',
    strategy: 'no_prefix',
    detectBrowserLanguage: false,
  },

  runtimeConfig: {
    apiBase: process.env.NUXT_API_BASE || 'http://127.0.0.1:8000/api',
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://127.0.0.1:8000/api',
      cloudinaryCloudName: process.env.NUXT_PUBLIC_CLOUDINARY_CLOUD_NAME || '',
      cloudinaryUploadPreset: process.env.NUXT_PUBLIC_CLOUDINARY_UPLOAD_PRESET || '',
      siteUrl: process.env.NUXT_PUBLIC_SITE_URL || 'https://lynslittlekitchen.vercel.app',
    },
  },

  app: {
    head: {
      meta: [
        { name: 'theme-color', content: '#fbf5ee' },
        { name: 'description', content: "Small-batch cookies, baked with love in Jasin, Melaka." },
        { property: 'og:type', content: 'website' },
        { property: 'og:image', content: `${process.env.NUXT_PUBLIC_SITE_URL || 'https://lynslittlekitchen.vercel.app'}/images/og-default.png` },
        { name: 'twitter:card', content: 'summary_large_image' },
      ],
      link: [
        { rel: 'icon', href: '/favicon.ico', sizes: 'any' },
        { rel: 'apple-touch-icon', href: '/apple-touch-icon.png' },
        { rel: 'icon', type: 'image/png', sizes: '192x192', href: '/icon-192.png' },
        { rel: 'icon', type: 'image/png', sizes: '512x512', href: '/icon-512.png' },
      ],
    },
  },

  vite: {
    plugins: [vueRouterDevtoolsNullGuard()],
  },
})