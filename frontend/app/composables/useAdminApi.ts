interface AdminApiOptions {
  method?: 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE'
  query?: Record<string, unknown>
  body?: unknown
}

export async function useAdminApi<T>(path: string, options: AdminApiOptions = {}): Promise<T> {
  const normalized = path.replace(/^\/+|\/+$/g, '')

  try {
    return await $fetch<T>(`/api/admin-proxy/${normalized}`, options)
  }
  catch (error: any) {
    const status = Number(error?.statusCode ?? error?.response?.status)
    if (status === 401 && import.meta.client) {
      const auth = useAdminAuthStore()
      auth.clearSession()
      await navigateTo(`/admin/login?redirect=${encodeURIComponent(useRoute().fullPath)}`)
    }
    throw error
  }
}
