<script setup lang="ts">
defineEmits<{ menu: [] }>()
const auth = useAdminAuthStore()
const initials = computed(() => auth.user?.name?.split(/\s+/).map(part => part[0]).slice(0, 2).join('').toUpperCase() || 'A')
</script>

<template>
  <header class="admin-header">
    <button id="admin-menu-button" class="admin-header__menu" type="button" aria-label="Open menu" @click="$emit('menu')">
      <AdminIcon name="menu" />
    </button>
    <div class="admin-header__spacer" />
    <button class="admin-icon-button" type="button" aria-label="Notifications">
      <AdminIcon name="bell" />
    </button>
    <div class="admin-profile">
      <span class="admin-profile__avatar">{{ initials }}</span>
      <span class="admin-profile__copy">
        <strong>{{ auth.user?.name || 'Admin' }}</strong>
        <small>Super Admin</small>
      </span>
    </div>
  </header>
</template>
