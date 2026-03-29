<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import {
    Sidebar, SidebarContent, SidebarFooter,
    SidebarGroup, SidebarGroupLabel,
    SidebarHeader, SidebarMenu,
} from '@/components/ui/sidebar'
import {
    DropdownMenu, DropdownMenuContent, DropdownMenuItem,
    DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import {
    Hotel, ChevronDown, Settings, LogOut,
    LayoutDashboard, UserCog, UserCheck, Users, BarChart3, Bed, Layers, Calendar
} from 'lucide-vue-next'
import AdminNavItem from './AdminNavItem.vue'

const isAdmin = computed(() => user.value.roles.some(role => role.name === 'admin'));
const isManager = computed(() => user.value.roles.some(role => role.name === 'manager'));
const isReceptionist = computed(() => user.value.roles.some(role => role.name === 'receptionist'));

const navItems = computed(() => isAdmin.value ? [
    { label: 'Dashboard', icon: LayoutDashboard, href: '/admins/dashboard' },
    { label: 'Manage Managers', icon: UserCog, href: '/admins/managers' },
    { label: 'Manage Receptionists', icon: UserCheck, href: '/admins/receptionists' },
    { label: 'Manage Clients', icon: Users, href: '/admins/clients' },
    { label: 'Manage Rooms', icon: Bed, href: '/admins/rooms' },
    { label: 'Manage Floors', icon: Layers, href: '/admins/floors' },
    { label: 'Manage Reservations', icon: Calendar, href: '/admins/reservations' },
] : isManager.value ? [
    { label: 'Dashboard', icon: LayoutDashboard, href: '/admins/dashboard' },
    { label: 'Manage Receptionists', icon: UserCheck, href: '/admins/receptionists' },
    { label: 'Manage Clients', icon: Users, href: '/admins/clients' },
    { label: 'Manage Rooms', icon: Bed, href: '/admins/rooms' },
    { label: 'Manage Floors', icon: Layers, href: '/admins/floors' },
    { label: 'Manage Reservations', icon: Calendar, href: '/admins/reservations' },
] : isReceptionist.value? [
    { label: 'Dashboard', icon: LayoutDashboard, href: '/admins/dashboard' },
    { label: 'Manage Clients', icon: Users, href: '/admins/clients' },
    { label: 'Approved Clients', icon: UserCheck, href: '/admins/approved-clients' },
    { label: 'Manage Reservations', icon: Calendar, href: '/admins/reservations' },
] : [

]);

const page = usePage()
const DEFAULT_AVATAR_PATH = '/images/default.png'

const authUser = computed(() => page.props.auth?.user ?? null)

const profileHref = computed(() => page.props.auth?.profile_route ?? '/profile')

const avatarSrc = computed(() => {
    const path = authUser.value?.avatar_image

    if (!path || path === 'default.png') {
        return DEFAULT_AVATAR_PATH
    }

    if (
        path.startsWith('http://') ||
        path.startsWith('https://') ||
        path.startsWith('/')
    ) {
        return path
    }

    return `/storage/${path}`
})

const initials = computed(() => {
    const name = authUser.value?.name ?? 'John Doe'

    return name
        .split(' ')
        .map((part) => part[0] ?? '')
        .join('')
        .slice(0, 2)
        .toUpperCase()
})

const user = computed(() => page.props.auth.user);


</script>

<template>
    <Sidebar class="border-r-0" :style="{
        '--sidebar-background': '#0c1a2e',
        '--sidebar-foreground': '#7a8fa6',
        '--sidebar-border': 'rgba(255,255,255,0.05)',
        '--sidebar-accent': 'rgba(14,165,233,0.15)',
        '--sidebar-accent-foreground': '#38bdf8',
        '--sidebar-primary': '#0ea5e9',
    }">
        <SidebarHeader class="px-4 py-5 border-b border-white/5">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-[10px] shrink-0 bg-gradient-to-br from-sky-500 to-sky-300
                    flex items-center justify-center shadow-[0_4px_12px_rgba(14,165,233,0.35)]">
                    <Hotel class="w-5 h-5 text-white" />
                </div>
                <div>
                    <p class="font-serif text-[17px] font-semibold text-[#f0f6ff] tracking-wide leading-tight">
                        Grand Luxury
                    </p>
                    <p class="text-[10px] text-[#5a7a96] mt-0.5">Hotel Management</p>
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
                    <AdminNavItem v-for="item in navItems" :key="item.href" :label="item.label" :icon="item.icon"
                        :href="item.href" />
                </SidebarMenu>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter class="p-3 border-t border-white/5">
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <button class="w-full flex items-center gap-2.5 px-2.5 py-2 rounded-[9px]
                         hover:bg-[#162840] transition-colors duration-150 text-left">
                        <Avatar class="w-[34px] h-[34px] shrink-0">
                            <AvatarImage :src="avatarSrc" :alt="authUser?.name ?? 'User'" class="object-cover" />
                            <AvatarFallback class="text-[12px] font-bold text-white"
                                style="background: linear-gradient(135deg,#8b5cf6,#ec4899)">
                                {{ initials }}
                            </AvatarFallback>
                        </Avatar>
                        <div class="flex-1 min-w-0">
                            <p class="text-[12.5px] font-semibold text-[#dde8f0] truncate">{{ authUser?.name ?? 'John Doe' }}</p>
                            <p class="text-[11px] text-[#5a7a96] mt-0.5">{{ page.props.auth?.role_label ?? 'Administrator' }}</p>
                        </div>
                        <ChevronDown class="w-3.5 h-3.5 text-[#3a5a76] shrink-0" />
                    </button>
                </DropdownMenuTrigger>

                <DropdownMenuContent side="top" align="start" class="w-56 mb-1">
                    <DropdownMenuLabel class="font-normal">
                        <p class="text-[13px] font-semibold">{{ authUser?.name ?? 'John Doe' }}</p>
                        <p class="text-[11.5px] text-muted-foreground">{{ authUser?.email ?? 'john@luxestay.com' }}</p>
                    </DropdownMenuLabel>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem as-child>
                        <Link :href="profileHref" class="flex items-center gap-2 cursor-pointer">
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
