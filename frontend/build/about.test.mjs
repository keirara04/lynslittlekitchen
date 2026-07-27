import test from 'node:test'
import assert from 'node:assert/strict'
import { readFile } from 'node:fs/promises'

const page = await readFile(new URL('../app/pages/about.vue', import.meta.url), 'utf8')

test('tells Lyn’s bakery story through the approved sections', () => {
  for (const text of [
    'The good things take a little longer.',
    'From Lyn’s little kitchen',
    'Generous flavours',
    'Baked close to delivery',
    'Packed for sharing',
    'Made slowly. Meant to be shared.',
  ]) assert.match(page, new RegExp(text))
})

test('links visitors to shopping and ordering help', () => {
  assert.match(page, /to="\/shop"/)
  assert.match(page, /to="\/how-to-order"/)
  assert.match(page, /to="\/contact"/)
})

test('uses the three locally hosted bakery photographs accessibly', () => {
  for (const image of ['baking-hands.png', 'cookie-close-up.png', 'packing-orders.png']) {
    assert.match(page, new RegExp(`/images/about/${image.replace('.', '\\.')}`))
  }
  assert.match(page, /alt="[^"]+"/)
  assert.match(page, /loading="lazy"/)
  assert.match(page, /width="\d+"\s+height="\d+"/)
})
