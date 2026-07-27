<script setup lang="ts">
const { t, tm } = useI18n()

useSeoMeta({
  title: "About us | Lyn's Little Kitchen",
  description: "Meet the home kitchen behind Lyn's small-batch cookies in Jasin, Melaka.",
})

interface BakeryPromise {
  title: string
  text: string
}

const promises = computed(() => tm('about.promises') as unknown as BakeryPromise[])
</script>

<template>
  <main class="about-page">
    <section class="about-hero" aria-labelledby="about-heading">
      <div class="about-hero__copy">
        <p class="about-kicker">{{ t('about.kicker') }}</p>
        <h1 id="about-heading">{{ t('about.heroTitle') }}</h1>
        <p class="about-hero__intro">
          {{ t('about.heroIntro') }}
        </p>
        <NuxtLink to="/shop" class="btn-primary about-hero__action">{{ t('about.seeWhatsBaking') }}</NuxtLink>
      </div>

      <figure class="about-hero__image">
        <img
          src="/images/about/baking-hands.png"
          alt="Hands arranging freshly baked cookies on a well-used baking tray"
          width="1440"
          height="960"
        >
        <figcaption>{{ t('about.heroCaption') }}</figcaption>
      </figure>
    </section>

    <section class="about-origin container-shell" aria-labelledby="origin-heading">
      <div class="about-origin__image-wrap">
        <img
          class="about-origin__image"
          src="/images/about/cookie-close-up.png"
          alt="A freshly baked chocolate cookie broken open on a ceramic plate"
          width="1024"
          height="1024"
          loading="lazy"
        >
      </div>
      <div class="about-origin__copy">
        <p class="about-kicker">{{ t('about.originKicker') }}</p>
        <h2 id="origin-heading">{{ t('about.originTitle') }}</h2>
        <p>
          {{ t('about.originPara1') }}
        </p>
        <p>
          {{ t('about.originPara2') }}
        </p>
      </div>
    </section>

    <section class="about-promises container-shell" aria-labelledby="promises-heading">
      <div class="about-promises__heading">
        <p class="about-kicker">{{ t('about.promisesKicker') }}</p>
        <h2 id="promises-heading">{{ t('about.promisesTitle') }}</h2>
      </div>
      <ol class="about-promises__list">
        <li v-for="(promise, index) in promises" :key="promise.title" class="about-promises__item">
          <span class="about-promises__number">0{{ index + 1 }}</span>
          <div>
            <h3>{{ promise.title }}</h3>
            <p>{{ promise.text }}</p>
          </div>
        </li>
      </ol>
    </section>

    <section class="about-closing container-shell" aria-labelledby="closing-heading">
      <div class="about-closing__copy">
        <p class="about-kicker">{{ t('about.closingKicker') }}</p>
        <h2 id="closing-heading">{{ t('about.closingTitle') }}</h2>
        <p>
          {{ t('about.closingText') }}
        </p>
        <div class="about-closing__actions">
          <NuxtLink to="/shop" class="btn-primary">{{ t('about.seeWhatsBaking') }}</NuxtLink>
          <NuxtLink to="/how-to-order" class="btn-ghost">{{ t('nav.howToOrder') }} <span aria-hidden="true">→</span></NuxtLink>
        </div>
      </div>
      <img
        class="about-closing__image"
        src="/images/about/packing-orders.png"
        alt="Hands packing a box of freshly baked cookies at a kitchen table"
        width="1440"
        height="1080"
        loading="lazy"
      >
    </section>

    <section class="about-note container-shell" aria-labelledby="note-heading">
      <div class="about-note__rule" aria-hidden="true" />
      <div>
        <p class="about-kicker">{{ t('about.noteKicker') }}</p>
        <h2 id="note-heading">{{ t('about.noteTitle') }}</h2>
      </div>
      <p>
        {{ t('about.noteText') }}
      </p>
      <NuxtLink to="/contact" class="btn-ghost about-note__link">{{ t('about.getInTouch') }} <span aria-hidden="true">→</span></NuxtLink>
    </section>
  </main>
</template>

<style scoped>
.about-page {
  overflow: clip;
  padding-bottom: 1px;
  background:
    radial-gradient(circle at 86% 8%, rgb(232 196 179 / 22%), transparent 25rem),
    var(--parchment);
}

.about-hero {
  display: grid;
  min-height: min(690px, calc(100vh - 76px));
  grid-template-columns: minmax(0, .88fr) minmax(0, 1.12fr);
  border-bottom: 1px solid var(--line);
}

.about-hero__copy {
  display: flex;
  max-width: 42rem;
  flex-direction: column;
  justify-content: center;
  padding: clamp(4.5rem, 10vw, 9rem) clamp(1.5rem, 8vw, 9rem);
}

.about-kicker {
  display: flex;
  align-items: center;
  gap: .65rem;
  margin: 0;
  color: var(--clay-dark);
  font-size: .7rem;
  font-weight: 700;
  letter-spacing: .12em;
  text-transform: uppercase;
}

.about-kicker::before {
  width: 1.65rem;
  height: 1px;
  background: currentColor;
  content: '';
}

.about-hero h1,
.about-origin h2,
.about-promises h2,
.about-closing h2 {
  margin: 1.25rem 0 0;
  font-family: var(--serif);
  font-weight: 500;
  letter-spacing: -.055em;
  line-height: .95;
}

