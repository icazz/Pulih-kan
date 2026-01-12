<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    reports: Array,
    auth: Object
});

// --- STATE ---
const searchQuery = ref('');
const activeTab = ref('Semua');
const tabs = ['Semua', 'Menunggu Verifikasi', 'Menunggu Pembayaran', 'Dalam Pengerjaan', 'Selesai', 'Dibatalkan'];
const showModal = ref(false);
const selectedReport = ref(null);

// Form khusus untuk input data admin
const form = useForm({
    action: '', // verify, confirm_payment, complete, cancel
    price: '',
    category: '',
});

// --- LOGIC STATUS & WARNA ---
const getStatusData = (status) => {
    const map = {
        'verification': { label: 'Perlu Verifikasi', class: 'bg-red-100 text-red-600 border-red-200', tab: 'Menunggu Verifikasi' },
        'pending':      { label: 'Menunggu Pembayaran', class: 'bg-orange-100 text-orange-600 border-orange-200', tab: 'Menunggu Pembayaran' },
        'process':      { label: 'Dalam Pengerjaan',    class: 'bg-blue-100 text-blue-600 border-blue-200', tab: 'Dalam Pengerjaan' },
        'completed':    { label: 'Selesai',             class: 'bg-green-100 text-green-600 border-green-200', tab: 'Selesai' },
        'cancelled':    { label: 'Dibatalkan',          class: 'bg-gray-100 text-gray-600 border-gray-200',   tab: 'Dibatalkan' },
    };
    return map[status] || map['verification'];
};

// --- MODAL LOGIC ---
const openDetail = (report) => {
    selectedReport.value = report;
    // Reset form value
    form.price = report.price_raw || '';
    form.category = report.category !== '-' ? report.category : '';
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedReport.value = null;
    form.reset();
};

const submitAction = (action) => {
    if (!selectedReport.value) return; // Pastikan ada laporan yang dipilih
    if (!confirm('Apakah Anda yakin ingin memproses tindakan ini?')) return;

    form.action = action;

    // KITA GUNAKAN URL MANUAL AGAR PASTI BENAR (Hapus route() helper)
    // Format: /admin/reports/{id}/status
    const url = `/admin/reports/${selectedReport.value.id}/status`;

    form.patch(url, {
        onSuccess: () => {
            closeModal();
            // Opsional: alert('Berhasil!');
        },
        onError: (errors) => {
            console.error(errors);
            alert('Terjadi kesalahan saat menyimpan.');
        },
        preserveScroll: true
    });
};

