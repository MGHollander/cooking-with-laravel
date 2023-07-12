<script setup>
import { Bold, Italic, Strikethrough, Underline } from "@ckeditor/ckeditor5-basic-styles";
import { ClassicEditor } from "@ckeditor/ckeditor5-editor-classic";
import { Essentials } from "@ckeditor/ckeditor5-essentials";
import { Link } from "@ckeditor/ckeditor5-link";
import { List } from "@ckeditor/ckeditor5-list";
import { Paragraph } from "@ckeditor/ckeditor5-paragraph";
import { faExternalLink } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { Head, router, useForm } from "@inertiajs/vue3";
import "../../../css/ckeditor.css";
import "../../../css/ckeditor-content-styles.css";
import { ref } from "vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import ValidationErrors from "@/Components/ValidationErrors.vue";
import DefaultLayout from "@/Layouts/Default.vue";

const props = defineProps({ recipe: Object });
const edit = route().current("recipes.edit") ?? false;

const form = useForm({
  // Fix multipart limitations @see https://inertiajs.com/file-uploads#multipart-limitations
  // NOTE: The form is also submitted useing the post method instead of the patch method.
  _method: edit ? "PATCH" : "POST",
  title: edit ? props.recipe.title : "",
  image: "",
  destroy_image: false,
  summary: edit ? props.recipe.summary ?? "" : "",
  tags: edit ? props.recipe.tags : "",
  preparation_minutes: edit ? props.recipe.preparation_minutes?.toString() : "",
  cooking_minutes: edit ? props.recipe.cooking_minutes?.toString() : "",
  servings: edit ? props.recipe.servings.toString() : "",
  difficulty: edit ? props.recipe.difficulty : "easy",
  ingredients: edit ? props.recipe.ingredients : "",
  instructions: edit ? props.recipe.instructions : "",
  source_label: edit ? props.recipe.source_label : "",
  source_link: edit ? props.recipe.source_link : "",
});

const imageInput = ref(null);
const imagePreview = ref(props.recipe?.image ?? null);

const updateImagePreview = (event) => {
  form.image = event.target.files[0];
  form.destroy_image = false;

  if (!form.image) return;

  const reader = new FileReader();

  reader.onload = (e) => {
    imagePreview.value = e.target.result;
  };

  reader.readAsDataURL(form.image);
};

const clearImageField = () => {
  form.image = null;
  form.destroy_image = true;
  imageInput.value.value = null;
  imagePreview.value = null;
};

const title = edit ? `Wijzig recept “${form.title}”` : "Voeg een nieuw recept toe";

const editor = ClassicEditor;
const summaryEditorConfig = {
  plugins: [Essentials, Bold, Italic, Link, Paragraph, Strikethrough, Underline],

  toolbar: {
    items: ["bold", "italic", "underline", "strikeThrough", "|", "link"],
  },
};

const instructionsEditorConfig = {
  plugins: [Essentials, Bold, Italic, Link, List, Paragraph, Strikethrough, Underline],

  toolbar: {
    items: ["bold", "italic", "underline", "strikeThrough", "|", "bulletedList", "numberedList", "|", "link"],
  },
};

function confirmDeletion() {
  if (confirm("Weet je zeker dat je dit recept wilt verwijderen?")) {
    router.delete(route("recipes.destroy", props.recipe.id), {
      method: "delete",
    });
  }
}
</script>

