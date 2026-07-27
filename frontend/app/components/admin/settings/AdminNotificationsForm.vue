<script setup lang="ts">
import type { AdminSettings } from '~/types/admin'

const props = defineProps<{ settings: AdminSettings; busy: boolean; serverErrors: Record<string, string[]> }>()
const emit = defineEmits<{ save: [payload: Partial<AdminSettings>] }>()

const form = reactive({
  alert_email: props.settings.alert_email || '',
  new_order_email_enabled: props.settings.new_order_email_enabled,
  low_stock_threshold: props.settings.low_stock_threshold ?? 0,
})

function submit() {
  emit('save', { ...form })
}
</script>

<template>
  <form class="admin-panel" @submit.prevent="submit">
    <div class="admin-form-grid">
      <label class="admin-field"><span>Alert Email</span><input v-model.trim="form.alert_email" type="email"><small v-if="serverErrors.alert_email" class="admin-field__error">{{ serverErrors.alert_email[0] }}</small></label>
      <label class="admin-field"><span>New Order Email Alerts</span><select v-model="form.new_order_email_enabled"><option :value="true">Enabled</option><option :value="false">Disabled</option></select></label>
      <label class="admin-field"><span>Low Stock Alert Threshold</span><input v-model.number="form.low_stock_threshold" type="number" min="0" step="1"></label>
      <div class="admin-form-actions">
        <button class="admin-button admin-button--primary" type="submit" :disabled="busy">{{ busy ? 'Saving…' : 'Save Changes' }}</button>
      </div>
    </div>
  </form>
</template>
