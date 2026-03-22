<script setup>
import { usePage, Link } from '@inertiajs/vue3'
import { SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar'

const props = defineProps({
  label: String,
  href:  String,
  icon:  Object,
})

const isActive = () => usePage().url === props.href
</script>

<template>
  <SidebarMenuItem>
    <SidebarMenuButton
      as-child
      :is-active="isActive()"
      class="h-9 rounded-lg text-[13.5px] font-medium text-[#7a8fa6]
             hover:bg-[#162840] hover:text-[#c8d8e8]
             data-[active=true]:bg-[rgba(14,165,233,0.15)]
             data-[active=true]:text-[#38bdf8]
             data-[active=true]:font-semibold
             transition-all duration-150"
    >
      <Link :href="href">
        <component :is="icon" class="w-[18px] h-[18px] shrink-0 opacity-70" />
        <span>{{ label }}</span>
      </Link>
    </SidebarMenuButton>
  </SidebarMenuItem>
</template>