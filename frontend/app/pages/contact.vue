<script setup lang="ts">
useSeoMeta({ title: "Contact | Lyn's Little Kitchen" })

const { data: storeSettings } = await useStoreSettings()
const location = computed(() => {
  const parts = [storeSettings.value?.city, storeSettings.value?.state].filter(Boolean)
  return parts.length ? parts.join(', ') + ', Malaysia' : 'Jasin, Melaka, Malaysia'
})
const hours = computed(() => storeSettings.value?.operating_hours || 'Monday–Saturday · 9am–6pm')

const WHATSAPP_NUMBERS = [
  { label: 'Malaysia', number: '601156819034' },
  { label: 'Korea', number: '821059378068' },
]

const name = ref('')
const phone = ref('')
const email = ref('')
const message = ref('')
const formError = ref('')

function buildWhatsappText() {
  const lines = [
    "Hi, I'd like to get in touch with Lyn's Little Kitchen.",
    '',
    `Name: ${name.value}`,
    `Phone: ${phone.value || '-'}`,
    `Email: ${email.value || '-'}`,
    '',
    `Message: ${message.value}`,
  ]
  return lines.join('\n')
}

function sendToWhatsapp() {
  if (!name.value.trim() || !message.value.trim()) {
    formError.value = 'Please fill in your name and message.'
    return
  }
  formError.value = ''
  const text = encodeURIComponent(buildWhatsappText())
  for (const wa of WHATSAPP_NUMBERS) {
    window.open(`https://wa.me/${wa.number}?text=${text}`, '_blank')
  }
}
</script>

<template>
  <div class="container-shell py-12 sm:py-16">
    <div class="grid gap-8 lg:grid-cols-[.8fr_1.2fr]">
      <section>
        <span class="eyebrow">Say hello</span>
        <h1 class="display-title mt-4 text-5xl sm:text-6xl">Questions before you order?</h1>
        <p class="mt-5 text-sm leading-7 text-stone-600">Ask about delivery, allergens, larger orders or the next available bake date.</p>
        <div class="mt-8 grid gap-4 text-sm">
          <div class="paper-card p-5"><p class="text-xs font-bold uppercase tracking-wider text-[#a85f4c]">Location</p><p class="mt-2">{{ location }}</p></div>
          <div class="paper-card p-5"><p class="text-xs font-bold uppercase tracking-wider text-[#a85f4c]">Response hours</p><p class="mt-2">{{ hours }}</p></div>
        </div>
      </section>

      <form class="paper-card p-5 sm:p-7" @submit.prevent>
        <h2 class="font-serif text-2xl">Send the kitchen a note</h2>
        <p class="mt-2 text-xs text-stone-500">Fill in your details, then send via WhatsApp.</p>
        <div class="mt-6 grid gap-5 sm:grid-cols-2">
          <label class="form-label">Name<input v-model="name" class="form-input" autocomplete="name"></label>
          <label class="form-label">Phone number<input v-model="phone" class="form-input" autocomplete="tel" inputmode="tel"></label>
          <label class="form-label sm:col-span-2">Email<input v-model="email" class="form-input" type="email" autocomplete="email"></label>
          <label class="form-label sm:col-span-2">Your message<textarea v-model="message" class="form-textarea" /></label>
        </div>
        <p v-if="formError" class="mt-3 text-xs text-red-600">{{ formError }}</p>
        <button class="btn-primary mt-6" type="button" @click="sendToWhatsapp">
          Send via WhatsApp
        </button>
      </form>
    </div>
  </div>
</template>
