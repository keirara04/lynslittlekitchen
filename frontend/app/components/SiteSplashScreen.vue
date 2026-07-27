<script setup lang="ts">
import { resolveProductImage } from '~/utils/storefront.mjs'
import type { PaginatedProducts } from '~/types/catalog'
import type { StoreSettings } from '~/composables/useStoreSettings'

const SESSION_KEY = 'llk-splash-shown'
const MIN_VISIBLE_MS = 1600
const MAX_WAIT_MS = 6000

const visible = ref(true)
if (import.meta.client) {
  visible.value = sessionStorage.getItem(SESSION_KEY) !== '1'
}

function loadImage(src: string) {
  return new Promise<void>((resolve) => {
    const img = new Image()
    img.onload = () => resolve()
    img.onerror = () => resolve()
    img.src = src
  })
}

async function preloadAssets() {
  const config = useRuntimeConfig()

  const [productsResult, settingsResult] = await Promise.allSettled([
    $fetch<PaginatedProducts>('/products', { baseURL: config.public.apiBase, query: { per_page: 50 } }),
    $fetch<StoreSettings>('/store-settings', { baseURL: config.public.apiBase }),
  ])

  const imageUrls = new Set<string>()

  if (productsResult.status === 'fulfilled') {
    for (const product of productsResult.value.data) {
      imageUrls.add(resolveProductImage(product.slug, product.images))
    }
  }

  if (settingsResult.status === 'fulfilled') {
    const settings = settingsResult.value
    if (settings.featured_hero_product?.image_url) imageUrls.add(settings.featured_hero_product.image_url)
    if (settings.featured_banner_product?.image_url) imageUrls.add(settings.featured_banner_product.image_url)
  }

  await Promise.allSettled([...imageUrls].map(loadImage))
}

onMounted(() => {
  if (!visible.value) return

  const minDelay = new Promise(resolve => setTimeout(resolve, MIN_VISIBLE_MS))
  const timeoutGuard = new Promise(resolve => setTimeout(resolve, MAX_WAIT_MS))

  Promise.all([minDelay, Promise.race([preloadAssets().catch(() => {}), timeoutGuard])]).then(() => {
    visible.value = false
    sessionStorage.setItem(SESSION_KEY, '1')
  })
})
</script>

<template>
  <Transition name="splash-fade">
    <div v-if="visible" class="splash-screen" role="status" aria-live="polite" aria-label="Loading Lyn's Little Kitchen">
      <div class="splash-oven">
        <span class="splash-cookie splash-cookie--1">🍪</span>
        <span class="splash-cookie splash-cookie--2">🍪</span>
        <span class="splash-cookie splash-cookie--3">🍪</span>
        <span class="splash-steam splash-steam--1" aria-hidden="true" />
        <span class="splash-steam splash-steam--2" aria-hidden="true" />
      </div>
      <p class="splash-title">Lyn's Little Kitchen</p>
      <p class="splash-subtitle">Baking something delicious…</p>
    </div>
  </Transition>
</template>

<style scoped>
.splash-screen {
  position: fixed;
  inset: 0;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: .9rem;
  background:
    radial-gradient(circle at 8% 3%, rgba(232, 196, 179, .35), transparent 28rem),
    var(--parchment, #fbf5ee);
}

.splash-oven {
  position: relative;
  display: flex;
  align-items: flex-end;
  gap: .6rem;
  height: 4.5rem;
}

.splash-cookie {
  font-size: 2.4rem;
  line-height: 1;
  animation: splash-bounce 1.1s ease-in-out infinite;
}

.splash-cookie--2 { animation-delay: .15s; }
.splash-cookie--3 { animation-delay: .3s; }

.splash-steam {
  position: absolute;
  top: -1.6rem;
  width: .5rem;
  height: 1.6rem;
  border-radius: 999px;
  background: rgba(168, 95, 76, .28);
  filter: blur(2px);
  animation: splash-steam 1.6s ease-in-out infinite;
}

.splash-steam--1 { left: 30%; animation-delay: 0s; }
.splash-steam--2 { left: 62%; animation-delay: .5s; }

.splash-title {
  font-family: var(--serif, Georgia, serif);
  font-size: 1.3rem;
  color: var(--cocoa, #44281f);
  margin: 0;
}

.splash-subtitle {
  font-size: .78rem;
  letter-spacing: .02em;
  color: var(--muted, #816b61);
  margin: 0;
}

@keyframes splash-bounce {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-.6rem); }
}

@keyframes splash-steam {
  0% { opacity: 0; transform: translateY(0) scale(1); }
  30% { opacity: .8; }
  100% { opacity: 0; transform: translateY(-1.4rem) scale(1.4); }
}

.splash-fade-leave-active { transition: opacity .4s ease; }
.splash-fade-leave-to { opacity: 0; }
</style>
