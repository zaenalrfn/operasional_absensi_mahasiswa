<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { Badge } from '@/components/ui/badge'; 

interface Course {
    id: number;
    kode_mk: string;
    nama_mk: string;
    sks: number;
    semester: number;
    jurusan: string;
    hari: string;
    jam_mulai: string;
    jam_selesai: string;
    ruangan: string; // Added ruangan
    lecturer?: {
        name: string;
    };
}

defineProps<{
    courses: Course[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Mata Kuliah Saya',
        href: '/student/my-courses',
    },
];
</script>

<template>
    <Head title="Mata Kuliah Saya" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Mata Kuliah Saya</h1>
                    <p class="text-sm text-muted-foreground">
                        Daftar mata kuliah yang Anda ambil semester ini.
                    </p>
                </div>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Jadwal Kuliah</CardTitle>
                    <CardDescription>Jadwal perkuliahan untuk mata kuliah yang terdaftar.</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Kode MK</TableHead>
                                    <TableHead>Mata Kuliah</TableHead>
                                    <TableHead class="text-center">SKS</TableHead>
                                    <TableHead>Dosen</TableHead>
                                    <TableHead>Hari / Jam</TableHead>
                                    <TableHead>Ruangan</TableHead>
                                    <TableHead>Status</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="course in courses" :key="course.id">
                                    <TableCell class="font-medium">{{ course.kode_mk }}</TableCell>
                                    <TableCell>
                                        {{ course.nama_mk }}
                                        <div class="text-xs text-muted-foreground">Sem. {{ course.semester }}</div>
                                    </TableCell>
                                    <TableCell class="text-center">{{ course.sks }}</TableCell>
                                    <TableCell>{{ course.lecturer?.name || '-' }}</TableCell>
                                    <TableCell>
                                        <div class="font-medium">{{ course.hari }}</div>
                                        <div class="text-xs text-muted-foreground">
                                            {{ course.jam_mulai }} - {{ course.jam_selesai }}
                                        </div>
                                    </TableCell>
                                    <TableCell>{{ course.ruangan }}</TableCell> 
                                    <TableCell>
                                        <Badge variant="outline" class="bg-emerald-50 text-emerald-700 border-emerald-200">
                                            Aktif
                                        </Badge>
                                    </TableCell>
                                </TableRow>

                                <TableRow v-if="courses.length === 0">
                                    <TableCell colspan="7" class="text-center h-24 text-muted-foreground">
                                        Belum ada mata kuliah yang diambil.
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
