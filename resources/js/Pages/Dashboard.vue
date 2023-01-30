<script setup>
import DefaultLayout from '@/Layouts/Default.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <DefaultLayout>
        <template #header>
            Dashboard
        </template>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                You're logged in!
            </div>
        </div>
    </DefaultLayout
>
</template>
