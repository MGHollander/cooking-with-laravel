<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import { computed, useAttrs } from "vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import DefaultLayout from "@/Layouts/Default.vue";
import { trans } from "laravel-vue-i18n";

const props = defineProps({
  public_url: String,
  default_language: String,
});

const attrs = useAttrs();

const form = useForm({
  public_url: props.public_url || "",
  default_language: props.default_language || "nl",
});

const submit = () => {
  form.patch(route(`users.settings.update.${attrs.locale}`));
};

const title = computed(() => trans("users.settings.title"));
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
            <Label for="public_url" :value="$t('users.settings.public_url')" />
            <Input v-model="form.public_url" type="text" class="block w-full" maxlength="50" />
            <p class="text-xs text-gray-500">
              {{ $t("users.settings.public_url_help") }}
            </p>
            <InputError :message="form.errors.public_url" />
          </div>

          <div class="col-span-6 space-y-1 sm:col-span-4">
            <Label for="default_language" :value="$t('users.settings.default_language')" />
            <select
              v-model="form.default_language"
              class="block w-full rounded-md border-gray-300 shadow-sm transition duration-150 ease-in-out focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            >
              <option value="nl">{{ $t("app.languages.nl") }}</option>
              <option value="en">{{ $t("app.languages.en") }}</option>
            </select>
            <InputError :message="form.errors.default_language" />
          </div>
        </div>
      </div>

      <div
        class="flex items-center justify-end border-b border-gray-200 bg-gray-50 px-4 py-3 text-right shadow sm:rounded-bl-md sm:rounded-br-md sm:px-6"
      >
        <Button class="text-xs" type="submit" :disabled="form.processing">{{ $t("users.settings.save") }}</Button>
      </div>
    </form>
  </DefaultLayout>
</template>
