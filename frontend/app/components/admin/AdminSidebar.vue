<script setup lang="ts">
defineProps<{ open: boolean }>()
const emit = defineEmits<{ close: [] }>()
const auth = useAdminAuthStore()

const navigation = [
  { label: 'Dashboard', to: '/admin', icon: 'dashboard' },
  { label: 'Products', to: '/admin/products', icon: 'products' },
  { label: 'Orders', to: '/admin/orders', icon: 'orders' },
  { label: 'Customers', to: '/admin/customers', icon: 'customers' },
  { label: 'Delivery Zones', to: '/admin/delivery-zones', icon: 'delivery-zones' },
  { label: 'Promotions', to: '/admin/promotions', icon: 'promotions' },
  { label: 'Reports', to: '/admin/reports', icon: 'reports' },
  { label: 'Settings', to: '/admin/settings', icon: 'settings' },
]
</script>

<template>
  <aside class="admin-sidebar" :class="{ 'admin-sidebar--open': open }" aria-label="Admin navigation">
    <div class="admin-sidebar__brand-row">
      <AdminBrand />
      <button class="admin-sidebar__close" type="button" aria-label="Close menu" @click="emit('close')">×</button>
    </div>

    <nav class="admin-sidebar__nav">
      <NuxtLink
        v-for="item in navigation"
        :key="item.to"
        :to="item.to"
        class="admin-nav-link"
        exact-active-class="admin-nav-link--active"
        @click="emit('close')"
      >
        <AdminIcon :name="item.icon" />
        <span>{{ item.label }}</span>
      </NuxtLink>
    </nav>

    <button class="admin-nav-link admin-sidebar__logout" type="button" @click="auth.logout()">
      <AdminIcon name="logout" />
      <span>Logout</span>
    </button>
  </aside>
</template>
