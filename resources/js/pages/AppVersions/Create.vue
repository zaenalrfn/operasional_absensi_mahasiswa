<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { route } from 'ziggy-js';
import type { BreadcrumbItem } from '@/types';
import { Plus, Trash2, ArrowLeft } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'App Versions',
        href: '/app-versions',
    },
    {
        title: 'Create',
        href: '/app-versions/create',
    },
];

const form = useForm({
    version_number: '',
    release_date: new Date().toISOString().split('T')[0], // Default to today
    platform: '', // Optional
    download_url: '',
    is_mandatory: false,
    release_notes: [''] as string[],
});

const addNote = () => {
    form.release_notes.push('');
};

const removeNote = (index: number) => {
    form.release_notes.splice(index, 1);
};

const submit = () => {
    form.post(route('app-versions.store'));
};
</script>

<template>
    <Head title="Create App Version" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="flex items-center mb-6">
                 <Link :href="route('app-versions.index')" class="mr-4">
                    <Button variant="outline" size="icon">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Create New App Version
                </h2>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg max-w-2xl mx-auto p-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <Label for="version_number">Version Number</Label>
                        <Input id="version_number" v-model="form.version_number" placeholder="e.g. 1.0.0" class="mt-1" required />
                        <InputError class="mt-2" :message="form.errors.version_number" />
                    </div>

                    <div>
                        <Label for="release_date">Release Date</Label>
                        <Input id="release_date" type="date" v-model="form.release_date" class="mt-1" required />
                        <InputError class="mt-2" :message="form.errors.release_date" />
                    </div>

                    <div>
                        <Label for="platform">Platform (Optional)</Label>
                        <Input id="platform" v-model="form.platform" placeholder="android, ios, or leave empty for all" class="mt-1" />
                        <InputError class="mt-2" :message="form.errors.platform" />
                    </div>

                    <div>
                         <Label for="download_url">Download URL (Optional)</Label>
                        <Input id="download_url" v-model="form.download_url" placeholder="https://..." class="mt-1" />
                        <InputError class="mt-2" :message="form.errors.download_url" />
                    </div>

                    <div class="flex items-center space-x-2">
                        <input 
                            id="is_mandatory" 
                            type="checkbox"
                            v-model="form.is_mandatory"
                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                        />
                        <Label for="is_mandatory" class="mb-0">Mandatory Update</Label>
                    </div>
                    <InputError class="mt-2" :message="form.errors.is_mandatory" />

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <Label>Release Notes</Label>
                            <Button type="button" variant="outline" size="sm" @click="addNote">
                                <Plus class="h-4 w-4 mr-1" /> Add Note
                            </Button>
                        </div>
                        <div v-for="(note, index) in form.release_notes" :key="index" class="flex gap-2 mb-2">
                            <Input v-model="form.release_notes[index]" placeholder="Update feature X..." />
                            <Button type="button" variant="destructive" size="icon" @click="removeNote(index)" v-if="form.release_notes.length > 1">
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </div>
                        <InputError class="mt-2" :message="form.errors.release_notes" />
                    </div>

                    <div class="flex justify-end">
                         <Button type="submit" :disabled="form.processing">
                            Save Version
                         </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
