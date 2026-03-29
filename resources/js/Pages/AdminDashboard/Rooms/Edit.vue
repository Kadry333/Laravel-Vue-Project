<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
    Select, SelectContent, SelectItem,
    SelectTrigger, SelectValue,
} from '@/components/ui/select'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    room:   Object,
    floors: Array,
})

const form = useForm({
    number:   props.room.number,
    capacity: props.room.capacity,
    price:    props.room.price ,
    floor_id: String(props.room.floor_id),
})

function submit() {
    form.transform(data => ({
        ...data,
        price:   data.price
    })).post(`/admins/rooms/${props.room.id}`)
}
</script>

<template>
    <div class="w-full">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Edit Room #{{ room.number }}</h1>
                <p class="text-sm text-slate-500 mt-1">Update the room details.</p>
            </div>
            <Link href="/admins/rooms">
                <Button variant="outline">Back</Button>
            </Link>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5 w-full">

            <div class="space-y-2">
                <Label>Room Number</Label>
                <Input v-model="form.number" placeholder="e.g. 101" required />
                <p v-if="form.errors.number" class="text-xs text-red-500">{{ form.errors.number }}</p>
            </div>

            <div class="space-y-2">
                <Label>Floor</Label>
                <Select v-model="form.floor_id" required>
                    <SelectTrigger class="w-full">
                        <SelectValue placeholder="Select a floor" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="floor in floors"
                            :key="floor.id"
                            :value="String(floor.id)"
                        >
                            {{ floor.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <p v-if="form.errors.floor_id" class="text-xs text-red-500">{{ form.errors.floor_id }}</p>
            </div>

            <div class="space-y-2">
                <Label>Capacity</Label>
                <Input v-model="form.capacity" type="number" min="1" placeholder="e.g. 2" required />
                <p v-if="form.errors.capacity" class="text-xs text-red-500">{{ form.errors.capacity }}</p>
            </div>

            <div class="space-y-2">
                <Label>Price per night ($)</Label>
                <Input v-model="form.price" type="number" min="1" placeholder="e.g. 120" required />
                <p v-if="form.errors.price" class="text-xs text-red-500">{{ form.errors.price }}</p>
            </div>

            <div class="flex gap-2 justify-end pt-2">
                <Link href="/admins/rooms">
                    <Button variant="outline">Cancel</Button>
                </Link>
                <Button @click="submit" :disabled="form.processing">
                    {{ form.processing ? 'Saving...' : 'Update Room' }}
                </Button>
            </div>

        </div>
    </div>
</template>
