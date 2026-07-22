import type { AdminProduct, PaginatedResponse } from '~/types/admin'

export function useAdminProducts(query: Ref<Record<string, unknown>>) {
  return useAsyncData<PaginatedResponse<AdminProduct>>(
    'admin-products',
    () => useAdminApi<PaginatedResponse<AdminProduct>>('products', { query: query.value }),
    { watch: [query] },
  )
}
