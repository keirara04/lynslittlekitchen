<script setup lang="ts">
import AdminCustomerFilters from '~/components/admin/customers/AdminCustomerFilters.vue'
import AdminCustomerTable from '~/components/admin/customers/AdminCustomerTable.vue'
import { buildAdminQuery } from '~/utils/admin.mjs'

definePageMeta({ layout: 'admin', middleware: 'admin-auth' })

const route = useRoute()
const searchDraft = ref(String(route.query.search || ''))
let searchTimer: ReturnType<typeof setTimeout> | undefined
const filters = computed(() => ({
  search: String(route.query.search || ''),
  status: String(route.query.status || ''),
  page: Number(route.query.page || 1),
}))
const apiQuery = computed(() => buildAdminQuery({ ...filters.value, per_page: 20 }))
const { data: response, pending, error, refresh } = await useAdminCustomers(apiQuery)

function updateFilter(key: string, value: string | number) {
  const query = { ...route.query, [key]: value || undefined }
  if (key !== 'page') query.page = undefined
  navigateTo({ path: '/admin/customers', query })
}
function queueSearch(value: string) {
  searchDraft.value = value
  if (searchTimer) clearTimeout(searchTimer)
  searchTimer = setTimeout(() => updateFilter('search', value.trim()), 250)
}

useSeoMeta({ title: "Customers | Lyn's Admin", robots: 'noindex, nofollow' })
</script>

<template>
  <div class="admin-page">
    <header class="admin-page-heading"><div><p class="admin-kicker">Customers</p><h1>Customers</h1><p>Manage your customers and their order history.</p></div></header>
    <AdminCustomerFilters :search="searchDraft" :status="filters.status" @search="queueSearch" @change="updateFilter" />
    <div v-if="pending" class="admin-table-loading admin-panel" aria-label="Loading customers" />
    <AdminEmptyState v-else-if="error" title="Customers could not be loaded" description="Check the API connection and try again."><button class="admin-button admin-button--primary" type="button" @click="refresh()">Try again</button></AdminEmptyState>
    <AdminEmptyState v-else-if="!response?.data.length" title="No customers found" description="Customers will appear here once orders come in." />
    <template v-else>
      <AdminCustomerTable :customers="response.data" />
      <nav class="admin-pagination" aria-label="Customer pages"><button type="button" :disabled="response.meta.current_page <= 1" @click="updateFilter('page', response.meta.current_page - 1)">← Previous</button><span>Page {{ response.meta.current_page }} of {{ response.meta.last_page }}</span><button type="button" :disabled="response.meta.current_page >= response.meta.last_page" @click="updateFilter('page', response.meta.current_page + 1)">Next →</button></nav>
    </template>
  </div>
</template>
