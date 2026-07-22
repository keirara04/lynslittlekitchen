# Vue Router Devtools Null Guard Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Prevent Vue Router's development-only devtools metadata assignment from crashing when Nuxt supplies a null internal component instance.

**Architecture:** A project-owned Vite pre-transform will target only `vue-router/dist/vue-router.js` and replace the exact unsafe assignment with a guarded assignment. The transform is independently tested and registered from `nuxt.config.ts`.

**Tech Stack:** Nuxt 4, Vite 8, Vue Router 5, Node's built-in test runner

## Global Constraints

- Do not modify `node_modules`.
- Do not change page routing or production application behavior.
- Fail loudly if the expected unsafe Vue Router source is absent.

---

### Task 1: Add and register the null-guard transform

**Files:**
- Create: `frontend/build/vue-router-devtools-null-guard.mjs`
- Create: `frontend/build/vue-router-devtools-null-guard.test.mjs`
- Modify: `frontend/nuxt.config.ts`
- Modify: `frontend/package.json`

**Interfaces:**
- Produces: `vueRouterDevtoolsNullGuard(): { name, enforce, transform }`
- Consumes: Vite's `transform(code, id)` plugin contract

- [ ] **Step 1: Write the failing regression tests**

```js
import assert from 'node:assert/strict'
import test from 'node:test'
import { vueRouterDevtoolsNullGuard } from './vue-router-devtools-null-guard.mjs'

test('guards a null Vue Router devtools instance', () => {
  const plugin = vueRouterDevtoolsNullGuard()
  const source = 'instance.__vrv_devtools = info;'
  const result = plugin.transform(source, '/app/node_modules/vue-router/dist/vue-router.js')
  assert.equal(result.code, 'if (instance) instance.__vrv_devtools = info;')
})

test('ignores unrelated modules', () => {
  const plugin = vueRouterDevtoolsNullGuard()
  assert.equal(plugin.transform('instance.__vrv_devtools = info;', '/app/app.vue'), null)
})

test('fails when the targeted Vue Router source no longer matches', () => {
  const plugin = vueRouterDevtoolsNullGuard()
  assert.throws(
    () => plugin.transform('export const router = {}', '/app/node_modules/vue-router/dist/vue-router.js'),
    /unsafe devtools assignment was not found/,
  )
})
```

- [ ] **Step 2: Run the tests and verify RED**

Run: `node --test build/vue-router-devtools-null-guard.test.mjs`

Expected: FAIL because `vue-router-devtools-null-guard.mjs` does not exist.

- [ ] **Step 3: Add the minimal transform**

```js
const vueRouterModule = /[/\\]node_modules[/\\]vue-router[/\\]dist[/\\]vue-router\.js(?:\?.*)?$/
const unsafeAssignment = 'instance.__vrv_devtools = info;'
const guardedAssignment = 'if (instance) instance.__vrv_devtools = info;'

export function vueRouterDevtoolsNullGuard() {
  return {
    name: 'vue-router-devtools-null-guard',
    enforce: 'pre',
    transform(code, id) {
      if (!vueRouterModule.test(id)) return null
      if (!code.includes(unsafeAssignment)) {
        throw new Error('[vue-router-devtools-null-guard] The unsafe devtools assignment was not found. Remove or update this workaround for the installed Vue Router version.')
      }
      return { code: code.replace(unsafeAssignment, guardedAssignment), map: null }
    },
  }
}
```

- [ ] **Step 4: Register the transform and test command**

Add `import { vueRouterDevtoolsNullGuard } from './build/vue-router-devtools-null-guard.mjs'` to `frontend/nuxt.config.ts`, and add:

```ts
vite: {
  plugins: [vueRouterDevtoolsNullGuard()],
},
```

Add to `frontend/package.json` scripts:

```json
"test": "node --test build/*.test.mjs"
```

- [ ] **Step 5: Verify GREEN and the Nuxt build**

Run: `npm test`

Expected: 3 tests pass with 0 failures.

Run: `npm run build`

Expected: Nuxt build exits 0.

- [ ] **Step 6: Restart and verify development output**

Run: `npm run dev -- --host 127.0.0.1 --port 3000`

Request `/` and inspect the served Vue Router module. Expected: `/` returns HTTP 200 with `Cookie Business`, and the served module contains `if (instance) instance.__vrv_devtools = info;`.

- [ ] **Step 7: Commit**

```bash
git add frontend/build/vue-router-devtools-null-guard.mjs frontend/build/vue-router-devtools-null-guard.test.mjs frontend/nuxt.config.ts frontend/package.json docs/superpowers/plans/2026-07-22-vue-router-devtools-null-guard.md
git commit -m "fix: guard vue router devtools instance"
```
