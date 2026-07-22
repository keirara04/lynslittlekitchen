<script setup lang="ts">
import AdminLowStock from '~/components/admin/dashboard/AdminLowStock.vue'
import AdminMetricCard from '~/components/admin/dashboard/AdminMetricCard.vue'
import AdminOrderOverview from '~/components/admin/dashboard/AdminOrderOverview.vue'
import AdminRecentOrders from '~/components/admin/dashboard/AdminRecentOrders.vue'
import { formatAdminCurrency } from '~/utils/admin.mjs'

definePageMeta({ layout: 'admin', middleware: 'admin-auth' })

const auth = useAdminAuthStore()
const { data: dashboard, pending, error, refresh } = await useAdminDashboard()

useSeoMeta({ title: "Dashboard | Lyn's Admin", robots: 'noindex, nofollow' })
</script>

<template>
  <div class="admin-page admin-dashboard">
    <header class="admin-page-heading">
      <div>
        <p class="admin-kicker">Kitchen ledger</p>
        <h1>Dashboard</h1>
        <p>Welcome back, {{ auth.user?.name || 'Admin' }}.</p>
      </div>
    </header>

    <div v-if="pending" class="admin-dashboard-loading" aria-label="Loading dashboard">
      <span v-for="index in 7" :key="index" />
    </div>

    <AdminEmptyState v-else-if="error" title="Dashboard could not be loaded" description="Check that the Laravel API is running, then try again.">
      <button class="admin-button admin-button--primary" type="button" @click="refresh()">Try again</button>
    </AdminEmptyState>

    <template v-else-if="dashboard">
      <section class="admin-metric-grid">
        <AdminMetricCard label="Today’s Paid Sales" :value="formatAdminCurrency(dashboard.todays_sales)" note="Paid orders today" accent="sage" />
        <AdminMetricCard label="Total Orders" :value="dashboard.total_orders" note="All recorded orders" />
        <AdminMetricCard label="Monthly Paid Revenue" :value="formatAdminCurrency(dashboard.monthly_revenue)" note="Current calendar month" accent="amber" />
        <AdminMetricCard label="Best Selling (Paid)" :value="dashboard.best_selling_product?.product || 'No paid sales yet'" :note="dashboard.best_selling_product ? `${dashboard.best_selling_product.total_sold} packs sold` : 'Waiting for the first sale'" />
      </section>

      <section class="admin-dashboard-grid">
        <AdminOrderOverview :counts="dashboard.orders_by_status" />
        <AdminLowStock :alerts="dashboard.low_stock_products" />
        <AdminRecentOrders :orders="dashboard.recent_orders" />
      </section>

      <section class="admin-quick-actions admin-panel">
        <header><h2>Quick Actions</h2></header>
        <div>
          <NuxtLink to="/admin/products/new" class="admin-button admin-button--secondary">＋ New Product</NuxtLink>
          <NuxtLink to="/admin/orders?order_status=pending" class="admin-button admin-button--secondary">View Pending Orders</NuxtLink>
        </div>
      </section>
    </template>
  </div>
</template>
