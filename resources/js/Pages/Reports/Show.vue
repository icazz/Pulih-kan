<script setup>
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";
import { computed, ref } from 'vue';

const props = defineProps({
    report: Object,
    auth: Object
});

// 1. LOGIKA BADGE STATUS (Ditambah 'payment_verification')
const getStatusBadge = (status) => {
    const map = {
        verification: { label: "Menunggu Verifikasi Admin", class: "bg-gray-500" },
        pending: { label: "Menunggu Pembayaran", class: "bg-[#CA8E31]" },
        
        // STATUS BARU: User sudah bayar, Admin belum klik OK
        payment_verification: { label: "Menunggu Konfirmasi Pembayaran", class: "bg-blue-500" }, 
        
        process: { label: "Dalam Pengerjaan", class: "bg-[#4688FB]" },
        completed: { label: "Selesai", class: "bg-[#09A600]" },
        cancelled: { label: "Dibatalkan", class: "bg-red-500" },
    };
    return map[status] || map["verification"];
};

const statusInfo = computed(() => getStatusBadge(props.report?.status));

// Helper Format Uang & Tanggal (Sama seperti sebelumnya)
const formatCurrency = (value) => {
    const number = parseInt(value);
    if (isNaN(number)) return '0';
    return number.toLocaleString('id-ID');
};

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

const whatsappLink = computed(() => {
    if (!props.report.vendor) return '#';
    let phone = props.report.vendor.no_telepon || '';
    if (phone.startsWith('0')) phone = '62' + phone.slice(1);
    const text = `Halo ${props.report.vendor.nama_mitra}, saya ${props.auth?.user?.name || 'Pelanggan'}. Terkait REQ-${props.report.id}.`;
    return `https://wa.me/${phone}?text=${encodeURIComponent(text)}`;
});

// 2. TIMELINE LOGIC (Diperbaiki agar Progress Jalan)
const timelineSteps = computed(() => [
    {
        title: "Pengajuan & Verifikasi",
        raw_date: props.report.created_at, 
        desc: "Laporan diperiksa admin.",
        // Selalu completed jika sudah masuk tahap pembayaran
        status: "completed", 
    },
    {
        title: "Pembayaran & Kontrak",
        raw_date: props.report.payment_method ? props.report.updated_at : '-',
        desc: "Upload bukti & kontrak.",
        // Jika status 'payment_verification' atau lebih lanjut, berarti step ini selesai
        status: (props.report.status === 'payment_verification' || props.report.status === 'process' || props.report.status === 'completed') 
                ? "completed" 
                : (props.report.status === 'pending' ? "current" : "upcoming"),
    },
    {
        title: "Pengerjaan & Survei",
        raw_date: "-", 
        desc: "Menunggu konfirmasi admin untuk mulai.",
        // Jika payment_verification, statusnya 'current' (sedang diproses admin)
        status: (props.report.status === 'payment_verification') 
                ? "current" 
                : (props.report.status === 'process' ? "current" : (props.report.status === 'completed' ? "completed" : "upcoming")),
    },
    {
        title: "Pekerjaan Selesai",
        raw_date: props.report.status === "completed" ? props.report.updated_at : "-",
        desc: "Pekerjaan diselesaikan.",
        status: props.report.status === "completed" ? "completed" : "upcoming",
    },
]);

const markAsComplete = () => {
    if (confirm("Apakah Anda yakin pekerjaan ini sudah selesai sepenuhnya?")) {
        router.patch(route('reports.complete', props.report.id), {}, {
            onSuccess: () => alert("Pekerjaan selesai! Terima kasih.")
        });
    }
};

const showReviewModal = ref(false);
const reviewForm = useForm({
    rating: 0,
    comment: ''
});

