import type { AdminCustomer, PaginatedResponse } from '~/types/admin'

export function useAdminCustomers(query: Ref<Record<string, unknown>>) {
  return useAsyncData<PaginatedResponse<AdminCustomer>>(
    'admin-customers',
    () => useAdminApi<PaginatedResponse<AdminCustomer>>('customers', { query: query.value }),
    { watch: [query] },
  )
}
