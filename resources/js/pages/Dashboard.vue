<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { BookOpen, Calendar, Clock, CheckCircle, XCircle, AlertCircle } from 'lucide-vue-next';
import VueApexCharts from 'vue3-apexcharts';
import { computed } from 'vue';

interface Props {
    adminStats?: {
        totalStudents: number;
        totalCourses: number;
        totalLecturers: number;
        totalAttendances: number;
        overallAttendanceRate: number;
        attendanceByStatus: {
            hadir: number;
            sakit: number;
            izin: number;
            alpha: number;
        };
        studentsByDepartment: Array<{
            name: string;
            total: number;
        }>;
        topCourses: Array<{
            name: string;
            students: number;
        }>;
        attendanceTrend: Array<{
            date: string;
            count: number;
        }>;
        recentActivities: Array<{
            student: string;
            course: string;
            status: string;
            date: string;
            time: string;
        }>;
    };
    studentStats?: {
        coursesTaken: number;
        attendanceOverview: {
            hadir: number;
            sakit: number;
            izin: number;
            alpha: number;
        };
        coursePerformance: Array<{
            name: string;
            rate: number;
            total_meetings: number;
        }>;
    };
    lecturerStats?: {
        totalCourses: number;
        totalStudents: number;
        totalAttendances: number;
        averageAttendanceRate: number;
        attendanceOverview: {
            hadir: number;
            sakit: number;
            izin: number;
            alpha: number;
        };
        coursePerformance: Array<{
            name: string;
            kelas: string;
            students: number;
            attendanceRate: number;
            totalMeetings: number;
        }>;
        recentActivity: Array<{
            date: string;
            count: number;
        }>;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// --- Charts Configuration ---

// 1. Overall Attendance (Donut)
const attendanceSeries = computed(() => {
    if (!props.studentStats?.attendanceOverview) return [0, 0, 0, 0];
    const { hadir, sakit, izin, alpha } = props.studentStats.attendanceOverview;
    return [hadir, izin, sakit, alpha];
});

const attendanceChartOptions = {
    chart: { type: 'donut' as const },
    labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
    colors: ['#22c55e', '#3b82f6', '#eab308', '#ef4444'], // Green, Blue, Yellow, Red
    legend: { position: 'bottom' as const },
    dataLabels: { enabled: false },
    plotOptions: {
        pie: {
            donut: {
                size: '70%',
                labels: {
                    show: true,
                    total: {
                        show: true,
                        label: 'Total',
                        formatter: function (w: any) {
                            return w.globals.seriesTotals.reduce((a: any, b: any) => a + b, 0);
                        }
                    }
                }
            }
        }
    }
};

// 2. Course Performance (Bar)
const coursePerformanceSeries = computed(() => [{
    name: 'Kehadiran (%)',
    data: props.studentStats?.coursePerformance.map(c => c.rate) || []
}]);

const coursePerformanceOptions = computed(() => ({
    chart: { type: 'bar' as const, toolbar: { show: false } },
    plotOptions: {
        bar: { borderRadius: 4, horizontal: true }
    },
    colors: ['#3b82f6'],
    xaxis: {
        categories: props.studentStats?.coursePerformance.map(c => c.name) || [],
        max: 100
    },
    dataLabels: { enabled: true, formatter: (val: number) => val + "%" },
    grid: { show: false }
}));

const overallAttendanceRate = computed(() => {
    if (!props.studentStats?.attendanceOverview) return 0;
    const { hadir, sakit, izin, alpha } = props.studentStats.attendanceOverview;
    const total = hadir + sakit + izin + alpha;
    return total > 0 ? Math.round((hadir / total) * 100) : 0;
});

// --- Lecturer Charts Configuration ---

// Lecturer Attendance Overview (Donut)
const lecturerAttendanceSeries = computed(() => {
    if (!props.lecturerStats?.attendanceOverview) return [0, 0, 0, 0];
    const { hadir, sakit, izin, alpha } = props.lecturerStats.attendanceOverview;
    return [hadir, izin, sakit, alpha];
});

const lecturerAttendanceChartOptions = {
    chart: { type: 'donut' as const },
    labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
    colors: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
    legend: { position: 'bottom' as const },
    dataLabels: { enabled: false },
    plotOptions: {
        pie: {
            donut: {
                size: '70%',
                labels: {
                    show: true,
                    total: {
                        show: true,
                        label: 'Total Absensi',
                        formatter: function (w: any) {
                            return w.globals.seriesTotals.reduce((a: any, b: any) => a + b, 0);
                        }
                    }
                }
            }
        }
    }
};

// Lecturer Course Performance (Bar)
const lecturerCoursePerformanceSeries = computed(() => [{
    name: 'Tingkat Kehadiran (%)',
    data: props.lecturerStats?.coursePerformance.map(c => c.attendanceRate) || []
}]);

const lecturerCoursePerformanceOptions = computed(() => ({
    chart: { type: 'bar' as const, toolbar: { show: false } },
    plotOptions: {
        bar: { borderRadius: 6, horizontal: true, distributed: false }
    },
    colors: ['#8b5cf6'],
    xaxis: {
        categories: props.lecturerStats?.coursePerformance.map(c => `${c.name} (${c.kelas})`) || [],
        max: 100
    },
    dataLabels: { 
        enabled: true, 
        formatter: (val: number) => val + "%",
        style: { colors: ['#fff'] }
    },
    grid: { show: true, borderColor: '#f3f4f6' },
    tooltip: {
        y: {
            formatter: function(val: number, opts: any) {
                const index = opts.dataPointIndex;
                const course = props.lecturerStats?.coursePerformance[index];
                return `${val}% (${course?.students} mahasiswa)`;
            }
        }
    }
}));

// Recent Activity (Line Chart)
const recentActivitySeries = computed(() => [{
    name: 'Jumlah Absensi',
    data: props.lecturerStats?.recentActivity.map(a => a.count) || []
}]);

const recentActivityOptions = computed(() => ({
    chart: { 
        type: 'area' as const, 
        toolbar: { show: false },
        sparkline: { enabled: false }
    },
    stroke: { curve: 'smooth' as const, width: 3 },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.7,
            opacityTo: 0.2,
        }
    },
    colors: ['#06b6d4'],
    xaxis: {
        categories: props.lecturerStats?.recentActivity.map(a => a.date) || [],
        labels: { style: { fontSize: '12px' } }
    },
    yaxis: {
        labels: { formatter: (val: number) => Math.round(val).toString() }
    },
    dataLabels: { enabled: false },
    grid: { borderColor: '#f3f4f6' },
    tooltip: {
        y: { formatter: (val: number) => `${val} absensi` }
    }
}));

