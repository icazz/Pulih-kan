<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { onMounted } from 'vue';

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

const props = defineProps({
    vendor: Object,
    auth: Object
});

onMounted(() => {
    // Inisialisasi Map
    const lat = parseFloat(props.vendor.latitude) || -6.175392;
    const lng = parseFloat(props.vendor.longitude) || 106.827153;

    const map = L.map('detailVendorMap').setView([lat, lng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.marker([lat, lng]).addTo(map)
        .bindPopup(props.vendor.nama_mitra)
        .openPopup();
});

const verifyVendor = (status) => {
    const action = status === 'approve' ? 'MENYETUJUI' : 'MENOLAK';
    if (!confirm(`Konfirmasi: Apakah Anda yakin ingin ${action} mitra ini?`)) return;

    // --- UBAH BAGIAN INI ---
    // Kita pakai URL manual string (Backtick `) biar pasti mengarah ke /verify
    const url = `/admin/vendors/${props.vendor.id}/verify`;

    router.patch(url, { status }, {
        onSuccess: () => {
            alert("Status berhasil diperbarui!");
            window.location.reload(); 
        },
        onError: (err) => alert("Gagal update: " + JSON.stringify(err))
    });
};
</script>

<template>
    <Head :title="`Detail Mitra: ${vendor.nama_mitra}`" />

    <div class="h-screen w-full flex flex-col md:flex-row overflow-hidden bg-white">
        <div class="w-full md:w-1/2 h-1/2 md:h-full relative bg-gray-100 order-2 md:order-1">
            <div id="detailVendorMap" class="w-full h-full z-0"></div>
            <Link :href="route('admin.dashboard')" class="absolute top-6 left-6 z-[400] bg-white text-gray-800 px-4 py-2 rounded-full shadow-lg font-bold flex items-center gap-2 hover:bg-gray-50 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Dashboard
            </Link>
        </div>

        <div class="w-full md:w-1/2 h-1/2 md:h-full flex flex-col bg-white border-l border-gray-200 order-1 md:order-2 shadow-2xl z-10">
            <div class="bg-[#4F3726] p-8 text-white flex-shrink-0">
                <h1 class="text-2xl font-bold">Verifikasi Kemitraan</h1>
                <p class="text-white/70 mt-1">ID Mitra: {{ vendor.id }} • Status: {{ vendor.status ? vendor.status.toUpperCase() : 'PENDING' }}</p>
            </div>

            <div class="flex-1 overflow-y-auto p-8 space-y-8">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Nama Mitra</label>
                        <p class="font-bold text-gray-800 text-xl">{{ vendor.nama_mitra }}</p>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Kontak</label>
                        <p class="font-bold text-gray-800">{{ vendor.no_telepon }}</p>
                        <p class="text-xs text-gray-500">{{ vendor.email }}</p>
                    </div>
                    <div class="col-span-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Alamat Lengkap</label>
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <p class="text-gray-800 text-sm leading-relaxed mb-2">{{ vendor.alamat }}</p>
                            <span class="inline-block bg-white border border-gray-200 px-2 py-1 rounded text-xs font-bold text-gray-500">
                                {{ vendor.kota }}, {{ vendor.provinsi }}
                            </span>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Layanan Jasa</label>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="jasa in vendor.jenis_jasa" :key="jasa" class="bg-orange-50 text-[#BB4D00] border border-orange-100 px-3 py-1 rounded-full text-sm font-bold">
                                {{ jasa }}
                            </span>
                        </div>
                        <p v-if="vendor.jasa_lainnya" class="mt-3 text-sm text-gray-600 italic border-l-4 border-orange-200 pl-3">
                            "{{ vendor.jasa_lainnya }}"
                        </p>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-4">Tindakan Admin</label>
                    
                    <div v-if="vendor.status === 'pending' || (!vendor.status && !vendor.is_verified)" class="flex gap-4">
                        <button @click="verifyVendor('reject')" class="px-6 py-4 rounded-xl font-bold text-red-600 bg-white border border-red-200 hover:bg-red-50 transition w-1/3">
                            Tolak
                        </button>
                        <button @click="verifyVendor('approve')" class="flex-1 py-4 rounded-xl font-bold text-white bg-green-600 hover:bg-green-700 transition shadow-lg shadow-green-200 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Setujui & Aktifkan Akun
                        </button>
                    </div>

                    <div v-else-if="vendor.status === 'verified' || vendor.is_verified" class="bg-green-50 p-6 rounded-xl border border-green-200">
                        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600 shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-green-800 text-lg">Mitra Terverifikasi</h4>
                                    <p class="text-xs text-green-600 leading-relaxed">Akun ini sedang aktif. Anda dapat membatalkan verifikasi jika mitra melanggar ketentuan.</p>
                                </div>
                            </div>
                            
                            <button @click="verifyVendor('reject')" class="px-5 py-3 bg-white border border-red-200 text-red-600 rounded-lg text-sm font-bold hover:bg-red-50 transition shadow-sm hover:shadow flex items-center gap-2 whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                Batalkan Verifikasi
                            </button>
                        </div>
                    </div>

                    <div v-else class="bg-red-50 p-6 rounded-xl border border-red-200">
                        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-red-600 shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-red-800 text-lg">Mitra Dinonaktifkan / Ditolak</h4>
                                    <p class="text-xs text-red-600 leading-relaxed">Akun ini tidak dapat menerima pesanan. Klik tombol di kanan untuk mengaktifkan kembali.</p>
                                </div>
                            </div>

                            <button @click="verifyVendor('approve')" class="px-5 py-3 bg-white border border-green-200 text-green-600 rounded-lg text-sm font-bold hover:bg-green-50 transition shadow-sm hover:shadow flex items-center gap-2 whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Pulihkan / Setujui Kembali
                            </button>
                        </div>
                    </div>
                </div>
                </div>
        </div>
    </div>
</template>