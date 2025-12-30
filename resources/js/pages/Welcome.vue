<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { onMounted, ref } from 'vue';
import AOS from 'aos';
import 'aos/dist/aos.css';
import { useTransition, TransitionPresets, useIntersectionObserver } from '@vueuse/core';

defineProps<{
    canRegister?: boolean;
}>();

const statsSection = ref(null);
const duration = 1500;

// Base values
const statsData = [
    { label: 'Mahasiswa Aktif', value: 1000, suffix: '+' },
    { label: 'Dosen Pengajar', value: 50, suffix: '+' },
    { label: 'Uptime Server', value: 100, suffix: '%' },
    { label: 'Akses Sistem', value: 24, suffix: '/7' },
];

// Reactive sources for transition (initially 0)
const source1 = ref(0);
const source2 = ref(0);
const source3 = ref(0);
const source4 = ref(0);

// Transioted output values
const output1 = useTransition(source1, { duration, transition: TransitionPresets.easeOutExpo });
const output2 = useTransition(source2, { duration, transition: TransitionPresets.easeOutExpo });
const output3 = useTransition(source3, { duration, transition: TransitionPresets.easeOutExpo });
const output4 = useTransition(source4, { duration, transition: TransitionPresets.easeOutExpo });

// Trigger animation on scroll
useIntersectionObserver(
    statsSection,
    ([{ isIntersecting }]) => {
        if (isIntersecting) {
            source1.value = 1000;
            source2.value = 50;
            source3.value = 100;
            source4.value = 24;
        }
    },
    { threshold: 0.5 }
);

onMounted(() => {
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        mirror: false,
    });
});
</script>

