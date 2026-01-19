<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Navbar from '@/Components/Navbar.vue';

const { props } = usePage();
const user = props.auth.user; 

const propsData = defineProps({
    reports: Array
});

// --- SEARCH & FILTER LOGIC ---
const searchQuery = ref('');
const activeTab = ref('Semua');

const tabs = [
    'Semua', 
    'Menunggu Verifikasi', 
    'Menunggu Pembayaran', 
    'Dalam Pengerjaan', 
    'Selesai', 
    'Dibatalkan' 
];

const getCoverImage = (report) => {
    // 1. Cek apakah ada data evidence
    if (!report.evidence_files) return null;

    let files = [];
    try {
        // 2. Parsing data. Jika database mengembalikan string JSON, kita parse.
        // Jika sudah array, langsung pakai.
        files = typeof report.evidence_files === 'string' 
            ? JSON.parse(report.evidence_files) 
            : report.evidence_files;
    } catch (e) {
        return null;
    }

    // 3. Ambil file pertama
    if (Array.isArray(files) && files.length > 0) {
        const firstFile = files[0];
        // Cek ekstensi file (pastikan gambar)
        if (firstFile.match(/\.(jpeg|jpg|gif|png|webp)$/i)) {
             // Return path lengkap ke storage
             return '/storage/' + firstFile;
        }
    }
    return null; // Jika tidak ada gambar atau error
};

// --- LOGIC STATUS BADGE (Diupdate dengan status baru) ---
const getStatusBadge = (status) => {
    const map = {
        'verification':         { label: 'Menunggu Verifikasi', class: 'bg-gray-500' }, 
        'pending':              { label: 'Menunggu Pembayaran', class: 'bg-[#CA8E31]' },
        'payment_verification': { label: 'Menunggu Konfirmasi', class: 'bg-blue-500' }, // <--- TAMBAHAN
        'process':              { label: 'Dalam Pengerjaan',    class: 'bg-[#4688FB]' },
        'completed':            { label: 'Selesai',             class: 'bg-[#09A600]' },
        'cancelled':            { label: 'Dibatalkan',          class: 'bg-red-500' },
    };
    return map[status] || map['verification'];
};

// --- LOGIC PROGRESS BAR (SESUAI REQUEST ANDA) ---
const getVisualProgress = (report) => {
    const status = report.status;
    const dbProgress = report.progress || 0;

    switch (status) {
        case 'verification': 
            return 25;
        case 'pending': 
            return 50;
        case 'payment_verification': 
            return 60; // Sedikit lebih maju dari pending
        case 'process': 
            // Jika di DB progressnya 0, kita tampilkan 80% sesuai request.
            // Tapi jika vendor sudah update (misal 90%), kita pakai data asli biar akurat.
            return dbProgress > 0 ? dbProgress : 80; 
        case 'completed': 
            return 100;
        case 'cancelled': 
            return 0;
        default: 
            return 0;
    }
};

// Filter Laporan
const filteredReports = computed(() => {
    let data = propsData.reports;
    
    // Filter by Tab
    if (activeTab.value !== 'Semua') {
        // Khusus tab "Menunggu Pembayaran", kita gabung status pending & payment_verification
        if (activeTab.value === 'Menunggu Pembayaran') {
            data = data.filter(r => r.status === 'pending' || r.status === 'payment_verification');
        } else {
            data = data.filter(r => getStatusBadge(r.status).label === activeTab.value);
        }
    }

    // Filter by Search
    if (searchQuery.value) {
        const lowerQ = searchQuery.value.toLowerCase();
        data = data.filter(r => 
            r.title.toLowerCase().includes(lowerQ) || 
            r.location.toLowerCase().includes(lowerQ) ||
            r.id.toString().includes(lowerQ)
        );
    }
    return data;
});
</script>

