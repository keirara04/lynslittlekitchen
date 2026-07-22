const productImages = {
  'choc-chip-crunch': '/images/products/choc-chip-crunch-temp.png',
  'dubai-chewy-cookie': '/images/products/dubai-chewy-cookie-temp.png',
}

const orderStatuses = [
  'pending',
  'preparing',
  'baking',
  'packing',
  'out_for_delivery',
  'completed',
]

export function formatRinggit(value) {
  const amount = Number(value)
  return `RM${Number.isFinite(amount) ? amount.toFixed(2) : '0.00'}`
}

export function filterAndSortProducts(products, filters = {}) {
  const search = String(filters.search ?? '').trim().toLowerCase()
  const category = String(filters.category ?? '').trim()

  const result = products.filter((product) => {
    const matchesSearch = !search || product.name.toLowerCase().includes(search)
    const matchesCategory = !category || category === 'all' || product.category?.slug === category
    return matchesSearch && matchesCategory
  })

  return result.toSorted((first, second) => {
    if (filters.sort === 'price_asc') return Number(first.price) - Number(second.price)
    if (filters.sort === 'price_desc') return Number(second.price) - Number(first.price)
    if (filters.sort === 'name') return first.name.localeCompare(second.name)
    return 0
  })
}

export function calculateCartTotals(lines, deliveryFee = 0) {
  const subtotal = lines.reduce(
    (sum, line) => sum + (Number(line.unitPrice) * Number(line.quantity)),
    0,
  )
  const itemCount = lines.reduce((sum, line) => sum + Number(line.quantity), 0)
  const normalizedDeliveryFee = Number(deliveryFee) || 0

  return {
    subtotal,
    deliveryFee: normalizedDeliveryFee,
    total: subtotal + normalizedDeliveryFee,
    itemCount,
  }
}

export function resolveProductImage(slug, images = []) {
  return productImages[slug] || images[0]?.url || '/images/products/cookie-placeholder.svg'
}

export function getOrderProgress(status) {
  if (status === 'cancelled' || status === 'rejected') return -1
  const progress = orderStatuses.indexOf(status)
  return progress === -1 ? 0 : progress
}

export function upsertCartLine(lines, incoming) {
  const existing = lines.find(line => line.key === incoming.key)
  if (!existing) return [...lines, { ...incoming }]

  return lines.map(line => line.key === incoming.key
    ? { ...line, quantity: Number(line.quantity) + Number(incoming.quantity) }
    : { ...line })
}

export function setCartLineQuantity(lines, key, quantity) {
  const normalizedQuantity = Math.max(0, Number(quantity) || 0)
  if (normalizedQuantity === 0) return lines.filter(line => line.key !== key)

  return lines.map(line => line.key === key
    ? { ...line, quantity: normalizedQuantity }
    : { ...line })
}