// --- Admin Charts Configuration ---

// Admin Attendance Status (Donut)
const adminAttendanceSeries = computed(() => {
    if (!props.adminStats?.attendanceByStatus) return [0, 0, 0, 0];
    const { hadir, sakit, izin, alpha } = props.adminStats.attendanceByStatus;
    return [hadir, izin, sakit, alpha];
});

const adminAttendanceChartOptions = {
    chart: { type: 'donut' as const },
    labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
    colors: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
    legend: { position: 'bottom' as const },
    dataLabels: { enabled: false },
    plotOptions: {
        pie: {
            donut: {
                size: '65%',
                labels: {
                    show: true,
                    total: {
                        show: true,
                        label: 'Total',
                        formatter: function (w: any) {
                            return w.globals.seriesTotals.reduce((a: any, b: any) => a + b, 0);
                        }
                    }
                }
            }
        }
    }
};

// Students by Department (Pie)
const departmentSeries = computed(() => 
    props.adminStats?.studentsByDepartment.map(d => d.total) || []
);

const departmentChartOptions = computed(() => ({
    chart: { type: 'pie' as const },
    labels: props.adminStats?.studentsByDepartment.map(d => d.name) || [],
    colors: ['#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#3b82f6', '#6366f1'],
    legend: { position: 'bottom' as const },
    dataLabels: { enabled: true },
    responsive: [{
        breakpoint: 480,
        options: {
            chart: { width: 300 },
            legend: { position: 'bottom' as const }
        }
    }]
}));

