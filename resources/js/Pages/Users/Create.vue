<script setup>
import { reactive } from 'vue';
import { Head, useForm } from '@inertiajs/inertia-vue3';

import DefaultLayout from '@/Layouts/Default.vue';
import Button from '@/Components/Button.vue';
import Input from '@/Components/Input.vue';
import InputError from '@/Components/InputError.vue';
import Label from '@/Components/Label.vue';

let form = useForm({
    name: '',
    email: '',
    password: '',
})

let submit = () => {
  form.post(route('users.store'));
};
</script>

<template>
    <Head title="Create a user" />

    <DefaultLayout>
        <template #header>
            Create a user
        </template>

        <form @submit.prevent="submit" class="max-w-2xl mx-auto">
            <div class="px-4 py-5 sm:p-6 bg-white shadow sm:rounded-tl-md sm:rounded-tr-md space-y-2">
                <div class="grid grid-cols-6 gap-6">
                    <div class="col-span-6 sm:col-span-4 space-y-1">
                        <Label for="name" value="Name" />
                        <Input v-model="form.name" type="text" required class="block w-full" autocomplete="name" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="col-span-6 sm:col-span-4 space-y-1">
                        <Label for="email" value="Email" />
                        <Input v-model="form.email" type="email" required class="block w-full" autocomplete="username" />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="col-span-6 sm:col-span-4 space-y-1">
                        <Label for="password" value="Password" />
                        <Input v-model="form.password" type="password" required class="block w-full" />
                        <InputError :message="form.errors.password" />
                    </div>
                </div>
            </div>

            <div class="flex items-center px-4 py-3 bg-gray-50 border-b border-gray-200 sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                <Button class="text-xs" type="submit" :disabled="form.processing">
                    Save
                </Button>
            </div>
        </form>
    </DefaultLayout
>
</template>
