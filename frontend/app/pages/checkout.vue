<script setup lang="ts">
import type { FetchError } from 'ofetch'

const { t } = useI18n()

useSeoMeta({ title: "Checkout | Lyn's Little Kitchen" })

const cart = useCartStore()
const submitting = ref(false)
const submitError = ref('')
const confirmation = ref<{ order_reference: string, total: number } | null>(null)

interface PaymentInfo {
  bank_name: string
  bank_account_name: string
  bank_account_number: string
  duitnow_id: string
  instructions: string
}

const { data: paymentInfo } = await useAsyncData<PaymentInfo>('payment-info', () =>
  useApiFetch<PaymentInfo>('/payment-info'), { getCachedData: () => undefined })

const { data: storeSettings } = await useStoreSettings()
const allowPickup = computed(() => storeSettings.value?.allow_pickup ?? true)
const allowDelivery = computed(() => storeSettings.value?.allow_delivery ?? true)
const minOrderAmount = computed(() => storeSettings.value?.min_order_amount ?? null)

const { upload: uploadReceipt, uploading: receiptUploading, error: receiptUploadError } = useCloudinaryUpload()
const receiptFileInput = ref<HTMLInputElement>()
const receiptUrl = ref('')
const proofSubmitting = ref(false)
const proofSubmitted = ref(false)
const proofError = ref('')

interface DeliveryZone {
  id: number
  name: string
  price: number
}

const { data: zonesResponse } = await useAsyncData<{ data: DeliveryZone[] }>('delivery-zones', () =>
  useApiFetch<{ data: DeliveryZone[] }>('/delivery-zones'), { getCachedData: () => undefined })
const zones = computed(() => zonesResponse.value?.data ?? [])

const minDeliveryDate = new Date(Date.now() + 3 * 86400000).toISOString().slice(0, 10)
const form = reactive({
  guest_name: '',
  guest_phone: '',
  guest_email: '',
  delivery_method: 'delivery',
  delivery_zone_id: null as number | null,
  delivery_address: '',
  delivery_date: minDeliveryDate,
  notes: cart.note,
})

watch(zones, (list) => {
  if (list[0] && !form.delivery_zone_id) form.delivery_zone_id = list[0].id
}, { immediate: true })

watch([allowDelivery, allowPickup], ([delivery, pickup]) => {
  if (form.delivery_method === 'delivery' && !delivery && pickup) form.delivery_method = 'pickup'
  else if (form.delivery_method === 'pickup' && !pickup && delivery) form.delivery_method = 'delivery'
}, { immediate: true })

watch(() => [form.delivery_method, form.delivery_zone_id], () => {
  cart.deliveryFee = form.delivery_method === 'pickup' ? 0 : (zones.value.find(zone => zone.id === Number(form.delivery_zone_id))?.price ?? 0)
}, { immediate: true })

async function placeOrder() {
  submitError.value = ''
  if (!cart.lines.length) {
    submitError.value = t('checkout.errors.cartEmpty')
    return
  }
  if (!form.guest_name.trim() || !form.guest_phone.trim() || !form.delivery_date) {
    submitError.value = t('checkout.errors.missingFields')
    return
  }
  if (form.delivery_method === 'delivery' && !form.delivery_address.trim()) {
    submitError.value = t('checkout.errors.missingAddress')
    return
  }
  if (minOrderAmount.value !== null && cart.totals.subtotal < minOrderAmount.value) {
    submitError.value = t('checkout.errors.minOrder', { amount: minOrderAmount.value.toFixed(2) })
    return
  }

  submitting.value = true
  try {
    const response = await useApiFetch<{ data: { order_reference: string, total: number } }>('/orders', {
      method: 'POST',
      body: {
        ...form,
        delivery_zone_id: form.delivery_method === 'delivery' ? Number(form.delivery_zone_id) : null,
        delivery_address: form.delivery_method === 'delivery' ? form.delivery_address : null,
        notes: form.notes || null,
        items: cart.lines.map(line => ({
          product_id: line.productId,
          product_variant_id: line.variantId,
          quantity: line.quantity,
        })),
      },
    })
    confirmation.value = response.data
  }
  catch (err) {
    const error = err as FetchError<{ message?: string }>
    submitError.value = error.data?.message || t('checkout.errors.orderFailed')
  }
  finally {
    submitting.value = false
  }
}

function triggerReceiptUpload() {
  receiptFileInput.value?.click()
}

async function onReceiptSelected(event: Event) {
  const input = event.target as HTMLInputElement
  const file = input.files?.[0]
  input.value = ''
  if (!file) return

  try {
    receiptUrl.value = await uploadReceipt(file)
    proofError.value = ''
  }
  catch {
    // receiptUploadError already holds the message for display.
  }
}

