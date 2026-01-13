<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    vendor: Object
});
</script>

<template>
    <Head title="Dashboard Mitra" />

    <div class="min-h-screen bg-gray-50 p-8 font-['Montserrat']">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-[#4F3726]">Dashboard Mitra</h1>
                    <p class="text-gray-500">Selamat datang, {{ vendor.nama_mitra }}</p>
                </div>
                <Link :href="route('welcome')" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm hover:bg-gray-100">
                    Kembali ke Beranda
                </Link>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 text-center py-20">
                <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
                    <i class="fas fa-check"></i> ✓
                </div>
                <h2 class="text-xl font-bold text-gray-800">Akun Terverifikasi!</h2>
                <p class="text-gray-500 mt-2">Anda sekarang dapat menerima pesanan dari klien.</p>
                <div class="mt-6">
                    <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-3 py-1 rounded-full">Fitur Pesanan Segera Hadir</span>
                </div>
            </div>
        </div>
    </div>
</template>