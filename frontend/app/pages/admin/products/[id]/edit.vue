<script setup lang="ts">
import AdminProductForm from '~/components/admin/products/AdminProductForm.vue'
import type { AdminProduct, CategoriesResponse, StoreProductPayload } from '~/types/admin'

definePageMeta({ layout: 'admin', middleware: 'admin-auth' })

const route = useRoute()
const id = Number(route.params.id)
if (!Number.isInteger(id) || id <= 0) throw createError({ statusCode: 404, statusMessage: 'Product not found.' })

const busy = ref(false)
const dirty = ref(false)
const serverErrors = ref<Record<string, string[]>>({})
const formError = ref('')
const { data: response, error } = await useAsyncData(`admin-product-${id}`, () => useAdminApi<{ data: AdminProduct }>(`products/${id}`))
const { data: categories } = await useAsyncData('admin-product-categories', () => useAdminApi<CategoriesResponse>('categories'))

async function save(payload: StoreProductPayload) {
  busy.value = true
  serverErrors.value = {}
  formError.value = ''
  try {
    await useAdminApi(`products/${id}`, { method: 'PUT', body: payload })
    dirty.value = false
    await navigateTo('/admin/products')
  }
  catch (requestError: any) {
    const data = requestError?.data?.data ?? requestError?.data
    serverErrors.value = data?.errors ?? {}
    formError.value = data?.message ?? 'Product changes could not be saved.'
  }
  finally { busy.value = false }
}

onBeforeRouteLeave(() => !dirty.value || !import.meta.client || window.confirm('Leave without saving your product changes?'))
useSeoMeta({ title: () => `${response.value?.data.name || 'Edit product'} | Lyn's Admin`, robots: 'noindex, nofollow' })
</script>

<template>
  <div class="admin-page">
    <header class="admin-page-heading"><div><p class="admin-kicker">Products / Edit</p><h1>{{ response?.data.name || 'Edit Product' }}</h1><p>Update catalogue details, inventory, variants, and images.</p></div></header>
    <AdminEmptyState v-if="error" title="Product could not be loaded" description="It may have been removed or the API is unavailable." />
    <template v-else-if="response?.data">
      <p v-if="formError" class="admin-form-error" role="alert">{{ formError }}</p>
      <AdminProductForm :initial-product="response.data" :categories="categories?.data || []" :busy="busy" :server-errors="serverErrors" @dirty="dirty = $event" @save="save" />
    </template>
  </div>
</template>
