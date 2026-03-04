<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import { computed, ref, useAttrs } from "vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import LocaleSelect from "@/Components/LocaleSelect.vue";
import DefaultLayout from "@/Layouts/Default.vue";
import { trans } from "laravel-vue-i18n";

const props = defineProps({
  public_url: String,
  default_language: String,
  default_visibility: String,
  languages: Object,
});

const attrs = useAttrs();

const form = useForm({
  public_url: props.public_url || "",
  default_language: props.default_language || "nl",
  default_visibility: props.default_visibility || "private",
});

const submit = () => {
  form.patch(route(`users.settings.update.${attrs.locale}`));
};

const title = computed(() => trans("users.settings.title"));

const fullPublicUrl = computed(() => {
  return `${window.location.origin}/${trans("users.settings.public_url_suffix")}/${form.public_url}`;
});

const copied = ref(false);

const copyToClipboard = async () => {
  try {
    await navigator.clipboard.writeText(fullPublicUrl.value);
    copied.value = true;
    setTimeout(() => {
      copied.value = false;
    }, 2000);
  } catch (err) {
    console.error("Failed to copy:", err);
  }
};
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
            <Input v-model="form.public_url" type="text" required class="block w-full" maxlength="50" />
            <p class="text-xs text-gray-500">
              {{ $t("users.settings.public_url_help") }}
            </p>
            <div class="group inline-flex cursor-pointer items-center gap-1 " @click="copyToClipboard">
              <p class="text-xs text-gray-800 bg-gray-100 py-1 px-2 rounded">
                {{ fullPublicUrl }}
              </p>
              <svg
                class="h-4 w-4 flex-shrink-0 text-gray-400 opacity-0 transition-opacity group-hover:opacity-100"
                :class="{ 'text-emerald-500 opacity-100': copied }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  v-if="!copied"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
                />
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <InputError :message="form.errors.public_url" />
          </div>

          <div class="col-span-6 space-y-1 sm:col-span-4">
            <Label for="default_language" :value="$t('users.settings.default_language')" />
            <LocaleSelect v-model="form.default_language" :languages="props.languages" />
            <p class="text-xs text-gray-500">
              {{ $t("users.settings.default_language_help") }}
            </p>
            <InputError :message="form.errors.default_language" />
          </div>

          <div class="col-span-6 space-y-1 sm:col-span-4">
            <Label for="default_visibility" :value="$t('users.settings.default_visibility')" />
            <select
              v-model="form.default_visibility"
              class="block w-full rounded-md border-gray-300 shadow-sm transition duration-150 ease-in-out focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            >
              <option value="private">{{ $t("recipes.visibility.private") }}</option>
              <option value="direct_link">{{ $t("recipes.visibility.direct_link") }}</option>
              <option value="public">{{ $t("recipes.visibility.public") }}</option>
            </select>
            <p class="text-xs text-gray-500">
              {{ $t("users.settings.default_visibility_help") }}
            </p>
            <InputError :message="form.errors.default_visibility" />
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
