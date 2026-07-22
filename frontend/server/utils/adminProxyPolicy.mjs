const rules = [
  ['GET', /^dashboard$/],
  ['GET', /^categories$/],
  ['GET', /^products$/],
  ['POST', /^products$/],
  ['GET', /^products\/\d+$/],
  ['PUT', /^products\/\d+$/],
  ['DELETE', /^products\/\d+$/],
  ['GET', /^orders$/],
  ['GET', /^orders\/\d+$/],
  ['PATCH', /^orders\/\d+\/status$/],
]

export function normalizeAdminProxyPath(path) {
  if (typeof path !== 'string') return null

  const lowered = path.toLowerCase()
  if (
    lowered.includes('%2f')
    || lowered.includes('%5c')
    || path.includes('\\')
    || path.includes('..')
    || path.includes('://')
  ) {
    return null
  }

  const normalized = path.replace(/^\/+|\/+$/g, '')
  if (!normalized || normalized.includes('//')) return null
  return normalized
}

export function isAllowedAdminProxy(method, path) {
  const normalized = normalizeAdminProxyPath(path)
  if (!normalized) return false

  const upperMethod = String(method || '').toUpperCase()
  return rules.some(([allowedMethod, pattern]) => (
    upperMethod === allowedMethod && pattern.test(normalized)
  ))
}