async function submitProof() {
  proofError.value = ''
  if (!receiptUrl.value) {
    proofError.value = t('checkout.errors.receiptMissing')
    return
  }
  if (!confirmation.value) return

  proofSubmitting.value = true
  try {
    await useApiFetch(`/orders/${confirmation.value.order_reference}/payment-proof`, {
      method: 'POST',
      body: { phone: form.guest_phone, proof_url: receiptUrl.value },
    })
    proofSubmitted.value = true
    cart.clear()
  }
  catch (err) {
    const error = err as FetchError<{ message?: string }>
    proofError.value = error.data?.message || t('checkout.errors.proofFailed')
  }
  finally {
    proofSubmitting.value = false
  }
}
</script>

<template>
  <div class="container-shell py-10 sm:py-14">
    <header>
      <span class="eyebrow">{{ t('checkout.eyebrow') }}</span>
      <h1 class="display-title mt-4 text-5xl">{{ t('checkout.title') }}</h1>
    </header>

    <div class="mt-7 grid grid-cols-4 gap-2 rounded-xl border border-[#70453418] bg-white/60 p-3 text-center text-[10px] font-bold uppercase tracking-wide sm:text-xs">
      <span class="rounded-lg bg-[#a85f4c] px-2 py-3 text-white">{{ t('checkout.steps.details') }}</span>
      <span class="px-2 py-3 text-stone-500">{{ t('checkout.steps.delivery') }}</span>
      <span class="px-2 py-3" :class="confirmation ? 'bg-[#a85f4c] rounded-lg text-white' : 'text-stone-500'">{{ t('checkout.steps.payment') }}</span>
      <span class="px-2 py-3 text-stone-500">{{ t('checkout.steps.confirm') }}</span>
    </div>

    <div v-if="confirmation" class="paper-card mt-8 px-6 py-10 text-center sm:px-10">
      <span class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-[#e5ead8] text-2xl text-[#66704d]">✓</span>
      <h2 class="mt-5 font-serif text-3xl">{{ t('checkout.orderInKitchen') }}</h2>
      <p class="mt-3 text-sm text-stone-600">{{ t('checkout.keepReference') }}</p>
      <p class="mt-3 font-serif text-2xl text-[#a85f4c]">{{ confirmation.order_reference }}</p>

      <div v-if="proofSubmitted" class="mx-auto mt-8 max-w-md rounded-xl border border-[#c9dab8] bg-[#eef3e4] p-5 text-left">
        <p class="text-sm font-bold text-[#4c5a37]">{{ t('checkout.receiptSubmitted') }}</p>
        <p class="mt-1 text-sm text-[#5c6a48]">{{ t('checkout.receiptSubmittedText') }}</p>
      </div>

      <div v-else class="mx-auto mt-8 max-w-md rounded-xl border border-[#70453418] bg-white/70 p-5 text-left">
        <h3 class="font-serif text-xl">{{ t('checkout.payByBankTransfer') }}</h3>
        <p class="mt-1 text-sm text-stone-600">{{ paymentInfo?.instructions }}</p>

        <dl class="mt-4 grid grid-cols-[auto_1fr] gap-x-3 gap-y-1.5 text-sm">
          <dt class="font-bold text-stone-500">{{ t('checkout.bank') }}</dt><dd>{{ paymentInfo?.bank_name }}</dd>
          <dt class="font-bold text-stone-500">{{ t('checkout.accountName') }}</dt><dd>{{ paymentInfo?.bank_account_name }}</dd>
          <dt class="font-bold text-stone-500">{{ t('checkout.accountNumber') }}</dt><dd>{{ paymentInfo?.bank_account_number }}</dd>
          <dt class="font-bold text-stone-500">{{ t('checkout.duitnowId') }}</dt><dd>{{ paymentInfo?.duitnow_id }}</dd>
          <dt class="font-bold text-stone-500">{{ t('checkout.amount') }}</dt><dd class="font-bold text-[#a85f4c]">RM{{ confirmation.total.toFixed(2) }}</dd>
        </dl>

        <div class="mt-5 border-t border-[#70453418] pt-5">
          <p class="text-xs font-bold uppercase tracking-wider text-stone-500">{{ t('checkout.receipt') }}</p>
          <input ref="receiptFileInput" type="file" accept="image/*" class="hidden" @change="onReceiptSelected">

          <div v-if="receiptUrl" class="mt-3 flex items-center gap-3">
            <img :src="receiptUrl" alt="Uploaded receipt" class="h-16 w-16 rounded-lg object-cover">
            <button type="button" class="btn-secondary text-xs" :disabled="receiptUploading" @click="triggerReceiptUpload">{{ t('checkout.replace') }}</button>
          </div>
          <button v-else type="button" class="btn-secondary mt-3 w-full" :disabled="receiptUploading" @click="triggerReceiptUpload">
            {{ receiptUploading ? t('checkout.uploading') : `⬆ ${t('checkout.uploadReceipt')}` }}
          </button>

          <p v-if="receiptUploadError" class="mt-2 text-xs font-semibold text-red-700">{{ receiptUploadError }}</p>
          <p v-if="proofError" class="mt-2 text-xs font-semibold text-red-700">{{ proofError }}</p>

          <button class="btn-primary mt-4 w-full" type="button" :disabled="proofSubmitting || !receiptUrl" @click="submitProof">
            {{ proofSubmitting ? t('checkout.submitting') : t('checkout.paidSubmitReceipt') }}
          </button>
        </div>
      </div>

      <NuxtLink :to="`/track-order?reference=${confirmation.order_reference}&phone=${encodeURIComponent(form.guest_phone)}`" class="btn-ghost mt-6">{{ t('checkout.trackThisOrder') }}</NuxtLink>
    </div>

    <form v-else class="mt-8 grid gap-6 lg:grid-cols-[1fr_340px]" @submit.prevent="placeOrder">
      <section class="paper-card p-5 sm:p-7">
        <h2 class="font-serif text-2xl">{{ t('checkout.deliveryDetails') }}</h2>
        <div class="mt-6 grid gap-5 sm:grid-cols-2">
          <label class="form-label">{{ t('checkout.fullName') }} *<input v-model="form.guest_name" class="form-input" autocomplete="name" required></label>
          <label class="form-label">{{ t('contact.phone') }} *<input v-model="form.guest_phone" class="form-input" autocomplete="tel" inputmode="tel" required></label>
          <label class="form-label sm:col-span-2">{{ t('checkout.emailOptional') }}<input v-model="form.guest_email" class="form-input" type="email" autocomplete="email"></label>
        </div>

        <fieldset class="mt-7">
          <legend class="text-xs font-bold uppercase tracking-wider">{{ t('checkout.deliveryMethod') }}</legend>
          <div class="mt-3 grid grid-cols-2 gap-3">
            <label v-if="allowDelivery" class="cursor-pointer"><input v-model="form.delivery_method" class="peer sr-only" type="radio" value="delivery"><span class="grid min-h-16 place-items-center rounded-xl border bg-white text-sm font-bold peer-checked:border-[#a85f4c] peer-checked:bg-[#f4ded2]">{{ t('checkout.localDelivery') }}</span></label>
            <label v-if="allowPickup" class="cursor-pointer"><input v-model="form.delivery_method" class="peer sr-only" type="radio" value="pickup"><span class="grid min-h-16 place-items-center rounded-xl border bg-white text-sm font-bold peer-checked:border-[#a85f4c] peer-checked:bg-[#f4ded2]">{{ t('checkout.pickup') }}</span></label>
          </div>
          <p v-if="minOrderAmount" class="mt-2 text-xs text-stone-500">{{ t('checkout.minimumOrder', { amount: minOrderAmount.toFixed(2) }) }}</p>
        </fieldset>

        <div class="mt-6 grid gap-5 sm:grid-cols-2">
          <label v-if="form.delivery_method === 'delivery'" class="form-label">{{ t('checkout.deliveryArea') }}
            <select v-model="form.delivery_zone_id" class="form-select">
              <option v-for="zone in zones" :key="zone.id" :value="zone.id">{{ zone.name }} · RM{{ zone.price.toFixed(2) }}</option>
            </select>
          </label>
          <label class="form-label">{{ t('checkout.preferredDate') }} *<input v-model="form.delivery_date" class="form-input" type="date" :min="minDeliveryDate" required><small class="mt-1 block text-xs text-stone-500">{{ t('checkout.dateHint') }}</small></label>
          <label v-if="form.delivery_method === 'delivery'" class="form-label sm:col-span-2">{{ t('checkout.deliveryAddress') }} *<textarea v-model="form.delivery_address" class="form-textarea" autocomplete="street-address" required /></label>
          <label class="form-label sm:col-span-2">{{ t('checkout.orderNotes') }}<textarea v-model="form.notes" class="form-textarea" :placeholder="t('checkout.orderNotesPlaceholder')" /></label>
        </div>

        <p v-if="submitError" class="mt-5 rounded-lg bg-red-50 p-4 text-sm font-semibold text-red-700" role="alert">{{ submitError }}</p>
        <button class="btn-primary mt-6 w-full lg:hidden" type="submit" :disabled="submitting">{{ submitting ? t('checkout.creatingOrder') : t('checkout.placeOrder') }}</button>
      </section>

      <div>
        <CartOrderSummary :action-label="submitting ? t('checkout.creatingOrder') : t('checkout.placeOrder')" :disabled="submitting" @action="placeOrder" />
        <p class="mt-3 text-center text-[11px] leading-5 text-stone-500">{{ t('checkout.payNextScreen') }}</p>
      </div>
    </form>
  </div>
</template>
