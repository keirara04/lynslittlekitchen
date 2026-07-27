<script setup lang="ts">
import type { AdminSettings } from '~/types/admin'

const props = defineProps<{ settings: AdminSettings; busy: boolean; serverErrors: Record<string, string[]> }>()
const emit = defineEmits<{ save: [payload: Partial<AdminSettings>] }>()

const form = reactive({
  bank_name: props.settings.bank_name || '',
  bank_account_name: props.settings.bank_account_name || '',
  bank_account_number: props.settings.bank_account_number || '',
  duitnow_id: props.settings.duitnow_id || '',
})

function submit() {
  emit('save', { ...form })
}
</script>

<template>
  <form class="admin-panel" @submit.prevent="submit">
    <div class="admin-form-grid">
      <label class="admin-field"><span>Bank Name</span><input v-model.trim="form.bank_name"></label>
      <label class="admin-field"><span>Bank Account Name</span><input v-model.trim="form.bank_account_name"></label>
      <label class="admin-field"><span>Bank Account Number</span><input v-model.trim="form.bank_account_number"></label>
      <label class="admin-field"><span>DuitNow ID</span><input v-model.trim="form.duitnow_id"></label>
      <div class="admin-form-actions">
        <button class="admin-button admin-button--primary" type="submit" :disabled="busy">{{ busy ? 'Saving…' : 'Save Changes' }}</button>
      </div>
    </div>
  </form>
</template>
