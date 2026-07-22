<script setup lang="ts">
import AdminInvoice from '~/components/admin/orders/AdminInvoice.vue'
import type { AdminOrder } from '~/types/admin'

definePageMeta({ layout: 'admin', middleware: 'admin-auth' })

const route = useRoute()
const id = Number(route.params.id)
if (!Number.isInteger(id) || id <= 0) throw createError({ statusCode: 404, statusMessage: 'Order not found.' })
const { data: response, error } = await useAsyncData(`admin-order-${id}`, () => useAdminApi<{ data: AdminOrder }>(`orders/${id}`))

function printInvoice() {
  window.print()
}

useSeoMeta({ title: () => `Invoice ${response.value?.data.order_reference || ''} | Lyn's Admin`, robots: 'noindex, nofollow' })
</script>

<template>
  <div class="admin-print-page">
    <header class="admin-page-heading admin-print-hidden">
      <div><p class="admin-kicker">Print preview</p><h1>Invoice</h1><p>Review the order, then use your browser to print or save as PDF.</p></div>
      <div class="admin-print-actions"><NuxtLink :to="`/admin/orders/${id}`" class="admin-button admin-button--secondary">Back to order</NuxtLink><button class="admin-button admin-button--primary" type="button" @click="printInvoice">Print Invoice</button></div>
    </header>
    <AdminEmptyState v-if="error" title="Invoice could not be loaded" description="Return to the order and try again." />
    <AdminInvoice v-else-if="response?.data" :order="response.data" />
  </div>
</template>
