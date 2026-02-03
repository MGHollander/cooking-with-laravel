<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed } from "vue";
import Button from "@/Components/Button.vue";
import GuestLayout from "@/Layouts/Guest.vue";
import { useAttrs } from "vue";

const attrs = useAttrs();

const props = defineProps({
  status: String,
});

const form = useForm();

const submit = () => {
  form.post(route(`verification.send.${attrs.locale}`));
};

const verificationLinkSent = computed(() => props.status === "verification-link-sent");
</script>

<template>
  <GuestLayout>
    <Head :title="$t('auth.verify_email_title')" />

    <div class="mb-4 text-sm text-gray-600">
      {{ $t('auth.verify_email_description') }}
    </div>

    <div v-if="verificationLinkSent" class="-mx-6 mb-4 bg-emerald-100 px-6 py-4 text-sm font-medium text-emerald-800">
      {{ $t('auth.verification_link_sent') }}
    </div>

    <form @submit.prevent="submit">
      <div class="mt-4 flex items-center justify-between">
        <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
          {{ $t('auth.resend_verification_email') }}
        </Button>

        <Link
          :href="route(`logout.${attrs.locale}`)"
          method="post"
          as="button"
          class="text-sm text-gray-600 underline hover:text-gray-900"
        >
          {{ $t('auth.logout') }}
        </Link>
      </div>
    </form>
  </GuestLayout>
</template>
