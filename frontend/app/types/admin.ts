import type { Category, Product, ProductImage, ProductVariant } from './catalog'

export type UserRole = 'admin' | 'customer'
export type ProductStatus = 'active' | 'inactive'
export type PaymentStatus = 'unpaid' | 'paid' | 'refunded'
export type DeliveryMethod = 'delivery' | 'pickup'
export type OrderStatus =
  | 'pending'
  | 'preparing'
  | 'baking'
  | 'packing'
  | 'out_for_delivery'
  | 'completed'
  | 'rejected'
  | 'cancelled'

export interface AdminUser {
  id: number
  name: string
  email: string
  role: UserRole
}

export interface AdminProduct extends Omit<Product, 'status'> {
  status: ProductStatus
}

export interface AdminCustomerSummary {
  type: 'guest' | 'registered'
  id: number | null
  name: string | null
  phone: string | null
  email: string | null
}

export interface AdminCustomer {
  id: number | string
  name: string | null
  email: string | null
  phone: string | null
  orders_count: number
  total_spent: number
  last_order_at: string | null
  status: 'active' | 'inactive'
}

export interface AdminSettings {
  id: number
  store_name: string | null
  business_registration_no: string | null
  business_type: string | null
  established_since: string | null
  address_line1: string | null
  address_line2: string | null
  postcode: string | null
  city: string | null
  state: string | null

  contact_phone: string | null
  contact_email: string | null
  operating_hours: string | null

  min_order_amount: number | null
  lead_time_days: number | null
  order_cutoff_time: string | null
  allow_pickup: boolean
  allow_delivery: boolean

  bank_name: string | null
  bank_account_name: string | null
  bank_account_number: string | null
  duitnow_id: string | null

  alert_email: string | null
  new_order_email_enabled: boolean
  low_stock_threshold: number | null

  featured_hero_product_id: number | null
  featured_banner_product_id: number | null
}

export interface AdminDeliveryZone {
  id: number
  name: string
  price: number
}

export interface AdminOrderItem {
  id: number
  product: AdminProduct
  product_variant_id: number | null
  variant_label: string | null
  quantity: number
  price: number
  subtotal: number
}

export interface AdminOrder {
  id: number
  order_reference: string
  customer: AdminCustomerSummary
  total: number
  delivery_fee: number
  payment_status: PaymentStatus
  payment_proof_url: string | null
  payment_proof_submitted_at: string | null
  paid_at: string | null
  order_status: OrderStatus
  allowed_next_statuses: OrderStatus[]
  delivery_method: DeliveryMethod
  delivery_address: string | null
  delivery_date: string | null
  notes: string | null
  delivery_zone: AdminDeliveryZone | null
  items: AdminOrderItem[]
  created_at: string
}

export interface DashboardLowStockAlert {
  product: string
  variant_label: string | null
  stock: number
}

export interface DashboardRecentOrder {
  id: number
  order_reference: string
  customer_name: string
  total: number
  payment_status: PaymentStatus
  order_status: OrderStatus
  created_at: string
}

export interface DashboardSummary {
  todays_sales: number
  total_orders: number
  monthly_revenue: number
  best_selling_product: { product: string; total_sold: number } | null
  low_stock_products: DashboardLowStockAlert[]
  orders_by_status: Record<OrderStatus, number>
  recent_orders: DashboardRecentOrder[]
}

export interface PaginationMeta {
  current_page: number
  from: number | null
  last_page: number
  per_page: number
  to: number | null
  total: number
}

export interface PaginationLinks {
  first: string | null
  last: string | null
  prev: string | null
  next: string | null
}

export interface PaginatedResponse<T> {
  data: T[]
  links: PaginationLinks
  meta: PaginationMeta
}

export interface ProductFormData {
  category_id: number | null
  name: string
  description: string
  ingredients: string
  allergens: string
  price: number | string
  stock: number | string
  status: ProductStatus
  is_signature: boolean
  images: Array<Pick<ProductImage, 'url'>>
  variants: Array<Pick<ProductVariant, 'label' | 'price' | 'stock'>>
}

export interface StoreProductPayload {
  category_id: number | null
  name: string
  description: string | null
  ingredients: string | null
  allergens: string | null
  price: number
  stock: number
  status: ProductStatus
  is_signature: boolean
  images: string[]
  variants: Array<{ label: string; price: number; stock: number }>
}

export interface CategoriesResponse {
  data: Category[]
}
