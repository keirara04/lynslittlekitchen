<script setup lang="ts">
useSeoMeta({ title: "Checkout | Lyn's Little Kitchen" })

const cart = useCartStore()
const config = useRuntimeConfig()
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
  $fetch<PaymentInfo>('/payment-info', { baseURL: config.public.apiBase }))

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
  $fetch<{ data: DeliveryZone[] }>('/delivery-zones', { baseURL: config.public.apiBase }))
const zones = computed(() => zonesResponse.value?.data ?? [])

const tomorrow = new Date(Date.now() + 86400000).toISOString().slice(0, 10)
const form = reactive({
  guest_name: '',
  guest_phone: '',
  guest_email: '',
  delivery_method: 'delivery',
  delivery_zone_id: null as number | null,
  delivery_address: '',
  delivery_date: tomorrow,
  notes: cart.note,
})

watch(zones, (list) => {
  if (list.length && !form.delivery_zone_id) form.delivery_zone_id = list[0].id
}, { immediate: true })

watch(() => [form.delivery_method, form.delivery_zone_id], () => {
  cart.deliveryFee = form.delivery_method === 'pickup' ? 0 : (zones.value.find(zone => zone.id === Number(form.delivery_zone_id))?.price ?? 0)
}, { immediate: true })

async function placeOrder() {
  submitError.value = ''
  if (!cart.lines.length) {
    submitError.value = 'Your cart is empty. Add a cookie before checking out.'
    return
  }
  if (!form.guest_name.trim() || !form.guest_phone.trim() || !form.delivery_date) {
    submitError.value = 'Enter your name, phone number and delivery date.'
    return
  }
  if (form.delivery_method === 'delivery' && !form.delivery_address.trim()) {
    submitError.value = 'Enter a delivery address or choose pickup.'
    return
  }

  submitting.value = true
  try {
    const response = await $fetch<{ data: { order_reference: string, total: number } }>('/orders', {
      method: 'POST',
      baseURL: config.public.apiBase,
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
    cart.clear()
  }
  catch (error: any) {
    submitError.value = error?.data?.message || 'We could not create the order. Check that Laravel is running and try again.'
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
    proofError.value = 'Please upload your bank transfer receipt first.'
    return
  }
  if (!confirmation.value) return

  proofSubmitting.value = true
  try {
    await $fetch(`/orders/${confirmation.value.order_reference}/payment-proof`, {
      method: 'POST',
      baseURL: config.public.apiBase,
      body: { phone: form.guest_phone, proof_url: receiptUrl.value },
    })
    proofSubmitted.value = true
  }
  catch (error: any) {
    proofError.value = error?.data?.message || 'Could not submit your receipt. Please try again.'
  }
  finally {
    proofSubmitting.value = false
  }
}
</script>

<template>
  <div class="container-shell py-10 sm:py-14">
    <header>
      <span class="eyebrow">Almost oven time</span>
      <h1 class="display-title mt-4 text-5xl">Checkout</h1>
    </header>

    <div class="mt-7 grid grid-cols-4 gap-2 rounded-xl border border-[#70453418] bg-white/60 p-3 text-center text-[10px] font-bold uppercase tracking-wide sm:text-xs">
      <span class="rounded-lg bg-[#a85f4c] px-2 py-3 text-white">1 · Details</span>
      <span class="px-2 py-3 text-stone-500">2 · Delivery</span>
      <span class="px-2 py-3" :class="confirmation ? 'bg-[#a85f4c] rounded-lg text-white' : 'text-stone-500'">3 · Payment</span>
      <span class="px-2 py-3 text-stone-500">4 · Confirm</span>
    </div>

    <div v-if="confirmation" class="paper-card mt-8 px-6 py-10 text-center sm:px-10">
      <span class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-[#e5ead8] text-2xl text-[#66704d]">✓</span>
      <h2 class="mt-5 font-serif text-3xl">Your order is in the kitchen</h2>
      <p class="mt-3 text-sm text-stone-600">Keep this reference to track your order:</p>
      <p class="mt-3 font-serif text-2xl text-[#a85f4c]">{{ confirmation.order_reference }}</p>

      <div v-if="proofSubmitted" class="mx-auto mt-8 max-w-md rounded-xl border border-[#c9dab8] bg-[#eef3e4] p-5 text-left">
        <p class="text-sm font-bold text-[#4c5a37]">Receipt submitted!</p>
        <p class="mt-1 text-sm text-[#5c6a48]">We'll confirm your payment shortly. You'll see it marked as paid once verified.</p>
      </div>

      <div v-else class="mx-auto mt-8 max-w-md rounded-xl border border-[#70453418] bg-white/70 p-5 text-left">
        <h3 class="font-serif text-xl">Pay by bank transfer</h3>
        <p class="mt-1 text-sm text-stone-600">{{ paymentInfo?.instructions }}</p>

        <dl class="mt-4 grid grid-cols-[auto_1fr] gap-x-3 gap-y-1.5 text-sm">
          <dt class="font-bold text-stone-500">Bank</dt><dd>{{ paymentInfo?.bank_name }}</dd>
          <dt class="font-bold text-stone-500">Account name</dt><dd>{{ paymentInfo?.bank_account_name }}</dd>
          <dt class="font-bold text-stone-500">Account number</dt><dd>{{ paymentInfo?.bank_account_number }}</dd>
          <dt class="font-bold text-stone-500">DuitNow ID</dt><dd>{{ paymentInfo?.duitnow_id }}</dd>
          <dt class="font-bold text-stone-500">Amount</dt><dd class="font-bold text-[#a85f4c]">RM{{ confirmation.total.toFixed(2) }}</dd>
        </dl>

        <div class="mt-5 border-t border-[#70453418] pt-5">
          <p class="text-xs font-bold uppercase tracking-wider text-stone-500">Receipt</p>
          <input ref="receiptFileInput" type="file" accept="image/*" class="hidden" @change="onReceiptSelected">

          <div v-if="receiptUrl" class="mt-3 flex items-center gap-3">
            <img :src="receiptUrl" alt="Uploaded receipt" class="h-16 w-16 rounded-lg object-cover">
            <button type="button" class="btn-secondary text-xs" :disabled="receiptUploading" @click="triggerReceiptUpload">Replace</button>
          </div>
          <button v-else type="button" class="btn-secondary mt-3 w-full" :disabled="receiptUploading" @click="triggerReceiptUpload">
            {{ receiptUploading ? 'Uploading…' : '⬆ Upload transfer receipt' }}
          </button>

          <p v-if="receiptUploadError" class="mt-2 text-xs font-semibold text-red-700">{{ receiptUploadError }}</p>
          <p v-if="proofError" class="mt-2 text-xs font-semibold text-red-700">{{ proofError }}</p>

          <button class="btn-primary mt-4 w-full" type="button" :disabled="proofSubmitting || !receiptUrl" @click="submitProof">
            {{ proofSubmitting ? 'Submitting…' : "I've paid — submit receipt" }}
          </button>
        </div>
      </div>

      <NuxtLink :to="`/track-order?reference=${confirmation.order_reference}&phone=${encodeURIComponent(form.guest_phone)}`" class="btn-ghost mt-6">Track this order</NuxtLink>
    </div>

    <form v-else class="mt-8 grid gap-6 lg:grid-cols-[1fr_340px]" @submit.prevent="placeOrder">
      <section class="paper-card p-5 sm:p-7">
        <h2 class="font-serif text-2xl">Delivery details</h2>
        <div class="mt-6 grid gap-5 sm:grid-cols-2">
          <label class="form-label">Full name *<input v-model="form.guest_name" class="form-input" autocomplete="name" required></label>
          <label class="form-label">Phone number *<input v-model="form.guest_phone" class="form-input" autocomplete="tel" inputmode="tel" required></label>
          <label class="form-label sm:col-span-2">Email (optional)<input v-model="form.guest_email" class="form-input" type="email" autocomplete="email"></label>
        </div>

        <fieldset class="mt-7">
          <legend class="text-xs font-bold uppercase tracking-wider">Delivery method</legend>
          <div class="mt-3 grid grid-cols-2 gap-3">
            <label class="cursor-pointer"><input v-model="form.delivery_method" class="peer sr-only" type="radio" value="delivery"><span class="grid min-h-16 place-items-center rounded-xl border bg-white text-sm font-bold peer-checked:border-[#a85f4c] peer-checked:bg-[#f4ded2]">Local delivery</span></label>
            <label class="cursor-pointer"><input v-model="form.delivery_method" class="peer sr-only" type="radio" value="pickup"><span class="grid min-h-16 place-items-center rounded-xl border bg-white text-sm font-bold peer-checked:border-[#a85f4c] peer-checked:bg-[#f4ded2]">Pickup</span></label>
          </div>
        </fieldset>

        <div class="mt-6 grid gap-5 sm:grid-cols-2">
          <label v-if="form.delivery_method === 'delivery'" class="form-label">Delivery area
            <select v-model="form.delivery_zone_id" class="form-select">
              <option v-for="zone in zones" :key="zone.id" :value="zone.id">{{ zone.name }} · RM{{ zone.price.toFixed(2) }}</option>
            </select>
          </label>
          <label class="form-label">Preferred date *<input v-model="form.delivery_date" class="form-input" type="date" :min="tomorrow" required></label>
          <label v-if="form.delivery_method === 'delivery'" class="form-label sm:col-span-2">Delivery address *<textarea v-model="form.delivery_address" class="form-textarea" autocomplete="street-address" required /></label>
          <label class="form-label sm:col-span-2">Order notes<textarea v-model="form.notes" class="form-textarea" placeholder="Anything the kitchen should know?" /></label>
        </div>

        <p v-if="submitError" class="mt-5 rounded-lg bg-red-50 p-4 text-sm font-semibold text-red-700" role="alert">{{ submitError }}</p>
        <button class="btn-primary mt-6 w-full lg:hidden" type="submit" :disabled="submitting">{{ submitting ? 'Creating your order…' : 'Place order' }}</button>
      </section>

      <div>
        <CartOrderSummary :action-label="submitting ? 'Creating your order…' : 'Place order'" :disabled="submitting" @action="placeOrder" />
        <p class="mt-3 text-center text-[11px] leading-5 text-stone-500">You'll pay by bank transfer on the next screen and upload your receipt.</p>
      </div>
    </form>
  </div>
</template>
