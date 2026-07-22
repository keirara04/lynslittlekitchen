import type { AdminUser } from '~/types/admin'

interface LoginBody {
  email: string
  password: string
  remember?: boolean
}

interface LoginResponse {
  user: AdminUser
  token: string
}

export default defineEventHandler(async (event) => {
  const body = await readBody<LoginBody>(event)

  if (!body?.email || !body?.password) {
    throw createError({
      statusCode: 422,
      statusMessage: 'Email and password are required.',
    })
  }

  const response = await requestLaravel<LoginResponse>(event, '/login', {
    method: 'POST',
    body: { email: body.email, password: body.password },
  })

  if (response.user.role !== 'admin') {
    try {
      await requestLaravel(event, '/logout', {
        method: 'POST',
        token: response.token,
      })
    }
    finally {
      throw createError({
        statusCode: 403,
        statusMessage: 'This account does not have admin access.',
      })
    }
  }

  setAdminToken(event, response.token, Boolean(body.remember))
  return { user: response.user }
})
