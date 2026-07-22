import assert from 'node:assert/strict'
import test from 'node:test'
import { vueRouterDevtoolsNullGuard } from './vue-router-devtools-null-guard.mjs'

test('guards a null Vue Router devtools instance', () => {
  const plugin = vueRouterDevtoolsNullGuard()
  const source = 'instance.__vrv_devtools = info;'
  const result = plugin.transform(
    source,
    '/app/node_modules/vue-router/dist/vue-router.js',
  )

  assert.equal(
    result.code,
    'if (instance) instance.__vrv_devtools = info;',
  )
})

test('ignores unrelated modules', () => {
  const plugin = vueRouterDevtoolsNullGuard()

  assert.equal(
    plugin.transform('instance.__vrv_devtools = info;', '/app/app.vue'),
    null,
  )
})

test('fails when the targeted Vue Router source no longer matches', () => {
  const plugin = vueRouterDevtoolsNullGuard()

  assert.throws(
    () =>
      plugin.transform(
        'export const router = {}',
        '/app/node_modules/vue-router/dist/vue-router.js',
      ),
    /unsafe devtools assignment was not found/,
  )
})
