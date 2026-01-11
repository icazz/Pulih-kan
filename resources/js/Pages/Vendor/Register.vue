<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import Navbar from '@/Components/Navbar.vue';

// --- LEAFLET SETUP ---
import "leaflet/dist/leaflet.css";
import L from "leaflet";

// Fix agar icon default tidak hilang di Vue
import icon from 'leaflet/dist/images/marker-icon.png';
import iconShadow from 'leaflet/dist/images/marker-shadow.png';

let DefaultIcon = L.icon({
    iconUrl: icon,
    shadowUrl: iconShadow,
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});
L.Marker.prototype.options.icon = DefaultIcon;
// ---------------------

const form = useForm({
    nama_usaha: '',
    nama_pemilik: '',
    no_telepon: '',
    nib: '', 
    email: '',
    jenis_vendor: '',
    dokumen_legal: null,
    provinsi: '',
    kota: '',
    alamat: '',
    ketersediaan: '',
    latitude: '',
    longitude: '',
    agreement: false,
});

const submit = () => {
    if (form.nib.length !== 13) {
        alert("Nomor Induk Berusaha harus 13 digit!");
        return;
    }
    console.log("Data dikirim", form);
};

onMounted(() => {
    // 1. Inisialisasi Peta
    const map = L.map('mapContainer').setView([-6.200000, 106.816666], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker;

    // Fungsi update marker
    const updateLocation = (lat, lng) => {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng]).addTo(map);
        }
        map.setView([lat, lng], 16);
        form.latitude = lat;
        form.longitude = lng;
    };

    // 2. Geolocation
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                updateLocation(position.coords.latitude, position.coords.longitude);
            },
            (error) => {
                console.warn("Geolocation error:", error.message);
            }
        );
    }

    // 3. Klik Manual
    map.on('click', (e) => {
        updateLocation(e.latlng.lat, e.latlng.lng);
    });
});
</script>

