import { fallbackProducts } from '~/data/products'
import type { PaginatedProducts, Product } from '~/types/catalog'
import { resolveProductImage } from '~/utils/storefront.mjs'

function decorateProduct(product: Product): Product {
  return {
    ...product,
    price: Number(product.price),
    images: [{ url: resolveProductImage(product.slug, product.images) }],
    variants: (product.variants || []).map(variant => ({
      ...variant,
      price: Number(variant.price),
    })),
  }
}

export function useCatalog() {
  const { locale } = useI18n()

  const result = useAsyncData<Product[]>('storefront-products', async () => {
    try {
      const response = await useApiFetch<PaginatedProducts>('/products', {
        query: { per_page: 50 },
      })
      return response.data.map(decorateProduct)
    }
    catch {
      return fallbackProducts.map(decorateProduct)
    }
  }, { default: () => fallbackProducts.map(decorateProduct), watch: [locale] })

  return {
    ...result,
    findBySlug: (slug: string) => result.data.value.find(product => product.slug === slug),
  }
}
