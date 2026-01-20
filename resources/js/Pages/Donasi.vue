<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";

// Menerima data dari Controller (Daftar Donatur Verified & Total)
const props = defineProps({
    donors: {
        type: Array,
        default: () => [] // Jika data kosong/undefined, otomatis jadi array kosong []
    },
    totalDonation: {
        type: Number,
        default: 0
    }
});

const maskName = (name) => {
    if (!name) return "";
    const firstChar = name.charAt(0); // Huruf pertama
    const lastChar = name.charAt(name.length - 1); // Huruf terakhir
    const stars = "*".repeat(6); // Jumlah bintang tetap (bisa disesuaikan)
    
    return `${firstChar}${stars}${lastChar}`;
};

// Setup Form Inertia
const form = useForm({
    name: '',
    amount: '',
    proof_file: null,
});

// Helper: Set nominal cepat
const setAmount = (val) => form.amount = val;

// Handle File Upload
const handleFileUpload = (event) => {
    form.proof_file = event.target.files[0];
};

// Submit Form
const submit = () => {
    form.post(route('donasi.store'), {
        onSuccess: () => {
            form.reset();
            alert('Terima kasih! Bukti pembayaran berhasil dikirim dan sedang menunggu verifikasi.');
        },
    });
};

// Helper: Format Rupiah
const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value || 0);
};

// Helper: Format Tanggal
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'numeric', year: 'numeric'
    });
};
</script>

