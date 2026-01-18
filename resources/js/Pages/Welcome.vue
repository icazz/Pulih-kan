<script setup>
import { Head, Link, usePage } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";
import { computed } from 'vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <Head title="Welcome" />

    <Navbar />

    <div
        id="hero"
        class="relative min-h-screen w-full font-['Montserrat'] overflow-hidden flex items-center"
    >
        <div class="absolute inset-0 z-0">
            <img
                src="/images/landing-bg.jpg"
                alt="Latar Belakang Banjir"
                class="w-full h-full object-cover"
            />
        </div>

        <div
            class="absolute inset-0 z-10 bg-gradient-to-r from-[#854d28] via-[#3a200d] to-black/60 opacity-70"
        ></div>

        <main class="relative z-20 w-full px-4 sm:px-6 lg:px-20 pt-20">
            <div class="max-w-4xl">
                <div
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gradient-to-r from-[#BB4D00] to-[#973C00] text-white text-xs font-bold tracking-wide shadow-lg mb-6 border border-white/20"
                >
                    <img
                        src="/images/icon-clock.png"
                        alt="Jam"
                        class="w-3.5 h-3.5 invert brightness-0 saturate-100"
                    />
                    <span>Platform Tanggap Darurat 24/7</span>
                </div>

                <h1
                    class="text-7xl font-extrabold text-[#FFFFFF] mb-4 drop-shadow-xl leading-tight"
                >
                    Pulih.kan
                </h1>

                <h2
                    class="text-3xl font-bold italic text-[#E8EFC5] mb-4 leading-snug drop-shadow-lg"
                >
                    Pemulihan Rumah Pasca Banjir, Lebih Cepat<br />
                    dan Terkoordinasi
                </h2>

                <p
                    class="text-lg font-medium text-[#FEF3C6] max-w-2xl mb-10 leading-relaxed drop-shadow-md"
                >
                    Temukan mitra profesional untuk restorasi kerusakan air,
                    rekonstruksi, dan pemulihan properti Anda dengan cepat dan
                    mudah.
                </p>

                <div class="flex flex-wrap gap-4">
                    <Link
                        :href="user ? route('reports.index') : route('login')"
                        class="flex items-center gap-3 px-8 py-4 rounded-lg bg-gradient-to-r from-[#BB4D00] to-[#973C00] text-white font-bold text-sm shadow-xl hover:scale-105 transition-transform duration-300 border border-white/10"
                    >
                        <img src="/images/icon-phone.png" alt="Phone" class="w-5 h-5 invert brightness-0 saturate-100" />
                        <span>Ajukan Pemulihan Rumah</span>
                    </Link>

                    <Link
                        v-if="$page.props.auth.user && $page.props.auth.user.vendor"
                        :href="route('vendor.dashboard')"
                        class="flex items-center gap-3 px-8 py-4 rounded-lg bg-gradient-to-r from-[#44403B] to-[#292524] text-white font-bold text-sm shadow-xl hover:scale-105 transition-transform duration-300 border border-white/10"
                    >
                        <img src="/images/icon-hand.png" alt="Hand" class="w-5 h-5 invert brightness-0 saturate-100" />
                        <span>Dashboard Mitra</span>
                    </Link>

                    <Link
                        v-if="$page.props.auth.user && $page.props.auth.user.vendor && $page.props.auth.user.vendor.status !== 'rejected'"
                        :href="route('vendor.dashboard')"
                        class="flex items-center gap-3 px-8 py-4 rounded-lg bg-gradient-to-r from-[#44403B] to-[#292524] text-white font-bold text-sm shadow-xl hover:scale-105 transition-transform duration-300 border border-white/10"
                    >
                        <img src="/images/icon-hand.png" alt="Hand" class="w-5 h-5 invert brightness-0 saturate-100" />
                        <span>Dashboard Mitra</span>
                    </Link>

                    <Link
                        v-else-if="$page.props.auth.user && (!$page.props.auth.user.vendor || $page.props.auth.user.vendor.status === 'rejected')"
                        :href="route('vendor.register')"
                        class="flex items-center gap-3 px-8 py-4 rounded-lg bg-gradient-to-r from-[#44403B] to-[#292524] text-white font-bold text-sm shadow-xl hover:scale-105 transition-transform duration-300 border border-white/10"
                    >
                        <img src="/images/icon-hand.png" alt="Hand" class="w-5 h-5 invert brightness-0 saturate-100" />
                        <span>
                            {{ $page.props.auth.user.vendor?.status === 'rejected' ? 'Daftar Ulang Mitra' : 'Dashboard Mitra' }}
                        </span>
                    </Link>
                </div>
            </div>
        </main>
    </div>

    <section
        class="w-full pt-16 pb-48 bg-gradient-to-r from-[#28160A] via-[#000000] to-[#2D190D] text-white font-['Montserrat'] relative z-0"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div
                class="flex flex-col md:flex-row justify-center items-center gap-16 md:gap-40 text-center"
            >
                <div class="flex flex-col items-center group">
                    <div
                        class="mb-4 p-3 rounded-full bg-white/5 group-hover:bg-white/10 transition-colors duration-300"
                    >
                        <img
                            src="/images/icon-vendor.png"
                            alt="Vendor Icon"
                            class="w-10 h-10 invert brightness-0 saturate-100"
                        />
                    </div>
                    <h3 class="text-xl font-semibold mb-2 tracking-wide">
                        Mitra Terdaftar
                    </h3>
                    <p class="text-gray-300 text-sm font-light">
                        200+ Mitra Berpengalaman
                    </p>
                </div>

                <div class="hidden md:block w-px h-16 bg-white/20"></div>

                <div class="flex flex-col items-center group">
                    <div
                        class="mb-4 p-3 rounded-full bg-white/5 group-hover:bg-white/10 transition-colors duration-300"
                    >
                        <img
                            src="/images/icon-location.png"
                            alt="Location Icon"
                            class="w-10 h-10 invert brightness-0 saturate-100"
                        />
                    </div>
                    <h3 class="text-xl font-semibold mb-2 tracking-wide">
                        Area Layanan
                    </h3>
                    <p class="text-gray-300 text-sm font-light">
                        Darurat Bencana Banjir Indonesia
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section
        id="layanan"
        class="-mt-24 relative z-10 w-full rounded-t-[5rem] bg-gradient-to-bl from-[#2D1505] to-[#FF6500] px-4 sm:px-6 lg:px-20 pt-24 pb-80 font-['Montserrat']"
    >
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2
                    class="text-4xl md:text-5xl font-bold text-[#FFEADB] mb-4 drop-shadow-md"
                >
                    Layanan Mitra Kami
                </h2>
                <p
                    class="text-[#FFFFFF] text-lg font-medium max-w-2xl mx-auto leading-relaxed"
                >
                    Temukan mitra profesional untuk setiap kebutuhan restorasi
                    kerusakan banjir Anda.
                </p>
            </div>

            <div class="flex flex-wrap justify-center gap-8">
                <div
                    class="bg-white rounded-3xl p-8 w-full md:w-[350px] hover:-translate-y-2 transition-transform duration-300 shadow-xl"
                >
                    <div
                        class="w-14 h-14 rounded-xl bg-gradient-to-br from-[#FEF3C6] to-[#F5F5F4] flex items-center justify-center mb-6 shadow-sm"
                    >
                        <img
                            src="/images/icon-air.png"
                            alt="Air"
                            class="w-8 h-8 object-contain"
                        />
                    </div>
                    <h3 class="text-[#1C1917] text-xl font-bold mb-3">
                        Perbaikan Instalasi Air
                    </h3>
                    <p class="text-[#57534D] text-sm leading-relaxed">
                        Perbaikan pipa, saluran air, dan sistem distribusi air bersih yang rusak agar aliran air kembali lancar dan higienis.
                    </p>
                </div>
                <div
                    class="bg-white rounded-3xl p-8 w-full md:w-[350px] hover:-translate-y-2 transition-transform duration-300 shadow-xl"
                >
                    <div
                        class="w-14 h-14 rounded-xl bg-gradient-to-br from-[#FEF3C6] to-[#F5F5F4] flex items-center justify-center mb-6 shadow-sm"
                    >
                        <img
                            src="/images/icon-listrik.png"
                            alt="Listrik"
                            class="w-8 h-8 object-contain"
                        />
                    </div>
                    <h3 class="text-[#1C1917] text-xl font-bold mb-3">
                        Perbaikan Instalasi Listrik
                    </h3>
                    <p class="text-[#57534D] text-sm leading-relaxed">
                        Pemeriksaan dan perbaikan sistem kelistrikan yang terdampak untuk mencegah korsleting dan aman digunakan.
                    </p>
                </div>
                <div
                    class="bg-white rounded-3xl p-8 w-full md:w-[350px] hover:-translate-y-2 transition-transform duration-300 shadow-xl"
                >
                    <div
                        class="w-14 h-14 rounded-xl bg-gradient-to-br from-[#FEF3C6] to-[#F5F5F4] flex items-center justify-center mb-6 shadow-sm"
                    >
                        <img
                            src="/images/icon-rekonstruksi.png"
                            alt="Rekonstruksi"
                            class="w-8 h-8 object-contain"
                        />
                    </div>
                    <h3 class="text-[#1C1917] text-xl font-bold mb-3">
                        Perbaikan Atap Bocor / Rusak
                    </h3>
                    <p class="text-[#57534D] text-sm leading-relaxed">
                        Mitra terpercaya untuk memperbaiki kerusakan struktural atap secara menyeluruh
                    </p>
                </div>
                <div
                    class="bg-white rounded-3xl p-8 w-full md:w-[350px] hover:-translate-y-2 transition-transform duration-300 shadow-xl"
                >
                    <div
                        class="w-14 h-14 rounded-xl bg-gradient-to-br from-[#FEF3C6] to-[#F5F5F4] flex items-center justify-center mb-6 shadow-sm"
                    >
                        <img
                            src="/images/icon-pembersihan.png"
                            alt="Pembersihan"
                            class="w-8 h-8 object-contain"
                        />
                    </div>
                    <h3 class="text-[#1C1917] text-xl font-bold mb-3">
                        Pembersihan Lumpur dan Puing
                    </h3>
                    <p class="text-[#57534D] text-sm leading-relaxed">
                        Proses pengangkatan lumpur, sampah, dan sisa material banjir agar area kembali aman serta siap digunakan.
                    </p>
                </div>
                <div
                    class="bg-white rounded-3xl p-8 w-full md:w-[350px] hover:-translate-y-2 transition-transform duration-300 shadow-xl"
                >
                    <div
                        class="w-14 h-14 rounded-xl bg-gradient-to-br from-[#FEF3C6] to-[#F5F5F4] flex items-center justify-center mb-6 shadow-sm"
                    >
                        <img
                            src="/images/icon-sanitasi.png"
                            alt="Sanitasi"
                            class="w-8 h-8 object-contain"
                        />
                    </div>
                    <h3 class="text-[#1C1917] text-xl font-bold mb-3">
                        Sanitasi dan Desinfeksi Rumah
                    </h3>
                    <p class="text-[#57534D] text-sm leading-relaxed">
                        Pembersihan profesional untuk sanitasi dan deodorisasi menyeluruh properti.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section
        id="proses"
        class="-mt-48 relative z-20 w-full rounded-t-[5rem] bg-gradient-to-tl from-[#231D14] from-25% via-[#98BF65] to-[#DCFFB6] px-4 sm:px-6 lg:px-20 pt-32 pb-80 font-['Montserrat']"
    >
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20">
                <h2
                    class="text-4xl md:text-5xl font-bold text-[#1C1917] mb-4 drop-shadow-sm"
                >
                    Cara Kerja Platform
                </h2>
                <p class="text-[#FAFFE6] text-lg font-medium max-w-2xl mx-auto">
                    Tiga langkah mudah untuk menemukan mitra dan memulihkan
                    properti Anda.
                </p>
            </div>

            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative"
            >
                <div
                    class="hidden lg:block absolute top-1/2 left-0 w-full h-1 bg-gradient-to-r from-[#FFD230] to-[#D6D3D1] -z-10 transform -translate-y-1/2 rounded-full opacity-80"
                ></div>

                <div
                    class="bg-[#0E2B0C] rounded-3xl p-10 shadow-2xl hover:-translate-y-2 transition-transform duration-300 h-full min-h-[450px] flex flex-col justify-between"
                >
                    <div>
                        <span
                            class="text-6xl font-bold mb-6 block bg-gradient-to-r from-[#FEE685] to-[#E7E5E4] bg-clip-text text-transparent"
                            >01</span
                        >
                        <div class="mb-8">
                            <img
                                src="/images/icon-step-1.png"
                                alt="Cari"
                                class="w-16 h-16 object-contain"
                            />
                        </div>
                        <h3 class="text-[#FFE7D6] text-2xl font-bold mb-4">
                            Isi Data Rumah
                        </h3>
                        <p
                            class="text-[#F5F5F5] text-base leading-relaxed opacity-90"
                        >
                            Lengkapi informasi rumah dan wilayah terdampak untuk
                            memulai proses pengajuan dengan akurat.
                        </p>
                    </div>
                </div>

                <div
                    class="bg-[#47622A] rounded-3xl p-10 shadow-2xl hover:-translate-y-2 transition-transform duration-300 h-full min-h-[450px] flex flex-col justify-between"
                >
                    <div>
                        <span
                            class="text-6xl font-bold mb-6 block bg-gradient-to-r from-[#FEE685] to-[#E7E5E4] bg-clip-text text-transparent"
                            >02</span
                        >
                        <div class="mb-8">
                            <img
                                src="/images/icon-step-2.png"
                                alt="Unggah"
                                class="w-16 h-16 object-contain"
                            />
                        </div>
                        <h3 class="text-[#FFE7D6] text-2xl font-bold mb-4">
                            Unggah Kondisi
                        </h3>
                        <p
                            class="text-[#FFFFFF] text-base leading-relaxed opacity-90"
                        >
                            Unggah foto/video kondisi kerusakan rumah saat ini agar kami
                            dapat melakukan penilaian awal yang tepat.
                        </p>
                    </div>
                </div>

                <div
                    class="bg-[#C6F48B] rounded-3xl p-10 shadow-2xl hover:-translate-y-2 transition-transform duration-300 h-full min-h-[450px] flex flex-col justify-between"
                >
                    <div>
                        <span
                            class="text-6xl font-bold mb-6 block bg-gradient-to-r from-[#5F4C3E] to-[#867F7B] bg-clip-text text-transparent"
                            >03</span
                        >
                        <div class="mb-8">
                            <img
                                src="/images/icon-step-3.png"
                                alt="Pengecekan"
                                class="w-16 h-16 object-contain"
                            />
                        </div>
                        <h3 class="text-[#24340E] text-2xl font-bold mb-4">
                            Pengecekan
                        </h3>
                        <p
                            class="text-[#47622A] text-base leading-relaxed font-medium"
                        >
                            Tim akan mengecek tingkat kerusakan dan memberikan
                            estimasi biaya perbaikan serta rekomendasi layanan.
                        </p>
                    </div>
                </div>

                <div
                    class="bg-[#E6FFCB] rounded-3xl p-10 shadow-2xl hover:-translate-y-2 transition-transform duration-300 h-full min-h-[450px] flex flex-col justify-between"
                >
                    <div>
                        <span
                            class="text-6xl font-bold mb-6 block bg-gradient-to-r from-[#4F3726] to-[#9A9A9A] bg-clip-text text-transparent"
                            >04</span
                        >
                        <div class="mb-8">
                            <img
                                src="/images/icon-step-4.png"
                                alt="Vendor"
                                class="w-16 h-16 object-contain"
                            />
                        </div>
                        <h3 class="text-[#1C1917] text-2xl font-bold mb-4">
                            Mitra Siap Hadir
                        </h3>
                        <p
                            class="text-[#57534D] text-base leading-relaxed font-medium"
                        >
                            Setelah pembayaran, layanan akan segera hadir dan
                            memperbaiki properti sesuai permintaan client.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section
        id="mitra"
        class="-mt-24 relative z-30 w-full rounded-t-[5rem] bg-gradient-to-r from-[#000000] via-[#53451F] to-[#47622A] px-4 sm:px-6 lg:px-20 py-32 font-['Montserrat']"
    >
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20">
                <h2
                    class="text-4xl md:text-5xl font-bold text-[#E7E9E5] mb-4 drop-shadow-md"
                >
                    Bergabung Sebagai Mitra
                </h2>
                <p class="text-[#FFFFFF] text-lg font-medium max-w-2xl mx-auto">
                    Kembangkan bisnis restorasi Anda dengan bergabung dalam
                    jaringan mitra terpercaya kami.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div
                    class="bg-white rounded-2xl p-8 border border-[#E7E5E4] shadow-lg hover:-translate-y-2 transition-transform duration-300"
                >
                    <div class="mb-6">
                        <img
                            src="/images/icon-mitra-1.png"
                            alt="Icon"
                            class="w-14 h-14 object-contain"
                        />
                    </div>
                    <h3 class="text-[#1C1917] text-xl font-bold mb-3">
                        Jangkauan Luas
                    </h3>
                    <p class="text-[#57534D] text-sm leading-relaxed">
                        Akses ke ribuan pelanggan potensial yang membutuhkan
                        layanan perbaikan banjir.
                    </p>
                </div>

                <div
                    class="bg-white rounded-2xl p-8 border border-[#E7E5E4] shadow-lg hover:-translate-y-2 transition-transform duration-300"
                >
                    <div class="mb-6">
                        <img
                            src="/images/icon-mitra-2.png"
                            alt="Icon"
                            class="w-14 h-14 object-contain"
                        />
                    </div>
                    <h3 class="text-[#1C1917] text-xl font-bold mb-3">
                        Tingkatkan Bisnis
                    </h3>
                    <p class="text-[#57534D] text-sm leading-relaxed">
                        Platform kami membantu meningkatkan visibilitas dan
                        pertumbuhan bisnis Anda.
                    </p>
                </div>

                <div
                    class="bg-white rounded-2xl p-8 border border-[#E7E5E4] shadow-lg hover:-translate-y-2 transition-transform duration-300"
                >
                    <div class="mb-6">
                        <img
                            src="/images/icon-mitra-3.png"
                            alt="Icon"
                            class="w-14 h-14 object-contain"
                        />
                    </div>
                    <h3 class="text-[#1C1917] text-xl font-bold mb-3">
                        Verifikasi & Kepercayaan
                    </h3>
                    <p class="text-[#57534D] text-sm leading-relaxed">
                        Badge verifikasi meningkatkan kredibilitas dan
                        kepercayaan pelanggan.
                    </p>
                </div>

                <div
                    class="bg-white rounded-2xl p-8 border border-[#E7E5E4] shadow-lg hover:-translate-y-2 transition-transform duration-300"
                >
                    <div class="mb-6">
                        <img
                            src="/images/icon-mitra-4.png"
                            alt="Icon"
                            class="w-14 h-14 object-contain"
                        />
                    </div>
                    <h3 class="text-[#1C1917] text-xl font-bold mb-3">
                        Manajemen Mudah
                    </h3>
                    <p class="text-[#57534D] text-sm leading-relaxed">
                        Dashboard vendor untuk mengelola proyek, klien, dan
                        pembayaran dengan mudah.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section
        class="w-full bg-[#180D06] px-4 sm:px-6 lg:px-20 py-24 font-['Montserrat']"
    >
        <div class="max-w-4xl mx-auto text-center">
            <h2
                class="text-3xl md:text-4xl font-bold text-white mb-6 drop-shadow-md"
            >
                Siap Menjadi Bagian dari Solusi?
            </h2>

            <p
                class="text-[#E7E5E4] text-lg font-medium leading-relaxed mb-10 max-w-2xl mx-auto"
            >
                Daftar sekarang dan mulai menerima permintaan proyek dari klien
                yang membutuhkan layanan Anda.
            </p>

            <div
                class="flex flex-col sm:flex-row justify-center items-center gap-4"
            >
                <Link
                    :href="user ? route('vendor.register') : route('login')"
                    class="px-8 py-4 rounded-lg bg-[#FFFFFF] text-[#7B3306] font-bold text-base shadow-lg hover:bg-gray-100 hover:scale-105 transition-all duration-300 w-full sm:w-auto text-center"
                >
                    Daftar Sebagai Mitra
                </Link>

                <Link
                    :href="user ? route('about.us') : route('login')"
                    class="px-8 py-4 rounded-lg border-2 border-[#A89078] text-white font-semibold text-base hover:bg-[#A89078]/20 transition-all duration-300 w-full sm:w-auto text-center"
                >
                    Pelajari Lebih Lanjut
                </Link>
            </div>
        </div>
    </section>

    <section
        id="testimoni"
        class="w-full bg-gradient-to-br from-[#7B3306] via-[#292524] to-[#461901] px-4 sm:px-6 lg:px-20 py-32 font-['Montserrat']"
    >
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20">
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-4 drop-shadow-md">
                    Apa Kata Klien Kami
                </h2>
                <p class="text-[#D6D3D1] text-lg font-medium max-w-2xl mx-auto">
                    Dipercaya oleh ratusan pemilik rumah dan bisnis di saat mereka membutuhkan bantuan.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <div class="rounded-2xl p-6 bg-[#44403B]/30 border border-[#BB4D00]/50 backdrop-blur-sm hover:transform hover:-translate-y-1 transition-all duration-300">
                    <div class="flex gap-1 mb-6 h-48 overflow-hidden rounded-lg relative">
                        <div class="w-1/2 h-full relative group">
                            <img src="/images/before-1.jpg" alt="Before" class="w-full h-full object-cover brightness-75 group-hover:brightness-100 transition duration-500">
                            <span class="absolute top-2 left-2 bg-red-600 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm z-10">Sebelum</span>
                        </div>
                        <div class="w-1/2 h-full relative group">
                            <img src="/images/after-1.jpg" alt="After" class="w-full h-full object-cover brightness-75 group-hover:brightness-100 transition duration-500">
                            <span class="absolute top-2 left-2 bg-yellow-600 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm z-10">Sesudah</span>
                        </div>
                    </div>

                    <div class="flex gap-1 mb-4 text-[#FFD700]">
                        <span v-for="n in 5" :key="n" class="text-lg">★</span>
                    </div>

                    <p class="text-[#E7E5E4] text-sm leading-relaxed mb-6 italic min-h-[80px]">
                        "Setelah rumah kami banjir, mereka datang dalam 2 jam. Profesional, efisien, dan sangat membantu. Mereka membersihkan hingga rumah kami kembali seperti sebelum banjir."
                    </p>

                    <div>
                        <h4 class="text-[#F5F5F4] font-bold text-base">Sari Dewi</h4>
                        <p class="text-[#A6A09B] text-xs">Kalimantan Selatan</p>
                    </div>
                </div>

                <div class="rounded-2xl p-6 bg-[#44403B]/30 border border-[#BB4D00]/50 backdrop-blur-sm hover:transform hover:-translate-y-1 transition-all duration-300">
                    <div class="flex gap-1 mb-6 h-48 overflow-hidden rounded-lg relative">
                        <div class="w-1/2 h-full relative group">
                            <img src="/images/before-2.jpg" alt="Before" class="w-full h-full object-cover brightness-75 group-hover:brightness-100 transition duration-500">
                            <span class="absolute top-2 left-2 bg-red-600 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm z-10">Sebelum</span>
                        </div>
                        <div class="w-1/2 h-full relative group">
                            <img src="/images/after-2.png" alt="After" class="w-full h-full object-cover brightness-75 group-hover:brightness-100 transition duration-500">
                            <span class="absolute top-2 left-2 bg-yellow-600 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm z-10">Sesudah</span>
                        </div>
                    </div>

                    <div class="flex gap-1 mb-4 text-[#FFD700]">
                        <span v-for="n in 5" :key="n" class="text-lg">★</span>
                    </div>

                    <p class="text-[#E7E5E4] text-sm leading-relaxed mb-6 italic min-h-[80px]">
                        "Layanan luar biasa dari awal hingga akhir. Mereka memperbaiki kerusakan atap akibat angin dan hujan deras sehingga rumah kami aman ditempati."
                    </p>

                    <div>
                        <h4 class="text-[#F5F5F4] font-bold text-base">Budi Santoso</h4>
                        <p class="text-[#A6A09B] text-xs">Tangerang</p>
                    </div>
                </div>

                <div class="rounded-2xl p-6 bg-[#44403B]/30 border border-[#BB4D00]/50 backdrop-blur-sm hover:transform hover:-translate-y-1 transition-all duration-300">
                    <div class="flex gap-1 mb-6 h-48 overflow-hidden rounded-lg relative">
                        <div class="w-1/2 h-full relative group">
                            <img src="/images/before-3.png" alt="Before" class="w-full h-full object-cover brightness-75 group-hover:brightness-100 transition duration-500">
                            <span class="absolute top-2 left-2 bg-red-600 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm z-10">Sebelum</span>
                        </div>
                        <div class="w-1/2 h-full relative group">
                            <img src="/images/after-3.png" alt="After" class="w-full h-full object-cover brightness-75 group-hover:brightness-100 transition duration-500">
                            <span class="absolute top-2 left-2 bg-yellow-600 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm z-10">Sesudah</span>
                        </div>
                    </div>

                    <div class="flex gap-1 mb-4 text-[#FFD700]">
                        <span v-for="n in 5" :key="n" class="text-lg">★</span>
                    </div>

                    <p class="text-[#E7E5E4] text-sm leading-relaxed mb-6 italic min-h-[80px]">
                        "Mitra vendor sangat paham bagaimana cara mengatasi saluran air yang kotor akibat terisi lumpur. Sekarang kami bisa mandi dan bersih-bersih dengan nyaman."
                    </p>

                    <div>
                        <h4 class="text-[#F5F5F4] font-bold text-base">Linda Wijaya</h4>
                        <p class="text-[#A6A09B] text-xs">Bekasi</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <footer
        id="kontak"
        class="w-full bg-gradient-to-r from-[#1C1917] via-[#461901] to-[#1C1917] pt-20 pb-10 font-['Montserrat'] text-white border-t border-white/5"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16"
            >
                <div>
                    <h3 class="text-2xl font-bold text-white mb-6">
                        Pulih.kan
                    </h3>
                    <p class="text-[#A6A09B] text-sm leading-relaxed">
                        Platform yang menghubungkan korban banjir dengan
                        penyedia jasa pemulihan rumah yang terverifikasi secara
                        cepat, transparan, dan terkoordinasi.
                    </p>
                </div>

                <div>
                    <h4 class="text-lg font-bold text-white mb-6">Kontak</h4>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <svg
                                class="w-5 h-5 text-[#FF8C00] mt-0.5 flex-shrink-0"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                                ></path>
                            </svg>
                            <div>
                                <p class="text-white text-sm font-semibold">
                                    (021) 123-4567
                                </p>
                                <p class="text-[#A6A09B] text-xs">
                                    Senin - Jumat
                                </p>
                            </div>
                        </li>

                        <li class="flex items-center gap-3">
                            <svg
                                class="w-5 h-5 text-[#FF8C00] flex-shrink-0"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                ></path>
                            </svg>
                            <span
                                class="text-[#A6A09B] text-sm hover:text-white transition-colors cursor-pointer"
                                >pulihkanofficial@gmail.com</span
                            >
                        </li>

                        <li class="flex items-center gap-3">
                            <svg
                                class="w-5 h-5 text-[#FF8C00] flex-shrink-0"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M7 2h10a5 5 0 015 5v10a5 5 0 01-5 5H7a5 5 0 01-5-5V7a5 5 0 015-5z"
                                ></path>
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"
                                ></path>
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17.5 6.5h.01"
                                ></path>
                            </svg>
                            <a
                                href="https://www.instagram.com/pulih.kan"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-[#A6A09B] text-sm hover:text-white transition-colors cursor-pointer"
                            >
                                @pulih.kan
                            </a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-bold text-white mb-6">Lokasi</h4>
                    <div class="flex items-start gap-3">
                        <svg
                            class="w-5 h-5 text-[#FF8C00] mt-0.5 flex-shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                            ></path>
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                            ></path>
                        </svg>
                        <div>
                            <p class="text-white text-sm font-semibold">
                                Jl. Pemulihan No. 123
                            </p>
                            <p class="text-[#A6A09B] text-xs">
                                Jakarta Selatan 12345
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-lg font-bold text-white mb-6">
                        Jam Operasional
                    </h4>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <svg
                                class="w-5 h-5 text-[#FF8C00] mt-0.5 flex-shrink-0"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                ></path>
                            </svg>
                            <div>
                                <p class="text-white text-sm font-semibold">
                                    Senin - Jumat: 08:00 - 18:00
                                </p>
                                <p class="text-[#A6A09B] text-xs">
                                    Sabtu: 09:00 - 15:00
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-white/10 pt-8 text-center">
                <p class="text-[#A6A09B] text-sm">
                    &copy; 2026 Pulih.kan. Hak cipta dilindungi.
                </p>
            </div>
        </div>
    </footer>
</template>
<style>
html {
    scroll-behavior: smooth;
}
</style>