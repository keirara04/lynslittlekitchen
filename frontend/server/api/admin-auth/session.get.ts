import type { AdminUser } from '~/types/admin'

export default defineEventHandler(async (event) => {
  const token = readAdminToken(event)
  if (!token) {
    throw createError({ statusCode: 401, statusMessage: 'Admin session required.' })
  }

  const response = await requestLaravel<{ data: AdminUser }>(event, '/me', { token })
  if (response.data.role !== 'admin') {
    clearAdminToken(event)
    throw createError({ statusCode: 403, statusMessage: 'Admin access required.' })
  }

  return { user: response.data }
})
