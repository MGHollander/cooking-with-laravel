<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import DefaultLayout from "@/Layouts/Default.vue";

let form = useForm({
  name: "",
  email: "",
  password: "",
});

let submit = () => {
  form.post(route("users.store"));
};
</script>

<template>
  <Head title="Create a user" />

  <DefaultLayout>
    <template #header> Gebruiker toevoegen </template>

    <form class="mx-auto max-w-2xl" @submit.prevent="submit">
      <div class="space-y-2 bg-white px-4 py-5 shadow sm:rounded-tl-md sm:rounded-tr-md sm:p-6">
        <div class="grid grid-cols-6 gap-6">
          <div class="col-span-6 space-y-1 sm:col-span-4">
            <Label for="name" value="Naam" />
            <Input v-model="form.name" type="text" required class="block w-full" autocomplete="name" />
            <InputError :message="form.errors.name" />
          </div>

          <div class="col-span-6 space-y-1 sm:col-span-4">
            <Label for="email" value="E-mailadres" />
            <Input v-model="form.email" type="email" required class="block w-full" autocomplete="email" />
            <InputError :message="form.errors.email" />
          </div>

          <div class="col-span-6 space-y-1 sm:col-span-4">
            <Label for="password" value="Wachtwoord" />
            <Input v-model="form.password" type="password" required class="block w-full" />
            <InputError :message="form.errors.password" />
          </div>
        </div>
      </div>

      <div
        class="flex items-center border-b border-gray-200 bg-gray-50 px-4 py-3 shadow sm:rounded-bl-md sm:rounded-br-md sm:px-6"
      >
        <Button class="text-xs" type="submit" :disabled="form.processing"> Opslaan </Button>
      </div>
    </form>
  </DefaultLayout>
</template>
