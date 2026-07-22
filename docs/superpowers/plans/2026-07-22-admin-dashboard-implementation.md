# Lyn's Little Kitchen Admin Dashboard Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the reference-matched, role-protected Lyn's Little Kitchen admin interface with functional login, dashboard, products, orders, printing, responsive navigation, and honest placeholders for modules without APIs.

**Architecture:** The browser talks only to same-origin Nuxt server handlers. Nuxt stores the Laravel Sanctum token in an `HttpOnly` cookie and forwards only allowlisted admin requests; Laravel's `auth:sanctum` and `admin` middleware remain authoritative. Vue pages use focused Pinia/composable boundaries and shared typed utilities, while the existing storefront stays independent.

**Tech Stack:** Nuxt 4, Vue 3 Composition API, TypeScript, Pinia, Tailwind CSS, Nitro/H3 server handlers, Laravel, Sanctum, PHPUnit, Node's built-in test runner.

## Global Constraints

- Work on the existing `main` branch as requested; do not create a worktree or feature branch.
- Preserve every unrelated uncommitted storefront and backend change.
- Use the backend's current `/api` prefix; `/api/v1` migration is out of scope.
- Never expose the Sanctum token through Pinia, `localStorage`, rendered HTML, or browser JavaScript.
- Laravel `auth:sanctum` plus `admin` middleware remains the final authorization boundary.
- Functional modules are Login, Dashboard, Products, Orders, Print Invoice, and responsive navigation.
- Customers, Delivery Zones, Promotions, Reports, and Settings use real routes with branded placeholder content and no fabricated API data.
- Payment status and fulfilment status remain separate concepts.
- Order progress is `pending → preparing → baking → packing → out_for_delivery → completed`; rejected and cancelled are terminal side states.
- Product images are URL-based; do not display a file-upload control.
- Use existing temporary cookie photographs and existing font families; do not add remote font or icon dependencies.
- Verify responsive behavior at 375px and desktop widths.
- Follow red-green-refactor: no production behavior before its failing test.

---

## File map

### Backend

- `backend/routes/api.php` — protected product/order detail routes.
- `backend/app/Http/Controllers/Api/Admin/ProductController.php` — admin product detail response.
- `backend/app/Http/Controllers/Api/Admin/OrderController.php` — admin order list/detail response.
- `backend/app/Http/Resources/AdminOrderResource.php` — protected fulfilment/customer fields.
- `backend/app/Services/DashboardService.php` — status counts and recent orders.
- `backend/tests/Feature/Product/Admin/ProductCrudTest.php` — product detail authority.
- `backend/tests/Feature/Order/Admin/OrderDetailTest.php` — private order detail authority.
- `backend/tests/Feature/Order/Admin/OrderListTest.php` — order filter contract.
- `backend/tests/Feature/Admin/DashboardTest.php` — dashboard response contract.

### Nuxt server boundary

- `frontend/server/utils/adminProxyPolicy.mjs` — pure allowlist matcher.
- `frontend/server/utils/adminSession.ts` — `HttpOnly` cookie lifecycle.
- `frontend/server/utils/laravelAdmin.ts` — Laravel request/error forwarding.
- `frontend/server/api/admin-auth/login.post.ts` — role-checked login.
- `frontend/server/api/admin-auth/session.get.ts` — safe current-user response.
- `frontend/server/api/admin-auth/logout.post.ts` — token revocation and cookie clearing.
- `frontend/server/api/admin-proxy/[...path].ts` — allowlisted authenticated proxy.
- `frontend/build/admin-server.test.mjs` — server-policy regression tests.

### Nuxt client foundation

- `frontend/app/types/admin.ts` — API and form types.
- `frontend/app/utils/admin.mjs` and `admin.d.ts` — pure format/status/query helpers.
- `frontend/app/stores/adminAuth.ts` — safe user/session state only.
- `frontend/app/composables/useAdminApi.ts` — typed same-origin requests.
- `frontend/app/middleware/admin-auth.ts` — protected-page navigation behavior.
- `frontend/app/layouts/admin-auth.vue` — split login shell.
- `frontend/app/layouts/admin.vue` — responsive dashboard shell.
- `frontend/app/components/admin/*` — focused brand, sidebar, header, status, dialog, empty, and placeholder primitives.
- `frontend/app/assets/css/admin.css` — reference-derived admin tokens and responsive rules.
- `frontend/build/admin.test.mjs` — pure client behavior tests.
- `frontend/build/admin-structure.test.mjs` — route/layout security structure tests.

### Screens

- `frontend/app/pages/admin/login.vue`
- `frontend/app/pages/admin/index.vue`
- `frontend/app/pages/admin/products/index.vue`
- `frontend/app/pages/admin/products/new.vue`
- `frontend/app/pages/admin/products/[id]/edit.vue`
- `frontend/app/pages/admin/orders/index.vue`
- `frontend/app/pages/admin/orders/[id]/index.vue`
- `frontend/app/pages/admin/orders/[id]/print.vue`
- `frontend/app/pages/admin/customers.vue`
- `frontend/app/pages/admin/delivery-zones.vue`
- `frontend/app/pages/admin/promotions.vue`
- `frontend/app/pages/admin/reports.vue`
- `frontend/app/pages/admin/settings.vue`

