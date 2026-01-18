<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import Navbar from '@/Components/Navbar.vue';

// --- LEAFLET SETUP ---
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

// 1. Menerima Data Lama (jika ada)
const props = defineProps({
    existingData: Object 
});

// 2. Inisialisasi Form dengan Data Lama (Prefill)
const form = useForm({
    nama_mitra: props.existingData?.nama_mitra || '',
    no_telepon: props.existingData?.no_telepon || '',
    email: props.existingData?.email || '',
    // Pastikan jenis_jasa array, jika null ganti []
    jenis_jasa: props.existingData?.jenis_jasa || [],
    jasa_lainnya: props.existingData?.jasa_lainnya || '',
    provinsi: props.existingData?.provinsi || '',
    kota: props.existingData?.kota || '',
    alamat: props.existingData?.alamat || '',
    latitude: props.existingData?.latitude || '',
    longitude: props.existingData?.longitude || '',
    agreement: false, // User wajib menyetujui ulang saat submit
});

const jenisJasaOptions = [
    'Saluran Air Rusak', 'Pembersihan Lumpur dan Puing', 'Instalasi Listrik Rusak',
    'Atap Bocor / Rusak', 'Muncul Jamur dan Lembab', 'Lainnya'
];

const toggleJasa = (item) => {
    const index = form.jenis_jasa.indexOf(item);
    if (index > -1) {
        form.jenis_jasa.splice(index, 1);
        if (item === 'Lainnya') form.jasa_lainnya = ''; 
    } else {
        form.jenis_jasa.push(item);
    }
};

const submit = () => {
    form.post(route('vendor.store'), {
        onBefore: () => {
            if (!Array.isArray(form.jenis_jasa)) {
                form.jenis_jasa = [form.jenis_jasa];
            }
        },
        onSuccess: () => {
            console.log("Pendaftaran Berhasil");
        },
        onError: (errors) => {
            console.error("Gagal mendaftar:", errors);
            alert("Terjadi kesalahan, mohon periksa kembali data Anda.");
        },
    });
};

onMounted(() => {
    // Tentukan titik awal: Jika ada data lama pakai itu, jika tidak pakai Default Jakarta
    const initialLat = form.latitude || -6.200000;
    const initialLng = form.longitude || 106.816666;
    const initialZoom = form.latitude ? 16 : 13;

    const map = L.map('mapContainer').setView([initialLat, initialLng], initialZoom);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker;

    // Jika sudah ada koordinat lama, langsung pasang marker
    if (form.latitude && form.longitude) {
        marker = L.marker([form.latitude, form.longitude]).addTo(map);
    }

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

    // Hanya minta lokasi browser JIKA data lama KOSONG
    if (!form.latitude && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => updateLocation(position.coords.latitude, position.coords.longitude),
            (error) => console.warn("Geolocation error:", error.message)
        );
    }

    map.on('click', (e) => updateLocation(e.latlng.lat, e.latlng.lng));
});
</script>