// --- FILTERING ---
const filteredReports = computed(() => {
    let data = props.reports;
    if (activeTab.value !== 'Semua') {
        data = data.filter(r => getStatusData(r.status).tab === activeTab.value);
    }
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        data = data.filter(r => r.title.toLowerCase().includes(q) || r.user_name.toLowerCase().includes(q) || r.id.toString().includes(q));
    }
    return data;
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <div class="min-h-screen bg-[#FFFDF8] font-['Montserrat'] relative">
        
        <nav class="absolute top-0 w-full flex justify-between items-center px-8 py-6 z-20">
            <div class="text-white font-bold text-xl">PULIH.KAN ADMIN</div>
            <Link :href="route('logout')" method="post" as="button" class="flex items-center gap-2 text-white hover:text-gray-200 transition bg-white/20 px-4 py-2 rounded-full backdrop-blur-sm">
                <span class="font-semibold">Logout</span>
            </Link>
        </nav>

        <div class="w-full bg-[linear-gradient(to_bottom,#A0522D,#D2691E)] pt-32 pb-24 px-6 relative shadow-lg">
            <div class="max-w-6xl mx-auto relative z-10">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-3">Selamat Datang, Admin!</h1>
                <p class="text-white/90 text-lg">Kelola pengajuan dan pantau progres pemulihan.</p>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-6 -mt-8 relative z-20">
            <div class="relative shadow-md rounded-xl">
                <input v-model="searchQuery" type="text" placeholder="Cari nama client atau ID..." class="w-full py-4 pl-6 pr-4 rounded-xl border-none focus:ring-2 focus:ring-[#BB4D00] text-gray-700 bg-white">
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-6 mt-8 overflow-x-auto">
            <div class="flex gap-2 border-b border-gray-200 pb-2 min-w-max">
                <button v-for="tab in tabs" :key="tab" @click="activeTab = tab" class="px-4 py-2 text-sm font-semibold transition-all rounded-t-lg" :class="activeTab === tab ? 'text-[#BB4D00] border-b-2 border-[#BB4D00] bg-orange-50' : 'text-gray-500 hover:text-[#BB4D00]'">{{ tab }}</button>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-6 py-8 space-y-4">
            <div v-if="filteredReports.length === 0" class="text-center py-20 text-gray-400">Tidak ada laporan.</div>

            <div v-for="report in filteredReports" :key="report.id" @click="openDetail(report)" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between hover:shadow-md transition cursor-pointer group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-[#BB4D00] group-hover:text-white transition">
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
            </div>
        </div>

        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                
                <div class="bg-[#28160A] p-6 flex justify-between items-start sticky top-0 z-10">
                    <div>
                        <h2 class="text-xl font-bold text-white">Detail Pengajuan</h2>
                        <p class="text-white/70 text-sm">REQ-{{ selectedReport.id }} • {{ selectedReport.user_name }}</p>
                    </div>
                    <button @click="closeModal" class="text-white/70 hover:text-white text-2xl">&times;</button>
                </div>

                <div class="p-6 space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <label class="text-xs text-gray-400 font-bold uppercase">Judul</label>
                            <p class="text-gray-800 font-semibold">{{ selectedReport.title }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <label class="text-xs text-gray-400 font-bold uppercase">Lokasi</label>
                            <p class="text-gray-800 text-sm">{{ selectedReport.location }}</p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="text-xs text-gray-400 font-bold uppercase">Deskripsi Kerusakan</label>
                        <p class="text-gray-700 bg-gray-50 p-4 rounded-xl border border-gray-100 mt-1 text-sm leading-relaxed">{{ selectedReport.description }}</p>
                    </div>

                    <div>
                        <label class="text-xs text-gray-400 font-bold uppercase">Bukti Foto/Video</label>
                        <a :href="selectedReport.drive_link" target="_blank" class="flex items-center gap-2 mt-1 text-blue-600 hover:underline font-medium p-3 bg-blue-50 rounded-xl border border-blue-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            Buka Link Google Drive
                        </a>
                    </div>

                    <hr class="border-gray-200">

                    <div>
                        <h3 class="font-bold text-[#BB4D00] text-lg mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Tindakan Admin
                        </h3>

                        <div v-if="selectedReport.status === 'verification'" class="bg-orange-50 p-5 rounded-xl border border-orange-200">
                            <p class="text-sm text-orange-800 mb-4 font-medium">Silakan lakukan penilaian awal untuk pengajuan ini.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Kategori Kerusakan</label>
                                    <select v-model="form.category" class="w-full rounded-lg border-gray-300 focus:ring-[#BB4D00]">
                                        <option value="" disabled>Pilih Kategori</option>
                                        <option value="Ringan">Ringan</option>
                                        <option value="Sedang">Sedang</option>
                                        <option value="Berat">Berat</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Estimasi Biaya (Rp)</label>
                                    <input v-model="form.price" type="number" placeholder="Contoh: 5000000" class="w-full rounded-lg border-gray-300 focus:ring-[#BB4D00]">
                                </div>
                            </div>
                            
                            <div class="flex gap-2">
                                <button @click="submitAction('verify')" :disabled="form.processing || !form.price || !form.category" class="flex-1 bg-[#BB4D00] text-white py-3 rounded-lg font-bold hover:bg-[#973C00] transition disabled:opacity-50">
                                    Verifikasi & Kirim Penawaran
                                </button>
                                <button @click="submitAction('cancel')" class="px-4 py-3 bg-red-100 text-red-600 rounded-lg font-bold hover:bg-red-200 transition">Tolak</button>
                            </div>
                        </div>

                        <div v-else-if="selectedReport.status === 'pending'" class="bg-blue-50 p-5 rounded-xl border border-blue-200 text-center">
                            <p class="text-blue-800 font-bold text-lg mb-2">Rp {{ parseInt(form.price).toLocaleString('id-ID') }}</p>
                            <p class="text-sm text-blue-600 mb-4">User telah menerima penawaran. Verifikasi jika pembayaran sudah diterima.</p>
                            <button @click="submitAction('confirm_payment')" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition">
                                Konfirmasi Pembayaran Diterima
                            </button>
                        </div>

                        <div v-else-if="selectedReport.status === 'process'" class="bg-green-50 p-5 rounded-xl border border-green-200 text-center">
                            <p class="text-sm text-green-700 mb-4">Proyek sedang berjalan. Tandai selesai jika vendor sudah menyelesaikan pekerjaan.</p>
                            <button @click="submitAction('complete')" class="w-full bg-green-600 text-white py-3 rounded-lg font-bold hover:bg-green-700 transition">
                                Tandai Pesanan Selesai
                            </button>
                        </div>

                        <div v-else class="text-center p-4 bg-gray-50 rounded-xl text-gray-500">
                            <p class="font-bold">Laporan ini telah selesai atau dibatalkan.</p>
                            <p class="text-sm mt-1">Tidak ada tindakan lebih lanjut diperlukan.</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</template>