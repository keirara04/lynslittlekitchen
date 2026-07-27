import type { AdminSettings } from '~/types/admin'

export function useAdminSettings() {
  return useAsyncData('admin-settings', () => useAdminApi<{ data: AdminSettings }>('settings'))
}
