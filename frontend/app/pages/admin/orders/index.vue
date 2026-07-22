<script setup lang="ts">
import AdminOrderFilters from '~/components/admin/orders/AdminOrderFilters.vue'
import AdminOrderTable from '~/components/admin/orders/AdminOrderTable.vue'
import { buildAdminQuery } from '~/utils/admin.mjs'

definePageMeta({ layout: 'admin', middleware: 'admin-auth' })

const route = useRoute()
const searchDraft = ref(String(route.query.search || ''))
let searchTimer: ReturnType<typeof setTimeout> | undefined
const filters = computed(() => ({
  search: String(route.query.search || ''),
  payment_status: String(route.query.payment_status || ''),
  order_status: String(route.query.order_status || ''),
  delivery_method: String(route.query.delivery_method || ''),
  page: Number(route.query.page || 1),
}))
const apiQuery = computed(() => buildAdminQuery({ ...filters.value, per_page: 20 }))
const { data: response, pending, error, refresh } = await useAdminOrders(apiQuery)

function updateFilter(key: string, value: string | number) {
  const query = { ...route.query, [key]: value || undefined }
  if (key !== 'page') query.page = undefined
  navigateTo({ path: '/admin/orders', query })
}
function queueSearch(value: string) {
  searchDraft.value = value
  if (searchTimer) clearTimeout(searchTimer)
  searchTimer = setTimeout(() => updateFilter('search', value.trim()), 250)
}

useSeoMeta({ title: "Orders | Lyn's Admin", robots: 'noindex, nofollow' })
</script>

<template>
  <div class="admin-page">
    <header class="admin-page-heading"><div><p class="admin-kicker">Fulfilment</p><h1>Orders</h1><p>Review payments, deliveries, and kitchen progress.</p></div></header>
    <AdminOrderFilters :search="searchDraft" :payment-status="filters.payment_status" :order-status="filters.order_status" :delivery-method="filters.delivery_method" @search="queueSearch" @change="updateFilter" />
    <div v-if="pending" class="admin-table-loading admin-panel" aria-label="Loading orders" />
    <AdminEmptyState v-else-if="error" title="Orders could not be loaded" description="Check the API connection and try again."><button class="admin-button admin-button--primary" type="button" @click="refresh()">Try again</button></AdminEmptyState>
    <AdminEmptyState v-else-if="!response?.data.length" title="No orders found" description="New orders or matching filter results will appear here." />
    <template v-else>
      <AdminOrderTable :orders="response.data" />
      <nav class="admin-pagination" aria-label="Order pages"><button type="button" :disabled="response.meta.current_page <= 1" @click="updateFilter('page', response.meta.current_page - 1)">← Previous</button><span>Page {{ response.meta.current_page }} of {{ response.meta.last_page }}</span><button type="button" :disabled="response.meta.current_page >= response.meta.last_page" @click="updateFilter('page', response.meta.current_page + 1)">Next →</button></nav>
    </template>
  </div>
</template>
