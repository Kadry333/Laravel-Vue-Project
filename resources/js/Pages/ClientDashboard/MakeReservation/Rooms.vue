<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue'
import { ref } from 'vue'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Users, DollarSign, Building2 } from 'lucide-vue-next'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'


defineOptions({ layout: ClientLayout })

const props = defineProps({
    rooms: Array
})

const open = ref(false)
const selected = ref(null)

function openBooking(room) {
    selected.value = room
    open.value = true
}
</script>

<template>
    <div>

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900">Make a Reservation</h1>
            <p class="text-sm text-slate-500 mt-1">Browse available rooms and book instantly</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="room in rooms" :key="room.id"
                class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-2xl font-bold text-slate-900">#{{ room.number }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ room.floor.name }}</p>
                    </div>
                    <Badge class="bg-emerald-50 text-emerald-700 border-emerald-200">
                        Available
                    </Badge>
                </div>

                <div class="flex gap-4 mb-4">
                    <div class="flex items-center gap-1.5 text-sm text-slate-500">
                        <Users class="w-4 h-4" />
                        {{ room.capacity }} guests
                    </div>
                    <div class="flex items-center gap-1.5 text-sm text-slate-500">
                        <DollarSign class="w-4 h-4" />
                        ${{ room.price }}/night
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                    <div class="flex items-center gap-1.5 text-xs text-slate-400">
                        <Building2 class="w-3.5 h-3.5" />
                        {{ room.floor.name }}
                    </div>
                    <Button size="sm" @click="openBooking(room)">
                        Book Now
                    </Button>
                </div>
            </div>
        </div>

        <Dialog v-model:open="open">
            <DialogContent class="max-w-md">
                <DialogHeader>
                    <DialogTitle>Complete Reservation</DialogTitle>
                </DialogHeader>

                <div v-if="selected" class="space-y-4">

                    <div class="bg-slate-50 rounded-lg p-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Room</span>
                            <span class="font-semibold">#{{ selected.number }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Floor.name</span>
                            <span class="font-semibold">{{ selected.floor.name }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Capacity</span>
                            <span class="font-semibold">{{ selected.capacity }} persons</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Price</span>
                            <span class="font-semibold">${{ selected.price }}/night</span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label>Accompany Number</Label>
                        <Input type="number" min="0" :max="selected.capacity" />
                        <p v-if="errorMsg" class="text-xs text-red-500">{{ errorMsg }}</p>
                    </div>

                    <div class="flex gap-2 justify-end pt-2">
                        <Button variant="outline" @click="open = false">Cancel</Button>
                        <Button @click="confirm">Confirm Booking</Button>
                    </div>

                </div>
            </DialogContent>
        </Dialog>

    </div>
</template>