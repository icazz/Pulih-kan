<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    vendors: Array,
    auth: Object
});

const searchQuery = ref('');
const selectedCategory = ref('Semua');

// List unik kategori jasa dari semua vendor
const categories = computed(() => {
    const allServices = props.vendors.flatMap(v => v.jenis_jasa || []);
    return ['Semua', ...new Set(allServices)];
});

// Logic Filter
const filteredVendors = computed(() => {
    return props.vendors.filter(vendor => {
        const matchesSearch = vendor.nama_mitra.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                              vendor.alamat.toLowerCase().includes(searchQuery.value.toLowerCase());
        
        const matchesCategory = selectedCategory.value === 'Semua' || 
                                (vendor.jenis_jasa && vendor.jenis_jasa.includes(selectedCategory.value));

        return matchesSearch && matchesCategory;
    });
});

// Helper: Tentukan Icon berdasarkan jenis jasa
const getCategoryIcons = (services) => {
    const icons = [];
    const s = services.map(srv => srv.toLowerCase()).join(' ');

    if (s.includes('listrik')) {
        icons.push({ path: 'M13 10V3L4 14h7v7l9-11h-7z', color: 'text-yellow-500', bg: 'bg-yellow-100' });
    }
    if (s.includes('air') || s.includes('pipa') || s.includes('keran')) {
        icons.push({ path: 'M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z', color: 'text-blue-500', bg: 'bg-blue-100' });
    }
    if (s.includes('atap') || s.includes('genteng') || s.includes('bocor')) {
        icons.push({ path: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', color: 'text-orange-500', bg: 'bg-orange-100' });
    }
    if (s.includes('jamur') || s.includes('lembab')) {
        icons.push({ path: 'M12 4V2m0 20v-2m8-8h2M2 12h2m13.657-5.657l1.414-1.414M4.929 19.071l1.414-1.414m0-11.314L4.93 4.93m14.14 14.14l-1.414-1.414M12 17a5 5 0 100-10 5 5 0 000 10z', color: 'text-teal-600', bg: 'bg-teal-100' });
    }
    if (s.includes('lumpur') || s.includes('puing') || s.includes('banjir')) {
        icons.push({ 
            path: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
            color: 'text-[#8D6E63]', bg: 'bg-[#D7CCC8]' 
        });
    }
    if (icons.length === 0) {
        icons.push({ path: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', color: 'text-gray-500', bg: 'bg-gray-100' });
    }
    return icons;
};
</script>

<template>
    <Head title="Cari Mitra Vendor" />
    <Navbar />

    <div class="min-h-screen bg-[#F0F4F0] font-['Montserrat'] pb-20">
        
        <div class="bg-gradient-to-r from-[#231D13] via-[#6B8146] to-[#C4E899] pt-32 pb-16 px-6 text-center shadow-md relative">
            
            <Link :href="route('welcome')" class="absolute top-24 left-6 md:left-12 flex items-center gap-2 text-white/70 hover:text-white transition text-sm font-medium group">
                <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </Link>

            <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">Daftar Mitra</h1>
            <p class="text-white/70 max-w-2xl mx-auto mb-8">
                Temukan mitra terpercaya untuk kebutuhan pemulihan Anda.
            </p>

            <div class="max-w-xl mx-auto relative">
                <input v-model="searchQuery" type="text" placeholder="Cari nama mitra atau kota..." 
                    class="w-full py-4 pl-12 pr-4 rounded-full border-none shadow-lg focus:ring-2 focus:ring-[#6B8146] text-sm">
                <svg class="w-6 h-6 text-gray-400 absolute left-4 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 mt-8">
            
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center gap-4 mb-6">
                <div class="flex items-center gap-2 text-gray-700 font-bold text-sm min-w-fit">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Kategori Mitra
                </div>
                
                <div class="flex gap-2 overflow-x-auto no-scrollbar w-full">
                    <button v-for="cat in categories" :key="cat" @click="selectedCategory = cat"
                        class="px-4 py-2 rounded-lg text-xs font-bold whitespace-nowrap transition"
                        :class="selectedCategory === cat ? 'bg-[#84CC16] text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                        {{ cat === 'Semua' ? 'Semua Mitra (' + vendors.length + ')' : cat }}
                    </button>
                </div>
            </div>

            <div class="mb-6 text-gray-500 text-sm">
                Menampilkan <span class="font-bold text-gray-800">{{ filteredVendors.length }}</span> mitra
            </div>

            <div v-if="filteredVendors.length === 0" class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-300">
                <p class="text-gray-400 italic">Tidak ada mitra ditemukan dengan filter ini.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="vendor in filteredVendors" :key="vendor.id" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all duration-300 relative group flex flex-col h-full">
                    
                    <div class="flex gap-2 mb-4">
                        <div v-for="(icon, i) in getCategoryIcons(vendor.jenis_jasa || [])" :key="i"
                            class="w-12 h-12 rounded-full flex items-center justify-center transition-colors"
                            :class="icon.bg">
                            <svg class="w-6 h-6" :class="icon.color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="icon.path" />
                            </svg>
                        </div>
                    </div>

                    <h3 class="font-bold text-gray-900 text-xl mb-1">{{ vendor.nama_mitra }}</h3>
                    <div class="flex items-center gap-1.5 text-sm mb-4">
                        <span class="text-yellow-400 text-lg">★</span>
                        <span class="font-bold text-gray-900">{{ vendor.rating || 'Belum ada ulasan' }}</span>
                        <span class="text-gray-300">•</span>
                        <span class="text-gray-500">{{ vendor.review_count || 0 }} ulasan</span>
                    </div>

                    <div class="space-y-2 mb-6">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span>{{ vendor.project_count }} project selesai</span> 
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="truncate">{{ vendor.alamat }}</span>
                        </div>
                    </div>

                    <div class="mb-6 flex-1">
                        <div class="flex items-center gap-1 text-xs text-gray-400 mb-2">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                            Spesialisasi:
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="(service, idx) in vendor.jenis_jasa" :key="idx" 
                                  class="px-2.5 py-1 bg-green-50 text-green-700 text-[10px] font-bold rounded-md">
                                {{ service }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-auto text-right">
                        <Link 
                            :href="route('vendor.show', vendor.id)" 
                            class="inline-flex items-center gap-1 px-5 py-2.5 bg-[#84CC16] hover:bg-[#65a30d] text-white font-bold rounded-lg text-xs transition shadow-md hover:shadow-lg"
                        >
                            Lihat Profil
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </Link>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>