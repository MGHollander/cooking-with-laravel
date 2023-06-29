<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import DefaultLayout from "@/Layouts/Default.vue";

const form = useForm({
  current_password: "",
  new_password: "",
  new_password_confirmation: "",
});

const submit = () => {
  form.post(route("users.password.update"), {
    onFinish: () => form.reset(),
  });
};

const title = "Wijzig je wachtwoord";
</script>

<template>
  <Head :title="title" />

  <DefaultLayout>
    <template #header>
      {{ title }}
    </template>

    <form class="mx-auto max-w-2xl" @submit.prevent="submit">
      <div class="space-y-2 bg-white px-4 py-5 shadow sm:rounded-tl-md sm:rounded-tr-md sm:p-6">
        <div class="grid grid-cols-6 gap-6">
          <div class="col-span-6 space-y-1 sm:col-span-4">
            <Label for="current_password" value="Huidige wachtwoord" />
            <Input v-model="form.current_password" type="password" required class="block w-full" />
            <InputError :message="form.errors.current_password" />
          </div>

          <div class="col-span-6 space-y-1 sm:col-span-4">
            <Label for="email" value="Nieuwe wachtwoord" />
            <Input v-model="form.new_password" type="password" required class="block w-full" />
            <InputError :message="form.errors.new_password" />
          </div>

          <div class="col-span-6 space-y-1 sm:col-span-4">
            <Label for="email" value="Bevestig je nieuwe wachtwoord" />
            <Input v-model="form.new_password_confirmation" type="password" required class="block w-full" />
            <InputError :message="form.errors.new_password_confirmation" />
          </div>
        </div>
      </div>

      <div
        class="flex items-center border-b border-gray-200 bg-gray-50 px-4 py-3 text-right shadow sm:rounded-bl-md sm:rounded-br-md sm:px-6"
      >
        <Button class="text-xs" type="submit" :disabled="form.processing">Opslaan</Button>
      </div>
    </form>
  </DefaultLayout>
</template>
