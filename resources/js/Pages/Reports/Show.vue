<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";

const props = defineProps({
    report: Object,
});

const getStatusBadge = (status) => {
    const map = {
        verification: { label: "Menunggu Verifikasi Admin", class: "bg-gray-500" },
        pending: { label: "Menunggu Pembayaran", class: "bg-[#CA8E31]" },
        process: { label: "Dalam Pengerjaan", class: "bg-[#4688FB]" },
        completed: { label: "Selesai", class: "bg-[#09A600]" },
        cancelled: { label: "Dibatalkan", class: "bg-red-500" },
    };
    return map[status] || map["verification"];
};

const statusInfo = getStatusBadge(props.report?.status || 'verification');

const timelineSteps = [
    {
        title: "Pengajuan & Verifikasi",
        date: props.report.created_at_formatted,
        desc: "Laporan sedang diperiksa admin.",
        status: props.report.status !== "verification" ? "completed" : "current",
        drive_link: null, 
    },
    {
        title: "Pembayaran & Kontrak",
        date: props.report.status === "pending" || props.report.status === "verification" ? "Menunggu" : "Lunas",
        desc: "Persetujuan biaya dan kontrak kerja.",
        status: props.report.status === "process" || props.report.status === "completed" ? "completed" : (props.report.status === "pending" ? "current" : "upcoming"),
        drive_link: null,
    },
    {
        title: "Pengerjaan & Survei",
        date: "Estimasi setelah bayar",
        desc: "Tim vendor melakukan perbaikan.",
        status: props.report.status === "process" || props.report.status === "completed" ? "current" : "upcoming",
        drive_link: props.report.drive_link,
    },
    {
        title: "Pekerjaan Selesai",
        date: "-",
        desc: "Seluruh pekerjaan telah diselesaikan.",
        status: props.report.status === "completed" ? "completed" : "upcoming",
        drive_link: null,
    },
];

const vendor = {
    name: "PT Green Restoration Indonesia",
    phone: "+62 812-3456-7890",
    email: "info@greenrestoration.co.id",
};

const payment = {
    total: props.report.price,
    method: "Transfer Bank BCA",
    status: props.report.status === "pending" ? "Belum Lunas" : "Lunas",
};
</script>

