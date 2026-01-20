<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    reports: Array,
    vendors: Array,
    volunteers: Array,
    donations: Array,
    auth: Object
});

const searchQuery = ref('');
const activeTab = ref('Laporan User'); 
const tabs = ['Laporan User', 'Verifikasi Vendor', 'Relawan', 'Donasi'];

// Logic Modal Gambar Bukti
const showProofModal = ref(false);
const selectedProofUrl = ref('');
const openProof = (url) => {
    selectedProofUrl.value = url;
    showProofModal.value = true;
};

// Logic Update Donasi
const updateDonationStatus = (id, status) => {
    if(confirm(`Ubah status menjadi ${status}?`)) {
        // Menggunakan URL manual untuk menghindari error Ziggy
        router.patch(`/admin/donations/${id}`, { status }, {
            preserveScroll: true,
            onSuccess: () => {
                alert('Status donasi berhasil diperbarui!');
            },
            onError: (err) => {
                console.error("Gagal update donasi:", err);
            }
        });
    }
};

// Helper Format Rupiah
const formatRupiah = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(val);

const volunteerTabs = ['Menunggu', 'Terverifikasi', 'Ditolak'];
const activeVolunteerTab = ref('Menunggu');

const getVolunteerBadge = (status) => {
    if (status === 'verified') return { label: 'Terverifikasi', class: 'bg-green-100 text-green-700' };
    if (status === 'rejected') return { label: 'Ditolak', class: 'bg-red-100 text-red-700' };
    return { label: 'Menunggu', class: 'bg-yellow-100 text-yellow-700' };
};

const goToVolunteerDetail = (id) => {
    router.visit(route('admin.volunteers.show', id));
};

const filteredVolunteers = computed(() => {
    let data = props.volunteers || [];
    
    // Filter Tab Status
    if (activeVolunteerTab.value === 'Menunggu') {
        data = data.filter(v => v.status === 'pending');
    } else if (activeVolunteerTab.value === 'Terverifikasi') {
        data = data.filter(v => v.status === 'verified');
    } else if (activeVolunteerTab.value === 'Ditolak') {
        data = data.filter(v => v.status === 'rejected');
    }

    // Filter Search
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        data = data.filter(v => v.name.toLowerCase().includes(q) || v.email.toLowerCase().includes(q) || v.role.toLowerCase().includes(q));
    }
    return data;
});

// 4. LOGIC MODAL DETAIL RELAWAN (Paste di bagian bawah script)
const showVolunteerModal = ref(false);
const selectedVolunteer = ref(null);

const openVolunteerModal = (volunteer) => {
    selectedVolunteer.value = volunteer;
    showVolunteerModal.value = true;
};

const closeVolunteerModal = () => {
    showVolunteerModal.value = false;
    selectedVolunteer.value = null;
};

const updateVolunteerStatus = (status) => {
    if (!selectedVolunteer.value) return;
    if (confirm(`Ubah status relawan menjadi ${status}?`)) {
        router.patch(route('admin.volunteers.update', selectedVolunteer.value.id), { status }, {
            onSuccess: () => closeVolunteerModal()
        });
    }
};

// Report Tabs
const reportTabs = ['Semua', 'Menunggu Verifikasi', 'Menunggu Pembayaran', 'Dalam Pengerjaan', 'Selesai', 'Dibatalkan'];
const activeReportTab = ref('Semua');

// Vendor Tabs
const vendorTabs = ['Menunggu', 'Terverifikasi', 'Ditolak'];
const activeVendorTab = ref('Menunggu');

// --- HELPER STATUS ---
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