<template>
    <Head title="Daftar Sebagai Vendor" />

    <div class="min-h-screen w-full bg-gradient-to-b from-[#47622A] to-[#91C856] py-16 px-4 sm:px-6 lg:px-8 font-['Montserrat'] flex flex-col items-center">
        
        <div class="text-center mb-10">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-3 drop-shadow-md">
                Selamat Datang!
            </h1>
            <p class="text-white text-lg font-medium opacity-90">
                Daftarkan diri Anda sebagai mitra layanan Pulih.Kan
            </p>
        </div>

        <div class="w-full max-w-5xl bg-[#FEF8DF] rounded-[3rem] shadow-2xl overflow-hidden">
            
            <div class="p-8 md:p-12">
                <form @submit.prevent="submit">
                    
                    <div class="mb-10">
                        <h2 class="text-2xl font-bold text-[#4F3726] mb-6">Data Identitas Vendor</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <input v-model="form.nama_usaha" type="text" placeholder="Nama Usaha" class="w-full bg-transparent border border-[#A89078] rounded-xl px-5 py-3 text-[#4F3726] placeholder-[#8C8070] focus:ring-2 focus:ring-[#47622A] focus:outline-none">
                            </div>
                            <div class="space-y-2">
                                <input v-model="form.nama_pemilik" type="text" placeholder="Nama Pemilik Usaha" class="w-full bg-transparent border border-[#A89078] rounded-xl px-5 py-3 text-[#4F3726] placeholder-[#8C8070] focus:ring-2 focus:ring-[#47622A] focus:outline-none">
                            </div>
                            <div class="space-y-2">
                                <input v-model="form.no_telepon" type="tel" placeholder="Nomor Telepon" class="w-full bg-transparent border border-[#A89078] rounded-xl px-5 py-3 text-[#4F3726] placeholder-[#8C8070] focus:ring-2 focus:ring-[#47622A] focus:outline-none">
                            </div>
                            <div class="space-y-2">
                                <input v-model="form.nib" type="text" maxlength="13" placeholder="Nomor Induk Berusaha (13 Digit)" class="w-full bg-transparent border border-[#A89078] rounded-xl px-5 py-3 text-[#4F3726] placeholder-[#8C8070] focus:ring-2 focus:ring-[#47622A] focus:outline-none">
                            </div>
                            <div class="space-y-2">
                                <input v-model="form.email" type="email" placeholder="Email" class="w-full bg-transparent border border-[#A89078] rounded-xl px-5 py-3 text-[#4F3726] placeholder-[#8C8070] focus:ring-2 focus:ring-[#47622A] focus:outline-none">
                            </div>
                            
                            <div class="space-y-2">
                                <select v-model="form.jenis_vendor" class="w-full bg-transparent border border-[#A89078] rounded-xl px-5 py-3 text-[#4F3726] focus:ring-2 focus:ring-[#47622A] focus:outline-none cursor-pointer">
                                    <option value="" disabled selected>Jenis Vendor</option>
                                    <option value="konstruksi">Konstruksi</option>
                                    <option value="kebersihan">Kebersihan</option>
                                    <option value="kelistrikan">Kelistrikan</option>
                                </select>
                            </div>

                            <div class="col-span-1 md:col-span-2 space-y-2">
                                <div class="w-full border border-[#A89078] rounded-xl px-5 py-3 flex items-center bg-transparent cursor-pointer hover:bg-black/5 transition">
                                    <span class="text-[#8C8070] flex-grow">Unggah Dokumen Legal Usaha</span>
                                    <input type="file" @input="form.dokumen_legal = $event.target.files[0]" class="hidden" id="fileUpload">
                                    <label for="fileUpload" class="cursor-pointer">
                                        <svg class="w-6 h-6 text-[#8C8070]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-10">
                        <h2 class="text-2xl font-bold text-[#4F3726] mb-6">Area & Lokasi Kerja</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            
                            <div class="space-y-2">
                                <select v-model="form.provinsi" class="w-full bg-transparent border border-[#A89078] rounded-xl px-5 py-3 text-[#4F3726] focus:ring-2 focus:ring-[#47622A] focus:outline-none cursor-pointer">
                                    <option value="" disabled selected>Provinsi</option>
                                    <option value="jawa_timur">Jawa Timur</option>
                                    <option value="jawa_tengah">Jawa Tengah</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <select v-model="form.kota" class="w-full bg-transparent border border-[#A89078] rounded-xl px-5 py-3 text-[#4F3726] focus:ring-2 focus:ring-[#47622A] focus:outline-none cursor-pointer">
                                    <option value="" disabled selected>Kecamatan / Kota</option>
                                    <option value="lamongan">Lamongan</option>
                                    <option value="surabaya">Surabaya</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <input v-model="form.alamat" type="text" placeholder="Alamat Lengkap" class="w-full bg-transparent border border-[#A89078] rounded-xl px-5 py-3 text-[#4F3726] placeholder-[#8C8070] focus:ring-2 focus:ring-[#47622A] focus:outline-none">
                            </div>

                            <div class="space-y-2">
                                <select v-model="form.ketersediaan" class="w-full bg-transparent border border-[#A89078] rounded-xl px-5 py-3 text-[#4F3726] focus:ring-2 focus:ring-[#47622A] focus:outline-none cursor-pointer">
                                    <option value="" disabled selected>Ketersediaan Kerja</option>
                                    <option value="full_time">Full Time</option>
                                    <option value="on_call">On Call (Darurat)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h3 class="text-[#4F3726] font-bold mb-2 flex items-center gap-2">
                                <img src="/images/icon-map-pin.png" alt="Map Pin Icon" class="w-4 h-4">
                                Pilih Titik di Peta
                            </h3>
                            
                            <div id="mapContainer" class="w-full h-64 z-0 rounded-xl overflow-hidden border border-[#A89078] shadow-inner relative">
                                <div class="absolute inset-0 flex items-center justify-center bg-gray-100 z-[-1]">
                                    <span class="text-gray-400 text-sm">Memuat Peta...</span>
                                </div>
                            </div>
                            <p class="text-xs text-[#8C8070] mt-2 italic flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Pastikan Anda mengizinkan akses lokasi di browser.
                            </p>
                        </div>
                    </div>

                    <div class="bg-[#EBE5CB] -mx-8 md:-mx-12 -mb-8 md:-mb-12 p-8 md:p-12 rounded-[3rem]">
                        <h2 class="text-2xl font-bold text-[#4F3726] mb-4">Status & Persetujuan</h2>
                        
                        <div class="flex items-start gap-3 mb-8">
                            <div class="flex items-center h-5">
                                <input id="agreement" v-model="form.agreement" type="checkbox" class="w-5 h-5 border border-[#4F3726] rounded text-[#4F3726] focus:ring-[#4F3726] bg-transparent">
                            </div>
                            <label for="agreement" class="text-[#4F3726] text-sm">
                                Saya menyatakan data yang diberikan benar dan dapat dipertanggungjawabkan
                            </label>
                        </div>

                        <button type="submit" class="w-full bg-[#4F3726] hover:bg-[#3a281c] text-white font-bold py-4 px-6 rounded-2xl shadow-lg transition-transform hover:scale-[1.01] mb-6">
                            Kirim untuk Verifikasi
                        </button>

                        <p class="text-[#4F3726] text-xs text-center italic mb-10 leading-relaxed opacity-80 max-w-2xl mx-auto">
                            Data akan diverifikasi oleh admin sebelum akun vendor diaktifkan. Verifikasi akan dilakukan selama 3 - 5 hari. Lihat halaman ini kembali untuk mengecek status verifikasi.
                        </p>

                        <div>
                            <h3 class="text-2xl font-bold text-[#4F3726] mb-4">Status Verifikasi</h3>
                            <div class="w-full bg-[#39E3EC] text-white text-center font-bold text-xl py-4 rounded-2xl shadow-md cursor-default">
                                Menunggu Verifikasi
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</template>