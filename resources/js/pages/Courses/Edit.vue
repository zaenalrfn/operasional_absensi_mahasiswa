<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import type { BreadcrumbItem } from '@/types';
import { route } from 'ziggy-js';

const props = defineProps<{
    course: any;
    lecturers: Array<{
        id: number;
        name: string;
    }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Courses',
        href: '/courses',
    },
    {
        title: 'Edit',
        href: `/courses/${props.course.id}/edit`,
    },
];

const form = useForm({
    kode_mk: props.course.kode_mk,
    nama_mk: props.course.nama_mk,
    jurusan: props.course.jurusan,
    dosen_id: props.course.dosen_id,
    sks: props.course.sks,
    kelas: props.course.kelas,
    hari: props.course.hari,
    jam_mulai: props.course.jam_mulai,
    jam_selesai: props.course.jam_selesai,
    semester: props.course.semester,
});

const submit = () => {
    form.put(route('courses.update', props.course.id));
};
</script>

<template>
    <Head title="Edit Course" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md dark:bg-gray-800">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Edit Mata Kuliah</h2>
                
                <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-1">
                        <Label for="kode_mk">Kode MK</Label>
                        <Input id="kode_mk" type="text" v-model="form.kode_mk" class="mt-1 block w-full" required autofocus />
                        <InputError class="mt-2" :message="form.errors.kode_mk" />
                    </div>

                    <div class="col-span-1">
                        <Label for="nama_mk">Nama Mata Kuliah</Label>
                        <Input id="nama_mk" type="text" v-model="form.nama_mk" class="mt-1 block w-full" required />
                        <InputError class="mt-2" :message="form.errors.nama_mk" />
                    </div>

                    <div class="col-span-1">
                        <Label for="jurusan">Program Jurusan</Label>
                        <Input id="jurusan" type="text" v-model="form.jurusan" class="mt-1 block w-full" placeholder="Contoh: Teknik Informatika" required />
                        <InputError class="mt-2" :message="form.errors.jurusan" />
                    </div>

                    <div class="col-span-1">
                        <Label for="dosen_id">Dosen Pengampu</Label>
                        <select id="dosen_id" v-model="form.dosen_id" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 mt-1" required>
                            <option value="">Pilih Dosen</option>
                            <option v-for="lecturer in lecturers" :key="lecturer.id" :value="lecturer.id">
                                {{ lecturer.name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.dosen_id" />
                    </div>

                    <div class="col-span-1">
                        <Label for="sks">SKS</Label>
                        <Input id="sks" type="number" v-model="form.sks" class="mt-1 block w-full" required min="1" />
                        <InputError class="mt-2" :message="form.errors.sks" />
                    </div>

                    <div class="col-span-1">
                        <Label for="kelas">Kelas</Label>
                        <Input id="kelas" type="text" v-model="form.kelas" class="mt-1 block w-full" required />
                        <InputError class="mt-2" :message="form.errors.kelas" />
                    </div>

                    <div class="col-span-1">
                        <Label for="semester">Semester</Label>
                        <Input id="semester" type="number" v-model="form.semester" class="mt-1 block w-full" required min="1" />
                        <InputError class="mt-2" :message="form.errors.semester" />
                    </div>

                    <div class="col-span-1">
                        <Label for="hari">Hari</Label>
                        <select id="hari" v-model="form.hari" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 mt-1" required>
                            <option value="">Pilih Hari</option>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                        </select>
                         <InputError class="mt-2" :message="form.errors.hari" />
                    </div>

                    <div class="col-span-1 grid grid-cols-2 gap-2">
                         <div>
                            <Label for="jam_mulai">Jam Mulai</Label>
                            <Input id="jam_mulai" type="time" v-model="form.jam_mulai" class="mt-1 block w-full" required />
                             <InputError class="mt-2" :message="form.errors.jam_mulai" />
                        </div>
                         <div>
                            <Label for="jam_selesai">Jam Selesai</Label>
                            <Input id="jam_selesai" type="time" v-model="form.jam_selesai" class="mt-1 block w-full" required />
                             <InputError class="mt-2" :message="form.errors.jam_selesai" />
                        </div>
                    </div>

                    <div class="col-span-1 md:col-span-2 flex items-center justify-end mt-4">
                        <Button :disabled="form.processing"> Update Course </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