<template>
  <Head :title="title" />

  <DefaultLayout>
    <template #header>
      {{ title }} <a v-if="edit" :href="route('recipes.show', props.recipe)" class="ml-4 text-sm">Bekijk het recept</a>
    </template>

    <form
      class="mx-auto mb-12 max-w-3xl space-y-8"
      @submit.prevent="edit ? form.post(route('recipes.update', props.recipe.id)) : form.post(route('recipes.store'))"
    >
      <div class="space-y-2 bg-white px-4 py-5 shadow sm:rounded sm:p-6 sm:pb-8">
        <div class="grid grid-cols-12 gap-6">
          <ValidationErrors class="col-span-12 -mx-4 -mt-5 p-4 sm:-mx-6 sm:-mt-6 sm:rounded-t" />

          <div class="col-span-12 space-y-1">
            <Label for="title" value="Titel" />
            <Input v-model="form.title" autocomplete="title" class="block w-full" required type="text" />
            <InputError :message="form.errors.title" />
          </div>

          <div v-if="edit" class="col-span-12 space-y-1">
            <Label for="slug" value="Slug" />
            <blockquote class="font-mono text-sm">/{{ props.recipe.slug }}</blockquote>
          </div>

          <div class="col-span-12 space-y-1">
            <Label for="image" value="Afbeelding (optioneel)" />
            <input
              ref="imageInput"
              accept="image/jpeg,image/png"
              type="file"
              class="hidden"
              @change="updateImagePreview"
            />

            <Button v-if="!imagePreview" class="text-xs" @click="imageInput.click()"> Upload afbeelding</Button>

            <div v-else class="col-span-12 space-y-1">
              <img
                alt="Voorbeeld van de afbeelding"
                class="block max-h-32 max-w-full rounded-md bg-contain bg-center bg-no-repeat"
                :src="imagePreview"
              />

              <Button class="mr-1 text-xs" button-style="secondary" @click="imageInput.click()">
                Vervang afbeelding
              </Button>

              <Button class="text-xs" button-style="danger" @click="clearImageField"> Verwijder afbeelding</Button>
            </div>

            <progress v-if="form.progress" :value="form.progress.percentage" max="100">
              {{ form.progress.percentage }}%
            </progress>

            <InputError :message="form.errors.image" />
          </div>

          <div class="col-span-12 space-y-1">
            <Label for="summary" value="Samenvatting (optioneel)" />
            <ckeditor v-model="form.summary" :editor="editor" :config="summaryEditorConfig" />
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
          <div class="col-span-12 space-y-1 self-end sm:col-span-4">
            <Label for="servings" value="Aantal porties" />
            <Input v-model="form.servings" class="block w-full" min="1" required type="number" />
            <InputError :message="form.errors.servings" />
          </div>

          <div class="col-span-12 space-y-1 self-end sm:col-span-4">
            <Label for="preparation_minutes" value="Voorbereidingstijd in minuten (optioneel)" />
            <Input v-model="form.preparation_minutes" class="block w-full" min="1" type="number" />
            <InputError :message="form.errors.preparation_minutes" />
          </div>

          <div class="col-span-12 space-y-1 self-end sm:col-span-4">
            <Label for="cooking_minutes" value="Bereidingstijd in minuten (optioneel)" />
            <Input v-model="form.cooking_minutes" class="block w-full" min="1" type="number" />
            <InputError :message="form.errors.cooking_minutes" />
          </div>

          <div class="col-span-12 space-y-1 sm:col-span-12">
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

          <div class="col-span-12 grid grid-cols-12 gap-6">
            <div class="col-span-12 space-y-1">
              <Label>Ingredienten</Label>
              <textarea
                v-model="form.ingredients"
                rows="10"
                class="block w-full rounded-md border-gray-300 shadow-sm transition duration-150 ease-in-out focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                required
              />
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

      <div class="space-y-2 bg-white px-4 py-5 shadow sm:rounded-md sm:p-6">
        <div class="grid grid-cols-12 gap-6">
          <div class="col-span-12 space-y-1">
            <Label for="instructions" value="Instructies" />
            <ckeditor v-model="form.instructions" :editor="editor" :config="instructionsEditorConfig" />
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
                v-if="
                  form.source_link &&
                  (form.source_link.startsWith('http://') || form.source_link.startsWith('https://'))
                "
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
      </div>

      <div class="fixed bottom-0 left-0 w-full border-t border-gray-200 bg-gray-50 px-4 py-3 sm:px-6">
        <div class="mx-auto flex max-w-3xl justify-between sm:px-6">
          <Button :disabled="form.processing" class="text-xs" type="submit">Opslaan</Button>
          <Button
            v-if="edit"
            :disabled="form.processing"
            button-style="danger"
            class="text-xs"
            @click="confirmDeletion"
          >
            Verwijder
          </Button>
        </div>
      </div>
    </form>
  </DefaultLayout>
</template>
