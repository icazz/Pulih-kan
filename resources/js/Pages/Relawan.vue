<script setup>
import { Head, useForm, Link } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";

// Tidak perlu props existingData lagi karena kita ingin form selalu fresh

const form = useForm({
    name: '',
    email: '',
    phone: '',
    emergency_contact: '',
    province: '',
    city: '',
    address: '',
    role: '',
    experience: '',
    agree: false,
});

const submit = () => {
    form.post(route('relawan.store'), {
        onSuccess: () => {
            // Reset form agar bisa diisi ulang (sesuai permintaan)
            form.reset();
            alert('Terima kasih! Data Anda berhasil disimpan.');
        },
    });
};
</script>

<template>
    <Head title="Daftar Relawan" />
    <Navbar />

    <div class="min-h-screen bg-[#FFFBEA] relative font-['Montserrat']">
        
        <div class="relative w-full h-[500px]">
            <img src="/images/banner-relawan.jpg" alt="Relawan" class="w-full h-full object-cover brightness-50" />
            <div class="absolute top-28 left-6 md:left-16 z-30">
                <Link :href="route('donasi')" 
                    class="group flex items-center gap-3 text-white bg-white/10 hover:bg-white/20 backdrop-blur-md px-6 py-3 rounded-full border border-white/20 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span class="font-semibold text-sm md:text-base">Kembali</span>
                </Link>
            </div>
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center pt-20 px-4">
                <h1 class="text-5xl md:text-7xl font-extrabold leading-tight text-white">
                    Bersama Pulihkan,<br>
                    <span class="text-[#FFF4D5]">Jadi Relawan</span>
                </h1>
                <p class="mt-4 text-lg text-white">Ambil peran nyata dalam proses pemulihan pasca bencana</p>
            </div>
        </div>

        <div class="relative z-10 mt-16 max-w-5xl mx-auto bg-gradient-to-b from-[#FFFBEA] to-[#99968C] rounded-[50px] md:rounded-[96px] p-8 md:p-16 shadow-2xl mb-16">
            
            <form @submit.prevent="submit" class="space-y-8">
                
                <div>
                    <h2 class="text-2xl font-bold text-black mb-6">Data Diri</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <input v-model="form.name" type="text" placeholder="Nama Lengkap" 
                                class="w-full px-6 py-4 rounded-[20px] bg-transparent border border-black/40 placeholder-black/60 focus:bg-white transition text-lg" />
                            <div v-if="form.errors.name" class="text-red-600 text-sm mt-1 ml-2">{{ form.errors.name }}</div>
                        </div>

                        <div>
                            <input v-model="form.email" type="email" placeholder="Email" 
                                class="w-full px-6 py-4 rounded-[20px] bg-transparent border border-black/40 placeholder-black/60 focus:bg-white transition text-lg" />
                            <div v-if="form.errors.email" class="text-red-600 text-sm mt-1 ml-2">{{ form.errors.email }}</div>
                        </div>

                        <div>
                            <input v-model="form.phone" type="text" placeholder="Nomor Telepon" 
                                class="w-full px-6 py-4 rounded-[20px] bg-transparent border border-black/40 placeholder-black/60 focus:bg-white transition text-lg" />
                            <div v-if="form.errors.phone" class="text-red-600 text-sm mt-1 ml-2">{{ form.errors.phone }}</div>
                        </div>

                        <div>
                            <input v-model="form.emergency_contact" type="text" placeholder="Kontak Darurat" 
                                class="w-full px-6 py-4 rounded-[20px] bg-transparent border border-black/40 placeholder-black/60 focus:bg-white transition text-lg" />
                            <div v-if="form.errors.emergency_contact" class="text-red-600 text-sm mt-1 ml-2">{{ form.errors.emergency_contact }}</div>
                        </div>

                        <div>
                            <input v-model="form.province" type="text" placeholder="Provinsi Asal" 
                                class="w-full px-6 py-4 rounded-[20px] bg-transparent border border-black/40 placeholder-black/60 focus:bg-white transition text-lg" />
                            <div v-if="form.errors.province" class="text-red-600 text-sm mt-1 ml-2">{{ form.errors.province }}</div>
                        </div>

                        <div>
                            <input v-model="form.city" type="text" placeholder="Kecamatan / Kota Asal" 
                                class="w-full px-6 py-4 rounded-[20px] bg-transparent border border-black/40 placeholder-black/60 focus:bg-white transition text-lg" />
                            <div v-if="form.errors.city" class="text-red-600 text-sm mt-1 ml-2">{{ form.errors.city }}</div>
                        </div>

                        <div class="md:col-span-2">
                            <input v-model="form.address" type="text" placeholder="Alamat Lengkap" 
                                class="w-full px-6 py-4 rounded-[20px] bg-transparent border border-black/40 placeholder-black/60 focus:bg-white transition text-lg" />
                            <div v-if="form.errors.address" class="text-red-600 text-sm mt-1 ml-2">{{ form.errors.address }}</div>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-2xl font-bold text-black mb-6">Peran & Keahlian</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <input v-model="form.role" type="text" placeholder="Peran Relawan" 
                                class="w-full px-6 py-4 rounded-[20px] bg-transparent border border-black/40 placeholder-black/60 focus:bg-white transition text-lg" />
                            <div v-if="form.errors.role" class="text-red-600 text-sm mt-1 ml-2">{{ form.errors.role }}</div>
                        </div>

                        <div>
                            <input v-model="form.experience" type="text" placeholder="Pengalaman" 
                                class="w-full px-6 py-4 rounded-[20px] bg-transparent border border-black/40 placeholder-black/60 focus:bg-white transition text-lg" />
                            <div v-if="form.errors.experience" class="text-red-600 text-sm mt-1 ml-2">{{ form.errors.experience }}</div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center gap-4">
                        <input v-model="form.agree" type="checkbox" id="agree" 
                            class="w-6 h-6 text-[#4F3726] border-2 border-black rounded focus:ring-[#4F3726] bg-transparent cursor-pointer" />
                        <label for="agree" class="text-lg text-black cursor-pointer">Saya bersedia mengikuti aturan dan arahan tim Pulih.Kan</label>
                    </div>
                    <div v-if="form.errors.agree" class="text-red-600 text-sm mt-1 ml-2">Anda wajib mencentang persetujuan ini.</div>
                </div>

                <button type="submit" :disabled="form.processing"
                    class="w-full py-5 bg-[#4F3726] hover:bg-[#3d2b1d] text-white font-bold text-xl rounded-[30px] shadow-lg transition transform hover:scale-[1.01] disabled:opacity-50">
                    <span v-if="form.processing">Mengirim...</span>
                    <span v-else>Daftar Jadi Relawan</span>
                </button>

            </form>
        </div>
    </div>
</template>