<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import Navbar from "@/Components/Navbar.vue";

const props = defineProps({
    report: Object,
    admin_fee: Number,
    auth: Object,
});

const selectedMethod = ref("Mandiri");
const paymentMethods = [
    { name: "BCA", icon: "/images/bca.png", type: "Bank", accountNumber: "8830123456", accountName: "PT Pulihkan Indonesia Jaya" },
    { name: "Mandiri", icon: "/images/mandiri.png", type: "Bank", accountNumber: "1370012345678", accountName: "PT Pulihkan Indonesia Jaya" },
    { name: "BNI", icon: "/images/bni.png", type: "Bank", accountNumber: "0123456789", accountName: "PT Pulihkan Indonesia Jaya" },
    { name: "GoPay", icon: "/images/gopay.png", type: "E-Wallet", accountNumber: "081234567890", accountName: "PULIHKAN - GO PAY" },
    { name: "OVO", icon: "/images/ovo.png", type: "E-Wallet", accountNumber: "081234567890", accountName: "PULIHKAN - OVO" },
    { name: "DANA", icon: "/images/dana.png", type: "E-Wallet", accountNumber: "081234567890", accountName: "PULIHKAN - DANA" },
];

const activeMethodDetails = computed(() => {
    return paymentMethods.find(m => m.name === selectedMethod.value);
});

// FORM UNTUK LINK (STRING)
const form = useForm({
    proof: '',      // String kosong (untuk link)
    contract: '',   // String kosong (untuk link)
    payment_type: selectedMethod.value,
});

// HAPUS FUNGSI handleUpload... KARENA KITA PAKAI V-MODEL LANGSUNG

const submitPayment = () => {
    // Validasi Sederhana
    if (!form.contract) return alert("Harap isi Link Google Drive (Kontrak).");
    if (!form.proof) return alert("Harap isi Link Bukti Transfer.");
    
    // POST DATA (Tanpa forceFormData karena ini JSON biasa)
    form.post(route("reports.storePayment", props.report.id), {
        onSuccess: () => {
            console.log("Berhasil!");
        },
        onError: (err) => {
            console.error("Gagal mengirim:", err);
        }
    });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency", currency: "IDR", minimumFractionDigits: 0,
    }).format(value);
};

const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text);
    alert("Berhasil disalin: " + text);
};
</script>

