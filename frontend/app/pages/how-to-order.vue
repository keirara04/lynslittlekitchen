<script setup lang="ts">
const { t, tm } = useI18n()

useSeoMeta({
  title: "How to order | Lyn's Little Kitchen",
  description: 'Five simple steps from choosing your cookies to receiving a freshly baked box around Jasin.',
})

interface OrderStep {
  title: string
  text: string
}

const stepMeta = [
  { number: '01', image: '/images/how-to-order/choose-cookies.png', alt: 'An open gift box filled with assorted cookies', side: 'left' as const },
  { number: '02', image: '/images/how-to-order/add-details.png', alt: 'A handwritten order note being filled in', side: 'right' as const },
  { number: '03', image: '/images/how-to-order/confirm-order.png', alt: 'A Lyn’s Little Kitchen box being sealed by hand', side: 'left' as const },
  { number: '04', image: '/images/how-to-order/we-bake.png', alt: 'Fresh cookies baking on an oven rack', side: 'right' as const },
  { number: '05', image: '/images/how-to-order/delivery-pickup.png', alt: 'A ribboned Lyn’s Little Kitchen box ready for delivery', side: 'left' as const },
]

const steps = computed(() => {
  const translated = tm('howToOrder.steps') as unknown as OrderStep[]
  return stepMeta.map((meta, index) => ({ ...meta, ...translated[index] }))
})
</script>

<template>
  <main class="order-page">
    <section class="order-hero">
      <div class="hero-copy">
        <p class="hero-kicker">
          {{ t('howToOrder.kicker') }}
          <svg viewBox="0 0 44 24" aria-hidden="true">
            <path d="M3 19C13 15 23 10 40 3M12 15C10 9 12 6 17 4c1 5-1 8-5 11Zm9-5c0-5 3-8 8-9 0 5-3 8-8 9Zm8-3c4 0 8 2 10 6-5 1-8-1-10-6ZM7 18c-3-1-5-4-5-8 5 1 7 4 5 8Z" />
          </svg>
        </p>

        <h1>{{ t('howToOrder.heroTitleLine1') }}<br>{{ t('howToOrder.heroTitleLine2') }}</h1>

        <div class="heart-rule" aria-hidden="true">
          <span />
          <b>♥</b>
          <span />
        </div>

        <p class="hero-intro">{{ t('howToOrder.heroIntro') }}</p>
        <NuxtLink to="/shop" class="order-button">{{ t('howToOrder.startYourOrder') }}</NuxtLink>

        <svg class="hero-leaves" viewBox="0 0 120 160" aria-hidden="true">
          <path d="M8 154C39 116 45 69 57 12M32 115C16 107 11 94 13 79c16 7 22 20 19 36Zm14-37C32 67 31 52 39 38c13 10 15 23 7 40Zm8-20c16-2 28-10 36-25-17-2-29 6-36 25Zm-20 65c18 1 34-4 47-17-17-5-34 1-47 17Z" />
        </svg>
      </div>

      <figure class="hero-image">
        <img src="/images/how-to-order/hero.png" alt="A stack of chocolate chip cookies beside a ribboned Lyn’s Little Kitchen gift box">
      </figure>
    </section>

    <section class="journey-section" aria-labelledby="journey-heading">
      <h2 id="journey-heading" class="sr-only">How your order is made</h2>

      <svg class="journey-path" viewBox="0 0 1000 470" preserveAspectRatio="none" aria-hidden="true">
        <path d="M376 51C520 51 542 52 551 75c8 23-85 21-54 65 18 25 195-13 255 7M626 167c-125 4-195 20-252 39 92 5 199 25 180 67-11 25-94 29-84 58 10 28 184 14 191 67 3 18-4 29-17 40" />
        <path class="path-heart" d="M703 143c-8-14-25-5-19 8l19 17 19-17c6-13-11-22-19-8ZM527 271c-8-14-25-5-19 8l19 17 19-17c6-13-11-22-19-8Z" />
      </svg>

      <div class="journey-grid">
        <article
          v-for="step in steps"
          :key="step.number"
          class="journey-step"
          :class="`journey-step--${step.side}`"
        >
          <img class="step-image" :src="step.image" :alt="step.alt">
          <div class="step-card">
            <span class="step-number">{{ step.number }}</span>
            <h3>{{ step.title }}</h3>
            <p>{{ step.text }}</p>
            <svg class="step-sprig" viewBox="0 0 45 45" aria-hidden="true">
              <path d="M5 40C17 28 25 17 38 5M15 31C8 29 6 24 7 19c7 2 10 6 8 12Zm8-9c-3-6-1-11 4-15 4 6 2 11-4 15Zm6-6c6 1 11-1 15-6-6-3-11-1-15 6Z" />
            </svg>
          </div>
        </article>
      </div>
    </section>

    <section class="delivery-banner">
      <div class="van-art" aria-hidden="true">
        <svg viewBox="0 0 280 110">
          <path d="M11 80h28M8 72h45M49 90h17m116 0h26m18 0h35M70 84V38c0-7 5-12 12-12h79c9 0 17 4 22 11l24 31v22M70 82h20m73 0h22M160 27v42h44M169 37h13l18 25h-31ZM98 45h37M98 53h31M112 83a17 17 0 1 0 0 34 17 17 0 0 0 0-34Zm87 0a17 17 0 1 0 0 34 17 17 0 0 0 0-34Z" />
          <text x="95" y="68">LYN'S</text>
          <path d="M32 60C24 52 20 43 21 32c10 6 14 15 11 28Zm8-4c11-5 17-13 19-24-12 2-19 10-19 24ZM234 73c6-12 14-20 25-24-1 12-10 20-25 24Zm7 5c12-1 22 3 29 11-12 4-22 0-29-11Z" />
        </svg>
      </div>

      <p>{{ t('howToOrder.bannerLine1') }}<br>{{ t('howToOrder.bannerLine2') }}</p>
      <NuxtLink to="/shop" class="order-button">{{ t('howToOrder.chooseYourCookies') }}</NuxtLink>

      <svg class="banner-sprig" viewBox="0 0 100 70" aria-hidden="true">
        <path d="M7 60C31 49 53 31 84 8M30 48C20 41 18 32 21 23c10 5 14 14 9 25Zm20-13c-3-11 2-20 12-27 5 11 1 20-12 27Zm14-10c11 0 20 5 26 14-12 3-21-2-26-14Z" />
      </svg>
    </section>
  </main>