<template>
    <Head title="Selamat Datang" />
    
    <div class="min-h-screen bg-slate-50 font-sans text-slate-900 overflow-x-hidden">
        <!-- Header / Navigation -->
        <header class="sticky top-0 z-50 w-full bg-white/80 backdrop-blur-md border-b border-slate-200 shadow-sm">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <!-- Logo -->
                    <div class="flex items-center gap-2">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 text-white shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.499 5.516 50.552 50.552 0 00-2.658.813m-15.482 0A50.553 50.553 0 0112 13.489a50.551 50.551 0 016.482-2.897m-15.482 0A50.553 50.553 0 0112 13.489a50.551 50.551 0 016.482-2.897" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold tracking-tight text-slate-900">PASSION</span>
                    </div>

                    <!-- Desktop Nav -->
                    <nav class="hidden md:flex gap-8">
                        <a href="#fitur" class="text-sm font-medium text-slate-600 hover:text-blue-600 transition">Fitur Utama</a>
                        <a href="#tentang" class="text-sm font-medium text-slate-600 hover:text-blue-600 transition">Tentang Kami</a>
                        <a href="#statistik" class="text-sm font-medium text-slate-600 hover:text-blue-600 transition">Statistik</a>
                    </nav>

                    <!-- Auth Buttons -->
                    <div class="flex items-center gap-4">
                        <template v-if="$page.props.auth.user">
                            <Link :href="route('dashboard')" class="text-sm font-semibold text-slate-700 hover:text-blue-600">
                                Dashboard
                            </Link>
                        </template>
                        <template v-else>
                            <Link :href="route('login')" class="text-sm font-semibold text-slate-700 hover:text-blue-600">
                                Masuk
                            </Link>
                            <Link 
                                v-if="canRegister" 
                                :href="route('student.register')" 
                                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-md transition-all hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            >
                                Daftar Mahasiswa
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </header>

        <!-- 1. Hero Section -->
        <section class="relative overflow-hidden pt-16 pb-24 lg:pt-32">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid gap-12 lg:grid-cols-2 lg:items-center">
                    <div class="max-w-2xl text-center lg:text-left" data-aos="fade-right">
                        <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 sm:text-6xl mb-6">
                            Kelola Absensi Kampus dengan <span class="text-blue-600">Mudah & Efisien</span>
                        </h1>
                        <p class="text-lg text-slate-600 mb-8 leading-relaxed">
                            <strong>Presensi Akademik Studi Scan Identitas Otentik NIM</strong>. Platform terintegrasi untuk memantau kehadiran mahasiswa, jadwal perkuliahan, dan operasional akademik secara real-time.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                            <Link :href="route('student.register')" class="inline-flex h-12 items-center justify-center rounded-lg bg-blue-600 px-8 text-base font-semibold text-white shadow-lg transition-all hover:bg-blue-700 hover:-translate-y-0.5">
                                Daftar Sekarang
                            </Link>
                            <a href="#fitur" class="inline-flex h-12 items-center justify-center rounded-lg border border-slate-200 bg-white px-8 text-base font-semibold text-slate-700 shadow-sm transition-all hover:bg-slate-50 hover:text-blue-600">
                                Pelajari Lebih Lanjut
                            </a>
                        </div>
                    </div>
                    <div class="relative lg:ml-auto" data-aos="fade-left">
                        <!-- Abstract decorative shapes -->
                        <div class="absolute -top-12 -left-12 h-64 w-64 rounded-full bg-blue-100 mix-blend-multiply blur-3xl opacity-70 animate-blob"></div>
                        <div class="absolute -bottom-12 -right-12 h-64 w-64 rounded-full bg-indigo-100 mix-blend-multiply blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
                        
                        <!-- Illustration Placeholder -->
                        <div class="relative overflow-hidden rounded-2xl bg-white shadow-2xl border border-slate-100">
                            <div class="bg-slate-50 p-4 border-b border-slate-100 flex items-center gap-2">
                                <div class="h-3 w-3 rounded-full bg-red-400"></div>
                                <div class="h-3 w-3 rounded-full bg-yellow-400"></div>
                                <div class="h-3 w-3 rounded-full bg-green-400"></div>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="h-8 w-1/3 bg-slate-200 rounded animate-pulse"></div>
                                <div class="space-y-2">
                                    <div class="h-4 w-full bg-slate-100 rounded"></div>
                                    <div class="h-4 w-5/6 bg-slate-100 rounded"></div>
                                    <div class="h-4 w-4/6 bg-slate-100 rounded"></div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 mt-6">
                                    <div class="h-24 rounded-lg bg-blue-50 border border-blue-100 p-4">
                                        <div class="h-8 w-8 bg-blue-200 rounded mb-2"></div>
                                        <div class="h-3 w-16 bg-blue-200 rounded"></div>
                                    </div>
                                    <div class="h-24 rounded-lg bg-indigo-50 border border-indigo-100 p-4">
                                        <div class="h-8 w-8 bg-indigo-200 rounded mb-2"></div>
                                        <div class="h-3 w-16 bg-indigo-200 rounded"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. Features Section -->
        <section id="fitur" class="py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                    <h2 class="text-blue-600 font-semibold uppercase tracking-wider text-sm mb-2">Fitur Unggulan</h2>
                    <h3 class="text-3xl font-bold text-slate-900 leading-tight">Solusi Lengkap untuk Manajemen Kampus</h3>
                    <p class="mt-4 text-slate-600">Sistem kami menyediakan berbagai alat canggih untuk mempermudah kegiatan operasional harian Anda.</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="group p-8 rounded-2xl bg-slate-50 border border-slate-100 transition-all hover:bg-white hover:shadow-xl hover:-translate-y-1" data-aos="fade-up" data-aos-delay="100">
                        <div class="h-12 w-12 rounded-lg bg-blue-600 text-white flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Absensi Real-time</h4>
                        <p class="text-slate-600 leading-relaxed">Mahasiswa dapat melakukan absen secara mandiri dan dosen dapat memantau kehadiran secara langsung.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="group p-8 rounded-2xl bg-slate-50 border border-slate-100 transition-all hover:bg-white hover:shadow-xl hover:-translate-y-1" data-aos="fade-up" data-aos-delay="200">
                        <div class="h-12 w-12 rounded-lg bg-indigo-600 text-white flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Jadwal Terintegrasi</h4>
                        <p class="text-slate-600 leading-relaxed">Pengaturan jadwal kuliah yang fleksibel dan terpusat, menghindari bentrok dan memudahkan akses informasi.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="group p-8 rounded-2xl bg-slate-50 border border-slate-100 transition-all hover:bg-white hover:shadow-xl hover:-translate-y-1" data-aos="fade-up" data-aos-delay="300">
                        <div class="h-12 w-12 rounded-lg bg-teal-600 text-white flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Validasi Lokasi</h4>
                        <p class="text-slate-600 leading-relaxed">Sistem memvalidasi lokasi mahasiswa saat melakukan absensi untuk memastikan kehadiran fisik yang akurat.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. About Section -->
        <section id="tentang" class="py-20 bg-slate-50 border-y border-slate-200">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center gap-12">
                    <div class="lg:w-1/2" data-aos="fade-right">
                        <div class="relative rounded-2xl overflow-hidden shadow-2xl">
                             <!-- Simple Image Placeholder -->
                             <div class="bg-gradient-to-br from-blue-500 to-indigo-700 h-96 w-full flex items-center justify-center text-white/20">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-48 h-48">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.499 5.516 50.552 50.552 0 00-2.658.813m-15.482 0A50.553 50.553 0 0112 13.489a50.551 50.551 0 016.482-2.897m-15.482 0A50.553 50.553 0 0112 13.489a50.551 50.551 0 016.482-2.897" />
                                  </svg>
                             </div>
                        </div>
                    </div>
                    <div class="lg:w-1/2" data-aos="fade-left">
                        <h2 class="text-3xl font-bold text-slate-900 mb-6">Mengapa Memilih Sistem Kami?</h2>
                        <div class="space-y-6">
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm">1</div>
                                <div>
                                    <h4 class="font-bold text-slate-900">Efisiensi Administrasi</h4>
                                    <p class="text-slate-600 text-sm mt-1">Mengurangi beban kerja manual administrasi kampus dengan otomatisasi pencatatan kehadiran.</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm">2</div>
                                <div>
                                    <h4 class="font-bold text-slate-900">Transparansi Data</h4>
                                    <p class="text-slate-600 text-sm mt-1">Mahasiswa dan dosen dapat melihat rekap kehadiran secara transparan kapan saja dan di mana saja.</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm">3</div>
                                <div>
                                    <h4 class="font-bold text-slate-900">Modern & User Friendly</h4>
                                    <p class="text-slate-600 text-sm mt-1">Antarmuka yang didesain modern dan mudah digunakan oleh seluruh civitas akademika.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. Stats Section -->
        <section id="statistik" class="py-20 bg-blue-600 text-white" ref="statsSection">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center" data-aos="zoom-in">
                    <div>
                        <div class="text-4xl lg:text-5xl font-extrabold mb-2">{{ Math.round(output1) }}+</div>
                        <div class="text-blue-100 font-medium">Mahasiswa Aktif</div>
                    </div>
                    <div>
                        <div class="text-4xl lg:text-5xl font-extrabold mb-2">{{ Math.round(output2) }}+</div>
                        <div class="text-blue-100 font-medium">Dosen Pengajar</div>
                    </div>
                    <div>
                        <div class="text-4xl lg:text-5xl font-extrabold mb-2">{{ Math.round(output3) }}%</div>
                        <div class="text-blue-100 font-medium">Uptime Server</div>
                    </div>
                    <div>
                        <div class="text-4xl lg:text-5xl font-extrabold mb-2">{{ Math.round(output4) }}/7</div>
                        <div class="text-blue-100 font-medium">Akses Sistem</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. CTA Section -->
        <section class="py-24 bg-white text-center">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-6">Siap untuk Transformasi Digital?</h2>
                <p class="text-lg text-slate-600 mb-10 max-w-2xl mx-auto">
                    Bergabunglah sekarang untuk menikmati kemudahan pengelolaan absensi dan operasional kampus.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link :href="route('student.register')" class="inline-flex h-12 items-center justify-center rounded-lg bg-blue-600 px-8 text-base font-semibold text-white shadow-lg transition-all hover:bg-blue-700 hover:scale-105">
                        Daftar Sebagai Mahasiswa
                    </Link>
                    <Link :href="route('login')" class="inline-flex h-12 items-center justify-center rounded-lg border border-slate-200 bg-white px-8 text-base font-semibold text-slate-700 shadow-sm transition-all hover:bg-slate-50 hover:text-blue-600">
                        Masuk Sistem
                    </Link>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-slate-900 py-12 text-slate-400 border-t border-slate-800">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-4 gap-8 mb-8">
                    <div class="col-span-1 md:col-span-2">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="h-8 w-8 rounded bg-blue-600 flex items-center justify-center text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.499 5.516 50.552 50.552 0 00-2.658.813m-15.482 0A50.553 50.553 0 0112 13.489a50.551 50.551 0 016.482-2.897m-15.482 0A50.553 50.553 0 0112 13.489a50.551 50.551 0 016.482-2.897" />
                                </svg>
                            </div>
                            <span class="text-xl font-bold text-white">PASSION</span>
                        </div>
                        <p class="text-sm leading-relaxed max-w-xs transition hover:text-white">
                            Sistem informasi manajemen absensi dan operasional kampus terdepan untuk efisiensi pendidikan.
                        </p>
                    </div>
                    <div>
                        <h5 class="text-white font-bold mb-4">Navigasi</h5>
                        <ul class="space-y-2 text-sm">
                            <li><Link :href="route('dashboard')" class="hover:text-blue-500 transition">Dashboard</Link></li>
                            <li><a href="#fitur" class="hover:text-blue-500 transition">Fitur</a></li>
                            <li><a href="#tentang" class="hover:text-blue-500 transition">Tentang</a></li>
                        </ul>
                    </div>
                    <div>
                        <h5 class="text-white font-bold mb-4">Bantuan</h5>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="hover:text-blue-500 transition">Pusat Bantuan</a></li>
                            <li><a href="#" class="hover:text-blue-500 transition">Kontak Support</a></li>
                            <li><a href="#" class="hover:text-blue-500 transition">Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-sm">© {{ new Date().getFullYear() }} Sistem Operasional Absensi Mahasiswa. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
@keyframes blob {
  0% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(30px, -50px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.9); }
  100% { transform: translate(0px, 0px) scale(1); }
}
.animate-blob {
  animation: blob 7s infinite;
}
.animation-delay-2000 {
  animation-delay: 2s;
}
html {
    scroll-behavior: smooth;
}
</style>
