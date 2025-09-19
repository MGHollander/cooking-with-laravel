<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Textarea from "@/Components/Textarea.vue";
import TipTapEditor from "@/Components/TipTapEditor.vue";
import ValidationErrors from "@/Components/ValidationErrors.vue";
import DefaultLayout from "@/Layouts/Default.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faExternalLink } from "@fortawesome/free-solid-svg-icons";

const props = defineProps({
  url: String,
  recipe: Object,
  import_log_id: Number,
});

const title = "Geïmporteerd recept controleren";

const form = useForm({
  title: props.recipe.title,
  external_image: props.recipe.images && props.recipe.images.length > 0 ? props.recipe.images[0] : "",
  summary: props.recipe.summary,
  tags: props.recipe.tags,
  preparation_minutes: String(props.recipe.preparation_minutes),
  cooking_minutes: String(props.recipe.cooking_minutes),
  servings: String(props.recipe.servings),
  difficulty: props.recipe.difficulty,
  ingredients: props.recipe.ingredients,
  instructions: props.recipe.instructions,
  source_label: props.recipe.source_label,
  source_link: props.recipe.source_link,
  import_log_id: props.import_log_id,
  return_to_import_page: false,
});
</script>

<template>
  <Head :title="title" />

  <DefaultLayout>
    <template #header>
      {{ title }}
    </template>

    <form class="mx-auto mb-12 max-w-3xl space-y-8" @submit.prevent="form.post(route('import.store'))">
      <div class="space-y-2 bg-white px-4 py-5 shadow sm:rounded sm:p-6">
        <div class="grid grid-cols-12 gap-6">
          <ValidationErrors class="col-span-12 -mx-4 -mt-5 p-4 sm:-mx-6 sm:-mt-6 sm:rounded-t" />

          <div class="col-span-12 space-y-1">
            <Label for="title" value="Titel" />
            <Input v-model="form.title" autocomplete="title" class="block w-full" required type="text" />
            <InputError :message="form.errors.title" />
          </div>

          <div v-if="recipe.images && recipe.images.length > 0" class="col-span-12 space-y-1">
            <Label for="image" value="Afbeelding" />
            <div class="flex gap-2">
              <label
                v-for="(image, index) in recipe.images"
                :key="index"
                class="relative cursor-pointer rounded-md border-2 border-transparent p-0.5 hover:border-indigo-300 has-[:checked]:border-2 has-[:checked]:border-indigo-500"
                :for="'image-' + index"
              >
                <input
                  type="radio"
                  v-model="form.external_image"
                  :value="image"
                  class="m-2 absolute"
                  :id="'image-' + index"
                  :checked="form.external_image === image"
                />
                <img
                  :src="image"
                  :alt="'Image ' + (index + 1)"
                  class="max-h-32 max-w-full rounded-md"
                  @click="$event.target.previousElementSibling.click()"
                />
              </label>
            </div>
            <InputError :message="form.errors.external_image" />
          </div>

          <div class="col-span-12 space-y-1">
            <Label for="summary" value="Samenvatting (optioneel)" />
            <TipTapEditor
              v-model="form.summary"
              placeholder="Voer een korte samenvatting van het recept in..."
              :rows="4"
              :toolbar="['bold', 'italic', 'underline']"
            />
            <InputError :message="form.errors.summary" />
          </div>

          <div class="col-span-12 space-y-1">
            <Label for="tags" value="Tags (optioneel)" />
            <Input v-model="form.tags" class="block w-full" type="text" />
            <p class="text-xs text-gray-500">
              Komma gescheiden lijst met tags. Bijvoorbeeld: "vegan, glutenvrij, lactosevrij"
            </p>
            <InputError :message="form.errors.tags" />
          </div>
        </div>
      </div>

      <div class="space-y-2 bg-white px-4 py-5 shadow sm:rounded-md sm:p-6">
        <div class="grid grid-cols-12 gap-6">
          <div class="col-span-12 space-y-1 self-end sm:col-span-6">
            <Label for="servings" value="Aantal porties" />
            <Input v-model="form.servings" class="block w-full" min="1" required type="number" />
            <InputError :message="form.errors.servings" />
          </div>

          <div class="col-span-12 space-y-1 sm:col-span-6">
            <Label for="difficulty" value="Moeilijkheid" />
            <select
              v-model="form.difficulty"
              class="block w-full rounded-md border-gray-300 shadow-sm transition duration-150 ease-in-out focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            >
              <option value="easy">Makkelijk</option>
              <option value="average">Gemiddeld</option>
              <option value="difficult">Moeilijk</option>
            </select>
            <InputError :message="form.errors.difficulty" />
          </div>

          <div class="col-span-12 space-y-1 self-end sm:col-span-6">
            <Label for="preparation_minutes" value="Voorbereidingstijd in minuten (optioneel)" />
            <Input v-model="form.preparation_minutes" class="block w-full" min="1" type="number" />
            <InputError :message="form.errors.preparation_minutes" />
          </div>

          <div class="col-span-12 space-y-1 self-end sm:col-span-6">
            <Label for="cooking_minutes" value="Bereidingstijd in minuten (optioneel)" />
            <Input v-model="form.cooking_minutes" class="block w-full" min="1" type="number" />
            <InputError :message="form.errors.cooking_minutes" />
          </div>

          <div class="col-span-12 grid grid-cols-12 gap-6">
            <div class="col-span-12 space-y-1">
              <Label>Ingredienten</Label>
              <Textarea v-model="form.ingredients" rows="10" class="block w-full" required />
              <InputError :message="form.errors.ingredients" />
              <p class="!my-3 text-xs text-gray-500">Je kunt de ingredienten verrijken met de volgende opties:</p>
              <ul class="list-outside pl-4 text-xs text-gray-500">
                <li>
                  Één ingredient per regel. Begin een regel met een # om een titel en een nieuwe sectie te maken. Voeg
                  een lege regel toe om een nieuwe sectie zonder titel te maken.
                </li>
                <li>
                  Ingredienten worden automatisch gesplitst in hoeveelheid (een getal), eenheid (bijv. gram, eetlepels,
                  etc.), naam (wortel) en info (in blokjes gesneden).
                </li>
                <li>
                  Je kunt een de meervoud van een ingredient toevoegen door een | achter de naam te zetten en de
                  meervoudsvorm toe te voegen. Bijvoorbeeld: wortel|wortels.
                </li>
                <li>Plaats info tussen haakjes. Bijvoorbeeld: wortel (in blokjes gesneden).</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="space-y-2 bg-white shadow sm:rounded-md">
        <div class="grid grid-cols-12 gap-6 px-4 py-5 sm:p-6">
          <div class="col-span-12 space-y-1">
            <Label for="instructions" value="Instructies" />
            <TipTapEditor
              v-model="form.instructions"
              placeholder="Voer de bereidingsinstructies in..."
              :rows="10"
              :toolbar="['orderedList', 'bulletList', '|', 'bold', 'italic', 'underline', '|', 'heading']"
            />
            <InputError :message="form.errors.instructions" />
          </div>

          <div class="col-span-12 space-y-1">
            <Label for="source_label" value="Bron naam (optioneel)" />
            <Input v-model="form.source_label" class="block w-full" type="text" />
            <InputError :message="form.errors.source_label" />
          </div>

          <div class="col-span-12 space-y-1">
            <Label for="source_link" value="Bron link (optioneel)" />
            <div class="flex items-stretch">
              <Input v-model="form.source_link" class="block w-full" type="text" />
              <Button
                v-if="form.source_link"
                :href="form.source_link"
                target="_blank"
                class="ml-4"
                button-style="secondary"
              >
                <FontAwesomeIcon :icon="faExternalLink" />
              </Button>
            </div>
            <InputError :message="form.errors.source_link" />
          </div>
        </div>

        <div class="fixed bottom-0 left-0 w-full border-t border-gray-200 bg-gray-50 px-4 py-3 sm:px-6">
          <div class="mx-auto flex max-w-3xl space-x-2 sm:px-6">
            <Button
              :disabled="form.processing"
              class="text-xs"
              type="submit"
              @click="form.return_to_import_page = false"
            >
              Opslaan
            </Button>

            <Button
              :disabled="form.processing"
              class="text-xs"
              type="submit"
              button-style="secondary"
              @click="form.return_to_import_page = true"
            >
              Opslaan en nieuw recept importeren
            </Button>
          </div>
        </div>
      </div>
    </form>
  </DefaultLayout>
</template>
