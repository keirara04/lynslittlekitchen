<script setup lang="ts">
import { formatAdminCurrency, formatAdminDate } from '~/utils/admin.mjs'
import type { AdminOrder } from '~/types/admin'

defineProps<{ orders: AdminOrder[] }>()
</script>

<template>
  <div class="admin-table-wrap admin-panel">
    <table class="admin-data-table">
      <thead><tr><th>Order ID</th><th>Customer</th><th>Payment</th><th>Status</th><th>Total</th><th>Date</th><th>Actions</th></tr></thead>
      <tbody>
        <tr v-for="order in orders" :key="order.id">
          <td data-label="Order ID"><strong class="admin-order-reference">{{ order.order_reference }}</strong></td>
          <td data-label="Customer">{{ order.customer?.name || 'Guest' }}</td>
          <td data-label="Payment"><AdminStatusBadge :status="order.payment_status" /></td>
          <td data-label="Status"><AdminStatusBadge :status="order.order_status" /></td>
          <td data-label="Total">{{ formatAdminCurrency(order.total) }}</td>
          <td data-label="Date">{{ formatAdminDate(order.created_at, { day: 'numeric', month: 'short', hour: 'numeric', minute: '2-digit' }) }}</td>
          <td data-label="Actions"><NuxtLink :to="`/admin/orders/${order.id}`" class="admin-table-action">View →</NuxtLink></td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
