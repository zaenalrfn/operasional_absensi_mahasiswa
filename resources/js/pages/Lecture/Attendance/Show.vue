<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'; // Assuming we have Tabs component or will use native div
import type { BreadcrumbItem } from '@/types';
import { ref, computed } from 'vue';

const props = defineProps<{
    course: any;
    students: Array<any>;
    attendances: Record<string, Array<any>>; // Keyed by date
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

// --- Logic for Tabs ---
const activeTab = ref('recap'); // 'recap' or 'input'

// --- Logic for Input/Edit ---
const selectedDate = ref(new Date().toISOString().split('T')[0]);
const inputForm = useForm({
    date: selectedDate.value,
    attendances: props.students.map(s => ({
        user_id: s.id,
        status: 'hadir', // default
    })),
});

// Watch selected date to pre-fill existing data if any
const onDateChange = () => {
    inputForm.date = selectedDate.value;
    const existing = props.attendances[selectedDate.value];
    
    if (existing) {
        inputForm.attendances = props.students.map(s => {
            const record = existing.find((r: any) => r.user_id === s.id);
            return {
                user_id: s.id,
                status: record ? record.status : 'hadir',
            };
        });
    } else {
        // Reset to default if no data for this date
        inputForm.attendances = props.students.map(s => ({
            user_id: s.id,
            status: 'hadir',
        }));
    }
};

const submitAttendance = () => {
    inputForm.post(route('lecture.attendance.store', props.course.id), {
        onSuccess: () => {
             // Maybe show specific success message?
        }
    });
};

// --- Logic for Recap Matrix ---
// Get unique sorted dates
const allDates = computed(() => Object.keys(props.attendances).sort().reverse());

// Helper to get status for a student on a specific date
const getStatus = (studentId: number, date: string) => {
    const records = props.attendances[date];
    if (!records) return '-';
    const record = records.find((r: any) => r.user_id === studentId);
    return record ? record.status.toUpperCase() : '-';
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'HADIR': return 'text-green-600 font-bold';
        case 'IZIN': return 'text-blue-600 font-bold';
        case 'SAKIT': return 'text-yellow-600 font-bold';
        case 'ALPHA': return 'text-red-600 font-bold';
        default: return 'text-gray-400';
    }
};
</script>

<template>
    <Head title="Detail Absensi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ course.nama_mk }}</h2>
                    <p class="text-gray-500">{{ course.kode_mk }} - Semester {{ course.semester }}</p>
                </div>
            </div>

            <!-- Tab Navigation (Simple Buttons for now if UI components not fully ready, or use shadcn Tabs if available) -->
            <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-gray-400">
                    <li class="mr-2">
                        <button 
                            @click="activeTab = 'recap'"
                            :class="activeTab === 'recap' ? 'border-blue-600 text-blue-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300'"
                            class="inline-block p-4 border-b-2 rounded-t-lg"
                        >
                            Rekapitulasi
                        </button>
                    </li>
                    <li class="mr-2">
                        <button 
                            @click="activeTab = 'input'"
                            :class="activeTab === 'input' ? 'border-blue-600 text-blue-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300'"
                            class="inline-block p-4 border-b-2 rounded-t-lg"
                        >
                            Input / Edit Absensi
                        </button>
                    </li>
                </ul>
            </div>

            <!-- RECAP VIEW -->
            <div v-if="activeTab === 'recap'">
                <Card>
                    <CardContent class="overflow-x-auto p-0">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3 sticky left-0 bg-gray-50 dark:bg-gray-700 z-10 w-48">
                                        Nama Mahasiswa
                                    </th>
                                    <!-- Dynamic Date Header -->
                                    <th v-for="date in allDates" :key="date" scope="col" class="px-6 py-3 whitespace-nowrap text-center">
                                        {{ date }}
                                    </th>
                                    <th v-if="allDates.length === 0" class="px-6 py-3 text-center italic font-normal text-gray-400">
                                        Belum ada history absen
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="student in students" :key="student.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white sticky left-0 bg-white dark:bg-gray-800 z-10">
                                        {{ student.name }}
                                    </td>
                                    <td v-for="date in allDates" :key="date" class="px-6 py-4 text-center">
                                        <span :class="getStatusColor(getStatus(student.id, date))">
                                            {{ getStatus(student.id, date) }}
                                        </span>
                                    </td>
                                    <td v-if="allDates.length === 0" class="px-6 py-4"></td>
                                </tr>
                            </tbody>
                        </table>
                    </CardContent>
                </Card>
            </div>

            <!-- INPUT VIEW -->
            <div v-if="activeTab === 'input'">
                <div class="mb-6 max-w-sm">
                    <Label for="attendance-date">Tanggal Absensi</Label>
                    <Input 
                        id="attendance-date" 
                        type="date" 
                        v-model="selectedDate" 
                        @change="onDateChange" 
                        class="mt-1"
                    />
                </div>

                <div class="bg-white rounded-lg shadow dark:bg-gray-800 p-6">
                    <form @submit.prevent="submitAttendance">
                        <div class="grid gap-6 mb-6">
                            <div v-for="(item, index) in inputForm.attendances" :key="item.user_id" class="flex items-center justify-between border-b pb-4 last:border-0 last:pb-0">
                                <span class="font-medium text-gray-900 dark:text-white flex-1">
                                    {{ students.find(s => s.id === item.user_id)?.name }}
                                </span>
                                <div class="flex gap-4">
                                     <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" :name="`status-${item.user_id}`" v-model="item.status" value="hadir" class="hidden peer">
                                        <span class="px-3 py-1 rounded-md text-sm border border-gray-200 peer-checked:bg-green-100 peer-checked:text-green-700 peer-checked:border-green-200 hover:bg-gray-50 transition-colors">Hadir</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" :name="`status-${item.user_id}`" v-model="item.status" value="izin" class="hidden peer">
                                        <span class="px-3 py-1 rounded-md text-sm border border-gray-200 peer-checked:bg-blue-100 peer-checked:text-blue-700 peer-checked:border-blue-200 hover:bg-gray-50 transition-colors">Izin</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" :name="`status-${item.user_id}`" v-model="item.status" value="sakit" class="hidden peer">
                                        <span class="px-3 py-1 rounded-md text-sm border border-gray-200 peer-checked:bg-yellow-100 peer-checked:text-yellow-700 peer-checked:border-yellow-200 hover:bg-gray-50 transition-colors">Sakit</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" :name="`status-${item.user_id}`" v-model="item.status" value="alpha" class="hidden peer">
                                        <span class="px-3 py-1 rounded-md text-sm border border-gray-200 peer-checked:bg-red-100 peer-checked:text-red-700 peer-checked:border-red-200 hover:bg-gray-50 transition-colors">Alpha</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                         <div class="flex justify-end mt-4">
                            <Button :disabled="inputForm.processing">
                                Simpan Absensi
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
