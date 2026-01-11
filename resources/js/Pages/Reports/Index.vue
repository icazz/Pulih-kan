<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3'; // <--- Pastikan Link ada di sini

defineProps({
    reports: Array
});

const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
};

const statusClass = (status) => {
    switch(status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800 border-yellow-200';
        case 'process': return 'bg-blue-100 text-blue-800 border-blue-200';
        case 'done': return 'bg-green-100 text-green-800 border-green-200';
        default: return 'bg-gray-100 text-gray-800';
    }
};

const statusLabel = (status) => {
    switch(status) {
        case 'pending': return '⏳ Menunggu Teknisi';
        case 'process': return '🛠️ Sedang Dikerjakan';
        case 'done': return '✅ Selesai';
        default: return status;
    }
};
</script>

<template>
    <Head title="Riwayat Laporan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Riwayat Laporan Saya</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div v-if="reports.length === 0" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-10 text-center">
                    <div class="text-5xl mb-4">📭</div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Belum ada laporan.</h3>
                    <p class="text-gray-500 mb-6">Punya kerusakan di rumah? Segera panggil teknisi kami.</p>
                    <Link :href="route('report.create')" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        + Buat Laporan Baru
                    </Link>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="report in reports" :key="report.id" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition duration-200 border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col h-full">
                        
                        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700/50">
                            <span :class="`px-3 py-1 rounded-full text-xs font-bold border ${statusClass(report.status)}`">
                                {{ statusLabel(report.status) }}
                            </span>
                            <span class="text-xs text-gray-500">
                                {{ formatDate(report.created_at) }}
                            </span>
                        </div>

                        <div class="p-4 flex-grow">
                            <h4 class="font-bold text-lg text-gray-800 dark:text-gray-100 mb-2 truncate">
                                {{ report.title }}
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-4">
                                {{ report.description }}
                            </p>
                            
                            <div v-if="report.attachments.length > 0" class="h-32 w-full rounded-lg overflow-hidden bg-gray-100">
                                <template v-if="report.attachments[0].file_type === 'image'">
                                    <img :src="`/storage/${report.attachments[0].file_path}`" class="w-full h-full object-cover">
                                </template>
                                <template v-else>
                                    <div class="flex items-center justify-center h-full text-gray-400 bg-gray-200">
                                        🎬 Video Terlampir
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="p-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 mt-auto">
                            <div class="flex items-start mb-3">
                                <span class="text-gray-400 mr-1">📍</span>
                                <span class="text-xs text-gray-500 line-clamp-1">{{ report.location }}</span>
                            </div>
                            
                            <Link :href="route('report.show', report.id)" class="block w-full py-2 text-center text-sm font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                                Lihat Detail
                            </Link>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>