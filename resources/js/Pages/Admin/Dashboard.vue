<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    reports: Array,
    auth: Object
});

// --- STATE ---
const searchQuery = ref('');
const activeTab = ref('Semua');
const tabs = ['Semua', 'Menunggu Verifikasi', 'Menunggu Pembayaran', 'Dalam Pengerjaan', 'Selesai', 'Dibatalkan'];

// --- LOGIC STATUS & WARNA ---
const getStatusData = (status) => {
    const map = {
        'verification': { label: 'Menunggu Verifikasi', class: 'bg-[#FF5722] text-white', tab: 'Menunggu Verifikasi' },
        'pending':      { label: 'Menunggu Pembayaran', class: 'bg-[#CA8E31] text-white', tab: 'Menunggu Pembayaran' },
        'process':      { label: 'Dalam Pengerjaan',    class: 'bg-[#4688FB] text-white', tab: 'Dalam Pengerjaan' },
        'completed':    { label: 'Selesai',             class: 'bg-[#09A600] text-white', tab: 'Selesai' },
        'cancelled':    { label: 'Dibatalkan',          class: 'bg-red-500 text-white',   tab: 'Dibatalkan' },
    };
    return map[status] || map['verification'];
};

// --- LOGIC GANTI STATUS (ADMIN ACTION) ---
const changeStatus = (id, newStatus) => {
    if (confirm(`Ubah status laporan REQ-${id} menjadi ${newStatus}?`)) {
        router.patch(route('admin.updateStatus', id), {
            status: newStatus
        }, {
            preserveScroll: true
        });
    }
};

// --- FILTERING ---
const filteredReports = computed(() => {
    let data = props.reports;

    // Filter Tab
    if (activeTab.value !== 'Semua') {
        data = data.filter(r => getStatusData(r.status).tab === activeTab.value);
    }

    // Filter Search
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        data = data.filter(r => 
            r.title.toLowerCase().includes(q) || 
            r.user_name.toLowerCase().includes(q) ||
            r.id.toString().includes(q)
        );
    }
    return data;
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <div class="min-h-screen bg-[#FFFDF8] font-['Montserrat']">
        
        <nav class="absolute top-0 w-full flex justify-between items-center px-8 py-6 z-20">
            <div class="text-white font-bold text-xl tracking-wide opacity-0">.</div> <Link :href="route('logout')" method="post" as="button" class="flex items-center gap-2 text-white hover:text-gray-200 transition bg-white/20 px-4 py-2 rounded-full backdrop-blur-sm">
                <span class="font-semibold">Logout</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </Link>
        </nav>

        <div class="w-full bg-[linear-gradient(to_bottom,#A0522D,#D2691E)] pt-32 pb-24 px-6 relative shadow-lg">
            <div class="max-w-6xl mx-auto relative z-10 flex justify-between items-end">
                <div>
                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-3">Selamat Datang, Admin!</h1>
                    <p class="text-white/90 text-lg">Kelola pengajuan, verifikasi kerusakan, dan pantau progres pemulihan rumah dalam satu dashboard.</p>
                </div>
                <div class="hidden md:block text-white/80">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-6 -mt-8 relative z-20">
            <div class="relative shadow-md rounded-xl">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input v-model="searchQuery" type="text" placeholder="Cari nama client, nomor pengajuan..." class="w-full py-4 pl-12 pr-4 rounded-xl border-none focus:ring-2 focus:ring-[#BB4D00] text-gray-700 bg-white">
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-6 mt-8">
            <div class="flex flex-wrap gap-2 border-b border-gray-200 pb-2">
                <button 
                    v-for="tab in tabs" :key="tab" 
                    @click="activeTab = tab"
                    class="px-4 py-2 text-sm font-semibold transition-all rounded-t-lg"
                    :class="activeTab === tab ? 'text-[#BB4D00] border-b-2 border-[#BB4D00] bg-orange-50' : 'text-gray-500 hover:text-[#BB4D00]'"
                >
                    {{ tab }}
                </button>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-6 py-8 space-y-6">
            
            <div v-if="filteredReports.length === 0" class="text-center py-20 text-gray-400">
                <p>Tidak ada laporan ditemukan.</p>
            </div>

            <div v-for="report in filteredReports" :key="report.id" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row gap-6 hover:shadow-md transition">
                
                <div class="w-full md:w-64 h-48 bg-gray-100 rounded-xl flex-shrink-0 overflow-hidden flex items-center justify-center relative group">
                    <div class="text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                        <span class="text-xs text-gray-500 block">Bukti di Google Drive</span>
                        <a v-if="report.drive_link" :href="report.drive_link" target="_blank" class="mt-2 inline-block px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">Buka</a>
                    </div>
                </div>

                <div class="flex-grow flex flex-col">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-[#BB4D00] font-bold flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            Pengajuan REQ-{{ report.id }}
                        </span>
                        
                        <div class="relative group">
                            <button :class="`${getStatusData(report.status).class} px-4 py-1.5 rounded-lg text-sm font-bold shadow-sm flex items-center gap-2`">
                                {{ getStatusData(report.status).label }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div class="absolute right-0 mt-1 w-48 bg-white rounded-lg shadow-xl border border-gray-100 overflow-hidden hidden group-hover:block z-50">
                                <div class="p-2 text-xs text-gray-400 font-semibold uppercase tracking-wider">Ubah Status</div>
                                <button @click="changeStatus(report.id, 'verification')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Menunggu Verifikasi</button>
                                <button @click="changeStatus(report.id, 'pending')" class="block w-full text-left px-4 py-2 text-sm text-[#CA8E31] hover:bg-orange-50">Menunggu Pembayaran</button>
                                <button @click="changeStatus(report.id, 'process')" class="block w-full text-left px-4 py-2 text-sm text-[#4688FB] hover:bg-blue-50">Dalam Pengerjaan</button>
                                <button @click="changeStatus(report.id, 'completed')" class="block w-full text-left px-4 py-2 text-sm text-[#09A600] hover:bg-green-50">Selesai</button>
                                <button @click="changeStatus(report.id, 'cancelled')" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Batalkan</button>
                            </div>
                        </div>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ report.title }}</h2>
                    
                    <div class="space-y-1 mb-4">
                        <div class="flex items-center gap-2 text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span class="font-medium">Client: {{ report.user_name }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-500 text-sm">
                            <svg class="w-4 h-4 text-[#F54900]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            {{ report.location }}
                        </div>
                        <div class="flex items-center gap-2 text-gray-500 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ report.date }}
                        </div>
                    </div>

                    <div class="mt-auto flex justify-between items-end border-t pt-4">
                        <div class="w-1/2">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>Progress Pekerjaan</span>
                                <span class="text-[#F54900] font-bold">{{ report.progress }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-[#F54900] h-1.5 rounded-full" :style="{ width: report.progress + '%' }"></div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-400">Total Biaya</p>
                            <p class="text-xl font-bold text-[#BB4D00]">{{ report.price }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>