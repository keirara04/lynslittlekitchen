<script setup lang="ts">
import AdminOrderItems from '~/components/admin/orders/AdminOrderItems.vue'
import AdminOrderStatusForm from '~/components/admin/orders/AdminOrderStatusForm.vue'
import AdminOrderTimeline from '~/components/admin/orders/AdminOrderTimeline.vue'
import { formatAdminDate, humanizeStatus } from '~/utils/admin.mjs'
import type { AdminOrder, OrderStatus } from '~/types/admin'

definePageMeta({ layout: 'admin', middleware: 'admin-auth' })

const route = useRoute()
const id = Number(route.params.id)
if (!Number.isInteger(id) || id <= 0) throw createError({ statusCode: 404, statusMessage: 'Order not found.' })

const { data: response, error } = await useAsyncData(`admin-order-${id}`, () => useAdminApi<{ data: AdminOrder }>(`orders/${id}`))
const busy = ref(false)
const updateError = ref('')
const pendingTerminal = ref<OrderStatus | null>(null)

async function updateStatus(status: OrderStatus) {
  if (['rejected', 'cancelled'].includes(status) && pendingTerminal.value !== status) {
    pendingTerminal.value = status
    return
  }
  busy.value = true
  updateError.value = ''
  try {
    const updated = await useAdminApi<{ data: AdminOrder }>(`orders/${id}/status`, { method: 'PATCH', body: { order_status: status } })
    response.value = updated
    pendingTerminal.value = null
    await refreshNuxtData('admin-dashboard')
  }
  catch (requestError: any) {
    const data = requestError?.data?.data ?? requestError?.data
    updateError.value = data?.errors?.order_status?.[0] ?? data?.message ?? 'Order status could not be updated.'
  }
  finally { busy.value = false }
}

useSeoMeta({ title: () => `${response.value?.data.order_reference || 'Order'} | Lyn's Admin`, robots: 'noindex, nofollow' })
</script>

<template>
  <div class="admin-page">
    <AdminEmptyState v-if="error" title="Order could not be loaded" description="It may not exist or the API is unavailable." />
    <template v-else-if="response?.data">
      <header class="admin-page-heading admin-order-heading">
        <div><p class="admin-kicker">Orders / {{ response.data.order_reference }}</p><h1>Order {{ response.data.order_reference }}</h1><p>Placed {{ formatAdminDate(response.data.created_at, { day: 'numeric', month: 'long', year: 'numeric', hour: 'numeric', minute: '2-digit' }) }}</p></div>
        <NuxtLink :to="`/admin/orders/${id}/print`" class="admin-button admin-button--secondary">Print Invoice</NuxtLink>
      </header>
      <AdminOrderTimeline :status="response.data.order_status" />
      <section class="admin-order-detail-grid">
        <div class="admin-order-detail-stack">
          <section class="admin-detail-card admin-panel"><h2>Customer Information</h2><dl><div><dt>Name</dt><dd>{{ response.data.customer.name || 'Guest' }}</dd></div><div><dt>Phone</dt><dd>{{ response.data.customer.phone || '—' }}</dd></div><div><dt>Email</dt><dd>{{ response.data.customer.email || '—' }}</dd></div></dl></section>
          <section class="admin-detail-card admin-panel"><h2>Delivery Information</h2><dl><div><dt>Method</dt><dd>{{ humanizeStatus(response.data.delivery_method) }}</dd></div><div><dt>Address</dt><dd>{{ response.data.delivery_address || 'Pickup at Lyn’s Little Kitchen' }}</dd></div><div><dt>Zone</dt><dd>{{ response.data.delivery_zone?.name || '—' }}</dd></div><div><dt>Delivery date</dt><dd>{{ formatAdminDate(response.data.delivery_date) }}</dd></div><div><dt>Notes</dt><dd>{{ response.data.notes || 'No order notes' }}</dd></div></dl></section>
          <section class="admin-detail-card admin-panel"><h2>Payment Information</h2><dl><div><dt>Status</dt><dd><AdminStatusBadge :status="response.data.payment_status" /></dd></div></dl></section>
        </div>
        <div class="admin-order-detail-stack admin-order-detail-stack--wide">
          <AdminOrderItems :order="response.data" />
          <AdminOrderStatusForm :order="response.data" :busy="busy" :error="updateError" @update="updateStatus" />
        </div>
      </section>
      <AdminConfirmDialog :open="Boolean(pendingTerminal)" :title="`${humanizeStatus(pendingTerminal || '')} this order?`" description="This is a terminal action and the order cannot return to active fulfilment." :confirm-label="humanizeStatus(pendingTerminal || '')" :busy="busy" @close="pendingTerminal = null" @confirm="pendingTerminal && updateStatus(pendingTerminal)" />
    </template>
  </div>
</template>
