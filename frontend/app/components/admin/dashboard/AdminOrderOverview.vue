<script setup lang="ts">
import { humanizeStatus } from '~/utils/admin.mjs'
import type { OrderStatus } from '~/types/admin'

defineProps<{ counts: Record<OrderStatus, number> }>()

const statuses: OrderStatus[] = ['pending', 'preparing', 'baking', 'packing', 'out_for_delivery', 'completed']
</script>

<template>
  <section class="admin-dashboard-panel admin-panel">
    <header><h2>Orders Overview</h2><span>This month</span></header>
    <dl class="admin-order-counts">
      <div v-for="status in statuses" :key="status">
        <dt>{{ humanizeStatus(status) }}</dt>
        <dd>{{ counts[status] || 0 }}</dd>
      </div>
    </dl>
  </section>
</template>