---

### Task 1: Complete protected Laravel detail and dashboard contracts

**Files:**
- Modify: `backend/routes/api.php`
- Modify: `backend/app/Http/Controllers/Api/Admin/ProductController.php`
- Modify: `backend/app/Http/Controllers/Api/Admin/OrderController.php`
- Create: `backend/app/Http/Resources/AdminOrderResource.php`
- Modify: `backend/app/Services/DashboardService.php`
- Modify: `backend/tests/Feature/Product/Admin/ProductCrudTest.php`
- Create: `backend/tests/Feature/Order/Admin/OrderDetailTest.php`
- Create: `backend/tests/Feature/Order/Admin/OrderListTest.php`
- Modify: `backend/tests/Feature/Admin/DashboardTest.php`

**Interfaces:**
- Produces: `GET /api/admin/products/{product}` returning `ProductResource`.
- Produces: `GET /api/admin/orders/{order}` returning `AdminOrderResource`.
- Produces: admin orders containing `customer: { type, id, name, phone, email }`.
- Produces: order filters `search`, `payment_status`, `order_status`, and `delivery_method`.
- Produces: dashboard keys `orders_by_status` and `recent_orders`.

- [ ] **Step 1: Add failing product-detail authorization tests**

Append tests that prove an admin can load an inactive product by ID and a customer cannot:

```php
public function test_an_admin_can_view_an_inactive_product_for_editing(): void
{
    $admin = User::factory()->admin()->create();
    $product = Product::factory()->create(['status' => 'inactive']);

    $this->actingAs($admin, 'sanctum')
        ->getJson("/api/admin/products/{$product->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $product->id)
        ->assertJsonPath('data.status', 'inactive');
}

public function test_a_customer_cannot_view_an_admin_product(): void
{
    $customer = User::factory()->create();
    $product = Product::factory()->create();

    $this->actingAs($customer, 'sanctum')
        ->getJson("/api/admin/products/{$product->id}")
        ->assertForbidden();
}
```

- [ ] **Step 2: Add failing protected order-detail tests**

Create `OrderDetailTest.php` with guest data and assert the protected response includes it while a customer token receives `403`:

```php
public function test_admin_can_view_guest_fulfilment_details(): void
{
    $admin = User::factory()->admin()->create();
    $order = Order::factory()->create([
        'guest_name' => 'Aina Syazwani',
        'guest_phone' => '0123456789',
        'guest_email' => 'aina@example.test',
    ]);

    $this->actingAs($admin, 'sanctum')
        ->getJson("/api/admin/orders/{$order->id}")
        ->assertOk()
        ->assertJsonPath('data.customer.type', 'guest')
        ->assertJsonPath('data.customer.name', 'Aina Syazwani')
        ->assertJsonPath('data.customer.phone', '0123456789')
        ->assertJsonPath('data.customer.email', 'aina@example.test');
}

public function test_customer_cannot_view_admin_order_detail(): void
{
    $customer = User::factory()->create();
    $order = Order::factory()->create();

    $this->actingAs($customer, 'sanctum')
        ->getJson("/api/admin/orders/{$order->id}")
        ->assertForbidden();
}
```

- [ ] **Step 3: Add failing order-list filter tests**

Create three orders with different references, payment states, fulfilment states, and delivery methods. Assert each supported filter returns only the matching order, including case-insensitive reference/customer search:

```php
$this->actingAs($admin, 'sanctum')
    ->getJson('/api/admin/orders?payment_status=paid&delivery_method=pickup')
    ->assertOk()
    ->assertJsonCount(1, 'data')
    ->assertJsonPath('data.0.id', $paidPickup->id);

$this->actingAs($admin, 'sanctum')
    ->getJson('/api/admin/orders?search=aina')
    ->assertOk()
    ->assertJsonCount(1, 'data')
    ->assertJsonPath('data.0.customer.name', 'Aina Syazwani');
```

- [ ] **Step 4: Add failing dashboard summary assertions**

Add orders in two statuses and assert exact count and recent-order shape:

```php
$response->assertJsonPath('orders_by_status.pending', 1)
    ->assertJsonPath('orders_by_status.completed', 1)
    ->assertJsonCount(2, 'recent_orders')
    ->assertJsonStructure([
        'recent_orders' => [[
            'id', 'order_reference', 'customer_name', 'total',
            'payment_status', 'order_status', 'created_at',
        ]],
    ]);
```

- [ ] **Step 5: Run the focused backend tests and confirm RED**

Run:

