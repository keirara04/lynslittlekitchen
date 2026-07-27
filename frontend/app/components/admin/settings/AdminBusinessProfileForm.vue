<script setup lang="ts">
import type { AdminSettings } from '~/types/admin'

const props = defineProps<{ settings: AdminSettings; busy: boolean; serverErrors: Record<string, string[]> }>()
const emit = defineEmits<{ save: [payload: Partial<AdminSettings>] }>()

const form = reactive({
  store_name: props.settings.store_name || '',
  business_registration_no: props.settings.business_registration_no || '',
  business_type: props.settings.business_type || '',
  established_since: props.settings.established_since || '',
  address_line1: props.settings.address_line1 || '',
  address_line2: props.settings.address_line2 || '',
  postcode: props.settings.postcode || '',
  city: props.settings.city || '',
  state: props.settings.state || '',
})

function submit() {
  emit('save', { ...form })
}
</script>

<template>
  <form class="admin-panel" @submit.prevent="submit">
    <div class="admin-form-grid">
      <label class="admin-field"><span>Store Name</span><input v-model.trim="form.store_name"><small v-if="serverErrors.store_name" class="admin-field__error">{{ serverErrors.store_name[0] }}</small></label>
      <label class="admin-field"><span>Business Registration No.</span><input v-model.trim="form.business_registration_no"></label>
      <label class="admin-field"><span>Business Type</span><select v-model="form.business_type"><option value="">Select type</option><option value="Sole Proprietorship">Sole Proprietorship</option><option value="Partnership">Partnership</option><option value="Enterprise">Enterprise</option><option value="Sdn Bhd">Sdn Bhd</option></select></label>
      <label class="admin-field"><span>Established Since</span><input v-model="form.established_since" type="date"></label>
      <label class="admin-field"><span>Address Line 1</span><input v-model.trim="form.address_line1"></label>
      <label class="admin-field"><span>Address Line 2</span><input v-model.trim="form.address_line2"></label>
      <label class="admin-field"><span>Postcode</span><input v-model.trim="form.postcode"></label>
      <label class="admin-field"><span>City</span><input v-model.trim="form.city"></label>
      <label class="admin-field"><span>State</span><input v-model.trim="form.state"></label>
      <div class="admin-form-actions">
        <button class="admin-button admin-button--primary" type="submit" :disabled="busy">{{ busy ? 'Saving…' : 'Save Changes' }}</button>
      </div>
    </div>
  </form>
</template>
