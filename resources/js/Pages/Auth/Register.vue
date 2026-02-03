<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import Label from "@/Components/Label.vue";
import ValidationErrors from "@/Components/ValidationErrors.vue";
import GuestLayout from "@/Layouts/Guest.vue";
import { useAttrs } from "vue";

const attrs = useAttrs();

const form = useForm({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  terms: false,
});

const submit = () => {
  form.post(route(`register.${attrs.locale}`), {
    onFinish: () => form.reset("password", "password_confirmation"),
  });
};
</script>

<template>
  <GuestLayout>
    <Head :title="$t('auth.register')" />

    <ValidationErrors class="-mx-6 -mt-4 mb-4 px-6 py-4" />

    <form @submit.prevent="submit">
      <div>
        <Label for="name" :value="$t('auth.name')" />
        <Input
          id="name"
          v-model="form.name"
          type="text"
          class="mt-1 block w-full"
          required
          autofocus
          autocomplete="name"
        />
      </div>

      <div class="mt-4">
        <Label for="email" :value="$t('auth.email')" />
        <Input
          id="email"
          v-model="form.email"
          type="email"
          class="mt-1 block w-full"
          required
          autocomplete="username"
        />
      </div>

      <div class="mt-4">
        <Label for="password" :value="$t('auth.password')" />
        <Input
          id="password"
          v-model="form.password"
          type="password"
          class="mt-1 block w-full"
          required
          autocomplete="new-password"
        />
      </div>

      <div class="mt-4">
        <Label for="password_confirmation" :value="$t('auth.confirm_password')" />
        <Input
          id="password_confirmation"
          v-model="form.password_confirmation"
          type="password"
          class="mt-1 block w-full"
          required
          autocomplete="new-password"
        />
      </div>

      <div class="mt-4 flex items-center justify-end">
        <Link :href="route(`login.${attrs.locale}`)" class="text-sm text-gray-600 underline hover:text-gray-900">
          {{ $t('auth.already_registered') }}
        </Link>

        <Button type="submit" class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
          {{ $t('auth.register') }}
        </Button>
      </div>
    </form>
  </GuestLayout>
</template>
