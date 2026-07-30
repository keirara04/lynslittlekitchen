export interface Category {
  id?: number
  name: string
  name_ms?: string | null
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
  name_ms?: string | null
  slug: string
  description: string
  description_ms?: string | null
  ingredients: string
  ingredients_ms?: string | null
  allergens: string
  allergens_ms?: string | null
  price: number
  stock: number
  in_stock: boolean
  status: string
  is_signature: boolean
  category: Category
  images: ProductImage[]
  variants: ProductVariant[]
}

export interface PaginatedProducts {
  data: Product[]
  meta?: Record<string, unknown>
}

export interface PublicOrder {
  id: number
  order_reference: string
  total: number
  delivery_fee: number
  payment_status: 'unpaid' | 'paid' | 'refunded'
  payment_proof_url: string | null
  payment_proof_submitted_at: string | null
  paid_at: string | null
  order_status: string
  allowed_next_statuses: string[]
  delivery_method: 'delivery' | 'pickup'
  delivery_address: string | null
  delivery_date: string | null
  notes: string | null
  created_at: string
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