<template>
    <Head title="Daftar Sebagai Vendor" />

    <div class="min-h-screen w-full bg-gradient-to-b from-[#47622A] to-[#91C856] py-16 px-4 sm:px-6 lg:px-8 font-['Montserrat'] flex flex-col items-center">
        
        <div class="text-center mb-10">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-3 drop-shadow-md">
                {{ existingData ? 'Perbaiki Data Mitra' : 'Selamat Datang!' }}
            </h1>
            <p class="text-white text-lg font-medium opacity-90">
                {{ existingData ? 'Lengkapi data Anda agar dapat diverifikasi ulang' : 'Daftarkan diri Anda sebagai mitra layanan Pulih.kan' }}
            </p>
        </div>

        <div class="w-full max-w-5xl bg-[#FEF8DF] rounded-[3rem] shadow-2xl overflow-hidden">
            <div class="p-8 md:p-12">
                
                <div v-if="existingData?.status === 'rejected'" class="mb-8 p-4 bg-red-100 text-red-700 rounded-xl border border-red-200 flex items-start gap-3 animate-pulse">
                    <svg class="w-6 h-6 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div>
                        <p class="font-bold text-lg">Pendaftaran Sebelumnya Ditolak</p>
                        <p class="text-sm mt-1">Mohon perbaiki data Anda sesuai ketentuan yang berlaku, lalu ajukan kembali.</p>
                    </div>
                </div>

                <form @submit.prevent="submit">
                    
                    <div class="mb-12">
                        <h2 class="text-2xl font-bold text-[#4F3726] mb-6 border-l-4 border-[#47622A] pl-4">Data Identitas Vendor</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex flex-col gap-2">
                                <label class="text-[#4F3726] font-semibold text-sm ml-1">Nama Mitra</label>
                                <input v-model="form.nama_mitra" type="text" placeholder="Masukkan Nama Lengkap / Brand" class="w-full bg-white border border-[#A89078] rounded-xl px-5 py-3 text-[#4F3726] focus:ring-2 focus:ring-[#47622A] focus:outline-none shadow-sm">
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-[#4F3726] font-semibold text-sm ml-1">Nomor Telepon (WhatsApp)</label>
                                <input v-model="form.no_telepon" type="tel" placeholder="Contoh: 0812xxxx" class="w-full bg-white border border-[#A89078] rounded-xl px-5 py-3 text-[#4F3726] focus:ring-2 focus:ring-[#47622A] focus:outline-none shadow-sm">
                            </div>
                            <div class="flex flex-col gap-2 md:col-span-2">
                                <label class="text-[#4F3726] font-semibold text-sm ml-1">Email Aktif</label>
                                <input v-model="form.email" type="email" placeholder="nama@email.com" class="w-full bg-white border border-[#A89078] rounded-xl px-5 py-3 text-[#4F3726] focus:ring-2 focus:ring-[#47622A] focus:outline-none shadow-sm">
                            </div>
                        </div>
                    </div>

                    <div class="mb-12">
                        <h2 class="text-2xl font-bold text-[#4F3726] mb-6 border-l-4 border-[#47622A] pl-4">Jenis Jasa (Bisa pilih lebih dari satu)</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-for="(item, index) in jenisJasaOptions" :key="index" @click="toggleJasa(item)" 
                                 class="cursor-pointer rounded-xl p-4 flex items-center gap-3 border transition-all duration-200 shadow-sm"
                                 :class="form.jenis_jasa.includes(item) ? 'bg-[#973C00] border-[#973C00] text-white' : 'bg-white border-[#A89078] text-[#4F3726] hover:bg-gray-50'">
                                <div class="w-5 h-5 rounded border flex items-center justify-center" :class="form.jenis_jasa.includes(item) ? 'border-white bg-white' : 'border-[#4F3726]'">
                                    <div v-if="form.jenis_jasa.includes(item)" class="w-3 h-3 bg-[#973C00] rounded-sm"></div>
                                </div>
                                <span class="font-medium select-none">{{ item }}</span>
                            </div>
                        </div>
                        
                        <div v-if="form.jenis_jasa.includes('Lainnya')" class="mt-6 transition-all duration-300">
                            <label class="text-[#4F3726] font-semibold text-sm mb-2 block">Sebutkan Jasa Lainnya:</label>
                            <textarea v-model="form.jasa_lainnya" placeholder="Deskripsikan jasa Anda di sini..." class="w-full bg-white border border-[#A89078] rounded-xl px-5 py-3 text-[#4F3726] focus:ring-2 focus:ring-[#47622A] focus:outline-none shadow-sm h-24"></textarea>
                        </div>
                    </div>

                    <div class="mb-10">
                        <h2 class="text-2xl font-bold text-[#4F3726] mb-6 border-l-4 border-[#47622A] pl-4">Area & Lokasi Kerja</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            
                            <div class="space-y-2">
                                <label class="text-[#4F3726] font-semibold text-sm ml-1">Provinsi</label>
                                <input 
                                    v-model="form.provinsi" 
                                    type="text" 
                                    placeholder="Contoh: Jawa Timur" 
                                    class="w-full bg-white border border-[#A89078] rounded-xl px-5 py-3 text-[#4F3726] focus:ring-2 focus:ring-[#47622A] focus:outline-none shadow-sm"
                                >
                            </div>

                            <div class="space-y-2">
                                <label class="text-[#4F3726] font-semibold text-sm ml-1">Kecamatan / Kota</label>
                                <input 
                                    v-model="form.kota" 
                                    type="text" 
                                    placeholder="Contoh: Surabaya" 
                                    class="w-full bg-white border border-[#A89078] rounded-xl px-5 py-3 text-[#4F3726] focus:ring-2 focus:ring-[#47622A] focus:outline-none shadow-sm"
                                >
                            </div>
                            <div class="space-y-2 md:col-span-2">
                                <label class="text-[#4F3726] font-semibold text-sm ml-1">Alamat Lengkap</label>
                                <input v-model="form.alamat" type="text" placeholder="Jl. Nama Jalan, No. Rumah, RT/RW" class="w-full bg-white border border-[#A89078] rounded-xl px-5 py-3 text-[#4F3726] focus:ring-2 focus:ring-[#47622A] shadow-sm focus:outline-none">
                            </div>
                        </div>

                        <div class="mb-4">
                            <h3 class="text-[#4F3726] font-bold mb-3 flex items-center gap-2">Pilih Titik di Peta</h3>
                            <div id="mapContainer" class="w-full h-80 z-0 rounded-2xl overflow-hidden border border-[#A89078] shadow-md relative">
                                <div class="absolute inset-0 flex items-center justify-center bg-gray-50 z-[-1]">
                                    <span class="text-gray-400">Memuat Peta...</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-[#EBE5CB] -mx-8 md:-mx-12 -mb-8 md:-mb-12 p-8 md:p-12 rounded-t-[3rem] border-t border-[#DED4B0]">
                        <h2 class="text-2xl font-bold text-[#4F3726] mb-6">Status & Persetujuan</h2>
                        
                        <div class="flex items-start gap-4 mb-10 bg-white/50 p-4 rounded-2xl border border-[#A89078]">
                            <div class="flex items-center h-6">
                                <input id="agreement" v-model="form.agreement" type="checkbox" class="w-6 h-6 border border-[#4F3726] rounded text-[#47622A] focus:ring-[#47622A] cursor-pointer">
                            </div>
                            <label for="agreement" class="text-[#4F3726] text-sm font-medium leading-relaxed cursor-pointer">
                                Saya menyatakan bahwa seluruh data yang saya berikan adalah benar dan bersedia mengikuti prosedur verifikasi mitra Pulih.kan.
                            </label>
                        </div>
                        
                        <button 
                            type="submit" 
                            :disabled="form.processing || !form.agreement"
                            class="w-full bg-[#4F3726] hover:bg-[#3a281c] disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-bold py-5 px-6 rounded-2xl shadow-xl transition-all hover:scale-[1.01] text-lg mb-6">
                            {{ form.processing ? 'Mengirim...' : (existingData ? 'Ajukan Kembali Verifikasi' : 'Kirim untuk Verifikasi') }}
                        </button>

                        <div class="text-center mb-10">
                            <p class="text-[#4F3726] text-xs italic opacity-80 leading-relaxed max-w-2xl mx-auto">
                                *Proses verifikasi manual membutuhkan waktu 1-3 hari kerja. Mohon pantau email atau halaman ini secara berkala.
                            </p>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Memastikan transisi smooth */
.transition-all {
    transition: all 0.3s ease;
}
</style>