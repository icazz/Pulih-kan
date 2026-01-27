<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";
</script>

<template>
    <Head title="FAQ - Pusat Bantuan" />

    <div class="bg-[#FAFAF9] text-[#1C1917] font-sans min-h-screen">
        
        <Navbar />

        <div class="bg-gradient-to-r from-[#292524] via-[#461901] to-[#292524] text-white pt-24 pb-16 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full opacity-10 bg-[url('https://www.transparenttextures.com/patterns/wood-pattern.png')]"></div>

            <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 drop-shadow-lg font-['Montserrat']">
                    Pusat Bantuan & FAQ
                </h1>
                <p class="text-[#E7E5E4] text-lg font-medium max-w-2xl mx-auto">
                    Temukan jawaban cepat seputar layanan pemulihan pascabencana di sini.
                </p>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 py-16 space-y-16">
            
            <section>
                <div class="flex items-center gap-3 mb-8">
                    <div class="p-3 bg-[#FFEADB] rounded-full">
                        <span class="text-2xl">👤</span>
                    </div>
                    <h2 class="text-3xl font-bold text-[#7B3306]">
                        Pertanyaan Umum Customer
                    </h2>
                </div>

                <div class="space-y-6">
                    <div class="bg-white p-8 rounded-2xl shadow-lg border border-[#E7E5E4] hover:border-[#BB4D00]/30 transition-all duration-300">
                        <h3 class="font-bold text-xl mb-4 text-[#292524]">
                            1. Bagaimana mengajukan layanan di Pulih.kan?
                        </h3>
                        <div class="text-[#57534D] leading-relaxed pl-6 border-l-4 border-[#BB4D00]">
                            <ol class="list-decimal pl-4 space-y-3">
                                <li><strong>Akses Website:</strong> Login dan masuk ke halaman dashboard.</li>
                                <li><strong>Isi Formulir:</strong> Lengkapi data kondisi kerusakan dan lokasi.</li>
                                <li><strong>Upload Foto:</strong> Lampirkan bukti foto kerusakan untuk verifikasi.</li>
                                <li><strong>Tunggu Mitra:</strong> Sistem akan menghubungkan Anda dengan mitra terdekat.</li>
                            </ol>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-2xl shadow-lg border border-[#E7E5E4] hover:border-[#BB4D00]/30 transition-all duration-300">
                        <h3 class="font-bold text-xl mb-4 text-[#292524]">
                            2. Apakah layanan Pulih.kan berbayar?
                        </h3>
                        <p class="text-[#57534D]">
                            Kami menyediakan <strong>estimasi biaya transparan</strong> di awal. Biaya final disepakati antara Anda dan Mitra melalui fitur negosiasi sebelum pengerjaan dimulai.
                        </p>
                    </div>

                    <div class="bg-white p-8 rounded-2xl shadow-lg border border-[#E7E5E4] hover:border-[#BB4D00]/30 transition-all duration-300">
                        <h3 class="font-bold text-xl mb-4 text-[#292524]">
                            3. Apakah pengerjaan diawasi?
                        </h3>
                        <p class="text-[#57534D]">
                            Tentu. Setiap proyek diawasi oleh <strong>Supervisor Lokal</strong> untuk memastikan kualitas pengerjaan dan keamanan di lokasi.
                        </p>
                    </div>
                </div>
            </section>

            <hr class="border-[#D6D3D1]" />

            <section>
                <div class="flex items-center gap-3 mb-8">
                    <div class="p-3 bg-[#FFEADB] rounded-full">
                        <span class="text-2xl">🛠️</span>
                    </div>
                    <h2 class="text-3xl font-bold text-[#7B3306]">
                        Pertanyaan Umum Mitra
                    </h2>
                </div>

                <div class="space-y-6">
                    <div class="bg-white p-8 rounded-2xl shadow-lg border border-[#E7E5E4] hover:border-[#BB4D00]/30 transition-all duration-300">
                        <h3 class="font-bold text-xl mb-4 text-[#292524]">
                            1. Siapa yang bisa mendaftar jadi Mitra?
                        </h3>
                        <p class="text-[#57534D]">
                            Warga lokal atau tenaga profesional yang memiliki keahlian di bidang pertukangan, kelistrikan, kebersihan, atau konstruksi dan berdomisili di area terdampak.
                        </p>
                    </div>

                    <div class="bg-white p-8 rounded-2xl shadow-lg border border-[#E7E5E4] hover:border-[#BB4D00]/30 transition-all duration-300">
                        <h3 class="font-bold text-xl mb-4 text-[#292524]">
                            2. Bagaimana sistem pembayarannya?
                        </h3>
                        <p class="text-[#57534D]">
                            Pembayaran aman menggunakan sistem <strong>Escrow (Rekening Bersama)</strong>. Dana akan dicairkan ke dompet Mitra segera setelah Customer mengonfirmasi pekerjaan selesai.
                        </p>
                    </div>

                    <div class="bg-white p-8 rounded-2xl shadow-lg border border-[#E7E5E4] hover:border-[#BB4D00]/30 transition-all duration-300">
                        <h3 class="font-bold text-xl mb-4 text-[#292524]">
                            3. Apa syarat verifikasi akun?
                        </h3>
                        <div class="text-[#57534D] space-y-2">
                            <p>Anda wajib melengkapi profil dengan:</p>
                            <ul class="list-disc pl-5">
                                <li>KTP / Identitas diri.</li>
                                <li>Foto diri terbaru.</li>
                                <li>Portofolio atau bukti pengalaman kerja sebelumnya.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

        </div>

        <footer class="bg-[#1C1917] text-white py-8 text-center mt-12 border-t border-[#44403B]">
            <p class="text-[#A6A09B]">&copy; 2026 Pulih.kan</p>
        </footer>
    </div>
</template>