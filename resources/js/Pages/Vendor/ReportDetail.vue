<script setup>
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";
import { computed } from "vue";

const props = defineProps({
    report: Object,
    vendor: Object
});

const form = useForm({
    final_price: props.report.final_price || '',
});

const updatePrice = () => {
    form.patch(route('vendor.reports.updateFinalPrice', props.report.id), {
        preserveScroll: true, // Agar halaman tidak loncat ke atas
        onSuccess: () => {
            // HAPUS alert() agar tidak mengganggu flow
            // Paksa ambil data terbaru dari server (Partial Reload)
            router.reload({ only: ['report'] }); 
        },
        onError: (errors) => console.log(errors) 
    });
};

const formatPrice = (value) => {
    if (!value) return 'Rp 0';
    return 'Rp ' + parseFloat(value).toLocaleString('id-ID', { minimumFractionDigits: 0 });
};

const whatsappLink = computed(() => {
    let phone = props.report.user?.no_telepon || ''; 
    if (phone.startsWith('0')) phone = '62' + phone.slice(1);
    return `https://wa.me/${phone}?text=Halo, saya dari ${props.vendor.nama_mitra} ingin mendiskusikan proyek REQ-${props.report.id}`;
});

const cancelForm = useForm({});

const handleCancelTask = (id) => {
    if (confirm("Apakah Anda yakin ingin membatalkan? Anda akan dihapus dari laporan ini dan Customer bisa memilih mitra lain.")) {
        cancelForm.post(route('vendor.reports.cancelSelection', id), {
            onSuccess: () => alert("Berhasil dibatalkan.")
        });
    }
};

const getStatusBadge = (status) => {
    const map = {
        pending: { label: "Menunggu Pembayaran", class: "bg-[#CA8E31]" },
        process: { label: "Dalam Pengerjaan", class: "bg-[#4688FB]" },
        completed: { label: "Selesai", class: "bg-[#09A600]" },
        cancelled: { label: "Dibatalkan", class: "bg-red-500" },
    };
    return map[status] || { label: status, class: "bg-gray-500" };
};

const statusInfo = computed(() => getStatusBadge(props.report.status));

const openDrive = (url) => {
    if (!url) return alert("Link Google Drive tidak tersedia.");
    const validUrl = url.startsWith('http') ? url : `https://${url}`;
    window.open(validUrl, '_blank');
};

const cancelOrder = () => {
    // 1. Konfirmasi agar tidak terpencet tidak sengaja
    if (!confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Pesanan akan kembali terbuka untuk mitra lain.')) {
        return;
    }

    // 2. Kirim request ke backend
    // PERBAIKAN: Gunakan nama route yang benar sesuai web.php ('vendor.reports.cancelSelection')
    router.post(route('vendor.reports.cancelSelection', props.report.id), {}, {
        onSuccess: () => {
            alert('Pesanan berhasil dibatalkan.');
            // Redirect otomatis diurus oleh Controller (ke Dashboard)
        },
        onError: (err) => {
            console.error(err);
            alert('Gagal membatalkan pesanan. ' + (err.message || ''));
        }
    });
};
</script>

