<script setup lang="ts">
import { formatAdminCurrency } from '~/utils/admin.mjs'
import type { AdminDeliveryZone } from '~/types/admin'

definePageMeta({ layout: 'admin', middleware: 'admin-auth' })

const { data: response, pending, error, refresh } = await useAdminDeliveryZones()

const editing = ref<AdminDeliveryZone | null>(null)
const form = reactive({ name: '', price: '' as number | string })
const serverErrors = ref<Record<string, string[]>>({})
const formError = ref('')
const busy = ref(false)
const deleteTarget = ref<AdminDeliveryZone | null>(null)
const deleting = ref(false)
const actionError = ref('')

function startCreate() {
  editing.value = null
  form.name = ''
  form.price = ''
  serverErrors.value = {}
  formError.value = ''
}

function startEdit(zone: AdminDeliveryZone) {
  editing.value = zone
  form.name = zone.name
  form.price = zone.price
  serverErrors.value = {}
  formError.value = ''
}

async function save() {
  busy.value = true
  serverErrors.value = {}
  formError.value = ''
  const payload = { name: form.name.trim(), price: Number(form.price) }

  try {
    if (editing.value) {
      await useAdminApi(`delivery-zones/${editing.value.id}`, { method: 'PUT', body: payload })
    }
    else {
      await useAdminApi('delivery-zones', { method: 'POST', body: payload })
    }
    startCreate()
    await refresh()
  }
  catch (err: any) {
    const data = err?.data?.data ?? err?.data
    serverErrors.value = data?.errors ?? {}
    formError.value = data?.message ?? 'Delivery zone could not be saved.'
  }
  finally {
    busy.value = false
  }
}

async function confirmDelete() {
  if (!deleteTarget.value) return
  deleting.value = true
  actionError.value = ''
  try {
    await useAdminApi(`delivery-zones/${deleteTarget.value.id}`, { method: 'DELETE' })
    if (editing.value?.id === deleteTarget.value.id) startCreate()
    deleteTarget.value = null
    await refresh()
  }
  catch (err: any) {
    actionError.value = err?.data?.message ?? 'Delivery zone could not be deleted.'
  }
  finally {
    deleting.value = false
  }
}

useSeoMeta({ title: "Delivery zones | Lyn's Admin", robots: 'noindex, nofollow' })
</script>

<template>
  <div class="admin-page">
    <header class="admin-page-heading">
      <div><p class="admin-kicker">Fulfilment</p><h1>Delivery Zones</h1><p>Configure delivery coverage areas and their fees.</p></div>
    </header>

    <div class="admin-panel">
      <h2>{{ editing ? `Edit ${editing.name}` : 'Add delivery zone' }}</h2>
      <p v-if="formError" class="admin-form-error" role="alert">{{ formError }}</p>
      <form class="admin-form-grid" @submit.prevent="save">
        <label class="admin-field">
          <span>Zone name</span>
          <input v-model.trim="form.name" required placeholder="e.g. Jasin">
          <small v-if="serverErrors.name" class="admin-field__error">{{ serverErrors.name[0] }}</small>
        </label>
        <label class="admin-field">
          <span>Delivery fee (RM)</span>
          <input v-model.number="form.price" type="number" min="0" step="0.01" required>
          <small v-if="serverErrors.price" class="admin-field__error">{{ serverErrors.price[0] }}</small>
        </label>
        <div class="admin-form-actions">
          <button class="admin-button admin-button--primary" type="submit" :disabled="busy">{{ busy ? 'Saving…' : editing ? 'Save changes' : 'Add zone' }}</button>
          <button v-if="editing" class="admin-button admin-button--secondary" type="button" :disabled="busy" @click="startCreate">Cancel</button>
        </div>
      </form>
    </div>

    <p v-if="actionError" class="admin-form-error" role="alert">{{ actionError }}</p>
    <div v-if="pending" class="admin-table-loading admin-panel" aria-label="Loading delivery zones" />
    <AdminEmptyState v-else-if="error" title="Delivery zones could not be loaded" description="Check the API connection and try again."><button class="admin-button admin-button--primary" type="button" @click="refresh()">Try again</button></AdminEmptyState>
    <AdminEmptyState v-else-if="!response?.data.length" title="No delivery zones yet" description="Add a zone above to start charging accurate delivery fees at checkout." />
    <div v-else class="admin-table-wrap admin-panel">
      <table class="admin-data-table">
        <thead>
          <tr><th>Zone</th><th>Fee</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <tr v-for="zone in response.data" :key="zone.id">
            <td data-label="Zone">{{ zone.name }}</td>
            <td data-label="Fee">{{ formatAdminCurrency(zone.price) }}</td>
            <td data-label="Actions">
              <div class="admin-row-actions">
                <button type="button" @click="startEdit(zone)">Edit</button>
                <button type="button" @click="deleteTarget = zone">Delete</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <AdminConfirmDialog
      :open="Boolean(deleteTarget)"
      title="Delete delivery zone?"
      :description="`${deleteTarget?.name || 'This zone'} will be removed. Existing orders keep their history but lose the zone reference.`"
      confirm-label="Delete zone"
      :busy="deleting"
      @close="deleteTarget = null"
      @confirm="confirmDelete"
    />
  </div>
</template>