// Top Courses (Bar)
const topCoursesSeries = computed(() => [{
    name: 'Jumlah Mahasiswa',
    data: props.adminStats?.topCourses.map(c => c.students) || []
}]);

const topCoursesOptions = computed(() => ({
    chart: { type: 'bar' as const, toolbar: { show: false } },
    plotOptions: {
        bar: { borderRadius: 8, horizontal: true, distributed: false }
    },
    colors: ['#f59e0b'],
    xaxis: {
        categories: props.adminStats?.topCourses.map(c => c.name) || []
    },
    dataLabels: { 
        enabled: true,
        style: { colors: ['#fff'] }
    },
    grid: { show: true, borderColor: '#f3f4f6' }
}));

// Attendance Trend (Line/Area)
const adminTrendSeries = computed(() => [{
    name: 'Jumlah Absensi',
    data: props.adminStats?.attendanceTrend.map(a => a.count) || []
}]);

const adminTrendOptions = computed(() => ({
    chart: { 
        type: 'area' as const, 
        toolbar: { show: false },
        sparkline: { enabled: false }
    },
    stroke: { curve: 'smooth' as const, width: 3 },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.7,
            opacityTo: 0.2,
        }
    },
    colors: ['#8b5cf6'],
    xaxis: {
        categories: props.adminStats?.attendanceTrend.map(a => a.date) || [],
        labels: { style: { fontSize: '12px' } }
    },
    yaxis: {
        labels: { formatter: (val: number) => Math.round(val).toString() }
    },
    dataLabels: { enabled: false },
    grid: { borderColor: '#f3f4f6' },
    tooltip: {
        y: { formatter: (val: number) => `${val} absensi` }
    }
}));


