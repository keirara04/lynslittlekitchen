<script setup lang="ts">
const { t } = useI18n()
const items = computed(() => [
  { icon: '♨', title: t('trustStrip.freshlyBaked.title'), text: t('trustStrip.freshlyBaked.text') },
  { icon: '✓', title: t('trustStrip.qualityIngredients.title'), text: t('trustStrip.qualityIngredients.text') },
  { icon: '▣', title: t('trustStrip.securePayment.title'), text: t('trustStrip.securePayment.text') },
  { icon: '⌂', title: t('trustStrip.delivery.title'), text: t('trustStrip.delivery.text') },
])
</script>

<template>
  <div class="trust-strip">
    <div v-for="item in items" :key="item.title" class="trust-item">
      <span class="trust-icon" aria-hidden="true">{{ item.icon }}</span>
      <span>
        <strong class="block text-xs">{{ item.title }}</strong>
        <small class="mt-0.5 block text-[10px] text-stone-500">{{ item.text }}</small>
      </span>
    </div>
  </div>
</template>
