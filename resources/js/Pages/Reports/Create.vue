<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import Navbar from '@/Components/Navbar.vue';

import "leaflet/dist/leaflet.css";
import L from "leaflet";
import icon from 'leaflet/dist/images/marker-icon.png';
import iconShadow from 'leaflet/dist/images/marker-shadow.png';

// Konfigurasi Icon Leaflet
let DefaultIcon = L.icon({
    iconUrl: icon,
    shadowUrl: iconShadow,
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});
L.Marker.prototype.options.icon = DefaultIcon;

const currentStep = ref(1);
const estimasi = ref({ kategori: 'Ringan', biaya: 'Rp 0', layanan: 'Pembersihan Standar' });

// --- REVISI 1: Update Struktur Form sesuai Backend ---
const form = useForm({
    title: '', // Judul Laporan (Wajib di Backend)
    provinsi: '', 
    kota: '', 
    alamat: '', 
    location: '', // Field gabungan untuk dikirim ke backend
    luas_rumah: '', 
    latitude: '', 
    longitude: '',
    kerusakan: [], 
    photos: [], // REVISI: Ubah jadi Array untuk banyak foto
    video: null, // REVISI: Tambah field khusus video
    deskripsi: ''
});

const opsiKerusakan = [
    'Saluran Air Rusak', 'Pembersihan Lumpur dan Puing', 'Instalasi Listrik Rusak',
    'Atap Bocor / Rusak', 'Struktur Bangunan Rusak', 'Muncul Jamur dan Lembab'
];

const toggleKerusakan = (item) => {
    if (form.kerusakan.includes(item)) form.kerusakan = form.kerusakan.filter(i => i !== item);
    else form.kerusakan.push(item);
};

// --- REVISI 2: Handler Upload Terpisah ---
const handlePhotos = (event) => {
    // Mengambil semua file yang dipilih user
    form.photos = Array.from(event.target.files);
};

const handleVideo = (event) => {
    // Mengambil satu file video
    form.video = event.target.files[0];
};

const hitungEstimasi = () => {
    const jumlah = form.kerusakan.length;
    if (jumlah <= 2) {
        estimasi.value = { kategori: 'Kerusakan Ringan', biaya: 'Rp 2.000.000 - Rp 5.000.000', layanan: 'Pembersihan Dasar & Perbaikan Minor' };
    } else if (jumlah <= 4) {
        estimasi.value = { kategori: 'Kerusakan Sedang', biaya: 'Rp 5.000.000 - Rp 15.000.000', layanan: 'Perbaikan Utilitas, Atap & Pembersihan Total' };
    } else {
        estimasi.value = { kategori: 'Kerusakan Berat', biaya: 'Rp 15.000.000 - Rp 30.000.000', layanan: 'Renovasi Struktur, Kelistrikan & Sanitasi Total' };
    }
};