.about-hero h1 { font-size: clamp(3.25rem, 5.4vw, 5.85rem); }

.about-hero__intro,
.about-origin__copy > p,
.about-closing__copy > p {
  max-width: 34rem;
  margin: 1.65rem 0 0;
  color: var(--muted);
  font-size: .98rem;
  line-height: 1.8;
}

.about-hero__action { margin-top: 2rem; width: fit-content; }

.about-hero__image { position: relative; min-height: 30rem; margin: 0; }
.about-hero__image img { display: block; width: 100%; height: 100%; object-fit: cover; }
.about-hero__image figcaption {
  position: absolute;
  bottom: 1.4rem;
  left: 1.4rem;
  padding: .7rem .85rem;
  color: var(--cocoa);
  background: rgb(255 250 245 / 88%);
  box-shadow: 0 8px 20px rgb(68 40 31 / 10%);
  font-size: .73rem;
  font-weight: 700;
  letter-spacing: .04em;
}

.about-origin,
.about-closing {
  display: grid;
  grid-template-columns: minmax(0, .88fr) minmax(0, 1.12fr);
  align-items: center;
  gap: clamp(2rem, 8vw, 8rem);
  padding-top: clamp(5.5rem, 11vw, 9rem);
}

.about-origin__image-wrap { overflow: hidden; aspect-ratio: 1; border-radius: 50% 50% 1.25rem 1.25rem; }
.about-origin__image, .about-closing__image { display: block; width: 100%; height: 100%; object-fit: cover; }
.about-origin h2, .about-promises h2, .about-closing h2 { font-size: clamp(2.35rem, 4vw, 4.35rem); }
.about-origin__copy > p + p { margin-top: 1rem; }

.about-promises { padding-top: clamp(5.5rem, 11vw, 9rem); }
.about-promises__heading { max-width: 38rem; }
.about-promises__list { margin: 3rem 0 0; padding: 0; border-top: 1px solid var(--line); list-style: none; }
.about-promises__item { display: grid; grid-template-columns: 4rem minmax(0, 1fr); gap: 1rem; padding: 1.55rem 0; }
.about-promises__item + .about-promises__item { border-top: 1px solid var(--line); }
.about-promises__number { padding-top: .3rem; color: var(--clay); font-size: .72rem; font-weight: 700; letter-spacing: .1em; }
.about-promises h3 { margin: 0; font-family: var(--serif); font-size: clamp(1.45rem, 2.2vw, 2rem); font-weight: 500; letter-spacing: -.035em; }
.about-promises__item p { max-width: 35rem; margin: .45rem 0 0; color: var(--muted); font-size: .93rem; line-height: 1.7; }

.about-closing { grid-template-columns: minmax(0, .9fr) minmax(0, 1.1fr); padding-bottom: clamp(4.5rem, 9vw, 7rem); }
.about-closing__image { min-height: 25rem; border-radius: 1.25rem 1.25rem 45% 45%; }
.about-closing__actions { display: flex; flex-wrap: wrap; align-items: center; gap: .6rem; margin-top: 2rem; }
.about-note {
  display: grid;
  grid-template-columns: auto minmax(0, 1.2fr) minmax(0, 1fr) auto;
  align-items: center;
  gap: 1.5rem;
  margin-bottom: clamp(4.5rem, 8vw, 7rem);
  padding: 1.65rem 0;
  border-top: 1px solid var(--line);
  border-bottom: 1px solid var(--line);
}

.about-note__rule {
  width: 2.75rem;
  height: 2.75rem;
  border: 1px solid rgb(168 95 76 / 26%);
  border-radius: 50%;
  background: radial-gradient(circle, rgb(232 196 179 / 58%) 0 18%, transparent 20% 100%);
}

.about-note h2 {
  margin: .65rem 0 0;
  font-family: var(--serif);
  font-size: clamp(1.45rem, 2.2vw, 2rem);
  font-weight: 500;
  letter-spacing: -.035em;
}

.about-note > p {
  margin: 0;
  color: var(--muted);
  font-size: .84rem;
  line-height: 1.65;
}

.about-note__link { white-space: nowrap; }

@media (max-width: 900px) {
  .about-hero, .about-origin, .about-closing { grid-template-columns: 1fr; }
  .about-hero { min-height: auto; }
  .about-hero__copy { max-width: none; padding: 5.5rem 1.5rem 3.5rem; }
  .about-hero__image { min-height: 24rem; }
  .about-origin, .about-closing { gap: 2.25rem; }
  .about-origin__copy { order: -1; }
  .about-origin__image-wrap { width: min(100%, 31rem); }
  .about-closing__image { min-height: 22rem; }
  .about-note { grid-template-columns: auto minmax(0, 1fr); }
  .about-note > p { grid-column: 2; }
  .about-note__link { grid-column: 2; width: fit-content; padding-left: 0; }
}

@media (max-width: 520px) {
  .about-hero h1 { font-size: clamp(3rem, 14vw, 4.3rem); }
  .about-hero__image { min-height: 20rem; }
  .about-promises__item { grid-template-columns: 2.75rem minmax(0, 1fr); }
}

@media (prefers-reduced-motion: reduce) {
  .about-page * { scroll-behavior: auto; transition: none !important; }
}
</style>
