<script setup lang="ts">
import AdminProductForm from '~/components/admin/products/AdminProductForm.vue'
import type { CategoriesResponse, StoreProductPayload } from '~/types/admin'

definePageMeta({ layout: 'admin', middleware: 'admin-auth' })

const busy = ref(false)
const dirty = ref(false)
const serverErrors = ref<Record<string, string[]>>({})
const formError = ref('')
const { data: categories } = await useAsyncData('admin-product-categories', () => useAdminApi<CategoriesResponse>('categories'))

async function save(payload: StoreProductPayload) {
  busy.value = true
  serverErrors.value = {}
  formError.value = ''
  try {
    await useAdminApi('products', { method: 'POST', body: payload })
    dirty.value = false
    await navigateTo('/admin/products')
  }
  catch (error: any) {
    const data = error?.data?.data ?? error?.data
    serverErrors.value = data?.errors ?? {}
    formError.value = data?.message ?? 'Product could not be saved.'
  }
  finally { busy.value = false }
}

onBeforeRouteLeave(() => !dirty.value || !import.meta.client || window.confirm('Leave without saving this product?'))
useSeoMeta({ title: "New product | Lyn's Admin", robots: 'noindex, nofollow' })
</script>

<template>
  <div class="admin-page">
    <header class="admin-page-heading"><div><p class="admin-kicker">Products / New</p><h1>New Product</h1><p>Add a cookie, its pack sizes, inventory, and image URLs.</p></div></header>
    <p v-if="formError" class="admin-form-error" role="alert">{{ formError }}</p>
    <AdminProductForm :categories="categories?.data || []" :busy="busy" :server-errors="serverErrors" @dirty="dirty = $event" @save="save" />
  </div>
</template>
