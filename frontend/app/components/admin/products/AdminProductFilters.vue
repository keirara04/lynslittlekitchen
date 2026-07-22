<script setup lang="ts">
import type { Category } from '~/types/catalog'

defineProps<{
  search: string
  category: string
  status: string
  categories: Category[]
}>()

defineEmits<{
  search: [value: string]
  change: [key: 'category' | 'status', value: string]
}>()
</script>

<template>
  <div class="admin-filters admin-panel">
    <label class="admin-search-field">
      <span class="sr-only">Search products</span>
      <input :value="search" type="search" placeholder="Search products…" @input="$emit('search', ($event.target as HTMLInputElement).value)">
    </label>
    <label>
      <span class="sr-only">Category</span>
      <select :value="category" @change="$emit('change', 'category', ($event.target as HTMLSelectElement).value)">
        <option value="">All Categories</option>
        <option v-for="item in categories" :key="item.slug" :value="item.slug">{{ item.name }}</option>
      </select>
    </label>
    <label>
      <span class="sr-only">Status</span>
      <select :value="status" @change="$emit('change', 'status', ($event.target as HTMLSelectElement).value)">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>
    </label>
  </div>
</template>
