<script setup lang="ts">
useSeoMeta({ title: "How to order | Lyn's Little Kitchen", description: 'Choose your cookies, check out and follow your fresh batch from our Jasin kitchen.' })

const steps = [
  { icon: '🛒', title: 'Choose your cookies', text: 'Browse the menu and pick your favourite pack.' },
  { icon: '✎', title: 'Add your details', text: 'Tell us where and when you would like your order.' },
  { icon: '▣', title: 'Confirm your order', text: 'Review the total and create your order securely.' },
  { icon: '♨', title: 'We bake', text: 'Your cookies are baked fresh—not pulled from a shelf.' },
  { icon: '⌂', title: 'Delivery or pickup', text: 'Receive your box around Jasin or collect it.' },
]
</script>

<template>
  <div>
    <section class="container-shell py-12 text-center sm:py-16">
      <span class="eyebrow">No complicated recipe</span>
      <h1 class="display-title mx-auto mt-4 max-w-3xl text-5xl sm:text-7xl">Simple steps to get your cookies</h1>
      <p class="mx-auto mt-5 max-w-xl text-sm leading-7 text-stone-600">From the first craving to a warm box at your door, ordering takes only a few minutes.</p>
    </section>

    <section class="container-shell pb-16">
      <div class="paper-card grid gap-0 overflow-hidden md:grid-cols-5">
        <article v-for="(step, index) in steps" :key="step.title" class="relative border-b border-[#70453418] p-6 text-center last:border-0 md:border-b-0 md:border-r md:last:border-r-0">
          <span class="absolute left-3 top-3 font-serif text-sm text-[#a85f4c]">0{{ index + 1 }}</span>
          <span class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-[#f3ded2] text-2xl" aria-hidden="true">{{ step.icon }}</span>
          <h2 class="mt-5 font-serif text-xl leading-tight">{{ step.title }}</h2>
          <p class="mt-2 text-xs leading-5 text-stone-500">{{ step.text }}</p>
        </article>
      </div>
      <div class="mt-6 rounded-xl border border-[#70453418] bg-[#f2e2d7] px-5 py-4 text-center text-sm font-semibold">Local delivery fees start from RM3.00 in Jasin.</div>
      <div class="mt-8 text-center"><NuxtLink to="/shop" class="btn-primary">Start your order</NuxtLink></div>
    </section>
  </div>
</template>
