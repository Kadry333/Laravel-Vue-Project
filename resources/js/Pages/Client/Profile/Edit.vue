<script setup>
import { computed, ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import { Check, ChevronsUpDown } from 'lucide-vue-next'
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
console.log(props.countries)
const countryOpen = ref(false)

const form = useForm({
    name: props.user?.name ?? '',
    email: props.user?.email ?? '',
    country: props.user?.country ?? '',
    gender: props.user?.gender ?? '',
    avatar_image: null,
    _method: 'patch',
})

const previewImage = computed(() => {
    if (form.avatar_image instanceof File) {
        return URL.createObjectURL(form.avatar_image)
    }

    if (props.user?.avatar_image) {
        if (
            props.user.avatar_image.startsWith('http://') ||
            props.user.avatar_image.startsWith('https://') ||
            props.user.avatar_image.startsWith('/')
        ) {
            return props.user.avatar_image
        }

        return `/storage/${props.user.avatar_image}`
    }

    return '/images/default.png'
})

const submit = () => {
    form.post(route('client.profile.update'), {
        forceFormData: true,
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="My Profile" />

    <div class="p-6">
        <div class="mx-auto max-w-5xl">
            <div class="rounded-2xl border bg-white p-8 shadow-sm">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold tracking-tight">My Profile</h1>
                    <p class="mt-2 text-sm text-muted-foreground">
                        Update your personal information and profile picture.
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-8">
                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                        <div class="space-y-4">
                            <div class="flex flex-col items-center gap-4">
                                <div class="h-40 w-40 overflow-hidden rounded-full border bg-muted">
                                    <img
                                        :src="previewImage"
                                        alt="Profile Image"
                                        class="h-full w-full object-cover"
                                    >
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="avatar_image">Profile Image</Label>
                                <Input
                                    id="avatar_image"
                                    type="file"
                                    accept=".jpg,.jpeg,.png"
                                    @change="form.avatar_image = $event.target.files[0]"
                                />
                                <p v-if="form.errors.avatar_image" class="text-sm text-red-500">
                                    {{ form.errors.avatar_image }}
                                </p>
                            </div>
                        </div>

                        <div class="lg:col-span-2 space-y-6">
                            <div class="space-y-2">
                                <Label for="name">Name</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    placeholder="Enter your name"
                                />
                                <p v-if="form.errors.name" class="text-sm text-red-500">
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="email">Email</Label>
                                <Input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    placeholder="Enter your email"
                                />
                                <p v-if="form.errors.email" class="text-sm text-red-500">
                                    {{ form.errors.email }}
                                </p>
                            </div>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="country">Country</Label>

                                    <Popover v-model:open="countryOpen">
                                        <PopoverTrigger as-child>
                                            <Button
                                                variant="outline"
                                                role="combobox"
                                                :aria-expanded="countryOpen"
                                                class="w-full justify-between"
                                            >
                                                {{ form.country || 'Select country' }}
                                                <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                            </Button>
                                        </PopoverTrigger>

                                        <PopoverContent class="w-[var(--radix-popover-trigger-width)] p-0">
                                            <Command>
                                                <CommandInput placeholder="Search country..." />
                                                <CommandEmpty>No country found.</CommandEmpty>

                                                <CommandList>
                                                    <CommandGroup>
                                                        <CommandItem
                                                            v-for="country in countries"
                                                            :key="country"
                                                            :value="country"
                                                            @select="
                                                                () => {
                                                                    form.country = country
                                                                    countryOpen = false
                                                                }
                                                            "
                                                        >
                                                            <Check
                                                                :class="
                                                                    cn(
                                                                        'mr-2 h-4 w-4',
                                                                        form.country === country
                                                                            ? 'opacity-100'
                                                                            : 'opacity-0'
                                                                    )
                                                                "
                                                            />
                                                            {{ country }}
                                                        </CommandItem>
                                                    </CommandGroup>
                                                </CommandList>
                                            </Command>
                                        </PopoverContent>
                                    </Popover>

                                    <p v-if="form.errors.country" class="text-sm text-red-500">
                                        {{ form.errors.country }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="gender">Gender</Label>
                                    <Select v-model="form.gender">
                                        <SelectTrigger id="gender" class="w-full">
                                            <SelectValue placeholder="Select gender" />
                                        </SelectTrigger>

                                        <SelectContent>
                                            <SelectItem value="male">Male</SelectItem>
                                            <SelectItem value="female">Female</SelectItem>
                                        </SelectContent>
                                    </Select>

                                    <p v-if="form.errors.gender" class="text-sm text-red-500">
                                        {{ form.errors.gender }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <Button type="submit" :disabled="form.processing">
                                    {{ form.processing ? 'Saving...' : 'Save Changes' }}
                                </Button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>