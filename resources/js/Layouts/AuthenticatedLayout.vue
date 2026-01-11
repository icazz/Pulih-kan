<script setup>
import Navbar from '@/Components/Navbar.vue';
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <Navbar />

        <main class="pt-24 pb-12 font-['Montserrat']">
            <slot />
        </main>
    </div>
</template>