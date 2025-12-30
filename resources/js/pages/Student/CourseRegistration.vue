<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardContent, CardDescription, CardFooter } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import type { BreadcrumbItem } from '@/types';
import { ref } from 'vue';
import { route } from 'ziggy-js';

// The props structure changes because Laravel's groupBy returns an object/map keyed by semester
const props = defineProps<{
    courses: Record<string, Array<{
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
        is_registered: boolean;
    }>>;
    jurusans: string[];
    userMajor?: string;
    filters: {
        jurusan?: string;
        angkatan?: string;
        semester?: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Course Registration',
        href: '/student/course-registration',
    },
];

const selectedSemester = ref(props.filters.semester || '1');
const selectedJurusan = ref(props.userMajor || props.filters.jurusan || '');
const selectedAngkatan = ref(props.filters.angkatan || '');

const years = [2020, 2021, 2022, 2023, 2024, 2025];

const applyFilters = () => {
    router.get(route('student.course-registration'), {
        jurusan: selectedJurusan.value,
        angkatan: selectedAngkatan.value,
        semester: selectedSemester.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
             // 
        }
    });
};

const register = (courseId: number) => {
    if (confirm('Apakah Anda yakin ingin mengambil mata kuliah ini?')) {
        router.post(route('student.course-registration.store'), {
            course_id: courseId,
        });
    }
};

const unregister = (courseId: number) => {
     if (confirm('Apakah Anda yakin ingin membatalkan mata kuliah ini?')) {
        router.delete(route('student.course-registration.destroy', courseId));
    }
}
</script>

<template>
    <Head title="Course Registration" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Pendaftaran Mata Kuliah (KRS)</h2>

            <!-- Filters -->
            <div class="bg-white p-6 rounded-lg shadow-sm mb-8 dark:bg-gray-800">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                    <div>
                        <Label for="angkatan-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Angkatan</Label>
                         <select 
                            id="angkatan-select" 
                            v-model="selectedAngkatan" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                            <option value="">Semua Angkatan</option>
                            <option v-for="year in years" :key="year" :value="year">
                                Angkatan {{ year }}
                            </option>
                        </select>
                    </div>
                    <div>
                         <Label for="jurusan-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Program Jurusan</Label>
                         <select 
                            id="jurusan-select" 
                            v-model="selectedJurusan" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                            <option value="">Semua Jurusan</option>
                            <option v-for="jur in jurusans" :key="jur" :value="jur">
                                {{ jur }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <Label for="semester-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Semester</Label>
                        <select 
                            id="semester-select" 
                            v-model="selectedSemester" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                            <option v-for="sem in 8" :key="sem" :value="sem">
                                Semester {{ sem }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <Button @click="applyFilters" class="w-full bg-blue-600 hover:bg-blue-700">
                            Tampilkan Mata Kuliah
                        </Button>
                    </div>
                </div>
            </div>

            <div class="space-y-10">
                <div>
                     <div class="flex justify-between items-center mb-4 border-b pb-2">
                         <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300">
                            Mata Kuliah Semester {{ selectedSemester }}
                        </h3>
                     </div>

                    <div v-if="courses[selectedSemester] && courses[selectedSemester].length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <Card v-for="course in courses[selectedSemester]" :key="course.id" class="flex flex-col h-full">
                            <CardHeader>
                                <CardTitle>{{ course.nama_mk }}</CardTitle>
                                <CardDescription>{{ course.kode_mk }}</CardDescription>
                            </CardHeader>
                            <CardContent class="flex-grow">
                                <div class="text-sm space-y-2">
                                    <p><strong>Dosen:</strong> {{ course.lecturer?.name || '-' }}</p>
                                    <p><strong>SKS:</strong> {{ course.sks }}</p>
                                    <p><strong>Kelas:</strong> {{ course.kelas }}</p>
                                    <p><strong>Jadwal:</strong> {{ course.hari }}, {{ course.jam_mulai }} - {{ course.jam_selesai }}</p>
                                </div>
                            </CardContent>
                            <CardFooter>
                                <Button 
                                    v-if="!course.is_registered" 
                                    @click="register(course.id)" 
                                    class="w-full bg-blue-600 hover:bg-blue-700"
                                >
                                    Ambil Mata Kuliah
                                </Button>
                                <Button 
                                    v-else 
                                    variant="destructive"
                                    @click="unregister(course.id)"
                                    class="w-full"
                                >
                                    Batalkan
                                </Button>
                            </CardFooter>
                        </Card>
                    </div>
                    <div v-else class="flex flex-col items-center justify-center py-10 bg-gray-50 rounded-lg dark:bg-gray-900 border border-dashed border-gray-300 dark:border-gray-700">
                        <p class="text-gray-500 text-lg dark:text-gray-400">Belum ada mata kuliah yang tersedia untuk Semester {{ selectedSemester }}.</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