const submitReview = () => {
    if (reviewForm.rating === 0) return alert("Silakan pilih bintang 1-5");
    
    reviewForm.post(route('reports.review', props.report.id), {
        onSuccess: () => {
            showReviewModal.value = false;
            alert("Terima kasih atas ulasan Anda!");
        }
    });
};
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
                    <span :class="`${statusInfo.class} px-4 py-1.5 rounded-lg font-bold text-sm shadow-md border border-white/20 text-white`">
                        {{ statusInfo.label }}
                    </span>
                </div>
                <h2 class="text-xl md:text-2xl font-light text-white/90 max-w-3xl leading-relaxed">{{ props.report.title }}</h2>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-6 space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 mt-10">
                
                <div class="lg:col-span-2 space-y-6">
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

                    <div v-if="props.report.status === 'process'" class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mt-6">
                        <h3 class="font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Konfirmasi Penyelesaian
                        </h3>
                        <p class="text-xs text-gray-500 mb-4 leading-relaxed">
                            Jika mitra sudah menyelesaikan perbaikan, silakan konfirmasi untuk menutup pesanan ini.
                        </p>
                        <button 
                            @click="markAsComplete" 
                            class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-md transition flex items-center justify-center gap-2">
                            <span>✅</span> Tandai Pekerjaan Selesai
                        </button>
                    </div>

                    <div v-if="props.report.status === 'completed'" class="bg-green-50 rounded-xl shadow-sm p-6 border border-green-200 mt-6 text-center transition-all">
    
                        <div class="mb-6">
                            <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <h3 class="font-bold text-green-800 mb-1">Pesanan Selesai</h3>
                            <p class="text-xs text-green-600">Terima kasih telah menggunakan jasa kami.</p>
                        </div>

                        <div v-if="props.report.review" class="bg-white p-4 rounded-xl border border-green-100 shadow-sm">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Ulasan Anda</p>
                            <div class="flex justify-center gap-1 mb-2">
                                <span v-for="n in 5" :key="n" class="text-xl" :class="n <= props.report.review.rating ? 'text-yellow-400' : 'text-gray-200'">★</span>
                            </div>
                            <p class="text-sm text-gray-600 italic">"{{ props.report.review.comment }}"</p>
                        </div>

                        <div v-else class="bg-white p-5 rounded-xl border border-green-100 shadow-sm">
                            <h4 class="font-bold text-gray-800 mb-2">Beri Nilai Mitra</h4>
                            <p class="text-xs text-gray-500 mb-4">Bagaimana hasil pengerjaan mitra kami?</p>

                            <div class="flex justify-center gap-2 mb-4">
                                <button 
                                    v-for="star in 5" 
                                    :key="star"
                                    @click="reviewForm.rating = star"
                                    type="button"
                                    class="text-3xl transition-transform hover:scale-110 focus:outline-none"
                                    :class="star <= reviewForm.rating ? 'text-yellow-400' : 'text-gray-300'"
                                >
                                    ★
                                </button>
                            </div>

                            <textarea 
                                v-model="reviewForm.comment"
                                rows="2"
                                class="w-full text-sm border-gray-200 rounded-lg focus:ring-green-500 focus:border-green-500 mb-3"
                                placeholder="Tulis ulasan Anda disini (Opsional)..."
                            ></textarea>

                            <button 
                                @click="submitReview"
                                :disabled="reviewForm.processing"
                                class="w-full py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg text-sm transition shadow-md disabled:opacity-50"
                            >
                                {{ reviewForm.processing ? 'Mengirim...' : 'Kirim Ulasan' }}
                            </button>
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
                            
                            <div v-if="props.report.payment_method">
                                <div class="p-3 bg-blue-50 border border-blue-100 rounded-xl mb-3">
                                    <p class="text-blue-700 text-xs font-bold flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Bukti Terkirim
                                    </p>
                                    <p class="text-[10px] text-blue-600 mt-1">Data pembayaran sudah masuk. Admin akan segera memverifikasi dan mengubah status ke "Dalam Pengerjaan".</p>
                                </div>

                                <div class="space-y-3 pt-2 border-t border-gray-100">
                                    <div>
                                        <p class="text-[10px] text-gray-400 uppercase font-bold">Metode</p>
                                        <p class="text-sm font-bold text-gray-800">{{ props.report.payment_method }}</p>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-2">
                                        <div v-if="props.report.contract_file">
                                            <p class="text-[10px] text-gray-400 uppercase font-bold mb-1">Kontrak Kerja</p>
                                            <a :href="props.report.contract_file" target="_blank" class="block w-full py-2 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-lg transition">
                                                📂 Buka Link
                                            </a>
                                        </div>
                                        <div v-if="props.report.payment_proof">
                                            <p class="text-[10px] text-gray-400 uppercase font-bold mb-1">Bukti Bayar</p>
                                            <a :href="props.report.payment_proof" target="_blank" class="block w-full py-2 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-lg transition">
                                                🔗 Buka Link
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-else-if="!props.report.final_price">
                                <button disabled class="w-full py-3 bg-orange-50 text-orange-400 font-bold rounded-xl cursor-not-allowed border border-orange-100">
                                    Menunggu Kesepakatan Harga
                                </button>
                            </div>
                            
                            <div v-else>
                                <Link :href="route('reports.payment', props.report.id)" 
                                    class="w-full py-3 bg-[#BB4D00] hover:bg-[#973C00] text-white font-bold rounded-xl shadow-md transition block text-center">
                                    Bayar Sekarang
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <h3 class="font-bold text-[#4B741F] mb-4 flex items-center gap-2">Informasi Vendor</h3>
                        <div v-if="props.report.vendor" class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold text-xl shadow-inner">
                                    {{ props.report.vendor.nama_mitra.charAt(0) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">{{ props.report.vendor.nama_mitra }}</p>
                                    <p class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded font-bold uppercase tracking-wider inline-block">Terverifikasi</p>
                                </div>
                            </div>
                            <div class="space-y-2 pt-2">
                                <a :href="whatsappLink" target="_blank" class="flex items-center justify-center gap-2 w-full py-3 bg-[#25D366] hover:bg-[#1DA851] text-white font-bold rounded-xl transition shadow-md">
                                Chat
                                </a>
                            </div>
                        </div>
                        <div v-else class="text-center py-6 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-400 italic">Belum ada mitra.</p>
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