<script setup lang="ts">
import type { ProductFormData } from '~/types/admin'

const model = defineModel<ProductFormData['variants']>({ required: true })

function addVariant() {
  model.value.push({ label: '', price: 0, stock: 0 })
}

function move(index: number, direction: -1 | 1) {
  const target = index + direction
  if (target < 0 || target >= model.value.length) return
  const rows = [...model.value]
  ;[rows[index], rows[target]] = [rows[target], rows[index]]
  model.value = rows
}
</script>

<template>
  <section class="admin-editor-section">
    <header><div><h2>Pack-size variants</h2><p>Each variant keeps its own selling price and stock.</p></div><button class="admin-button admin-button--secondary" type="button" @click="addVariant">＋ Add variant</button></header>
    <div v-if="model.length" class="admin-repeater">
      <article v-for="(variant, index) in model" :key="index" class="admin-repeater__row admin-repeater__row--variant">
        <label class="admin-field"><span>Label</span><input v-model="variant.label" required placeholder="300g (12 pcs)"></label>
        <label class="admin-field"><span>Price (RM)</span><input v-model.number="variant.price" type="number" min="0" step="0.01" required></label>
        <label class="admin-field"><span>Stock</span><input v-model.number="variant.stock" type="number" min="0" step="1" required></label>
        <div class="admin-repeater__actions">
          <button type="button" :disabled="index === 0" aria-label="Move variant up" @click="move(index, -1)">↑</button>
          <button type="button" :disabled="index === model.length - 1" aria-label="Move variant down" @click="move(index, 1)">↓</button>
          <button type="button" aria-label="Remove variant" @click="model.splice(index, 1)">×</button>
        </div>
      </article>
    </div>
    <p v-else class="admin-editor-hint">No variants. The base price and stock will be used.</p>
  </section>
</template>
