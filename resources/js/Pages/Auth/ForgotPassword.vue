<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import Label from "@/Components/Label.vue";
import ValidationErrors from "@/Components/ValidationErrors.vue";
import GuestLayout from "@/Layouts/Guest.vue";

defineProps({
  status: String,
});

const form = useForm({
  email: "",
});

const submit = () => {
  form.post(route("password.email"));
};
</script>

<template>
  <GuestLayout>
    <Head title="Forgot Password" />

    <div class="mb-4 text-sm text-gray-600">
      Wachtwoord vergeten? Geen probleem. Laat je e-mailadres achter en we sturen je een wachtwoord herstel link waarmee
      je een nieuw wachtwoord kunt aanmaken.
    </div>

    <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
      {{ status }}
    </div>

    <ValidationErrors class="mb-4" />

    <form @submit.prevent="submit">
      <div>
        <Label for="email" value="Email" />
        <Input
          id="email"
          v-model="form.email"
          type="email"
          class="mt-1 block w-full"
          required
          autofocus
          autocomplete="username"
        />
      </div>

      <div class="mt-4 flex items-center justify-end">
        <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
          Wachtwoord herstel link sturen
        </Button>
      </div>
    </form>
  </GuestLayout>
</template>
