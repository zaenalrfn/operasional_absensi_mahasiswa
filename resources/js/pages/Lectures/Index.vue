<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import type { BreadcrumbItem } from '@/types';

defineProps<{
    lectures: Array<{
        id: number;
        name: string;
        email: string;
    }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Lectures',
        href: '/lectures',
    },
];

const deleteLecture = (id: number) => {
    if (confirm('Are you sure you want to delete this lecture?')) {
        router.delete(route('lectures.destroy', id));
    }
};
</script>

<template>
    <Head title="Lectures" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Daftar Dosen
                </h2>
                <Link :href="route('lectures.create')">
                    <Button>Tambah Dosen</Button>
                </Link>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        Email
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                <tr v-for="lecture in lectures" :key="lecture.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ lecture.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ lecture.email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link :href="route('lectures.edit', lecture.id)" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</Link>
                                        <button @click="deleteLecture(lecture.id)" class="text-red-600 hover:text-red-900">Delete</button>
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
