import { defineStore } from 'pinia'
import { calculateCartTotals, resolveProductImage, setCartLineQuantity, upsertCartLine } from '~/utils/storefront.mjs'
import type { CartLine, Product, ProductVariant } from '~/types/catalog'

export const useCartStore = defineStore('cart', () => {
  const lines = ref<CartLine[]>([])
  const note = ref('')
  const hydrated = ref(false)
  const deliveryFee = ref(0)

  const totals = computed(() => calculateCartTotals(lines.value, deliveryFee.value))

  function persist() {
    if (!import.meta.client || !hydrated.value) return
    localStorage.setItem('llk-cart', JSON.stringify({ lines: lines.value, note: note.value }))
  }

  function hydrate() {
    if (!import.meta.client || hydrated.value) return
    try {
      const saved = JSON.parse(localStorage.getItem('llk-cart') || '{}')
      lines.value = Array.isArray(saved.lines) ? saved.lines : []
      note.value = typeof saved.note === 'string' ? saved.note : ''
    }
    catch {
      lines.value = []
    }
    hydrated.value = true
  }

  function addProduct(product: Product, variant?: ProductVariant, quantity = 1) {
    const selectedVariant = variant || product.variants?.[0]
    const variantKey = selectedVariant?.id ?? 'base'
    const line: CartLine = {
      key: `${product.slug}:${variantKey}`,
      productId: product.id,
      slug: product.slug,
      name: product.name,
      image: resolveProductImage(product.slug, product.images),
      variantId: selectedVariant?.id ?? null,
      variantLabel: selectedVariant?.label ?? 'Standard box',
      unitPrice: Number(selectedVariant?.price ?? product.price),
      quantity: Math.max(1, quantity),
    }
    lines.value = upsertCartLine(lines.value, line)
    persist()
  }

  function updateQuantity(key: string, quantity: number) {
    lines.value = setCartLineQuantity(lines.value, key, quantity)
    persist()
  }

  function removeLine(key: string) {
    lines.value = lines.value.filter(line => line.key !== key)
    persist()
  }

  function setNote(value: string) {
    note.value = value
    persist()
  }

  function clear() {
    lines.value = []
    note.value = ''
    persist()
  }

  return { lines, note, hydrated, deliveryFee, totals, hydrate, addProduct, updateQuantity, removeLine, setNote, clear }
})
