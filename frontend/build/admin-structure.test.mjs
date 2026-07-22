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

test('future admin modules render intentional placeholders without API calls', async () => {
  const routes = {
    'customers.vue': 'Customers',
    'delivery-zones.vue': 'Delivery Zones',
    'promotions.vue': 'Promotions',
    'reports.vue': 'Reports',
    'settings.vue': 'Settings',
  }

  for (const [file, title] of Object.entries(routes)) {
    const content = await source(`pages/admin/${file}`)
    assert.match(content, /AdminPlaceholder/)
    assert.match(content, new RegExp(`title="${title}"`))
    assert.doesNotMatch(content, /useAdminApi|\$fetch|useFetch|useAsyncData/)
  }
})

test('shared admin dialogs and status states are accessible', async () => {
  const dialog = await source('components/admin/AdminConfirmDialog.vue')
  assert.match(dialog, /role="dialog"/)
  assert.match(dialog, /aria-modal="true"/)
  assert.match(dialog, /Escape/)

  const status = await source('components/admin/AdminStatusBadge.vue')
  assert.match(status, /humanizeStatus/)
})

test('dashboard renders real API metrics without invented trends', async () => {
  const content = await source('pages/admin/index.vue')
  for (const key of ['todays_sales', 'total_orders', 'monthly_revenue', 'best_selling_product', 'orders_by_status', 'low_stock_products', 'recent_orders']) {
    assert.match(content, new RegExp(key))
  }
  assert.doesNotMatch(content, /yesterday|last month|trendPercentage|chartData/)
  assert.match(content, /middleware:\s*'admin-auth'/)
})

test('print route renders a complete semantic invoice', async () => {
  const page = await source('pages/admin/orders/[id]/print.vue')
  const invoice = await source('components/admin/orders/AdminInvoice.vue')
  assert.match(page, /window\.print\(\)/)
  assert.match(page, /AdminInvoice/)
  for (const text of ['Lyn’s Little Kitchen', 'order_reference', 'Customer', 'Subtotal', 'Delivery fee', 'Total']) {
    assert.match(invoice, new RegExp(text))
  }
  assert.match(invoice, /<table/)
  assert.match(invoice, /<tfoot/)
  assert.match(invoice, /<address/)
})
