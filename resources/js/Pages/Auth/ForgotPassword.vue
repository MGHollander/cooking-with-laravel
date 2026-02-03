<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import Label from "@/Components/Label.vue";
import ValidationErrors from "@/Components/ValidationErrors.vue";
import GuestLayout from "@/Layouts/Guest.vue";
import { useAttrs } from "vue";

const attrs = useAttrs();

defineProps({
  status: String,
});

const form = useForm({
  email: "",
});

const submit = () => {
  form.post(route(`password.email.${attrs.locale}`));
};
</script>

<template>
  <GuestLayout>
    <Head :title="$t('auth.forgot_password.title')" />

    <div class="mb-4 text-sm text-gray-600">
      {{ $t('auth.forgot_password.description') }}
    </div>

    <ValidationErrors class="-mx-6 mb-4 px-6 py-4" />

    <div v-if="status" class="-mx-6 mb-4 bg-emerald-100 px-6 py-4 text-sm font-medium text-emerald-800">
      {{ status }}
    </div>

    <form @submit.prevent="submit">
      <div>
        <Label for="email" :value="$t('auth.email')" />
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
          {{ $t('auth.forgot_password.send_password_reset_link') }}
        </Button>
      </div>
    </form>
  </GuestLayout>
</template>
