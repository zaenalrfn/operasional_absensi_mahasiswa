<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, ClipboardCheck, ClipboardList, Folder, GraduationCap, LayoutGrid, MapPin, Users, Smartphone } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth.user);

const allNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Manajemen Absensi',
        href: '/lecture/attendance',
        icon: ClipboardCheck,
    },
    {
        title: 'Dosen',
        href: '/lectures',
        icon: Users,
    },
    {
        title: 'Lokasi Absensi',
        href: '/attendance-locations',
        icon: MapPin,
    },
    {
        title: 'Mata Kuliah',
        href: '/courses',
        icon: BookOpen,
    },
    {
        title: 'Mahasiswa',
        href: '/students',
        icon: GraduationCap,
    },
    {
        title: 'KRS (Mahasiswa)',
        href: '/student/course-registration',
        icon: ClipboardList,
    },
    {
        title: 'Mata Kuliah Saya',
        href: '/student/my-courses',
        icon: BookOpen,
    },
    {
        title: 'Kehadiran',
        href: '/student/attendance',
        icon: ClipboardCheck,
    },
    {
        title: 'App Versions',
        href: '/app-versions',
        icon: Smartphone,
    },
];

const mainNavItems = computed(() => {
    // @ts-ignore
    const roles = user.value?.roles || [];
    
    if (roles.includes('super-admin')) {
        return allNavItems;
    }

    if (roles.includes('admin')) {
        return allNavItems.filter(item => item.title !== 'Lokasi Absensi');
    }

    if (roles.includes('dosen')) {
        return allNavItems.filter(item => 
            ['Dashboard', 'Manajemen Absensi', 'Dosen'].includes(item.title)
        );
    }

    if (roles.includes('mahasiswa')) {
        return allNavItems.filter(item => 
            ['Dashboard', 'KRS (Mahasiswa)', 'Mata Kuliah Saya', 'Kehadiran'].includes(item.title)
        );
    }

    return [];
});

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
