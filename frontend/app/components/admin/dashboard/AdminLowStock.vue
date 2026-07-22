<script setup lang="ts">
import type { DashboardLowStockAlert } from '~/types/admin'

defineProps<{ alerts: DashboardLowStockAlert[] }>()
</script>

<template>
  <section class="admin-dashboard-panel admin-panel">
    <header><h2>Low Stock Alerts</h2><span>{{ alerts.length }} items</span></header>
    <div v-if="alerts.length" class="admin-stock-list">
      <article v-for="item in alerts.slice(0, 5)" :key="`${item.product}-${item.variant_label}`">
        <img src="/images/products/cookie-placeholder.svg" alt="">
        <div><strong>{{ item.product }}</strong><small>{{ item.variant_label || 'Base product' }}</small></div>
        <span>{{ item.stock }} left</span>
      </article>
    </div>
    <p v-else class="admin-panel-empty">All products have healthy stock levels.</p>
    <NuxtLink v-if="alerts.length" to="/admin/products" class="admin-panel-link">View all products →</NuxtLink>
  </section>
</template>
