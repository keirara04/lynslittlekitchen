import type { AdminDeliveryZone } from '~/types/admin'

export function useAdminDeliveryZones() {
  return useAsyncData('admin-delivery-zones', () => useAdminApi<{ data: AdminDeliveryZone[] }>('delivery-zones'))
}