```bash
cd backend
php artisan test tests/Feature/Product/Admin/ProductCrudTest.php tests/Feature/Order/Admin/OrderDetailTest.php tests/Feature/Order/Admin/OrderListTest.php tests/Feature/Admin/DashboardTest.php
```

Expected: failures for missing product/order detail routes and dashboard keys.

- [ ] **Step 6: Add protected detail routes and controller methods**

Inside the existing admin route group add:

```php
Route::get('/products/{product}', [AdminProductController::class, 'show']);
Route::get('/orders/{order}', [AdminOrderController::class, 'show']);
```

Add the product action:

```php
public function show(Product $product): ProductResource
{
    return new ProductResource($product->load(['category', 'images', 'variants']));
}
```

Update the order controller to load `user` and return `AdminOrderResource` for both list and detail:

```php
public function show(Order $order): AdminOrderResource
{
    return new AdminOrderResource(
        $order->load(['user', 'items.product', 'items.productVariant', 'deliveryZone'])
    );
}
```

- [ ] **Step 7: Add complete order list filters**

Build the list query with `user`, items, variants, and delivery zone eager-loaded. Apply exact enum filters and a grouped search across order reference, guest name, and registered-user name:

```php
if ($search = $request->string('search')->trim()->value()) {
    $query->where(function ($query) use ($search) {
        $query->whereRaw('LOWER(order_reference) LIKE ?', ['%'.mb_strtolower($search).'%'])
            ->orWhereRaw('LOWER(guest_name) LIKE ?', ['%'.mb_strtolower($search).'%'])
            ->orWhereHas('user', fn ($user) => $user
                ->whereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($search).'%']));
    });
}

foreach (['payment_status', 'order_status', 'delivery_method'] as $filter) {
    if ($value = $request->string($filter)->value()) {
        $query->where($filter, $value);
    }
}
```

Return `AdminOrderResource::collection($query->paginate($request->integer('per_page', 20)))`.

- [ ] **Step 8: Create the protected order resource**

Use the public resource as the safe base and merge protected customer fields:

```php
final class AdminOrderResource extends OrderResource
{
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'customer' => [
                'type' => $this->user_id ? 'registered' : 'guest',
                'id' => $this->user_id,
                'name' => $this->user?->name ?? $this->guest_name,
                'phone' => $this->guest_phone,
                'email' => $this->user?->email ?? $this->guest_email,
            ],
        ]);
    }
}
```

- [ ] **Step 9: Add accurate dashboard status counts and recent orders**

Return every enum key with zero defaults, then overlay database counts. Map the five latest orders to this exact shape:

```php
[
    'id' => $order->id,
    'order_reference' => $order->order_reference,
    'customer_name' => $order->user?->name ?? $order->guest_name ?? 'Guest',
    'total' => (float) $order->total,
    'payment_status' => $order->payment_status->value,
    'order_status' => $order->order_status->value,
    'created_at' => $order->created_at?->toIso8601String(),
]
```

- [ ] **Step 10: Run backend tests and confirm GREEN**

Run the focused command from Step 5, then:

```bash
php artisan test
```

Expected: all backend tests pass.

- [ ] **Step 11: Commit the backend contract slice**

```bash
git add backend/routes/api.php backend/app/Http/Controllers/Api/Admin backend/app/Http/Resources/AdminOrderResource.php backend/app/Services/DashboardService.php backend/tests/Feature
git commit -m "feat: complete admin dashboard API contracts"
```

---

### Task 2: Define tested admin types and pure behavior

**Files:**
- Create: `frontend/app/types/admin.ts`
- Create: `frontend/app/utils/admin.mjs`
- Create: `frontend/app/utils/admin.d.ts`
- Create: `frontend/build/admin.test.mjs`

**Interfaces:**
- Produces: `formatAdminCurrency`, `formatAdminDate`, `humanizeStatus`, `stockSummary`, `buildAdminQuery`, `progressIndex`.
- Produces: `AdminUser`, `AdminProduct`, `AdminOrder`, `DashboardSummary`, and Laravel pagination types.

- [ ] **Step 1: Write failing pure utility tests**

Test exact Malaysian currency, query omission, variant stock, and bounded progress:

```js
test('formats admin currency in Malaysian Ringgit', () => {
  assert.equal(formatAdminCurrency(86), 'RM86.00')
})

test('omits empty admin filters', () => {
  assert.deepEqual(buildAdminQuery({ search: '', status: 'active', page: 2 }), {
    status: 'active', page: 2,
  })
})

test('summarizes variant inventory instead of ignored base stock', () => {
  assert.equal(stockSummary({ stock: 999, variants: [{ stock: 4 }, { stock: 7 }] }), '11 across 2 variants')
})

test('keeps rejected orders outside successful progress', () => {
  assert.equal(progressIndex('rejected'), -1)
  assert.equal(progressIndex('packing'), 3)
})
```

- [ ] **Step 2: Run the test and confirm RED**

Run `cd frontend && node --test build/admin.test.mjs`.

Expected: module-not-found failure for `app/utils/admin.mjs`.

