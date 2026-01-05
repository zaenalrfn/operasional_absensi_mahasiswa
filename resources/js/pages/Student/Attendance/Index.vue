<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import type { BreadcrumbItem } from '@/types';

defineProps<{
    courses: Array<{
        id: number;
        nama_mk: string;
        kode_mk: string;
        sks: number;
        jadwal: string;
        meetings: Record<number, string | null>;
    }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Kehadiran',
        href: '/student/attendance',
    },
];

const getStatusClass = (status: string | null) => {
    switch (status) {
        case 'H': return 'bg-green-100 text-green-800 font-bold';
        case 'I': return 'bg-blue-100 text-blue-800 font-bold';
        case 'S': return 'bg-yellow-100 text-yellow-800 font-bold';
        case 'A': return 'bg-red-100 text-red-800 font-bold';
        default: return '';
    }
};
</script>

<template>
    <Head title="Kehadiran" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 w-full">
            <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Rekapitulasi Kehadiran</h2>

            <Card>
                <CardContent class="p-0 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow class="bg-gray-50 dark:bg-gray-800 border-b">
                                <TableHead rowspan="2" class="w-12 text-center border-r">No</TableHead>
                                <TableHead rowspan="2" class="min-w-[250px] border-r">Mata Kuliah</TableHead>
                                <TableHead rowspan="2" class="w-16 text-center border-r">SKS</TableHead>
                                <TableHead rowspan="2" class="min-w-[200px] border-r">Jadwal</TableHead>
                                <TableHead colspan="14" class="text-center font-bold border-b">Pertemuan</TableHead>
                            </TableRow>
                            <TableRow class="bg-gray-50 dark:bg-gray-800 text-xs">
                                <TableHead v-for="i in 14" :key="i" class="w-8 text-center border-r last:border-r-0">{{ i }}</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="(course, index) in courses" :key="course.id" class="hover:bg-gray-50 dark:hover:bg-gray-900/50">
                                <TableCell class="text-center font-medium border-r">{{ index + 1 }}</TableCell>
                                <TableCell class="border-r">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ course.nama_mk }}</div>
                                    <div class="text-xs text-gray-500">{{ course.kode_mk }}</div>
                                </TableCell>
                                <TableCell class="text-center border-r">{{ course.sks }}</TableCell>
                                <TableCell class="text-sm text-gray-600 dark:text-gray-400 border-r">{{ course.jadwal }}</TableCell>
                                
                                <TableCell v-for="i in 14" :key="i" class="p-0 text-center border-r last:border-r-0 aspect-square">
                                    <div v-if="course.meetings[i]" class="flex items-center justify-center w-full h-full min-h-[40px] border-transparent" :class="getStatusClass(course.meetings[i])">
                                        {{ course.meetings[i] }}
                                    </div>
                                    <div v-else class="min-h-[40px]"></div>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="courses.length === 0">
                                <TableCell colspan="18" class="text-center py-8 text-gray-500">
                                    Belum ada data mata kuliah yang diambil.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
