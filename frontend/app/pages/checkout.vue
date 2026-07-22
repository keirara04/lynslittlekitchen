<script setup lang="ts">
useSeoMeta({ title: "Checkout | Lyn's Little Kitchen" })

const cart = useCartStore()
const config = useRuntimeConfig()
const submitting = ref(false)
const submitError = ref('')
const confirmation = ref<{ order_reference: string, total: number } | null>(null)

const tomorrow = new Date(Date.now() + 86400000).toISOString().slice(0, 10)
const form = reactive({
  guest_name: '',
  guest_phone: '',
  guest_email: '',
  delivery_method: 'delivery',
  delivery_zone_id: 1,
  delivery_address: '',
  delivery_date: tomorrow,
  notes: cart.note,
})

const zones = [
  { id: 1, name: 'Jasin', fee: 3 },
  { id: 2, name: 'Melaka Tengah', fee: 6 },
  { id: 3, name: 'Alor Gajah', fee: 8 },
  { id: 4, name: 'Outside Melaka', fee: 15 },
]

watch(() => [form.delivery_method, form.delivery_zone_id], () => {
  cart.deliveryFee = form.delivery_method === 'pickup' ? 0 : (zones.find(zone => zone.id === Number(form.delivery_zone_id))?.fee ?? 0)
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
      <span class="px-2 py-3 text-stone-500">3 · Payment</span>
      <span class="px-2 py-3 text-stone-500">4 · Confirm</span>
    </div>

    <div v-if="confirmation" class="paper-card mt-8 px-6 py-16 text-center">
      <span class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-[#e5ead8] text-2xl text-[#66704d]">✓</span>
      <h2 class="mt-5 font-serif text-3xl">Your order is in the kitchen</h2>
      <p class="mt-3 text-sm text-stone-600">Keep this reference to track your order:</p>
      <p class="mt-3 font-serif text-2xl text-[#a85f4c]">{{ confirmation.order_reference }}</p>
      <p class="mt-5 text-sm text-stone-500">Payment integration is coming next. Your order is currently marked unpaid.</p>
      <NuxtLink :to="`/track-order?reference=${confirmation.order_reference}&phone=${encodeURIComponent(form.guest_phone)}`" class="btn-primary mt-7">Track this order</NuxtLink>
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
              <option v-for="zone in zones" :key="zone.id" :value="zone.id">{{ zone.name }} · RM{{ zone.fee.toFixed(2) }}</option>
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
        <p class="mt-3 text-center text-[11px] leading-5 text-stone-500">Payment is not charged on this screen. Billplz integration will be connected later.</p>
      </div>
    </form>
  </div>
</template>
