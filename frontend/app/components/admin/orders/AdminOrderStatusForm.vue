<script setup lang="ts">
import { humanizeStatus, orderActions } from '~/utils/admin.mjs'
import type { AdminOrder, OrderStatus } from '~/types/admin'

const props = defineProps<{ order: AdminOrder; busy?: boolean; error?: string }>()
const emit = defineEmits<{ update: [status: OrderStatus] }>()
const selected = ref<OrderStatus | ''>('')
const actions = computed(() => orderActions(props.order))
watch(() => props.order.order_status, () => { selected.value = '' })
</script>

<template>
  <section class="admin-status-form admin-panel">
    <p class="admin-kicker">Update order</p>
    <h2>Next fulfilment status</h2>
    <template v-if="actions.length">
      <label class="admin-field"><span>Allowed next status</span><select v-model="selected"><option value="">Select status</option><option v-for="status in actions" :key="status" :value="status">{{ humanizeStatus(status) }}</option></select></label>
      <p v-if="error" class="admin-form-error" role="alert">{{ error }}</p>
      <button class="admin-button admin-button--primary" type="button" :disabled="!selected || busy" @click="selected && emit('update', selected)">{{ busy ? 'Updating…' : 'Update Status' }}</button>
    </template>
    <p v-else class="admin-editor-hint">This order is terminal. No further status changes are allowed.</p>
  </section>
</template>
