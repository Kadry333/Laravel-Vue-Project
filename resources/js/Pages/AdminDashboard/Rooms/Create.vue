<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { computed } from 'vue'
import { useForm, Link, usePage } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
    Select, SelectContent, SelectItem,
    SelectTrigger, SelectValue,
} from '@/components/ui/select'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    floors:   Array,
    managers: Array,
})

const page    = usePage()
const isAdmin = computed(() => page.props.auth.primary_role === 'admin')

const form = useForm({
    number:     '',
    capacity:   '',
    price:      '',
    floor_id:   '',
    manager_id: '',
})

function submit() {
    form.post('/admins/rooms', {
        onSuccess: () => form.reset(),
    })
}
</script>

<template>
    <div class="w-full">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Add New Room</h1>
                <p class="text-sm text-slate-500 mt-1">Fill in the details to create a new room.</p>
            </div>
            <Link href="/admins/rooms">
                <Button variant="outline">Back</Button>
            </Link>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5 w-full">

            <div class="space-y-2">
                <Label>Room Number</Label>
                <Input v-model="form.number" placeholder="e.g. 101" />
                <p v-if="form.errors.number" class="text-xs text-red-500">{{ form.errors.number }}</p>
            </div>

            <div class="space-y-2">
                <Label>Floor</Label>
                <Select v-model="form.floor_id">
                    <SelectTrigger class="w-full">
                        <SelectValue placeholder="Select a floor" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="floor in floors" :key="floor.id" :value="String(floor.id)">
                            {{ floor.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <p v-if="form.errors.floor_id" class="text-xs text-red-500">{{ form.errors.floor_id }}</p>
            </div>

            <div class="space-y-2">
                <Label>Capacity</Label>
                <Input v-model="form.capacity" type="number" min="1" placeholder="e.g. 2" />
                <p v-if="form.errors.capacity" class="text-xs text-red-500">{{ form.errors.capacity }}</p>
            </div>

            <div class="space-y-2">
                <Label>Price per night ($)</Label>
                <Input v-model="form.price" type="number" min="1" placeholder="e.g. 120" />
                <p v-if="form.errors.price" class="text-xs text-red-500">{{ form.errors.price }}</p>
            </div>

            <!-- Manager select: Admin only -->
            <div v-if="isAdmin" class="space-y-2">
                <Label>Manager</Label>
                <Select v-model="form.manager_id">
                    <SelectTrigger class="w-full">
                        <SelectValue placeholder="Select a manager" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="manager in managers" :key="manager.id" :value="String(manager.id)">
                            {{ manager.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <p v-if="form.errors.manager_id" class="text-xs text-red-500">{{ form.errors.manager_id }}</p>
            </div>

            <div class="flex gap-2 justify-end pt-2">
                <Link href="/admins/rooms">
                    <Button variant="outline">Cancel</Button>
                </Link>
                <Button @click="submit" :disabled="form.processing">
                    {{ form.processing ? 'Saving...' : 'Create Room' }}
                </Button>
            </div>

        </div>
    </div>
</template>
