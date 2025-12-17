<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger, DialogFooter } from '@/components/ui/dialog';
import "leaflet/dist/leaflet.css";
import { LMap, LTileLayer, LMarker, LCircle } from "@vue-leaflet/vue-leaflet";
import type { BreadcrumbItem } from '@/types';
import { route } from 'ziggy-js';

// Props
defineProps<{
    locations: Array<{
        id: number;
        name: string;
        latitude: string;
        longitude: string;
        radius_meters: number;
    }>;
}>();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Lokasi Absensi', href: '/attendance-locations' },
];

// Form
const form = useForm({
    name: '',
    latitude: -7.756573, // Default (e.g., Jogja)
    longitude: 110.344110,
    radius_meters: 100,
});

const isDialogOpen = ref(false);

// Map Settings
const zoom = ref(15);
const center = ref<[number, number]>([-7.756573, 110.344110]); // Reactive center
const mapRef = ref(null); // Map reference

// Search
const searchQuery = ref('');
const searchResults = ref<any[]>([]);

const searchLocation = async () => {
    if (!searchQuery.value) return;
    
    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(searchQuery.value)}`);
        const data = await response.json();
        searchResults.value = data;
    } catch (error) {
        console.error('Error searching location:', error);
    }
};

const selectSearchResult = (result: any) => {
    const lat = parseFloat(result.lat);
    const lon = parseFloat(result.lon);
    
    form.latitude = lat;
    form.longitude = lon;
    
    // Update map view via Leaflet Object
    if (mapRef.value) {
        // @ts-ignore
        mapRef.value.leafletObject.flyTo([lat, lon], 18);
    } else {
        center.value = [lat, lon];
        zoom.value = 18;
    }
    
    searchResults.value = []; // Clear results
};

const onMapClick = (e: any) => {
    form.latitude = e.latlng.lat;
    form.longitude = e.latlng.lng;
};

const submit = () => {
    form.post(route('attendance-locations.store'), {
        onSuccess: () => {
            isDialogOpen.value = false;
            form.reset();
            searchQuery.value = '';
            searchResults.value = [];
        },
    });
};

const deleteLocation = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus lokasi ini?')) {
        useForm({}).delete(route('attendance-locations.destroy', id));
    }
};
</script>

<template>
    <Head title="Lokasi Absensi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold tracking-tight">Manajemen Lokasi Absensi</h2>
                <Dialog v-model:open="isDialogOpen">
                    <DialogTrigger as-child>
                        <Button>Tambah Lokasi</Button>
                    </DialogTrigger>
                    <DialogContent class="sm:max-w-[600px] overflow-y-auto h-[calc(100vh-5rem)]">
                        <DialogHeader>
                            <DialogTitle>Tambah Lokasi Baru</DialogTitle>
                        </DialogHeader>
                        
                        <div class="grid gap-4 py-4">
                            <div class="grid gap-2">
                                <Label for="name">Nama Lokasi</Label>
                                <Input id="name" v-model="form.name" placeholder="Contoh: Kampus Utama" />
                                <span v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</span>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="grid gap-2">
                                    <Label for="lat">Latitude</Label>
                                    <Input id="lat" v-model="form.latitude" type="number" step="any" readonly />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="lng">Longitude</Label>
                                    <Input id="lng" v-model="form.longitude" type="number" step="any" readonly />
                                </div>
                            </div>

                            <div class="grid gap-2">
                                <Label for="radius">Radius (Meter)</Label>
                                <Input id="radius" v-model="form.radius_meters" type="number" />
                            </div>

                            <!-- Search -->
                            <div class="grid gap-2">
                                <Label>Cari Lokasi (Maps)</Label>
                                <div class="flex gap-2">
                                    <Input v-model="searchQuery" placeholder="Cari nama tempat..." @keyup.enter="searchLocation" />
                                    <Button type="button" variant="secondary" @click="searchLocation">Cari</Button>
                                </div>
                                
                                <!-- Search Results -->
                                <div v-if="searchResults.length > 0" class="border rounded-md mt-2 max-h-40 overflow-y-auto bg-white">
                                    <div 
                                        v-for="(result, index) in searchResults" 
                                        :key="index"
                                        class="p-2 hover:bg-gray-100 cursor-pointer text-sm border-b last:border-0"
                                        @click="selectSearchResult(result)"
                                    >
                                        {{ result.display_name }}
                                    </div>
                                </div>
                            </div>

                            <!-- Map Container -->
                             <div class="h-[300px] w-full rounded border relative overflow-hidden z-0">
                                <l-map
                                    ref="mapRef"
                                    v-model:zoom="zoom"
                                    :center="center"
                                    :use-global-leaflet="false"
                                    @click="onMapClick"
                                >
                                    <l-tile-layer
                                        url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                                        layer-type="base"
                                        name="OpenStreetMap"
                                    ></l-tile-layer>

                                    <!-- Marker for selected position -->
                                    <l-marker :lat-lng="[Number(form.latitude), Number(form.longitude)]"></l-marker>
                                    
                                    <!-- Circle to visualize radius -->
                                    <l-circle 
                                        :lat-lng="[Number(form.latitude), Number(form.longitude)]" 
                                        :radius="form.radius_meters"
                                        color="blue"
                                        fill-color="#3b82f6"
                                        :fill-opacity="0.2"
                                    />
                                </l-map>
                            </div>
                            <p class="text-xs text-muted-foreground">Klik pada peta untuk menentukan titik pusat lokasi.</p>
                        </div>

                        <DialogFooter>
                            <Button @click="submit" :disabled="form.processing">Simpan Lokasi</Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>
            </div>

            <!-- List Locations -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="location in locations" :key="location.id">
                    <CardHeader>
                        <CardTitle>{{ location.name }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">Lat/Long:</span>
                                <span>{{ Number(location.latitude).toFixed(6) }}, {{ Number(location.longitude).toFixed(6) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">Radius:</span>
                                <span>{{ location.radius_meters }} Meter</span>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <Button variant="destructive" size="sm" @click="deleteLocation(location.id)">Hapus</Button>
                        </div>
                    </CardContent>
                </Card>
                
                <div v-if="locations.length === 0" class="col-span-full p-8 text-center text-muted-foreground bg-gray-50 rounded-lg border border-dashed">
                    Belum ada lokasi absensi yang ditambahkan.
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
/* Fix Leaflet Z-Index if needed inside Modal */
.leaflet-pane {
    z-index: 0 !important;
}
.leaflet-top, .leaflet-bottom {
    z-index: 1 !important;
}
</style>
