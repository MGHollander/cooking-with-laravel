<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import Label from "@/Components/Label.vue";
import ValidationErrors from "@/Components/ValidationErrors.vue";
import GuestLayout from "@/Layouts/Guest.vue";

const form = useForm({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  terms: false,
});

const submit = () => {
  form.post(route("register"), {
    onFinish: () => form.reset("password", "password_confirmation"),
  });
};
</script>

<template>
  <GuestLayout>
    <Head title="Registreren" />

    <ValidationErrors class="mb-4" />

    <form @submit.prevent="submit">
      <div>
        <Label for="name" value="Naam" />
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
        <Label for="email" value="E-mailadres" />
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
        <Label for="password" value="Wachtwoord" />
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
        <Label for="password_confirmation" value="Bevestig je wachtwoord" />
        <Input
          id="password_confirmation"
          v-model="form.password_confirmation"
          type="password"
          class="mt-1 block w-full"
          required
          autocomplete="new-password"
        />
        />
      </div>

      <div class="mt-4 flex items-center justify-end">
        <Link :href="route('login')" class="text-sm text-gray-600 underline hover:text-gray-900">
          Ben je al geregistreerd? Log dan in.
        </Link>

        <Button type="submit" class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
          Registreren
        </Button>
      </div>
    </form>
  </GuestLayout>
</template>
