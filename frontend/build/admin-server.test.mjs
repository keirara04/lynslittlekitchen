import assert from 'node:assert/strict'
import { readFile } from 'node:fs/promises'
import test from 'node:test'

import { isAllowedAdminProxy, normalizeAdminProxyPath } from '../server/utils/adminProxyPolicy.mjs'

test('allowlists only supported Laravel paths and methods', () => {
  assert.equal(isAllowedAdminProxy('GET', 'dashboard'), true)
  assert.equal(isAllowedAdminProxy('GET', 'categories'), true)
  assert.equal(isAllowedAdminProxy('PUT', 'products/12'), true)
  assert.equal(isAllowedAdminProxy('PATCH', 'orders/7/status'), true)
  assert.equal(isAllowedAdminProxy('POST', 'orders/7/status'), false)
  assert.equal(isAllowedAdminProxy('GET', 'https://example.com'), false)
  assert.equal(isAllowedAdminProxy('GET', 'products/../orders'), false)
})

test('normalizes harmless surrounding slashes and rejects encoded path separators', () => {
  assert.equal(normalizeAdminProxyPath('/products/12/'), 'products/12')
  assert.equal(normalizeAdminProxyPath('products%2F12'), null)
  assert.equal(normalizeAdminProxyPath('products\\12'), null)
})

test('session implementation uses an HttpOnly cookie without browser storage', async () => {
  const source = await readFile(new URL('../server/utils/adminSession.ts', import.meta.url), 'utf8')
  assert.match(source, /httpOnly:\s*true/)
  assert.match(source, /sameSite:\s*'lax'/)
  assert.doesNotMatch(source, /localStorage/)
})

test('login rejects authenticated non-admin accounts', async () => {
  const source = await readFile(new URL('../server/api/admin-auth/login.post.ts', import.meta.url), 'utf8')
  assert.match(source, /user\.role !== 'admin'/)
  assert.match(source, /\/logout/)
})