- [ ] **Step 3: Implement pure helpers**

Use the exact successful sequence and filter behavior:

```js
export const fulfilmentStatuses = [
  'pending', 'preparing', 'baking', 'packing', 'out_for_delivery', 'completed',
]

export function progressIndex(status) {
  return fulfilmentStatuses.indexOf(status)
}

export function buildAdminQuery(filters) {
  return Object.fromEntries(Object.entries(filters).filter(([, value]) => (
    value !== '' && value !== null && value !== undefined
  )))
}
```

Implement currency with `Intl.NumberFormat('en-MY', { style: 'currency', currency: 'MYR', currencyDisplay: 'narrowSymbol' })` and normalize its output to the required `RM` label.

- [ ] **Step 4: Define TypeScript contracts matching Laravel resources**

Include exact unions:

```ts
export type UserRole = 'admin' | 'customer'
export type ProductStatus = 'active' | 'inactive'
export type PaymentStatus = 'unpaid' | 'paid' | 'refunded'
export type OrderStatus = 'pending' | 'preparing' | 'baking' | 'packing' | 'out_for_delivery' | 'completed' | 'rejected' | 'cancelled'
```

`AdminOrder` must include `allowed_next_statuses: OrderStatus[]`, `customer`, `items`, delivery fields, totals, and timestamps. `DashboardSummary` must use the Task 1 keys.

- [ ] **Step 5: Run tests and confirm GREEN**

Run `npm test` from `frontend`.

Expected: existing storefront/router tests and new admin utility tests all pass.

- [ ] **Step 6: Commit**

```bash
git add frontend/app/types/admin.ts frontend/app/utils/admin.mjs frontend/app/utils/admin.d.ts frontend/build/admin.test.mjs
git commit -m "test: define admin dashboard domain behavior"
```

---

### Task 3: Build the secure Nuxt-to-Laravel session boundary

**Files:**
- Modify: `frontend/nuxt.config.ts`
- Create: `frontend/server/utils/adminProxyPolicy.mjs`
- Create: `frontend/server/utils/adminSession.ts`
- Create: `frontend/server/utils/laravelAdmin.ts`
- Create: `frontend/server/api/admin-auth/login.post.ts`
- Create: `frontend/server/api/admin-auth/session.get.ts`
- Create: `frontend/server/api/admin-auth/logout.post.ts`
- Create: `frontend/server/api/admin-proxy/[...path].ts`
- Create: `frontend/build/admin-server.test.mjs`

**Interfaces:**
- Consumes: Laravel `/api/login`, `/api/me`, `/api/logout`, and protected routes.
- Produces: same-origin `/api/admin-auth/*` and `/api/admin-proxy/*`.
- Produces: `isAllowedAdminProxy(method, path): boolean`.

- [ ] **Step 1: Write failing proxy-policy and secret-exposure tests**

```js
test('allowlists only supported Laravel paths and methods', () => {
  assert.equal(isAllowedAdminProxy('GET', 'dashboard'), true)
  assert.equal(isAllowedAdminProxy('PUT', 'products/12'), true)
  assert.equal(isAllowedAdminProxy('PATCH', 'orders/7/status'), true)
  assert.equal(isAllowedAdminProxy('POST', 'orders/7/status'), false)
  assert.equal(isAllowedAdminProxy('GET', 'https://example.com'), false)
})

test('session implementation uses an HttpOnly cookie', async () => {
  const source = await readFile(new URL('../server/utils/adminSession.ts', import.meta.url), 'utf8')
  assert.match(source, /httpOnly:\s*true/)
  assert.doesNotMatch(source, /localStorage/)
})
```

- [ ] **Step 2: Run and confirm RED**

Run `node --test build/admin-server.test.mjs`.

Expected: missing policy/session modules.

- [ ] **Step 3: Implement a closed allowlist**

Allow only these method/path pairs:

```js
const rules = [
  ['GET', /^dashboard$/],
  ['GET', /^categories$/],
  ['GET', /^products$/],
  ['POST', /^products$/],
  ['GET', /^products\/\d+$/],
  ['PUT', /^products\/\d+$/],
  ['DELETE', /^products\/\d+$/],
  ['GET', /^orders$/],
  ['GET', /^orders\/\d+$/],
  ['PATCH', /^orders\/\d+\/status$/],
]
```

Normalize by stripping leading/trailing slashes and reject paths containing a scheme, backslash, `..`, or encoded slash.

- [ ] **Step 4: Add private runtime configuration and cookie helpers**

Add `runtimeConfig.apiBase` using `NUXT_API_BASE`, defaulting to `http://127.0.0.1:8000/api`; keep `runtimeConfig.public.apiBase` for the storefront.

Use cookie name `llk_admin_token` with:

```ts
{
  httpOnly: true,
  secure: !import.meta.dev,
  sameSite: 'lax',
  path: '/',
  maxAge: remember ? 60 * 60 * 24 * 14 : undefined,
}
```

