<script setup lang="ts">
interface HealthResponse {
  status: string
  message: string
}

const config = useRuntimeConfig()

const {
  data: health,
  error,
  pending,
} = await useFetch<HealthResponse>('/health', {
  baseURL: config.public.apiBase,
})
</script>

<template>
  <main class="min-h-screen bg-amber-50 px-6 py-16">
    <div class="mx-auto max-w-5xl">
      <p class="font-medium text-amber-700">
        Freshly baked in Jasin, Melaka, Malaysia
      </p>

      <h1 class="mt-3 text-5xl font-bold text-stone-900">
        Cookie Business
      </h1>

      <div class="mt-8 rounded-xl bg-white p-5 shadow-sm">
        <p v-if="pending">
          Checking backend...
        </p>

        <p v-else-if="error" class="text-red-600">
          Backend connection failed.
        </p>

        <p v-else class="text-green-700">
          {{ health?.message }}
        </p>
      </div>
    </div>
  </main>
</template>