interface CloudinaryUploadResponse {
  secure_url: string
}

export function useCloudinaryUpload() {
  const config = useRuntimeConfig()
  const uploading = ref(false)
  const error = ref('')

  async function upload(file: File): Promise<string> {
    const cloudName = config.public.cloudinaryCloudName
    const uploadPreset = config.public.cloudinaryUploadPreset

    if (!cloudName || !uploadPreset) {
      throw new Error('Cloudinary is not configured — missing cloud name or upload preset.')
    }

    uploading.value = true
    error.value = ''

    try {
      const formData = new FormData()
      formData.append('file', file)
      formData.append('upload_preset', uploadPreset)

      const response = await $fetch<CloudinaryUploadResponse>(
        `https://api.cloudinary.com/v1_1/${cloudName}/image/upload`,
        { method: 'POST', body: formData },
      )

      return response.secure_url
    }
    catch (err: any) {
      error.value = err?.data?.error?.message ?? 'Image upload failed. Please try again.'
      throw err
    }
    finally {
      uploading.value = false
    }
  }

  return { upload, uploading, error }
}
