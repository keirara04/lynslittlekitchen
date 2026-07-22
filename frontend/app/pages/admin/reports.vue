<script setup lang="ts">
definePageMeta({ layout: 'admin', middleware: 'admin-auth' })
useSeoMeta({ title: "Reports | Lyn's Admin", robots: 'noindex, nofollow' })
</script>

<template>
  <AdminPlaceholder title="Reports" description="Detailed sales and product performance reports require time-series analytics APIs." />
</template>
