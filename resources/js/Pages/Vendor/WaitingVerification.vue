<template>
    <div class="min-h-screen bg-[#FEF8DF] flex flex-col items-center justify-center p-6 text-center font-['Montserrat']">
        <div class="w-24 h-24 bg-[#39E3EC] rounded-full flex items-center justify-center mb-6 shadow-lg border-4 border-white">
            <svg class="w-12 h-12 text-[#4F3726] animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        
        <h1 class="text-3xl font-bold text-[#4F3726] mb-3">Verifikasi Sedang Berlangsung</h1>
        <p class="text-[#8C8070] max-w-md mb-10 leading-relaxed">
            Halo <strong>{{ $page.props.auth?.user?.nama_mitra || 'Calon Mitra' }}</strong>, pendaftaran Anda telah kami terima. Admin akan memverifikasi data Anda dalam 1-3 hari kerja.
        </p>
        
        <Link :href="route('welcome')" class="px-8 py-3 bg-[#4F3726] text-white rounded-xl font-bold shadow-md hover:bg-[#3a281c] transition-all">
            Kembali ke Beranda
        </Link>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<style scoped>
.animate-spin-slow {
    animation: spin 3s linear infinite;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>