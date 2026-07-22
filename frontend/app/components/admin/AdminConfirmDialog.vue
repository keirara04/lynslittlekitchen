<script setup lang="ts">
const props = withDefaults(defineProps<{
  open: boolean
  title: string
  description: string
  confirmLabel?: string
  busy?: boolean
}>(), {
  confirmLabel: 'Confirm',
  busy: false,
})

const emit = defineEmits<{ confirm: []; close: [] }>()
const panel = ref<HTMLElement | null>(null)
let previousFocus: HTMLElement | null = null

watch(() => props.open, async (open) => {
  if (open && import.meta.client) {
    previousFocus = document.activeElement as HTMLElement
    await nextTick()
    panel.value?.querySelector<HTMLElement>('button')?.focus()
  }
  else if (!open && previousFocus) {
    previousFocus.focus()
    previousFocus = null
  }
})

function onKeydown(event: KeyboardEvent) {
  if (event.key === 'Escape' && !props.busy) emit('close')
  if (event.key !== 'Tab' || !panel.value) return

  const controls = [...panel.value.querySelectorAll<HTMLElement>('button:not([disabled])')]
  if (!controls.length) return
  const first = controls[0]
  const last = controls[controls.length - 1]

  if (event.shiftKey && document.activeElement === first) {
    event.preventDefault()
    last?.focus()
  }
  else if (!event.shiftKey && document.activeElement === last) {
    event.preventDefault()
    first?.focus()
  }
}
</script>

<template>
  <Teleport to="body">
    <div v-if="open" class="admin-dialog-backdrop" @mousedown.self="!busy && emit('close')">
      <section ref="panel" class="admin-dialog" role="dialog" aria-modal="true" :aria-labelledby="`${$attrs.id || 'admin-confirm'}-title`" @keydown="onKeydown">
        <p class="admin-kicker">Please confirm</p>
        <h2 :id="`${$attrs.id || 'admin-confirm'}-title`">{{ title }}</h2>
        <p>{{ description }}</p>
        <div class="admin-dialog__actions">
          <button class="admin-button admin-button--secondary" type="button" :disabled="busy" @click="emit('close')">Cancel</button>
          <button class="admin-button admin-button--danger" type="button" :disabled="busy" @click="emit('confirm')">{{ busy ? 'Working…' : confirmLabel }}</button>
        </div>
      </section>
    </div>
  </Teleport>
</template>
