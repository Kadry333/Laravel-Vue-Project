<script setup>
import { computed, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Camera, IdCard, Mail, UserRound } from 'lucide-vue-next'

import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

const DEFAULT_AVATAR_PATH = '/images/default.png'

const props = defineProps({
  form: {
    type: Object,
    required: true,
  },
  mode: {
    type: String,
    required: true,
  },
  title: {
    type: String,
    required: true,
  },
  description: {
    type: String,
    required: true,
  },
  backHref: {
    type: String,
    default: '/admins/receptionists',
  },
  receptionist: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['submit'])

const avatarInput = ref(null)
const imageLoadFailed = ref(false)

const previewImage = computed(() => {
  if (imageLoadFailed.value) {
    return DEFAULT_AVATAR_PATH
  }

  if (props.form.avatar_image instanceof File) {
    return URL.createObjectURL(props.form.avatar_image)
  }

  if (props.receptionist?.avatar_image) {
    if (props.receptionist.avatar_image === 'default.png') {
      return DEFAULT_AVATAR_PATH
    }

    if (
      props.receptionist.avatar_image.startsWith('http://') ||
      props.receptionist.avatar_image.startsWith('https://') ||
      props.receptionist.avatar_image.startsWith('/')
    ) {
      return props.receptionist.avatar_image
    }

    return `/storage/${props.receptionist.avatar_image}`
  }

  return DEFAULT_AVATAR_PATH
})

const selectedAvatarName = computed(() => {
  if (props.form.avatar_image instanceof File) {
    return props.form.avatar_image.name
  }

  return 'No file chosen'
})

const handleAvatarChange = (event) => {
  imageLoadFailed.value = false
  props.form.avatar_image = event.target.files?.[0] ?? null
}
</script>

<template>
  <form
    @submit.prevent="emit('submit')"
    class="grid grid-cols-1 gap-6 lg:grid-cols-[300px_minmax(0,1fr)]"
  >
    <Card class="rounded-3xl border-[#d9e2ec] shadow-sm">
      <CardHeader class="pb-4">
        <CardTitle class="text-xl text-[#0c1a2e]">Profile Image</CardTitle>
        <CardDescription class="text-[#5b708b]">
          Upload a JPG, PNG, or WEBP image up to 2MB.
        </CardDescription>
      </CardHeader>

      <CardContent class="space-y-5">
        <div class="flex flex-col items-center text-center">
          <div class="relative">
            <div class="h-32 w-32 overflow-hidden rounded-full border-4 border-[#edf2f7] bg-[#edf2f7] shadow-sm">
              <img
                :src="previewImage"
                alt="Receptionist avatar"
                class="h-full w-full object-cover"
                @error="imageLoadFailed = true"
              >
            </div>
            <div class="absolute bottom-1 right-1 rounded-full border border-white bg-[#0c1a2e] p-2 text-white shadow">
              <Camera class="h-4 w-4" />
            </div>
          </div>

          <p class="mt-4 text-sm leading-6 text-[#5b708b]">
            This image will appear across the receptionist account and listings.
          </p>
        </div>

        <div class="space-y-2">
          <Label for="avatar_image" class="text-[#0c1a2e]">Avatar</Label>
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
            Leave empty to keep the current photo or use the default avatar.
          </p>
          <p v-if="form.errors.avatar_image" class="text-sm text-red-500">
            {{ form.errors.avatar_image }}
          </p>
        </div>
      </CardContent>
    </Card>

    <Card class="rounded-3xl border-[#d9e2ec] shadow-sm">
      <CardHeader class="border-b border-[#e7edf5] pb-5">
        <CardTitle class="text-2xl text-[#0c1a2e]">{{ title }}</CardTitle>
        <CardDescription class="max-w-2xl text-sm leading-6 text-[#5b708b]">
          {{ description }}
        </CardDescription>
      </CardHeader>

      <CardContent class="pt-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
          <div class="space-y-2 md:col-span-2">
            <Label for="name" class="text-[#0c1a2e]">Name</Label>
            <div class="relative">
              <UserRound class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#5b708b]" />
              <Input
                id="name"
                v-model="form.name"
                type="text"
                placeholder="Enter receptionist name"
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
                placeholder="receptionist@example.com"
                class="h-11 border-[#d9e2ec] bg-white pl-10 text-[#0c1a2e] placeholder:text-[#8aa0b8] focus-visible:ring-[#0c1a2e]"
              />
            </div>
            <p v-if="form.errors.email" class="text-sm text-red-500">
              {{ form.errors.email }}
            </p>
          </div>

          <div class="space-y-2">
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

          <div v-if="mode === 'create'" class="space-y-2">
            <Label for="password" class="text-[#0c1a2e]">Password</Label>
            <Input
              id="password"
              v-model="form.password"
              type="password"
              placeholder="Create a password"
              class="h-11 border-[#d9e2ec] bg-white text-[#0c1a2e] placeholder:text-[#8aa0b8] focus-visible:ring-[#0c1a2e]"
            />
            <p v-if="form.errors.password" class="text-sm text-red-500">
              {{ form.errors.password }}
            </p>
          </div>
        </div>

        <div class="mt-8 flex flex-col gap-3 border-t border-[#e7edf5] pt-6 sm:flex-row sm:items-center sm:justify-between">
          <p class="text-sm text-[#5b708b]">
            Review the details carefully before saving this receptionist account.
          </p>

          <div class="flex flex-col gap-3 sm:flex-row">
            <Link :href="backHref">
              <Button
                type="button"
                variant="outline"
                class="h-11 rounded-xl border-[#d9e2ec] px-6 text-sm font-medium text-[#0c1a2e] hover:bg-[#f8fbff]"
              >
                Cancel
              </Button>
            </Link>

            <Button
              type="submit"
              :disabled="form.processing"
              class="h-11 rounded-xl bg-[#0c1a2e] px-6 text-sm font-medium text-white hover:bg-[#132844]"
            >
              {{ form.processing ? 'Saving...' : mode === 'create' ? 'Create Receptionist' : 'Save Changes' }}
            </Button>
          </div>
        </div>
      </CardContent>
    </Card>
  </form>
</template>
