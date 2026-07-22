<script setup lang="ts">
defineProps<{ title: string; description: string }>()
</script>

<template>
  <section class="admin-empty-state">
    <span class="admin-empty-state__mark" aria-hidden="true">⌇</span>
    <h2>{{ title }}</h2>
    <p>{{ description }}</p>
    <div v-if="$slots.default" class="admin-empty-state__action"><slot /></div>
  </section>
</template>
