<script setup lang="ts">
import { filterAndSortProducts } from '~/utils/storefront.mjs'

const { t } = useI18n()

useSeoMeta({ title: "Shop cookies | Lyn's Little Kitchen", description: 'Browse fresh cookies and gift boxes baked in Jasin, Melaka.' })

const { data: products, pending } = useCatalog()
const search = ref('')
const category = ref('all')
const sort = ref('featured')

const categories = computed(() => {
  const unique = new Map(
    products.value
      .filter(product => product.category)
      .map(product => [product.category.slug, product.category]),
  )
  return [{ name: t('shop.allCookies'), slug: 'all' }, ...unique.values()]
})

const visibleProducts = computed(() => filterAndSortProducts(products.value, {
  search: search.value,
  category: category.value,
  sort: sort.value,
}))
</script>

<template>
  <div class="container-shell py-10 sm:py-14">
    <header class="max-w-2xl">
      <span class="eyebrow">{{ t('shop.eyebrow') }}</span>
      <h1 class="display-title mt-4 text-5xl sm:text-6xl">{{ t('shop.title') }}</h1>
      <p class="mt-4 text-sm leading-7 text-stone-600">{{ t('shop.intro') }}</p>
    </header>

    <div class="mt-9 grid gap-7 lg:grid-cols-[220px_1fr]">
      <aside class="paper-card h-fit p-4 lg:sticky lg:top-24">
        <label class="form-label">{{ t('shop.search') }}
          <input v-model="search" class="form-input" type="search" :placeholder="t('shop.searchPlaceholder')">
        </label>
        <div class="mt-6">
          <p class="text-xs font-bold uppercase tracking-wider">{{ t('shop.categories') }}</p>
          <div class="mt-3 grid gap-1">
            <button v-for="item in categories" :key="item.slug" class="min-h-11 rounded-lg px-3 text-left text-sm transition" :class="category === item.slug ? 'bg-[#f0d9cc] font-bold text-[#854536]' : 'hover:bg-white'" @click="category = item.slug">
              {{ item.name }}
            </button>
          </div>
        </div>
      </aside>

      <section>
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
          <p class="text-sm text-stone-500"><strong class="text-stone-800">{{ visibleProducts.length }}</strong> {{ t('shop.products') }}</p>
          <label class="flex items-center gap-2 text-xs font-bold">{{ t('shop.sortBy') }}
            <select v-model="sort" class="form-select w-auto">
              <option value="featured">{{ t('shop.sortFeatured') }}</option>
              <option value="price_asc">{{ t('shop.sortPriceAsc') }}</option>
              <option value="price_desc">{{ t('shop.sortPriceDesc') }}</option>
              <option value="name">{{ t('shop.sortName') }}</option>
            </select>
          </label>
        </div>

        <div v-if="pending" class="grid grid-cols-2 gap-4 md:grid-cols-3">
          <div v-for="index in 6" :key="index" class="aspect-[.8] animate-pulse rounded-2xl bg-[#ead8cc]" />
        </div>
        <div v-else-if="visibleProducts.length" class="grid grid-cols-2 gap-3 sm:gap-5 md:grid-cols-3">
          <ProductCard v-for="product in visibleProducts" :key="product.slug" :product="product" />
        </div>
        <div v-else class="paper-card px-6 py-16 text-center">
          <p class="font-serif text-2xl">{{ t('shop.noneFound') }}</p>
          <p class="mt-2 text-sm text-stone-500">{{ t('shop.tryAnother') }}</p>
          <button class="btn-secondary mt-5" @click="search = ''; category = 'all'">{{ t('shop.clearFilters') }}</button>
        </div>
      </section>
    </div>
  </div>
</template>
