<script setup>
import Button from '@/Components/Button.vue';
import GuestLayout from '@/Layouts/Guest.vue';
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue';
import ValidationErrors from '@/Components/ValidationErrors.vue';
import { Head, useForm } from '@inertiajs/inertia-vue3';

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

        <ValidationErrors class="mb-4" />

        <form @submit.prevent="submit">
            <div>
                <Label for="email" value="Email" />
                <Input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <Label for="password" value="Password" />
                <Input id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <Label for="password_confirmation" value="Confirm Password" />
                <Input id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Reset Password
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
