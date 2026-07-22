<script setup lang="ts">
useSeoMeta({ title: "Contact | Lyn's Little Kitchen" })
</script>

<template>
  <div class="container-shell py-12 sm:py-16">
    <div class="grid gap-8 lg:grid-cols-[.8fr_1.2fr]">
      <section>
        <span class="eyebrow">Say hello</span>
        <h1 class="display-title mt-4 text-5xl sm:text-6xl">Questions before you order?</h1>
        <p class="mt-5 text-sm leading-7 text-stone-600">Ask about delivery, allergens, larger orders or the next available bake date.</p>
        <div class="mt-8 grid gap-4 text-sm">
          <div class="paper-card p-5"><p class="text-xs font-bold uppercase tracking-wider text-[#a85f4c]">Location</p><p class="mt-2">Jasin, Melaka, Malaysia</p></div>
          <div class="paper-card p-5"><p class="text-xs font-bold uppercase tracking-wider text-[#a85f4c]">Response hours</p><p class="mt-2">Monday–Saturday · 9am–6pm</p></div>
        </div>
      </section>

      <form class="paper-card p-5 sm:p-7" @submit.prevent>
        <h2 class="font-serif text-2xl">Send the kitchen a note</h2>
        <p class="mt-2 text-xs text-stone-500">This form is a visual preview until messaging is connected.</p>
        <div class="mt-6 grid gap-5 sm:grid-cols-2">
          <label class="form-label">Name<input class="form-input" autocomplete="name"></label>
          <label class="form-label">Phone number<input class="form-input" autocomplete="tel" inputmode="tel"></label>
          <label class="form-label sm:col-span-2">Email<input class="form-input" type="email" autocomplete="email"></label>
          <label class="form-label sm:col-span-2">Your message<textarea class="form-textarea" /></label>
        </div>
        <button class="btn-primary mt-6" type="submit" disabled>Messaging coming soon</button>
      </form>
    </div>
  </div>
</template>
