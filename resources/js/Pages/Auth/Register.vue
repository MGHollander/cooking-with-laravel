<script setup>
import Button from '@/Components/Button.vue';
import GuestLayout from '@/Layouts/Guest.vue';
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue';
import ValidationErrors from '@/Components/ValidationErrors.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Registreren"/>

        <ValidationErrors class="mb-4"/>

        <form @submit.prevent="submit">
            <div>
                <Label for="name" value="Naam"/>
                <Input id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus
                       autocomplete="name"/>
            </div>

            <div class="mt-4">
                <Label for="email" value="E-mailadres"/>
                <Input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
                       autocomplete="username"/>
            </div>

            <div class="mt-4">
                <Label for="password" value="Wachtwoord"/>
                <Input id="password" type="password" class="mt-1 block w-full" v-model="form.password" required
                       autocomplete="new-password"/>
            </div>

            <div class="mt-4">
                <Label for="password_confirmation" value="Bevestig je wachtwoord"/>
                <Input id="password_confirmation" type="password" class="mt-1 block w-full"
                       v-model="form.password_confirmation" required autocomplete="new-password"/>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link :href="route('login')" class="underline text-sm text-gray-600 hover:text-gray-900">
                    Ben je al geregistreerd? Log dan in.
                </Link>

                <Button type="submit" class="ml-4" :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing">
                    Registreren
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
