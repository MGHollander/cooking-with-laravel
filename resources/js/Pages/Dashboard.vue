<script setup>
import {Head, Link} from '@inertiajs/vue3';

import DefaultLayout from '@/Layouts/Default.vue';</script>

<template>
    <Head title="Dashboard"/>

    <DefaultLayout>
        <template #header>
            Dashboard
        </template>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <Link :href="route('recipes.create')" class="text-emerald-700 no-underline hover:text-black">
                    Add a recipe
                </Link>
            </div>
        </div>
    </DefaultLayout>
</template>
