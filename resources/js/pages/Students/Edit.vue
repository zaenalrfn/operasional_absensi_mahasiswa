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
    student: {
        id: number;
        name: string;
        email: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Students',
        href: '/students',
    },
    {
        title: 'Edit',
        href: `/students/${props.student.id}/edit`,
    },
];

const form = useForm({
    name: props.student.name,
    email: props.student.email,
    password: '',
});

const submit = () => {
    form.put(route('students.update', props.student.id));
};
</script>

<template>
    <Head title="Edit Student" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md dark:bg-gray-800">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Edit Mahasiswa</h2>
                
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

                    <div>
                        <Label for="password">Password (Optional)</Label>
                        <Input id="password" type="password" v-model="form.password" class="mt-1 block w-full" placeholder="Leave blank to keep current password" />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="flex items-center justify-end">
                        <Button :disabled="form.processing"> Update </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
