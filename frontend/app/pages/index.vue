<script setup lang="ts">
import { resolveProductImage } from '~/utils/storefront.mjs'
import type { FeaturedProduct } from '~/composables/useStoreSettings'

const { t, tm } = useI18n()
const { data: products } = useCatalog()
const featured = computed(() => products.value.slice(0, 6))

const { data: storeSettings } = await useStoreSettings()

function fallbackFeatured(index: number): FeaturedProduct | null {
  const product = products.value[index]
  if (!product) return null
  return {
    name: product.name,
    slug: product.slug,
    description: product.description,
    image_url: product.images[0]?.url ?? null,
  }
}

const heroProduct = computed(() => storeSettings.value?.featured_hero_product ?? fallbackFeatured(0))
const bannerProduct = computed(() => storeSettings.value?.featured_banner_product ?? fallbackFeatured(1) ?? fallbackFeatured(0))

const heroImageSrc = computed(() => heroProduct.value?.image_url || resolveProductImage(heroProduct.value?.slug ?? ''))
const bannerImageSrc = computed(() => bannerProduct.value?.image_url || resolveProductImage(bannerProduct.value?.slug ?? ''))

useSeoMeta({
  title: "Lyn's Little Kitchen | Fresh cookies in Jasin, Melaka",
  description: 'Small-batch cookies baked fresh in Jasin. Shop Choc Chip Crunch, Dubai Chewy Cookie and our developing menu.',
  ogTitle: "Lyn's Little Kitchen",
  ogDescription: 'Little cookies, big happiness—baked fresh in Jasin, Melaka.',
  ogImage: () => heroImageSrc.value,
})

const config = useRuntimeConfig()

useHead({
  script: () => {
    if (!storeSettings.value) return []
    const settings = storeSettings.value
    return [{
      type: 'application/ld+json',
      innerHTML: JSON.stringify({
        '@context': 'https://schema.org',
        '@type': 'Bakery',
        name: settings.store_name || "Lyn's Little Kitchen",
        address: {
          '@type': 'PostalAddress',
          streetAddress: [settings.address_line1, settings.address_line2].filter(Boolean).join(', '),
          addressLocality: settings.city,
          addressRegion: settings.state,
          postalCode: settings.postcode,
          addressCountry: 'MY',
        },
        telephone: settings.contact_phone,
        openingHours: settings.operating_hours,
        url: config.public.siteUrl,
        image: `${config.public.siteUrl}/images/og-default.png`,
      }),
    }]
  },
})

interface ProcessStep {
  title: string
  text: string
}

const process = computed(() => tm('home.process') as unknown as ProcessStep[])
</script>