</template>

<style scoped>
.order-page {
  --order-ink: #40251c;
  --order-muted: #79655d;
  --order-clay: #b84d2d;
  --order-line: #dfb7a6;
  position: relative;
  overflow: hidden;
  color: var(--order-ink);
  background:
    radial-gradient(circle at 9% 8%, rgb(229 194 168 / 14%), transparent 24rem),
    linear-gradient(90deg, rgb(255 253 249 / 96%), rgb(252 248 242 / 92%));
}

.order-hero {
  display: grid;
  min-height: 378px;
  grid-template-columns: 49.2% 50.8%;
  border-bottom: 1px solid rgb(112 69 52 / 14%);
}

.hero-copy {
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 38px clamp(32px, 8.9vw, 170px) 30px;
}

.hero-kicker {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 0 0 18px;
  color: var(--order-clay);
  font-size: 14px;
  font-weight: 700;
  letter-spacing: .18em;
  text-transform: uppercase;
}

.hero-kicker svg {
  width: 34px;
  fill: none;
  stroke: #c98f78;
  stroke-linecap: round;
  stroke-linejoin: round;
  stroke-width: 1;
}

.hero-copy h1 {
  margin: 0;
  font-family: Georgia, 'Times New Roman', serif;
  font-size: clamp(48px, 4.45vw, 72px);
  font-weight: 400;
  letter-spacing: -.047em;
  line-height: .95;
}

.heart-rule {
  display: grid;
  width: min(100%, 478px);
  grid-template-columns: 1fr auto 1fr;
  align-items: center;
  gap: 10px;
  margin: 20px 0 10px;
  color: var(--order-clay);
}

.heart-rule span {
  height: 1px;
  background: rgb(112 69 52 / 13%);
}

.heart-rule b {
  font-size: 12px;
}

.hero-intro {
  margin: 0 0 20px;
  color: var(--order-muted);
  font-size: 14px;
  letter-spacing: .025em;
}

