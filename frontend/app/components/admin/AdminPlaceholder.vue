<script setup lang="ts">
defineProps<{ title: string; description: string }>()
</script>

<template>
  <div class="admin-page">
    <header class="admin-page-heading">
      <div>
        <p class="admin-kicker">Workspace module</p>
        <h1>{{ title }}</h1>
        <p>{{ description }}</p>
      </div>
    </header>
    <section class="admin-placeholder-panel">
      <span class="admin-placeholder-panel__seal" aria-hidden="true">L</span>
      <p class="admin-kicker">Coming in a future phase</p>
      <h2>{{ title }} is not connected yet</h2>
      <p>This page is ready in the admin navigation, but it will stay read-only until its Laravel API is available.</p>
      <NuxtLink to="/admin" class="admin-button admin-button--primary">Back to dashboard</NuxtLink>
    </section>
  </div>
</template>
