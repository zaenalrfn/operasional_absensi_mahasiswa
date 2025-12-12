<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import type { BreadcrumbItem } from '@/types';

defineProps<{
    courses: Array<{
        id: number;
        kode_mk: string;
        nama_mk: string;
        lecturer: {
            name: string;
        };
        sks: number;
        kelas: string;
        hari: string;
        jam_mulai: string;
        jam_selesai: string;
        semester: number;
    }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Courses',
        href: '/courses',
    },
];

const deleteCourse = (id: number) => {
    if (confirm('Are you sure you want to delete this course?')) {
        router.delete(route('courses.destroy', id));
    }
};
</script>

<template>
    <Head title="Courses" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Daftar Mata Kuliah
                </h2>
                <Link :href="route('courses.create')">
                    <Button>Tambah Mata Kuliah</Button>
                </Link>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Kode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Mata Kuliah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Dosen</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">SKS</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Kelas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Jadwal</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                <tr v-for="course in courses" :key="course.id">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ course.kode_mk }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ course.nama_mk }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ course.lecturer?.name || '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ course.sks }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ course.kelas }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ course.hari }} ({{ course.jam_mulai }} - {{ course.jam_selesai }})
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link :href="route('courses.edit', course.id)" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</Link>
                                        <button @click="deleteCourse(course.id)" class="text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
