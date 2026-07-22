import type { AdminOrder, PaginatedResponse } from '~/types/admin'

export function useAdminOrders(query: Ref<Record<string, unknown>>) {
  return useAsyncData<PaginatedResponse<AdminOrder>>(
    'admin-orders',
    () => useAdminApi<PaginatedResponse<AdminOrder>>('orders', { query: query.value }),
    { watch: [query] },
  )
}
