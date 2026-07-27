<script setup lang="ts">
import type { AdminProduct, AdminSettings } from '~/types/admin'

const props = defineProps<{ settings: AdminSettings; products: AdminProduct[]; busy: boolean; serverErrors: Record<string, string[]> }>()
const emit = defineEmits<{ save: [payload: Partial<AdminSettings>] }>()

const form = reactive({
  featured_hero_product_id: props.settings.featured_hero_product_id,
  featured_banner_product_id: props.settings.featured_banner_product_id,
})

function submit() {
  emit('save', { ...form })
}
</script>

<template>
  <form class="admin-panel" @submit.prevent="submit">
    <div class="admin-form-grid">
      <label class="admin-field">
        <span>Hero Product</span>
        <select v-model="form.featured_hero_product_id">
          <option :value="null">None — use default</option>
          <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
        </select>
        <small v-if="serverErrors.featured_hero_product_id" class="admin-field__error">{{ serverErrors.featured_hero_product_id[0] }}</small>
      </label>
      <label class="admin-field">
        <span>Banner Product</span>
        <select v-model="form.featured_banner_product_id">
          <option :value="null">None — use default</option>
          <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
        </select>
        <small v-if="serverErrors.featured_banner_product_id" class="admin-field__error">{{ serverErrors.featured_banner_product_id[0] }}</small>
      </label>
      <div class="admin-form-actions">
        <button class="admin-button admin-button--primary" type="submit" :disabled="busy">{{ busy ? 'Saving…' : 'Save Changes' }}</button>
      </div>
    </div>
  </form>
</template>
