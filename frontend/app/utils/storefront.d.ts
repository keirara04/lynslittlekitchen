import type { CartLine, Product, ProductImage } from '~/types/catalog'

export function formatRinggit(value: number | string): string
export function filterAndSortProducts(products: Product[], filters?: { search?: string, category?: string, sort?: string }): Product[]
export function calculateCartTotals(lines: Array<{ unitPrice: number, quantity: number }>, deliveryFee?: number): { subtotal: number, deliveryFee: number, total: number, itemCount: number }
export function resolveProductImage(slug: string, images?: ProductImage[]): string
export function getOrderProgress(status: string): number
export function upsertCartLine(lines: CartLine[], incoming: CartLine): CartLine[]
export function setCartLineQuantity(lines: CartLine[], key: string, quantity: number): CartLine[]
