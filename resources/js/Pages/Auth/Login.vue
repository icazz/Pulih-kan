<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <Head title="Log in" />

    <div
        class="min-h-screen w-full flex bg-[#FFF5E5] font-['Montserrat'] overflow-hidden"
    >
        <div class="hidden lg:block w-1/2 relative h-screen">
            <div
                class="relative w-full h-full overflow-hidden rounded-r-[4rem] shadow-2xl z-10"
            >
                <img
                    src="/images/login-bg.jpg"
                    alt="Background Lumpur"
                    class="object-cover w-full h-full"
                />

                <div class="absolute inset-0 bg-[#A69385]/40"></div>

                <div class="absolute inset-0 flex flex-col justify-center px-16 z-20">
                    <h1
                        class="text-6xl font-extrabold leading-tight mb-6 text-[#FFE0CC] drop-shadow-lg"
                    >
                        Selamat<br />Datang!
                    </h1>
                    <p class="text-2xl font-medium drop-shadow-md pr-10 text-[#FFFFFF] leading-relaxed opacity-90">
                        Mau perbaiki apa hari ini?
                    </p>
                </div>
            </div>
        </div>

        <div
            class="w-full lg:w-1/2 h-screen flex flex-col justify-center items-center p-8 lg:p-20 bg-[#FFF5E5]"
        >
            <div class="w-full max-w-md z-0">
                <h2 class="text-5xl font-bold text-[#5A3217] mb-12 text-center">
                    Login
                </h2>

                <div
                    v-if="status"
                    class="mb-4 font-medium text-sm text-green-600"
                >
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-7">
                    <div>
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            placeholder="Email"
                            class="w-full px-6 py-4 rounded-xl border border-[#5A3217]/30 bg-transparent placeholder-[#5A3217]/50 text-[#5A3217] focus:border-[#973C00] focus:ring-1 focus:ring-[#973C00] transition-all outline-none text-lg"
                            required
                            autofocus
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

                    <button
                        type="submit"
                        class="w-full py-4 mt-8 rounded-xl bg-[#973C00] text-white font-bold text-xl hover:bg-[#5A3217] transition-colors duration-300 shadow-lg transform hover:scale-[1.01] active:scale-[0.99]"
                        :disabled="form.processing"
                    >
                        Masuk Sekarang
                    </button>

                    <div class="text-center mt-8 text-[#5A3217] text-lg">
                        Belum punya akun?
                        <Link
                            :href="route('register')"
                            class="font-bold text-[#973C00] hover:underline"
                        >
                            Daftar di sini
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
