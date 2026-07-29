export default defineNuxtPlugin(() => {
  const route = useRoute()
  const config = useRuntimeConfig()

  useHead({
    link: [
      { rel: 'canonical', href: () => `${config.public.siteUrl}${route.path}` },
    ],
  })
})
