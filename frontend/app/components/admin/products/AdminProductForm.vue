<script setup lang="ts">
import AdminImageUrlEditor from './AdminImageUrlEditor.vue'
import AdminVariantEditor from './AdminVariantEditor.vue'
import { toProductPayload } from '~/utils/admin.mjs'
import type { AdminProduct, ProductFormData, StoreProductPayload } from '~/types/admin'
import type { Category } from '~/types/catalog'

const props = withDefaults(defineProps<{
  initialProduct?: AdminProduct | null
  categories: Category[]
  busy?: boolean
  serverErrors?: Record<string, string[]>
}>(), {
  initialProduct: null,
  busy: false,
  serverErrors: () => ({}),
})

const emit = defineEmits<{ save: [payload: StoreProductPayload]; dirty: [value: boolean] }>()
const activeSection = ref<'basic' | 'variants' | 'images'>('basic')
const ready = ref(false)
const form = reactive<ProductFormData>({
  category_id: null,
  name: '',
  description: '',
  ingredients: '',
  allergens: '',
  price: 0,
  stock: 0,
  status: 'active',
  images: [],
  variants: [],
})

function hydrate(product?: AdminProduct | null) {
  if (!product) return
  Object.assign(form, {
    category_id: product.category?.id ?? null,
    name: product.name,
    description: product.description || '',
    ingredients: product.ingredients || '',
    allergens: product.allergens || '',
    price: product.price,
    stock: product.stock,
    status: product.status,
    images: product.images.map(image => ({ url: image.url })),
    variants: product.variants.map(variant => ({ label: variant.label, price: variant.price, stock: variant.stock })),
  })
}

hydrate(props.initialProduct)
onMounted(() => { ready.value = true })
watch(form, () => { if (ready.value) emit('dirty', true) }, { deep: true })

function submit() {
  emit('save', toProductPayload(form))
}
</script>

<template>
  <form class="admin-product-editor admin-panel" @submit.prevent="submit">
    <nav class="admin-editor-tabs" aria-label="Product form sections">
      <button v-for="section in ['basic', 'variants', 'images'] as const" :key="section" type="button" :class="{ active: activeSection === section }" @click="activeSection = section">
        {{ section === 'basic' ? 'Basic Info' : section.charAt(0).toUpperCase() + section.slice(1) }}
      </button>
    </nav>

    <section v-show="activeSection === 'basic'" class="admin-editor-section admin-editor-basic">
      <div class="admin-editor-column">
        <label class="admin-field"><span>Product name</span><input v-model.trim="form.name" required placeholder="e.g. Choc Chip Crunch"><small v-if="serverErrors.name" class="admin-field__error">{{ serverErrors.name[0] }}</small></label>
        <label class="admin-field"><span>Category</span><select v-model="form.category_id"><option :value="null">Select category</option><option v-for="category in categories" :key="category.slug" :value="category.id">{{ category.name }}</option></select><small v-if="serverErrors.category_id" class="admin-field__error">{{ serverErrors.category_id[0] }}</small></label>
        <label class="admin-field"><span>Status</span><select v-model="form.status"><option value="active">Active</option><option value="inactive">Inactive</option></select></label>
        <label class="admin-field"><span>Description</span><textarea v-model="form.description" rows="5" placeholder="Write product description…" /></label>
        <label class="admin-field"><span>Ingredients</span><input v-model="form.ingredients" placeholder="Flour, butter, chocolate chips…"></label>
        <label class="admin-field"><span>Allergens</span><input v-model="form.allergens" placeholder="Gluten, milk, eggs…"></label>
      </div>
      <aside class="admin-editor-column admin-editor-pricing">
        <label class="admin-field"><span>Base price (RM)</span><input v-model.number="form.price" type="number" min="0" step="0.01" required><small v-if="serverErrors.price" class="admin-field__error">{{ serverErrors.price[0] }}</small></label>
        <label class="admin-field"><span>Base stock</span><input v-model.number="form.stock" type="number" min="0" step="1" required><small v-if="serverErrors.stock" class="admin-field__error">{{ serverErrors.stock[0] }}</small></label>
        <p v-if="form.variants.length" class="admin-editor-notice">This product has variants, so variant prices and stock are used for ordering. Base values remain the “from” display fallback.</p>
      </aside>
    </section>

    <AdminVariantEditor v-show="activeSection === 'variants'" v-model="form.variants" />
    <AdminImageUrlEditor v-show="activeSection === 'images'" v-model="form.images" />

    <p v-if="serverErrors.variants" class="admin-form-error">{{ serverErrors.variants[0] }}</p>
    <footer class="admin-editor-footer">
      <NuxtLink to="/admin/products" class="admin-button admin-button--secondary">Cancel</NuxtLink>
      <button class="admin-button admin-button--primary" type="submit" :disabled="busy">{{ busy ? 'Saving…' : 'Save Product' }}</button>
    </footer>
  </form>
</template>
