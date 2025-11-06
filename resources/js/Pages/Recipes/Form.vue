<script setup>
import { faExternalLink } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { Head, router, useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import { Cropper } from "vue-advanced-cropper";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Textarea from "@/Components/Textarea.vue";
import TipTapEditor from "@/Components/TipTapEditor.vue";
import ValidationErrors from "@/Components/ValidationErrors.vue";
import DefaultLayout from "@/Layouts/Default.vue";
import "vue-advanced-cropper/dist/style.css";
import FlashMessage from "@/Components/FlashMessage.vue";

const props = defineProps({ recipe: Object, config: Object });
const edit = route().current("recipes.edit") ?? false;

const form = useForm({
  // Fix multipart limitations @see https://inertiajs.com/file-uploads#multipart-limitations
  // NOTE: The form is also submitted using the post method instead of the patch method.
  _method: edit ? "PATCH" : "POST",
  title: edit ? props.recipe.title : "",
  media: null,
  media_dimensions: null,
  destroy_media: false,
  summary: edit ? (props.recipe.summary ?? "") : "",
  tags: edit ? props.recipe.tags : "",
  preparation_minutes: edit ? props.recipe.preparation_minutes?.toString() : "",
  cooking_minutes: edit ? props.recipe.cooking_minutes?.toString() : "",
  servings: edit ? props.recipe.servings.toString() : "",
  difficulty: edit ? props.recipe.difficulty : "easy",
  ingredients: edit ? props.recipe.ingredients : "",
  instructions: edit ? props.recipe.instructions : "",
  source_label: edit ? props.recipe.source_label : "",
  source_link: edit ? props.recipe.source_link : "",
  no_index: edit ? Boolean(props.recipe.no_index) : false,
});

const title = edit ? `Wijzig recept "${form.title}"` : "Voeg een nieuw recept toe";

const file = ref(null);
const image = ref({ src: props.recipe?.media?.original_url ?? null, type: null });
const cropperCard = ref(null);
const cropperShow = ref(null);
const imageSizeWarning = ref("");

const save = () => {
  form.media_dimensions = {
    card: cropperCard?.value ? cropperCard.value.getResult().coordinates : null,
    show: cropperShow?.value ? cropperShow.value.getResult().coordinates : null,
  };

  form.post(edit ? route("recipes.update", props.recipe.id) : route("recipes.store"));
};

function resetErrors() {
  form.errors.media = null;
  imageSizeWarning.value = null;
}

function destroyMedia() {
  resetErrors();
  form.destroy_media = true;
  form.media = null;
  form.media_dimensions = null;
  image.value = { src: null, type: null };
}

// Source: https://advanced-cropper.github.io/vue-advanced-cropper/guides/recipes.html#load-image-from-a-disc
function loadImage(event) {
  const { files } = event.target;

  if (files && files[0]) {
    resetErrors();

    if (files[0].size > props.config.max_file_size) {
      form.errors.media = `De afbeelding is te groot. De maximale grootte is ${props.config.max_file_size / 1024 / 1024} MB. Verklein de afbeelding en probeer het opnieuw.`;
      return;
    }

    form.media = files[0];

    const img = new Image();
    img.onload = function () {
      if (
        img.width < props.config.image_dimensions.advised_minimum.width ||
        img.height < props.config.image_dimensions.advised_minimum.height
      ) {
        imageSizeWarning.value = `De afbeelding is kleiner dan ${props.config.image_dimensions.advised_minimum.width}x${props.config.image_dimensions.advised_minimum.height} pixels. Als je deze gebruikt, dan wordt deze vergroot. Dit gaat ten koste van de kwaliteit.`;
      }
    };
    img.src = URL.createObjectURL(form.media);

    if (image.value.src) {
      URL.revokeObjectURL(image.value.src);
    }

    const blob = URL.createObjectURL(form.media);
    const reader = new FileReader();
    reader.onload = (e) => {
      image.value = {
        src: blob,
        type: getMimeType(e.target.result, form.media.type),
      };
    };
    reader.readAsArrayBuffer(form.media);
  }
}

// This function is used to detect the actual image type,
// Source: https://advanced-cropper.github.io/vue-advanced-cropper/guides/recipes.html#load-image-from-a-disc
function getMimeType(file, fallback = null) {
  const byteArray = new Uint8Array(file).subarray(0, 4);
  let header = "";
  for (let i = 0; i < byteArray.length; i++) {
    header += byteArray[i].toString(16);
  }
  switch (header) {
    case "89504e47":
      return "image/png";
    case "47494638":
      return "image/gif";
    case "ffd8ffe0":
    case "ffd8ffe1":
    case "ffd8ffe2":
    case "ffd8ffe3":
    case "ffd8ffe8":
      return "image/jpeg";
    default:
      return fallback;
  }
}

function getStencilSize({ boundaries }) {
  return {
    width: boundaries.width - 25,
    height: boundaries.height - 25,
  };
}

function confirmDeletion() {
  if (confirm("Weet je zeker dat je dit recept wilt verwijderen?")) {
    router.delete(route("recipes.destroy", props.recipe.id), {
      method: "delete",
    });
  }
}

onMounted(() => {
  const coordinatesCard = props.recipe?.media?.manipulations?.card?.manualCrop;
  if (coordinatesCard) {
    const [width, height, left, top] = coordinatesCard;

    cropperCard.value.setCoordinates({
      width: width,
      height: height,
      left: left,
      top: top,
    });
  }

  const coordinatesShow = props.recipe?.media?.manipulations?.show?.manualCrop;
  if (coordinatesShow) {
    const [width, height, left, top] = coordinatesShow;

    cropperShow.value.setCoordinates({
      width: width,
      height: height,
      left: left,
      top: top,
    });
  }
});
</script>

<template>
  <Head :title="title" />

  <DefaultLayout>
    <template #header>
      {{ title }} <a v-if="edit" :href="route('recipes.show', props.recipe)" class="ml-4 text-sm">Bekijk het recept</a>
    </template>

    <form class="mx-auto mb-12 max-w-3xl space-y-8" @submit.prevent="save">
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

            <div v-if="image.src" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div>
                <strong class="text-sm">Overzicht pagina's</strong>
                <cropper
                  ref="cropperCard"
                  class="vue-advanced-cropper h-[16rem] max-w-full"
                  :src="image.src"
                  :stencil-size="getStencilSize"
                  :stencil-props="{
                    handlers: {},
                    movable: false,
                    resizable: false,
                    aspectRatio:
                      config.image_dimensions.conversions.card.width / config.image_dimensions.conversions.card.height,
                    previewClass: 'cropper-preview',
                  }"
                  :min-width="1"
                  :min-height="1"
                  image-restriction="stencil"
                  background-class="cropper-bg"
                />
              </div>
              <div>
                <strong class="text-sm">Recept pagina</strong>
                <cropper
                  ref="cropperShow"
                  class="vue-advanced-cropper h-[16rem] max-w-full"
                  :src="image.src"
                  :stencil-size="getStencilSize"
                  :stencil-props="{
                    handlers: {},
                    movable: false,
                    resizable: false,
                    aspectRatio:
                      config.image_dimensions.conversions.show.width / config.image_dimensions.conversions.show.height,
                    previewClass: 'cropper-preview',
                  }"
                  :min-width="1"
                  :min-height="1"
                  image-restriction="stencil"
                  background-class="cropper-bg"
                />
              </div>
            </div>

            <input ref="file" type="file" class="hidden" accept="image/jpeg,image/png,image/webp,image/avif" @change="loadImage($event)" />
            <template v-if="image.src">
              <Button class="mr-1 text-xs" button-style="secondary" @click="file.click()">Vervang afbeelding</Button>
              <Button class="text-xs" button-style="danger" @click="destroyMedia">Verwijder afbeelding</Button>
            </template>
            <Button v-else class="text-xs" @click="file.click()">Upload afbeelding</Button>

            <progress v-if="form.progress" :value="form.progress.percentage" max="100">
              {{ form.progress.percentage }}%
            </progress>

            <InputError :message="form.errors.media" />
          </div>

          <FlashMessage
            v-if="imageSizeWarning"
            :message="imageSizeWarning"
            type="warning"
            class="col-span-12 text-sm"
          />

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

      <div class="space-y-2 bg-white px-4 py-5 shadow sm:rounded-md sm:p-6">
        <div class="grid grid-cols-12 gap-6">
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

          <div class="col-span-12 space-y-1">
            <label class="flex items-center">
              <input v-model="form.no_index" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
              <span class="ml-2 text-sm text-gray-600">Zoekmachines mogen dit recept niet indexeren</span>
            </label>
            <InputError :message="form.errors.no_index" />
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

<style scss>
/* Styling the cropper requires a global selector. @see https://advanced-cropper.github.io/vue-advanced-cropper/guides/customize-appearance.html#styling-notice */
.cropper-bg {
  background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQAQMAAAAlPW0iAAAAA3NCSVQICAjb4U/gAAAABlBMVEXMzMz////TjRV2AAAACXBIWXMAAArrAAAK6wGCiw1aAAAAHHRFWHRTb2Z0d2FyZQBBZG9iZSBGaXJld29ya3MgQ1M26LyyjAAAABFJREFUCJlj+M/AgBVhF/0PAH6/D/HkDxOGAAAAAElFTkSuQmCC);
  background-color: transparent;
}

.cropper-preview {
  border-width: 2px;
  border-color: dodgerblue;
}

.vue-simple-line {
  border-width: 0px;
}
</style>
