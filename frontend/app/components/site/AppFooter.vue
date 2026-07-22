<template>
  <footer class="site-footer mt-20">
    <div class="container-shell grid gap-10 py-12 sm:grid-cols-2 lg:grid-cols-4">
      <div>
        <div class="font-serif text-3xl">LYN’S</div>
        <p class="mt-1 text-sm italic text-[#f4cbbb]">Little Kitchen</p>
        <p class="mt-5 max-w-xs text-sm leading-6 text-white/70">
          Small-batch cookies made with love in Jasin, Melaka.
        </p>
      </div>
      <div>
        <h2 class="text-xs font-bold uppercase tracking-[.14em]">Quick links</h2>
        <div class="mt-4 grid gap-2 text-sm">
          <NuxtLink to="/">Home</NuxtLink><NuxtLink to="/shop">Shop</NuxtLink><NuxtLink to="/about">About us</NuxtLink><NuxtLink to="/how-to-order">How to order</NuxtLink>
        </div>
      </div>
      <div>
        <h2 class="text-xs font-bold uppercase tracking-[.14em]">Customer care</h2>
        <div class="mt-4 grid gap-2 text-sm">
          <NuxtLink to="/track-order">Track order</NuxtLink><NuxtLink to="/contact">Delivery information</NuxtLink><NuxtLink to="/contact">Contact</NuxtLink>
        </div>
      </div>
      <div>
        <h2 class="text-xs font-bold uppercase tracking-[.14em]">Let’s connect</h2>
        <p class="mt-4 text-sm text-white/70">Instagram · Facebook · WhatsApp</p>
        <p class="mt-3 text-sm text-white/70">Jasin, Melaka, Malaysia</p>
      </div>
    </div>
    <div class="border-t border-white/10 py-5 text-center text-xs text-white/55">
      © {{ new Date().getFullYear() }} Lyn’s Little Kitchen. Baked with care.
    </div>
  </footer>
</template>
