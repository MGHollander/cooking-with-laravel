<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";
import Checkbox from "@/Components/Checkbox.vue";
import Input from "@/Components/Input.vue";
import Label from "@/Components/Label.vue";
import ValidationErrors from "@/Components/ValidationErrors.vue";
import GuestLayout from "@/Layouts/Guest.vue";

defineProps({
  canResetPassword: Boolean,
  status: String,
});

const form = useForm({
  email: "",
  password: "",
  remember: false,
});

const submit = () => {
  form.post(route("login"), {
    onFinish: () => form.reset("password"),
  });
};
</script>

<template>
  <GuestLayout>
    <Head :title="$t('auth.login.title')" />

    <ValidationErrors class="-mx-6 -mt-4 mb-4 px-6 py-4" />

    <div v-if="status" class="-mx-6 -mt-4 mb-4 bg-emerald-100 px-6 py-4 text-sm font-medium text-emerald-800">
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

      <div class="mt-4">
        <Label for="password" :value="$t('auth.password')" />
        <Input
          id="password"
          v-model="form.password"
          type="password"
          class="mt-1 block w-full"
          required
          autocomplete="current-password"
        />
      </div>

      <div class="mt-4 block">
        <label class="flex items-center">
          <Checkbox v-model:checked="form.remember" name="remember" />
          <span class="ml-2 text-sm text-gray-600">{{ $t('auth.remember_me') }}</span>
        </label>
      </div>

      <div class="mt-4 flex items-center justify-end">
        <Link
          v-if="canResetPassword"
          :href="route('password.request')"
          class="text-sm text-gray-600 underline hover:text-gray-900"
        >
          {{ $t('auth.login.forgot_password') }}
        </Link>

        <Button type="submit" class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
          {{ $t('auth.login.title') }}
        </Button>
      </div>
    </form>
  </GuestLayout>
</template>
