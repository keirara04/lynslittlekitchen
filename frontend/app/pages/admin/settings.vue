<script setup lang="ts">
import type { FetchError } from 'ofetch'
import AdminBusinessProfileForm from '~/components/admin/settings/AdminBusinessProfileForm.vue'
import AdminStoreContactForm from '~/components/admin/settings/AdminStoreContactForm.vue'
import AdminOrderSettingsForm from '~/components/admin/settings/AdminOrderSettingsForm.vue'
import AdminPaymentDetailsForm from '~/components/admin/settings/AdminPaymentDetailsForm.vue'
import AdminNotificationsForm from '~/components/admin/settings/AdminNotificationsForm.vue'
import AdminHomepageForm from '~/components/admin/settings/AdminHomepageForm.vue'
import type { AdminSettings, PaginatedResponse, AdminProduct } from '~/types/admin'

definePageMeta({ layout: 'admin', middleware: 'admin-auth' })

const tabs = ['business', 'contact', 'orders', 'payment', 'notifications', 'homepage'] as const
const tabLabels: Record<typeof tabs[number], string> = {
  business: 'Business Profile',
  contact: 'Store Contact',
  orders: 'Order Settings',
  payment: 'Payment Details',
  notifications: 'Notifications',
  homepage: 'Homepage',
}
const activeTab = ref<typeof tabs[number]>('business')

const busy = ref(false)
const serverErrors = ref<Record<string, string[]>>({})
const formError = ref('')
const { data: response, pending, error, refresh } = await useAdminSettings()
const { data: productsResponse } = await useAsyncData('admin-settings-products', () =>
  useAdminApi<PaginatedResponse<AdminProduct>>('products', { query: { per_page: 100 } }))
const products = computed(() => productsResponse.value?.data ?? [])

async function save(payload: Partial<AdminSettings>) {
  busy.value = true
  serverErrors.value = {}
  formError.value = ''
  try {
    const updated = await useAdminApi<{ data: AdminSettings }>('settings', { method: 'PUT', body: payload })
    if (response.value) response.value.data = updated.data
  }
  catch (err) {
    const requestError = err as FetchError<{ data?: { errors?: Record<string, string[]>, message?: string }, errors?: Record<string, string[]>, message?: string }>
    const data = requestError.data?.data ?? requestError.data
    serverErrors.value = data?.errors ?? {}
    formError.value = data?.message ?? 'Settings could not be saved.'
  }
  finally { busy.value = false }
}

useSeoMeta({ title: "Settings | Lyn's Admin", robots: 'noindex, nofollow' })
</script>

<template>
  <div class="admin-page">
    <header class="admin-page-heading"><div><p class="admin-kicker">Settings</p><h1>Settings</h1><p>Manage your store settings and preferences.</p></div></header>
    <div v-if="pending" class="admin-table-loading admin-panel" aria-label="Loading settings" />
    <AdminEmptyState v-else-if="error" title="Settings could not be loaded" description="Check the API connection and try again."><button class="admin-button admin-button--primary" type="button" @click="refresh()">Try again</button></AdminEmptyState>
    <template v-else-if="response?.data">
      <nav class="admin-editor-tabs" aria-label="Settings sections">
        <button v-for="tab in tabs" :key="tab" type="button" :class="{ active: activeTab === tab }" @click="activeTab = tab">{{ tabLabels[tab] }}</button>
      </nav>
      <p v-if="formError" class="admin-form-error" role="alert">{{ formError }}</p>
      <AdminBusinessProfileForm v-show="activeTab === 'business'" :settings="response.data" :busy="busy" :server-errors="serverErrors" @save="save" />
      <AdminStoreContactForm v-show="activeTab === 'contact'" :settings="response.data" :busy="busy" :server-errors="serverErrors" @save="save" />
      <AdminOrderSettingsForm v-show="activeTab === 'orders'" :settings="response.data" :busy="busy" :server-errors="serverErrors" @save="save" />
      <AdminPaymentDetailsForm v-show="activeTab === 'payment'" :settings="response.data" :busy="busy" :server-errors="serverErrors" @save="save" />
      <AdminNotificationsForm v-show="activeTab === 'notifications'" :settings="response.data" :busy="busy" :server-errors="serverErrors" @save="save" />
      <AdminHomepageForm v-show="activeTab === 'homepage'" :settings="response.data" :products="products" :busy="busy" :server-errors="serverErrors" @save="save" />
    </template>
  </div>
</template>
