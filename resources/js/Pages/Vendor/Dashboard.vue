<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";
import { ref, computed } from "vue";

const props = defineProps({
    reports: Array,
    vendor: Object,
    auth: Object
});

const formatPrice = (value) => {
    if (!value) return 'Rp 0';
    // Mengubah string/angka menjadi format Rupiah (Contoh: 150000.00 -> 150.000)
    return 'Rp ' + parseFloat(value).toLocaleString('id-ID', { minimumFractionDigits: 0 });
};

const activeFilter = ref("Semua");
const filters = ["Semua", "pending", "process", "completed", "cancelled"];

const getFilterLabel = (f) => {
    if (f === 'pending') return 'Menunggu Pembayaran';
    if (f === 'process') return 'Dalam Pengerjaan';
    if (f === 'completed') return 'Selesai';
    if (f === 'cancelled') return 'Dibatalkan';
    return f;
};

// --- LOGIC PROGRESS BAR (SAMA DENGAN USER) ---
const getVisualProgress = (report) => {
    const status = report.status;
    const dbProgress = report.progress || 0;

    switch (status) {
        case 'verification': 
            return 25;
        case 'pending': 
            return 50;
        case 'payment_verification': 
            return 60; // User sudah bayar, menunggu konfirmasi
        case 'process': 
            // Jika DB 0 tapi status process, tampilkan 80%.
            // Jika vendor sudah update (misal 90%), pakai data asli.
            return dbProgress > 0 ? dbProgress : 80; 
        case 'completed': 
            return 100;
        case 'cancelled': 
            return 0;
        default: 
            return 0;
    }
};

const filteredReports = computed(() => {
    if (activeFilter.value === "Semua") return props.reports;
    return props.reports.filter((r) => r.status === activeFilter.value);
});

const getStatusClass = (status) => {
    const map = {
        pending: "bg-[#CA8E31]",
        process: "bg-[#4688FB]",
        completed: "bg-[#09A600]",
        cancelled: "bg-red-500",
    };
    return map[status] || "bg-gray-500";
};

// Fungsi membuka Drive dengan validasi HTTPS
const openDrive = (url) => {
    if (!url) return alert("Link Google Drive tidak tersedia.");
    const validUrl = url.startsWith('http') ? url : `https://${url}`;
    window.open(validUrl, '_blank');
};
</script>

<template>
    <Head title="Dashboard Mitra" />
    <Navbar />

    <div class="min-h-screen bg-[#FFFDF4] font-['Montserrat'] pb-20">
        <div class="w-full bg-gradient-to-r from-[#8FC555] to-[#4B692D] pt-32 pb-16 px-6">
            <div class="max-w-6xl mx-auto">
                <Link :href="route('welcome')" class="inline-flex items-center text-white/90 hover:text-white mb-6 transition gap-2 group">
                    <svg class="w-6 h-6 transform group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span class="font-medium text-lg">Kembali ke Halaman Utama</span>
                </Link>
                <h1 class="text-white text-3xl md:text-5xl font-bold mb-2">Halo, {{ vendor.nama_mitra }}!</h1>
                <p class="text-white/80">Pantau dan kelola pengerjaan proyek pemulihan rumah di sini.</p>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-6 mt-8">
            <div class="bg-white p-2 rounded-2xl shadow-md border border-gray-100 flex overflow-x-auto gap-2 no-scrollbar">
                <button v-for="f in filters" :key="f" @click="activeFilter = f"
                    :class="['px-6 py-2.5 rounded-xl font-bold text-sm transition-all shrink-0', activeFilter === f ? 'bg-[#BB4D00] text-white shadow-lg shadow-orange-100' : 'text-gray-500 hover:bg-gray-50']">
                    {{ getFilterLabel(f) }}
                </button>
            </div>

            <div class="mt-10 space-y-6">
                <div v-if="filteredReports.length === 0" class="text-center py-20 bg-white rounded-3xl border border-dashed border-gray-300">
                    <p class="text-gray-400 italic">Belum ada pesanan masuk untuk kategori ini.</p>
                </div>

                <div v-for="report in filteredReports" :key="report.id" 
                     class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all duration-300 group">
                    
                    <div class="flex flex-col md:flex-row p-4 md:p-6 gap-6 relative">
                        
                        <div class="w-full md:w-64 h-48 bg-gray-100 rounded-2xl overflow-hidden shrink-0 flex flex-col items-center justify-center border-2 border-dashed border-gray-200 relative">
                            <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            <button @click="openDrive(report.drive_link)" class="bg-white px-4 py-1.5 rounded-full border border-gray-200 text-[10px] font-bold text-gray-500 hover:bg-[#BB4D00] hover:text-white hover:border-[#BB4D00] transition-all uppercase tracking-widest shadow-sm">
                                Buka Google Drive
                            </button>
                        </div>

                        <div class="flex-1 flex flex-col justify-between py-1">
                            <div>
                                <div class="flex items-center gap-2 text-[#BB4D00] mb-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <span class="text-xs font-bold font-mono uppercase">Pengajuan REQ-{{ report.id }}</span>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ report.title }}</h3>
                                
                                <div class="space-y-1">
                                    <p class="text-sm text-gray-500 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" stroke-width="2"/></svg>
                                        {{ report.location }}
                                    </p>
                                    <p class="text-sm text-gray-500 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"/></svg>
                                        {{ report.date }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6">
                                <div class="flex justify-between text-xs mb-1.5">
                                    <span class="text-gray-400 font-medium">Progress Pekerjaan</span>
                                    <span class="font-bold text-[#BB4D00]">{{ getVisualProgress(report) }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                                    <div class="bg-[#BB4D00] h-full transition-all duration-700" :style="{ width: getVisualProgress(report) + '%' }"></div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col justify-between items-end shrink-0 md:min-w-[220px] border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-8">
                            
                            <div class="mb-auto">
                                <span :class="getStatusClass(report.status)" class="px-4 py-1.5 rounded-lg text-white text-[10px] font-bold uppercase tracking-wider shadow-sm">
                                    {{ getFilterLabel(report.status) }}
                                </span>
                            </div>

                            <div class="text-right">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">
                                    {{ report.price_final ? 'Total Biaya Fix' : 'Estimasi Awal' }}
                                </span>
                                
                                <p class="text-xl font-black text-[#4F3726]">
                                    {{ formatPrice(report.price_final || report.price_estimasi) }}
                                </p>
                            </div>
                            
                            <Link :href="route('vendor.reports.show', report.id)" class="mt-4 p-3 rounded-full bg-gray-50 text-gray-400 hover:bg-[#BB4D00] hover:text-white transition-all duration-300 border border-gray-100 shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M14 5l7 7m0 0l-7 7m7-7H3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>