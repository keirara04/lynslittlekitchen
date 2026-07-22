import type { AdminProduct, OrderStatus } from '../types/admin'

export const fulfilmentStatuses: OrderStatus[]

export function formatAdminCurrency(value: number | string | null | undefined): string
export function formatAdminDate(value: string | Date | null | undefined, options?: Intl.DateTimeFormatOptions): string
export function humanizeStatus(status: string | null | undefined): string
export function progressIndex(status: string): number
export function stockSummary(product: Pick<AdminProduct, 'stock' | 'variants'>): string
export function buildAdminQuery(filters: Record<string, unknown>): Record<string, unknown>
