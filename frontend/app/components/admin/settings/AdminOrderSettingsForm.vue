<script setup lang="ts">
import type { AdminSettings } from '~/types/admin'

const props = defineProps<{ settings: AdminSettings; busy: boolean; serverErrors: Record<string, string[]> }>()
const emit = defineEmits<{ save: [payload: Partial<AdminSettings>] }>()

const form = reactive({
  min_order_amount: props.settings.min_order_amount ?? 0,
  lead_time_days: props.settings.lead_time_days ?? 0,
  order_cutoff_time: (props.settings.order_cutoff_time || '').slice(0, 5),
  allow_pickup: props.settings.allow_pickup,
  allow_delivery: props.settings.allow_delivery,
})

function submit() {
  emit('save', { ...form })
}
</script>

<template>
  <form class="admin-panel" @submit.prevent="submit">
    <div class="admin-form-grid">
      <label class="admin-field"><span>Minimum Order Amount (RM)</span><input v-model.number="form.min_order_amount" type="number" min="0" step="0.01"><small v-if="serverErrors.min_order_amount" class="admin-field__error">{{ serverErrors.min_order_amount[0] }}</small></label>
      <label class="admin-field"><span>Prep Lead Time (days)</span><input v-model.number="form.lead_time_days" type="number" min="0" step="1"></label>
      <label class="admin-field"><span>Order Cutoff Time</span><input v-model="form.order_cutoff_time" type="time"><small v-if="serverErrors.order_cutoff_time" class="admin-field__error">{{ serverErrors.order_cutoff_time[0] }}</small></label>
      <label class="admin-field"><span>Allow Pickup</span><select v-model="form.allow_pickup"><option :value="true">Enabled</option><option :value="false">Disabled</option></select></label>
      <label class="admin-field"><span>Allow Delivery</span><select v-model="form.allow_delivery"><option :value="true">Enabled</option><option :value="false">Disabled</option></select></label>
      <div class="admin-form-actions">
        <button class="admin-button admin-button--primary" type="submit" :disabled="busy">{{ busy ? 'Saving…' : 'Save Changes' }}</button>
      </div>
    </div>
  </form>
</template>
