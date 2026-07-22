import test from 'node:test'
import assert from 'node:assert/strict'

import {
  calculateCartTotals,
  filterAndSortProducts,
  formatRinggit,
  getOrderProgress,
  resolveProductImage,
  setCartLineQuantity,
  upsertCartLine,
} from '../app/utils/storefront.mjs'

const products = [
  { name: 'Choc Chip Crunch', slug: 'choc-chip-crunch', price: 18, category: { slug: 'classic-cookies' } },
  { name: 'Dubai Chewy Cookie', slug: 'dubai-chewy-cookie', price: 24, category: { slug: 'stuffed-cookies' } },
  { name: 'Raya Kurma Cookie', slug: 'raya-kurma-cookie', price: 22, category: { slug: 'seasonal-specials' } },
]

test('formats prices as Malaysian Ringgit', () => {
  assert.equal(formatRinggit(18), 'RM18.00')
  assert.equal(formatRinggit('24.5'), 'RM24.50')
})

test('filters products by search and category before sorting by price', () => {
  const result = filterAndSortProducts(products, {
    search: 'cookie',
    category: 'stuffed-cookies',
    sort: 'price_desc',
  })

  assert.deepEqual(result.map(product => product.slug), ['dubai-chewy-cookie'])
})

test('sorts a copy without mutating the API product array', () => {
  const originalOrder = products.map(product => product.slug)
  const result = filterAndSortProducts(products, { sort: 'price_desc' })

  assert.deepEqual(result.map(product => product.price), [24, 22, 18])
  assert.deepEqual(products.map(product => product.slug), originalOrder)
})

test('calculates cart subtotal, delivery fee, total, and item count', () => {
  assert.deepEqual(calculateCartTotals([
    { unitPrice: 18, quantity: 2 },
    { unitPrice: 24.5, quantity: 1 },
  ], 8), {
    subtotal: 60.5,
    deliveryFee: 8,
    total: 68.5,
    itemCount: 3,
  })
})

test('maps real products to replaceable local images and others to the fallback', () => {
  assert.equal(resolveProductImage('choc-chip-crunch'), '/images/products/choc-chip-crunch-temp.png')
  assert.equal(resolveProductImage('dubai-chewy-cookie'), '/images/products/dubai-chewy-cookie-temp.png')
  assert.equal(resolveProductImage('raya-kurma-cookie'), '/images/products/cookie-placeholder.svg')
})

test('maps an order status to a bounded customer-facing progress index', () => {
  assert.equal(getOrderProgress('pending'), 0)
  assert.equal(getOrderProgress('packing'), 3)
  assert.equal(getOrderProgress('completed'), 5)
  assert.equal(getOrderProgress('cancelled'), -1)
  assert.equal(getOrderProgress('unexpected'), 0)
})

test('merges identical products and variants when adding to cart', () => {
  const existing = [{ key: 'choc-chip-crunch:12', quantity: 1, unitPrice: 18 }]
  const result = upsertCartLine(existing, {
    key: 'choc-chip-crunch:12',
    quantity: 2,
    unitPrice: 18,
  })

  assert.equal(result.length, 1)
  assert.equal(result[0].quantity, 3)
  assert.equal(existing[0].quantity, 1)
})

test('keeps different variants as separate cart lines', () => {
  const existing = [{ key: 'choc-chip-crunch:12', quantity: 1 }]
  const result = upsertCartLine(existing, { key: 'choc-chip-crunch:20', quantity: 1 })

  assert.deepEqual(result.map(line => line.key), ['choc-chip-crunch:12', 'choc-chip-crunch:20'])
})

test('removes a cart line when its quantity reaches zero', () => {
  const lines = [{ key: 'dubai-chewy-cookie:base', quantity: 2 }]
  assert.deepEqual(setCartLineQuantity(lines, 'dubai-chewy-cookie:base', 0), [])
})