const getVendorStatusBadge = (vendor) => {
    if (vendor.status === 'verified' || vendor.is_verified) return { label: 'Terverifikasi', class: 'bg-green-100 text-green-700' };
    if (vendor.status === 'rejected') return { label: 'Ditolak', class: 'bg-red-100 text-red-700' };
    return { label: 'Menunggu', class: 'bg-yellow-100 text-yellow-700' };
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

// FUNGSI BARU: Pindah ke Halaman Detail Vendor
const goToVendorDetail = (vendorId) => {
    // Pastikan di web.php route ini ada
    router.visit(route('admin.vendors.show', vendorId));
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
                <div v-for="vendor in filteredVendors" :key="vendor.id" @click="goToVendorDetail(vendor.id)" class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:border-orange-300 transition-all cursor-pointer relative overflow-hidden group">
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
        <div v-if="activeTab === 'Relawan'" class="max-w-7xl mx-auto px-6 py-8">

            <div v-if="filteredVolunteers.length === 0" class="text-center py-20 text-gray-400 bg-white rounded-2xl border border-dashed border-gray-300">
                Tidak ada data relawan {{ activeVolunteerTab.toLowerCase() }}.
            </div>

            <div v-else class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500 font-bold">
                                <th class="p-4 min-w-[250px]">Profil Relawan</th>
                                <th class="p-4 min-w-[200px]">Kontak</th>
                                <th class="p-4 min-w-[200px]">Peran & Keahlian</th>
                                <th class="p-4 min-w-[250px]">Alamat Domisili</th>
                                <th class="p-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <tr v-for="vol in filteredVolunteers" :key="vol.id" class="hover:bg-orange-50/30 transition">
                                
                                <td class="p-4 align-top">
                                    <div class="flex gap-3">
                                        <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-[#BB4D00] font-bold shrink-0">
                                            {{ vol.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 text-base">{{ vol.name }}</p>
                                            <p class="text-gray-500">{{ vol.email }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="p-4 align-top">
                                    <div class="space-y-2">
                                        <div>
                                            <p class="text-[10px] text-gray-400 uppercase font-bold">Pribadi</p>
                                            <p class="font-medium text-gray-700">{{ vol.phone }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] text-gray-400 uppercase font-bold">Darurat</p>
                                            <p class="font-medium text-red-600">{{ vol.emergency_contact }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="p-4 align-top">
                                    <div class="mb-2">
                                        <span class="inline-block px-2 py-1 bg-orange-50 text-[#BB4D00] rounded text-xs font-bold border border-orange-100">
                                            {{ vol.role }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600 text-xs leading-relaxed max-w-[250px]">
                                        {{ vol.experience }}
                                    </p>
                                </td>

                                <td class="p-4 align-top">
                                    <p class="text-gray-800 font-medium mb-1">{{ vol.address }}</p>
                                    <p class="text-gray-500 text-xs">
                                        {{ vol.city }}, {{ vol.province }}
                                    </p>
                                </td>

                                <td class="p-4 align-top text-center">
                                    <span :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getVolunteerBadge(vol.status).class}`">
                                        {{ getVolunteerBadge(vol.status).label }}
                                    </span>
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div v-if="activeTab === 'Donasi'" class="max-w-6xl mx-auto px-6 py-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-bold border-b">
                        <tr>
                            <th class="p-4">Nama Donatur</th>
                            <th class="p-4">Jumlah</th>
                            <th class="p-4 text-center">Bukti</th>
                            <th class="p-4 text-center">Status</th>
                            <th class="p-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-sm">
                        <tr v-for="item in donations" :key="item.id" class="hover:bg-gray-50">
                            <td class="p-4 font-bold">{{ item.name }}<br><span class="text-xs text-gray-400 font-normal">{{ new Date(item.created_at).toLocaleDateString() }}</span></td>
                            <td class="p-4 font-bold text-[#BB4D00]">{{ formatRupiah(item.amount) }}</td>
                            <td class="p-4 text-center">
                                <button @click="openProof('/storage/' + item.proof_file)" class="text-blue-600 underline text-xs">Lihat Bukti</button>
                            </td>
                            <td class="p-4 text-center">
                                <span :class="`px-2 py-1 rounded text-xs font-bold ${item.status === 'verified' ? 'bg-green-100 text-green-700' : item.status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'}`">
                                    {{ item.status === 'verified' ? 'Diterima' : item.status === 'rejected' ? 'Ditolak' : 'Menunggu' }}
                                </span>
                            </td>
                            <td class="p-4 text-right flex justify-end gap-2">
                                <button v-if="item.status === 'pending'" @click="updateDonationStatus(item.id, 'verified')" class="bg-green-500 text-white p-2 rounded hover:bg-green-600" title="Terima">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                <button v-if="item.status === 'pending'" @click="updateDonationStatus(item.id, 'rejected')" class="bg-red-500 text-white p-2 rounded hover:bg-red-600" title="Tolak">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="showProofModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80 p-4" @click="showProofModal = false">
            <img :src="selectedProofUrl" class="max-w-full max-h-[90vh] rounded-lg shadow-2xl" />
        </div>
    </div>
</template>