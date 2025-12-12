<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardContent, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { BookOpen } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import { ref } from 'vue';

const props = defineProps<{
    courses: Array<{
        id: number;
        kode_mk: string;
        nama_mk: string;
        lecturer: {
            name: string;
        };
        sks: number;
        kelas: string;
        semester: number;
    }>;
    filters: {
        semester?: string;
        angkatan?: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Manajemen Absensi',
        href: '/lecture/attendance',
    },
];

const selectedAngkatan = ref(props.filters.angkatan || '');
const selectedSemester = ref(props.filters.semester || '');

const applyFilters = () => {
    router.get('/lecture/attendance', {
        angkatan: selectedAngkatan.value,
        semester: selectedSemester.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const years = [2020, 2021, 2022, 2023, 2024, 2025];
</script>

<template>
    <Head title="Manajemen Absensi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Pilih Mata Kuliah</h2>

            <!-- Filters -->
            <div class="bg-white p-6 rounded-lg shadow-sm mb-8 dark:bg-gray-800">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                    <div>
                        <Label for="angkatan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Angkatan</Label>
                         <select 
                            id="angkatan" 
                            v-model="selectedAngkatan" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                            <option value="" disabled>Pilih Tahun Angkatan</option>
                            <option v-for="year in years" :key="year" :value="year">
                                Angkatan {{ year }}
                            </option>
                        </select>
                    </div>
                    <div>
                         <Label for="semester" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Semester</Label>
                         <select 
                            id="semester" 
                            v-model="selectedSemester" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                            <option value="" disabled>Pilih Semester</option>
                            <option v-for="sem in 8" :key="sem" :value="sem">
                                Semester {{ sem }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex justify-end">
                    <Button @click="applyFilters" :disabled="!selectedAngkatan || !selectedSemester">
                        Tampilkan Mata Kuliah
                    </Button>
                </div>
            </div>

            <!-- Course Grid (Only shown if filters active) -->
            <div v-if="filters.semester && filters.angkatan">
                <div v-if="courses.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Card v-for="course in courses" :key="course.id" class="hover:shadow-lg transition-shadow">
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">
                                {{ course.kode_mk }}
                            </CardTitle>
                            <BookOpen class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">{{ course.nama_mk }}</div>
                            <p class="text-xs text-muted-foreground mt-1">
                                Kelas {{ course.kelas }} - Semester {{ course.semester }}
                            </p>
                             <p class="text-xs text-muted-foreground">
                                Dosen: {{ course.lecturer?.name }}
                            </p>
                        </CardContent>
                        <CardFooter>
                            <Link :href="route('lecture.attendance.show', course.id)" class="w-full">
                                <Button class="w-full">Lihat Absensi</Button>
                            </Link>
                        </CardFooter>
                    </Card>
                </div>
                <div v-else class="text-center py-10 text-gray-500 bg-white rounded-lg dark:bg-gray-800">
                    Tidak ada mata kuliah ditemukan untuk filter ini.
                </div>
            </div>
             <div v-else class="text-center py-10 text-gray-400 italic">
                Silakan pilih Angkatan dan Semester terlebih dahulu untuk melihat daftar mata kuliah.
            </div>
        </div>
    </AppLayout>
</template>
