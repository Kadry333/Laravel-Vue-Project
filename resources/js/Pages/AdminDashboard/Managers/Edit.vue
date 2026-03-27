<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { ref } from 'vue'
import { toast } from 'vue-sonner'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    manager: Object,
})

const defaultAvatar = props.manager.avatar_image
    ? `/storage/${props.manager.avatar_image}`
    : '/images/default.png'

const preview = ref(defaultAvatar)

const form = useForm({
    name: props.manager.name,
    email: props.manager.email,
    password: '',
    national_id: props.manager.national_id,
    avatar_image: null,
})

function handleImage(e) {
    const file = e.target.files[0]

    if (!file) return

    if (!file.type.startsWith('image/')) {
        toast.error('Please select a valid image file.')
        return
    }

    form.avatar_image = file
    preview.value = URL.createObjectURL(file)
}

function submit() {
    form.post(`/admins/managers/${props.manager.id}`, {
        forceFormData: true,
        _method: 'PUT',
        onSuccess: () => {
            toast.success('Manager updated successfully')
        },
    })
}
</script>

<template>
    <div class="w-full">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Edit Manager</h1>
                <p class="text-sm text-slate-500 mt-1">Fill in the details to edit the manager.</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5 w-full">

            <div class="space-y-2" >
              <Label>Image</Label>
              <label :class="{ 'pointer-events-none opacity-50': form.processing }" class="cursor-pointer">
                <div class="w-24 h-24 rounded-full overflow-hidden border">
                  <img :src="preview" class="w-full h-full object-cover" />
                </div>

                <input type="file" class="hidden" @change="handleImage" :disabled="form.processing" />
              </label>
              <p v-if="form.errors.avatar_image" class="text-xs text-red-500">{{ form.errors.avatar_image }}</p>

            </div>

            <div class="space-y-2">
                <Label>Name</Label>
                <Input v-model="form.name" placeholder="e.g. Amr Shoukry" required type="text" :disabled="form.processing" />
                <p v-if="form.errors.name" class="text-xs text-red-500">{{ form.errors.name }}</p>
            </div>

            <div class="space-y-2">
                <Label>National ID</Label>
                <Input v-model="form.national_id" placeholder="e.g. 12345678901234" type="string" required :disabled="form.processing" />
                <p v-if="form.errors.national_id" class="text-xs text-red-500">{{ form.errors.national_id }}</p>
            </div>

            <div class="space-y-2">
                <Label>Email</Label>
                <Input v-model="form.email" placeholder="e.g. amr@gmail.com" type="email" required :disabled="form.processing" />
                <p v-if="form.errors.email" class="text-xs text-red-500">{{ form.errors.email }}</p>
            </div>

            <div class="space-y-2">
                <Label>Password</Label>
                <Input v-model="form.password" placeholder="e.g. 123456" type="password" required :disabled="form.processing" />
                <p v-if="form.errors.password" class="text-xs text-red-500">{{ form.errors.password }}</p>
            </div>


            <div class="flex gap-2 justify-end pt-2">
                <Link href="/admins/managers">
                    <Button variant="outline">Cancel</Button>
                </Link>
                <Button @click="submit" :disabled="form.processing">
                    {{ form.processing ? 'Saving...' : 'Update Manager' }}
                </Button>
            </div>

        </div>
    </div>
</template>
