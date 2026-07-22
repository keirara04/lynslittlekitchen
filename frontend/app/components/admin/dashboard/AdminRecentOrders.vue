<script setup lang="ts">
import { formatAdminCurrency, formatAdminDate } from '~/utils/admin.mjs'
import type { DashboardRecentOrder } from '~/types/admin'

defineProps<{ orders: DashboardRecentOrder[] }>()
</script>

<template>
  <section class="admin-dashboard-panel admin-dashboard-panel--recent admin-panel">
    <header><h2>Recent Orders</h2><NuxtLink to="/admin/orders">View all orders →</NuxtLink></header>
    <div v-if="orders.length" class="admin-recent-orders">
      <NuxtLink v-for="order in orders" :key="order.id" :to="`/admin/orders/${order.id}`">
        <div><strong>{{ order.order_reference }}</strong><small>{{ order.customer_name }}</small></div>
        <AdminStatusBadge :status="order.order_status" />
        <div class="admin-recent-orders__amount"><strong>{{ formatAdminCurrency(order.total) }}</strong><small>{{ formatAdminDate(order.created_at, { day: 'numeric', month: 'short' }) }}</small></div>
      </NuxtLink>
    </div>
    <p v-else class="admin-panel-empty">New orders will appear here as they arrive.</p>
  </section>
</template>
