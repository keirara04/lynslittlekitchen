<script setup lang="ts">
import type { Product } from '~/types/catalog'
import { formatRinggit, resolveProductImage } from '~/utils/storefront.mjs'

const props = defineProps<{ product: Product, compact?: boolean }>()
const cart = useCartStore()
const added = ref(false)

function quickAdd() {
  cart.addProduct(props.product, props.product.variants?.[0], 1)
  added.value = true
  window.setTimeout(() => { added.value = false }, 1200)
}
</script>

<template>
  <article class="product-card group">
    <NuxtLink :to="`/shop/${product.slug}`" class="block">
      <div class="product-image-frame">
        <img :src="resolveProductImage(product.slug, product.images)" :alt="product.name" width="640" height="640">
        <span v-if="product.slug === 'choc-chip-crunch' || product.slug === 'dubai-chewy-cookie'" class="absolute left-3 top-3 rounded-full bg-[#fffaf5]/90 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-[#854536]">
          Our signature
        </span>
      </div>
    </NuxtLink>
    <div :class="compact ? 'p-3' : 'p-4'">
      <p class="text-[10px] font-bold uppercase tracking-[.12em] text-[#a85f4c]">{{ product.category?.name }}</p>
      <NuxtLink :to="`/shop/${product.slug}`" class="mt-1 block font-serif text-lg leading-tight hover:text-[#a85f4c]">
        {{ product.name }}
      </NuxtLink>
      <div class="mt-3 flex items-center justify-between gap-3">
        <strong class="text-sm">{{ product.variants?.length ? 'From ' : '' }}{{ formatRinggit(product.price) }}</strong>
        <button class="grid h-10 w-10 place-items-center rounded-full border border-[#a85f4c55] text-[#854536] transition hover:bg-[#a85f4c] hover:text-white" :aria-label="`Add ${product.name} to cart`" @click="quickAdd">
          <span v-if="added" aria-hidden="true">✓</span>
          <svg v-else viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 4h2l2 10h10l2-7H6M10 19h.01M17 19h.01"/></svg>
        </button>
      </div>
    </div>
  </article>
</template>
