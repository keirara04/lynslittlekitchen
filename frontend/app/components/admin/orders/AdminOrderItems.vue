<script setup lang="ts">
import { formatAdminCurrency } from '~/utils/admin.mjs'
import type { AdminOrder } from '~/types/admin'

const props = defineProps<{ order: AdminOrder }>()
const subtotal = computed(() => props.order.items.reduce((sum, item) => sum + Number(item.subtotal), 0))
</script>

<template>
  <section class="admin-order-items admin-panel">
    <header><h2>Order Items</h2><span>{{ order.items.length }} lines</span></header>
    <div class="admin-order-items__table">
      <table>
        <thead><tr><th>Item</th><th>Variant</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
        <tbody><tr v-for="item in order.items" :key="item.id"><td>{{ item.product?.name || 'Removed product' }}</td><td>{{ item.variant_label || 'Base product' }}</td><td>{{ item.quantity }}</td><td>{{ formatAdminCurrency(item.price) }}</td><td>{{ formatAdminCurrency(item.subtotal) }}</td></tr></tbody>
      </table>
    </div>
    <dl class="admin-order-totals"><div><dt>Subtotal</dt><dd>{{ formatAdminCurrency(subtotal) }}</dd></div><div><dt>Delivery fee</dt><dd>{{ formatAdminCurrency(order.delivery_fee) }}</dd></div><div><dt>Total</dt><dd>{{ formatAdminCurrency(order.total) }}</dd></div></dl>
  </section>
</template>
