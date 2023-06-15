<script setup>
import {Head, Link} from '@inertiajs/vue3';

import DefaultLayout from '@/Layouts/Default.vue';
import Button from '@/Components/Button.vue';
</script>

<template>
    <Head title="Dashboard"/>

    <DefaultLayout>
        <template #header>
            Dashboard
        </template>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <Button :href="route('recipes.create')" class="text-sm text-emerald-700 no-underline hover:text-black">
                    Add a recipe
                </Button>
            </div>
        </div>
    </DefaultLayout>
</template>
