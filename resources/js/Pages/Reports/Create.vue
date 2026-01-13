<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted, ref, nextTick } from 'vue';
import Navbar from '@/Components/Navbar.vue';
import "leaflet/dist/leaflet.css";
import L from "leaflet";
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

const currentStep = ref(1);

const form = useForm({
    title: '', 
    provinsi: '', 
    kota: '', 
    alamat: '', 
    location: '', 
    luas_rumah: '', 
    latitude: '', 
    longitude: '',
    kerusakan: [], 
    drive_link: '', 
    deskripsi: ''
});

const opsiKerusakan = [
    'Saluran Air Rusak', 'Pembersihan Lumpur dan Puing', 'Instalasi Listrik Rusak',
    'Atap Bocor / Rusak', 'Muncul Jamur dan Lembab', 'Lainnya'
];

const toggleKerusakan = (item) => {
    if (form.kerusakan.includes(item)) form.kerusakan = form.kerusakan.filter(i => i !== item);
    else form.kerusakan.push(item);
};

const nextStep = () => {
    if (currentStep.value === 1) {
        if (!form.title || !form.alamat || !form.provinsi || !form.kota || !form.latitude) {
            alert("Mohon lengkapi judul, data rumah, dan pilih titik lokasi di peta."); return;
        }
        currentStep.value++;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

const submitForm = () => {
    // Validasi Step 2 sebelum submit
    if (form.kerusakan.length === 0 || !form.deskripsi || !form.drive_link) {
        alert("Mohon lengkapi kerusakan dan Link Google Drive."); return;
    }

    form.location = `${form.alamat}, ${form.kota}, ${form.provinsi}`;

    form.post(route('reports.store'), {
        onSuccess: () => {
            // Berhasil, Controller akan redirect ke reports.show
        },
        onError: (errors) => {
            console.error("Error submit:", errors);
            alert("Gagal mengirim laporan. Pastikan link drive valid.");
        }
    });
};

onMounted(() => {
    if (currentStep.value === 1) {
        nextTick(() => { setTimeout(initMap, 500); });
    }
});

let map;
let marker;
const initMap = () => {
    if(!document.getElementById('mapContainer')) return;
    if(map) return;

    map = L.map('mapContainer').setView([-6.200000, 106.816666], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { 
        attribution: '&copy; OpenStreetMap',
        maxZoom: 19
    }).addTo(map);

    const updateLocation = (lat, lng) => {
        if (marker) marker.setLatLng([lat, lng]);
        else marker = L.marker([lat, lng]).addTo(map);
        map.setView([lat, lng], 16);
        form.latitude = lat; 
        form.longitude = lng;
    };

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((pos) => { 
            updateLocation(pos.coords.latitude, pos.coords.longitude); 
        });
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
                    Pengajuan <br class="md:hidden"> Perbaikan Rumah
                </h1>
                <p class="text-white text-base md:text-lg opacity-90 font-light">
                    Lengkapi data untuk mendapatkan perbaikan
                </p>
            </div>
        </div>

        <div class="max-w-5xl mx-auto mt-12 mb-16 px-6">
            <div class="flex items-center justify-between relative w-full max-w-2xl mx-auto">
                <div class="absolute top-1/2 left-0 w-full h-[4px] bg-[#D6C5BA] z-0 transform -translate-y-1/2 rounded-full"></div>
                <div class="relative z-10 bg-[#FFFFFA] px-3">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center border-[3px] transition-all duration-300 shadow-sm" :class="currentStep === 1 ? 'bg-[#973C00] border-[#973C00]' : 'bg-[#D6C5BA] border-[#D6C5BA]'">
                        <img src="/images/step-1-home.png" class="w-8 h-8 object-contain brightness-0 invert" :class="currentStep === 1 ? 'opacity-100' : 'opacity-60'">
                    </div>
                </div>
                <div class="relative z-10 bg-[#FFFFFA] px-3">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center border-[3px] transition-all duration-300 shadow-sm" :class="currentStep === 2 ? 'bg-[#973C00] border-[#973C00]' : 'bg-[#D6C5BA] border-[#D6C5BA]'">
                        <img src="/images/step-2-alert.png" class="w-8 h-8 object-contain brightness-0 invert" :class="currentStep === 2 ? 'opacity-100' : 'opacity-60'">
                    </div>
                </div>
                <div class="relative z-10 bg-[#FFFFFA] px-3">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center border-[3px] transition-all duration-300 shadow-sm bg-[#D6C5BA] border-[#D6C5BA]">
                        <img src="/images/step-3-file.png" class="w-8 h-8 object-contain brightness-0 invert opacity-60">
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
                    <h3 class="text-[#4F3726] font-bold mb-3 flex items-center gap-2">Pilih Titik di Peta</h3>
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
                    <div v-for="(item, index) in opsiKerusakan" :key="index" @click="toggleKerusakan(item)" class="cursor-pointer rounded-xl p-4 flex items-center gap-3 border transition-all duration-200" :class="form.kerusakan.includes(item) ? 'bg-[#FFFBEE] border-[#973C00] shadow-md' : 'bg-[#FFFBEE] border-[#973C00] hover:shadow-sm'">
                        <div class="w-5 h-5 rounded border border-[#973C00] flex items-center justify-center bg-white">
                            <div v-if="form.kerusakan.includes(item)" class="w-3 h-3 bg-[#973C00] rounded-sm"></div>
                        </div>
                        <span class="text-[#4F3726] font-medium select-none">{{ item }}</span>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-[#716363] text-lg mb-2 font-medium">
                        Link Folder Google Drive (Foto/Video Bukti)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                        </div>
                        <input type="url" v-model="form.drive_link" class="w-full bg-white border border-[#973C00] rounded-xl pl-10 pr-4 py-3 text-[#4F3726] focus:ring-2 focus:ring-[#973C00] shadow-sm placeholder-gray-400" placeholder="https://drive.google.com/drive/folders/...">
                    </div>
                    <p class="text-sm text-gray-500 mt-2">*Pastikan akses link sudah "Anyone with the link".</p>
                </div>

                <div class="mb-10">
                    <label class="block text-[#716363] text-lg mb-2 font-medium">Deskripsikan Kerusakan secara Detail</label>
                    <textarea v-model="form.deskripsi" rows="5" class="w-full bg-white border border-[#973C00] rounded-xl p-4 text-[#4F3726] focus:ring-2 focus:ring-[#973C00] shadow-sm" placeholder="Contoh: Tembok ruang tamu retak parah..."></textarea>
                    <p class="text-sm text-gray-500 mt-2">*Hasil verifikasi akan diberikan maksimal dalam waktu 1 x 48 jam .</p>
                </div>

                <div class="flex justify-between items-center mt-12">
                    <button @click="prevStep" class="flex items-center gap-2 text-[#696565] font-semibold hover:text-[#4F3726] transition">Kembali</button>
                    <button @click="submitForm" :disabled="form.processing" class="px-8 py-3 rounded-full text-white font-bold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all flex items-center gap-2 bg-gradient-to-r from-[#552300] to-[#BB4D00]">
                        {{ form.processing ? 'Mengirim...' : 'Verifikasi & Kirim' }}
                    </button>
                </div>
            </div>

        </div>
    </div>
</template>