<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";
import { ref } from 'vue';

// Dummy Data untuk Tampilan
const donors = ref([
    { name: "B**********o", date: "15/1/2026", amount: 500000, verified: true },
    { name: "A********a", date: "16/1/2026", amount: 250000, verified: true },
    { name: "C********i", date: "17/1/2026", amount: 1000000, verified: true },
    { name: "D******", date: "18/1/2026", amount: 150000, verified: false },
]);

const amount = ref(0);
const setAmount = (val) => amount.value = val;
</script>

<template>
    <Head title="Mari Berbagi Kebaikan" />
    <Navbar />

    <div class="min-h-screen bg-[#FFF7ED] font-['Arimo'] pb-20">
        
        <div class="relative w-full h-[400px] bg-gradient-to-r from-[#481313] to-[#AE2E2E] overflow-hidden pt-32 px-6">
             <div class="absolute inset-0 bg-black/20"></div>
            
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
                            <p class="text-5xl font-bold mb-2">Rp 1.750.000</p>
                            <p class="text-[#FFEDD4]">Dari 3 donatur</p>
                        </div>
                        <div class="absolute right-0 top-0 opacity-10">
                            <svg width="200" height="200" viewBox="0 0 200 200" fill="white"><circle cx="150" cy="50" r="100"/></svg>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                        <h3 class="text-2xl font-bold text-[#1E2939] mb-6">Form Donasi</h3>
                        
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-[#364153] mb-2">Nama Lengkap</label>
                                <input type="text" placeholder="Masukkan nama Anda" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-[#F54900] focus:ring-0 transition" />
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-[#364153] mb-2">Jumlah Donasi</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-3.5 text-gray-500">Rp</span>
                                    <input v-model="amount" type="number" class="w-full pl-12 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-[#F54900] focus:ring-0 transition font-bold text-gray-700" />
                                </div>
                            </div>

                            <div class="grid grid-cols-4 gap-2">
                                <button @click="setAmount(50000)" class="py-2 border-2 border-[#FFD6A7] rounded-lg text-[#F54900] hover:bg-[#FFF4E2] font-medium text-sm transition">50.000</button>
                                <button @click="setAmount(100000)" class="py-2 border-2 border-[#FFD6A7] rounded-lg text-[#F54900] hover:bg-[#FFF4E2] font-medium text-sm transition">100.000</button>
                                <button @click="setAmount(250000)" class="py-2 border-2 border-[#FFD6A7] rounded-lg text-[#F54900] hover:bg-[#FFF4E2] font-medium text-sm transition">250.000</button>
                                <button @click="setAmount(500000)" class="py-2 border-2 border-[#FFD6A7] rounded-lg text-[#F54900] hover:bg-[#FFF4E2] font-medium text-sm transition">500.000</button>
                            </div>

                            <div class="bg-[#FFF4E2] border-2 border-[#FFD9AE] rounded-2xl p-6 text-center">
                                <h4 class="font-bold text-lg mb-4">Scan QRIS untuk Pembayaran</h4>
                                <div class="bg-white p-4 rounded-xl inline-block shadow-sm mb-4">
                                    <div class="w-48 h-48 bg-gray-200 flex items-center justify-center text-gray-400">
                                        [QRIS CODE IMG]
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <label class="block text-left text-sm font-medium mb-2">Upload Bukti Pembayaran</label>
                                    <div class="border-2 border-dashed border-gray-400 rounded-xl p-6 bg-white cursor-pointer hover:bg-gray-50 transition text-center">
                                        <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                        <p class="text-sm text-gray-600">Klik untuk upload atau drag & drop</p>
                                        <p class="text-xs text-gray-400">PNG, JPG hingga 5MB</p>
                                    </div>
                                </div>
                            </div>

                            <button class="w-full py-4 bg-[#73C444] hover:bg-[#5da335] text-white font-bold rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                Sudah Transfer
                            </button>
                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    
                    <div class="bg-gradient-to-br from-[#3F2B0D] to-[#F54900] rounded-2xl p-8 text-white shadow-xl relative overflow-hidden">
                        <div class="relative z-10">
                            <h2 class="text-2xl font-bold mb-2">Jadilah Relawan</h2>
                            <p class="text-[#FFF7ED] mb-6 text-lg leading-relaxed">
                                Bergabunglah dengan kami untuk membantu sesama dan membuat perubahan nyata di masyarakat.
                            </p>
                            
                            <Link :href="route('relawan')" class="inline-block bg-white text-[#F54900] px-6 py-3 rounded-xl font-bold hover:bg-gray-100 transition shadow-md">
                                Daftar Sekarang
                            </Link>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                        <h3 class="text-2xl font-bold text-[#1E2939] mb-6">Daftar Donatur</h3>
                        
                        <div class="space-y-4">
                            <div v-for="(donor, index) in donors" :key="index" 
                                class="flex justify-between items-center p-4 rounded-xl border"
                                :class="donor.verified ? 'bg-[#F0FDF4] border-[#B9F8CF]' : 'bg-[#FEFCE8] border-[#FFF085]'"
                            >
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-[#1E2939]">{{ donor.name }}</span>
                                        <svg v-if="donor.verified" class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <svg v-else class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <p class="text-sm text-gray-500">{{ donor.date }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-lg text-[#F54900]">Rp {{ donor.amount.toLocaleString('id-ID') }}</p>
                                    <p class="text-xs font-medium" :class="donor.verified ? 'text-green-600' : 'text-yellow-600'">
                                        {{ donor.verified ? 'Terverifikasi' : 'Menunggu Verifikasi' }}
                                    </p>
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
        </div>
    </div>
</template>