<script setup>
import { Link } from '@inertiajs/vue3'
import {
  Sidebar, SidebarContent, SidebarFooter,
  SidebarGroup, SidebarGroupLabel,
  SidebarHeader, SidebarMenu,
} from '@/components/ui/sidebar'
import {
  DropdownMenu, DropdownMenuContent, DropdownMenuItem,
  DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import {
  Hotel, ChevronDown, Settings, LogOut,
  LayoutDashboard, CalendarDays, BedDouble,
} from 'lucide-vue-next'
import ClientNavItem from './ClientNavItem.vue'

const navItems = [
  { label: 'Dashboard',        icon: LayoutDashboard, href: '/'        },
  { label: 'My Reservations',  icon: CalendarDays,    href: '/my-reservations'  },
  { label: 'Make Reservation', icon: BedDouble,       href: '/client/rooms' },
]
</script>

<template>
  <Sidebar
    class="border-r-0"
    :style="{
      '--sidebar-background':        '#0c1a2e',
      '--sidebar-foreground':        '#7a8fa6',
      '--sidebar-border':            'rgba(255,255,255,0.05)',
      '--sidebar-accent':            'rgba(14,165,233,0.15)',
      '--sidebar-accent-foreground': '#38bdf8',
      '--sidebar-primary':           '#0ea5e9',
    }"
  >
    <SidebarHeader class="px-4 py-5 border-b border-white/5">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-[10px] shrink-0 bg-gradient-to-br from-sky-500 to-sky-300
                    flex items-center justify-center shadow-[0_4px_12px_rgba(14,165,233,0.35)]">
          <Hotel class="w-5 h-5 text-white" />
        </div>
        <div>
          <p class="font-serif text-[17px] font-semibold text-[#f0f6ff] tracking-wide leading-tight">
            LuxeStay
          </p>
          <p class="text-[10px] text-[#5a7a96] mt-0.5">Guest Portal</p>
        </div>
      </div>
    </SidebarHeader>

    <SidebarContent class="px-3 pt-2">
      <SidebarGroup>
        <SidebarGroupLabel class="text-[9.5px] font-bold uppercase tracking-[1.2px]
                                  text-[rgba(90,122,150,0.6)] px-1.5 pb-1 pt-2">
          Navigation
        </SidebarGroupLabel>
        <SidebarMenu>
          <ClientNavItem
            v-for="item in navItems"
            :key="item.href"
            :label="item.label"
            :icon="item.icon"
            :href="item.href"
          />
        </SidebarMenu>
      </SidebarGroup>
    </SidebarContent>

    <SidebarFooter class="p-3 border-t border-white/5">
      <DropdownMenu>
        <DropdownMenuTrigger as-child>
          <button class="w-full flex items-center gap-2.5 px-2.5 py-2 rounded-[9px]
                         hover:bg-[#162840] transition-colors duration-150 text-left">
            <Avatar class="w-[34px] h-[34px] shrink-0">
              <AvatarFallback
                class="text-[12px] font-bold text-white"
                style="background: linear-gradient(135deg,#8b5cf6,#ec4899)"
              >
                AM
              </AvatarFallback>
            </Avatar>
            <div class="flex-1 min-w-0">
              <p class="text-[12.5px] font-semibold text-[#dde8f0] truncate">Ahmed Mostafa</p>
              <p class="text-[11px] text-[#5a7a96] mt-0.5">Client</p>
            </div>
            <ChevronDown class="w-3.5 h-3.5 text-[#3a5a76] shrink-0" />
          </button>
        </DropdownMenuTrigger>

        <DropdownMenuContent side="top" align="start" class="w-56 mb-1">
          <DropdownMenuLabel class="font-normal">
            <p class="text-[13px] font-semibold">Ahmed Mostafa</p>
            <p class="text-[11.5px] text-muted-foreground">ahmed@email.com</p>
          </DropdownMenuLabel>
          <DropdownMenuSeparator />
          <DropdownMenuItem as-child>
            <Link :href="route('client.profile.edit')" class="flex items-center gap-2 cursor-pointer">
              <Settings class="w-4 h-4" /> Profile Settings
            </Link>
          </DropdownMenuItem>
          <DropdownMenuSeparator />
          <DropdownMenuItem as-child>
            <Link href="/logout" method="post" as="button"
                  class="flex items-center gap-2 w-full text-red-500 focus:text-red-500 cursor-pointer">
              <LogOut class="w-4 h-4" /> Sign Out
            </Link>
          </DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </SidebarFooter>
  </Sidebar>
</template>