<template>
    <Head title="Laporan Saya" />
    <Navbar />

    <div class="min-h-screen bg-[#F9F9F9] font-['Montserrat'] pb-20">
        
        <div class="w-full bg-[linear-gradient(to_left,#4B741F,#BB4D00,#BB4D00,#412D20)] pt-32 pb-16 px-6 relative overflow-hidden">
            <div class="max-w-6xl mx-auto relative z-10">
                <Link :href="route('welcome')" class="inline-flex items-center text-white/90 hover:text-white mb-6 transition gap-2 group">
                    <svg class="w-6 h-6 transform group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span class="font-medium text-lg">Kembali</span>
                </Link>
                <div class="absolute top-0 right-0 text-white opacity-80 cursor-pointer hover:opacity-100 p-2">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>

                <h1 class="text-3xl md:text-4xl font-bold text-[#FFFFFF] mb-2">
                    Selamat Datang, {{ user.name }}!
                </h1>
                <p class="text-[#FFF3C4] text-lg mb-8">
                    Ada yang bisa kami bantu hari ini? Ajukan dan pantau progres pemulihan Anda.
                </p>

                <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
                    <div class="relative w-full md:w-1/2">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input v-model="searchQuery" type="text" placeholder="Cari pengajuan..." class="w-full py-3 pl-12 pr-4 rounded-lg bg-[#FFFFFFF2] text-gray-700 border-none focus:ring-2 focus:ring-[#CA3500] shadow-sm">
                    </div>
                    <Link :href="route('reports.create')" class="w-full md:w-auto bg-[#CA3500] hover:bg-[#a62b00] text-white font-semibold py-3 px-6 rounded-lg shadow-md transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Ajukan Pemulihan Rumah
                    </Link>
                </div>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-6 mt-8 mb-6 overflow-x-auto pb-2">
            <div class="flex flex-nowrap md:flex-wrap gap-2 min-w-max">
                <button v-for="tab in tabs" :key="tab" @click="activeTab = tab" class="px-5 py-2 rounded-full text-sm font-semibold transition-all border whitespace-nowrap" :class="activeTab === tab ? 'bg-[#F54900] text-white border-[#F54900]' : 'bg-white text-gray-600 border-[#E5E7EB] hover:bg-gray-50'">
                    {{ tab }}
                </button>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-6 space-y-6">
            
            <div v-if="filteredReports.length === 0" class="bg-white rounded-3xl p-12 text-center shadow-sm border border-gray-100">
                <h3 class="text-xl font-bold text-gray-800">Tidak ada laporan</h3>
                <p class="text-gray-500 mb-6">Tidak ada pengajuan ditemukan di tab "{{ activeTab }}".</p>
                <Link v-if="activeTab === 'Semua'" :href="route('reports.create')" class="text-[#BB4D00] font-bold hover:underline">Ajukan Sekarang</Link>
            </div>

            <div 
                v-for="report in filteredReports" 
                :key="report.id"
                class="bg-white rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition-all overflow-hidden"
            >
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-50">
                    <div class="flex items-center gap-2 text-[#BB4D00]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        <span class="text-sm font-medium text-[#787676]">
                            Pengajuan <span class="text-[#BB4D00] font-bold">REQ-{{ report.id }}</span>
                        </span>
                    </div>

                    <span :class="`${getStatusBadge(report.status).class} text-white text-sm font-bold px-4 py-1.5 rounded-lg shadow-sm`">
                        {{ getStatusBadge(report.status).label }}
                    </span>
                </div>

                <div class="p-6 flex flex-col md:flex-row gap-6">
                    <div class="w-full md:w-56 h-48 rounded-2xl overflow-hidden flex-shrink-0 relative shadow-sm border border-gray-100 bg-gray-50 group">
    
                        <img 
                            v-if="getCoverImage(report)" 
                            :src="getCoverImage(report)" 
                            class="w-full h-full object-cover transition duration-700 group-hover:scale-110"
                            alt="Bukti Kerusakan"
                        />

                        <div v-else class="w-full h-full flex flex-col items-center justify-center bg-gray-200 text-gray-400">
                            <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-xs font-medium">No Image</span>
                        </div>

                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <Link :href="route('reports.show', report.id)" class="text-white text-xs font-bold border border-white px-4 py-2 rounded-full hover:bg-white hover:text-black transition">
                                Lihat Detail
                            </Link>
                        </div>
                    </div>

                    <div class="flex-grow flex flex-col justify-between py-1">
                        <h2 class="text-2xl font-bold text-[#1C1917] mb-3 leading-tight">
                            {{ report.title }}
                        </h2>
                        <div class="space-y-2 mb-4">
                            <div class="flex items-start gap-3">
                                <div class="w-5 flex justify-center mt-0.5"><svg class="w-5 h-5 text-[#F54900]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg></div>
                                <span class="text-gray-600 text-sm md:text-base">{{ report.location }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-5 flex justify-center"><svg class="w-5 h-5 text-[#BB4D00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>
                                <span class="text-gray-500 text-sm md:text-base">{{ report.date }}</span>
                            </div>
                        </div>

                        <div class="mt-auto">
                            <div class="flex justify-between text-xs text-[#787676] mb-1.5">
                                <span>Progress Pekerjaan</span>
                                <span class="text-[#F54900] font-bold">{{ getVisualProgress(report) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-[#F54900] h-full rounded-full transition-all duration-500" :style="{ width: getVisualProgress(report) + '%' }"></div>
                            </div>
                        </div>
                    </div>

                    <div class="w-full md:w-auto flex flex-col justify-between items-end min-w-[140px] pl-4 border-l border-gray-50/0 md:border-gray-50">
                        <Link :href="route('reports.show', report.id)" class="text-gray-400 hover:text-[#F54900] transition p-2 -mr-2 mb-4">
                            <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </Link>
                        <div class="w-full md:w-auto flex flex-col justify-between items-end min-w-[160px] pl-4 border-l border-gray-50/0 md:border-gray-50">                            
                            <div class="text-right mt-auto">
                                <p class="text-[10px] font-bold uppercase tracking-widest" 
                                :class="report.has_final_price ? 'text-[#09A600]' : 'text-[#787676]'">
                                    {{ report.has_final_price ? 'Biaya Fix' : 'Estimasi Biaya' }}
                                </p>

                                <p class="text-xl md:text-2xl font-black text-[#973C00] mb-1">
                                    {{ report.price }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>