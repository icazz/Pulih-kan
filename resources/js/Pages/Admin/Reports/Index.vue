<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({ reports: Array });

const statusClass = (status) => {
    switch(status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'process': return 'bg-blue-100 text-blue-800';
        case 'done': return 'bg-green-100 text-green-800';
    }
};
</script>

<template>
    <Head title="Admin - Kelola Laporan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Daftar Laporan Masuk
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelapor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Masalah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lokasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="report in reports" :key="report.id">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ report.user.name }}</div>
                                        <div class="text-xs text-gray-500">{{ report.user.email }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        {{ report.title }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ report.location }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span :class="`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass(report.status)}`">
                                            {{ report.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <Link :href="route('admin.reports.show', report.id)" class="text-indigo-600 hover:text-indigo-900 font-bold">
                                            Kelola
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>