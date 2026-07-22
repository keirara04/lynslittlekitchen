import type { DashboardSummary } from '~/types/admin'

export function useAdminDashboard() {
  return useAsyncData<DashboardSummary>(
    'admin-dashboard',
    () => useAdminApi<DashboardSummary>('dashboard'),
  )
}
