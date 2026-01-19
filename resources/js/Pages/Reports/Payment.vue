<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import Navbar from "@/Components/Navbar.vue";

const props = defineProps({
    report: Object,
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

// --- PERUBAHAN 1: Form Proof jadi null (File) & Hapus Contract ---
const form = useForm({
    payment_type: selectedMethod.value,
    proof: null, // Siap menerima File Object
});

const previewImage = ref(null);

// --- PERUBAHAN 2: Handle Upload Gambar & Preview ---
const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.proof = file;
        // Buat preview gambar
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImage.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submitPayment = () => {
    if (!form.proof) return alert("Harap upload bukti transfer terlebih dahulu.");
    
    // --- PERUBAHAN 3: Gunakan forceFormData: true ---
    form.post(route("reports.storePayment", props.report.id), {
        forceFormData: true, // Wajib untuk upload file di Inertia
        onSuccess: () => alert("Bukti pembayaran berhasil dikirim!"),
        onError: (err) => {
            console.error(err);
            // Tampilkan error detail jika ada
            alert("Gagal mengirim: " + (err.proof || err.error || "Terjadi kesalahan server"));
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
                            <div class="pt-4 border-t border-gray-50 flex justify-between items-center">
                                <span class="font-bold text-gray-800">Total</span>
                                <span class="text-xl font-black text-[#BB4D00]">{{ formatCurrency(report.final_price) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 text-center">
                            <h3 class="font-bold text-xs mb-1">Upload Bukti Transfer</h3>
                            <p class="text-[9px] text-gray-400 mb-4 italic">Format: JPG, PNG, PDF (Maks. 5MB)</p>
                            
                            <div class="relative w-full border-2 border-dashed border-gray-300 rounded-xl hover:bg-gray-50 transition cursor-pointer group h-40 flex flex-col items-center justify-center overflow-hidden">
                                <input type="file" @change="handleFileChange" accept="image/*,application/pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
                                
                                <div v-if="previewImage" class="absolute inset-0 p-2 bg-white">
                                    <img :src="previewImage" class="w-full h-full object-contain rounded-lg" alt="Preview Bukti">
                                </div>
                                <div v-else class="flex flex-col items-center text-gray-400 group-hover:text-gray-600">
                                    <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    <span class="text-[10px] font-bold">Klik atau geser foto ke sini</span>
                                </div>
                            </div>
                            <p v-if="form.errors.proof" class="text-red-500 text-[10px] mt-2 font-bold">{{ form.errors.proof }}</p>
                        </div>

                        <button @click="submitPayment" :disabled="form.processing"
                            class="w-full py-3 bg-[#BB4D00] text-white font-bold rounded-xl shadow-md transition uppercase text-[10px] tracking-widest hover:bg-[#a04100] disabled:opacity-50">
                            {{ form.processing ? 'Mengirim...' : 'Kirim Bukti Pembayaran' }}
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>