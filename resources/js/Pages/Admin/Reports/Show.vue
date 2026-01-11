<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

const props = defineProps({ report: Object });

// Form khusus untuk update status
const form = useForm({
    status: props.report.status,
});

const updateStatus = () => {
    form.patch(route('admin.reports.update', props.report.id));
};

// Setup Peta
const mapContainer = ref(null);
onMounted(() => {
    const map = L.map(mapContainer.value).setView([props.report.latitude, props.report.longitude], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    L.marker([props.report.latitude, props.report.longitude]).addTo(map);
});
</script>

<template>
    <Head title="Detail Laporan Admin" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin: Kelola Laporan</h2>
                <Link :href="route('admin.reports.index')" class="text-indigo-600 font-bold">Kembali</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
                        <div class="h-64 w-full rounded-lg border mb-4 z-0" ref="mapContainer"></div>
                        
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ report.title }}</h3>
                        <p class="text-gray-500 mb-4">Pelapor: {{ report.user.name }} ({{ report.user.phone }})</p>
                        
                        <div class="bg-gray-50 p-4 rounded-lg text-gray-700 border">
                            {{ report.description }}
                        </div>

                        <div class="mt-4 grid grid-cols-3 gap-2">
                            <div v-for="file in report.attachments" :key="file.id">
                                <img v-if="file.file_type === 'image'" :src="`/storage/${file.file_path}`" class="h-24 w-full object-cover rounded border">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-1">
                    <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg border-t-4 border-indigo-500">
                        <h4 class="font-bold text-lg mb-4 text-gray-800 dark:text-gray-200">Aksi Admin</h4>
                        
                        <form @submit.prevent="updateStatus" class="space-y-4">
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300 mb-2 font-bold">Update Status</label>
                                <select v-model="form.status" class="w-full border-gray-300 rounded-md shadow-sm text-gray-700">
                                    <option value="pending">⏳ Pending (Menunggu)</option>
                                    <option value="process">🛠️ On Process (Dikerjakan)</option>
                                    <option value="done">✅ Done (Selesai)</option>
                                </select>
                            </div>

                            <button type="submit" :disabled="form.processing" class="w-full py-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 shadow-lg">
                                Simpan Perubahan
                            </button>

                            <div v-if="form.recentlySuccessful" class="text-green-600 text-center text-sm font-bold mt-2">
                                Status Berhasil Diupdate!
                            </div>
                        </form>
                        
                        <div class="mt-6 pt-6 border-t">
                            <p class="text-sm text-gray-500">
                                <strong>Tips:</strong> Ubah status menjadi "On Process" jika teknisi sudah meluncur. Ubah "Done" jika perbaikan selesai.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>