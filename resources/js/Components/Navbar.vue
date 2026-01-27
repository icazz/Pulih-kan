<script setup>
import { Link, usePage, router } from "@inertiajs/vue3";
import { computed, ref, onMounted, onUnmounted } from "vue";

const page = usePage();
const user = computed(() => page.props.auth?.user);

// State untuk Menu Mobile (Hamburger) & Dropdown User
const isMobileMenuOpen = ref(false);
const isUserDropdownOpen = ref(false);

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const toggleUserDropdown = () => {
    isUserDropdownOpen.value = !isUserDropdownOpen.value;
};

// Tutup dropdown jika klik di luar
const closeDropdowns = (e) => {
    if (!e.target.closest('#user-menu-btn') && !e.target.closest('#user-menu-dropdown')) {
        isUserDropdownOpen.value = false;
    }
};

onMounted(() => window.addEventListener('click', closeDropdowns));
onUnmounted(() => window.removeEventListener('click', closeDropdowns));

const logout = () => {
    router.post(route("logout"));
};

const menuItems = [
    { text: "Layanan", href: "/#layanan" },
    { text: "Proses", href: "/#proses" },
    { text: "Mitra", href: "/#mitra" },
    { text: "Testimoni", href: "/#testimoni" },
    { text: "Kontak", href: "/#kontak" },
];
</script>

<template>
    <nav class="fixed w-full z-50 top-0 h-16 shadow-lg font-['Montserrat'] transition-all duration-300 bg-gradient-to-r from-[#FDFDFD] from-5% via-[#4C1E00] via-40% to-[#050200] to-100%">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex justify-between items-center">

            <div class="flex-shrink-0 flex items-center">
                <a href="/">
                    <img src="/images/logo-pulihkan.png" alt="Logo Pulih.kan" class="h-10 w-auto object-contain" />
                </a>
            </div>

            <div class="hidden lg:flex space-x-8 items-center">
                <a v-for="item in menuItems" :key="item.text" :href="item.href"
                    class="text-white text-sm font-normal hover:text-[#FF8C00] transition-colors duration-300 tracking-wide">
                    {{ item.text }}
                </a>
            </div>

            <div class="flex items-center gap-2 sm:gap-4">
                
                <Link :href="route('vendor.list')" 
                    class="flex items-center gap-2 px-3 py-1.5 sm:px-5 sm:py-1.5 rounded-2xl bg-[#7C3507] hover:bg-[#96420a] text-white font-medium text-xs sm:text-sm transition-all shadow-md border border-white/10 group">
                    <img src="/images/icon-love.png" alt="Icon" class="w-4 h-4 object-contain group-hover:scale-110 transition-transform" />
                    <span class="hidden sm:inline">Review</span>
                </Link>

                <template v-if="!user">
                    <Link :href="route('login')"
                        class="flex items-center gap-2 px-3 py-1.5 sm:px-5 sm:py-2 rounded-lg bg-[#1F1F1F] hover:bg-black text-white text-xs font-semibold transition-transform hover:scale-105 shadow-md border border-white/10">
                        <img src="/images/icon-masuk.png" alt="Icon" class="w-3.5 h-3.5 invert brightness-0 saturate-100" />
                        <span class="hidden sm:inline">Masuk</span>
                    </Link>
                </template>

                <template v-else>
                    <div class="relative">
                        <button id="user-menu-btn" @click.stop="toggleUserDropdown"
                            class="flex items-center gap-2 px-3 py-1.5 sm:px-4 sm:py-2 rounded-lg bg-[#1F1F1F] hover:bg-black text-white text-xs font-semibold border border-white/10 focus:outline-none">
                            <svg class="w-4 h-4 text-[#FF8C00]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="hidden sm:inline max-w-[100px] truncate">Halo, {{ user.name }}</span>
                            <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': isUserDropdownOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div id="user-menu-dropdown" v-show="isUserDropdownOpen"
                            class="absolute right-0 top-full mt-2 w-48 bg-[#1C1917] rounded-lg shadow-xl border border-white/10 overflow-hidden py-1 z-50 origin-top-right transition-all">
                            <div class="px-4 py-2 text-xs text-gray-400 sm:hidden block border-b border-white/10 mb-1">
                                Signed in as <br><strong class="text-white">{{ user.name }}</strong>
                            </div>
                            <Link :href="route('profile.edit')" class="block px-4 py-2 text-sm text-gray-300 hover:bg-[#2D190D] hover:text-white transition-colors">
                                Profile Saya
                            </Link>
                            <button @click="logout" class="block w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-[#2D190D] hover:text-red-300 transition-colors">
                                Keluar
                            </button>
                        </div>
                    </div>
                </template>

                <button @click="toggleMobileMenu" class="lg:hidden text-white focus:outline-none p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="!isMobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div v-show="isMobileMenuOpen" class="lg:hidden bg-[#1C1917] border-t border-white/10 shadow-xl absolute w-full left-0 top-16">
            <div class="px-4 py-4 space-y-2">
                <a v-for="item in menuItems" :key="item.text" :href="item.href"
                    @click="isMobileMenuOpen = false"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-[#2D190D] transition">
                    {{ item.text }}
                </a>
                
                <template v-if="!user">
                    <Link :href="route('login')" class="block w-full text-center px-3 py-2 mt-4 rounded-md bg-[#7C3507] text-white font-bold text-sm">
                        Masuk Sekarang
                    </Link>
                </template>
            </div>
        </div>
    </nav>
</template>