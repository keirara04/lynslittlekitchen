<script setup lang="ts">
import type { AdminSettings } from '~/types/admin'

const props = defineProps<{ settings: AdminSettings; busy: boolean; serverErrors: Record<string, string[]> }>()
const emit = defineEmits<{ save: [payload: Partial<AdminSettings>] }>()

const form = reactive({
  contact_phone: props.settings.contact_phone || '',
  contact_email: props.settings.contact_email || '',
  operating_hours: props.settings.operating_hours || '',
  operating_hours_ms: props.settings.operating_hours_ms || '',
})

function submit() {
  emit('save', { ...form })
}
</script>

<template>
  <form class="admin-panel" @submit.prevent="submit">
    <div class="admin-form-grid">
      <label class="admin-field"><span>Phone</span><input v-model.trim="form.contact_phone"></label>
      <label class="admin-field"><span>Email</span><input v-model.trim="form.contact_email" type="email"><small v-if="serverErrors.contact_email" class="admin-field__error">{{ serverErrors.contact_email[0] }}</small></label>
      <label class="admin-field"><span>Operating Hours</span><input v-model.trim="form.operating_hours" placeholder="e.g. Mon–Sat, 9am–6pm"></label>
      <label class="admin-field"><span>Operating Hours (Malay)</span><input v-model.trim="form.operating_hours_ms" placeholder="Optional"></label>
      <div class="admin-form-actions">
        <button class="admin-button admin-button--primary" type="submit" :disabled="busy">{{ busy ? 'Saving…' : 'Save Changes' }}</button>
      </div>
    </div>
  </form>
</template>
