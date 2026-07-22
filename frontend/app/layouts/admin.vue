<script setup lang="ts">
const mobileNavOpen = ref(false)
const route = useRoute()

function closeNavigation(restoreFocus = false) {
  mobileNavOpen.value = false
  if (restoreFocus && import.meta.client) {
    nextTick(() => document.getElementById('admin-menu-button')?.focus())
  }
}

watch(() => route.fullPath, () => closeNavigation())
</script>

<template>
  <div class="admin-app admin-surface">
    <AdminSidebar :open="mobileNavOpen" @close="closeNavigation(true)" />
    <button
      v-if="mobileNavOpen"
      class="admin-drawer-scrim"
      type="button"
      aria-label="Close navigation"
      @click="closeNavigation(true)"
    />
    <div class="admin-workspace">
      <AdminHeader @menu="mobileNavOpen = true" />
      <main class="admin-main">
        <slot />
      </main>
    </div>
  </div>
</template>
