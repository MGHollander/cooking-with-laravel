<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import DefaultLayout from "@/Layouts/Default.vue";

const props = defineProps({
  url: String,
  firecrawl: Boolean,
  openAI: Boolean,
});

const form = useForm({
  url: props.url ?? "",
  parser: "structured-data",
  force_import: false,
});

const title = "Recept importeren";
let showHelp = ref(false);
</script>

<template>
  <Head :title="title" />

  <DefaultLayout>
    <template #header>
      {{ title }}
    </template>

    <div class="mx-auto max-w-3xl space-y-8">
      <form class="mb-12 space-y-8" @submit.prevent="form.get(route('import.create'))">
        <div class="bg-white shadow sm:rounded-md">
          <div class="grid grid-cols-12 gap-6 px-4 py-5 sm:p-6">
            <div class="col-span-12 space-y-1">
              <Label for="url" value="URL" />
              <Input v-model="form.url" autocomplete="url" class="block w-full" required type="url" />
              <InputError :message="form.errors.url" />
            </div>

            <div v-if="props.firecrawl || props.openAI" class="col-span-12 space-y-1">
              <Label for="parser" value="Methode" />
              <div>
                <label class="flex items-center space-x-2">
                  <input v-model="form.parser" value="structured-data" required type="radio" />
                  <span>Structured data</span>
                </label>
              </div>
              <div v-if="props.firecrawl">
                <label class="flex items-center space-x-2">
                  <input v-model="form.parser" value="firecrawl" required type="radio" />
                  <span>Firecrawl</span>
                </label>
              </div>
              <div v-if="props.openAI">
                <label class="flex items-center space-x-2">
                  <input v-model="form.parser" value="open-ai" required type="radio" />
                  <span>AI (experimenteel)</span>
                </label>
              </div>
              <InputError :message="form.errors.parser" />
            </div>

            <div class="col-span-12 space-y-1">
              <label class="flex items-center space-x-2">
                <input v-model="form.force_import" value="true" type="checkbox" />
                <span>Forceer importeren van de originele bron.</span>
              </label>
            </div>
          </div>

          <div class="border-t border-gray-200 bg-gray-50 px-4 py-3 sm:px-6" :class="{ 'sm:rounded-b': !showHelp }">
            <div class="mx-auto max-w-3xl space-x-2 md:space-x-4">
              <Button :disabled="form.processing" class="text-xs" type="submit"> Importeren</Button>

              <a class="cursor-pointer text-sm text-gray-600 hover:text-gray-900" @click="showHelp = !showHelp">
                Hoe werkt dit?
              </a>
            </div>
          </div>

          <div v-if="showHelp" class="border-t border-gray-200 bg-sky-100 p-4 text-sky-700 md:rounded-b">
            <p>
              <strong>Structured data</strong><br />
              Bij het importeren wordt er op een webpagina gezocht naar een recept dat is gedefinieerd in het
              <a href="https://schema.org/Recipe" class="text-sky-900">schema.org/Recipe</a> formaat. Er wordt gezocht
              naar Microdata, RDFa en JSON-LD markups.<br />
              Als er geen recept wordt gevonden dan wordt er een foutmelding weergegeven.
            </p>
            <p v-if="props.firecrawl">
              <strong>Firecrawl</strong><br />
              Firecrawl is een geavanceerde web scraping service die gebruik maakt van AI om recepten van webpagina's te
              halen. Er zijn kosten verbonden aan het gebruik van Firecrawl.
            </p>
            <p v-if="props.openAI">
              <strong>Open AI</strong><br />
              Met behulp van Open AI wordt er een tekst gegenereerd op basis van de inhoud van de webpagina. Deze tekst
              wordt vervolgens geanalyseerd om te kijken of er een recept in staat. Als er een recept wordt gevonden dan
              wordt deze geïmporteerd. Als er geen recept wordt gevonden dan wordt er een foutmelding weergegeven. Deze
              methode is experimenteel en kan dus fouten bevatten. Er zijn ook kosten verbonden aan het gebruik van Open
              AI.
            </p>
          </div>
        </div>
      </form>
    </div>
  </DefaultLayout>
</template>
