<script setup lang="ts">
useSeoMeta({ title: "About us | Lyn's Little Kitchen", description: "Meet the home kitchen behind Lyn's small-batch cookies in Jasin, Melaka." })
</script>

<template>
  <div class="container-shell py-12 sm:py-16">
    <div class="grid items-center gap-8 lg:grid-cols-2 lg:gap-14">
      <div>
        <span class="eyebrow">A little kitchen in Jasin</span>
        <h1 class="display-title mt-4 text-5xl sm:text-7xl">Every batch starts at home.</h1>
        <p class="mt-6 text-sm leading-7 text-stone-600 sm:text-base">Lyn’s Little Kitchen is a home-based cookie bakery built around small batches, generous flavours and the joy of sharing something freshly made.</p>
        <p class="mt-4 text-sm leading-7 text-stone-600 sm:text-base">We prepare each order with care and bake close to delivery time, so your cookies arrive the way they should: fragrant, comforting and ready to share.</p>
        <NuxtLink to="/shop" class="btn-primary mt-7">See what’s baking</NuxtLink>
      </div>
      <div class="relative">
        <img src="/images/products/dubai-chewy-cookie-temp.png" alt="Freshly baked Dubai Chewy Cookies" class="aspect-square w-full rounded-[45%_45%_1.5rem_1.5rem] object-cover shadow-[0_25px_70px_rgba(91,53,39,.18)]" width="900" height="900">
        <div class="absolute -bottom-4 left-4 rounded-xl bg-[#fffaf5] px-5 py-4 shadow-xl sm:left-[-1rem]"><p class="font-serif text-xl">Baked with patience</p><p class="mt-1 text-xs text-stone-500">Small batches · Jasin, Melaka</p></div>
      </div>
    </div>
    <div class="mt-16"><SharedTrustStrip /></div>
  </div>
</template>
