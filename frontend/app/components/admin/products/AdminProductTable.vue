<script setup lang="ts">
import { adminProductImage, formatAdminCurrency, stockSummary } from '~/utils/admin.mjs'
import type { AdminProduct } from '~/types/admin'

defineProps<{ products: AdminProduct[] }>()
defineEmits<{ delete: [product: AdminProduct] }>()
</script>

<template>
  <div class="admin-table-wrap admin-panel">
    <table class="admin-data-table admin-product-table">
      <thead>
        <tr><th>Product</th><th>Category</th><th>Variants</th><th>Stock</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <tr v-for="product in products" :key="product.id">
          <td data-label="Product">
            <div class="admin-product-cell">
              <img :src="adminProductImage(product)" :alt="product.name">
              <div><strong>{{ product.name }}</strong><small>{{ formatAdminCurrency(product.price) }}</small></div>
            </div>
          </td>
          <td data-label="Category">{{ product.category?.name || 'Uncategorised' }}</td>
          <td data-label="Variants">{{ product.variants.length }}</td>
          <td data-label="Stock">{{ stockSummary(product) }}</td>
          <td data-label="Status"><AdminStatusBadge :status="product.status" /></td>
          <td data-label="Actions">
            <div class="admin-row-actions">
              <NuxtLink :to="`/admin/products/${product.id}/edit`">Edit</NuxtLink>
              <button type="button" @click="$emit('delete', product)">Delete</button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
