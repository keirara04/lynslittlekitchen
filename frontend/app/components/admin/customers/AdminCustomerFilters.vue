<script setup lang="ts">
defineProps<{ search: string; status: string }>()
defineEmits<{ search: [value: string]; change: [key: string, value: string] }>()
</script>

<template>
  <div class="admin-filters admin-customer-filters admin-panel">
    <label class="admin-search-field"><span class="sr-only">Search customers</span><input :value="search" type="search" placeholder="Search customers by name, email or phone…" @input="$emit('search', ($event.target as HTMLInputElement).value)"></label>
    <label><span class="sr-only">Status</span><select :value="status" @change="$emit('change', 'status', ($event.target as HTMLSelectElement).value)"><option value="">All Status</option><option value="active">Active</option><option value="inactive">Inactive</option></select></label>
  </div>
</template>
