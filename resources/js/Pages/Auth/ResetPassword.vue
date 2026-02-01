<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import Label from "@/Components/Label.vue";
import ValidationErrors from "@/Components/ValidationErrors.vue";
import GuestLayout from "@/Layouts/Guest.vue";
import { useAttrs } from "vue";

const attrs = useAttrs();

const props = defineProps({
  email: String,
  token: String,
});

const form = useForm({
  token: props.token,
  email: props.email,
  password: "",
  password_confirmation: "",
});

const submit = () => {
  form.post(route(`password.update.${attrs.locale}`), {
    onFinish: () => form.reset("password", "password_confirmation"),
  });
};
</script>

<template>
  <GuestLayout>
    <Head title="Reset je wachtwoord" />

    <ValidationErrors class="-mx-6 -mt-4 mb-4 px-6 py-4" />

    <form @submit.prevent="submit">
      <div>
        <Label for="email" value="E-mailadres" />
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
      </div>

      <div class="mt-4 flex items-center justify-end">
        <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
          Reset je wachtwoord
        </Button>
      </div>
    </form>
  </GuestLayout>
</template>
