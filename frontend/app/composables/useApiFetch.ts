export function useApiFetch<T>(path: string, options: Parameters<typeof $fetch<T>>[1] = {}) {
  const config = useRuntimeConfig()
  const { locale } = useI18n()

  return $fetch<T>(path, {
    ...options,
    baseURL: config.public.apiBase,
    headers: {
      ...options?.headers,
      'X-Locale': locale.value,
    },
  })
}
