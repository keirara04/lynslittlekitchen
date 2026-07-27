export interface FeaturedProduct {
  name: string
  slug: string
  description: string | null
  image_url: string | null
}

export interface StoreSettings {
  store_name: string | null
  address_line1: string | null
  address_line2: string | null
  postcode: string | null
  city: string | null
  state: string | null
  contact_phone: string | null
  contact_email: string | null
  operating_hours: string | null
  min_order_amount: number | null
  allow_pickup: boolean
  allow_delivery: boolean
  featured_hero_product: FeaturedProduct | null
  featured_banner_product: FeaturedProduct | null
}

export function useStoreSettings() {
  const { locale } = useI18n()

  return useAsyncData<StoreSettings>('store-settings', () =>
    useApiFetch<StoreSettings>('/store-settings'), {
    getCachedData: () => undefined,
    watch: [locale],
  })
}