.order-button {
  display: inline-flex;
  width: fit-content;
  min-height: 48px;
  align-items: center;
  justify-content: center;
  border-radius: 7px;
  padding: 0 27px;
  color: #fff;
  background: linear-gradient(135deg, #b94c2c, #aa4023);
  box-shadow: 0 9px 18px rgb(131 53 32 / 22%);
  font-size: 14px;
  font-weight: 700;
  transition: transform .2s ease, box-shadow .2s ease;
}

.order-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 22px rgb(131 53 32 / 28%);
}

.hero-leaves {
  position: absolute;
  bottom: -1px;
  left: 0;
  width: 112px;
  fill: none;
  stroke: #d7b6a7;
  stroke-linecap: round;
  stroke-linejoin: round;
  stroke-width: 1.2;
}

.hero-image {
  min-width: 0;
  margin: 0;
}

.hero-image img {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.journey-section {
  position: relative;
  width: min(1116px, calc(100% - 48px));
  min-height: 430px;
  margin: 0 auto;
  padding: 10px 0 8px;
}

.journey-grid {
  position: relative;
  z-index: 1;
  display: grid;
  grid-template-columns: 1fr 1fr;
  grid-template-rows: repeat(3, 1fr);
  column-gap: 124px;
}

.journey-step {
  position: relative;
  display: grid;
  min-width: 0;
  grid-template-columns: minmax(190px, 240px) minmax(210px, 242px);
  align-items: center;
  padding: 6px 0;
}

.journey-step--left {
  grid-column: 1;
}

.journey-step--right {
  grid-column: 2;
}

.journey-step:nth-child(1) {
  grid-row: 1;
}

.journey-step:nth-child(2) {
  grid-row: 1;
}

.journey-step:nth-child(3) {
  grid-row: 2;
}

.journey-step:nth-child(4) {
  grid-row: 2;
}

.journey-step:nth-child(5) {
  grid-row: 3;
}

.step-image {
  width: 100%;
  height: 128px;
  border-radius: 14px;
  object-fit: cover;
}

.step-card {
  position: relative;
  min-height: 102px;
  margin-left: -10px;
  border: 1px solid var(--order-line);
  border-radius: 11px;
  padding: 24px 38px 14px;
  background: rgb(255 251 246 / 97%);
  box-shadow: 0 3px 8px rgb(95 57 41 / 9%);
}

.step-number {
  position: absolute;
  top: -22px;
  left: 36px;
  display: grid;
  width: 39px;
  height: 39px;
  place-items: center;
  border-radius: 50%;
  color: white;
  background: linear-gradient(145deg, #c45f3e, #a94529);
  box-shadow: 0 4px 9px rgb(126 55 35 / 20%);
  font-family: Georgia, 'Times New Roman', serif;
  font-size: 17px;
}

.step-card h3 {
  margin: 0 0 4px;
  font-family: Georgia, 'Times New Roman', serif;
  font-size: 18px;
  font-weight: 400;
  line-height: 1.1;
}

.step-card p {
  max-width: 24ch;
  margin: 0;
  color: var(--order-muted);
  font-size: 12px;
  line-height: 1.55;
}

.step-sprig {
  position: absolute;
  right: 9px;
  bottom: 7px;
  width: 32px;
  fill: none;
  stroke: #c5a293;
  stroke-linecap: round;
  stroke-linejoin: round;
  stroke-width: 1;
}

.journey-path {
  position: absolute;
  z-index: 0;
  inset: 5px 5% 0;
  width: 90%;
  height: 420px;
  overflow: visible;
  fill: none;
  stroke: #c76042;
  stroke-linecap: round;
  stroke-width: 1.2;
}

.journey-path .path-heart {
  fill: #d38a71;
  stroke: none;
}

.delivery-banner {
  position: relative;
  display: grid;
  width: min(1250px, calc(100% - 48px));
  min-height: 125px;
  grid-template-columns: 300px 1fr auto 120px;
  align-items: center;
  gap: 24px;
  margin: 0 auto 16px;
  overflow: hidden;
  border: 1px solid #edc5ae;
  border-radius: 20px;
  padding: 10px 25px;
  background:
    radial-gradient(circle at 86% 14%, rgb(209 135 95 / 13%) 0 1px, transparent 2px),
    linear-gradient(105deg, #fdebdc, #f8dfce);
  background-size: 7px 7px, auto;
}

.delivery-banner::after {
  position: absolute;
  right: 7%;
  bottom: -35px;
  width: 180px;
  height: 70px;
  border-radius: 50%;
  background: rgb(224 154 117 / 12%);
  content: '';
  filter: blur(10px);
}

.delivery-banner p {
  margin: 0;
  font-family: Georgia, 'Times New Roman', serif;
  font-size: clamp(27px, 2.4vw, 38px);
  letter-spacing: -.035em;
  line-height: 1.05;
}

.van-art {
  color: #c46a49;
}

.van-art svg {
  display: block;
  width: 100%;
  max-height: 105px;
  fill: none;
  stroke: currentColor;
  stroke-linecap: round;
  stroke-linejoin: round;
  stroke-width: 1.3;
}

.van-art text {
  fill: currentColor;
  stroke: none;
  font-family: Georgia, 'Times New Roman', serif;
  font-size: 10px;
}

.banner-sprig {
  position: relative;
  z-index: 1;
  width: 88px;
  fill: none;
  stroke: #b96040;
  stroke-linecap: round;
  stroke-linejoin: round;
  stroke-width: 1.1;
}

@media (max-width: 1100px) {
  .hero-copy {
    padding-left: 50px;
    padding-right: 40px;
  }

  .journey-grid {
    column-gap: 50px;
  }

  .journey-step {
    grid-template-columns: minmax(150px, 1fr) minmax(195px, 1fr);
  }

  .step-card {
    padding-left: 24px;
    padding-right: 30px;
  }

  .delivery-banner {
    grid-template-columns: 220px 1fr auto;
  }

  .banner-sprig {
    display: none;
  }
}

@media (max-width: 800px) {
  .order-hero {
    grid-template-columns: 1fr;
  }

  .hero-copy {
    min-height: 430px;
    padding: 58px 24px 42px;
    text-align: center;
  }

  .hero-kicker,
  .heart-rule,
  .order-button {
    margin-right: auto;
    margin-left: auto;
  }

  .hero-copy h1 {
    font-size: clamp(45px, 13vw, 64px);
  }

  .hero-intro {
    max-width: 34ch;
    margin-right: auto;
    margin-left: auto;
  }

  .hero-image {
    height: min(66vw, 400px);
  }

  .journey-section {
    width: min(590px, calc(100% - 32px));
    padding: 38px 0 28px;
  }

  .journey-grid {
    display: flex;
    flex-direction: column;
    gap: 34px;
  }

  .journey-step {
    grid-template-columns: minmax(110px, 44%) 1fr;
  }

  .journey-step:nth-child(even) {
    grid-template-columns: 1fr minmax(110px, 44%);
  }

  .journey-step:nth-child(even) .step-image {
    grid-column: 2;
  }

  .journey-step:nth-child(even) .step-card {
    z-index: 1;
    grid-row: 1;
    grid-column: 1;
    margin-right: -10px;
    margin-left: 0;
  }

  .journey-path {
    display: none;
  }

  .step-card {
    min-height: 118px;
    padding-top: 30px;
  }

  .step-image {
    height: 148px;
  }

  .delivery-banner {
    width: calc(100% - 32px);
    grid-template-columns: 1fr;
    justify-items: center;
    gap: 14px;
    padding: 25px 20px 30px;
    text-align: center;
  }

  .van-art {
    width: min(270px, 80%);
  }

  .delivery-banner p {
    font-size: clamp(27px, 8vw, 36px);
  }
}

@media (max-width: 480px) {
  .hero-kicker {
    gap: 8px;
    font-size: 11px;
  }

  .hero-kicker svg {
    width: 28px;
  }

  .hero-leaves {
    width: 76px;
  }

  .journey-step,
  .journey-step:nth-child(even) {
    grid-template-columns: 40% 1fr;
  }

  .journey-step:nth-child(even) {
    grid-template-columns: 1fr 40%;
  }

  .step-card {
    padding-right: 20px;
    padding-left: 20px;
  }

  .step-card h3 {
    font-size: 17px;
  }

  .step-card p {
    font-size: 11px;
  }

  .step-number {
    left: 20px;
  }
}

@media (prefers-reduced-motion: reduce) {
  .order-button {
    transition: none;
  }
}
</style>
