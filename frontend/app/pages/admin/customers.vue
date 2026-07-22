<script setup lang="ts">
definePageMeta({ layout: 'admin', middleware: 'admin-auth' })
useSeoMeta({ title: "Customers | Lyn's Admin", robots: 'noindex, nofollow' })
</script>

<template>
  <AdminPlaceholder title="Customers" description="Customer profiles, order history, and spending summaries will live here." />
</template>
