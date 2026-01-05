<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import type { BreadcrumbItem } from '@/types';
import { route } from 'ziggy-js';


defineProps<{
    students: Array<{
        id: number;
        name: string;
        email: string;
    }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Students',
        href: '/students',
    },
];

const deleteStudent = (id: number) => {
    if (confirm('Are you sure you want to delete this student?')) {
        router.delete(route('students.destroy', id));
    }
};
</script>

<template>
    <Head title="Students" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Daftar Mahasiswa
                </h2>
                <Link :href="route('students.create')">
                    <Button>Tambah Mahasiswa</Button>
                </Link>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Email</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                <tr v-for="student in students" :key="student.id">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ student.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ student.email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link :href="route('students.edit', student.id)" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</Link>
                                        <button @click="deleteStudent(student.id)" class="text-red-600 hover:text-red-900">Delete</button>
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
