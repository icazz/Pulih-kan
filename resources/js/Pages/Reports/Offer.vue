<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';

const props = defineProps({
    report: Object,
    formattedPrice: String
});

// Data Dummy untuk simulasi respon admin
const kategori = "Kerusakan Berat"; 
const layanan = "Renovasi Struktur, Kelistrikan & Sanitasi Total";
</script>

<template>
    <Head title="Penawaran Pemulihan" />
    <Navbar />

    <div class="min-h-screen bg-[#FFFFFA] font-['Montserrat'] pb-20 pt-32">
        <div class="max-w-4xl mx-auto px-6">
            
            <h1 class="text-3xl md:text-4xl font-bold text-[#28160A] mb-2">
                Penawaran Pemulihan
            </h1>
            <p class="text-gray-500 mb-8">
                Berikut adalah hasil analisis dan penawaran dari vendor kami.
            </p>

            <div class="flex justify-center items-center gap-4 mb-10 opacity-60 grayscale">
                <div class="w-10 h-10 bg-[#D6C5BA] rounded-full flex items-center justify-center"><img src="/images/step-1-home.png" class="w-5 brightness-0 invert"></div>
                <div class="w-20 h-1 bg-[#D6C5BA]"></div>
                <div class="w-10 h-10 bg-[#D6C5BA] rounded-full flex items-center justify-center"><img src="/images/step-2-alert.png" class="w-5 brightness-0 invert"></div>
                <div class="w-20 h-1 bg-[#973C00]"></div>
                <div class="w-10 h-10 bg-[#973C00] rounded-full flex items-center justify-center shadow-lg transform scale-110"><img src="/images/step-3-file.png" class="w-5 brightness-0 invert"></div>
            </div>

            <div class="bg-white border border-gray-300 rounded-2xl p-6 md:p-8 mb-6 shadow-sm">
                <h2 class="text-xl font-bold text-[#1C1917] mb-4">Kategori Kerusakan</h2>
                <div class="inline-flex items-center gap-2 bg-[#FFE4E4] border border-[#FFCFCF] rounded-full px-6 py-2">
                    <div class="w-5 h-5 rounded-full border-2 border-[#D32F2F] flex items-center justify-center"><div class="w-3 h-0.5 bg-[#D32F2F]"></div></div>
                    <span class="text-[#D32F2F] font-bold text-lg">{{ kategori }}</span>
                </div>
            </div>

            <div class="bg-white border border-yellow-400 rounded-2xl p-6 md:p-8 mb-6 shadow-sm bg-yellow-50/10">
                <h2 class="text-xl font-bold text-[#1C1917] mb-4">Rekomendasi Layanan</h2>
                <p class="text-gray-700 text-lg leading-relaxed mb-4">{{ layanan }}</p>
                <div class="bg-white border border-gray-200 rounded-lg p-3 flex justify-between items-center text-gray-500">
                    <span>Cari tahu vendormu di sini : <span class="bg-yellow-400 text-white text-xs px-2 py-1 rounded">Lihat Vendor</span></span>
                </div>
                <div class="mt-2 p-3 border rounded bg-gray-50 text-gray-600">
                    Vendor A - Jarak/Alamat
                </div>
            </div>

            <div class="bg-white border border-gray-300 rounded-2xl p-6 md:p-8 mb-6 shadow-sm">
                <h2 class="text-xl font-bold text-[#1C1917] mb-2">Estimasi Biaya Awal</h2>
                <p class="text-4xl md:text-5xl font-bold text-[#BB4D00] mb-2">
                    {{ formattedPrice }}
                </p>
                <p class="text-gray-400 text-sm">*Estimasi berdasarkan assessment awal. Biaya final akan ditentukan setelah inspeksi vendor melalui kontrak.</p>
            </div>

            <div class="bg-white border border-yellow-400 rounded-2xl p-6 md:p-8 mb-10 shadow-sm bg-yellow-50/10">
                <h2 class="text-xl font-bold text-[#1C1917] mb-3">Langkah Selanjutnya</h2>
                <p class="text-gray-600 leading-relaxed">
                    Setelah Anda melanjutkan pengajuan, kami akan menghubungkan Anda dengan vendor terpercaya di wilayah Anda untuk inspeksi detail dan penawaran harga.
                </p>
            </div>

            <div class="flex justify-end mt-8 gap-4 pb-20">
                <Link :href="route('reports.show', report.id)" class="px-6 py-3 rounded-full text-gray-600 font-bold hover:bg-gray-100 transition border border-gray-300">
                    Kembali
                </Link>
                
                <button class="px-8 py-3 rounded-full text-white font-bold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all flex items-center gap-2 bg-gradient-to-r from-[#552300] to-[#BB4D00]">
                    Ajukan Kerja Sama <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>

        </div>
    </div>
</template>