<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { BookOpen, Calendar, Clock } from 'lucide-vue-next';

interface Props {
    adminStats?: {
        totalStudents: number;
        totalCourses: number;
        totalLectures: number;
    };
    studentStats?: {
        coursesTaken: number;
    };
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            
            <!-- Admin Dashboard -->
            <div v-if="adminStats" class="grid auto-rows-min gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader>
                        <CardTitle>Total Mahasiswa</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ adminStats.totalStudents }}</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader>
                        <CardTitle>Total Mata Kuliah</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ adminStats.totalCourses }}</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader>
                        <CardTitle>Total Dosen</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ adminStats.totalLectures }}</div>
                    </CardContent>
                </Card>
            </div>

            <!-- Student Dashboard -->
            <div v-if="studentStats" class="grid auto-rows-min gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Mata Kuliah Diambil
                        </CardTitle>
                        <BookOpen class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ studentStats.coursesTaken }}</div>
                        <p class="text-xs text-muted-foreground">
                            Semester ini
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Kehadiran
                        </CardTitle>
                        <Clock class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">--%</div>
                        <p class="text-xs text-muted-foreground">
                            Rata-rata kehadiran
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Jadwal Hari Ini
                        </CardTitle>
                        <Calendar class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">--</div>
                        <p class="text-xs text-muted-foreground">
                            Mata kuliah
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Actions Shortcut for Student -->
             <div v-if="studentStats" class="mt-8">
                <h3 class="text-lg font-semibold mb-4">Aksi Cepat</h3>
                <div class="flex gap-4">
                    <Link href="/student/course-registration" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-md transition-all hover:bg-blue-700">
                        Isi KRS
                    </Link>
                    <Link href="/student/my-courses" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition-all hover:bg-slate-50">
                        Lihat Jadwal
                    </Link>
                </div>
            </div>

        </div>
    </AppLayout>
</template>