- [ ] **Step 5: Implement Laravel forwarding and normalized errors**

`requestLaravelAdmin<T>(event, path, options)` must:

- Read the private runtime base.
- Read the token only on the server.
- Attach `Accept: application/json` and `Authorization: Bearer <token>`.
- Forward query and JSON body.
- Preserve Laravel `401`, `403`, `404`, `422`, and `429` status codes and payloads through `createError`.
- Delete the token cookie when Laravel returns `401`.

- [ ] **Step 6: Implement login, session, and logout handlers**

Login accepts `{ email, password, remember }`, calls `/login`, verifies `response.user.role === 'admin'`, revokes a customer token with `/logout`, stores only the valid admin token in the cookie, and returns `{ user }`.

Session returns `{ user }` from `/me`. Logout attempts Laravel `/logout` and always clears the cookie in `finally`.

- [ ] **Step 7: Implement the allowlisted proxy**

Read the catch-all path, verify it with `isAllowedAdminProxy`, then forward to `/admin/<path>` except `categories`, which forwards to `/categories`. Pass query parameters for GET and the parsed body for POST/PUT/PATCH/DELETE.

- [ ] **Step 8: Run tests and build**

Run:

```bash
npm test
npm run build
```

Expected: all tests pass and Nitro compiles every server handler.

- [ ] **Step 9: Commit**

```bash
git add frontend/nuxt.config.ts frontend/server frontend/build/admin-server.test.mjs
git commit -m "feat: add secure admin API session proxy"
```

---

### Task 4: Create admin authentication and layout foundation

**Files:**
- Modify: `frontend/nuxt.config.ts`
- Create: `frontend/app/assets/css/admin.css`
- Create: `frontend/app/stores/adminAuth.ts`
- Create: `frontend/app/composables/useAdminApi.ts`
- Create: `frontend/app/middleware/admin-auth.ts`
- Create: `frontend/app/layouts/admin-auth.vue`
- Create: `frontend/app/layouts/admin.vue`
- Create: `frontend/app/components/admin/AdminBrand.vue`
- Create: `frontend/app/components/admin/AdminSidebar.vue`
- Create: `frontend/app/components/admin/AdminHeader.vue`
- Create: `frontend/app/pages/admin/login.vue`
- Create: `frontend/build/admin-structure.test.mjs`

**Interfaces:**
- Consumes: `/api/admin-auth/login`, `/session`, `/logout`.
- Produces: `useAdminAuthStore()` with `user`, `checked`, `isAuthenticated`, `checkSession`, `login`, `logout`.
- Produces: `useAdminApi<T>(path, options)`.

- [ ] **Step 1: Write failing structural security tests**

Assert that the admin layout declares `admin-auth` middleware behavior, login uses `admin-auth` layout, and the store source contains no token property or `localStorage` call.

- [ ] **Step 2: Run and confirm RED**

Run `node --test build/admin-structure.test.mjs`.

Expected: missing store, middleware, layouts, and login page.

- [ ] **Step 3: Implement safe auth state**

The Pinia state contains only:

```ts
const user = ref<AdminUser | null>(null)
const checked = ref(false)
const isAuthenticated = computed(() => user.value?.role === 'admin')
```

`checkSession()` deduplicates simultaneous requests, sets the safe user on success, clears it on `401`, and always marks `checked`. `login()` sends credentials to the Nuxt handler. `logout()` calls the Nuxt handler, clears state, and navigates to `/admin/login`.

- [ ] **Step 4: Implement route middleware**

For every protected admin page, await `checkSession()`. Redirect unauthenticated users to `/admin/login?redirect=<encoded fullPath>`. The login page has no auth middleware and redirects already authenticated admins to `/admin`.

- [ ] **Step 5: Create the two layouts**

`admin-auth.vue` renders a centered split card on porcelain. `admin.vue` renders `AdminSidebar`, `AdminHeader`, and the page slot; it owns `mobileNavOpen`, closes the drawer after navigation, and restores focus to the menu button.

- [ ] **Step 6: Build the reference-matched login screen**

Use the existing `/images/products/choc-chip-crunch-temp.png` on the image panel. Include labelled email/password fields, password visibility, remember checkbox, adjacent Laravel validation errors, disabled/loading Sign in state, and no password-recovery link.

- [ ] **Step 7: Add admin tokens and responsive shell CSS**

Import `~/assets/css/admin.css` after `main.css`. Define the approved porcelain, paper, cocoa, terracotta, peach, sage, amber, and rose variables under `.admin-surface`. Add fixed desktop sidebar, mobile drawer, dimming scrim, 44px controls, visible focus, and reduced-motion rules.

- [ ] **Step 8: Run tests and build, then commit**

Run `npm test && npm run build`.

```bash
git add frontend/app/assets/css/admin.css frontend/app/stores/adminAuth.ts frontend/app/composables/useAdminApi.ts frontend/app/middleware frontend/app/layouts/admin*.vue frontend/app/components/admin frontend/app/pages/admin/login.vue frontend/nuxt.config.ts frontend/build/admin-structure.test.mjs
git commit -m "feat: add secure branded admin shell"
```