<template>
    <Head title="Detail Laporan" />
    <Navbar />

    <div class="min-h-screen bg-[#FFFDF4] font-['Montserrat'] pb-20">
        <div class="w-full bg-[linear-gradient(to_right,#BB4D00,#4B741F)] pt-32 pb-16 px-6 relative overflow-hidden">
            <div class="max-w-6xl mx-auto relative z-10">
                <Link :href="route('reports.index')" class="inline-flex items-center text-white/90 hover:text-white mb-6 transition gap-2 group">
                    <svg class="w-6 h-6 transform group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span class="font-medium text-lg">Kembali ke Daftar Pengajuan</span>
                </Link>

                <div class="flex flex-col md:flex-row md:items-center gap-4 mb-3">
                    <h1 class="text-[#FFFFFF] text-4xl md:text-5xl font-bold tracking-tight">REQ-{{ props.report.id }}</h1>
                    <span :class="`${statusInfo.class} px-4 py-1.5 rounded-lg font-bold text-sm shadow-md border border-white/20 text-white`">{{ statusInfo.label }}</span>
                </div>
                <h2 class="text-xl md:text-2xl font-light text-white/90 max-w-3xl leading-relaxed">{{ props.report.title }}</h2>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-6 space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 mt-10">
                
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-gray-600 font-medium">Progress Keseluruhan</span>
                            <span class="text-2xl font-bold text-[#F54900]">{{ props.report.progress }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-3 mb-3">
                            <div class="bg-[#F54900] h-3 rounded-full transition-all duration-1000" :style="{ width: props.report.progress + '%' }"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-400">
                            <span>Mulai: {{ props.report.created_at_formatted }}</span>
                            <span>Target: Estimasi 30 Hari</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-8 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800 mb-8 flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#F54900]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Timeline Pekerjaan
                        </h3>
                        <div class="relative pl-4">
                            <div class="absolute top-2 left-[27px] h-full w-[2px] bg-gray-200"></div>
                            <div v-for="(step, index) in timelineSteps" :key="index" class="relative pl-12 pb-10 last:pb-0">
                                <div class="absolute top-0 left-0 w-14 h-14 flex items-center justify-center bg-white">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center z-10 border-2" :class="step.status === 'completed' ? 'bg-[#00C853] border-[#00C853]' : step.status === 'current' ? 'bg-white border-[#F54900] animate-pulse' : 'bg-gray-100 border-gray-300'">
                                        <svg v-if="step.status === 'completed'" class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        <div v-else class="w-2.5 h-2.5 rounded-full" :class="step.status === 'current' ? 'bg-[#F54900]' : 'bg-gray-300'"></div>
                                    </div>
                                </div>
                                <div v-if="step.status === 'completed'" class="bg-[#F8FDF9] rounded-lg p-5 border border-green-50">
                                    <h4 class="font-bold text-gray-800">{{ step.title }}</h4>
                                    <p class="text-sm text-gray-600 mb-3">{{ step.desc }}</p>
                                    <div v-if="step.drive_link" class="mt-3">
                                        <a :href="step.drive_link" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg text-sm font-bold border border-blue-100 hover:bg-blue-100 transition">
                                            Link Drive <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    </div>
                                </div>
                                <div class="p-2" v-else>
                                    <h4 class="font-bold" :class="step.status === 'current' ? 'text-gray-800' : 'text-gray-400'">{{ step.title }}</h4>
                                    <p class="text-sm text-gray-400">{{ step.desc }}</p>
                                    <div v-if="step.drive_link && step.status === 'current'" class="mt-2">
                                        <a :href="step.drive_link" target="_blank" class="text-sm text-blue-600 hover:underline flex items-center gap-1">Buka Drive</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    
                    <div v-if="props.report.status === 'pending' || props.report.status === 'process'" class="mb-4">
                        <Link :href="route('reports.offer', props.report.id)" class="block w-full bg-[#BB4D00] hover:bg-[#973C00] text-white text-center font-bold py-4 rounded-xl shadow-lg transition transform hover:-translate-y-1 flex justify-center items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Lihat Penawaran & Kontrak
                        </Link>
                    </div>
                    <div v-else-if="props.report.status === 'verification'" class="bg-yellow-50 rounded-xl p-6 border border-yellow-200 text-center text-yellow-800 text-sm mb-4">
                        ⏳ Menunggu Admin membuat penawaran & estimasi.
                    </div>

                    <div v-if="props.report.status !== 'verification'" class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <h3 class="font-bold text-[#BB4D00] mb-4 flex items-center gap-2"><span class="text-lg">$</span> Informasi Pembayaran</h3>
                        <div class="space-y-4">
                            <div><p class="text-xs text-gray-400">Total Biaya</p><p class="text-xl font-bold text-[#BB4D00]">{{ payment.total }}</p></div>
                            <div class="border-t pt-3"><p class="text-xs text-gray-400 mb-1">Metode Pembayaran</p><p class="text-gray-800 font-medium">{{ payment.method }}</p></div>
                            <div v-if="payment.status === 'Lunas'" class="bg-[#ECFDF5] border border-[#A7F3D0] text-[#047857] px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-bold">Lunas</div>
                            <div v-else class="bg-[#FFFBEB] border border-[#FDE68A] text-[#B45309] px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-bold">Menunggu Pembayaran</div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <h3 class="font-bold text-[#4B741F] mb-4 flex items-center gap-2">Informasi Vendor</h3>
                        <div class="space-y-3">
                            <div><p class="text-xs text-gray-400">Nama Vendor</p><p class="font-bold text-gray-800">{{ vendor.name }}</p></div>
                            <div class="flex items-center gap-2 text-sm text-gray-600">{{ vendor.phone }}</div>
                            <div class="flex items-center gap-2 text-sm text-gray-600">{{ vendor.email }}</div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <h3 class="font-bold text-[#BB4D00] mb-4 flex items-center gap-2">Lokasi Perbaikan</h3>
                        <div class="mb-4"><p class="text-xs text-gray-400">Alamat Lengkap</p><p class="text-sm text-gray-700 leading-relaxed mt-1">{{ props.report.location }}</p></div>
                        <a :href="`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(props.report.location)}`" target="_blank" class="block w-full text-center border border-[#BB4D00] text-[#BB4D00] hover:bg-orange-50 font-bold py-2 rounded-lg transition text-sm">Lihat di Maps</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>