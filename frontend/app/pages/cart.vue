<script setup lang="ts">
import { formatRinggit } from '~/utils/storefront.mjs'

useSeoMeta({ title: "Your cart | Lyn's Little Kitchen" })
const cart = useCartStore()

function goToCheckout() {
  navigateTo('/checkout')
}
</script>

<template>
  <div class="container-shell py-10 sm:py-14">
    <header>
      <span class="eyebrow">Your selection</span>
      <h1 class="display-title mt-4 text-5xl">Your cart <span class="font-sans text-base tracking-normal text-stone-500">({{ cart.totals.itemCount }} items)</span></h1>
    </header>

    <div v-if="cart.lines.length" class="mt-9 grid gap-6 lg:grid-cols-[1fr_340px]">
      <section class="paper-card overflow-hidden">
        <article v-for="line in cart.lines" :key="line.key" class="grid grid-cols-[76px_1fr] gap-4 border-b border-[#70453418] p-4 last:border-0 sm:grid-cols-[92px_1fr_auto] sm:items-center sm:p-5">
          <NuxtLink :to="`/shop/${line.slug}`" class="overflow-hidden rounded-xl bg-[#ead8cc]">
            <img :src="line.image" :alt="line.name" class="aspect-square h-full w-full object-cover" width="180" height="180">
          </NuxtLink>
          <div>
            <NuxtLink :to="`/shop/${line.slug}`" class="font-serif text-lg hover:text-[#a85f4c]">{{ line.name }}</NuxtLink>
            <p class="mt-1 text-xs text-stone-500">{{ line.variantLabel }}</p>
            <p class="mt-2 text-sm font-bold">{{ formatRinggit(line.unitPrice) }}</p>
            <div class="mt-3 sm:hidden">
              <CartQuantityStepper :model-value="line.quantity" :min="0" @update:model-value="cart.updateQuantity(line.key, $event)" />
            </div>
          </div>
          <div class="col-span-2 flex items-center justify-between sm:col-span-1 sm:grid sm:justify-items-end sm:gap-3">
            <div class="hidden sm:block"><CartQuantityStepper :model-value="line.quantity" :min="0" @update:model-value="cart.updateQuantity(line.key, $event)" /></div>
            <strong>{{ formatRinggit(line.unitPrice * line.quantity) }}</strong>
            <button class="min-h-11 text-xs font-bold text-[#a85f4c] underline-offset-4 hover:underline" @click="cart.removeLine(line.key)">Remove</button>
          </div>
        </article>
        <div class="p-5">
          <label class="form-label">Add a note for the kitchen
            <textarea class="form-textarea" :value="cart.note" placeholder="Example: Please deliver after 4pm" @input="cart.setNote(($event.target as HTMLTextAreaElement).value)" />
          </label>
        </div>
      </section>
      <CartOrderSummary action-label="Proceed to checkout" @action="goToCheckout" />
    </div>

    <div v-else class="paper-card mt-9 px-6 py-20 text-center">
      <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-[#f1ddd1] text-3xl">🍪</div>
      <h2 class="mt-5 font-serif text-3xl">Your cookie box is empty</h2>
      <p class="mt-2 text-sm text-stone-500">Pick a favourite and we’ll start the batch.</p>
      <NuxtLink to="/shop" class="btn-primary mt-6">Browse cookies</NuxtLink>
    </div>

    <div class="mt-8"><SharedTrustStrip /></div>
  </div>
</template>
