<script setup lang="ts">
import { formatAdminCurrency, formatAdminDate, humanizeStatus } from '~/utils/admin.mjs'
import type { AdminOrder } from '~/types/admin'

const props = defineProps<{ order: AdminOrder }>()
const subtotal = computed(() => props.order.items.reduce((sum, item) => sum + Number(item.subtotal), 0))
</script>

<template>
  <article class="admin-invoice">
    <header class="admin-invoice__header">
      <div><AdminBrand /><p>Homemade cookies, baked with love in Jasin, Melaka.</p></div>
      <div><h1>INVOICE</h1><dl><div><dt>Order ID</dt><dd>{{ order.order_reference }}</dd></div><div><dt>Date</dt><dd><time :datetime="order.created_at">{{ formatAdminDate(order.created_at) }}</time></dd></div><div><dt>Payment</dt><dd>{{ humanizeStatus(order.payment_status) }}</dd></div></dl></div>
    </header>

    <section class="admin-invoice__addresses">
      <div><h2>Customer</h2><address><strong>{{ order.customer.name || 'Guest customer' }}</strong><br>{{ order.customer.phone || '' }}<br>{{ order.customer.email || '' }}</address></div>
      <div><h2>{{ order.delivery_method === 'pickup' ? 'Pickup' : 'Delivery' }}</h2><address>{{ order.delivery_address || 'Lyn’s Little Kitchen' }}<br>{{ order.delivery_zone?.name || 'Jasin, Melaka' }}<br><span v-if="order.delivery_date">Delivery date: {{ formatAdminDate(order.delivery_date) }}</span></address></div>
    </section>

    <table class="admin-invoice__items">
      <thead><tr><th>Item</th><th>Variant</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
      <tbody><tr v-for="item in order.items" :key="item.id"><td>{{ item.product?.name || 'Product' }}</td><td>{{ item.variant_label || 'Base product' }}</td><td>{{ item.quantity }}</td><td>{{ formatAdminCurrency(item.price) }}</td><td>{{ formatAdminCurrency(item.subtotal) }}</td></tr></tbody>
      <tfoot><tr><th colspan="4">Subtotal</th><td>{{ formatAdminCurrency(subtotal) }}</td></tr><tr><th colspan="4">Delivery fee</th><td>{{ formatAdminCurrency(order.delivery_fee) }}</td></tr><tr class="admin-invoice__total"><th colspan="4">Total</th><td>{{ formatAdminCurrency(order.total) }}</td></tr></tfoot>
    </table>

    <footer><p>Thank you for supporting Lyn’s Little Kitchen.</p><small>Order {{ order.order_reference }}</small></footer>
  </article>
</template>
