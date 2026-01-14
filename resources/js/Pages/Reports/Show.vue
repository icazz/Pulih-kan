<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";
import { computed } from 'vue';

const props = defineProps({
    report: Object,
    auth: Object // Diperlukan untuk mengambil nama user saat chat WA
});

const getStatusBadge = (status) => {
    const map = {
        verification: { label: "Menunggu Verifikasi Admin", class: "bg-gray-500" },
        pending: { label: "Menunggu Pembayaran", class: "bg-[#CA8E31]" },
        process: { label: "Dalam Pengerjaan", class: "bg-[#4688FB]" },
        completed: { label: "Selesai", class: "bg-[#09A600]" },
        cancelled: { label: "Dibatalkan", class: "bg-red-500" },
    };
    return map[status] || map["verification"];
};

const statusInfo = getStatusBadge(props.report?.status || 'verification');

// Helper Format Uang
const formatCurrency = (value) => {
    const number = parseInt(value);
    if (isNaN(number)) return '0';
    return number.toLocaleString('id-ID');
};

// Helper Format Tanggal & Jam
const formatDate = (dateString) => {
    if (!dateString || dateString === '-' || dateString === 'Menunggu') return '-';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString; 
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const formatTime = (dateString) => {
    if (!dateString || dateString === '-' || dateString === 'Menunggu') return '';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '';
    return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
};

// --- LOGIKA DINAMIS VENDOR & CHAT ---

// 1. Link WhatsApp (Otomatis mengambil no_telepon vendor jika ada)
const whatsappLink = computed(() => {
    if (!props.report.vendor) return '#';
    
    let phone = props.report.vendor.no_telepon || '';
    // Format nomor 08... menjadi 628...
    if (phone.startsWith('0')) {
        phone = '62' + phone.slice(1);
    }
    
    const text = `Halo ${props.report.vendor.nama_mitra}, saya ${props.auth?.user?.name || 'Pelanggan'}. Saya ingin mendiskusikan biaya akhir dan kontrak untuk laporan REQ-${props.report.id}.`;
    
    return `https://wa.me/${phone}?text=${encodeURIComponent(text)}`;
});

// 2. Placeholder Upload Kontrak
const handleUploadContract = () => {
    alert("Fitur upload kontrak kerja akan segera tersedia.");
};

// 3. Placeholder Pembayaran
const handlePayment = () => {
    if(!props.report.final_price) return;
    alert("Mengarahkan ke halaman pembayaran...");
};

const timelineSteps = [
    {
        title: "Pengajuan & Verifikasi",
        raw_date: props.report.created_at, 
        desc: "Laporan sedang diperiksa admin.",
        status: props.report.status !== "verification" ? "completed" : "current",
        drive_link: null, 
    },
    {
        title: "Pembayaran & Kontrak",
        raw_date: (props.report.status !== 'verification' && props.report.status !== 'pending') ? props.report.updated_at : '-',
        desc: "Persetujuan biaya dan kontrak kerja.",
        status: props.report.status === "process" || props.report.status === "completed" ? "completed" : (props.report.status === "pending" ? "current" : "upcoming"),
        drive_link: null,
    },
    {
        title: "Pengerjaan & Survei",
        raw_date: "-", 
        desc: "Tim vendor melakukan perbaikan.",
        status: props.report.status === "process" || props.report.status === "completed" ? "current" : "upcoming",
        drive_link: props.report.drive_link,
    },
    {
        title: "Pekerjaan Selesai",
        raw_date: props.report.status === "completed" ? props.report.updated_at : "-",
        desc: "Seluruh pekerjaan telah diselesaikan.",
        status: props.report.status === "completed" ? "completed" : "upcoming",
        drive_link: null,
    },
];
</script>

<template>
    <Head title="Detail Laporan" />
    <Navbar />

    <div class="min-h-screen bg-[#FFFDF4] font-['Montserrat'] pb-20">
        <div class="w-full bg-[linear-gradient(to_right,#BB4D00,#4B741F)] pt-32 pb-16 px-6 relative overflow-hidden">
            <div class="max-w-6xl mx-auto relative z-10">
                <Link :href="route('reports.index')" class="inline-flex items-center text-white/90 hover:text-white mb-6 transition gap-2 group">
                    <svg class="w-6 h-6 transform group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span class="font-medium text-lg">Kembali ke Daftar Pengajuan</span>
                </Link>

                <div class="flex flex-col md:flex-row md:items-center gap-4 mb-3">
                    <h1 class="text-[#FFFFFF] text-4xl md:text-5xl font-bold tracking-tight">REQ-{{ props.report.id }}</h1>
                    <span :class="`${statusInfo.class} px-4 py-1.5 rounded-lg font-bold text-sm shadow-md border border-white/20 text-white`">{{ statusInfo.label }}</span>
                </div>
                <h2 class="text-xl md:text-2xl font-light text-white/90 max-w-3xl leading-relaxed">{{ props.report.title }}</h2>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-6 space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 mt-10">
                
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-gray-600 font-medium">Progress Keseluruhan</span>
                            <span class="text-2xl font-bold text-[#F54900]">{{ props.report.progress }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-3 mb-3">
                            <div class="bg-[#F54900] h-3 rounded-full transition-all duration-1000" :style="{ width: props.report.progress + '%' }"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-400">
                            <span>Mulai: {{ formatDate(props.report.created_at) }}</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-8 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800 mb-8 flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#F54900]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Timeline Pekerjaan
                        </h3>
                        <div class="relative pl-4">
                            <div class="absolute top-2 left-[27px] h-full w-[2px] bg-gray-200"></div>
                            <div v-for="(step, index) in timelineSteps" :key="index" class="relative pl-12 pb-10 last:pb-0">
                                <div class="absolute top-0 left-0 w-14 h-14 flex items-center justify-center bg-white">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center z-10 border-2" :class="step.status === 'completed' ? 'bg-[#00C853] border-[#00C853]' : step.status === 'current' ? 'bg-white border-[#F54900] animate-pulse' : 'bg-gray-100 border-gray-300'">
                                        <svg v-if="step.status === 'completed'" class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        <div v-else class="w-2.5 h-2.5 rounded-full" :class="step.status === 'current' ? 'bg-[#F54900]' : 'bg-gray-300'"></div>
                                    </div>
                                </div>
                                <div :class="step.status === 'completed' ? 'bg-[#F8FDF9] border-green-50' : 'bg-white border-transparent'" class="rounded-lg p-3 border">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-bold text-gray-800">{{ step.title }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ step.desc }}</p>
                                        </div>
                                        <div class="text-right shrink-0 ml-4 min-w-[90px]">
                                            <p class="text-sm font-bold text-gray-700">{{ formatDate(step.raw_date) }}</p>
                                            <p class="text-xs text-gray-500">{{ formatTime(step.raw_date) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    
                    <div v-if="props.report.status !== 'verification'" class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <h3 class="font-bold text-[#BB4D00] mb-4 flex items-center gap-2"><span class="text-lg">$</span> Informasi Pembayaran</h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wide">
                                    {{ props.report.final_price ? 'Biaya Akhir (Fix)' : 'Estimasi Awal' }}
                                </p>
                                <p class="text-2xl font-black text-[#BB4D00]">Rp {{ formatCurrency(props.report.final_price || props.report.price) }}</p>
                            </div>
                            
                            <div v-if="!props.report.final_price">
                                <button disabled class="w-full py-3 bg-orange-50 text-orange-400 font-bold rounded-xl cursor-not-allowed border border-orange-100">
                                    Menunggu Kesepakatan Harga
                                </button>
                                <p class="text-[10px] text-center mt-2 text-gray-400">Silakan diskusikan harga fix dengan mitra terlebih dahulu.</p>
                            </div>
                            <div v-else>
                                <button @click="handlePayment" class="w-full py-3 bg-[#BB4D00] hover:bg-[#973C00] text-white font-bold rounded-xl shadow-md transition">
                                    Bayar Sekarang
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <h3 class="font-bold text-[#4B741F] mb-4 flex items-center gap-2">Informasi Vendor</h3>
                        
                        <div v-if="props.report.status === 'verification'" class="text-center py-6 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                            <p class="text-sm text-gray-400 italic">Menunggu verifikasi admin untuk meninjau layanan.</p>
                        </div>

                        <div v-else-if="props.report.status === 'pending' && !props.report.vendor" class="text-center py-6 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                            <p class="text-sm text-gray-400 italic mb-3">Belum ada mitra yang ditunjuk.</p>
                            <Link :href="route('reports.offer', props.report.id)" class="px-5 py-2 bg-white border border-[#BB4D00] text-[#BB4D00] text-sm font-bold rounded-full hover:bg-[#BB4D00] hover:text-white transition shadow-sm">
                                Pilih Mitra Sekarang
                            </Link>
                        </div>

                        <div v-else-if="props.report.vendor" class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold text-xl shadow-inner">
                                    {{ props.report.vendor.nama_mitra.charAt(0) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">{{ props.report.vendor.nama_mitra }}</p>
                                    <p class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded font-bold uppercase tracking-wider inline-block">Terverifikasi</p>
                                </div>
                            </div>
                            
                            <div class="text-sm text-gray-600 space-y-1">
                                <p class="flex items-start gap-2">
                                    <svg class="w-4 h-4 mt-0.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                    {{ props.report.vendor.alamat }}
                                </p>
                            </div>

                            <div class="space-y-2 pt-2">
                                <a :href="whatsappLink" target="_blank" class="flex items-center justify-center gap-2 w-full py-3 bg-[#25D366] hover:bg-[#1DA851] text-white font-bold rounded-xl transition shadow-md">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.463 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                Chat via WhatsApp
                                </a>
                                <button @click="handleUploadContract" class="flex items-center justify-center gap-2 w-full py-3 bg-white border-2 border-gray-200 text-gray-600 font-bold rounded-xl hover:bg-gray-50 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    Upload Kontrak Kerja
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <h3 class="font-bold text-[#BB4D00] mb-4 flex items-center gap-2">Lokasi Perbaikan</h3>
                        <div class="mb-4"><p class="text-xs text-gray-400">Alamat Lengkap</p><p class="text-sm text-gray-700 leading-relaxed mt-1">{{ props.report.location }}</p></div>
                        <a :href="`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(props.report.location)}`" target="_blank" class="block w-full text-center border border-[#BB4D00] text-[#BB4D00] hover:bg-orange-50 font-bold py-2 rounded-lg transition text-sm">Lihat di Maps</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>