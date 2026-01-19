<script setup>
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";
import { computed, ref } from "vue";

const props = defineProps({
    report: Object,
    vendor: Object
});

const contractInput = ref(null);

const triggerContractUpload = () => {
    contractInput.value.click();
};

const handleContractUpload = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    if (file.type !== 'application/pdf') {
        alert('Mohon upload file dalam format PDF.');
        return;
    }

    if (confirm(`Apakah Anda yakin ingin mengunggah "${file.name}" sebagai kontrak kerja?`)) {
        // Kita gunakan router manual (bukan useForm) agar lebih bersih untuk single file upload
        router.post(route('vendor.reports.uploadContract', props.report.id), {
            _method: 'post',
            contract_file: file
        }, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                alert('Kontrak berhasil diunggah!');
                // HAPUS baris router.reload ini, biarkan Inertia yang refresh otomatis
                // router.reload({ only: ['report'] }); 
            },
            onError: (errors) => {
                console.error(errors);
                alert('Gagal mengunggah kontrak.');
            }
        });
    }
};

const evidenceFiles = computed(() => {
    if (!props.report.evidence_files) return [];
    if (Array.isArray(props.report.evidence_files)) return props.report.evidence_files;
    try {
        return JSON.parse(props.report.evidence_files);
    } catch (e) {
        return [];
    }
});

// 2. Helper untuk cek ekstensi gambar
const isImage = (file) => {
    return file.match(/\.(jpeg|jpg|gif|png|webp|bmp)$/i);
};

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
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    
                    <Link :href="route('vendor.dashboard')" class="text-white/70 hover:text-white flex items-center gap-2 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Kembali ke Dashboard
                    </Link>

                    <a href="https://docs.google.com/document/d/1LYdgiC8T2FyuJtoUd2rndYsQIQ1ynflLHc_WRfBUhm8/edit?usp=sharing" 
                    target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 border border-white/50 text-white text-xs font-bold rounded-lg backdrop-blur-sm transition shadow-sm"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Template Kontrak Kerja
                    </a>

                </div>

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
                                    <p class="text-gray-400 uppercase text-[10px] font-bold tracking-widest mb-2">Bukti Kerusakan</p>
                                    
                                    <div v-if="evidenceFiles.length > 0" class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                        <a 
                                            v-for="(file, index) in evidenceFiles" 
                                            :key="index"
                                            :href="`/storage/${file}`" 
                                            target="_blank"
                                            class="block aspect-square rounded-lg overflow-hidden border border-gray-200 relative group cursor-pointer shadow-sm"
                                        >
                                            <img 
                                                v-if="isImage(file)" 
                                                :src="`/storage/${file}`" 
                                                class="w-full h-full object-cover transition duration-300 group-hover:scale-110" 
                                                alt="Bukti"
                                            />
                                            <div v-else class="w-full h-full bg-black flex items-center justify-center text-white group-hover:bg-gray-900 transition">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                            
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                <span class="bg-white text-xs font-bold px-2 py-1 rounded text-black shadow-sm hover:bg-gray-100">Lihat</span>
                                            </div>
                                        </a>
                                    </div>

                                    <div v-else class="w-full p-4 bg-gray-50 rounded-xl border border-gray-200 text-center border-dashed">
                                        <p class="text-xs text-gray-400 italic">Tidak ada lampiran foto/video.</p>
                                    </div>
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
                            
                            <div class="flex flex-col sm:flex-row gap-3">
                                <div class="flex-1 w-full">
                                    <input 
                                        type="file" 
                                        ref="contractInput" 
                                        class="hidden" 
                                        accept="application/pdf"
                                        @change="handleContractUpload"
                                    />

                                    <button 
                                        @click="triggerContractUpload"
                                        class="flex-1 w-full min-w-[140px] py-3 bg-[#4B741F] hover:bg-[#3d5e18] text-white font-bold rounded-xl shadow-md transition font-['Montserrat'] flex items-center justify-center gap-2"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        {{ report.contract_file ? 'Ganti Kontrak (PDF)' : 'Upload Kontrak (PDF)' }}
                                    </button>

                                    <div v-if="report.contract_file" class="mt-2 text-center">
                                        <a 
                                            :href="report.contract_file" 
                                            target="_blank" 
                                            class="text-xs text-[#4B741F] font-bold hover:underline inline-flex items-center gap-1"
                                        >
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Lihat File Kontrak
                                        </a>
                                    </div>
                                </div>
                                <a 
                                    :href="`/chat/${report.user_id}`"
                                    target="_blank" 
                                    class="flex-1 min-w-[140px] py-3 bg-[#2F2F2F] hover:bg-black text-white font-bold rounded-xl text-center shadow-md transition flex items-center justify-center gap-2 font-['Montserrat']"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                    Chat Customer
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