---

### Task 5: Add shared admin primitives and placeholder routes

**Files:**
- Create: `frontend/app/components/admin/AdminStatusBadge.vue`
- Create: `frontend/app/components/admin/AdminEmptyState.vue`
- Create: `frontend/app/components/admin/AdminConfirmDialog.vue`
- Create: `frontend/app/components/admin/AdminPlaceholder.vue`
- Create: five placeholder page files under `frontend/app/pages/admin/`
- Modify: `frontend/build/admin-structure.test.mjs`

**Interfaces:**
- Produces: reusable status, empty, confirm, and placeholder components.

- [ ] **Step 1: Extend structural tests and confirm RED**

Assert all five placeholder routes exist and each renders `AdminPlaceholder` with its correct module title.

- [ ] **Step 2: Implement shared primitives**

`AdminStatusBadge` accepts `status: string`; `AdminEmptyState` accepts `title`, `description`, and optional action slot; `AdminConfirmDialog` accepts `open`, `title`, `description`, `confirmLabel`, `busy`, emits `confirm`/`close`, traps focus, closes on Escape, and returns focus; `AdminPlaceholder` accepts `title` and `description` and links to `/admin`.

- [ ] **Step 3: Implement placeholder routes**

Each page uses `layout: 'admin'`, `middleware: 'admin-auth'`, a unique title, and concrete copy explaining which backend connection is required. Do not call any composable or API from these pages.

- [ ] **Step 4: Run tests/build and commit**

Run `npm test && npm run build`.

```bash
git add frontend/app/components/admin frontend/app/pages/admin/{customers,delivery-zones,promotions,reports,settings}.vue frontend/build/admin-structure.test.mjs
git commit -m "feat: add admin placeholders and shared states"
```

---

### Task 6: Build the functional dashboard

**Files:**
- Create: `frontend/app/composables/useAdminDashboard.ts`
- Create: `frontend/app/components/admin/dashboard/AdminMetricCard.vue`
- Create: `frontend/app/components/admin/dashboard/AdminOrderOverview.vue`
- Create: `frontend/app/components/admin/dashboard/AdminLowStock.vue`
- Create: `frontend/app/components/admin/dashboard/AdminRecentOrders.vue`
- Create: `frontend/app/pages/admin/index.vue`
- Modify: `frontend/build/admin-structure.test.mjs`

**Interfaces:**
- Consumes: `GET /api/admin-proxy/dashboard` returning `DashboardSummary`.

- [ ] **Step 1: Add failing dashboard structure and formatter assertions**

Assert the page uses real dashboard keys and contains no hard-coded trend percentages or chart data.

- [ ] **Step 2: Run and confirm RED**

Run the focused admin structure test.

- [ ] **Step 3: Implement dashboard data composable**

Use `useAsyncData('admin-dashboard', () => useAdminApi<DashboardSummary>('dashboard'))`, expose `refresh`, and normalize network errors into actionable page copy.

- [ ] **Step 4: Implement dashboard cards and panels**

Render the four supplied metrics, all order status counts, variant-aware low-stock rows, recent orders, and quick links to `/admin/products/new` and `/admin/orders?order_status=pending`. Use honest empty states when arrays are empty; omit trend percentages.

- [ ] **Step 5: Run tests/build and commit**

Run `npm test && npm run build`.

```bash
git add frontend/app/composables/useAdminDashboard.ts frontend/app/components/admin/dashboard frontend/app/pages/admin/index.vue frontend/build/admin-structure.test.mjs
git commit -m "feat: build functional admin dashboard"
```

---

### Task 7: Build product list, filtering, and deletion

**Files:**
- Create: `frontend/app/composables/useAdminProducts.ts`
- Create: `frontend/app/components/admin/products/AdminProductFilters.vue`
- Create: `frontend/app/components/admin/products/AdminProductTable.vue`
- Create: `frontend/app/pages/admin/products/index.vue`
- Modify: `frontend/build/admin.test.mjs`

**Interfaces:**
- Consumes: `GET/DELETE /api/admin-proxy/products[/id]` and `GET /api/admin-proxy/categories`.

- [ ] **Step 1: Add failing filter-query and stock display cases**

Cover search/category/status/page query mapping, fallback image behavior, base stock, and variant stock summary.

- [ ] **Step 2: Run and confirm RED**

Run `node --test build/admin.test.mjs` and confirm the new case fails for missing mapping behavior.

- [ ] **Step 3: Implement query-driven product loading**

Read filters from `useRoute().query`, debounce search by 250ms, update query with `navigateTo`, and call `GET products` with `search`, `category`, `status`, `page`, and `per_page: 20`. Use Laravel pagination metadata for controls.

- [ ] **Step 4: Implement responsive product rows**

