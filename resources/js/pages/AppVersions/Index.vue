<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { route } from 'ziggy-js';
import type { BreadcrumbItem } from '@/types';

defineProps<{
    versions: Array<{
        id: number;
        version_number: string;
        release_date: string;
        release_notes: string[] | null;
        is_mandatory: boolean;
        platform: string | null;
        download_url: string | null;
    }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'App Versions',
        href: '/app-versions',
    },
];

const deleteVersion = (id: number) => {
    if (confirm('Are you sure you want to delete this version?')) {
        router.delete(route('app-versions.destroy', id));
    }
};
</script>

<template>
    <Head title="App Versions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    App Versions
                </h2>
                <Link :href="route('app-versions.create')">
                    <Button>Add New Version</Button>
                </Link>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Version</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Platform</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Mandatory?</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                <tr v-for="version in versions" :key="version.id">
                                    <td class="px-6 py-4 whitespace-nowrap font-bold">{{ version.version_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ new Date(version.release_date).toLocaleDateString() }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap uppercase">{{ version.platform || 'All' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="version.is_mandatory" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Yes
                                        </span>
                                        <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            No
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link :href="route('app-versions.edit', version.id)" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</Link>
                                        <button @click="deleteVersion(version.id)" class="text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                                <tr v-if="versions.length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No versions found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
