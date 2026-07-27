import type { CategoriesResponse } from '~/types/admin'

export function useAdminCategories() {
  return useAsyncData('admin-product-categories', () => useAdminApi<CategoriesResponse>('categories'))
}
