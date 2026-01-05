<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { Loader2 } from 'lucide-vue-next';
import { route } from 'ziggy-js';


defineProps<{
    jurusans: string[];
}>();

const form = useForm({
    name: '',
    email: '',
    program_studi: '',
    photo: null as File | null,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('student.register.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
        forceFormData: true, // Important for file uploads
    });
};
</script>

<template>
    <Head title="Pendaftaran Mahasiswa" />

    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-xl w-full space-y-8 bg-white dark:bg-gray-800 p-8 rounded-xl shadow-lg">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                    Pendaftaran Mahasiswa
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                    Lengkapi data diri Anda untuk mendaftar sistem absensi.
                </p>
            </div>
            
            <form class="mt-8 space-y-6" @submit.prevent="submit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Name -->
                    <div class="col-span-2">
                        <Label for="name">Nama Lengkap</Label>
                        <Input 
                            id="name" 
                            type="text" 
                            v-model="form.name"
                            required 
                            placeholder="Nama Lengkap" 
                             class="mt-1"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <!-- Email -->
                    <div class="col-span-2">
                        <Label for="email">Email</Label>
                        <Input 
                            id="email" 
                            type="email" 
                            v-model="form.email"
                            required 
                            placeholder="email@mahasiswa.ac.id" 
                             class="mt-1"
                        />
                         <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <!-- Program Studi -->
                    <div class="col-span-2">
                        <Label for="program_studi">Program Studi</Label>
                        <select 
                            id="program_studi" 
                            v-model="form.program_studi" 
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 mt-1"
                            required
                        >
                            <option value="" disabled>Pilih Jurusan</option>
                            <option v-for="jur in jurusans" :key="jur" :value="jur">
                                {{ jur }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.program_studi" />
                    </div>

                     <!-- Photo -->
                    <div class="col-span-2">
                        <Label for="photo">Foto Profil</Label>
                        <Input 
                            id="photo" 
                            type="file" 
                            @input="form.photo = $event.target.files[0]"
                            class="mt-1 cursor-pointer"
                            accept="image/*"
                            required
                        />
                         <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Max 2MB.</p>
                        <InputError class="mt-2" :message="form.errors.photo" />
                    </div>

                    <!-- Password -->
                    <div class="col-span-1">
                         <Label for="password">Password</Label>
                        <Input 
                            id="password" 
                            type="password" 
                            v-model="form.password"
                            required 
                            placeholder="******" 
                             class="mt-1"
                        />
                         <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="col-span-1">
                         <Label for="password_confirmation">Konfirmasi Password</Label>
                        <Input 
                            id="password_confirmation" 
                            type="password" 
                            v-model="form.password_confirmation"
                            required 
                            placeholder="******" 
                             class="mt-1"
                        />
                         <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>
                </div>

                <div>
                    <Button type="submit" class="w-full mt-4" :disabled="form.processing">
                        <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                        Daftar Sebagai Mahasiswa
                    </Button>
                </div>

                <div class="flex items-center justify-center mt-4">
                     <span class="text-sm text-gray-600 dark:text-gray-400">Sudah punya akun? </span>
                    <Link :href="route('login')" class="ml-2 font-medium text-blue-600 hover:text-blue-500">
                        Login disini
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>
