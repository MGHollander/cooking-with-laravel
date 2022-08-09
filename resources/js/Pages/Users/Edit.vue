<script setup>
import { reactive } from 'vue';
import { Head, useForm } from '@inertiajs/inertia-vue3';

import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
import Button from '@/Components/Button.vue';
import Input from '@/Components/Input.vue';
import InputError from '@/Components/InputError.vue';
import Label from '@/Components/Label.vue';

let props = defineProps({
    id: Number,
    name: String,
    email: String,
});

console.log(props)

let form = useForm({
    name: props.name,
    email: props.email,
})

let submit = () => {
  form.patch(route('users.update', props.id));
};
</script>

<template>
    <Head :title="`Edit ${form.name}`" />

    <AuthenticatedLayout>
        <template #header>
            Edit {{ form.name }}
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
                </div>
            </div>

            <div class="flex items-center justify-end px-4 py-3 bg-gray-50 border-b border-gray-200 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                <Button type="submit" :disabled="form.processing">
                    Save
                </Button>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
