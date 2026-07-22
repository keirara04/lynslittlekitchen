<script setup lang="ts">
defineProps<{ search: string; paymentStatus: string; orderStatus: string; deliveryMethod: string }>()
defineEmits<{ search: [value: string]; change: [key: string, value: string] }>()
</script>

<template>
  <div class="admin-filters admin-order-filters admin-panel">
    <label class="admin-search-field"><span class="sr-only">Search orders</span><input :value="search" type="search" placeholder="Search orders…" @input="$emit('search', ($event.target as HTMLInputElement).value)"></label>
    <label><span class="sr-only">Payment status</span><select :value="paymentStatus" @change="$emit('change', 'payment_status', ($event.target as HTMLSelectElement).value)"><option value="">All Payment Status</option><option value="unpaid">Unpaid</option><option value="paid">Paid</option><option value="refunded">Refunded</option></select></label>
    <label><span class="sr-only">Order status</span><select :value="orderStatus" @change="$emit('change', 'order_status', ($event.target as HTMLSelectElement).value)"><option value="">All Order Status</option><option v-for="status in ['pending','preparing','baking','packing','out_for_delivery','completed','rejected','cancelled']" :key="status" :value="status">{{ status.replaceAll('_', ' ') }}</option></select></label>
    <label><span class="sr-only">Delivery method</span><select :value="deliveryMethod" @change="$emit('change', 'delivery_method', ($event.target as HTMLSelectElement).value)"><option value="">All Delivery Methods</option><option value="delivery">Delivery</option><option value="pickup">Pickup</option></select></label>
  </div>
</template>
