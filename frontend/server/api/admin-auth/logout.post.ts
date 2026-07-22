export default defineEventHandler(async (event) => {
  const token = readAdminToken(event)

  try {
    if (token) {
      await requestLaravel(event, '/logout', { method: 'POST', token })
    }
  }
  finally {
    clearAdminToken(event)
  }

  return { message: 'Logged out.' }
})
