<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import "leaflet/dist/leaflet.css";
import L from "leaflet";
// Fix Icon Leaflet
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
    report: Object, 
    auth: Object
});

const form = useForm({ action: '', price: props.report.price || '', category: props.report.category || '' });

onMounted(() => {
    const lat = parseFloat(props.report.latitude) || -6.175392;
    const lng = parseFloat(props.report.longitude) || 106.827153;

    const map = L.map('detailMap').setView([lat, lng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.marker([lat, lng]).addTo(map)
        .bindPopup(props.report.location)
        .openPopup();
});

const submitAction = (action) => {
    if (!confirm('Apakah Anda yakin ingin memproses status ini?')) return;
    
    form.action = action;
    form.patch(`/admin/reports/${props.report.id}/status`, {
        preserveScroll: true,
        onSuccess: () => { alert('Status berhasil diperbarui!'); }
    });
};
</script>

<template>
    <Head :title="`Detail Laporan #${report.id}`" />

    <div class="h-screen w-full flex flex-col md:flex-row overflow-hidden bg-white">
        
        <div class="w-full md:w-1/2 h-1/2 md:h-full relative bg-gray-100 order-2 md:order-1">
            <div id="detailMap" class="w-full h-full z-0"></div>
            
            <Link :href="route('admin.dashboard')" class="absolute top-6 left-6 z-[400] bg-white text-gray-800 px-4 py-2 rounded-full shadow-lg font-bold flex items-center gap-2 hover:bg-gray-50 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Dashboard
            </Link>
        </div>

        <div class="w-full md:w-1/2 h-1/2 md:h-full flex flex-col bg-white border-l border-gray-200 order-1 md:order-2 shadow-2xl z-10">
            
            <div class="bg-[#28160A] p-8 text-white flex-shrink-0">
                <h1 class="text-2xl font-bold">Detail Pengajuan</h1>
                <p class="text-white/70 mt-1">ID: REQ-{{ report.id }} • Client: {{ report.user_name }}</p>
                <div class="mt-4 inline-block px-3 py-1 rounded bg-white/10 border border-white/20 text-xs font-mono">
                    Status: {{ report.status.toUpperCase() }}
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-8 space-y-8">
                
                <div class="space-y-6">
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-1">Judul Laporan</label>
                        <p class="text-xl font-bold text-gray-900 leading-tight">{{ report.title }}</p>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-1">Lokasi</label>
                        <p class="text-gray-700">{{ report.location }}</p>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-2">Deskripsi Masalah</label>
                        <p class="text-gray-800 leading-relaxed">{{ report.description }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-1">Luas Area</label>
                            <p class="font-bold text-lg">{{ report.house_size || '-' }}</p>
                        </div>

                        <div class="p-4 bg-green-50 rounded-xl border border-green-100">
                            <label class="text-xs font-bold text-green-600 uppercase tracking-widest block mb-1">Estimasi Biaya</label>
                            <p class="font-bold text-lg text-green-700">
                                {{ report.price ? 'Rp ' + parseInt(report.price).toLocaleString('id-ID') : 'Belum ditentukan' }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <a v-if="report.drive_link" :href="report.drive_link" target="_blank" class="block w-full p-4 bg-blue-50 rounded-xl border border-blue-100 hover:bg-blue-100 transition cursor-pointer group relative z-20">
                            <label class="text-xs font-bold text-blue-400 uppercase tracking-widest block mb-1 cursor-pointer group-hover:text-blue-500">Bukti Foto/Video</label>
                            <div class="font-bold text-blue-700 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                Buka Link Google Drive
                            </div>
                        </a>

                        <div v-else class="block w-full p-4 bg-gray-50 rounded-xl border border-gray-200 cursor-not-allowed opacity-70">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-1">Bukti Foto/Video</label>
                            <div class="font-bold text-gray-400 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                Tidak ada link dilampirkan
                            </div>
                        </div>

                        <p class="text-[10px] text-gray-400 mt-1 font-mono">
                            Debug URL: {{ report.drive_link ? report.drive_link : '(KOSONG)' }}
                        </p>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-2">Checklist Kerusakan</label>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="(dmg, i) in report.damage_types" :key="i" class="px-3 py-1 rounded-full bg-red-50 text-red-700 text-sm font-medium border border-red-100">
                                {{ dmg }}
                            </span>
                            <span v-if="!report.damage_types?.length" class="text-gray-400 italic text-sm">Tidak ada data checklist.</span>
                        </div>
                    </div>
                </div>

                <div class="pt-8 border-t border-gray-100">
                    <h3 class="font-bold text-[#BB4D00] text-lg mb-6 uppercase tracking-wide flex items-center gap-2">
                        <span class="w-8 h-[2px] bg-[#BB4D00]"></span> Tindakan Admin
                    </h3>

                    <div v-if="report.status === 'verification'" class="bg-orange-50 p-6 rounded-2xl border border-orange-200">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Kategori Kerusakan</label>
                                <select v-model="form.category" class="w-full rounded-xl border-gray-300 focus:ring-[#BB4D00] focus:border-[#BB4D00]">
                                    <option value="" disabled>Pilih Kategori...</option>
                                    <option value="Ringan">Ringan</option>
                                    <option value="Sedang">Sedang</option>
                                    <option value="Berat">Berat</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Input Estimasi Biaya (Rp)</label>
                                <input v-model="form.price" type="number" placeholder="0" class="w-full rounded-xl border-gray-300 focus:ring-[#BB4D00] focus:border-[#BB4D00]">
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <button @click="submitAction('verify')" :disabled="form.processing || !form.price || !form.category" class="flex-1 bg-[#BB4D00] hover:bg-[#903000] text-white py-3 rounded-xl font-bold transition disabled:opacity-50 shadow-lg shadow-orange-200">
                                Verifikasi & Kirim Penawaran
                            </button>
                            <button @click="submitAction('cancel')" class="px-6 py-3 bg-white border border-red-200 text-red-600 rounded-xl font-bold hover:bg-red-50 transition">
                                Tolak
                            </button>
                        </div>
                    </div>

                    <div v-else-if="report.status === 'pending'" class="bg-blue-50 p-6 rounded-2xl border border-blue-200 text-center">
                        <h4 class="text-blue-900 font-bold mb-1">Menunggu Pembayaran User</h4>
                        <p class="text-2xl font-black text-blue-600 mb-6">Rp {{ parseInt(form.price).toLocaleString('id-ID') }}</p>
                        <button @click="submitAction('confirm_payment')" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-bold transition shadow-lg shadow-blue-200">
                            Konfirmasi Pembayaran Diterima (Manual)
                        </button>
                    </div>

                    <div v-else-if="report.status === 'process'" class="bg-green-50 p-6 rounded-2xl border border-green-200 text-center">
                        <h4 class="text-green-900 font-bold mb-1">Proyek Dalam Pengerjaan</h4>
                         <p class="text-xl font-bold text-green-700 mb-4">Total Biaya: Rp {{ parseInt(report.price).toLocaleString('id-ID') }}</p>
                        
                        <p class="text-green-800 mb-4 font-medium text-sm">Klik tombol di bawah jika pengerjaan telah selesai sepenuhnya.</p>
                        <button @click="submitAction('complete')" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-xl font-bold transition shadow-lg shadow-green-200">
                            Tandai Pesanan Selesai
                        </button>
                    </div>

                    <div v-else class="text-center p-6 bg-gray-50 border border-gray-200 rounded-xl text-gray-500">
                        <p v-if="report.price" class="font-bold text-lg mb-2 text-gray-700">
                            Total Transaksi: Rp {{ parseInt(report.price).toLocaleString('id-ID') }}
                        </p>
                        <p>Tidak ada tindakan yang diperlukan untuk status ini.</p>
                    </div>
                </div>

                <div class="h-20"></div>
            </div>
        </div>
    </div>
</template>