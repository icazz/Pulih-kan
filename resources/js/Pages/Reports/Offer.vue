<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import { computed } from 'vue';

const props = defineProps({
    report: Object,
    formattedPrice: String
});
const layanan = computed(() => {
    const cat = props.report.category;
    if (cat === 'Ringan') return "Perbaikan Kosmetik, Pengecatan & Perbaikan Minor";
    if (cat === 'Sedang') return "Perbaikan Struktural Ringan, Atap & Utilitas";
    if (cat === 'Berat') return "Renovasi Struktur, Kelistrikan & Sanitasi Total";
    return "Menunggu Assessment Lanjut";
});
</script>

<template>
    <Head title="Pengajuan Pemulihan Rumah" />
        <Navbar /> 

    <div class="min-h-screen bg-[#FFFFFA] font-['Montserrat'] pb-20">
        <div class="w-full bg-gradient-to-r from-[#28160A] via-[#000000] to-[#2D190D] pt-32 pb-16 shadow-lg">
            <div class="max-w-5xl mx-auto px-6">
                <Link :href="route('reports.index')" class="inline-flex items-center text-white/90 hover:text-white mb-6 transition gap-2 group">
                    <svg class="w-6 h-6 transform group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span class="font-medium text-lg">Kembali</span>
                </Link>
                <h1 class="text-3xl md:text-5xl font-bold text-[#FFEBDE] mb-3 leading-tight">
                    Pengajuan <br class="md:hidden"> Perbaikan Rumah
                </h1>
                <p class="text-white text-base md:text-lg opacity-90 font-light">
                    Lengkapi data untuk mendapatkan perbaikan
                </p>
            </div>
        </div>
        <div class="max-w-4xl mx-auto px-6">

            <div class="max-w-5xl mx-auto mt-12 mb-16 px-6">
                <div class="flex items-center justify-between relative w-full max-w-2xl mx-auto">
                    <div class="absolute top-1/2 left-0 w-full h-[4px] bg-[#D6C5BA] z-0 transform -translate-y-1/2 rounded-full"></div>
                    <div class="relative z-10 bg-[#FFFFFA] px-3">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center border-[3px] transition-all duration-300 shadow-sm bg-[#D6C5BA] border-[#D6C5BA]">
                            <img src="/images/step-1-home.png" class="w-8 h-8 object-contain brightness-0 invert opacity-60">
                        </div>
                    </div>
                    <div class="relative z-10 bg-[#FFFFFA] px-3">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center border-[3px] transition-all duration-300 shadow-sm bg-[#D6C5BA] border-[#D6C5BA]">
                            <img src="/images/step-2-alert.png" class="w-8 h-8 object-contain brightness-0 invert opacity-60">
                        </div>
                    </div>
                    <div class="relative z-10 bg-[#FFFFFA] px-3">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center border-[3px] transition-all duration-300 shadow-sm bg-[#973C00] border-[#973C00]">
                            <img src="/images/step-3-file.png" class="w-8 h-8 object-contain brightness-0 invert opacity-100">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-300 rounded-2xl p-6 md:p-8 mb-6 shadow-sm">
                <h2 class="text-xl font-bold text-[#1C1917] mb-4">Kategori Kerusakan</h2>
                <div class="inline-flex items-center gap-2 bg-[#FFE4E4] border border-[#FFCFCF] rounded-full px-6 py-2">
                    <div class="w-5 h-5 rounded-full border-2 border-[#D32F2F] flex items-center justify-center"><div class="w-3 h-0.5 bg-[#D32F2F]"></div></div>
                    <span class="text-[#D32F2F] font-bold text-lg">Kerusakan {{ report.category }}</span>
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