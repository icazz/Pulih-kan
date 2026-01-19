<script setup>
import { Head } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";
</script>

<template>
    <Head title="Daftar Relawan" />
    <Navbar />

    <div class="min-h-screen bg-[#FFFBEA] relative font-['Montserrat']">
        
        <div class="relative w-full h-[500px]">
            <img src="/images/banner-relawan.jpg" alt="Relawan" class="w-full h-full object-cover brightness-50" />
            
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center text-white pt-20">
                <p class="text-2xl md:text-4xl font-extrabold mb-4">Selamat Datang! Daftarkan diri Anda</p>
                <h1 class="text-5xl md:text-7xl font-extrabold leading-tight">Bersama Pulihkan,<br>Jadi Relawan</h1>
                <p class="mt-4 text-lg md:text-xl font-medium">Ambil peran nyata dalam proses pemulihan pasca bencana</p>
            </div>
        </div>

        <div class="relative z-10 mt-16 max-w-5xl mx-auto bg-gradient-to-b from-[#FFFBEA] to-[#99968C] rounded-[50px] md:rounded-[96px] p-8 md:p-16 shadow-2xl mb-20">
            
            <form class="space-y-12">
                
                <div>
                    <h2 class="text-3xl font-bold text-black mb-8 border-l-4 border-[#000] pl-4">Data Diri</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <input type="text" placeholder="Nama Lengkap" class="w-full px-6 py-4 rounded-[20px] bg-transparent border border-black/60 placeholder-black/70 focus:bg-white transition text-lg" />
                        </div>
                        <div>
                            <input type="email" placeholder="Email" class="w-full px-6 py-4 rounded-[20px] bg-transparent border border-black/60 placeholder-black/70 focus:bg-white transition text-lg" />
                        </div>
                        <div>
                            <input type="text" placeholder="Nomor Telepon" class="w-full px-6 py-4 rounded-[20px] bg-transparent border border-black/60 placeholder-black/70 focus:bg-white transition text-lg" />
                        </div>
                        <div>
                            <input type="text" placeholder="Kontak Darurat" class="w-full px-6 py-4 rounded-[20px] bg-transparent border border-black/60 placeholder-black/70 focus:bg-white transition text-lg" />
                        </div>
                        <div class="relative">
                            <select class="w-full px-6 py-4 rounded-[20px] bg-transparent border border-black/60 text-black/70 focus:bg-white transition text-lg appearance-none cursor-pointer">
                                <option>Provinsi Asal</option>
                            </select>
                            <div class="absolute right-6 top-5 pointer-events-none">▼</div>
                        </div>
                        <div class="relative">
                            <select class="w-full px-6 py-4 rounded-[20px] bg-transparent border border-black/60 text-black/70 focus:bg-white transition text-lg appearance-none cursor-pointer">
                                <option>Kecamatan / Kota Asal</option>
                            </select>
                            <div class="absolute right-6 top-5 pointer-events-none">▼</div>
                        </div>
                        <div class="md:col-span-2">
                            <input type="text" placeholder="Alamat Lengkap" class="w-full px-6 py-4 rounded-[20px] bg-transparent border border-black/60 placeholder-black/70 focus:bg-white transition text-lg" />
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-3xl font-bold text-black mb-8 border-l-4 border-[#000] pl-4">Peran & Keahlian</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="relative">
                            <select class="w-full px-6 py-4 rounded-[20px] bg-transparent border border-black/60 text-black/70 focus:bg-white transition text-lg appearance-none cursor-pointer">
                                <option>Peran Relawan (Logistik, Medis, dll)</option>
                            </select>
                            <div class="absolute right-6 top-5 pointer-events-none">▼</div>
                        </div>
                        <div>
                            <input type="text" placeholder="Pengalaman (Tahun/Deskripsi)" class="w-full px-6 py-4 rounded-[20px] bg-transparent border border-black/60 placeholder-black/70 focus:bg-white transition text-lg" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4 p-4 border border-[#4F3726] rounded-lg bg-white/50">
                    <input type="checkbox" id="agree" class="w-6 h-6 text-[#4F3726] border-2 border-[#4F3726] rounded focus:ring-[#4F3726]" />
                    <label for="agree" class="text-lg text-black">Saya bersedia mengikuti aturan dan arahan tim Pulih.Kan</label>
                </div>

                <button type="submit" class="w-full py-5 bg-[#4F3726] hover:bg-[#3d2b1d] text-white font-bold text-xl rounded-[30px] shadow-lg transition transform hover:scale-[1.01]">
                    Kirim untuk Verifikasi
                </button>

                <p class="text-center text-[#4F3726]/80 italic text-lg px-4">
                    Data akan diverifikasi oleh admin sebelum akun vendor diaktifkan. Verifikasi akan dilakukan selama 3 - 5 hari. Lihat halaman ini kembali untuk mengecek status verifikasi.
                </p>

                <div class="bg-[#7D91E6] rounded-[30px] py-4 text-center shadow-md">
                    <p class="text-white font-bold text-2xl">Menunggu Verifikasi</p>
                </div>

            </form>
        </div>
    </div>
</template>