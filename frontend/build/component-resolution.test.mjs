import test from 'node:test'
import assert from 'node:assert/strict'
import { readFile } from 'node:fs/promises'

const productPages = [
  'app/pages/index.vue',
  'app/pages/shop/index.vue',
  'app/pages/shop/[slug].vue',
]

test('product pages use Nuxt registered ProductCard component name', async () => {
  for (const file of productPages) {
    const source = await readFile(new URL(`../${file}`, import.meta.url), 'utf8')
    assert.equal(source.includes('ProductProductCard'), false, `${file} uses an unresolved component name`)
    assert.equal(source.includes('<ProductCard'), true, `${file} should render ProductCard`)
  }
})

test('local API fallback matches the Laravel route prefix', async () => {
  const source = await readFile(new URL('../nuxt.config.ts', import.meta.url), 'utf8')
  assert.match(source, /http:\/\/127\.0\.0\.1:8000\/api['"]/)
  assert.doesNotMatch(source, /127\.0\.0\.1:8000\/api\/v1/)
})
