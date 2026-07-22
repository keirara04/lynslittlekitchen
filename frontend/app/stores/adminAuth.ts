import type { AdminUser } from '~/types/admin'

interface LoginCredentials {
  email: string
  password: string
  remember: boolean
}

export const useAdminAuthStore = defineStore('admin-auth', () => {
  const user = ref<AdminUser | null>(null)
  const checked = ref(false)
  const isAuthenticated = computed(() => user.value?.role === 'admin')
  let pendingCheck: Promise<void> | null = null

  async function checkSession(force = false): Promise<void> {
    if (checked.value && !force) return
    if (pendingCheck) return pendingCheck

    pendingCheck = (async () => {
      try {
        const headers = import.meta.server ? useRequestHeaders(['cookie']) : undefined
        const response = await $fetch<{ user: AdminUser }>('/api/admin-auth/session', { headers })
        user.value = response.user
      }
      catch {
        user.value = null
      }
      finally {
        checked.value = true
        pendingCheck = null
      }
    })()

    return pendingCheck
  }

  async function login(credentials: LoginCredentials): Promise<AdminUser> {
    const response = await $fetch<{ user: AdminUser }>('/api/admin-auth/login', {
      method: 'POST',
      body: credentials,
    })
    user.value = response.user
    checked.value = true
    return response.user
  }

  async function logout(): Promise<void> {
    try {
      await $fetch('/api/admin-auth/logout', { method: 'POST' })
    }
    finally {
      user.value = null
      checked.value = true
      await navigateTo('/admin/login')
    }
  }

  function clearSession(): void {
    user.value = null
    checked.value = true
  }

  return { user, checked, isAuthenticated, checkSession, login, logout, clearSession }
})
