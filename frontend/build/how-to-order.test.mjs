import test from 'node:test'
import assert from 'node:assert/strict'
import { readFile } from 'node:fs/promises'

const page = await readFile(new URL('../app/pages/how-to-order.vue', import.meta.url), 'utf8')

test('renders the complete five-step ordering journey', () => {
  assert.equal((page.match(/number: '\d{2}'/g) || []).length, 5)

  for (const title of [
    'Choose your cookies',
    'Add your details',
    'Confirm your order',
    'We bake',
    'Delivery or pickup',
  ]) {
    assert.match(page, new RegExp(title))
  }
})

test('provides both shop calls to action', () => {
  assert.equal((page.match(/to="\/shop"/g) || []).length, 2)
})

test('uses all six local reference photographs', () => {
  for (const image of [
    'hero.png',
    'choose-cookies.png',
    'add-details.png',
    'confirm-order.png',
    'we-bake.png',
    'delivery-pickup.png',
  ]) {
    assert.match(page, new RegExp(`/images/how-to-order/${image.replace('.', '\\.')}`))
  }
})