<template>
    <Head title="Mari Berbagi Kebaikan" />
    <Navbar />

    <div class="min-h-screen bg-[#FFF7ED] font-['Montserrat'] pb-20">
        
        <div class="relative w-full h-[500px] overflow-hidden pt-32 px-6">
            
            <img 
                src="/images/donasi-bg.jpg" 
                alt="Background Donasi" 
                class="absolute inset-0 w-full h-full object-cover"
            />

            <div class="absolute top-28 left-6 md:left-16 z-30">
                <Link :href="route('welcome')" 
                    class="group flex items-center gap-3 text-white bg-white/10 hover:bg-white/20 backdrop-blur-md px-6 py-3 rounded-full border border-white/20 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span class="font-semibold text-sm md:text-base">Kembali</span>
                </Link>
            </div>

            <div class="absolute inset-0 bg-gradient-to-br from-[#481313] via-[#7B1E1E] to-[#CD5C5C] opacity-90 mix-blend-multiply"></div>
            
            <div class="relative z-10 max-w-6xl mx-auto text-center text-white">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Mari Berbagi Kebaikan Bersama</h1>
                <p class="text-lg md:text-xl text-[#FFF7ED] max-w-3xl mx-auto mb-10">
                    Setiap donasi dan bantuan Anda membawa harapan baru bagi mereka yang membutuhkan.
                    Bergabunglah sebagai donatur atau relawan kami hari ini!
                </p>

                <div class="flex flex-wrap justify-center gap-6">
                    <div class="bg-white/20 backdrop-blur-sm border border-white/30 rounded-xl px-6 py-3 min-w-[160px]">
                        <p class="text-[#FFEDD4] text-sm">Target Bulan Ini</p>
                        <p class="text-2xl font-bold">Rp 50.000.000</p>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm border border-white/30 rounded-xl px-6 py-3 min-w-[160px]">
                        <p class="text-[#FFEDD4] text-sm">Relawan Aktif</p>
                        <p class="text-2xl font-bold">150+ Orang</p>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm border border-white/30 rounded-xl px-6 py-3 min-w-[160px]">
                        <p class="text-[#FFEDD4] text-sm">Program Berjalan</p>
                        <p class="text-2xl font-bold">25 Program</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 relative z-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <div class="space-y-8">
                    
                    <div class="bg-gradient-to-br from-[#452C0D] to-[#E17100] rounded-2xl p-8 text-white shadow-xl relative overflow-hidden">
                        <div class="relative z-10">
                            <h2 class="text-2xl font-bold mb-1">Total Donasi Terkumpul</h2>
                            <p class="text-5xl font-bold mb-2">{{ formatRupiah(totalDonation) }}</p>
                            <p class="text-[#FFEDD4]">Dari {{ donors.length }} donatur</p>
                        </div>
                        <div class="absolute right-0 top-0 opacity-10">
                            <svg width="200" height="200" viewBox="0 0 200 200" fill="white"><circle cx="150" cy="50" r="100"/></svg>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                        <h3 class="text-2xl font-bold text-[#1E2939] mb-6">Form Donasi</h3>
                        
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-[#364153] mb-2">Nama Lengkap</label>
                                <input v-model="form.name" type="text" placeholder="Masukkan nama Anda" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-[#F54900] focus:ring-0 transition" />
                                <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#364153] mb-2">Jumlah Donasi</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-3.5 text-gray-500">Rp</span>
                                    <input v-model="form.amount" type="number" class="w-full pl-12 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-[#F54900] focus:ring-0 transition font-bold text-gray-700" />
                                </div>
                                <div v-if="form.errors.amount" class="text-red-500 text-sm mt-1">{{ form.errors.amount }}</div>
                            </div>

                            <div class="grid grid-cols-4 gap-2">
                                <button type="button" @click="setAmount(50000)" class="py-2 border-2 border-[#FFD6A7] rounded-lg text-[#F54900] hover:bg-[#FFF4E2] font-medium text-sm transition">50.000</button>
                                <button type="button" @click="setAmount(100000)" class="py-2 border-2 border-[#FFD6A7] rounded-lg text-[#F54900] hover:bg-[#FFF4E2] font-medium text-sm transition">100.000</button>
                                <button type="button" @click="setAmount(250000)" class="py-2 border-2 border-[#FFD6A7] rounded-lg text-[#F54900] hover:bg-[#FFF4E2] font-medium text-sm transition">250.000</button>
                                <button type="button" @click="setAmount(500000)" class="py-2 border-2 border-[#FFD6A7] rounded-lg text-[#F54900] hover:bg-[#FFF4E2] font-medium text-sm transition">500.000</button>
                            </div>

                            <div class="bg-[#FFF4E2] border-2 border-[#FFD9AE] rounded-2xl p-6 text-center">
                                <h4 class="font-bold text-lg mb-4">Scan QRIS untuk Pembayaran</h4>
                                <div class="bg-white p-4 rounded-xl inline-block shadow-sm mb-4">
                                    <div class="w-48 h-48 bg-gray-200 flex items-center justify-center text-gray-400">
                                        <img src="/images/barcode-donasi.png" alt="QRIS" class="w-full h-full object-contain">
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <label class="block text-left text-sm font-medium mb-2">Upload Bukti Pembayaran</label>
                                    
                                    <div class="border-2 border-dashed border-gray-400 rounded-xl p-6 bg-white cursor-pointer hover:bg-gray-50 transition text-center relative">
                                        
                                        <input type="file" @change="handleFileUpload" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
                                        
                                        <div v-if="!form.proof_file">
                                            <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                            <p class="text-sm text-gray-600">Klik untuk upload atau drag & drop</p>
                                            <p class="text-xs text-gray-400">PNG, JPG hingga 5MB</p>
                                        </div>
                                        <div v-else class="text-green-600 font-bold flex items-center justify-center gap-2">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            File Terpilih: {{ form.proof_file.name }}
                                        </div>
                                    </div>
                                    <div v-if="form.errors.proof_file" class="text-red-500 text-sm mt-1 text-left">{{ form.errors.proof_file }}</div>
                                </div>
                            </div>

                            <button type="submit" :disabled="form.processing" class="w-full py-4 bg-[#73C444] hover:bg-[#5da335] text-white font-bold rounded-xl shadow-lg transition flex items-center justify-center gap-2 disabled:opacity-50">
                                <svg v-if="!form.processing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                <span v-if="form.processing">Mengirim...</span>
                                <span v-else>Sudah Transfer</span>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="space-y-8">
                    
                    <div class="bg-gradient-to-br from-[#3F2B0D] to-[#F54900] rounded-2xl p-8 text-white shadow-xl relative overflow-hidden">
                        <div class="relative z-10">
                            <div class="flex items-center gap-3 mb-4">
                                <img src="/images/icon-vendor.png" alt="gambar relawan" class="w-8 h-8 invert brightness-0 saturate-100" />
                                <h2 class="text-2xl font-bold">Jadilah Relawan</h2>
                            </div>
                            <p class="text-[#FFF7ED] mb-6 text-lg leading-relaxed">
                                Bergabunglah dengan kami untuk membantu sesama dan membuat perubahan nyata di masyarakat.
                            </p>
                            <Link :href="route('relawan.create')" class="inline-block bg-white text-[#F54900] px-6 py-3 rounded-xl font-bold hover:bg-gray-100 transition shadow-md">
                                Daftar Sekarang
                            </Link>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                        <h3 class="text-2xl font-bold text-[#1E2939] mb-6">Daftar Donatur</h3>
                        
                        <div class="space-y-4 max-h-[600px] overflow-y-auto pr-1">
                            <div v-if="donors.length === 0" class="text-center text-gray-400 py-4">
                                Belum ada donasi terverifikasi.
                            </div>

                            <div v-for="donor in donors" :key="donor.id" 
                                class="flex justify-between items-center p-4 rounded-xl border bg-[#F0FDF4] border-[#B9F8CF]"
                            >
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-[#1E2939]">{{ maskName(donor.name) }}</span>
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <p class="text-sm text-gray-500">{{ formatDate(donor.created_at) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-lg text-[#F54900]">{{ formatRupiah(donor.amount) }}</p>
                                    <p class="text-xs font-medium text-green-600">Terverifikasi</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                            <p class="text-center text-sm text-gray-600">
                                <span class="font-bold">Catatan:</span> Nama donatur akan disensor untuk privasi.
                            </p>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100 space-y-4">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-lg">Progress Keseluruhan</h3>
                            <span class="text-2xl font-bold text-[#F54900]">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-[#AE3400] to-[#FF6900] h-3 rounded-full" style="width: 0%"></div>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Mulai: 20 Des 2025</span>
                            <span>Target: 20 Mar 2026</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <button class="w-full py-4 bg-[#284D13] hover:bg-[#1e3a0e] text-white font-bold rounded-xl transition">
                            Lihat Informasi Penggunaan Dana
                        </button>
                        <button class="w-full py-4 bg-[#4B8729] hover:bg-[#3a691f] text-white font-bold rounded-xl transition">
                            Kabar Terbaru
                        </button>
                    </div>

                </div>
            </div>

            <div class="mt-12 bg-[#FFF9EA] border-2 border-[#FFD59E] rounded-2xl p-8 shadow-sm">
                <h3 class="text-xl font-bold text-[#2D3748] mb-4">Proses Verifikasi Donasi</h3>
                <ol class="list-decimal list-inside space-y-2 text-[#4A5568] font-medium text-lg">
                    <li>Isi form donasi dengan nama dan jumlah yang ingin didonasikan.</li>
                    <li>Scan QRIS dan transfer sesuai jumlah donasi yang diisikan.</li>
                    <li>Klik "Sudah Transfer" setelah pembayaran selesai.</li>
                    <li>Admin akan memverifikasi pembayaran Anda (maksimal 1x24 jam).</li>
                    <li>Setelah terverifikasi, donasi Anda akan masuk ke total donasi terkumpul.</li>
                </ol>
            </div>
        </div>
    </div>
</template>