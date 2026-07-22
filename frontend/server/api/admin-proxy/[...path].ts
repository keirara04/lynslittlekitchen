import { isAllowedAdminProxy, normalizeAdminProxyPath } from '../../utils/adminProxyPolicy.mjs'

export default defineEventHandler(async (event) => {
  const method = getMethod(event).toUpperCase()
  const rawPath = getRouterParam(event, 'path')
  const path = normalizeAdminProxyPath(rawPath)

  if (!path || !isAllowedAdminProxy(method, path)) {
    throw createError({ statusCode: 404, statusMessage: 'Admin API route not found.' })
  }

  if (!readAdminToken(event)) {
    throw createError({ statusCode: 401, statusMessage: 'Admin session required.' })
  }

  const target = path === 'categories' ? '/categories' : `/admin/${path}`
  const hasBody = ['POST', 'PUT', 'PATCH'].includes(method)

  return requestLaravel(event, target, {
    method: method as 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE',
    query: getQuery(event),
    body: hasBody ? await readBody(event) : undefined,
  })
})
