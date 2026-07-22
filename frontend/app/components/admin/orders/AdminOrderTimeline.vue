<script setup lang="ts">
import { fulfilmentStatuses, humanizeStatus, progressIndex } from '~/utils/admin.mjs'
import type { OrderStatus } from '~/types/admin'

const props = defineProps<{ status: OrderStatus }>()
const current = computed(() => progressIndex(props.status))
</script>

<template>
  <section class="admin-order-timeline admin-panel" :class="{ 'admin-order-timeline--terminal': current < 0 }">
    <header><div><p class="admin-kicker">Fulfilment progress</p><h2>Order status</h2></div><AdminStatusBadge :status="status" /></header>
    <ol v-if="current >= 0">
      <li v-for="(step, index) in fulfilmentStatuses" :key="step" :class="{ complete: index < current, current: index === current }">
        <span>{{ index < current ? '✓' : index + 1 }}</span>
        <small>{{ humanizeStatus(step) }}</small>
      </li>
    </ol>
    <p v-else>This order is in a terminal {{ humanizeStatus(status).toLowerCase() }} state and cannot continue through fulfilment.</p>
  </section>
</template>