<template>
    <Head :title="`Detail REQ-${report.id}`" />
    <Navbar />

    <div class="min-h-screen bg-[#FFFDF4] font-['Montserrat'] pb-20">
        <div class="w-full bg-gradient-to-r from-[#8FC555] to-[#4B692D] pt-32 pb-16 px-6">
            <div class="max-w-6xl mx-auto">
                <Link :href="route('vendor.dashboard')" class="text-white/70 hover:text-white flex items-center gap-2 mb-6 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Kembali ke Dashboard
                </Link>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-white text-4xl font-bold">REQ-{{ report.id }}</h1>
                    <span :class="`px-4 py-1.5 rounded-lg text-white text-[11px] font-bold uppercase tracking-wider shadow-md ${statusInfo.class}`">
                        {{ statusInfo.label }}
                    </span>
                </div>
                <p class="text-white/80 text-xl mt-3 font-light">{{ report.title }}</p>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-6 mt-10 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">

                <div class="space-y-6">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-6 h-6 text-[#BB4D00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Timeline Pekerjaan
                    </h3>
                    
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white shadow-sm">✓</div>
                            <div class="w-0.5 h-full bg-gray-200"></div>
                        </div>
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex-1 mb-4">
                            <div class="flex justify-between items-start mb-4">
                                <h4 class="font-bold text-gray-800">Pengajuan Disetujui</h4>
                                <span class="text-xs text-gray-400">Diverifikasi Admin</span>
                            </div>
                            
                            <div class="space-y-4 text-sm bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <div>
                                    <p class="text-gray-400 uppercase text-[10px] font-bold tracking-widest mb-1">Kategori Kerusakan</p>
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase"
                                         :class="report.category === 'Berat' ? 'bg-red-100 text-red-600' : report.category === 'Sedang' ? 'bg-orange-100 text-orange-600' : 'bg-green-100 text-green-600'">
                                        Kerusakan {{ report.category || '-' }}
                                    </div>
                                </div>
                                <div>
                                    <p class="text-gray-400 uppercase text-[10px] font-bold tracking-widest mb-1">Jenis Kerusakan</p>
                                    <p class="text-gray-800 font-medium capitalize">{{ report.damage_types?.join(', ') || '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 uppercase text-[10px] font-bold tracking-widest mb-1">Deskripsi Masalah</p>
                                    <p class="text-gray-700 leading-relaxed">{{ report.description }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 uppercase text-[10px] font-bold tracking-widest mb-1">Bukti Kerusakan</p>
                                    <button @click="openDrive(report.drive_link)" class="mt-1 flex items-center gap-2 text-blue-600 font-bold hover:text-blue-800 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        Buka Google Drive
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div :class="report.final_price ? 'bg-green-500' : 'bg-gray-200'" class="w-8 h-8 rounded-full flex items-center justify-center text-white shadow-sm transition-colors">
                                {{ report.final_price ? '✓' : '2' }}
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex-1">
                            <h4 class="font-bold text-gray-800 mb-2">Kerja Sama Disetujui Client</h4>
                            <p class="text-sm text-gray-500 mb-6 leading-relaxed">Diskusikan harga final dengan client melalui chat, lalu input biaya akhir pada panel informasi pembayaran.</p>
                            
                            <div class="flex flex-wrap gap-3">
                                <button class="flex-1 min-w-[140px] py-3 bg-[#4B741F] hover:bg-[#3d5e18] text-white font-bold rounded-xl shadow-md transition font-['Montserrat']">Download Kontrak</button>
                                <a :href="whatsappLink" target="_blank" class="flex-1 min-w-[140px] py-3 bg-[#134E4A] hover:bg-[#0f3d3a] text-white font-bold rounded-xl text-center shadow-md transition flex items-center justify-center gap-2 font-['Montserrat']">
                                    Chat
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="text-orange-500 font-bold">$</span> Informasi Pembayaran
                    </h3>
                    <div class="space-y-4">
                        
                        <div v-if="!report.final_price || report.final_price == 0">
                            <p class="text-xs text-gray-400 uppercase font-bold tracking-wider">Input Total Biaya Fix</p>
                            <div class="flex gap-2 mt-2">
                                <input 
                                    v-model="form.final_price" 
                                    type="number" 
                                    class="w-full border-gray-200 rounded-lg text-sm focus:ring-[#BB4D00] focus:border-[#BB4D00]" 
                                    placeholder="Contoh: 350000"
                                >
                                <button 
                                    @click="updatePrice" 
                                    :disabled="form.processing"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 rounded-lg text-xs font-bold transition flex items-center"
                                >
                                    {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                                </button>
                            </div>
                            <p v-if="form.errors.final_price" class="text-red-500 text-[10px] mt-1">{{ form.errors.final_price }}</p>
                        </div>
                        
                        <div v-else class="space-y-3">
                            <div class="bg-green-50 p-3 rounded-lg border border-green-100">
                                <p class="text-green-700 text-xs font-bold flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    Harga deal telah ditetapkan
                                </p>
                            </div>

                            <div class="pt-4 border-t border-gray-50 flex justify-between items-center">
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-1">Biaya Fix</p>
                                    <p class="text-lg font-bold text-[#BB4D00]">{{ formatPrice(report.final_price) }}</p>
                                </div>
                                
                                <div v-if="report.status === 'pending'" class="bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                    Belum Dibayar
                                </div>
                                <div v-else-if="report.status === 'payment_verification' || report.status === 'process'" class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                    Sudah Dibayar
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4">Informasi Client</h3>
                    <div class="space-y-4 text-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-[#BB4D00]/10 text-[#BB4D00] flex items-center justify-center font-bold shadow-inner">
                                {{ report.user?.name.charAt(0) }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">{{ report.user?.name }}</p>
                                <p class="text-xs text-gray-500">{{ report.user?.email }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-2">Lokasi Perbaikan</h3>
                    <p class="text-xs text-gray-500 leading-relaxed mb-4">{{ report.location }}</p>
                    <a :href="`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(report.location)}`" target="_blank" class="block w-full text-center py-2.5 border border-[#BB4D00] text-[#BB4D00] hover:bg-orange-50 font-bold rounded-xl transition text-xs">Lihat di Maps</a>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4">Tindakan Pesanan</h3>
                    
                    <div class="flex flex-col gap-3">
                        
                        <button 
                            v-if="report.status === 'pending' && !report.final_price" 
                            @click="cancelOrder" 
                            class="w-full bg-red-50 text-red-600 px-4 py-3 rounded-xl font-bold border border-red-200 hover:bg-red-100 transition"
                        >
                            Batalkan Pesanan
                        </button>

                        <div>
                            <button :disabled="report.status !== 'process'"
                                    :class="report.status === 'process' ? 'bg-[#4B741F] hover:bg-[#3d5e18] text-white shadow-lg' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                                    class="w-full py-4 font-bold rounded-xl transition">
                                Tandai Selesai
                            </button>
                            <p v-if="report.status === 'process'" class="text-[10px] text-gray-400 mt-2 text-center italic">
                                Klik jika pekerjaan telah rampung 100%.
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>