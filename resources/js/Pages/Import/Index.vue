<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import DefaultLayout from "@/Layouts/Default.vue";

const props = defineProps({
  url: String,
  parser: String,
  forceImport: Boolean,
  firecrawl: Boolean,
  openAI: Boolean,
});

const form = useForm({
  url: props.url ?? "",
  parser: props.parser ?? "auto",
  force_import: props.forceImport ?? false,
});

let showHelp = ref(false);
</script>

<template>
  <Head :title="title" />

  <DefaultLayout>
    <template #header>
      {{  $t('import.title') }}
    </template>

    <div class="mx-auto max-w-3xl space-y-8">
      <form class="mb-12 space-y-8" @submit.prevent="form.get(route('import.create'))">
        <div class="bg-white shadow sm:rounded-md">
          <div class="grid grid-cols-12 gap-6 px-4 py-5 sm:p-6">
            <div class="col-span-12 space-y-1">
              <Label for="url" :value="$t('import.url')" />
              <Input v-model="form.url" autocomplete="url" class="block w-full" required type="url" />
              <InputError :message="form.errors.url" />
            </div>

            <div class="col-span-12 space-y-1">
              <Label for="parser" :value="$t('import.method')" />
              <div>
                <label class="flex items-center space-x-2">
                  <input v-model="form.parser" value="auto" required type="radio" />
                  <span>{{ $t('import.auto') }}</span>
                </label>
              </div>
              <div>
                <label class="flex items-center space-x-2">
                  <input v-model="form.parser" value="structured-data" required type="radio" />
                  <span>{{ $t('import.structured_data') }}</span>
                </label>
              </div>
              <div v-if="props.firecrawl">
                <label class="flex items-center space-x-2">
                  <input v-model="form.parser" value="firecrawl" required type="radio" />
                  <span>{{ $t('import.firecrawl') }}</span>
                </label>
              </div>
              <div v-if="props.openAI">
                <label class="flex items-center space-x-2">
                  <input v-model="form.parser" value="open-ai" required type="radio" />
                  <span>{{ $t('import.ai_experimental') }}</span>
                </label>
              </div>
              <InputError :message="form.errors.parser" />
            </div>

            <div class="col-span-12 space-y-1">
              <label class="flex items-center space-x-2">
                <input v-model="form.force_import" value="true" type="checkbox" />
                <span>{{ $t('import.force_import') }}</span>
              </label>
            </div>
          </div>

          <div class="border-t border-gray-200 bg-gray-50 px-4 py-3 sm:px-6" :class="{ 'sm:rounded-b': !showHelp }">
            <div class="mx-auto max-w-3xl space-x-2 md:space-x-4">
              <Button :disabled="form.processing" class="text-xs" type="submit">{{ $t('import.import_button') }}</Button>

              <a class="cursor-pointer text-sm text-gray-600 hover:text-gray-900" @click="showHelp = !showHelp">
                {{ $t('import.how_does_it_work') }}
              </a>
            </div>
          </div>

          <div v-if="showHelp" class="border-t border-gray-200 bg-sky-100 p-4 text-sky-700 md:rounded-b">
            <p>
              <strong>{{ $t('import.auto') }}</strong><br />
              {{ $t('import.auto_description') }}
            </p>
            <p>
              <strong>{{ $t('import.structured_data') }}</strong><br />
              <span v-html="$t('import.structured_data_description', { schema: '<a href=\'https://schema.org/Recipe\' class=\'text-sky-900\'>schema.org/Recipe</a>' })"></span>
            </p>
            <p v-if="props.firecrawl">
              <strong>{{ $t('import.firecrawl') }}</strong><br />
              {{ $t('import.firecrawl_description') }}
            </p>
            <p v-if="props.openAI">
              <strong>{{ $t('import.ai_experimental') }}</strong><br />
              {{ $t('import.ai_description') }}
            </p>
          </div>
        </div>
      </form>
    </div>
  </DefaultLayout>
</template>
