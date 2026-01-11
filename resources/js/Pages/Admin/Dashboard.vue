<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: Object,
    pending_verifications: Array
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Admin Control Panel
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                
                <h3 class="text-gray-500 dark:text-gray-400 font-bold mb-4 px-1">Ringkasan Statistik</h3>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                    
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl p-6 border-l-4 border-blue-500 hover:shadow-lg transition-shadow">
                        <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total User</div>
                        <div class="text-3xl font-extrabold text-gray-800 dark:text-gray-100 mt-2">{{ stats.total_users }}</div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl p-6 border-l-4 border-green-500 hover:shadow-lg transition-shadow">
                        <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Mitra Resmi</div>
                        <div class="text-3xl font-extrabold text-gray-800 dark:text-gray-100 mt-2">{{ stats.total_vendors }}</div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl p-6 border-l-4 border-orange-500 relative hover:shadow-lg transition-shadow">
                        <div class="text-orange-600 text-xs font-bold uppercase tracking-wider">Pending Vendor</div>
                        <div class="text-3xl font-extrabold text-gray-800 dark:text-gray-100 mt-2">{{ stats.pending_vendors }}</div>
                        
                        <span v-if="stats.pending_vendors > 0" class="absolute top-4 right-4 flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
                        </span>
                    </div>

                    <Link :href="route('admin.reports.index')" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl p-6 border-l-4 border-red-500 hover:shadow-lg transition-shadow cursor-pointer group">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="text-gray-500 text-xs font-bold uppercase tracking-wider group-hover:text-red-600 transition-colors">Total Laporan</div>
                                <div class="flex items-end gap-2">
                                    <div class="text-3xl font-extrabold text-gray-800 dark:text-gray-100 mt-2">{{ stats.total_reports }}</div>
                                    <div v-if="stats.pending_reports > 0" class="text-sm text-red-500 font-bold mb-1">
                                        ({{ stats.pending_reports }} Baru)
                                    </div>
                                </div>
                            </div>
                            <div class="text-gray-300 group-hover:text-red-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </div>
                        </div>
                    </Link>
                </div>

                <h3 class="text-gray-500 dark:text-gray-400 font-bold mb-4 px-1">Daftar Tugas</h3>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700/50">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-black-100 flex items-center gap-2">
                            📋 Verifikasi Mitra Baru
                            <span v-if="pending_verifications.length > 0" class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-800 text-xs">
                                {{ pending_verifications.length }}
                            </span>
                        </h3>
                        <Link href="#" class="text-indigo-600 hover:text-indigo-800 text-sm font-bold">Lihat Semua</Link>
                    </div>

                    <div class="p-6">
                        <div v-if="pending_verifications.length === 0" class="text-center text-gray-500 py-10 flex flex-col items-center">
                            <div class="text-5xl mb-4 opacity-50">☕</div>
                            <p class="font-medium">Semua aman! Tidak ada antrian verifikasi.</p>
                        </div>

                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama Toko</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pemilik</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kategori</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="vendor in pending_verifications" :key="vendor.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-800 dark:text-gray-200">
                                            {{ vendor.shop_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400">
                                            {{ vendor.user.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ vendor.service_type }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <Link :href="`/admin/verify-vendor/${vendor.id}`" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:shadow-outline-indigo transition ease-in-out duration-150">
                                                🔍 Cek Detail
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>