Desktop columns are Product, Category, Variants, Stock, Status, and Actions. Mobile rows become labelled cards. Images use the first API image and fall back to `/images/products/cookie-placeholder.svg`.

- [ ] **Step 5: Implement deletion confirmation**

Open `AdminConfirmDialog` with the product name, issue `DELETE products/{id}`, refresh on success, keep the dialog open with a specific error on failure, and prevent repeat submissions while busy.

- [ ] **Step 6: Run tests/build and commit**

Run `npm test && npm run build`.

```bash
git add frontend/app/composables/useAdminProducts.ts frontend/app/components/admin/products frontend/app/pages/admin/products/index.vue frontend/build/admin.test.mjs
git commit -m "feat: add admin product management list"
```

---

### Task 8: Build product create and edit forms

**Files:**
- Create: `frontend/app/components/admin/products/AdminProductForm.vue`
- Create: `frontend/app/components/admin/products/AdminVariantEditor.vue`
- Create: `frontend/app/components/admin/products/AdminImageUrlEditor.vue`
- Create: `frontend/app/pages/admin/products/new.vue`
- Create: `frontend/app/pages/admin/products/[id]/edit.vue`
- Modify: `frontend/app/utils/admin.mjs`
- Modify: `frontend/app/utils/admin.d.ts`
- Modify: `frontend/build/admin.test.mjs`

**Interfaces:**
- Produces: `toProductPayload(form): StoreProductPayload`.
- Consumes: POST/PUT product proxy endpoints and GET categories/product detail.

- [ ] **Step 1: Write failing payload-mapping tests**

Assert numeric coercion, blank nullable fields, ordered image URLs, and exact variant objects:

```js
assert.deepEqual(toProductPayload(form), {
  category_id: 2,
  name: 'Choc Chip Crunch',
  description: 'Soft-centred cookie',
  ingredients: 'Flour, butter, chocolate',
  allergens: 'Gluten, milk',
  price: 25,
  stock: 0,
  status: 'active',
  images: ['https://images.example.test/choc-chip.jpg'],
  variants: [{ label: '300g (12 pcs)', price: 25, stock: 12 }],
})
```

- [ ] **Step 2: Run and confirm RED, then implement mapping**

Run the focused test. Implement `toProductPayload` in `admin.mjs`, rerun, and confirm GREEN.

- [ ] **Step 3: Implement one shared form**

Expose props `initialProduct?: AdminProduct`, `categories: Category[]`, `busy: boolean`, `serverErrors: Record<string,string[]>`, and emit `save` with the mapped payload. Sections are Basic info, Variants, and Images. Show that base stock is ignored whenever at least one variant exists.

- [ ] **Step 4: Implement dynamic variant and image editors**

Variant rows contain label, price, stock, reorder, and remove controls. Image rows contain HTTPS URL, preview, reorder, and remove controls. Invalid previews retain the URL and show “Image could not be loaded.”

- [ ] **Step 5: Implement create/edit pages**

Create posts to `products`; edit loads `products/{id}` and puts to the same path. Both fetch categories, map `422` field errors, show non-validation failures globally, disable duplicate saves, warn before leaving with dirty changes, and navigate to `/admin/products` after success.

- [ ] **Step 6: Run tests/build and commit**

Run `npm test && npm run build`.

```bash
git add frontend/app/components/admin/products frontend/app/pages/admin/products frontend/app/utils/admin.mjs frontend/app/utils/admin.d.ts frontend/build/admin.test.mjs
git commit -m "feat: add admin product editor"
```

---

### Task 9: Build order list and valid status workflow

**Files:**
- Create: `frontend/app/composables/useAdminOrders.ts`
- Create: `frontend/app/components/admin/orders/AdminOrderFilters.vue`
- Create: `frontend/app/components/admin/orders/AdminOrderTable.vue`
- Create: `frontend/app/components/admin/orders/AdminOrderTimeline.vue`
- Create: `frontend/app/components/admin/orders/AdminOrderItems.vue`
- Create: `frontend/app/components/admin/orders/AdminOrderStatusForm.vue`
- Create: `frontend/app/pages/admin/orders/index.vue`
- Create: `frontend/app/pages/admin/orders/[id]/index.vue`
- Modify: `frontend/build/admin.test.mjs`

**Interfaces:**
- Consumes: GET orders/order detail and PATCH `orders/{id}/status`.
- Consumes: backend `allowed_next_statuses`; never calculates permitted mutations independently.

- [ ] **Step 1: Add failing tests for status labels and action visibility**

Assert `out_for_delivery` displays as “Out for delivery,” terminal states return no actions, and payment `paid` never advances fulfilment progress.

- [ ] **Step 2: Run and confirm RED, then complete pure helpers**

Run the focused admin utility test, implement missing helpers, and rerun to GREEN.

- [ ] **Step 3: Implement query-driven order list**

Use filters `search`, `payment_status`, `order_status`, `delivery_method`, `page`, and `per_page: 20`. Render reference, customer, payment, fulfilment, total, date, and detail action with responsive cards on mobile.

