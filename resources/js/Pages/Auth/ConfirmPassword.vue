<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import Label from "@/Components/Label.vue";
import ValidationErrors from "@/Components/ValidationErrors.vue";
import GuestLayout from "@/Layouts/Guest.vue";
import { useAttrs } from "vue";

const attrs = useAttrs();

const form = useForm({
  password: "",
});

const submit = () => {
  form.post(route(`password.confirm.${attrs.locale}`), {
    onFinish: () => form.reset(),
  });
};
</script>

<template>
  <GuestLayout>
    <Head :title="$t('auth.confirm_password_title')" />

    <div class="mb-4 text-sm text-gray-600">
      {{ $t('auth.confirm_password_description') }}
    </div>

    <ValidationErrors class="-mx-6 mb-4 px-6 py-4" />

    <form @submit.prevent="submit">
      <div>
        <Label for="password" :value="$t('auth.password')" />
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
          {{ $t('auth.confirm_password_button') }}
        </Button>
      </div>
    </form>
  </GuestLayout>
</template>
