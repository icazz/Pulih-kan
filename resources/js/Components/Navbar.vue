<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted } from 'vue';

// Mengambil data User dari Global Props Inertia
const page = usePage();
const user = computed(() => page.props.auth.user);

// State untuk Dropdown User
const isDropdownOpen = ref(false);
const dropdownRef = ref(null);

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};

// Menutup dropdown jika klik di luar
const closeDropdown = (e) => {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        isDropdownOpen.value = false;
    }
};

onMounted(() => document.addEventListener('click', closeDropdown));
onUnmounted(() => document.removeEventListener('click', closeDropdown));

// Fungsi Logout
const logout = () => {
    router.post(route('logout'));
};

const menuItems = [
    { text: "Layanan", href: "/#layanan" },
    { text: "Proses", href: "/#proses" },
    { text: "Mitra Vendor", href: "/#mitra" },
    { text: "Testimoni", href: "/#testimoni" },
    { text: "Kontak", href: "/#kontak" },
];
</script>

<template>
    <nav class="fixed w-full z-50 top-0 h-16 flex items-center shadow-lg font-['Montserrat'] transition-all duration-300 bg-gradient-to-r from-[#FDFDFD] from-5% via-[#4C1E00] via-40% to-[#050200] to-100%">
        <div class="w-full max-w-[100%] xl:max-w-[90%] mx-auto flex justify-start items-center">
            
            <div class="flex-shrink-0">
                <a href="#hero">
                    <img
                        src="/images/logo-pulihkan.png"
                        alt="Logo Pulih.kan"
                        class="h-14 w-auto object-contain"
                    />
                </a>
            </div>

            <div class="hidden lg:flex space-x-8 ml-auto items-center">
                <a
                    v-for="item in menuItems"
                    :key="item.text"
                    :href="item.href"
                    class="text-white text-sm font-normal hover:text-[#6C2F05] transition-colors duration-300 tracking-wide"
                >
                    {{ item.text }}
                </a>
            </div>

            <div class="flex items-center space-x-3 ml-20 relative" ref="dropdownRef">
                
                <template v-if="!user">
                    <Link
                        :href="route('login')"
                        class="flex items-center gap-2 px-5 py-2 rounded-lg bg-[#973C00] hover:bg-[#7a3000] text-white text-xs font-semibold transition-transform hover:scale-105 shadow-md border border-white/10"
                    >
                        <img
                            src="/images/icon-love.png"
                            alt="Icon"
                            class="w-3.5 h-3.5 invert brightness-0 saturate-100"
                        />
                        <span>Daftar Relawan</span>
                    </Link>

                    <Link
                        :href="route('login')"
                        class="flex items-center gap-2 px-5 py-2 rounded-lg bg-[#1F1F1F] hover:bg-black text-white text-xs font-semibold transition-transform hover:scale-105 shadow-md border border-white/10"
                    >
                        <img
                            src="/images/icon-door.png"
                            alt="Icon"
                            class="w-3.5 h-3.5 invert brightness-0 saturate-100"
                        />
                        <span>Masuk</span>
                    </Link>
                </template>

                <template v-else>
                    <button 
                        @click.stop="toggleDropdown" 
                        class="flex items-center gap-2 px-5 py-2 rounded-lg bg-[#1F1F1F] hover:bg-black text-white text-xs font-semibold transition-all border border-white/10 focus:outline-none"
                    >
                        <svg class="w-4 h-4 text-[#FF8C00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span>Halo, {{ user.name }}</span>
                        <svg class="w-3 h-3 ml-1 transition-transform duration-200" :class="{'rotate-180': isDropdownOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div v-show="isDropdownOpen" class="absolute right-0 top-full mt-2 w-48 bg-[#1C1917] rounded-lg shadow-xl border border-white/10 overflow-hidden py-1 z-50 origin-top-right transition-all">
                        <Link :href="route('profile.edit')" class="block px-4 py-2 text-sm text-gray-300 hover:bg-[#2D190D] hover:text-white transition-colors">
                            Profile Saya
                        </Link>
                        <div class="border-t border-white/10 my-1"></div>
                        <button @click="logout" class="block w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-[#2D190D] hover:text-red-300 transition-colors">
                            Keluar
                        </button>
                    </div>
                </template>

            </div>

            <div class="lg:hidden flex items-center ml-auto">
                <button class="text-white focus:outline-none">
                    <svg
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7"
                        ></path>
                    </svg>
                </button>
            </div>
        </div>
    </nav>
</template>