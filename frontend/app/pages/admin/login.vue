<script setup lang="ts">
definePageMeta({ layout: 'admin-auth' })

const auth = useAdminAuthStore()
const route = useRoute()
const email = ref('')
const password = ref('')
const remember = ref(false)
const showPassword = ref(false)
const busy = ref(false)
const errorMessage = ref('')
const fieldErrors = ref<Record<string, string[]>>({})

await auth.checkSession()
if (auth.isAuthenticated) await navigateTo('/admin')

async function submit() {
  busy.value = true
  errorMessage.value = ''
  fieldErrors.value = {}

  try {
    await auth.login({ email: email.value, password: password.value, remember: remember.value })
    const redirect = typeof route.query.redirect === 'string' && route.query.redirect.startsWith('/admin')
      ? route.query.redirect
      : '/admin'
    await navigateTo(redirect)
  }
  catch (error: any) {
    const data = error?.data?.data ?? error?.data
    fieldErrors.value = data?.errors ?? {}
    errorMessage.value = data?.message ?? error?.statusMessage ?? 'Sign in failed. Check your details and try again.'
  }
  finally {
    busy.value = false
  }
}

useSeoMeta({ title: "Admin sign in | Lyn's Little Kitchen", robots: 'noindex, nofollow' })
</script>

<template>
  <section class="admin-login-card">
    <div class="admin-login-card__form-panel">
      <AdminBrand />
      <div class="admin-login-card__heading">
        <p class="admin-kicker">Private workspace</p>
        <h1>Admin Area</h1>
        <p>Sign in to manage your cookies, orders, and daily kitchen operations.</p>
      </div>

      <form class="admin-login-form" @submit.prevent="submit">
        <label class="admin-field">
          <span>Email address</span>
          <input v-model.trim="email" type="email" autocomplete="email" required placeholder="Enter your admin email">
          <small v-if="fieldErrors.email" class="admin-field__error">{{ fieldErrors.email[0] }}</small>
        </label>

        <div class="admin-field">
          <label for="admin-password">Password</label>
          <span class="admin-password-field">
            <input id="admin-password" v-model="password" :type="showPassword ? 'text' : 'password'" autocomplete="current-password" required placeholder="Enter your password">
            <button
              type="button"
              :aria-label="showPassword ? 'Hide password' : 'Show password'"
              :aria-pressed="showPassword"
              @click.stop="showPassword = !showPassword"
            >
              {{ showPassword ? 'Hide' : 'Show' }}
            </button>
          </span>
          <small v-if="fieldErrors.password" class="admin-field__error">{{ fieldErrors.password[0] }}</small>
        </div>

        <label class="admin-check">
          <input v-model="remember" type="checkbox">
          <span>Remember me for 14 days</span>
        </label>

        <p v-if="errorMessage" class="admin-form-error" role="alert">{{ errorMessage }}</p>
        <button class="admin-button admin-button--primary admin-login-form__submit" type="submit" :disabled="busy">
          {{ busy ? 'Signing in…' : 'Sign in' }}
        </button>
      </form>

      <p class="admin-login-card__copyright">© 2026 Lyn’s Little Kitchen</p>
    </div>

    <div class="admin-login-card__image-panel" aria-hidden="true">
      <img src="/images/products/choc-chip-crunch-temp.png" alt="">
      <span class="admin-login-card__image-wash" />
    </div>
  </section>
</template>
