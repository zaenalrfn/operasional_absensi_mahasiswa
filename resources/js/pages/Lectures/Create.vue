<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import type { BreadcrumbItem } from '@/types';
import { route } from 'ziggy-js';


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Lectures',
        href: '/lectures',
    },
    {
        title: 'Create',
        href: '/lectures/create',
    },
];

const form = useForm({
    name: '',
    email: '',
});

const submit = () => {
    form.post(route('lectures.store'));
};
</script>

<template>
    <Head title="Create Lecture" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md dark:bg-gray-800">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Tambah Dosen</h2>
                
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <Label for="name">Name</Label>
                        <Input id="name" type="text" v-model="form.name" class="mt-1 block w-full" required autofocus />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div>
                        <Label for="email">Email</Label>
                        <Input id="email" type="email" v-model="form.email" class="mt-1 block w-full" required />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="flex items-center justify-end">
                        <Button :disabled="form.processing"> Save </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