- [ ] **Step 4: Implement order detail**

Load by numeric ID and render protected customer data, fulfilment address/date/method/notes, order items and variants, subtotal, delivery fee, total, separate payment badge, and the successful fulfilment timeline.

- [ ] **Step 5: Implement status updates**

Populate the selector exclusively from `order.allowed_next_statuses`. Require confirmation for `rejected` or `cancelled`; PATCH `{ order_status }`; display Laravel `422` transition errors; replace local order data with the returned resource on success.

- [ ] **Step 6: Run tests/build and commit**

Run `npm test && npm run build`.

```bash
git add frontend/app/composables/useAdminOrders.ts frontend/app/components/admin/orders frontend/app/pages/admin/orders frontend/build/admin.test.mjs
git commit -m "feat: add admin order fulfilment workflow"
```

---

### Task 10: Add print invoice and finish responsive reference matching

**Files:**
- Create: `frontend/app/pages/admin/orders/[id]/print.vue`
- Create: `frontend/app/components/admin/orders/AdminInvoice.vue`
- Modify: `frontend/app/assets/css/admin.css`
- Modify: `frontend/app/components/admin/AdminSidebar.vue`
- Modify: `frontend/app/components/admin/AdminHeader.vue`
- Modify: `frontend/build/admin-structure.test.mjs`

**Interfaces:**
- Consumes: protected order detail.
- Produces: print-safe semantic invoice and native `window.print()` action.

- [ ] **Step 1: Add failing print-structure assertions**

Assert the print page contains the business name, order reference, customer block, line-item table, subtotal, delivery fee, total, and a print button wired to `window.print()`.

- [ ] **Step 2: Run and confirm RED**

Run the focused structure test.

- [ ] **Step 3: Implement semantic invoice**

Use `<header>`, `<address>`, `<table>`, `<tfoot>`, and `<time>` elements. Never infer payment dates or transaction IDs that the API does not provide; omit absent fields.

- [ ] **Step 4: Add print and responsive CSS**

Under `@media print`, set white background, hide `.admin-print-hidden`, remove navigation/shadows, use A4-friendly margins, prevent item rows splitting, and preserve dark readable text. At 375px verify drawer width, stacked cards, full-width form actions, and no body-level horizontal overflow.

- [ ] **Step 5: Run tests/build and commit**

Run `npm test && npm run build`.

```bash
git add frontend/app/pages/admin/orders frontend/app/components/admin/orders/AdminInvoice.vue frontend/app/assets/css/admin.css frontend/app/components/admin/AdminSidebar.vue frontend/app/components/admin/AdminHeader.vue frontend/build/admin-structure.test.mjs
git commit -m "feat: add printable responsive admin orders"
```

---

### Task 11: Full verification and visual critique

**Files:**
- Modify only files revealed by concrete verification failures.

**Interfaces:**
- Validates the complete admin release and existing storefront.

- [ ] **Step 1: Run all automated verification**

```bash
cd backend && php artisan test
cd ../frontend && npm test
npm run build
```

Expected: all Laravel tests pass, all Node tests pass, and Nuxt production build exits zero without hydration/compiler errors.

- [ ] **Step 2: Start both applications and verify authority**

Start Laravel at `127.0.0.1:8000` and Nuxt at `127.0.0.1:3000`. Verify guest redirection, admin login, customer-role rejection, refresh persistence, logout revocation, and direct Laravel admin requests returning `401`/`403` correctly.

- [ ] **Step 3: Verify functional journeys**

Check dashboard data, product search/filter/create/edit/delete, order filters/detail/allowed transition, terminal-state behavior, placeholder navigation, and print preview.

- [ ] **Step 4: Critique against the reference at desktop and 375px**

Capture desktop and mobile screenshots. Compare sidebar width, panel density, typography, card borders, status stamps, table rhythm, login split, drawer overlay, and print output. Remove decorative elements that do not support the bakery-ledger direction.

- [ ] **Step 5: Confirm token and privacy boundaries**

Verify no `llk_admin_token` value appears in `localStorage`, Pinia devtools, page HTML, or network response bodies; verify public order tracking does not expose customer contact fields.

- [ ] **Step 6: Review working tree and commit fixes**

Use `git status --short` and `git diff --check`. Stage only admin-related verification fixes, then:

```bash
git commit -m "fix: polish and verify admin dashboard"
```

Do not stage unrelated pre-existing storefront or backend files.

---

## Completion criteria

- All acceptance criteria in `docs/superpowers/specs/2026-07-22-admin-dashboard-design.md` are satisfied.
- Core screens closely replicate the supplied reference without invented business data.
- Placeholder modules are navigable and honest.
- Laravel tests, frontend tests, and production build pass.
- Admin authority and customer privacy remain enforced server-side.
- Desktop, mobile drawer, Safari, and print-preview checks complete without console or hydration errors.
