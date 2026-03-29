<script setup>
import { computed, ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import { Camera, Mail, ShieldCheck, UserRound, IdCard } from 'lucide-vue-next'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

defineOptions({ layout: AdminLayout })

const DEFAULT_AVATAR_PATH = '/images/default.png'

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
})

const avatarInput = ref(null)
const imageLoadFailed = ref(false)

const form = useForm({
  name: props.user?.name ?? '',
  email: props.user?.email ?? '',
  national_id: props.user?.national_id ?? '',
  avatar_image: null,
})

const roleLabel = computed(() => {
  const role = props.user?.primary_role ?? 'staff'
  return role.charAt(0).toUpperCase() + role.slice(1)
})

const previewImage = computed(() => {
  if (imageLoadFailed.value) {
    return DEFAULT_AVATAR_PATH
  }

  if (form.avatar_image instanceof File) {
    return URL.createObjectURL(form.avatar_image)
  }

  if (props.user?.avatar_image) {
    if (props.user.avatar_image === 'default.png') {
      return DEFAULT_AVATAR_PATH
    }

    if (
      props.user.avatar_image.startsWith('http://') ||
      props.user.avatar_image.startsWith('https://') ||
      props.user.avatar_image.startsWith('/')
    ) {
      return props.user.avatar_image
    }

    return `/storage/${props.user.avatar_image}`
  }

  return DEFAULT_AVATAR_PATH
})

const selectedAvatarName = computed(() => {
  if (form.avatar_image instanceof File) {
    return form.avatar_image.name
  }

  return 'No file chosen'
})

const handleAvatarChange = (event) => {
  imageLoadFailed.value = false
  form.avatar_image = event.target.files?.[0] ?? null
}