<template>
  <div>
    <section class="container-shell">
      <div class="hero-grid">
        <div class="hero-copy">
          <span class="eyebrow">{{ t('home.eyebrow') }}</span>
          <h1 class="hero-title">{{ t('home.heroTitle') }} <em>{{ t('home.heroTitleEmphasis') }}</em></h1>
          <p class="mt-6 max-w-md text-sm leading-7 text-stone-600 sm:text-base">
            {{ t('home.heroIntro') }}
          </p>
          <div class="mt-7 flex flex-wrap gap-3">
            <NuxtLink to="/shop" class="btn-primary">{{ t('home.shopCookies') }} <span aria-hidden="true">→</span></NuxtLink>
            <NuxtLink to="/how-to-order" class="btn-secondary">{{ t('nav.howToOrder') }}</NuxtLink>
          </div>
          <p class="mt-6 text-xs font-semibold text-[#78825e]">● {{ t('home.freshBatches') }}</p>
        </div>
        <div class="hero-photo-wrap">
          <img class="hero-photo" :src="heroImageSrc" :alt="heroProduct ? t('home.heroImageAlt', { name: heroProduct.name }) : t('home.heroImageAltFallback')" width="900" height="900">
          <span class="hero-stamp">{{ t('home.heroStampLine1') }}<br>{{ t('home.heroStampLine2') }}<br>{{ t('home.heroStampLine3') }}</span>
          <span class="crumb-trail" aria-hidden="true">• · • ·</span>
        </div>
      </div>
      <div class="relative z-10 mx-auto -mt-4 w-[94%]">
        <SharedTrustStrip />
      </div>
    </section>

    <section class="container-shell py-16 sm:py-20">
      <div class="section-heading">
        <div>
          <span class="eyebrow">{{ t('home.counterEyebrow') }}</span>
          <h2 class="mt-3">{{ t('home.counterTitle') }}</h2>
        </div>
        <NuxtLink to="/shop" class="hidden text-xs font-bold text-[#a85f4c] sm:block">{{ t('home.viewFullMenu') }} →</NuxtLink>
      </div>
      <div class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-6">
        <ProductCard v-for="product in featured" :key="product.slug" :product="product" compact />
      </div>
      <NuxtLink to="/shop" class="btn-secondary mt-6 w-full sm:hidden">{{ t('home.viewFullMenu') }}</NuxtLink>
    </section>

    <section class="container-shell pb-16 sm:pb-20">
      <div class="grid gap-4 lg:grid-cols-[.85fr_1.2fr_1fr]">
        <article class="paper-card p-6 sm:p-8">
          <span class="eyebrow">{{ t('home.kitchenEyebrow') }}</span>
          <h2 class="mt-4 font-serif text-3xl leading-tight">{{ t('home.kitchenTitle') }}</h2>
          <p class="mt-4 text-sm leading-7 text-stone-600">
            {{ t('home.kitchenText') }}
          </p>
          <NuxtLink to="/about" class="btn-primary mt-6">{{ t('home.meetTheKitchen') }}</NuxtLink>
        </article>

        <div class="relative min-h-72 overflow-hidden rounded-[1.2rem]">
          <img :src="bannerImageSrc" :alt="bannerProduct?.name || t('home.heroImageAltFallback')" class="absolute inset-0 h-full w-full object-cover" width="800" height="800">
          <div v-if="bannerProduct" class="absolute inset-x-4 bottom-4 rounded-xl bg-[#fffaf5]/90 p-4 backdrop-blur">
            <p class="font-serif text-xl">{{ bannerProduct.name }}</p>
            <p v-if="bannerProduct.description" class="mt-1 text-xs text-stone-600 line-clamp-2">{{ bannerProduct.description }}</p>
          </div>
        </div>

        <article class="paper-card p-6 sm:p-8">
          <span class="eyebrow">{{ t('home.whyLoveEyebrow') }}</span>
          <ul class="mt-5 grid gap-4 text-sm">
            <li class="flex gap-3"><span class="text-[#a85f4c]">✓</span> {{ t('home.whyLove1') }}</li>
            <li class="flex gap-3"><span class="text-[#a85f4c]">✓</span> {{ t('home.whyLove2') }}</li>
            <li class="flex gap-3"><span class="text-[#a85f4c]">✓</span> {{ t('home.whyLove3') }}</li>
            <li class="flex gap-3"><span class="text-[#a85f4c]">✓</span> {{ t('home.whyLove4') }}</li>
          </ul>
          <div class="mt-8 border-t border-dashed border-[#a85f4c55] pt-5 font-serif text-xl italic text-[#a85f4c]">{{ t('home.sweeterDays') }}</div>
        </article>
      </div>
    </section>

    <section class="border-y border-[#70453418] bg-[#f4e4d9]/65 py-14 sm:py-16">
      <div class="container-shell">
        <div class="text-center">
          <span class="eyebrow">{{ t('home.processEyebrow') }}</span>
          <h2 class="mt-4 font-serif text-3xl sm:text-4xl">{{ t('home.processTitle') }}</h2>
        </div>
        <div class="process-line mt-10">
          <div v-for="(step, index) in process" :key="step.title" class="process-step">
            <span class="process-number">{{ index + 1 }}</span>
            <strong class="text-sm">{{ step.title }}</strong>
            <span class="text-xs text-stone-500">{{ step.text }}</span>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
