<script setup lang="ts">
import { resolveProductImage } from '~/utils/storefront.mjs'

useSeoMeta({
  title: "Lyn's Little Kitchen | Fresh cookies in Jasin, Melaka",
  description: 'Small-batch cookies baked fresh in Jasin. Shop Choc Chip Crunch, Dubai Chewy Cookie and our developing menu.',
  ogTitle: "Lyn's Little Kitchen",
  ogDescription: 'Little cookies, big happiness—baked fresh in Jasin, Melaka.',
  ogImage: '/images/products/choc-chip-crunch-temp.png',
})

const { data: products } = useCatalog()
const featured = computed(() => products.value.slice(0, 6))

const process = [
  ['Browse', 'Choose your cookies'],
  ['Cart', 'Pick your pack size'],
  ['Checkout', 'Add delivery details'],
  ['We bake', 'Fresh for your order'],
  ['Delivery', 'Jasin or pickup'],
]
</script>

<template>
  <div>
    <section class="container-shell">
      <div class="hero-grid">
        <div class="hero-copy">
          <span class="eyebrow">Made with love in Jasin</span>
          <h1 class="hero-title">Little cookies, <em>big happiness.</em></h1>
          <p class="mt-6 max-w-md text-sm leading-7 text-stone-600 sm:text-base">
            Small-batch cookies with crisp edges, generous centres and the kind of comfort worth sharing.
          </p>
          <div class="mt-7 flex flex-wrap gap-3">
            <NuxtLink to="/shop" class="btn-primary">Shop cookies <span aria-hidden="true">→</span></NuxtLink>
            <NuxtLink to="/how-to-order" class="btn-secondary">How to order</NuxtLink>
          </div>
          <p class="mt-6 text-xs font-semibold text-[#78825e]">● Fresh batches · Preorder recommended</p>
        </div>
        <div class="hero-photo-wrap">
          <img class="hero-photo" :src="resolveProductImage('choc-chip-crunch')" alt="A stack of Choc Chip Crunch cookies" width="900" height="900">
          <span class="hero-stamp">Baked<br>in small<br>batches</span>
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
          <span class="eyebrow">Today’s counter</span>
          <h2 class="mt-3">Cookies to fall for</h2>
        </div>
        <NuxtLink to="/shop" class="hidden text-xs font-bold text-[#a85f4c] sm:block">View the full menu →</NuxtLink>
      </div>
      <div class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-6">
        <ProductCard v-for="product in featured" :key="product.slug" :product="product" compact />
      </div>
      <NuxtLink to="/shop" class="btn-secondary mt-6 w-full sm:hidden">View the full menu</NuxtLink>
    </section>

    <section class="container-shell pb-16 sm:pb-20">
      <div class="grid gap-4 lg:grid-cols-[.85fr_1.2fr_1fr]">
        <article class="paper-card p-6 sm:p-8">
          <span class="eyebrow">Our kitchen</span>
          <h2 class="mt-4 font-serif text-3xl leading-tight">Home-baked, never hurried.</h2>
          <p class="mt-4 text-sm leading-7 text-stone-600">
            Lyn’s Little Kitchen is a home-based bakery in Jasin. Every order is mixed, shaped and baked in small batches.
          </p>
          <NuxtLink to="/about" class="btn-primary mt-6">Meet the kitchen</NuxtLink>
        </article>

        <div class="relative min-h-72 overflow-hidden rounded-[1.2rem]">
          <img src="/images/products/dubai-chewy-cookie-temp.png" alt="Dubai Chewy Cookie with pistachio filling" class="absolute inset-0 h-full w-full object-cover" width="800" height="800">
          <div class="absolute inset-x-4 bottom-4 rounded-xl bg-[#fffaf5]/90 p-4 backdrop-blur">
            <p class="font-serif text-xl">The chewy one</p>
            <p class="mt-1 text-xs text-stone-600">Chocolate, pistachio cream and toasted kataifi.</p>
          </div>
        </div>

        <article class="paper-card p-6 sm:p-8">
          <span class="eyebrow">Why you’ll love them</span>
          <ul class="mt-5 grid gap-4 text-sm">
            <li class="flex gap-3"><span class="text-[#a85f4c]">✓</span> Baked only after you order</li>
            <li class="flex gap-3"><span class="text-[#a85f4c]">✓</span> Generous chocolate in every batch</li>
            <li class="flex gap-3"><span class="text-[#a85f4c]">✓</span> Local delivery around Jasin</li>
            <li class="flex gap-3"><span class="text-[#a85f4c]">✓</span> Gift-ready pack sizes</li>
          </ul>
          <div class="mt-8 border-t border-dashed border-[#a85f4c55] pt-5 font-serif text-xl italic text-[#a85f4c]">Made to make days sweeter.</div>
        </article>
      </div>
    </section>

    <section class="border-y border-[#70453418] bg-[#f4e4d9]/65 py-14 sm:py-16">
      <div class="container-shell">
        <div class="text-center">
          <span class="eyebrow">From craving to doorstep</span>
          <h2 class="mt-4 font-serif text-3xl sm:text-4xl">A simple way to order</h2>
        </div>
        <div class="process-line mt-10">
          <div v-for="(step, index) in process" :key="step[0]" class="process-step">
            <span class="process-number">{{ index + 1 }}</span>
            <strong class="text-sm">{{ step[0] }}</strong>
            <span class="text-xs text-stone-500">{{ step[1] }}</span>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
