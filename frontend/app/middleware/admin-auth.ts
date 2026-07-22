export default defineNuxtRouteMiddleware(async (to) => {
  const auth = useAdminAuthStore()
  await auth.checkSession()

  if (!auth.isAuthenticated) {
    return navigateTo({
      path: '/admin/login',
      query: { redirect: to.fullPath },
    })
  }
})
