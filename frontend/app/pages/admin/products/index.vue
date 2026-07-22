<script setup lang="ts">
import AdminProductFilters from '~/components/admin/products/AdminProductFilters.vue'
import AdminProductTable from '~/components/admin/products/AdminProductTable.vue'
import { buildAdminQuery } from '~/utils/admin.mjs'
import type { AdminProduct, CategoriesResponse } from '~/types/admin'

definePageMeta({ layout: 'admin', middleware: 'admin-auth' })

const route = useRoute()
const searchDraft = ref(String(route.query.search || ''))
const deleteTarget = ref<AdminProduct | null>(null)
const deleting = ref(false)
const actionError = ref('')
let searchTimer: ReturnType<typeof setTimeout> | undefined

const filters = computed(() => ({
  search: String(route.query.search || ''),
  category: String(route.query.category || ''),
  status: String(route.query.status || ''),
  page: Number(route.query.page || 1),
}))

const apiQuery = computed(() => buildAdminQuery({ ...filters.value, per_page: 20 }))
const { data: response, pending, error, refresh } = await useAdminProducts(apiQuery)
const { data: categoryResponse } = await useAsyncData('admin-product-categories', () => useAdminApi<CategoriesResponse>('categories'))

function updateFilter(key: string, value: string | number) {
  const query = { ...route.query, [key]: value || undefined }
  if (key !== 'page') query.page = undefined
  navigateTo({ path: '/admin/products', query })
}

function queueSearch(value: string) {
  searchDraft.value = value
  if (searchTimer) clearTimeout(searchTimer)
  searchTimer = setTimeout(() => updateFilter('search', value.trim()), 250)
}

async function confirmDelete() {
  if (!deleteTarget.value) return
  deleting.value = true
  actionError.value = ''
  try {
    await useAdminApi(`products/${deleteTarget.value.id}`, { method: 'DELETE' })
    deleteTarget.value = null
    await refresh()
  }
  catch (error: any) {
    actionError.value = error?.data?.message ?? 'Product could not be deleted.'
  }
  finally {
    deleting.value = false
  }
}

useSeoMeta({ title: "Products | Lyn's Admin", robots: 'noindex, nofollow' })
</script>

<template>
  <div class="admin-page">
    <header class="admin-page-heading">
      <div><p class="admin-kicker">Catalogue</p><h1>Products</h1><p>Manage cookies, pricing, variants, and inventory.</p></div>
      <NuxtLink to="/admin/products/new" class="admin-button admin-button--primary">＋ New Product</NuxtLink>
    </header>

    <AdminProductFilters
      :search="searchDraft"
      :category="filters.category"
      :status="filters.status"
      :categories="categoryResponse?.data || []"
      @search="queueSearch"
      @change="updateFilter"
    />

    <p v-if="actionError" class="admin-form-error" role="alert">{{ actionError }}</p>
    <div v-if="pending" class="admin-table-loading admin-panel" aria-label="Loading products" />
    <AdminEmptyState v-else-if="error" title="Products could not be loaded" description="Check the API connection and try again."><button class="admin-button admin-button--primary" type="button" @click="refresh()">Try again</button></AdminEmptyState>
    <AdminEmptyState v-else-if="!response?.data.length" title="No products found" description="Change the filters or add a new cookie to the catalogue."><NuxtLink to="/admin/products/new" class="admin-button admin-button--primary">Add product</NuxtLink></AdminEmptyState>
    <template v-else>
      <AdminProductTable :products="response.data" @delete="deleteTarget = $event" />
      <nav class="admin-pagination" aria-label="Product pages">
        <button type="button" :disabled="response.meta.current_page <= 1" @click="updateFilter('page', response.meta.current_page - 1)">← Previous</button>
        <span>Page {{ response.meta.current_page }} of {{ response.meta.last_page }}</span>
        <button type="button" :disabled="response.meta.current_page >= response.meta.last_page" @click="updateFilter('page', response.meta.current_page + 1)">Next →</button>
      </nav>
    </template>

    <AdminConfirmDialog
      :open="Boolean(deleteTarget)"
      title="Delete product?"
      :description="`${deleteTarget?.name || 'This product'} will be removed from the active catalogue. This uses Laravel soft deletion.`"
      confirm-label="Delete product"
      :busy="deleting"
      @close="deleteTarget = null"
      @confirm="confirmDelete"
    />
  </div>
</template>
