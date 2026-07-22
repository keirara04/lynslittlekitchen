<script setup lang="ts">
const cart = useCartStore()
onMounted(() => cart.hydrate())
</script>

<template>
  <div>
    <SiteAppHeader />
    <main class="page-shell">
      <slot />
    </main>
    <SiteAppFooter />
  </div>
</template>
