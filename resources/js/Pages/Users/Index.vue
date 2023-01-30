<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { Head, Link } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';

import DefaultLayout from '@/Layouts/Default.vue';
import Button from '@/Components/Button.vue';
import Input from '@/Components/Input.vue';
import Pagination from '@/Components/Pagination.vue';

let props = defineProps({
    users: Object,
    filters: Object,
});

let search = ref(props.filters.search);

watch(search, debounce(value => {
    Inertia.get(route('users'), { search: value }, {
        preserveState: true,
        replace: true
    });
}, 300));
</script>

<template>
    <Head title="Users" />

    <DefaultLayout>
        <template #header>
            Users
        </template>

        <div class="mb-4 px-2 sm:px-0 flex justify-between items-center">
            <Button :href="route('users.create')" class="px-2 text-sm bg-indigo-600 text-sm no-underline hover:bg-indigo-800 hover:text-white">
                Create a user
            </Button>

            <Input v-model="search" placeholder="Search..." class="p-2 w-full sm:w-60" />
        </div>


        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow border-b border-gray-200 bg-white sm:rounded-md">
                    <table v-if="users.data.length > 0" class="min-w-full divide-y divide-gray-200">
                        <tr v-for="user in users.data" :key="user.id">
                            <td class="px-4 py-5 sm:px-6 whitespace-nowrap leading-tight">
                                {{ user.name }}<br/>
                                <span class="text-sm text-gray-500">{{ user.email }}</span>
                            </td>

                            <td class="px-4 py-5 sm:px-6 whitespace-nowrap text-right text-sm font-medium">
                                <Link :href="route('users.edit', user.id)" class="text-indigo-600 hover:text-indigo-900 no-underline hover:underline">
                                    Edit
                                </Link>
                            </td>
                        </tr>
                    </table>

                    <p v-else class="px-4 py-5 sm:px-6">No user found with <strong>{{ filters.search }}</strong> in the name.</p>
                </div>
            </div>
        </div>

        <div class="mt-6 px-4 py-5 sm:px-6">
            <Pagination :links="users.links" class="flex flex-auto flex-wrap justify-center" />
        </div>
    </DefaultLayout
>
</template>