const nextStep = () => {
    if (currentStep.value === 1) {
        // Validasi Step 1
        if (!form.title || !form.alamat || !form.provinsi || !form.kota || !form.latitude) {
            alert("Mohon lengkapi judul, data rumah, dan titik lokasi."); return;
        }
    } else if (currentStep.value === 2) {
        // Validasi Step 2
        if (form.kerusakan.length === 0 || !form.deskripsi) {
            alert("Mohon lengkapi data kerusakan."); return;
        }
        hitungEstimasi();
    }
    currentStep.value++;
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

// --- REVISI 3: Submit Asli ke Backend ---
const submitForm = () => {
    // Gabungkan alamat menjadi satu string 'location'
    form.location = `${form.alamat}, ${form.kota}, ${form.provinsi}`;

    form.post(route('reports.store'), {
        forceFormData: true, // Wajib true untuk upload file
        onSuccess: () => {
            // alert("Laporan berhasil diajukan!"); // Bisa handle redirect dari controller
        },
        onError: (errors) => {
            console.error(errors);
            alert("Gagal mengirim laporan. Cek inputan Anda.");
        }
    });
};

onMounted(() => {
    if (currentStep.value === 1) setTimeout(initMap, 100);
});

let map;
let marker;
const initMap = () => {
    if(map) return;
    map = L.map('mapContainer').setView([-6.200000, 106.816666], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(map);
    const updateLocation = (lat, lng) => {
        if (marker) marker.setLatLng([lat, lng]);
        else marker = L.marker([lat, lng]).addTo(map);
        map.setView([lat, lng], 16);
        form.latitude = lat; form.longitude = lng;
    };
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((pos) => { updateLocation(pos.coords.latitude, pos.coords.longitude); });
    }
    map.on('click', (e) => updateLocation(e.latlng.lat, e.latlng.lng));
};
</script>

<template>
    <Head title="Pengajuan Pemulihan Rumah" />
    <Navbar /> 

    <div class="min-h-screen bg-[#FFFFFA] font-['Montserrat'] pb-20">
        
        <div class="w-full bg-gradient-to-r from-[#28160A] via-[#000000] to-[#2D190D] pt-32 pb-16 shadow-lg">
            <div class="max-w-5xl mx-auto px-6">
                <h1 class="text-3xl md:text-5xl font-bold text-[#FFEBDE] mb-3 leading-tight">
                    Pengajuan <br class="md:hidden"> Pemulihan Rumah
                </h1>
                <p class="text-white text-base md:text-lg opacity-90 font-light">
                    Lengkapi data untuk mendapatkan bantuan pemulihan
                </p>
            </div>
        </div>

        <div class="max-w-5xl mx-auto mt-12 mb-16 px-6">
            <div class="flex items-center justify-between relative w-full max-w-3xl mx-auto">
                <div class="absolute top-1/2 left-0 w-full h-[4px] bg-[#D6C5BA] z-0 transform -translate-y-1/2 rounded-full"></div>

                <div class="relative z-10 bg-[#FFFFFA] px-3">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center border-[3px] transition-all duration-300 shadow-sm"
                        :class="currentStep === 1 ? 'bg-[#973C00] border-[#973C00]' : 'bg-[#D6C5BA] border-[#D6C5BA]'">
                        <img src="/images/step-1-home.png" class="w-8 h-8 object-contain brightness-0 invert" 
                             :class="currentStep === 1 ? 'opacity-100' : 'opacity-60'">
                    </div>
                </div>

                <div class="relative z-10 bg-[#FFFFFA] px-3">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center border-[3px] transition-all duration-300 shadow-sm"
                        :class="currentStep === 2 ? 'bg-[#973C00] border-[#973C00]' : 'bg-[#D6C5BA] border-[#D6C5BA]'">
                        <img src="/images/step-2-alert.png" class="w-8 h-8 object-contain brightness-0 invert" 
                             :class="currentStep === 2 ? 'opacity-100' : 'opacity-60'">
                    </div>
                </div>

                <div class="relative z-10 bg-[#FFFFFA] px-3">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center border-[3px] transition-all duration-300 shadow-sm"
                        :class="currentStep === 3 ? 'bg-[#973C00] border-[#973C00]' : 'bg-[#D6C5BA] border-[#D6C5BA]'">
                        <img src="/images/step-3-file.png" class="w-8 h-8 object-contain brightness-0 invert" 
                             :class="currentStep === 3 ? 'opacity-100' : 'opacity-60'">
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-5xl mx-auto px-6">
            
            <div v-show="currentStep === 1">
                <h2 class="text-2xl font-bold text-black mb-6">Data Rumah</h2>
                
                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-2">Judul Laporan</label>
                    <input type="text" v-model="form.title" placeholder="Contoh: Atap Roboh Bagian Dapur" class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-gray-700 focus:ring-2 focus:ring-[#973C00] shadow-sm">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <select v-model="form.provinsi" class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-gray-700 focus:ring-2 focus:ring-[#973C00] shadow-sm cursor-pointer">
                            <option value="" disabled selected>Provinsi</option>
                            <option value="Jawa Timur">Jawa Timur</option>
                            <option value="Jawa Tengah">Jawa Tengah</option>
                        </select>
                    </div>
                    <div>
                        <select v-model="form.kota" class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-gray-700 focus:ring-2 focus:ring-[#973C00] shadow-sm cursor-pointer">
                            <option value="" disabled selected>Kecamatan / Kota</option>
                            <option value="Lamongan">Lamongan</option>
                            <option value="Surabaya">Surabaya</option>
                        </select>
                    </div>
                    <div><input type="text" v-model="form.alamat" placeholder="Alamat Lengkap" class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-gray-700 focus:ring-2 focus:ring-[#973C00] shadow-sm"></div>
                    <div><input type="number" v-model="form.luas_rumah" placeholder="Luas Rumah (m²)" class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-gray-700 focus:ring-2 focus:ring-[#973C00] shadow-sm"></div>
                </div>
                <div class="mb-8">
                    <h3 class="text-[#4F3726] font-bold mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#973C00]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                        Pilih Titik di Peta
                    </h3>
                    <div id="mapContainer" class="w-full h-64 md:h-80 rounded-2xl overflow-hidden border border-gray-300 shadow-md relative z-0"></div>
                </div>
                <div class="flex justify-end mt-12">
                    <button @click="nextStep" class="px-8 py-3 rounded-full text-white font-bold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all flex items-center gap-2 bg-gradient-to-r from-[#552300] to-[#BB4D00]">
                        Lanjutkan <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </div>

            <div v-show="currentStep === 2">
                <h2 class="text-2xl font-bold text-black mb-6">Kondisi Kerusakan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                    <div v-for="(item, index) in opsiKerusakan" :key="index" @click="toggleKerusakan(item)"
                        class="cursor-pointer rounded-xl p-4 flex items-center gap-3 border transition-all duration-200"
                        :class="form.kerusakan.includes(item) ? 'bg-[#FFFBEE] border-[#973C00] shadow-md' : 'bg-[#FFFBEE] border-[#973C00] hover:shadow-sm'">
                        <div class="w-5 h-5 rounded border border-[#973C00] flex items-center justify-center bg-white">
                            <div v-if="form.kerusakan.includes(item)" class="w-3 h-3 bg-[#973C00] rounded-sm"></div>
                        </div>
                        <span class="text-[#4F3726] font-medium select-none">{{ item }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-[#716363] text-lg mb-2 font-medium">Unggah Foto (Bisa Banyak)</label>
                        <div class="w-full h-32 border-2 border-dashed border-[#973C00] rounded-xl bg-[#FFFFFA] flex flex-col items-center justify-center cursor-pointer hover:bg-orange-50 transition relative overflow-hidden">
                            <input type="file" multiple @change="handlePhotos" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*">
                            <span class="text-[#973C00] font-bold text-lg mb-1">
                                {{ form.photos.length > 0 ? form.photos.length + ' Foto Dipilih' : 'Pilih Foto-foto' }}
                            </span>
                            <span class="text-gray-400 text-sm">Format: JPG, PNG (Max 5MB/file)</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[#716363] text-lg mb-2 font-medium">Unggah Video (Opsional)</label>
                        <div class="w-full h-32 border-2 border-dashed border-gray-400 rounded-xl bg-gray-50 flex flex-col items-center justify-center cursor-pointer hover:bg-gray-100 transition relative overflow-hidden">
                            <input type="file" @change="handleVideo" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="video/mp4,video/mov">
                            <span class="text-gray-600 font-bold text-lg mb-1">
                                {{ form.video ? '1 Video Dipilih' : 'Pilih Video Kondisi' }}
                            </span>
                            <span class="text-gray-400 text-sm">Format: MP4, MOV (Max 50MB)</span>
                        </div>
                    </div>
                </div>

                <div v-if="form.progress" class="mb-6">
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-[#973C00] h-2.5 rounded-full" :style="{ width: form.progress.percentage + '%' }"></div>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">Mengunggah... {{ form.progress.percentage }}%</p>
                </div>

                <div class="mb-10">
                    <label class="block text-[#716363] text-lg mb-2 font-medium">Deskripsikan Kerusakan secara Detail</label>
                    <textarea v-model="form.deskripsi" rows="5" class="w-full bg-white border border-[#973C00] rounded-xl p-4 text-[#4F3726] focus:ring-2 focus:ring-[#973C00] shadow-sm" placeholder="Contoh: Tembok ruang tamu retak parah, atap dapur bocor saat hujan deras..."></textarea>
                </div>

                <div class="flex justify-between items-center mt-12">
                    <button @click="prevStep" class="flex items-center gap-2 text-[#696565] font-semibold hover:text-[#4F3726] transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Kembali
                    </button>
                    <button @click="nextStep" class="px-8 py-3 rounded-full text-white font-bold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all flex items-center gap-2 bg-gradient-to-r from-[#552300] to-[#BB4D00]">
                        Verifikasi <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </div>

            <div v-show="currentStep === 3">
                <div class="bg-white border border-gray-300 rounded-2xl p-6 md:p-8 mb-6 shadow-sm">
                    <h2 class="text-xl font-bold text-[#1C1917] mb-4">Kategori Kerusakan</h2>
                    <div class="inline-flex items-center gap-2 bg-[#FFE4E4] border border-[#FFCFCF] rounded-full px-6 py-2">
                        <div class="w-5 h-5 rounded-full border-2 border-[#D32F2F] flex items-center justify-center"><div class="w-3 h-0.5 bg-[#D32F2F]"></div></div>
                        <span class="text-[#D32F2F] font-bold text-lg">{{ estimasi.kategori }}</span>
                    </div>
                </div>
                <div class="bg-white border border-yellow-400 rounded-2xl p-6 md:p-8 mb-6 shadow-sm bg-yellow-50/10">
                    <h2 class="text-xl font-bold text-[#1C1917] mb-4">Rekomendasi Layanan</h2>
                    <p class="text-gray-700 text-lg leading-relaxed">{{ estimasi.layanan }}</p>
                </div>
                <div class="bg-white border border-gray-300 rounded-2xl p-6 md:p-8 mb-6 shadow-sm">
                    <h2 class="text-xl font-bold text-[#1C1917] mb-2">Estimasi Biaya Awal</h2>
                    <p class="text-4xl md:text-5xl font-bold text-[#BB4D00] mb-2">{{ estimasi.biaya }}</p>
                    <p class="text-gray-400 text-sm">*Estimasi berdasarkan assessment awal.</p>
                </div>
                <div class="bg-white border border-yellow-400 rounded-2xl p-6 md:p-8 mb-10 shadow-sm bg-yellow-50/10">
                    <h2 class="text-xl font-bold text-[#1C1917] mb-3">Langkah Selanjutnya</h2>
                    <p class="text-gray-600 leading-relaxed">Kami akan menghubungkan Anda dengan vendor terpercaya setelah Anda mengirimkan laporan ini.</p>
                </div>
                <div class="flex justify-between items-center mt-8">
                    <button @click="prevStep" class="flex items-center gap-2 text-[#696565] font-semibold hover:text-[#4F3726] transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Kembali
                    </button>
                    <button @click="submitForm" :disabled="form.processing" class="px-8 py-3 rounded-full text-white font-bold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all flex items-center gap-2 bg-gradient-to-r from-[#552300] to-[#BB4D00] disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ form.processing ? 'Mengirim...' : 'Ajukan Pemulihan' }} 
                        <svg v-if="!form.processing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </div>

        </div>
    </div>
</template>