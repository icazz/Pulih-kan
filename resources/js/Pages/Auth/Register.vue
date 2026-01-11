<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const form = useForm({
    name: "",
    email: "",
    phone: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <Head title="Register" />

    <div
        class="min-h-screen w-full flex bg-[#FFF5E5] font-['Montserrat'] overflow-hidden"
    >
        <div class="hidden lg:block w-1/2 relative h-screen">
            <div
                class="relative w-full h-full overflow-hidden rounded-r-[4rem] shadow-2xl z-10"
            >
                <img
                    src="/images/register-bg.jpg"
                    alt="Background Konstruksi"
                    class="object-cover w-full h-full"
                />

                <div class="absolute inset-0 bg-[#A69385]/30"></div>

                <div
                    class="absolute inset-0 flex flex-col justify-center px-16 z-20"
                >
                    <h1
                        class="text-6xl font-extrabold leading-tight mb-6 text-[#FFFAE7] drop-shadow-lg"
                    >
                        Buat<br />Akunmu!
                    </h1>
                    <p
                        class="text-2xl font-medium drop-shadow-md pr-10 text-[#FFFFFF] leading-relaxed opacity-90"
                    >
                        Beri tahu masalahmu dan kami siap membantu
                        memulihkannya.
                    </p>
                </div>
            </div>
        </div>

        <div
            class="w-full lg:w-1/2 h-screen flex flex-col justify-center items-center p-8 lg:p-20 bg-[#FFF5E5] overflow-y-auto"
        >
            <div class="w-full max-w-md my-auto">
                <h2 class="text-5xl font-bold text-[#5A3217] mb-10 text-center">
                    Register
                </h2>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <input
                            id="name"
                            type="text"
                            v-model="form.name"
                            placeholder="Nama Lengkap"
                            class="w-full px-6 py-4 rounded-xl border border-[#5A3217]/30 bg-transparent placeholder-[#5A3217]/50 text-[#5A3217] focus:border-[#973C00] focus:ring-1 focus:ring-[#973C00] transition-all outline-none text-lg"
                            required
                            autofocus
                        />
                        <div
                            v-if="form.errors.name"
                            class="text-red-600 text-sm mt-1 ml-2"
                        >
                            {{ form.errors.name }}
                        </div>
                    </div>

                    <div>
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            placeholder="Email"
                            class="w-full px-6 py-4 rounded-xl border border-[#5A3217]/30 bg-transparent placeholder-[#5A3217]/50 text-[#5A3217] focus:border-[#973C00] focus:ring-1 focus:ring-[#973C00] transition-all outline-none text-lg"
                            required
                        />
                        <div
                            v-if="form.errors.email"
                            class="text-red-600 text-sm mt-1 ml-2"
                        >
                            {{ form.errors.email }}
                        </div>
                    </div>

                    <div>
                        <input
                            id="phone"
                            type="text"
                            v-model="form.phone"
                            placeholder="Nomor HP"
                            class="w-full px-6 py-4 rounded-xl border border-[#5A3217]/30 bg-transparent placeholder-[#5A3217]/50 text-[#5A3217] focus:border-[#973C00] focus:ring-1 focus:ring-[#973C00] transition-all outline-none text-lg"
                            required
                        />
                        <div
                            v-if="form.errors.phone"
                            class="text-red-600 text-sm mt-1 ml-2"
                        >
                            {{ form.errors.phone }}
                        </div>
                    </div>

                    <div>
                        <input
                            id="password"
                            type="password"
                            v-model="form.password"
                            placeholder="Password"
                            class="w-full px-6 py-4 rounded-xl border border-[#5A3217]/30 bg-transparent placeholder-[#5A3217]/50 text-[#5A3217] focus:border-[#973C00] focus:ring-1 focus:ring-[#973C00] transition-all outline-none text-lg"
                            required
                        />
                        <div
                            v-if="form.errors.password"
                            class="text-red-600 text-sm mt-1 ml-2"
                        >
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <div>
                        <input
                            id="password_confirmation"
                            type="password"
                            v-model="form.password_confirmation"
                            placeholder="Konfirmasi Password"
                            class="w-full px-6 py-4 rounded-xl border border-[#5A3217]/30 bg-transparent placeholder-[#5A3217]/50 text-[#5A3217] focus:border-[#973C00] focus:ring-1 focus:ring-[#973C00] transition-all outline-none text-lg"
                            required
                        />
                        <div
                            v-if="form.errors.password_confirmation"
                            class="text-red-600 text-sm mt-1 ml-2"
                        >
                            {{ form.errors.password_confirmation }}
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full py-4 mt-6 rounded-xl bg-[#973C00] text-white font-bold text-xl hover:bg-[#5A3217] transition-colors duration-300 shadow-lg transform hover:scale-[1.01] active:scale-[0.99]"
                        :disabled="form.processing"
                    >
                        Daftar Sekarang
                    </button>

                    <div class="text-center mt-8 text-[#5A3217] text-lg pb-10">
                        Sudah punya akun?
                        <Link
                            :href="route('login')"
                            class="font-bold text-[#973C00] hover:underline"
                        >
                            Login di sini
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
