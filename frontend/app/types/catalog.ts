export interface Category {
  id?: number
  name: string
  slug: string
}

export interface ProductImage {
  id?: number
  url: string
  sort_order?: number
}

export interface ProductVariant {
  id: number
  label: string
  price: number
  stock: number
  sort_order?: number
}

export interface Product {
  id: number
  name: string
  slug: string
  description: string
  ingredients: string
  allergens: string
  price: number
  stock: number
  in_stock: boolean
  status: string
  category: Category
  images: ProductImage[]
  variants: ProductVariant[]
}

export interface PaginatedProducts {
  data: Product[]
  meta?: Record<string, unknown>
}

export interface CartLine {
  key: string
  productId: number
  slug: string
  name: string
  image: string
  variantId: number | null
  variantLabel: string
  unitPrice: number
  quantity: number
}
