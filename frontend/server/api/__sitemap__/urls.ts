import type { SitemapUrlInput } from '#sitemap/types'

interface ProductSlug {
  slug: string
}

interface PaginatedSlugs {
  data: ProductSlug[]
}

export default defineEventHandler(async (event): Promise<SitemapUrlInput[]> => {
  const config = useRuntimeConfig(event)

  try {
    const response = await $fetch<PaginatedSlugs>('/products', {
      baseURL: config.apiBase,
      query: { per_page: 50 },
    })

    return response.data.map(product => ({ loc: `/shop/${product.slug}` }))
  }
  catch {
    return []
  }
})