</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6">
            
            <!-- Admin Dashboard -->
            <div v-if="adminStats" class="space-y-6">
                <!-- Welcome Header -->
                <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-xl p-6 text-white shadow-xl">
                    <h2 class="text-3xl font-bold mb-2">Dashboard Admin</h2>
                    <p class="text-purple-100">Selamat datang! Berikut ringkasan sistem absensi mahasiswa</p>
                </div>

                <!-- Top Summary Cards -->
                <div class="grid gap-6 md:grid-cols-4">
                    <Card class="border-0 shadow-lg hover:shadow-xl transition-all bg-gradient-to-br from-blue-500 to-blue-600">
                        <CardContent class="p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-100 text-sm font-medium">Total Mahasiswa</p>
                                    <h3 class="text-4xl font-bold mt-2">{{ adminStats.totalStudents }}</h3>
                                </div>
                                <div class="p-3 bg-white/20 rounded-full">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="border-0 shadow-lg hover:shadow-xl transition-all bg-gradient-to-br from-purple-500 to-purple-600">
                        <CardContent class="p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-purple-100 text-sm font-medium">Total Mata Kuliah</p>
                                    <h3 class="text-4xl font-bold mt-2">{{ adminStats.totalCourses }}</h3>
                                </div>
                                <div class="p-3 bg-white/20 rounded-full">
                                    <BookOpen class="w-8 h-8" />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="border-0 shadow-lg hover:shadow-xl transition-all bg-gradient-to-br from-pink-500 to-pink-600">
                        <CardContent class="p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-pink-100 text-sm font-medium">Total Dosen</p>
                                    <h3 class="text-4xl font-bold mt-2">{{ adminStats.totalLecturers }}</h3>
                                </div>
                                <div class="p-3 bg-white/20 rounded-full">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="border-0 shadow-lg hover:shadow-xl transition-all bg-gradient-to-br from-green-500 to-emerald-600">
                        <CardContent class="p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-green-100 text-sm font-medium">Tingkat Kehadiran</p>
                                    <h3 class="text-4xl font-bold mt-2">{{ adminStats.overallAttendanceRate }}%</h3>
                                </div>
                                <div class="p-3 bg-white/20 rounded-full">
                                    <CheckCircle class="w-8 h-8" />
                                </div>
                            </div>
                            <div class="mt-3 bg-white/20 rounded-full h-2">
                                <div class="bg-white h-2 rounded-full transition-all" :style="{ width: adminStats.overallAttendanceRate + '%' }"></div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Charts Grid -->
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Attendance Status Chart -->
                    <Card class="border-0 shadow-md">
                        <CardHeader class="border-b bg-gradient-to-r from-gray-50 to-gray-100">
                            <CardTitle class="flex items-center gap-2">
                                <div class="p-2 bg-purple-100 rounded-lg">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                                    </svg>
                                </div>
                                Status Kehadiran Keseluruhan
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="pt-6">
                            <div class="h-[300px] flex items-center justify-center">
                                <VueApexCharts type="donut" height="300" :options="adminAttendanceChartOptions" :series="adminAttendanceSeries" />
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Department Distribution Chart -->
                    <Card class="border-0 shadow-md">
                        <CardHeader class="border-b bg-gradient-to-r from-gray-50 to-gray-100">
                            <CardTitle class="flex items-center gap-2">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                Distribusi Mahasiswa per Jurusan
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="pt-6">
                            <div class="h-[300px] flex items-center justify-center">
                                <VueApexCharts type="pie" height="300" :options="departmentChartOptions" :series="departmentSeries" />
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Trend and Top Courses -->
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Attendance Trend -->
                    <Card class="border-0 shadow-md">
                        <CardHeader class="border-b bg-gradient-to-r from-gray-50 to-gray-100">
                            <CardTitle class="flex items-center gap-2">
                                <div class="p-2 bg-purple-100 rounded-lg">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                                    </svg>
                                </div>
                                Tren Absensi 7 Hari Terakhir
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="pt-6">
                            <div class="h-[300px]">
                                <VueApexCharts type="area" height="300" :options="adminTrendOptions" :series="adminTrendSeries" />
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Top Courses -->
                    <Card class="border-0 shadow-md">
                        <CardHeader class="border-b bg-gradient-to-r from-gray-50 to-gray-100">
                            <CardTitle class="flex items-center gap-2">
                                <div class="p-2 bg-orange-100 rounded-lg">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                Top 5 Mata Kuliah Terpopuler
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="pt-6">
                            <div class="h-[300px]">
                                <VueApexCharts type="bar" height="300" :options="topCoursesOptions" :series="topCoursesSeries" />
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Recent Activities -->
                <Card class="border-0 shadow-md">
                    <CardHeader class="border-b bg-gradient-to-r from-gray-50 to-gray-100">
                        <CardTitle class="flex items-center gap-2">
                            <div class="p-2 bg-cyan-100 rounded-lg">
                                <Clock class="w-5 h-5 text-cyan-600" />
                            </div>
                            Aktivitas Terbaru
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="pt-6">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mahasiswa</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Kuliah</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="(activity, index) in adminStats.recentActivities" :key="index" class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ activity.student }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ activity.course }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span :class="{
                                                'px-2 py-1 rounded-full text-xs font-semibold': true,
                                                'bg-green-100 text-green-800': activity.status === 'hadir',
                                                'bg-blue-100 text-blue-800': activity.status === 'izin',
                                                'bg-yellow-100 text-yellow-800': activity.status === 'sakit',
                                                'bg-red-100 text-red-800': activity.status === 'alpha'
                                            }">
                                                {{ activity.status.charAt(0).toUpperCase() + activity.status.slice(1) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ activity.time }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            </div>


            <!-- Lecturer Dashboard -->
            <div v-if="lecturerStats" class="space-y-6">
                <!-- Welcome Header -->
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-xl p-6 text-white shadow-lg">
                    <h2 class="text-2xl font-bold mb-2">Dashboard Dosen</h2>
                    <p class="text-purple-100">Selamat datang! Berikut ringkasan aktivitas pengajaran Anda</p>
                </div>

                <!-- Top Summary Cards -->
                <div class="grid gap-6 md:grid-cols-4">
                    <Card class="border-0 shadow-md hover:shadow-lg transition-shadow">
                        <CardContent class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Total Mata Kuliah</p>
                                    <h3 class="text-3xl font-bold mt-2 text-purple-600">{{ lecturerStats.totalCourses }}</h3>
                                </div>
                                <div class="p-3 bg-purple-100 rounded-full">
                                    <BookOpen class="w-6 h-6 text-purple-600" />
                                </div>
                            </div>
                            <p class="text-xs text-gray-400 mt-3">Mata kuliah yang diampu</p>
                        </CardContent>
                    </Card>

                    <Card class="border-0 shadow-md hover:shadow-lg transition-shadow">
                        <CardContent class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Total Mahasiswa</p>
                                    <h3 class="text-3xl font-bold mt-2 text-blue-600">{{ lecturerStats.totalStudents }}</h3>
                                </div>
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                            </div>
                            <p class="text-xs text-gray-400 mt-3">Mahasiswa aktif</p>
                        </CardContent>
                    </Card>

                    <Card class="border-0 shadow-md hover:shadow-lg transition-shadow">
                        <CardContent class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Total Absensi</p>
                                    <h3 class="text-3xl font-bold mt-2 text-cyan-600">{{ lecturerStats.totalAttendances }}</h3>
                                </div>
                                <div class="p-3 bg-cyan-100 rounded-full">
                                    <CheckCircle class="w-6 h-6 text-cyan-600" />
                                </div>
                            </div>
                            <p class="text-xs text-gray-400 mt-3">Record kehadiran</p>
                        </CardContent>
                    </Card>

                    <Card class="border-0 shadow-md hover:shadow-lg transition-shadow bg-gradient-to-br from-green-50 to-emerald-50">
                        <CardContent class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Rata-rata Kehadiran</p>
                                    <h3 class="text-3xl font-bold mt-2 text-green-600">{{ lecturerStats.averageAttendanceRate }}%</h3>
                                </div>
                                <div class="p-3 bg-green-200 rounded-full">
                                    <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center">
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full transition-all" :style="{ width: lecturerStats.averageAttendanceRate + '%' }"></div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Charts Section -->
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Attendance Overview Chart -->
                    <Card class="border-0 shadow-md">
                        <CardHeader class="border-b bg-gray-50">
                            <CardTitle class="flex items-center gap-2">
                                <div class="p-2 bg-purple-100 rounded-lg">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                                    </svg>
                                </div>
                                Ringkasan Kehadiran Mahasiswa
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="pt-6">
                            <div class="h-[320px] flex items-center justify-center">
                                <VueApexCharts type="donut" height="320" :options="lecturerAttendanceChartOptions" :series="lecturerAttendanceSeries" />
                            </div>
                            <div class="mt-4 grid grid-cols-4 gap-2 text-center">
                                <div class="p-2 bg-green-50 rounded-lg">
                                    <p class="text-xs text-gray-600">Hadir</p>
                                    <p class="text-lg font-bold text-green-600">{{ lecturerStats.attendanceOverview.hadir }}</p>
                                </div>
                                <div class="p-2 bg-blue-50 rounded-lg">
                                    <p class="text-xs text-gray-600">Izin</p>
                                    <p class="text-lg font-bold text-blue-600">{{ lecturerStats.attendanceOverview.izin }}</p>
                                </div>
                                <div class="p-2 bg-yellow-50 rounded-lg">
                                    <p class="text-xs text-gray-600">Sakit</p>
                                    <p class="text-lg font-bold text-yellow-600">{{ lecturerStats.attendanceOverview.sakit }}</p>
                                </div>
                                <div class="p-2 bg-red-50 rounded-lg">
                                    <p class="text-xs text-gray-600">Alpha</p>
                                    <p class="text-lg font-bold text-red-600">{{ lecturerStats.attendanceOverview.alpha }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Recent Activity Chart -->
                    <Card class="border-0 shadow-md">
                        <CardHeader class="border-b bg-gray-50">
                            <CardTitle class="flex items-center gap-2">
                                <div class="p-2 bg-cyan-100 rounded-lg">
                                    <Clock class="w-5 h-5 text-cyan-600" />
                                </div>
                                Aktivitas 7 Hari Terakhir
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="pt-6">
                            <div class="h-[320px]">
                                <VueApexCharts type="area" height="320" :options="recentActivityOptions" :series="recentActivitySeries" />
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Course Performance Chart -->
                <Card class="border-0 shadow-md">
                    <CardHeader class="border-b bg-gray-50">
                        <CardTitle class="flex items-center gap-2">
                            <div class="p-2 bg-purple-100 rounded-lg">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            Tingkat Kehadiran per Mata Kuliah
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="pt-6">
                        <div class="h-[400px]">
                            <VueApexCharts type="bar" height="400" :options="lecturerCoursePerformanceOptions" :series="lecturerCoursePerformanceSeries" />
                        </div>
                    </CardContent>
                </Card>

                <!-- Quick Actions -->
                <div class="max-w-md mx-auto">
                    <Link href="/lecture/attendance" class="block">
                        <Card class="border-0 shadow-md hover:shadow-xl transition-all hover:scale-105 cursor-pointer bg-gradient-to-br from-blue-500 to-indigo-600">
                            <CardContent class="p-8 flex items-center gap-6">
                                <div class="p-4 bg-white/20 rounded-xl backdrop-blur-sm">
                                    <BookOpen class="w-8 h-8 text-white" />
                                </div>
                                <div class="text-white">
                                    <h4 class="text-xl font-bold mb-1">Kelola Absensi</h4>
                                    <p class="text-blue-100">Input & verifikasi kehadiran mahasiswa</p>
                                </div>
                            </CardContent>
                        </Card>
                    </Link>
                </div>
            </div>

            <!-- Student Dashboard -->
            <div v-if="studentStats" class="space-y-6">
                <!-- Welcome Header with Gradient -->
                <div class="bg-gradient-to-r from-cyan-500 via-blue-500 to-purple-600 rounded-2xl p-8 text-white shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-24 -mb-24"></div>
                    <div class="relative z-10">
                        <h2 class="text-3xl font-bold mb-2">Dashboard Mahasiswa</h2>
                        <p class="text-blue-100">Pantau perkembangan kehadiran dan performa akademik Anda</p>
                    </div>
                </div>

                <!-- Enhanced Summary Cards -->
                <div class="grid gap-6 md:grid-cols-4">
                    <!-- Mata Kuliah Card -->
                    <Card class="border-0 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 bg-gradient-to-br from-blue-500 to-blue-600 overflow-hidden">
                        <CardContent class="p-6 text-white relative">
                            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                                        <BookOpen class="w-6 h-6" />
                                    </div>
                                </div>
                                <p class="text-blue-100 text-sm font-medium mb-1">Mata Kuliah</p>
                                <h3 class="text-4xl font-bold">{{ studentStats.coursesTaken }}</h3>
                                <p class="text-blue-100 text-xs mt-2">Semester ini</p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Rata-rata Kehadiran Card -->
                    <Card class="border-0 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 bg-gradient-to-br from-green-500 to-emerald-600 overflow-hidden">
                        <CardContent class="p-6 text-white relative">
                            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                                        <CheckCircle class="w-6 h-6" />
                                    </div>
                                    <div v-if="overallAttendanceRate >= 80" class="px-3 py-1 bg-white/30 rounded-full text-xs font-bold backdrop-blur-sm">
                                        Excellent!
                                    </div>
                                </div>
                                <p class="text-green-100 text-sm font-medium mb-1">Kehadiran</p>
                                <h3 class="text-4xl font-bold">{{ overallAttendanceRate }}%</h3>
                                <div class="mt-3 bg-white/20 rounded-full h-2">
                                    <div class="bg-white h-2 rounded-full transition-all duration-500" :style="{ width: overallAttendanceRate + '%' }"></div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Total Hadir Card -->
                    <Card class="border-0 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 bg-gradient-to-br from-purple-500 to-pink-600 overflow-hidden">
                        <CardContent class="p-6 text-white relative">
                            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-purple-100 text-sm font-medium mb-1">Total Hadir</p>
                                <h3 class="text-4xl font-bold">{{ studentStats.attendanceOverview.hadir }}</h3>
                                <p class="text-purple-100 text-xs mt-2">Pertemuan</p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Quick Actions Card -->
                    <Card class="border-0 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 bg-gradient-to-br from-orange-500 to-red-600 overflow-hidden">
                        <CardContent class="p-6 text-white relative">
                            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
                            <div class="relative z-10">
                                <p class="text-orange-100 text-sm font-medium mb-3">Quick Actions</p>
                                <div class="space-y-2">
                                    <Link href="/student/attendance" class="flex items-center gap-2 p-2 bg-white/20 rounded-lg hover:bg-white/30 transition-colors backdrop-blur-sm">
                                        <Clock class="w-4 h-4" />
                                        <span class="text-sm font-semibold">Cek Absensi</span>
                                    </Link>
                                    <Link href="/student/my-courses" class="flex items-center gap-2 p-2 bg-white/20 rounded-lg hover:bg-white/30 transition-colors backdrop-blur-sm">
                                        <Calendar class="w-4 h-4" />
                                        <span class="text-sm font-semibold">Jadwal Kuliah</span>
                                    </Link>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Attendance Breakdown Mini Cards -->
                <div class="grid gap-4 md:grid-cols-4">
                    <Card class="border-l-4 border-l-green-500 shadow-md hover:shadow-lg transition-shadow">
                        <CardContent class="p-4 flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Hadir</p>
                                <h4 class="text-2xl font-bold text-green-600">{{ studentStats.attendanceOverview.hadir }}</h4>
                            </div>
                            <div class="p-3 bg-green-100 rounded-full">
                                <CheckCircle class="w-6 h-6 text-green-600" />
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="border-l-4 border-l-blue-500 shadow-md hover:shadow-lg transition-shadow">
                        <CardContent class="p-4 flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Izin</p>
                                <h4 class="text-2xl font-bold text-blue-600">{{ studentStats.attendanceOverview.izin }}</h4>
                            </div>
                            <div class="p-3 bg-blue-100 rounded-full">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="border-l-4 border-l-yellow-500 shadow-md hover:shadow-lg transition-shadow">
                        <CardContent class="p-4 flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Sakit</p>
                                <h4 class="text-2xl font-bold text-yellow-600">{{ studentStats.attendanceOverview.sakit }}</h4>
                            </div>
                            <div class="p-3 bg-yellow-100 rounded-full">
                                <AlertCircle class="w-6 h-6 text-yellow-600" />
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="border-l-4 border-l-red-500 shadow-md hover:shadow-lg transition-shadow">
                        <CardContent class="p-4 flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Alpha</p>
                                <h4 class="text-2xl font-bold text-red-600">{{ studentStats.attendanceOverview.alpha }}</h4>
                            </div>
                            <div class="p-3 bg-red-100 rounded-full">
                                <XCircle class="w-6 h-6 text-red-600" />
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Enhanced Charts Section -->
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Attendance Overview Chart -->
                    <Card class="border-0 shadow-xl">
                        <CardHeader class="border-b bg-gradient-to-r from-blue-50 to-purple-50">
                            <CardTitle class="flex items-center gap-3">
                                <div class="p-2 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                                    </svg>
                                </div>
                                <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent font-bold">
                                    Ringkasan Kehadiran
                                </span>
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="pt-6">
                            <div class="h-[320px] flex items-center justify-center">
                                <VueApexCharts type="donut" height="320" :options="attendanceChartOptions" :series="attendanceSeries" />
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Course Performance Chart -->
                    <Card class="border-0 shadow-xl">
                        <CardHeader class="border-b bg-gradient-to-r from-green-50 to-emerald-50">
                            <CardTitle class="flex items-center gap-3">
                                <div class="p-2 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                <span class="bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent font-bold">
                                    Performa per Mata Kuliah
                                </span>
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="pt-6">
                            <div class="h-[320px]">
                                <VueApexCharts type="bar" height="320" :options="coursePerformanceOptions" :series="coursePerformanceSeries" />
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>


        </div>
    </AppLayout>
</template>
