import assert from 'node:assert/strict'
import { access, readFile } from 'node:fs/promises'
import test from 'node:test'

const appUrl = new URL('../app/', import.meta.url)

async function source(path) {
  return readFile(new URL(path, appUrl), 'utf8')
}

test('admin auth store keeps only safe user session state', async () => {
  const content = await source('stores/adminAuth.ts')
  assert.match(content, /AdminUser \| null/)
  assert.match(content, /checkSession/)
  assert.doesNotMatch(content, /localStorage/)
  assert.doesNotMatch(content, /token\s*=/)
})

test('admin middleware checks the server-backed session', async () => {
  const content = await source('middleware/admin-auth.ts')
  assert.match(content, /checkSession/)
  assert.match(content, /\/admin\/login/)
})

test('login uses the dedicated auth layout', async () => {
  const content = await source('pages/admin/login.vue')
  assert.match(content, /layout:\s*'admin-auth'/)
  assert.match(content, /auth\.login/)
})

test('admin layout contains responsive navigation landmarks', async () => {
  const content = await source('layouts/admin.vue')
  assert.match(content, /AdminSidebar/)
  assert.match(content, /AdminHeader/)
  assert.match(content, /mobileNavOpen/)
})

test('admin visual tokens and both layouts exist', async () => {
  await access(new URL('layouts/admin-auth.vue', appUrl))
  const css = await source('assets/css/admin.css')
  assert.match(css, /--admin-terracotta:\s*#9d523b/i)
  assert.match(css, /@media \(max-width:\s*767px\)/)
  assert.match(css, /prefers-reduced-motion/)
})
