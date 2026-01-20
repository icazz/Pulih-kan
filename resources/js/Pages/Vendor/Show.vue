<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';

const props = defineProps({
    vendor: Object,
    ratingDist: Object, // Distribusi bintang 5,4,3,2,1
    reviews: Array,
    auth: Object
});

// Helper hitung persentase bar rating
const getPercentage = (count) => {
    if (props.vendor.review_count === 0) return 0;
    return (count / props.vendor.review_count) * 100;
};
</script>

<template>
    <Head :title="vendor.nama_mitra" />
    <Navbar />

    <div class="min-h-screen bg-[#F0F4F0] font-['Montserrat'] pb-20 pt-16">
        
        <div class="bg-gradient-to-r from-[#4F6F46] to-[#6C8D5D] text-white pt-12 pb-12 px-6 shadow-lg">
            <div class="max-w-6xl mx-auto relative">
                <Link :href="route('vendor.list')" class="inline-flex items-center gap-2 text-white/80 hover:text-white transition text-sm mb-8 group">
                    <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Daftar Mitra
                </Link>

                <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
                    <div class="relative flex-shrink-0">
                        <div class="w-32 h-32 rounded-2xl bg-white/20 backdrop-blur-sm border-2 border-white/30 flex items-center justify-center text-4xl font-bold shadow-2xl text-white">
                             {{ vendor.initial }}
                        </div>
                        <div class="absolute -bottom-3 -right-3 bg-green-500 text-white p-1.5 rounded-full border-4 border-[#5E7E55] shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                    </div>

                    <div class="text-center md:text-left space-y-3 flex-1">
                        <div>
                            <h1 class="text-3xl md:text-4xl font-bold tracking-tight">{{ vendor.nama_mitra }}</h1>
                            <p class="text-white/80 text-sm mt-1">Mitra Pemulihan & Konstruksi Terpercaya</p>
                        </div>
                        
                        <div class="flex flex-wrap justify-center md:justify-start gap-4 text-xs font-medium text-white/90">
                            <div class="flex items-center gap-1 bg-black/20 px-3 py-1 rounded-full backdrop-blur-sm border border-white/10">
                                <span class="text-yellow-400 text-sm">★</span> {{ vendor.rating }} 
                            </div>
                            <div class="flex items-center gap-1 bg-black/20 px-3 py-1 rounded-full backdrop-blur-sm border border-white/10">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                {{ vendor.review_count }} Ulasan
                            </div>
                            <div class="flex items-center gap-1 bg-black/20 px-3 py-1 rounded-full backdrop-blur-sm border border-white/10">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                {{ vendor.project_count }} Project Selesai
                            </div>
                        </div>

                        <div class="flex flex-wrap justify-center md:justify-start gap-3 pt-2">
                            <div class="flex items-center gap-2 bg-white/10 px-4 py-2 rounded-lg text-xs backdrop-blur-sm border border-white/10">
                                <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ vendor.alamat }}
                            </div>
                            <a :href="`https://wa.me/${vendor.no_telepon}`" target="_blank" class="flex items-center gap-2 bg-white/10 px-4 py-2 rounded-lg text-xs backdrop-blur-sm hover:bg-white/20 transition cursor-pointer border border-white/10">
                                <svg class="w-4 h-4 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                {{ vendor.no_telepon }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="max-w-6xl mx-auto px-6 mt-8 space-y-8">
            
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-6 text-lg border-b border-gray-100 pb-4">Tentang Mitra</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-[#F3F8F2] p-8 rounded-xl text-center border border-[#E0EBDD] hover:shadow-md transition">
                        <div class="text-[#5E7E55] mb-3 flex justify-center">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <h4 class="text-4xl font-bold text-gray-800">{{ vendor.project_count }}</h4>
                        <p class="text-sm text-gray-500 font-bold uppercase tracking-wider mt-1">Project Selesai</p>
                    </div>
                    <div class="bg-[#F3F8F2] p-8 rounded-xl text-center border border-[#E0EBDD] hover:shadow-md transition">
                        <div class="text-[#5E7E55] mb-3 flex justify-center">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                        </div>
                        <h4 class="text-4xl font-bold text-gray-800">{{ vendor.rating }}</h4>
                        <p class="text-sm text-gray-500 font-bold uppercase tracking-wider mt-1">Rating Rata-rata</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    <div class="text-center lg:text-left">
                        <h3 class="font-bold text-gray-800 mb-4 text-lg">Ringkasan Ulasan</h3>
                        <div class="text-7xl font-bold text-[#5E7E55]">{{ vendor.rating }}</div>
                        <div class="flex justify-center lg:justify-start text-yellow-400 text-2xl my-3 gap-1">
                            <span v-for="i in 5" :key="i">{{ i <= Math.round(vendor.rating) ? '★' : '☆' }}</span>
                        </div>
                        <p class="text-sm text-gray-500 font-medium">Berdasarkan {{ vendor.review_count }} ulasan pelanggan</p>
                    </div>

                    <div class="lg:col-span-2 space-y-3 pt-2">
                        <div v-for="star in [5, 4, 3, 2, 1]" :key="star" class="flex items-center gap-4 text-sm">
                            <div class="flex items-center w-12 gap-1 font-bold text-gray-600">
                                {{ star }} <span class="text-yellow-400 text-lg">★</span>
                            </div>
                            <div class="flex-1 h-3 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-[#84CC16]" :style="{ width: getPercentage(ratingDist[star]) + '%' }"></div>
                            </div>
                            <span class="w-10 text-right text-gray-500 text-xs font-bold">{{ ratingDist[star] }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-10 pt-10 border-t border-gray-100">
                    <h4 class="font-bold text-gray-800 mb-6 text-sm uppercase tracking-wide text-gray-400">Metrik Performa</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-[#F9FAF9] p-5 rounded-xl border border-gray-100">
                            <p class="text-xs text-gray-500 mb-2 font-bold uppercase">Ketepatan Waktu</p>
                            <div class="flex items-end gap-3">
                                <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden"><div class="w-[95%] h-full bg-[#84CC16] rounded-full"></div></div>
                                <span class="text-sm font-bold text-gray-800">95%</span>
                            </div>
                        </div>
                        <div class="bg-[#F9FAF9] p-5 rounded-xl border border-gray-100">
                            <p class="text-xs text-gray-500 mb-2 font-bold uppercase">Kualitas Pekerjaan</p>
                            <div class="flex items-end gap-3">
                                <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden"><div class="w-[98%] h-full bg-[#84CC16] rounded-full"></div></div>
                                <span class="text-sm font-bold text-gray-800">98%</span>
                            </div>
                        </div>
                        <div class="bg-[#F9FAF9] p-5 rounded-xl border border-gray-100">
                            <p class="text-xs text-gray-500 mb-2 font-bold uppercase">Komunikasi</p>
                            <div class="flex items-end gap-3">
                                <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden"><div class="w-[92%] h-full bg-[#84CC16] rounded-full"></div></div>
                                <span class="text-sm font-bold text-gray-800">92%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-8 border-b border-gray-100 pb-4">
                    <h3 class="font-bold text-gray-800 text-lg">Apa Kata Mereka?</h3>
                </div>

                <div v-if="reviews.length === 0" class="text-center py-12">
                    <div class="inline-block p-4 bg-gray-50 rounded-full mb-3">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <p class="text-gray-500 italic">Belum ada ulasan untuk vendor ini.</p>
                </div>

                <div class="space-y-8">
                    <div v-for="review in reviews" :key="review.id" class="border-b border-gray-100 last:border-0 pb-8 last:pb-0">
                        <div class="flex items-start gap-5">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-green-100 to-green-200 text-green-700 flex items-center justify-center font-bold text-lg shadow-sm">
                                {{ review.user_initial }}
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-2">
                                    <div>
                                        <h5 class="font-bold text-gray-900">{{ review.user_name }}</h5>
                                        <p class="text-[10px] uppercase font-bold text-green-600 bg-green-50 inline-block px-2 py-0.5 rounded mt-1 border border-green-100">Terverifikasi</p>
                                    </div>
                                    <div class="flex items-center gap-2 mt-2 sm:mt-0">
                                        <div class="flex text-yellow-400 text-sm">
                                            <span v-for="n in 5" :key="n">{{ n <= review.rating ? '★' : '☆' }}</span>
                                        </div>
                                        <span class="text-gray-400 text-xs">{{ review.date }}</span>
                                    </div>
                                </div>
                                
                                <div class="mt-3 mb-3 inline-flex items-center gap-1 px-3 py-1 bg-gray-50 text-gray-600 text-xs rounded-full font-medium border border-gray-200">
                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    Project: {{ review.project_name }}
                                </div>

                                <p class="text-gray-700 leading-relaxed italic bg-gray-50/50 p-4 rounded-xl border border-gray-100 relative">
                                    <span class="absolute top-2 left-2 text-gray-200 text-4xl font-serif">“</span>
                                    <span class="relative z-10">{{ review.comment }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>