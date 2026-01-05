<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import type { BreadcrumbItem } from '@/types';
import { ref, computed, watch } from 'vue';
import { cn } from '@/lib/utils';
import { route } from 'ziggy-js';

const props = defineProps<{
    course: any;
    students: Array<any>;
    attendances: Record<string, Array<any>>; // Keyed by date YYYY-MM-DD
    meetingDates: string[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Manajemen Absensi',
        href: '/lecture/attendance',
    },
    {
        title: props.course.nama_mk,
        href: `/lecture/attendance/${props.course.id}`,
    },
];

// --- Tabs Logic ---
const activeTab = ref('recap'); // 'recap' | 'input'

// --- Input/Edit Logic ---
const selectedDate = ref(new Date().toISOString().split('T')[0]);

interface AttendanceItem {
    user_id: number;
    status: string;
}

const inputForm = useForm({
    date: selectedDate.value,
    attendances: [] as AttendanceItem[],
});

// Initialize form data based on selected date and existing data
const updateFormForDate = (date: string) => {
    inputForm.date = date;
    const existingRecords = props.attendances[date] || [];
    
    inputForm.attendances = props.students.map(student => {
        const record = existingRecords.find((r: any) => r.user_id === student.id);
        return {
            user_id: student.id,
            status: record ? record.status : 'alpha', // Default to alpha if no record found? Or maybe null? 
            // User requirement: "mahasiswa yang telat absen... di edit". 
            // Usually defaulting to 'Alpha' or 'Hadir' depends on policy. 
            // Let's default to 'alpha' (absent) so lecturer must explicitly mark them present if they didn't check in.
            // But if it's a new day, maybe default to empty or 'alpha'. 
            // Let's use 'alpha' as safe default for "not present yet".
        };
    });
};

// Watch for date changes
watch(selectedDate, (newDate) => {
    updateFormForDate(newDate);
}, { immediate: true });

// Also watch props in case they update (e.g. after save)
watch(() => props.attendances, () => {
    updateFormForDate(selectedDate.value);
}, { deep: true });

const submitAttendance = () => {
    inputForm.post(route('lecture.attendance.store', props.course.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Optional: nice toast notification could go here
        }
    });
};

// --- Recap Logic ---
const allDates = computed(() => Object.keys(props.attendances).sort().reverse());

const getStatus = (studentId: number, date: string) => {
    const records = props.attendances[date];
    if (!records) return '-';
    const record = records.find((r: any) => r.user_id === studentId);
    return record ? record.status : '-';
};



const getStatusLabel = (status: string) => {
    switch(status) {
        case 'hadir': return 'Hadir';
        case 'izin': return 'Izin';
        case 'sakit': return 'Sakit';
        case 'alpha': return 'Alpha';
        default: return '-';
    }
};

// Map Meeting Number (1..14) to Status
const getMeetingStatus = (studentId: number, meetingNumber: number) => {
    // 0-based index for array
    const dateIndex = meetingNumber - 1;
    const date = props.meetingDates[dateIndex];
    
    if (!date) return '-'; // Meeting hasn't happened yet or no record

    return getStatus(studentId, date);
};

const getMeetingStatusLabel = (studentId: number, meetingNumber: number) => {
    const status = getMeetingStatus(studentId, meetingNumber);
    if (status === '-') return '';
    // Return first letter capitalized as label in the small box, or full text if space allows. 
    // The previous design used full text 'Hadir', but in a 14-col grid, 'H' is better.
    // User image showed "H", "A" etc.
    return status.charAt(0).toUpperCase();
};



const getStatusClass = (status: string) => {
    switch (status) {
        case 'hadir': return 'text-green-600 font-bold bg-green-50 px-2 py-1 rounded';
        case 'izin': return 'text-blue-600 font-bold bg-blue-50 px-2 py-1 rounded';
        case 'sakit': return 'text-yellow-600 font-bold bg-yellow-50 px-2 py-1 rounded';
        case 'alpha': return 'text-red-600 font-bold bg-red-50 px-2 py-1 rounded';
        default: return 'text-gray-400';
    }
};
</script>

