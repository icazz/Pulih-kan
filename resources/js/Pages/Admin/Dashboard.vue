<script setup>
import { Head, Link, router } from '@inertiajs/vue3'; // Hapus useForm karena tidak dipakai lagi di dashboard
import { ref, computed, watch, nextTick } from 'vue';

// --- LEAFLET SETUP ---
import "leaflet/dist/leaflet.css";
import L from "leaflet";
// Fix Icon Leaflet di Vue
import icon from 'leaflet/dist/images/marker-icon.png';
import iconShadow from 'leaflet/dist/images/marker-shadow.png';

let DefaultIcon = L.icon({
    iconUrl: icon,
    shadowUrl: iconShadow,
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});
L.Marker.prototype.options.icon = DefaultIcon;

const props = defineProps({
    reports: Array,
    vendors: Array,
    auth: Object
});

// --- STATE ---
const searchQuery = ref('');
const activeTab = ref('Laporan User'); 
const tabs = ['Laporan User', 'Verifikasi Vendor'];

// Report Tabs
const reportTabs = ['Semua', 'Menunggu Verifikasi', 'Menunggu Pembayaran', 'Dalam Pengerjaan', 'Selesai', 'Dibatalkan'];
const activeReportTab = ref('Semua');

// Vendor Tabs
const vendorTabs = ['Menunggu', 'Terverifikasi', 'Ditolak'];
const activeVendorTab = ref('Menunggu');

// --- STATE VENDOR (TETAP) ---
const showVendorModal = ref(false); // Modal Vendor Tetap Ada
const selectedVendor = ref(null);

// Map Instance (Reusable)
let map = null;

// --- LOGIC MAPS (Reusable) ---
const initMap = (elementId, lat, lng) => {
    if (map) {
        map.remove();
        map = null;
    }

    const latitude = parseFloat(lat) || -6.175392;
    const longitude = parseFloat(lng) || 106.827153;

    map = L.map(elementId).setView([latitude, longitude], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.marker([latitude, longitude]).addTo(map)
        .bindPopup("Lokasi Terpilih")
        .openPopup();
};

// --- LOGIC VENDOR (TIDAK DISENTUH) ---
const openVendorDetail = (vendor) => {
    selectedVendor.value = vendor;
    showVendorModal.value = true;
};

watch(showVendorModal, (val) => {
    if (val && selectedVendor.value) {
        nextTick(() => {
            initMap('vendorMap', selectedVendor.value.latitude, selectedVendor.value.longitude);
        });
    }
});

const verifyVendor = (vendorId, status) => {
    const action = status === 'approve' ? 'MENYETUJUI' : 'MENOLAK';
    if (!confirm(`Konfirmasi: Apakah Anda yakin ingin ${action} mitra ini?`)) return;

    router.patch(`/admin/vendors/${vendorId}/verify`, { status }, {
        onSuccess: () => {
            showVendorModal.value = false;
            selectedVendor.value = null;
        },
        preserveScroll: true
    });
};

// --- LOGIC REPORT (Hanya Helper Status) ---
const getStatusData = (status) => {
    const map = {
        'verification': { label: 'Perlu Verifikasi', class: 'bg-red-100 text-red-600 border-red-200', tab: 'Menunggu Verifikasi' },
        'pending':      { label: 'Menunggu Pembayaran', class: 'bg-orange-100 text-orange-600 border-orange-200', tab: 'Menunggu Pembayaran' },
        'process':      { label: 'Dalam Pengerjaan',    class: 'bg-blue-100 text-blue-600 border-blue-200', tab: 'Dalam Pengerjaan' },
        'completed':    { label: 'Selesai',             class: 'bg-green-100 text-green-600 border-green-200', tab: 'Selesai' },
        'cancelled':    { label: 'Dibatalkan',          class: 'bg-gray-100 text-gray-600 border-gray-200',    tab: 'Dibatalkan' },
    };
    return map[status] || map['verification'];
};

// --- FILTERING ---
const filteredReports = computed(() => {
    let data = props.reports;
    if (activeReportTab.value !== 'Semua') {
        data = data.filter(r => getStatusData(r.status).tab === activeReportTab.value);
    }
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        data = data.filter(r => r.title.toLowerCase().includes(q) || r.user_name.toLowerCase().includes(q));
    }
    return data;
});

const filteredVendors = computed(() => {
    let data = props.vendors || [];
    
    if (activeVendorTab.value === 'Menunggu') {
        data = data.filter(v => v.status === 'pending' || (!v.status && !v.is_verified)); 
    } else if (activeVendorTab.value === 'Terverifikasi') {
        data = data.filter(v => v.status === 'verified' || v.is_verified);
    } else if (activeVendorTab.value === 'Ditolak') {
        data = data.filter(v => v.status === 'rejected');
    }

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        data = data.filter(v => v.nama_mitra.toLowerCase().includes(q) || v.email.toLowerCase().includes(q));
    }
    return data;
});

