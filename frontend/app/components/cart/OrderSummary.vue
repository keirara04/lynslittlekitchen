<script setup lang="ts">
import { formatRinggit } from '~/utils/storefront.mjs'

defineProps<{ actionLabel?: string, disabled?: boolean }>()
defineEmits<{ action: [] }>()
const cart = useCartStore()
</script>

<template>
  <aside class="paper-card p-5 sm:p-6">
    <h2 class="font-serif text-xl">Order summary</h2>
    <div class="mt-5 grid gap-3 text-sm">
      <div class="flex justify-between"><span class="text-stone-500">Subtotal</span><strong>{{ formatRinggit(cart.totals.subtotal) }}</strong></div>
      <div class="flex justify-between"><span class="text-stone-500">Delivery fee</span><strong>{{ cart.totals.deliveryFee ? formatRinggit(cart.totals.deliveryFee) : 'Calculated later' }}</strong></div>
      <div class="my-1 border-t border-[#70453422]" />
      <div class="flex justify-between text-base"><strong>Total</strong><strong>{{ formatRinggit(cart.totals.total) }}</strong></div>
    </div>
    <button v-if="actionLabel" class="btn-primary mt-6 w-full" type="button" :disabled="disabled" @click="$emit('action')">
      {{ actionLabel }}
    </button>
  </aside>
</template>