<template>
    <Head title="Detail Absensi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 w-full">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ course.nama_mk }}</h2>
                    <p class="text-gray-500">{{ course.kode_mk }} - Semester {{ course.semester }} - {{ course.kelas }}</p>
                </div>
            </div>

            <!-- Custom Tabs -->
            <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                <nav class="flex space-x-8" aria-label="Tabs">
                    <button 
                        @click="activeTab = 'recap'"
                        :class="[
                            activeTab === 'recap' 
                                ? 'border-blue-500 text-blue-600 dark:text-blue-400' 
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
                            'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors'
                        ]"
                    >
                        Rekapitulasi Absensi (14 Pertemuan)
                    </button>
                    <button 
                        @click="activeTab = 'input'"
                        :class="[
                            activeTab === 'input' 
                                ? 'border-blue-500 text-blue-600 dark:text-blue-400' 
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
                            'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors'
                        ]"
                    >
                        Input / Edit Absensi
                    </button>
                </nav>
            </div>

            <!-- RECAP TAB (14 Meetings) -->
            <div v-show="activeTab === 'recap'" class="animate-in fade-in slide-in-from-bottom-2 duration-300">
                <Card>
                    <CardContent class="p-0 overflow-x-auto">
                        <Table>
                            <TableHeader>
                                <TableRow class="bg-gray-50 dark:bg-gray-800 border-b">
                                    <TableHead class="w-[250px] sticky left-0 bg-gray-50 dark:bg-gray-800 z-10 font-bold text-gray-900 border-r dark:border-gray-700 dark:text-white" rowspan="2">
                                        Nama Mahasiswa
                                    </TableHead>
                                    <TableHead colspan="14" class="text-center font-bold border-b dark:border-gray-700">Pertemuan Ke-</TableHead>
                                </TableRow>
                                <TableRow class="bg-gray-50 dark:bg-gray-800 text-xs">
                                    <TableHead v-for="i in 14" :key="i" class="w-12 text-center border-r last:border-r-0 dark:border-gray-700">
                                        <div class="flex flex-col items-center">
                                            <span>{{ i }}</span>
                                            <!-- Optional: Date hint tooltip could go here -->
                                        </div>
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="student in students" :key="student.id" class="hover:bg-gray-50 dark:hover:bg-gray-900/50">
                                    <TableCell class="font-medium sticky left-0 bg-white dark:bg-gray-950 z-10 border-r dark:border-gray-800">
                                        {{ student.name }}
                                        <div class="text-xs text-gray-400 font-normal">{{ student.nim }}</div>
                                    </TableCell>
                                    <TableCell v-for="i in 14" :key="i" class="text-center p-0 border-r last:border-r-0 dark:border-gray-800 h-12 w-12">
                                        <!-- Logic: check if meetingDates[i-1] exists, if yes get status, else empty -->
                                        <div 
                                            class="flex items-center justify-center w-full h-full font-bold"
                                            :class="getStatusClass(getMeetingStatus(student.id, i))"
                                        >
                                           {{ getMeetingStatusLabel(student.id, i) }}
                                        </div>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>

            <!-- INPUT/EDIT TAB -->
            <div v-show="activeTab === 'input'" class="animate-in fade-in slide-in-from-bottom-2 duration-300">
                <Card>
                    <CardContent class="p-6">
                        <div class="flex flex-col md:flex-row gap-6 mb-8 items-end">
                            <div class="w-full md:w-auto">
                                <Label for="attendance-date" class="mb-2 block">Pilih Tanggal Absensi</Label>
                                <Input 
                                    id="attendance-date" 
                                    type="date" 
                                    v-model="selectedDate"
                                    class="w-full md:w-64"
                                />
                            </div>
                        </div>

                        <form @submit.prevent="submitAttendance">
                            <div class="rounded-md border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                                <Table>
                                    <TableHeader>
                                        <TableRow class="bg-gray-50 dark:bg-gray-800">
                                            <TableHead class="w-[300px]">Nama Mahasiswa</TableHead>
                                            <TableHead class="w-[200px]">Status Kehadiran</TableHead>
                                            <TableHead>Keterangan</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        <TableRow v-for="(item, index) in inputForm.attendances" :key="item.user_id">
                                            <TableCell class="font-medium relative">
                                                {{ students.find(s => s.id === item.user_id)?.name }}
                                                <div class="text-xs text-gray-500">
                                                    {{ students.find(s => s.id === item.user_id)?.nim }}
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                <select 
                                                    v-model="item.status"
                                                    class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                                >
                                                    <option value="hadir">Hadir</option>
                                                    <option value="izin">Izin</option>
                                                    <option value="sakit">Sakit</option>
                                                    <option value="alpha">Alpha</option>
                                                </select>
                                            </TableCell>
                                            <TableCell class="text-gray-400 text-sm italic">
                                                {{ item.status === 'alpha' ? 'Tanpa Keterangan' : (item.status === 'hadir' ? 'Hadir di kelas' : 'Perlu bukti pendukung') }}
                                            </TableCell>
                                        </TableRow>
                                    </TableBody>
                                </Table>
                            </div>

                            <div class="flex justify-end">
                                <Button type="submit" size="lg" :disabled="inputForm.processing">
                                    Simpan Perubahan Absensi
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
