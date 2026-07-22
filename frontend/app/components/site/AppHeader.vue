<script setup lang="ts">
const menuOpen = ref(false)
const cart = useCartStore()
const route = useRoute()

watch(() => route.fullPath, () => { menuOpen.value = false })

const links = [
  { label: 'Home', to: '/' },
  { label: 'Shop', to: '/shop' },
  { label: 'About us', to: '/about' },
  { label: 'How to order', to: '/how-to-order' },
  { label: 'Contact', to: '/contact' },
]
</script>

<template>
  <header class="site-header">
    <div class="container-shell flex h-[74px] items-center justify-between gap-5">
      <NuxtLink to="/" class="brand-mark" aria-label="Lyn's Little Kitchen home">
        LYN’S
        <span>Little Kitchen</span>
      </NuxtLink>

      <nav class="hidden items-center gap-7 md:flex" aria-label="Main navigation">
        <NuxtLink v-for="link in links" :key="link.to" :to="link.to" class="nav-link">
          {{ link.label }}
        </NuxtLink>
      </nav>

      <div class="flex items-center gap-2">
        <NuxtLink to="/track-order" class="hidden min-h-11 items-center px-2 text-xs font-semibold text-stone-600 sm:flex">
          Track order
        </NuxtLink>
        <NuxtLink to="/cart" class="relative grid h-11 w-11 place-items-center rounded-full hover:bg-white" aria-label="Open cart">
          <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
            <path d="M3 4h2l2.2 10.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.6L20.2 8H6.1"/>
            <circle cx="10" cy="20" r="1"/><circle cx="17" cy="20" r="1"/>
          </svg>
          <span v-if="cart.totals.itemCount" class="absolute right-0 top-0 grid h-5 min-w-5 place-items-center rounded-full bg-[#a85f4c] px-1 text-[10px] font-bold text-white">
            {{ cart.totals.itemCount }}
          </span>
        </NuxtLink>
        <button class="grid h-11 w-11 place-items-center rounded-full hover:bg-white md:hidden" :aria-expanded="menuOpen" aria-controls="mobile-navigation" aria-label="Toggle menu" @click="menuOpen = !menuOpen">
          <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path v-if="!menuOpen" d="M4 7h16M4 12h16M4 17h16"/>
            <path v-else d="m6 6 12 12M18 6 6 18"/>
          </svg>
        </button>
      </div>
    </div>

    <nav v-if="menuOpen" id="mobile-navigation" class="border-t border-[#7045341a] bg-[#fffaf5] px-4 py-3 md:hidden" aria-label="Mobile navigation">
      <div class="container-shell grid">
        <NuxtLink v-for="link in links" :key="link.to" :to="link.to" class="border-b border-[#70453412] py-3 text-sm font-semibold">
          {{ link.label }}
        </NuxtLink>
        <NuxtLink to="/track-order" class="py-3 text-sm font-semibold text-[#a85f4c]">Track your order</NuxtLink>
      </div>
    </nav>
  </header>
</template>
