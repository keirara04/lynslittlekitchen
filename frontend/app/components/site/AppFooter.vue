<script setup lang="ts">
const { data: storeSettings } = await useStoreSettings()
const { t } = useI18n()
const location = computed(() => {
  const parts = [storeSettings.value?.city, storeSettings.value?.state].filter(Boolean)
  return parts.length ? parts.join(', ') + `, ${t('footer.malaysia')}` : t('footer.defaultLocation')
})
</script>

<template>
  <footer class="site-footer mt-20">
    <div class="container-shell grid gap-10 py-12 sm:grid-cols-2 lg:grid-cols-4">
      <div>
        <div class="font-serif text-3xl">LYN’S</div>
        <p class="mt-1 text-sm italic text-[#f4cbbb]">Little Kitchen</p>
        <p class="mt-5 max-w-xs text-sm leading-6 text-white/70">
          {{ t('footer.tagline') }}
        </p>
      </div>
      <div>
        <h2 class="text-xs font-bold uppercase tracking-[.14em]">{{ t('footer.quickLinks') }}</h2>
        <div class="mt-4 grid gap-2 text-sm">
          <NuxtLink to="/">{{ t('nav.home') }}</NuxtLink><NuxtLink to="/shop">{{ t('nav.shop') }}</NuxtLink><NuxtLink to="/about">{{ t('nav.about') }}</NuxtLink><NuxtLink to="/how-to-order">{{ t('nav.howToOrder') }}</NuxtLink>
        </div>
      </div>
      <div>
        <h2 class="text-xs font-bold uppercase tracking-[.14em]">{{ t('footer.customerCare') }}</h2>
        <div class="mt-4 grid gap-2 text-sm">
          <NuxtLink to="/track-order">{{ t('nav.trackOrder') }}</NuxtLink><NuxtLink to="/contact">{{ t('footer.deliveryInfo') }}</NuxtLink><NuxtLink to="/contact">{{ t('nav.contact') }}</NuxtLink>
        </div>
      </div>
      <div>
        <h2 class="text-xs font-bold uppercase tracking-[.14em]">{{ t('footer.letsConnect') }}</h2>
        <p class="mt-4 text-sm text-white/70">Instagram · Facebook · WhatsApp</p>
        <p class="mt-3 text-sm text-white/70">{{ location }}</p>
      </div>
    </div>
    <div class="border-t border-white/10 py-5 text-center text-xs text-white/55">
      © {{ new Date().getFullYear() }} {{ t('footer.copyright') }}
    </div>
  </footer>
</template>
