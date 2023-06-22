<script setup>
import Button from '@/Components/Button.vue';
import GuestLayout from '@/Layouts/Guest.vue';
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue';
import ValidationErrors from '@/Components/ValidationErrors.vue';
import {Head, useForm} from '@inertiajs/vue3';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password"/>

        <div class="mb-4 text-sm text-gray-600">
            Wachtwoord vergeten? Geen probleem. Laat je e-mailadres achter en we sturen je een
            wachtwoord herstel link waarmee je een nieuw wachtwoord kunt aanmaken.
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <ValidationErrors class="mb-4"/>

        <form @submit.prevent="submit">
            <div>
                <Label for="email" value="Email"/>
                <Input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus
                       autocomplete="username"/>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Wachtwoord herstel link sturen
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