const submit = () => {
  form.patch(route('admins.profile.update'), {
    forceFormData: true,
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="Admin Profile" />

  <div class="min-h-screen bg-[#f4f7fb] px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto w-full max-w-5xl space-y-6">
      <div class="rounded-3xl border border-[#d9e2ec] bg-white p-6 shadow-sm sm:p-8">
        <div class="space-y-2">
          <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#5b708b]">
            Staff Profile
          </p>
          <h1 class="text-3xl font-semibold tracking-tight text-[#0c1a2e] sm:text-4xl">
            Admin Profile
          </h1>
          <p class="max-w-2xl text-sm leading-6 text-[#5b708b]">
            Keep your profile details up to date while staying consistent with the shared dashboard design system.
          </p>
        </div>
      </div>

      <form
        v-if="user.can_manage_avatar"
        @submit.prevent="submit"
        class="grid grid-cols-1 gap-6 lg:grid-cols-[280px_minmax(0,1fr)]"
      >
        <section class="rounded-3xl border border-[#d9e2ec] bg-white p-6 shadow-sm sm:p-7">
          <div class="flex flex-col items-center text-center">
            <div class="relative">
              <div class="h-32 w-32 overflow-hidden rounded-full border-4 border-[#edf2f7] bg-[#edf2f7] shadow-sm sm:h-36 sm:w-36">
                <img
                  :src="previewImage"
                  alt="Profile Image"
                  class="h-full w-full object-cover"
                  @error="imageLoadFailed = true"
                >
              </div>
              <div class="absolute bottom-1 right-1 rounded-full border border-white bg-[#0c1a2e] p-2 text-white shadow">
                <Camera class="h-4 w-4" />
              </div>
            </div>

            <div class="mt-5 space-y-1">
              <h2 class="text-lg font-semibold text-[#0c1a2e]">
                Profile photo
              </h2>
              <p class="text-sm leading-6 text-[#5b708b]">
                Upload a JPG, PNG, or WEBP image up to 2MB.
              </p>
            </div>
          </div>

          <div class="mt-6 space-y-2">
            <Label class="text-[#0c1a2e]">Profile Image</Label>
            <input
              id="avatar_image"
              ref="avatarInput"
              type="file"
              accept=".jpg,.jpeg,.png,.webp"
              class="sr-only"
              @change="handleAvatarChange"
            >
            <div class="flex items-center gap-3 rounded-xl border border-[#d9e2ec] bg-white p-2">
              <Button
                type="button"
                variant="outline"
                class="h-10 border-[#0c1a2e] bg-[#0c1a2e] px-4 text-sm font-medium text-white hover:bg-[#132844] hover:text-white"
                @click="avatarInput?.click()"
              >
                Choose File
              </Button>
              <span class="min-w-0 flex-1 truncate text-sm text-[#0c1a2e]">
                {{ selectedAvatarName }}
              </span>
            </div>
            <p class="text-xs text-[#5b708b]">
              Recommended for the clearest circular preview.
            </p>
            <p v-if="form.errors.avatar_image" class="text-sm text-red-500">
              {{ form.errors.avatar_image }}
            </p>
          </div>
        </section>

        <section class="rounded-3xl border border-[#d9e2ec] bg-white p-6 shadow-sm sm:p-7">
          <div class="flex flex-col gap-2 border-b border-[#e7edf5] pb-5">
            <h2 class="text-xl font-semibold text-[#0c1a2e]">
              Personal Information
            </h2>
            <p class="text-sm leading-6 text-[#5b708b]">
              Review and update the details associated with your {{ roleLabel.toLowerCase() }} account.
            </p>
          </div>

          <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="space-y-2 md:col-span-2">
              <Label for="name" class="text-[#0c1a2e]">Name</Label>
              <div class="relative">
                <UserRound class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#5b708b]" />
                <Input
                  id="name"
                  v-model="form.name"
                  type="text"
                  placeholder="Enter your name"
                  class="h-11 border-[#d9e2ec] bg-white pl-10 text-[#0c1a2e] placeholder:text-[#8aa0b8] focus-visible:ring-[#0c1a2e]"
                />
              </div>
              <p v-if="form.errors.name" class="text-sm text-red-500">
                {{ form.errors.name }}
              </p>
            </div>

            <div class="space-y-2 md:col-span-2">
              <Label for="email" class="text-[#0c1a2e]">Email</Label>
              <div class="relative">
                <Mail class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#5b708b]" />
                <Input
                  id="email"
                  v-model="form.email"
                  type="email"
                  placeholder="Enter your email"
                  class="h-11 border-[#d9e2ec] bg-white pl-10 text-[#0c1a2e] placeholder:text-[#8aa0b8] focus-visible:ring-[#0c1a2e]"
                />
              </div>
              <p v-if="form.errors.email" class="text-sm text-red-500">
                {{ form.errors.email }}
              </p>
            </div>

            <div class="space-y-2 md:col-span-2">
              <Label for="national_id" class="text-[#0c1a2e]">National ID</Label>
              <div class="relative">
                <IdCard class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#5b708b]" />
                <Input
                  id="national_id"
                  v-model="form.national_id"
                  type="text"
                  placeholder="Enter national ID"
                  class="h-11 border-[#d9e2ec] bg-white pl-10 text-[#0c1a2e] placeholder:text-[#8aa0b8] focus-visible:ring-[#0c1a2e]"
                />
              </div>
              <p v-if="form.errors.national_id" class="text-sm text-red-500">
                {{ form.errors.national_id }}
              </p>
            </div>
          </div>

          <div class="mt-8 flex flex-col gap-3 border-t border-[#e7edf5] pt-6 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-[#5b708b]">
              Make sure your information is accurate before saving.
            </p>

            <Button
              type="submit"
              :disabled="form.processing"
              class="h-11 rounded-xl bg-[#0c1a2e] px-6 text-sm font-medium text-white hover:bg-[#132844]"
            >
              {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </Button>
          </div>
        </section>
      </form>

      <form
        v-else
        @submit.prevent="submit"
        class="space-y-6"
      >
        <Card class="rounded-3xl border-[#d9e2ec] shadow-sm">
          <CardContent class="p-6 sm:p-7">
            <div class="flex items-start gap-4 border-b border-[#e7edf5] pb-5">
              <div class="rounded-2xl bg-slate-100 p-3 text-[#0c1a2e]">
                <ShieldCheck class="h-5 w-5" />
              </div>
              <div class="space-y-1">
                <h2 class="text-xl font-semibold text-[#0c1a2e]">Personal Information</h2>
                <p class="text-sm leading-6 text-[#5b708b]">
                  Review and update the details associated with your {{ roleLabel.toLowerCase() }} account.
                </p>
              </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-6">
              <div class="space-y-2">
                <Label for="admin-name" class="text-[#0c1a2e]">Name</Label>
                <div class="relative">
                  <UserRound class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#5b708b]" />
                  <Input
                    id="admin-name"
                    v-model="form.name"
                    type="text"
                    placeholder="Enter your name"
                    class="h-11 border-[#d9e2ec] bg-white pl-10 text-[#0c1a2e] placeholder:text-[#8aa0b8] focus-visible:ring-[#0c1a2e]"
                  />
                </div>
                <p v-if="form.errors.name" class="text-sm text-red-500">
                  {{ form.errors.name }}
                </p>
              </div>

              <div class="space-y-2">
                <Label for="admin-email" class="text-[#0c1a2e]">Email</Label>
                <div class="relative">
                  <Mail class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#5b708b]" />
                  <Input
                    id="admin-email"
                    v-model="form.email"
                    type="email"
                    placeholder="Enter your email"
                    class="h-11 border-[#d9e2ec] bg-white pl-10 text-[#0c1a2e] placeholder:text-[#8aa0b8] focus-visible:ring-[#0c1a2e]"
                  />
                </div>
                <p v-if="form.errors.email" class="text-sm text-red-500">
                  {{ form.errors.email }}
                </p>
              </div>
            </div>

            <div class="mt-8 flex flex-col gap-3 border-t border-[#e7edf5] pt-6 sm:flex-row sm:items-center sm:justify-between">
              <p class="text-sm text-[#5b708b]">
                Make sure your information is accurate before saving.
              </p>

              <Button
                type="submit"
                :disabled="form.processing"
                class="h-11 rounded-xl bg-[#0c1a2e] px-6 text-sm font-medium text-white hover:bg-[#132844]"
              >
                {{ form.processing ? 'Saving...' : 'Save Changes' }}
              </Button>
            </div>
          </CardContent>
        </Card>
      </form>
    </div>
  </div>
</template>
