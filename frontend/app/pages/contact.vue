<script setup lang="ts">
const { t } = useI18n()

useSeoMeta({ title: "Contact | Lyn's Little Kitchen" })

const { data: storeSettings } = await useStoreSettings()
const location = computed(() => {
  const parts = [storeSettings.value?.city, storeSettings.value?.state].filter(Boolean)
  return parts.length ? parts.join(', ') + `, ${t('footer.malaysia')}` : t('footer.defaultLocation')
})
const hours = computed(() => storeSettings.value?.operating_hours || t('contact.defaultHours'))

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
    t('contact.whatsappGreeting'),
    '',
    `${t('contact.name')}: ${name.value}`,
    `${t('contact.phone')}: ${phone.value || '-'}`,
    `${t('contact.email')}: ${email.value || '-'}`,
    '',
    `${t('contact.message')}: ${message.value}`,
  ]
  return lines.join('\n')
}

function sendToWhatsapp() {
  if (!name.value.trim() || !message.value.trim()) {
    formError.value = t('contact.formError')
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
        <span class="eyebrow">{{ t('contact.eyebrow') }}</span>
        <h1 class="display-title mt-4 text-5xl sm:text-6xl">{{ t('contact.title') }}</h1>
        <p class="mt-5 text-sm leading-7 text-stone-600">{{ t('contact.intro') }}</p>
        <div class="mt-8 grid gap-4 text-sm">
          <div class="paper-card p-5"><p class="text-xs font-bold uppercase tracking-wider text-[#a85f4c]">{{ t('contact.location') }}</p><p class="mt-2">{{ location }}</p></div>
          <div class="paper-card p-5"><p class="text-xs font-bold uppercase tracking-wider text-[#a85f4c]">{{ t('contact.responseHours') }}</p><p class="mt-2">{{ hours }}</p></div>
        </div>
      </section>

      <form class="paper-card p-5 sm:p-7" @submit.prevent>
        <h2 class="font-serif text-2xl">{{ t('contact.formTitle') }}</h2>
        <p class="mt-2 text-xs text-stone-500">{{ t('contact.formSubtitle') }}</p>
        <div class="mt-6 grid gap-5 sm:grid-cols-2">
          <label class="form-label">{{ t('contact.name') }}<input v-model="name" class="form-input" autocomplete="name"></label>
          <label class="form-label">{{ t('contact.phone') }}<input v-model="phone" class="form-input" autocomplete="tel" inputmode="tel"></label>
          <label class="form-label sm:col-span-2">{{ t('contact.email') }}<input v-model="email" class="form-input" type="email" autocomplete="email"></label>
          <label class="form-label sm:col-span-2">{{ t('contact.yourMessage') }}<textarea v-model="message" class="form-textarea" /></label>
        </div>
        <p v-if="formError" class="mt-3 text-xs text-red-600">{{ formError }}</p>
        <button class="btn-primary mt-6" type="button" @click="sendToWhatsapp">
          {{ t('contact.sendViaWhatsapp') }}
        </button>
      </form>
    </div>
  </div>
</template>
