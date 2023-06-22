<script setup>
import Button from '@/Components/Button.vue';
import DefaultLayout from '@/Layouts/Default.vue';
import Label from '@/Components/Label.vue';
import Input from '@/Components/Input.vue';
import InputError from '@/Components/InputError.vue';
import {Head, useForm} from '@inertiajs/vue3';

const form = useForm({
    current_password: '',
    new_password: '',
    new_password_confirmation: '',
});

const submit = () => {
    form.post(route('users.password.update'), {
        onFinish: () => form.reset(),
    });
};

const title = 'Wijzig je wachtwoord';
</script>

<template>
    <Head :title="title"/>

    <DefaultLayout>
        <template #header>
            {{ title }}
        </template>

        <form @submit.prevent="submit" class="max-w-2xl mx-auto">
            <div class="px-4 py-5 sm:p-6 bg-white shadow sm:rounded-tl-md sm:rounded-tr-md space-y-2">
                <div class="grid grid-cols-6 gap-6">
                    <div class="col-span-6 sm:col-span-4 space-y-1">
                        <Label for="current_password" value="Huidige wachtwoord"/>
                        <Input v-model="form.current_password" type="password" required class="block w-full"/>
                        <InputError :message="form.errors.current_password"/>
                    </div>

                    <div class="col-span-6 sm:col-span-4 space-y-1">
                        <Label for="email" value="Nieuwe wachtwoord"/>
                        <Input v-model="form.new_password" type="password" required class="block w-full"/>
                        <InputError :message="form.errors.new_password"/>
                    </div>

                    <div class="col-span-6 sm:col-span-4 space-y-1">
                        <Label for="email" value="Bevestig je nieuwe wachtwoord"/>
                        <Input v-model="form.new_password_confirmation" type="password" required class="block w-full"/>
                        <InputError :message="form.errors.new_password_confirmation"/>
                    </div>
                </div>
            </div>

            <div
                class="flex items-center px-4 py-3 bg-gray-50 border-b border-gray-200 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                <Button class="text-xs" type="submit" :disabled="form.processing">
                    Opslaan
                </Button>
            </div>
        </form>
    </DefaultLayout>
</template>
