<script setup lang="ts">
const items = [
  { icon: '♨', title: 'Freshly baked', text: 'Made to order' },
  { icon: '✓', title: 'Quality ingredients', text: 'Prepared with care' },
  { icon: '▣', title: 'Secure payment', text: 'Billplz ready' },
  { icon: '⌂', title: 'Delivery in Jasin', text: 'Fast & reliable' },
]
</script>

<template>
  <div class="trust-strip">
    <div v-for="item in items" :key="item.title" class="trust-item">
      <span class="trust-icon" aria-hidden="true">{{ item.icon }}</span>
      <span>
        <strong class="block text-xs">{{ item.title }}</strong>
        <small class="mt-0.5 block text-[10px] text-stone-500">{{ item.text }}</small>
      </span>
    </div>
  </div>
</template>
