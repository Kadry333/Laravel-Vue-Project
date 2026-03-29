<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    clients: Array,
    receptionists: Array,
})
</script>

<template>
    <div v-for="client in clients" :key="client.id">
        {{ client.id }} {{ client.gender }} {{ client.country.official_name }}
    </div>
    <div>=======================================================================</div>
    <div v-for="receptionist in receptionists" :key="receptionist.id">
        {{ receptionist.id }} {{ receptionist.gender }}
    </div>
</template>