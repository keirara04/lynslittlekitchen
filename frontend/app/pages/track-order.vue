<script setup lang="ts">
import { formatRinggit, getOrderProgress } from '~/utils/storefront.mjs'

const { t, tm } = useI18n()

useSeoMeta({ title: "Track your order | Lyn's Little Kitchen" })
const route = useRoute()
const reference = ref(String(route.query.reference || ''))
const phone = ref(String(route.query.phone || ''))
const pending = ref(false)
const errorMessage = ref('')
const order = ref<any>(null)
const statusKeys = ['pending', 'preparing', 'baking', 'packing', 'out_for_delivery', 'completed']
const statuses = computed(() => {
  const labels = tm('trackOrder.statuses') as unknown as string[]
  return statusKeys.map((key, index) => [key, labels[index]] as const)
})

const progress = computed(() => getOrderProgress(order.value?.order_status || 'pending'))

async function lookup() {
  errorMessage.value = ''
  order.value = null
  if (!reference.value.trim() || !phone.value.trim()) {
    errorMessage.value = t('trackOrder.missingFields')
    return
  }
  pending.value = true
  try {
    const response = await useApiFetch<{ data: any }>(`/orders/${encodeURIComponent(reference.value.trim())}`, {
      query: { phone: phone.value.trim() },
    })
    order.value = response.data
  }
  catch {
    errorMessage.value = t('trackOrder.notFound')
  }
  finally {
    pending.value = false
  }
}

onMounted(() => {
  if (reference.value && phone.value) lookup()
})
</script>

<template>
  <div class="container-shell py-10 sm:py-14">
    <header class="max-w-2xl">
      <span class="eyebrow">{{ t('trackOrder.eyebrow') }}</span>
      <h1 class="display-title mt-4 text-5xl">{{ t('trackOrder.title') }}</h1>
      <p class="mt-4 text-sm leading-7 text-stone-600">{{ t('trackOrder.intro') }}</p>
    </header>

    <form class="paper-card mt-8 grid gap-4 p-5 sm:grid-cols-[1fr_1fr_auto] sm:items-end" @submit.prevent="lookup">
      <label class="form-label">{{ t('trackOrder.orderReference') }}<input v-model="reference" class="form-input" placeholder="LLK-2026-000123"></label>
      <label class="form-label">{{ t('contact.phone') }}<input v-model="phone" class="form-input" inputmode="tel" :placeholder="t('trackOrder.phonePlaceholder')"></label>
      <button class="btn-primary" :disabled="pending">{{ pending ? t('trackOrder.checking') : t('nav.trackOrder') }}</button>
    </form>
    <p v-if="errorMessage" class="mt-4 rounded-xl bg-red-50 p-4 text-sm font-semibold text-red-700" role="alert">{{ errorMessage }}</p>

    <section v-if="order" class="paper-card mt-8 p-5 sm:p-7">
      <div class="flex flex-wrap items-start justify-between gap-4">
        <div><p class="text-xs font-bold uppercase tracking-wider text-stone-500">{{ t('trackOrder.orderReference') }}</p><h2 class="mt-1 font-serif text-2xl">{{ order.order_reference }}</h2></div>
        <span class="rounded-full bg-[#e5ead8] px-3 py-1.5 text-xs font-bold capitalize text-[#596143]">{{ order.payment_status }}</span>
      </div>

      <div v-if="progress >= 0" class="order-progress mt-10">
        <div v-for="(status, index) in statuses" :key="status[0]" class="order-progress-step" :class="{ 'is-done': index < progress, 'is-current': index === progress }">
          <span>{{ status[1] }}</span>
        </div>
      </div>
      <div v-else class="mt-8 rounded-xl bg-red-50 p-5 text-sm text-red-700">{{ t('trackOrder.orderStatusHelp', { status: order.order_status }) }}</div>

      <div class="mt-10 grid gap-5 border-t border-[#70453418] pt-6 sm:grid-cols-3">
        <div><p class="text-xs text-stone-500">{{ t('trackOrder.deliveryMethod') }}</p><p class="mt-1 text-sm font-bold capitalize">{{ order.delivery_method }}</p></div>
        <div><p class="text-xs text-stone-500">{{ t('trackOrder.deliveryDate') }}</p><p class="mt-1 text-sm font-bold">{{ order.delivery_date }}</p></div>
        <div><p class="text-xs text-stone-500">{{ t('trackOrder.orderTotal') }}</p><p class="mt-1 text-sm font-bold">{{ formatRinggit(order.total) }}</p></div>
      </div>
    </section>
  </div>
</template>
