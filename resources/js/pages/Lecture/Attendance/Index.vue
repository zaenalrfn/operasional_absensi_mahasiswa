<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardContent, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { 
    DropdownMenu, 
    DropdownMenuContent, 
    DropdownMenuItem, 
    DropdownMenuTrigger,
    DropdownMenuSeparator,
    DropdownMenuLabel
} from '@/components/ui/dropdown-menu';
import { BookOpen, ChevronDown, Check } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import { ref, computed } from 'vue';

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
        jurusan: string;
    }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Manajemen Absensi',
        href: '/lecture/attendance',
    },
];

const selectedCourseId = ref<number | ''>('');

const selectedCourseName = computed(() => {
    if (!selectedCourseId.value) {
        return 'Tampilkan Semua Mata Kuliah';
    }
    const course = props.courses.find(c => c.id === selectedCourseId.value);
    return course ? `${course.nama_mk} - Kelas ${course.kelas}` : 'Tampilkan Semua Mata Kuliah';
});

const filteredCourses = computed(() => {
    if (!selectedCourseId.value) {
        return props.courses;
    }
    return props.courses.filter(course => course.id === selectedCourseId.value);
});

const selectCourse = (courseId: number | '') => {
    selectedCourseId.value = courseId;
};
</script>

<template>
    <Head title="Manajemen Absensi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Pilih Mata Kuliah</h2>

            <!-- Simplified Filter -->
            <div class="bg-white p-6 rounded-lg shadow-sm mb-8 dark:bg-gray-800">
                <div class="max-w-xl">
                    <Label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Pilih Mata Kuliah
                    </Label>
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button 
                                variant="outline" 
                                class="w-full justify-between text-left font-normal"
                            >
                                <span class="flex items-center gap-2">
                                    <BookOpen class="h-4 w-4" />
                                    {{ selectedCourseName }}
                                </span>
                                <ChevronDown class="h-4 w-4 opacity-50" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent class="w-[500px] max-h-[400px] overflow-y-auto">
                            <DropdownMenuLabel>Daftar Mata Kuliah</DropdownMenuLabel>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem 
                                @click="selectCourse('')"
                                class="cursor-pointer"
                            >
                                <Check 
                                    :class="[
                                        'mr-2 h-4 w-4',
                                        selectedCourseId === '' ? 'opacity-100' : 'opacity-0'
                                    ]" 
                                />
                                Tampilkan Semua Mata Kuliah
                            </DropdownMenuItem>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem 
                                v-for="course in courses" 
                                :key="course.id"
                                @click="selectCourse(course.id)"
                                class="cursor-pointer"
                            >
                                <Check 
                                    :class="[
                                        'mr-2 h-4 w-4',
                                        selectedCourseId === course.id ? 'opacity-100' : 'opacity-0'
                                    ]" 
                                />
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ course.nama_mk }}</span>
                                    <span class="text-xs text-muted-foreground">
                                        Kelas {{ course.kelas }} • Semester {{ course.semester }} • {{ course.jurusan }}
                                    </span>
                                </div>
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Pilih mata kuliah dari daftar di atas untuk memfilter tampilan.
                    </p>
                </div>
            </div>

            <!-- Course Grid -->
            <div>
                <div v-if="filteredCourses.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Card v-for="course in filteredCourses" :key="course.id" class="hover:shadow-lg transition-shadow border-t-4 border-t-blue-500">
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                             <div class="space-y-1">
                                <CardTitle class="text-base font-bold text-gray-800 dark:text-white">
                                    {{ course.nama_mk }}
                                </CardTitle>
                                <p class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full inline-block">
                                    {{ course.kode_mk }}
                                </p>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-2 mt-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Kelas:</span>
                                    <span class="font-medium">{{ course.kelas }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Semester:</span>
                                    <span class="font-medium">{{ course.semester }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Jurusan:</span>
                                    <span class="font-medium text-right truncate w-32 ml-2">{{ course.jurusan }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">SKS:</span>
                                    <span class="font-medium">{{ course.sks }}</span>
                                </div>
                            </div>
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

        </div>
    </AppLayout>
</template>
