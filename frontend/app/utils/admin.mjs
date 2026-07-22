export const fulfilmentStatuses = [
  'pending',
  'preparing',
  'baking',
  'packing',
  'out_for_delivery',
  'completed',
]

export function formatAdminCurrency(value) {
  const amount = Number.isFinite(Number(value)) ? Number(value) : 0
  return `RM${amount.toLocaleString('en-MY', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })}`
}

export function formatAdminDate(value, options = {}) {
  if (!value) return '—'

  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return '—'

  return new Intl.DateTimeFormat('en-MY', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    ...options,
  }).format(date)
}

export function humanizeStatus(status) {
  if (!status) return 'Unknown'
  const label = String(status).replaceAll('_', ' ')
  return label.charAt(0).toUpperCase() + label.slice(1)
}

export function progressIndex(status) {
  return fulfilmentStatuses.indexOf(status)
}

export function stockSummary(product) {
  const variants = Array.isArray(product?.variants) ? product.variants : []
  if (variants.length > 0) {
    const total = variants.reduce((sum, variant) => sum + Number(variant.stock || 0), 0)
    return `${total} across ${variants.length} ${variants.length === 1 ? 'variant' : 'variants'}`
  }

  return `${Number(product?.stock || 0)} in stock`
}

export function buildAdminQuery(filters) {
  return Object.fromEntries(Object.entries(filters).filter(([, value]) => (
    value !== '' && value !== null && value !== undefined
  )))
}

export function adminProductImage(product) {
  return product?.images?.[0]?.url || '/images/products/cookie-placeholder.svg'
}

function nullableText(value) {
  const text = String(value || '').trim()
  return text || null
}

export function toProductPayload(form) {
  return {
    category_id: form.category_id === '' || form.category_id === null ? null : Number(form.category_id),
    name: String(form.name || '').trim(),
    description: nullableText(form.description),
    ingredients: nullableText(form.ingredients),
    allergens: nullableText(form.allergens),
    price: Number(form.price || 0),
    stock: Number(form.stock || 0),
    status: form.status,
    images: (form.images || []).map(image => String(image.url || '').trim()).filter(Boolean),
    variants: (form.variants || []).map(variant => ({
      label: String(variant.label || '').trim(),
      price: Number(variant.price || 0),
      stock: Number(variant.stock || 0),
    })),
  }
}

export function orderActions(order) {
  return Array.isArray(order?.allowed_next_statuses) ? [...order.allowed_next_statuses] : []
}