<template>
    <Head title="Pembayaran" />
    <Navbar />

    <div class="min-h-screen bg-[#F9F9F9] font-['Montserrat'] pb-20">
        <div class="w-full bg-[#BB4D00] pt-32 pb-12 px-6">
            <div class="max-w-6xl mx-auto text-white">
                <Link :href="route('reports.show', report.id)" class="flex items-center gap-2 text-sm mb-4 opacity-80 hover:opacity-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    Kembali
                </Link>
                <h1 class="text-4xl font-bold">Pembayaran</h1>
                <p class="opacity-80">Selesaikan pembayaran untuk melanjutkan pengajuan</p>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-6 mt-8">
            <div class="bg-[#FFF8E6] border border-[#FFE08A] p-4 rounded-xl flex items-center gap-3 mb-6">
                <svg class="w-6 h-6 text-[#B28900]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" /></svg>
                <p class="text-sm text-[#856404]">
                    <span class="font-bold">Batas Waktu Pembayaran:</span> Selesaikan pembayaran dalam <span class="font-bold text-[#BB4D00]">23 jam 45 menit</span>.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-lg mb-6">Pilih Metode Pembayaran</h3>
                        <div class="grid grid-cols-3 gap-4 mb-8">
                            <button v-for="method in paymentMethods" :key="method.name"
                                @click="selectedMethod = method.name; form.payment_type = method.name"
                                :class="selectedMethod === method.name ? 'border-[#BB4D00] bg-orange-50 ring-1 ring-[#BB4D00]' : 'border-gray-200'"
                                class="p-4 border rounded-xl flex flex-col items-center justify-center gap-2 hover:bg-gray-50 transition">
                                <span class="font-bold text-gray-500 text-xs">{{ method.name }}</span>
                                <span class="text-[9px] uppercase font-bold text-gray-400">{{ method.type }}</span>
                            </button>
                        </div>

                        <div class="bg-[#FDFCFB] border border-orange-100 rounded-2xl p-6">
                            <div class="flex items-center gap-2 text-[#BB4D00] font-bold text-sm mb-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" stroke-width="2" /></svg>
                                Detail Pembayaran - {{ activeMethodDetails.name }}
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs text-gray-400 uppercase font-bold">Nomor {{ activeMethodDetails.type === 'Bank' ? 'Rekening' : 'HP' }}</p>
                                    <div class="flex items-center justify-between mt-1">
                                        <span class="text-xl font-bold tracking-wider">{{ activeMethodDetails.accountNumber }}</span>
                                        <button @click="copyToClipboard(activeMethodDetails.accountNumber)" class="text-[#F54900] text-xs font-bold px-3 py-1 bg-orange-50 rounded-lg">Salin</button>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 uppercase font-bold">Nama Penerima</p>
                                    <p class="font-bold">{{ activeMethodDetails.accountName }}</p>
                                </div>
                                <div class="p-4 bg-[#FFF3C4]/30 rounded-xl">
                                    <p class="text-xs text-gray-500 font-bold uppercase">Total yang Harus Dibayar</p>
                                    <div class="flex items-center justify-between">
                                        <p class="text-2xl font-black text-[#BB4D00]">{{ formatCurrency(report.final_price) }}</p>
                                        <button @click="copyToClipboard(report.final_price)" class="text-[#F54900] text-xs font-bold">Salin</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-lg mb-6">Ringkasan Pengajuan</h3>
                        <div class="space-y-4 text-sm">
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold">Nomor Pengajuan</p>
                                <p class="font-bold text-gray-800 uppercase">REQ-{{ report.id }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold">Jenis Pemulihan</p>
                                <p class="font-bold text-gray-800">{{ report.title }}</p>
                            </div>
                            <div class="pt-4 border-t border-gray-50">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-gray-800">Total</span>
                                    <span class="text-xl font-black text-[#BB4D00]">{{ formatCurrency(report.final_price) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 text-center">
                            <h3 class="font-bold text-xs mb-1">Link Google Drive (Kontrak)</h3>
                            <p class="text-[9px] text-gray-400 mb-2 italic">Pastikan link folder sudah di-set "Anyone with the link"</p>
                            
                            <input type="url" v-model="form.contract" 
                                placeholder="https://drive.google.com/..."
                                class="w-full text-xs border-gray-200 rounded-lg p-2 focus:ring-[#BB4D00] focus:border-[#BB4D00]"
                            />
                            <p v-if="form.errors.contract" class="text-red-500 text-[10px] mt-1 font-bold">{{ form.errors.contract }}</p>
                        </div>

                        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 text-center">
                            <h3 class="font-bold text-xs mb-1">Link Bukti Transfer</h3>
                            <p class="text-[9px] text-gray-400 mb-2 italic">Link gambar/PDF bukti pembayaran</p>
                            
                            <input type="url" v-model="form.proof" 
                                placeholder="https://drive.google.com/..."
                                class="w-full text-xs border-gray-200 rounded-lg p-2 focus:ring-[#BB4D00] focus:border-[#BB4D00]"
                            />
                            <p v-if="form.errors.proof" class="text-red-500 text-[10px] mt-1 font-bold">{{ form.errors.proof }}</p>
                        </div>

                        <button @click="submitPayment" :disabled="form.processing"
                            class="w-full py-3 bg-[#BB4D00] text-white font-bold rounded-xl shadow-md transition uppercase text-[10px] tracking-widest hover:bg-[#a04100]">
                            {{ form.processing ? 'Mengirim...' : 'Kirim Link & Bayar' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>