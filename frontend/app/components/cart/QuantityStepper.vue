<script setup lang="ts">
const props = withDefaults(defineProps<{ modelValue: number, min?: number, max?: number }>(), { min: 1, max: 99 })
const emit = defineEmits<{ 'update:modelValue': [value: number] }>()

function setValue(value: number) {
  emit('update:modelValue', Math.min(props.max, Math.max(props.min, value)))
}
</script>

<template>
  <div class="inline-flex min-h-11 items-center overflow-hidden rounded-lg border border-[#70453425] bg-white" aria-label="Quantity">
    <button class="grid h-11 w-10 place-items-center text-lg hover:bg-[#fbf5ee]" type="button" aria-label="Decrease quantity" @click="setValue(modelValue - 1)">−</button>
    <span class="min-w-8 text-center text-sm font-bold">{{ modelValue }}</span>
    <button class="grid h-11 w-10 place-items-center text-lg hover:bg-[#fbf5ee]" type="button" aria-label="Increase quantity" @click="setValue(modelValue + 1)">+</button>
  </div>
</template>
