<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"; // Sesuaikan layout admin Anda

const props = defineProps({
    volunteer: Object,
});

// Fungsi Helper untuk Warna Badge Status
const getStatusBadge = (status) => {
    if (status === 'verified') return 'bg-green-100 text-green-700 border-green-200';
    if (status === 'rejected') return 'bg-red-100 text-red-700 border-red-200';
    return 'bg-yellow-100 text-yellow-700 border-yellow-200';
};

// Fungsi Update Status
const updateStatus = (newStatus) => {
    const action = newStatus === 'verified' ? 'menerima' : 'menolak';
    if (confirm(`Apakah Anda yakin ingin ${action} pengajuan relawan ini?`)) {
        router.patch(route('admin.volunteers.update', props.volunteer.id), {
            status: newStatus
        }, {
            onSuccess: () => alert('Status berhasil diperbarui!')
        });
    }
};
</script>

<template>
    <Head :title="`Detail Relawan - ${volunteer.name}`" />

    <AuthenticatedLayout>
        <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
            <div class="max-w-5xl mx-auto px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <Link :href="route('admin.dashboard')" class="p-2 rounded-full hover:bg-gray-100 text-gray-500 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </Link>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">Detail Relawan</h1>
                        <p class="text-sm text-gray-500">ID: VOL-{{ volunteer.id }}</p>
                    </div>
                </div>
                
                <span :class="`px-4 py-2 rounded-full text-sm font-bold border ${getStatusBadge(volunteer.status)}`">
                    {{ volunteer.status === 'pending' ? 'Menunggu Verifikasi' : (volunteer.status === 'verified' ? 'Terverifikasi' : 'Ditolak') }}
                </span>
            </div>
        </div>

        <div class="max-w-5xl mx-auto px-6 py-10 font-['Montserrat']">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-6">
                    
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                        <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#BB4D00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Data Pribadi
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                            <div>
                                <label class="text-xs text-gray-400 uppercase tracking-wide font-bold">Nama Lengkap</label>
                                <p class="text-gray-800 font-semibold text-lg mt-1">{{ volunteer.name }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-400 uppercase tracking-wide font-bold">Email</label>
                                <p class="text-gray-800 font-medium mt-1">{{ volunteer.email }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-400 uppercase tracking-wide font-bold">Nomor Telepon</label>
                                <p class="text-gray-800 font-medium mt-1">{{ volunteer.phone }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-400 uppercase tracking-wide font-bold">Kontak Darurat</label>
                                <p class="text-gray-800 font-medium mt-1">{{ volunteer.emergency_contact }}</p>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-100">
                            <label class="text-xs text-gray-400 uppercase tracking-wide font-bold">Alamat Lengkap</label>
                            <p class="text-gray-800 mt-1">{{ volunteer.address }}</p>
                            <p class="text-gray-500 text-sm">{{ volunteer.city }}, {{ volunteer.province }}</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                        <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#BB4D00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Peran & Pengalaman
                        </h2>
                        
                        <div class="mb-6">
                            <label class="text-xs text-gray-400 uppercase tracking-wide font-bold">Peran yang Diajukan</label>
                            <div class="mt-2 inline-block px-4 py-2 bg-orange-50 text-[#BB4D00] font-bold rounded-lg border border-orange-100">
                                {{ volunteer.role }}
                            </div>
                        </div>

                        <div>
                            <label class="text-xs text-gray-400 uppercase tracking-wide font-bold">Pengalaman</label>
                            <div class="mt-2 bg-gray-50 p-4 rounded-xl border border-gray-100 text-gray-700 leading-relaxed">
                                {{ volunteer.experience }}
                            </div>
                        </div>
                    </div>

                </div>

                <div class="space-y-6">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sticky top-28">
                        <h3 class="font-bold text-gray-800 mb-4">Tindakan Admin</h3>
                        
                        <div class="space-y-3">
                            <button 
                                v-if="volunteer.status !== 'verified'"
                                @click="updateStatus('verified')"
                                class="w-full py-3 bg-[#47622A] hover:bg-[#364b20] text-white font-bold rounded-xl shadow-md transition flex justify-center items-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Verifikasi Relawan
                            </button>

                            <button 
                                v-if="volunteer.status !== 'rejected'"
                                @click="updateStatus('rejected')"
                                class="w-full py-3 bg-white border-2 border-red-100 text-red-600 hover:bg-red-50 font-bold rounded-xl transition flex justify-center items-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Tolak Permohonan
                            </button>
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-100 text-center text-xs text-gray-400">
                            <p>Diajukan pada: {{ new Date(volunteer.created_at).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>