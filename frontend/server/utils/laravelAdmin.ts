import type { H3Event } from 'h3'

interface LaravelRequestOptions {
  method?: 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE'
  body?: unknown
  query?: Record<string, unknown>
  token?: string
}

export async function requestLaravel<T>(
  event: H3Event,
  path: string,
  options: LaravelRequestOptions = {},
): Promise<T> {
  const config = useRuntimeConfig(event)
  const token = options.token ?? readAdminToken(event)

  try {
    return await $fetch<T>(path, {
      baseURL: config.apiBase,
      method: options.method ?? 'GET',
      body: options.body,
      query: options.query,
      headers: {
        Accept: 'application/json',
        ...(token ? { Authorization: `Bearer ${token}` } : {}),
      },
    })
  }
  catch (error: any) {
    const statusCode = Number(error?.response?.status ?? error?.statusCode ?? 500)
    const data = error?.data ?? error?.response?._data

    if (statusCode === 401) clearAdminToken(event)

    throw createError({
      statusCode,
      statusMessage: data?.message ?? error?.message ?? 'Laravel API request failed',
      data,
    })
  }
}
