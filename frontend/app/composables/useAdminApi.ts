import type { FetchError } from 'ofetch'

interface AdminApiOptions {
  method?: 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE'
  query?: Record<string, unknown>
  body?: unknown
}

type FetchBody = BodyInit | Record<string, unknown> | null | undefined

export async function useAdminApi<T>(path: string, options: AdminApiOptions = {}): Promise<T> {
  const normalized = path.replace(/^\/+|\/+$/g, '')

  try {
    const headers = import.meta.server ? useRequestHeaders(['cookie']) : undefined
    return await ($fetch(`/api/admin-proxy/${normalized}`, {
      ...options,
      body: options.body as FetchBody,
      headers,
    }) as Promise<T>)
  }
  catch (err) {
    const error = err as FetchError
    const status = Number(error.statusCode ?? error.response?.status)
    if (status === 401 && import.meta.client) {
      const auth = useAdminAuthStore()
      auth.clearSession()
      await navigateTo(`/admin/login?redirect=${encodeURIComponent(useRoute().fullPath)}`)
    }
    throw error
  }
}