const getVendorStatusBadge = (vendor) => {
    if (vendor.status === 'verified' || vendor.is_verified) return { label: 'Terverifikasi', class: 'bg-green-100 text-green-700' };
    if (vendor.status === 'rejected') return { label: 'Ditolak', class: 'bg-red-100 text-red-700' };
    return { label: 'Menunggu', class: 'bg-yellow-100 text-yellow-700' };
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <div class="min-h-screen bg-[#FFFDF8] font-['Montserrat'] relative">
        <nav class="absolute top-0 w-full flex justify-between items-center px-8 py-6 z-20">
            <div class="text-white font-bold text-xl uppercase tracking-wider">Pulih.kan Admin</div>
            <Link :href="route('logout')" method="post" as="button" class="text-white hover:text-gray-200 transition bg-white/20 px-6 py-2 rounded-full backdrop-blur-md font-semibold border border-white/30">
                Logout
            </Link>
        </nav>

        <div class="w-full bg-[linear-gradient(to_bottom,#A0522D,#D2691E)] pt-32 pb-24 px-6 shadow-lg">
            <div class="max-w-6xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-3">Panel Kendali Admin</h1>
                <p class="text-white/90 text-lg">Kelola laporan kerusakan dan verifikasi kemitraan vendor.</p>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-6 mt-8 relative z-20">
            <input v-model="searchQuery" type="text" :placeholder="activeTab === 'Laporan User' ? 'Cari ID Laporan atau Nama Klien...' : 'Cari Nama Mitra...'" class="w-full py-4 px-6 rounded-xl shadow-xl border-none focus:ring-2 focus:ring-[#BB4D00] text-gray-700 bg-white">
        </div>

        <div class="max-w-6xl mx-auto px-6 mt-10">
            <div class="flex gap-8 border-b border-gray-200">
                <button v-for="tab in tabs" :key="tab" @click="activeTab = tab" class="pb-4 text-lg font-bold transition-all relative" :class="activeTab === tab ? 'text-[#BB4D00]' : 'text-gray-400 hover:text-gray-600'">
                    {{ tab }}
                    <div v-if="activeTab === tab" class="absolute bottom-0 left-0 w-full h-1 bg-[#BB4D00] rounded-full"></div>
                </button>
            </div>
        </div>

        <div v-if="activeTab === 'Laporan User'" class="max-w-6xl mx-auto px-6 py-8">
            <div class="flex gap-2 overflow-x-auto mb-6 pb-2">
                <button v-for="tab in reportTabs" :key="tab" @click="activeReportTab = tab" 
                    class="px-4 py-2 text-sm font-semibold rounded-full border transition-all whitespace-nowrap"
                    :class="activeReportTab === tab ? 'bg-[#BB4D00] text-white border-[#BB4D00]' : 'bg-white text-gray-500 border-gray-200 hover:border-[#BB4D00]'">
                    {{ tab }}
                </button>
            </div>

            <div v-if="filteredReports.length === 0" class="text-center py-20 text-gray-400 bg-white rounded-2xl border border-dashed border-gray-300">Tidak ada laporan ditemukan.</div>
            
            <div class="grid gap-4">
                <Link 
                    v-for="report in filteredReports" 
                    :key="report.id" 
                    :href="route('admin.reports.show', report.id)" 
                    class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between hover:shadow-md hover:border-orange-300 transition cursor-pointer group"
                >
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center text-[#BB4D00] group-hover:bg-[#BB4D00] group-hover:text-white transition shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-lg group-hover:text-[#BB4D00] transition">{{ report.title }}</h3>
                            <p class="text-sm text-gray-500">Client: {{ report.user_name }} • ID: REQ-{{ report.id }}</p>
                        </div>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        <span :class="`px-3 py-1 rounded-full text-xs font-bold border ${getStatusData(report.status).class}`">
                            {{ getStatusData(report.status).label }}
                        </span>
                        <span class="text-xs text-gray-400">{{ report.date }}</span>
                    </div>
                </Link>
            </div>
        </div>

        <div v-if="activeTab === 'Verifikasi Vendor'" class="max-w-6xl mx-auto px-6 py-8">
            <div class="flex gap-2 overflow-x-auto mb-6 pb-2">
                <button v-for="tab in vendorTabs" :key="tab" @click="activeVendorTab = tab" 
                    class="px-4 py-2 text-sm font-semibold rounded-full border transition-all whitespace-nowrap"
                    :class="activeVendorTab === tab ? 'bg-[#BB4D00] text-white border-[#BB4D00]' : 'bg-white text-gray-500 border-gray-200 hover:border-[#BB4D00]'">
                    {{ tab }}
                </button>
            </div>

            <div v-if="filteredVendors.length === 0" class="text-center py-20 text-gray-400 bg-white rounded-2xl border border-dashed border-gray-300">Tidak ada pengajuan vendor baru.</div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div v-for="vendor in filteredVendors" :key="vendor.id" @click="openVendorDetail(vendor)" class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:border-orange-300 transition-all cursor-pointer relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-2">
                        <span :class="`text-[10px] font-black px-2 py-1 rounded-bl-xl uppercase tracking-tighter ${getVendorStatusBadge(vendor).class}`">
                            {{ getVendorStatusBadge(vendor).label }}
                        </span>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-orange-50 flex items-center justify-center text-[#BB4D00] font-bold text-xl shadow-sm">
                            {{ vendor.nama_mitra.charAt(0) }}
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800 text-lg group-hover:text-[#BB4D00] transition">{{ vendor.nama_mitra }}</h3>
                            <p class="text-xs text-gray-500 mb-2">{{ vendor.email }}</p>
                            <div class="flex flex-wrap gap-1">
                                <span v-for="(jasa, idx) in vendor.jenis_jasa.slice(0,3)" :key="idx" class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded">
                                    {{ jasa }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-50 flex justify-between items-center">
                        <span class="text-xs text-gray-400 italic">{{ vendor.kota }}</span>
                        <span class="text-[#BB4D00] font-bold text-xs flex items-center gap-1">Detail & Peta <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"></path></svg></span>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showVendorModal && selectedVendor" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="bg-white w-full max-w-5xl rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row max-h-[90vh]">
                
                <div class="w-full md:w-1/2 bg-gray-100 relative min-h-[300px] md:h-auto">
                    <div id="vendorMap" class="w-full h-full z-0"></div>
                    <div class="absolute bottom-4 left-4 bg-white/90 backdrop-blur px-4 py-2 rounded-lg shadow-lg border border-white/50 z-[400] text-xs">
                        <span class="font-bold text-gray-500 block">KOORDINAT</span>
                        <span class="font-mono text-gray-800">{{ selectedVendor.latitude }}, {{ selectedVendor.longitude }}</span>
                    </div>
                </div>

                <div class="w-full md:w-1/2 flex flex-col bg-white h-full">
                    <div class="bg-[#4F3726] p-6 text-white flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold">Verifikasi Kemitraan</h2>
                            <p class="text-white/70 text-sm">Tinjau kelengkapan data sebelum menyetujui.</p>
                        </div>
                        <button @click="showVendorModal = false" class="text-white/50 hover:text-white text-3xl">&times;</button>
                    </div>

                    <div class="p-8 overflow-y-auto flex-1 space-y-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Mitra</label>
                                <p class="font-bold text-gray-800 text-lg">{{ selectedVendor.nama_mitra }}</p>
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Kontak</label>
                                <p class="font-bold text-gray-800">{{ selectedVendor.no_telepon }}</p>
                                <p class="text-xs text-gray-500">{{ selectedVendor.email }}</p>
                            </div>
                            <div class="col-span-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Alamat Lengkap</label>
                                <p class="text-gray-700 text-sm leading-relaxed bg-gray-50 p-3 rounded-lg border border-gray-100">
                                    {{ selectedVendor.alamat }}<br>
                                    <span class="font-semibold text-gray-500">{{ selectedVendor.kota }}, {{ selectedVendor.provinsi }}</span>
                                </p>
                            </div>
                            <div class="col-span-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Jasa / Layanan</label>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span v-for="jasa in selectedVendor.jenis_jasa" :key="jasa" class="bg-orange-50 text-[#BB4D00] border border-orange-100 px-3 py-1 rounded-full text-xs font-bold">
                                        {{ jasa }}
                                    </span>
                                </div>
                                <p v-if="selectedVendor.jasa_lainnya" class="mt-2 text-xs text-gray-500 italic border-l-2 border-orange-200 pl-2">
                                    Note: "{{ selectedVendor.jasa_lainnya }}"
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 border-t border-gray-100 bg-gray-50 flex gap-3" 
                        v-if="selectedVendor.status === 'pending' || (!selectedVendor.status && !selectedVendor.is_verified)">
                        
                        <button @click="verifyVendor(selectedVendor.id, 'reject')" class="px-6 py-3 rounded-xl font-bold text-red-600 bg-white border border-red-200 hover:bg-red-50 transition">
                            Tolak
                        </button>
                        
                        <button @click="verifyVendor(selectedVendor.id, 'approve')" class="flex-1 py-3 rounded-xl font-bold text-white bg-green-600 hover:bg-green-700 transition shadow-lg shadow-green-200 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Setujui & Aktifkan Akun
                        </button>
                    </div>

                    <div class="p-6 border-t border-gray-100 bg-gray-50 text-center" v-else>
                        <p class="text-gray-500 font-medium">
                            Mitra ini telah diproses dengan status: 
                            <span class="font-bold uppercase" :class="selectedVendor.status === 'verified' || selectedVendor.is_verified ? 'text-green-600' : 'text-red-600'">
                                {{ selectedVendor.status === 'verified' || selectedVendor.is_verified ? 'TERVERIFIKASI' : 'DITOLAK' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<style scoped>
.leaflet-pane { z-index: 0; }
</style>