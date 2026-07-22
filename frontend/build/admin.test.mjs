import assert from 'node:assert/strict'
import test from 'node:test'

import {
  adminProductImage,
  buildAdminQuery,
  formatAdminCurrency,
  humanizeStatus,
  progressIndex,
  stockSummary,
  toProductPayload,
} from '../app/utils/admin.mjs'

test('formats admin currency in Malaysian Ringgit', () => {
  assert.equal(formatAdminCurrency(86), 'RM86.00')
})

test('omits empty admin filters without removing zero', () => {
  assert.deepEqual(buildAdminQuery({ search: '', status: 'active', page: 2, stock: 0 }), {
    status: 'active',
    page: 2,
    stock: 0,
  })
})

test('summarizes variant inventory instead of ignored base stock', () => {
  assert.equal(stockSummary({ stock: 999, variants: [{ stock: 4 }, { stock: 7 }] }), '11 across 2 variants')
  assert.equal(stockSummary({ stock: 8, variants: [] }), '8 in stock')
})

test('keeps terminal side states outside successful progress', () => {
  assert.equal(progressIndex('rejected'), -1)
  assert.equal(progressIndex('cancelled'), -1)
  assert.equal(progressIndex('packing'), 3)
})

test('humanizes API status labels', () => {
  assert.equal(humanizeStatus('out_for_delivery'), 'Out for delivery')
  assert.equal(humanizeStatus('paid'), 'Paid')
})

test('uses the first product image with a stable placeholder fallback', () => {
  assert.equal(adminProductImage({ images: [{ url: 'https://images.example.test/cookie.jpg' }] }), 'https://images.example.test/cookie.jpg')
  assert.equal(adminProductImage({ images: [] }), '/images/products/cookie-placeholder.svg')
})

test('maps the product editor to the exact Laravel payload', () => {
  const form = {
    category_id: '2',
    name: ' Choc Chip Crunch ',
    description: ' Soft-centred cookie ',
    ingredients: 'Flour, butter, chocolate',
    allergens: ' ',
    price: '25.00',
    stock: '0',
    status: 'active',
    images: [{ url: ' https://images.example.test/choc-chip.jpg ' }, { url: '' }],
    variants: [{ label: ' 300g (12 pcs) ', price: '25', stock: '12' }],
  }

  assert.deepEqual(toProductPayload(form), {
    category_id: 2,
    name: 'Choc Chip Crunch',
    description: 'Soft-centred cookie',
    ingredients: 'Flour, butter, chocolate',
    allergens: null,
    price: 25,
    stock: 0,
    status: 'active',
    images: ['https://images.example.test/choc-chip.jpg'],
    variants: [{ label: '300g (12 pcs)', price: 25, stock: 12 }],
  })
})
