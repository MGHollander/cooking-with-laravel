<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import Label from "@/Components/Label.vue";
import ValidationErrors from "@/Components/ValidationErrors.vue";
import GuestLayout from "@/Layouts/Guest.vue";

const form = useForm({
  password: "",
});

const submit = () => {
  form.post(route("password.confirm"), {
    onFinish: () => form.reset(),
  });
};
</script>

<template>
  <GuestLayout>
    <Head title="Bevestig je wachtwoord" />

    <div class="mb-4 text-sm text-gray-600">
      Dit is een beveiligd gedeelte van de website. Bevestig je wachtwoord voordat je doorgaat.
    </div>

    <ValidationErrors class="-mx-6 mb-4 px-6 py-4" />

    <form @submit.prevent="submit">
      <div>
        <Label for="password" value="Password" />
        <Input
          id="password"
          v-model="form.password"
          type="password"
          class="mt-1 block w-full"
          required
          autocomplete="current-password"
          autofocus
        />
      </div>

      <div class="mt-4 flex justify-end">
        <Button type="submit" class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
          Bevestig
        </Button>
      </div>
    </form>
  </GuestLayout>
</template>
