<script setup lang="ts">
import type { ProductFormData } from '~/types/admin'

const model = defineModel<ProductFormData['images']>({ required: true })
const failed = ref<Record<number, boolean>>({})

const { upload, uploading, error } = useCloudinaryUpload()
const fileInput = ref<HTMLInputElement>()

function addImage() {
  model.value.push({ url: '' })
}

function triggerUpload() {
  fileInput.value?.click()
}

async function onFileSelected(event: Event) {
  const input = event.target as HTMLInputElement
  const file = input.files?.[0]
  input.value = ''
  if (!file) return

  try {
    const url = await upload(file)
    model.value.push({ url })
  }
  catch {
    // error is already captured in the `error` ref for display below.
  }
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
    <header>
      <div><h2>Product images</h2><p>Upload a photo or paste an HTTPS image URL directly.</p></div>
      <div style="display: flex; gap: .5rem;">
        <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="onFileSelected">
        <button class="admin-button admin-button--secondary" type="button" :disabled="uploading" @click="triggerUpload">
          {{ uploading ? 'Uploading…' : '⬆ Upload image' }}
        </button>
        <button class="admin-button admin-button--secondary" type="button" @click="addImage">＋ Add image URL</button>
      </div>
    </header>
    <p v-if="error" class="admin-field__error">{{ error }}</p>
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
