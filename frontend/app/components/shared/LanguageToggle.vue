<script setup lang="ts">
const { locale, setLocale } = useI18n()
</script>

<template>
  <div class="flex items-center rounded-full border border-[#70453433] bg-white/70 p-0.5 text-[10px] font-bold uppercase tracking-wide">
    <button
      type="button"
      class="rounded-full px-2.5 py-1.5 transition-colors"
      :class="locale === 'en' ? 'bg-[#a85f4c] text-white' : 'text-stone-600 hover:text-[#a85f4c]'"
      @click="setLocale('en')"
    >
      EN
    </button>
    <button
      type="button"
      class="rounded-full px-2.5 py-1.5 transition-colors"
      :class="locale === 'ms' ? 'bg-[#a85f4c] text-white' : 'text-stone-600 hover:text-[#a85f4c]'"
      @click="setLocale('ms')"
    >
      MY
    </button>
  </div>
</template>
