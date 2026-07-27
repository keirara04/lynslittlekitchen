<script setup lang="ts">
import type { Category } from '~/types/catalog'

const { data: response, pending, error, refresh } = await useAdminCategories()

const editing = ref<Category | null>(null)
const form = reactive({ name: '', slug: '' })
const serverErrors = ref<Record<string, string[]>>({})
const formError = ref('')
const busy = ref(false)
const deleteTarget = ref<Category | null>(null)
const deleting = ref(false)
const actionError = ref('')

function startCreate() {
  editing.value = null
  form.name = ''
  form.slug = ''
  serverErrors.value = {}
  formError.value = ''
}

function startEdit(category: Category) {
  editing.value = category
  form.name = category.name
  form.slug = category.slug
  serverErrors.value = {}
  formError.value = ''
}

async function save() {
  busy.value = true
  serverErrors.value = {}
  formError.value = ''
  const payload = { name: form.name.trim(), slug: form.slug.trim() || null }

  try {
    if (editing.value) {
      await useAdminApi(`categories/${editing.value.id}`, { method: 'PUT', body: payload })
    }
    else {
      await useAdminApi('categories', { method: 'POST', body: payload })
    }
    startCreate()
    await refresh()
  }
  catch (err: any) {
    const data = err?.data?.data ?? err?.data
    serverErrors.value = data?.errors ?? {}
    formError.value = data?.message ?? 'Category could not be saved.'
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
    await useAdminApi(`categories/${deleteTarget.value.id}`, { method: 'DELETE' })
    if (editing.value?.id === deleteTarget.value.id) startCreate()
    deleteTarget.value = null
    await refresh()
  }
  catch (err: any) {
    actionError.value = err?.data?.message ?? 'Category could not be deleted.'
  }
  finally {
    deleting.value = false
  }
}
</script>

<template>
  <div class="admin-panel">
    <h2>{{ editing ? `Edit ${editing.name}` : 'Add category' }}</h2>
    <p v-if="formError" class="admin-form-error" role="alert">{{ formError }}</p>
    <form class="admin-form-grid" @submit.prevent="save">
      <label class="admin-field">
        <span>Category name</span>
        <input v-model.trim="form.name" required placeholder="e.g. Signature Cakes">
        <small v-if="serverErrors.name" class="admin-field__error">{{ serverErrors.name[0] }}</small>
      </label>
      <label class="admin-field">
        <span>Slug</span>
        <input v-model.trim="form.slug" placeholder="Optional — auto-generated from name if left blank">
        <small v-if="serverErrors.slug" class="admin-field__error">{{ serverErrors.slug[0] }}</small>
      </label>
      <div class="admin-form-actions">
        <button class="admin-button admin-button--primary" type="submit" :disabled="busy">{{ busy ? 'Saving…' : editing ? 'Save changes' : 'Add category' }}</button>
        <button v-if="editing" class="admin-button admin-button--secondary" type="button" :disabled="busy" @click="startCreate">Cancel</button>
      </div>
    </form>

    <p v-if="actionError" class="admin-form-error" role="alert">{{ actionError }}</p>
    <div v-if="pending" class="admin-table-loading" aria-label="Loading categories" />
    <AdminEmptyState v-else-if="error" title="Categories could not be loaded" description="Check the API connection and try again."><button class="admin-button admin-button--primary" type="button" @click="refresh()">Try again</button></AdminEmptyState>
    <AdminEmptyState v-else-if="!response?.data.length" title="No categories yet" description="Add a category above to start organising products." />
    <div v-else class="admin-table-wrap">
      <table class="admin-data-table">
        <thead>
          <tr><th>Name</th><th>Slug</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <tr v-for="category in response.data" :key="category.id">
            <td data-label="Name">{{ category.name }}</td>
            <td data-label="Slug">{{ category.slug }}</td>
            <td data-label="Actions">
              <div class="admin-row-actions">
                <button type="button" @click="startEdit(category)">Edit</button>
                <button type="button" @click="deleteTarget = category">Delete</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <AdminConfirmDialog
      :open="Boolean(deleteTarget)"
      title="Delete category?"
      :description="`${deleteTarget?.name || 'This category'} will be removed. Categories still assigned to products cannot be deleted.`"
      confirm-label="Delete category"
      :busy="deleting"
      @close="deleteTarget = null"
      @confirm="confirmDelete"
    />
  </div>
</template>
