<script setup lang="ts">
import { formatAdminCurrency, formatAdminDate } from '~/utils/admin.mjs'
import type { AdminCustomer } from '~/types/admin'

defineProps<{ customers: AdminCustomer[] }>()
</script>

<template>
  <div class="admin-table-wrap admin-panel">
    <table class="admin-data-table">
      <thead><tr><th>Customer</th><th>Contact</th><th>Orders</th><th>Total Spent</th><th>Last Order</th><th>Status</th></tr></thead>
      <tbody>
        <tr v-for="customer in customers" :key="customer.id">
          <td data-label="Customer">{{ customer.name || 'Guest' }}</td>
          <td data-label="Contact">{{ customer.email }}<br>{{ customer.phone }}</td>
          <td data-label="Orders">{{ customer.orders_count }}</td>
          <td data-label="Total Spent">{{ formatAdminCurrency(customer.total_spent) }}</td>
          <td data-label="Last Order">{{ formatAdminDate(customer.last_order_at) }}</td>
          <td data-label="Status"><AdminStatusBadge :status="customer.status" /></td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
