<script setup lang="ts">
import { humanizeStatus } from '~/utils/admin.mjs'

const props = defineProps<{ status: string }>()

const tone = computed(() => {
  if (['paid', 'active', 'completed'].includes(props.status)) return 'success'
  if (['preparing', 'baking', 'packing', 'out_for_delivery', 'unpaid'].includes(props.status)) return 'warning'
  if (['rejected', 'cancelled', 'refunded', 'inactive'].includes(props.status)) return 'danger'
  return 'neutral'
})
</script>

<template>
  <span class="admin-status" :class="`admin-status--${tone}`">{{ humanizeStatus(status) }}</span>
</template>
