<script setup>
import { computed, ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import { Camera, Check, ChevronsUpDown, Mail, MapPin, UserRound } from 'lucide-vue-next'
import { cn } from '@/lib/utils'

import ClientLayout from '@/Layouts/ClientLayout.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from '@/components/ui/command'

defineOptions({ layout: ClientLayout })

const DEFAULT_AVATAR_PATH = '/images/default.png'

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
  countries: {
    type: Array,
    default: () => [],
  },
})

const countryOpen = ref(false)
const avatarInput = ref(null)
const imageLoadFailed = ref(false)

const form = useForm({
  name: props.user?.name ?? '',
  email: props.user?.email ?? '',
  country_id: props.user?.country_id ?? null,
  gender: props.user?.gender ?? '',
  avatar_image: null,
})

const selectedCountryName = computed(() => {
  const selectedCountry = props.countries.find(
    (country) => Number(country.id) === Number(form.country_id)
  )

  return selectedCountry?.official_name ?? props.user?.country?.official_name ?? 'Select country'
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

const handleImageError = () => {
  imageLoadFailed.value = true
}

const submit = () => {
  form.patch(route('client.profile.update'), {
    forceFormData: true,
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="My Profile" />

  <div class="min-h-screen bg-[#f4f7fb] px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto w-full max-w-5xl space-y-6">
      <div class="rounded-3xl border border-[#d9e2ec] bg-white p-6 shadow-sm sm:p-8">
        <div class="space-y-2">
          <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#5b708b]">
            Client Profile
          </p>
          <h1 class="text-3xl font-semibold tracking-tight text-[#0c1a2e] sm:text-4xl">
            My Profile
          </h1>
          <p class="max-w-2xl text-sm leading-6 text-[#5b708b]">
            Keep your personal details up to date and choose the profile image that
            appears across your client account.
          </p>
        </div>
      </div>

      <form
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
                  @error="handleImageError"
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
                Upload a JPG or PNG image up to 2MB.
              </p>
            </div>
          </div>

          <div class="mt-6 space-y-2">
            <Label class="text-[#0c1a2e]">Profile Image</Label>
            <input
              id="avatar_image"
              ref="avatarInput"
              type="file"
              accept=".jpg,.jpeg,.png"
              class="sr-only"
              @change="handleAvatarChange"
            />
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
              Review and update the details associated with your account.
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

            <div class="space-y-2">
              <Label for="country" class="text-[#0c1a2e]">Country</Label>

              <Popover v-model:open="countryOpen">
                <PopoverTrigger as-child>
                  <Button
                    variant="outline"
                    role="combobox"
                    :aria-expanded="countryOpen"
                    class="h-11 w-full justify-between border-[#d9e2ec] bg-white px-3 font-normal text-[#0c1a2e] hover:bg-[#f8fbff]"
                  >
                    <span class="flex min-w-0 items-center gap-2 truncate">
                      <MapPin class="h-4 w-4 shrink-0 text-[#5b708b]" />
                      <span class="truncate">
                        {{ selectedCountryName }}
                      </span>
                    </span>
                    <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 text-[#5b708b]" />
                  </Button>
                </PopoverTrigger>

                <PopoverContent
                  align="start"
                  class="w-[var(--radix-popover-trigger-width)] border border-[#d9e2ec] bg-white p-0 shadow-xl"
                >
                  <Command class="bg-white text-[#0c1a2e]">
                    <CommandInput
                      placeholder="Search country..."
                      class="h-11 border-b border-[#e7edf5] bg-white text-[#0c1a2e] placeholder:text-[#8aa0b8]"
                    />
                    <CommandEmpty class="py-6 text-center text-sm text-[#5b708b]">
                      No country found.
                    </CommandEmpty>

                    <CommandList class="max-h-64 bg-white">
                      <CommandGroup class="bg-white">
                        <CommandItem
                          v-for="country in countries"
                          :key="country.id"
                          :value="country.official_name"
                          class="cursor-pointer bg-white text-[#0c1a2e] aria-selected:bg-[#eaf1f8] aria-selected:text-[#0c1a2e] data-[selected=true]:bg-[#eaf1f8] data-[selected=true]:text-[#0c1a2e]"
                          @select="
                            () => {
                              form.country_id = Number(country.id)
                              countryOpen = false
                            }
                          "
                        >
                          <Check
                            :class="
                              cn(
                                'mr-2 h-4 w-4 text-[#0c1a2e]',
                                Number(form.country_id) === Number(country.id) ? 'opacity-100' : 'opacity-0'
                              )
                            "
                          />
                          {{ country.official_name }}
                        </CommandItem>
                      </CommandGroup>
                    </CommandList>
                  </Command>
                </PopoverContent>
              </Popover>

              <p v-if="form.errors.country_id" class="text-sm text-red-500">
                {{ form.errors.country_id }}
              </p>
            </div>

            <div class="space-y-2">
              <Label for="gender" class="text-[#0c1a2e]">Gender</Label>
              <Select v-model="form.gender">
                <SelectTrigger
                  id="gender"
                  class="h-11 w-full border-[#d9e2ec] bg-white text-[#0c1a2e] focus:ring-[#0c1a2e]"
                >
                  <SelectValue placeholder="Select gender" />
                </SelectTrigger>

                <SelectContent class="border border-[#d9e2ec] bg-white text-[#0c1a2e] shadow-xl">
                  <SelectItem
                    value="male"
                    class="text-[#0c1a2e] focus:bg-[#eaf1f8] focus:text-[#0c1a2e]"
                  >
                    Male
                  </SelectItem>
                  <SelectItem
                    value="female"
                    class="text-[#0c1a2e] focus:bg-[#eaf1f8] focus:text-[#0c1a2e]"
                  >
                    Female
                  </SelectItem>
                </SelectContent>
              </Select>

              <p v-if="form.errors.gender" class="text-sm text-red-500">
                {{ form.errors.gender }}
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
    </div>
  </div>
</template>
