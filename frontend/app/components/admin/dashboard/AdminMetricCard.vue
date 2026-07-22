<script setup lang="ts">
defineProps<{
  label: string
  value: string | number
  note?: string
  accent?: 'clay' | 'sage' | 'amber'
}>()
</script>

<template>
  <article class="admin-metric-card" :class="`admin-metric-card--${accent || 'clay'}`">
    <div>
      <p>{{ label }}</p>
      <strong>{{ value }}</strong>
      <small v-if="note">{{ note }}</small>
    </div>
    <span class="admin-metric-card__stamp" aria-hidden="true">⌇</span>
  </article>
</template>
