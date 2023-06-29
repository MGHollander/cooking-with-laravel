<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed } from "vue";
import Button from "@/Components/Button.vue";
import GuestLayout from "@/Layouts/Guest.vue";

const props = defineProps({
  status: String,
});

const form = useForm();

const submit = () => {
  form.post(route("verification.send"));
};

const verificationLinkSent = computed(() => props.status === "verification-link-sent");
</script>

<template>
  <GuestLayout>
    <Head title="E-mail verificatie" />

    <div class="mb-4 text-sm text-gray-600">
      Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just
      emailed to you? If you didn't receive the email, we will gladly send you another. Bedankt voor het registreren!
      Voordat je begint, kun je je e-mailadres verifiëren door op de link te klikken die we je zojuist hebben gemaild.
      Als je de e-mail niet hebt ontvangen, sturen we je graag een andere.
    </div>

    <div v-if="verificationLinkSent" class="mb-4 text-sm font-medium text-green-600">
      Er is een nieuwe verificatielink naar het e-mailadres gestuurd dat je hebt opgegeven tijdens het registreren.
    </div>

    <form @submit.prevent="submit">
      <div class="mt-4 flex items-center justify-between">
        <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
          Verificatie e-mail opnieuw versturen
        </Button>

        <Link
          :href="route('logout')"
          method="post"
          as="button"
          class="text-sm text-gray-600 underline hover:text-gray-900"
          >Uitloggen
        </Link>
      </div>
    </form>
  </GuestLayout>
</template>
