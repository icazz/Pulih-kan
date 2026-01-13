<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import { computed } from 'vue';

const props = defineProps({
    report: Object,
    formattedPrice: String
});

// REVISI: Mengambil data dari checklist kerusakan (damage_types)
const layanan = computed(() => {
    if (props.report.damage_types && props.report.damage_types.length > 0) {
        // Menggabungkan array menjadi string dipisah koma
        return props.report.damage_types.join(', ');
    }
    return "Perbaikan Umum & Pemulihan";
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
                
                <p class="text-gray-700 text-lg leading-relaxed mb-4 capitalize">
                    {{ layanan }}
                </p>

                <div class="bg-white border border-gray-200 rounded-lg p-3 flex justify-between items-center text-gray-500">
                    <span>Cari tahu vendormu di sini : <span class="bg-yellow-400 text-white text-xs px-2 py-1 rounded">Lihat Vendor</span></span>
                </div>

                <div v-if="report.vendor" class="mt-2 p-4 border border-gray-200 rounded-xl bg-gray-50 text-gray-700 flex flex-col gap-1 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-lg text-[#28160A]">{{ report.vendor.nama_mitra }}</span>
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded font-bold">Mitra Terpilih</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm mt-1">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>{{ report.vendor.alamat }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        <span>{{ report.vendor.no_telepon }}</span>
                    </div>
                </div>
                
                <div v-else class="mt-2 p-3 border border-red-200 rounded bg-red-50 text-red-600 text-sm italic">
                    Belum ada mitra yang ditugaskan oleh admin.
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