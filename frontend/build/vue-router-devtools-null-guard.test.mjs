import assert from 'node:assert/strict'
import { readFile } from 'node:fs/promises'
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

test('registers the guard in the Nuxt Vite configuration', async () => {
  const config = await readFile(
    new URL('../nuxt.config.ts', import.meta.url),
    'utf8',
  )

  assert.match(
    config,
    /import \{ vueRouterDevtoolsNullGuard \} from '.\/build\/vue-router-devtools-null-guard\.mjs'/,
  )
  assert.match(config, /plugins:\s*\[vueRouterDevtoolsNullGuard\(\)\]/)
})
