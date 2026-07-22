<script setup lang="ts">
import type { ProductFormData } from '~/types/admin'

const model = defineModel<ProductFormData['images']>({ required: true })
const failed = ref<Record<number, boolean>>({})

function addImage() {
  model.value.push({ url: '' })
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
    <header><div><h2>Product images</h2><p>Add HTTPS image URLs in display order. File upload is not connected yet.</p></div><button class="admin-button admin-button--secondary" type="button" @click="addImage">＋ Add image URL</button></header>
    <div v-if="model.length" class="admin-repeater">
      <article v-for="(image, index) in model" :key="index" class="admin-repeater__row admin-repeater__row--image">
        <div class="admin-image-preview">
          <img v-if="image.url && !failed[index]" :src="image.url" alt="Product preview" @error="failed[index] = true" @load="failed[index] = false">
          <span v-else>{{ image.url ? 'Image could not be loaded.' : 'No preview' }}</span>
        </div>
        <label class="admin-field"><span>HTTPS image URL</span><input v-model.trim="image.url" type="url" placeholder="https://…" @input="failed[index] = false"></label>
        <div class="admin-repeater__actions">
          <button type="button" :disabled="index === 0" aria-label="Move image up" @click="move(index, -1)">↑</button>
          <button type="button" :disabled="index === model.length - 1" aria-label="Move image down" @click="move(index, 1)">↓</button>
          <button type="button" aria-label="Remove image" @click="model.splice(index, 1)">×</button>
        </div>
      </article>
    </div>
    <p v-else class="admin-editor-hint">No image URLs. The storefront placeholder will be used.</p>
  </section>
</template>
