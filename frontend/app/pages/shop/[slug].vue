<script setup lang="ts">
import { formatRinggit, resolveProductImage } from '~/utils/storefront.mjs'

const route = useRoute()
const cart = useCartStore()
const { data: products } = useCatalog()
const product = computed(() => products.value.find(item => item.slug === route.params.slug))
const selectedVariantId = ref<number | null>(null)
const quantity = ref(1)
const added = ref(false)

const selectedVariant = computed(() => product.value?.variants.find(item => item.id === selectedVariantId.value) || product.value?.variants[0])
const price = computed(() => Number(selectedVariant.value?.price ?? product.value?.price ?? 0))
const related = computed(() => products.value.filter(item => item.slug !== product.value?.slug).slice(0, 3))

watch(product, (value) => {
  selectedVariantId.value = value?.variants?.[0]?.id ?? null
}, { immediate: true })

useSeoMeta({
  title: () => product.value ? `${product.value.name} | Lyn's Little Kitchen` : "Cookie | Lyn's Little Kitchen",
  description: () => product.value?.description || 'Fresh cookies baked in Jasin, Melaka.',
  ogImage: () => product.value ? resolveProductImage(product.value.slug, product.value.images) : undefined,
})

function addToCart() {
  if (!product.value) return
  cart.addProduct(product.value, selectedVariant.value, quantity.value)
  added.value = true
  window.setTimeout(() => { added.value = false }, 1400)
}

async function buyNow() {
  addToCart()
  await navigateTo('/cart')
}
</script>

<template>
  <div class="container-shell py-8 sm:py-12">
    <div class="mb-7 text-xs text-stone-500"><NuxtLink to="/">Home</NuxtLink> / <NuxtLink to="/shop">Shop</NuxtLink> / {{ product?.name || 'Cookie' }}</div>

    <div v-if="product" class="grid gap-8 lg:grid-cols-[1.05fr_.95fr] lg:gap-14">
      <div>
        <div class="overflow-hidden rounded-[1.25rem] border border-[#70453420] bg-[#ead8cc] shadow-[0_20px_60px_rgba(91,53,39,.12)]">
          <img :src="resolveProductImage(product.slug, product.images)" :alt="product.name" class="aspect-square w-full object-cover" width="900" height="900">
        </div>
        <div class="mt-3 flex gap-3">
          <button class="h-16 w-16 overflow-hidden rounded-lg border-2 border-[#a85f4c]" aria-label="View main product photo">
            <img :src="resolveProductImage(product.slug, product.images)" alt="" class="h-full w-full object-cover">
          </button>
        </div>
      </div>

      <section class="self-center">
        <span class="eyebrow">{{ product.category.name }}</span>
        <h1 class="display-title mt-4 text-5xl sm:text-6xl">{{ product.name }}</h1>
        <p class="mt-4 font-serif text-2xl text-[#a85f4c]">{{ formatRinggit(price) }}</p>
        <p class="mt-5 max-w-xl text-sm leading-7 text-stone-600">{{ product.description }}</p>

        <div class="mt-6 grid gap-3 text-sm">
          <p><strong>Ingredients:</strong> <span class="text-stone-600">{{ product.ingredients }}</span></p>
          <p><strong>Allergens:</strong> <span class="text-stone-600">{{ product.allergens }}</span></p>
          <p class="font-semibold" :class="product.in_stock ? 'text-[#78825e]' : 'text-red-700'">● {{ product.in_stock ? 'Available for preorder' : 'Currently unavailable' }}</p>
        </div>

        <fieldset v-if="product.variants.length" class="mt-7">
          <legend class="text-xs font-bold uppercase tracking-wider">Choose pack size</legend>
          <div class="mt-3 flex flex-wrap gap-2">
            <label v-for="variant in product.variants" :key="variant.id" class="cursor-pointer">
              <input v-model="selectedVariantId" class="peer sr-only" type="radio" :value="variant.id">
              <span class="inline-flex min-h-11 items-center rounded-lg border border-[#7045342b] bg-white px-4 text-xs font-semibold peer-checked:border-[#a85f4c] peer-checked:bg-[#f4ded2]">{{ variant.label }}</span>
            </label>
          </div>
        </fieldset>

        <div class="mt-7 flex flex-wrap gap-3">
          <CartQuantityStepper v-model="quantity" />
          <button class="btn-primary min-w-52 flex-1" :disabled="!product.in_stock" @click="addToCart">{{ added ? 'Added to cart ✓' : 'Add to cart' }}</button>
        </div>
        <button class="btn-secondary mt-3 w-full" :disabled="!product.in_stock" @click="buyNow">Buy now</button>
        <div class="mt-8"><SharedTrustStrip /></div>
      </section>
    </div>

    <div v-else class="paper-card py-20 text-center">
      <h1 class="font-serif text-3xl">That cookie isn’t on the counter</h1>
      <NuxtLink to="/shop" class="btn-primary mt-6">Return to the shop</NuxtLink>
    </div>

    <section v-if="related.length" class="mt-20">
      <div class="section-heading"><h2>You may also like</h2></div>
      <div class="grid grid-cols-2 gap-3 md:grid-cols-3">
        <ProductCard v-for="item in related" :key="item.slug" :product="item" />
      </div>
    </section>
  </div>
</template>
