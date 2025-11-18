<script setup>
import { computed, onMounted, ref, watch } from "vue";
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
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
import { Cropper } from "vue-advanced-cropper";
import "vue-advanced-cropper/dist/style.css";

const props = defineProps({
  url: String,
  parser: String,
  force_import: Boolean,
  recipe: Object,
  import_log_id: Number,
  config: Object,
});

const isLoading = ref(true);
const errorMessage = ref("");
const images = ref([]);
const page = usePage();

const cropperCard = ref(null);
const cropperShow = ref(null);
const image = ref(null);

const form = useForm({
  locale: page.props.locale || 'nl',
  title: "",
  external_image: "",
  media_dimensions: null,
  summary: "",
  tags: "",
  preparation_minutes: "",
  cooking_minutes: "",
  servings: "",
  difficulty: "",
  ingredients: "",
  instructions: "",
  source_label: "",
  source_link: "",
  import_log_id: null,
  return_to_import_page: false,
  no_index: true, // Default to true for imported recipes
});

const submitForm = () => {
  form.media_dimensions = {
    card: cropperCard?.value ? cropperCard.value.getResult().coordinates : null,
    show: cropperShow?.value ? cropperShow.value.getResult().coordinates : null,
  };
  form.post(route("import.store"));
};

const loadExternalImage = () => {
  if (!form.external_image) {
    image.value = null;
    return;
  }

  // Use our backend proxy to avoid CORS issues with external images
  const proxyUrl = route("import.proxy-image", { url: form.external_image });
  image.value = proxyUrl;
};

const getStencilSize = ({ boundaries }) => {
  return {
    width: boundaries.width - 25,
    height: boundaries.height - 25,
  };
};

watch(() => form.external_image, loadExternalImage);

onMounted(() => {
  if (isLoading.value) {
    axios
      .post(route("import.import-recipe"), {
        url: props.url,
        parser: props.parser,
        force_import: props.force_import,
      })
      .then((response) => {
        const recipe = response.data.recipe;
        
        form.locale = response.data.locale || page.props.locale || 'nl';
        form.title = recipe.title;
        form.external_image = recipe?.images?.length > 0 ? recipe.images[0] : "";
        form.summary = recipe.summary;
        form.tags = recipe.tags;
        form.preparation_minutes = recipe.preparation_minutes ? String(recipe.preparation_minutes) : "";
        form.cooking_minutes = recipe.cooking_minutes ? String(recipe.cooking_minutes) : "";
        form.servings = String(recipe.servings);
        form.difficulty = recipe.difficulty;
        form.ingredients = recipe.ingredients;
        form.instructions = recipe.instructions;
        form.source_label = recipe.source_label;
        form.source_link = recipe.source_link;

        isLoading.value = false;

        if (recipe.images?.length) {
          images.value = recipe.images;
        }
      })
      .catch((error) => {
        router.get(route("import.index"));
      });
  }
});
</script>

<template>
  <Head :title="title" />

  <DefaultLayout>
    <template #header>
      {{ $t('import.form.title') }}
    </template>
    <Transition mode="out-in">
      <div v-if="isLoading" class="flex flex-col justify-center items-center h-64">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="100">
          <circle fill="#047857" stroke="#047857" stroke-width="15" r="15" cx="40" cy="65">
            <animate
              attributeName="cy"
              calcMode="spline"
              dur="2"
              values="65;135;65;"
              keySplines=".5 0 .5 1;.5 0 .5 1"
              repeatCount="indefinite"
              begin="-.4"
            ></animate>
          </circle>
          <circle fill="#047857" stroke="#047857" stroke-width="15" r="15" cx="100" cy="65">
            <animate
              attributeName="cy"
              calcMode="spline"
              dur="2"
              values="65;135;65;"
              keySplines=".5 0 .5 1;.5 0 .5 1"
              repeatCount="indefinite"
              begin="-.2"
            ></animate>
          </circle>
          <circle fill="#047857" stroke="#047857" stroke-width="15" r="15" cx="160" cy="65">
            <animate
              attributeName="cy"
              calcMode="spline"
              dur="2"
              values="65;135;65;"
              keySplines=".5 0 .5 1;.5 0 .5 1"
              repeatCount="indefinite"
              begin="0"
            ></animate>
          </circle>
        </svg>
        <p class="font-bold">{{ $t('import.form.loading') }}</p>
      </div>
      <div v-else-if="errorMessage" class="text-red-500 p-4">{{ errorMessage }}</div>
      <form v-else class="mx-auto max-w-3xl space-y-8" @submit.prevent="submitForm">
        <div class="space-y-2 bg-white px-4 py-5 shadow sm:rounded sm:p-6">
          <div class="space-y-1">
            <Label for="locale" :value="$t('recipes.form.language')" />
            <select
              v-model="form.locale"
              class="block w-full rounded-md border-gray-300 shadow-sm transition duration-150 ease-in-out focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            >
              <option value="en">🇬🇧 English</option>
              <option value="nl">🇳🇱 Nederlands</option>
            </select>
            <InputError :message="form.errors.locale" />
          </div>
        </div>

        <div class="space-y-2 bg-white px-4 py-5 shadow sm:rounded sm:p-6">
          <div class="grid grid-cols-12 gap-6">
            <ValidationErrors class="col-span-12 -mx-4 -mt-5 p-4 sm:-mx-6 sm:-mt-6 sm:rounded-t" />

            <div class="col-span-12 space-y-1">
              <Label for="title" :value="$t('recipes.form.title')" />
              <Input v-model="form.title" autocomplete="title" class="block w-full" required type="text" />
              <InputError :message="form.errors.title" />
            </div>

            <div v-if="images.length > 0" class="col-span-12 space-y-1">
              <Label for="image" :value="$t('recipes.form.image')" />
              <div class="flex gap-2 mb-4">
                <label
                  v-for="(img, index) in images"
                  :key="index"
                  class="relative cursor-pointer rounded-md border-2 border-transparent p-0.5 hover:border-indigo-300 has-[:checked]:border-2 has-[:checked]:border-indigo-500"
                  :for="'image-' + index"
                >
                  <input
                    type="radio"
                    v-model="form.external_image"
                    :value="img"
                    class="m-2 absolute"
                    :id="'image-' + index"
                    :checked="form.external_image === img"
                  />
                  <img :src="img" :alt="'Image ' + (index + 1)" class="max-h-32 max-w-full rounded-md" />
                </label>
              </div>
              <label class="flex items-center gap-2">
                <input type="radio" v-model="form.external_image" value="" />
                <span>{{ $t('import.form.import_without_image') }}</span>
              </label>

              <div v-if="image" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                  <strong class="text-sm">{{ $t('recipes.form.card_page') }}</strong>
                  <cropper
                    ref="cropperCard"
                    class="vue-advanced-cropper h-[16rem] max-w-full"
                    :src="image"
                    :stencil-size="getStencilSize"
                    :stencil-props="{
                      handlers: {},
                      movable: false,
                      resizable: false,
                      aspectRatio:
                        props.config.image_dimensions.conversions.card.width /
                        props.config.image_dimensions.conversions.card.height,
                      previewClass: 'cropper-preview',
                    }"
                    :min-width="1"
                    :min-height="1"
                    image-restriction="stencil"
                    background-class="cropper-bg"
                  />
                </div>
                <div>
                  <strong class="text-sm">{{ $t('recipes.form.recipe_page') }}</strong>
                  <cropper
                    ref="cropperShow"
                    class="vue-advanced-cropper h-[16rem] max-w-full"
                    :src="image"
                    :stencil-size="getStencilSize"
                    :stencil-props="{
                      handlers: {},
                      movable: false,
                      resizable: false,
                      aspectRatio:
                        props.config.image_dimensions.conversions.show.width /
                        props.config.image_dimensions.conversions.show.height,
                      previewClass: 'cropper-preview',
                    }"
                    :min-width="1"
                    :min-height="1"
                    image-restriction="stencil"
                    background-class="cropper-bg"
                  />
                </div>
              </div>

              <InputError :message="form.errors.external_image" />
            </div>

            <div v-else class="col-span-12 space-y-1">
              <Label for="image" :value="$t('recipes.form.image')" />
              <div class="rounded-md bg-yellow-50 p-4 border border-yellow-200">
                <p class="text-sm text-yellow-800">
                  {{ $t('import.form.no_images_found') }}
                </p>
              </div>
            </div>

            <div class="col-span-12 space-y-1">
              <Label for="summary" :value="$t('recipes.form.summary')" />
              <TipTapEditor
                v-model="form.summary"
                :placeholder="$t('recipes.form.summary_placeholder')"
                :rows="4"
                :toolbar="['bold', 'italic', 'underline']"
              />
              <InputError :message="form.errors.summary" />
            </div>

            <div class="col-span-12 space-y-1">
              <Label for="tags" :value="$t('recipes.form.tags')" />
              <Input v-model="form.tags" class="block w-full" type="text" />
              <p class="text-xs text-gray-500">
                {{ $t('recipes.form.tags_help') }}
              </p>
              <InputError :message="form.errors.tags" />
            </div>
          </div>
        </div>

        <div class="space-y-2 bg-white px-4 py-5 shadow sm:rounded-md sm:p-6">
          <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12 space-y-1 self-end sm:col-span-6">
              <Label for="servings" :value="$t('recipes.form.servings')" />
              <Input v-model="form.servings" class="block w-full" min="1" required type="number" />
              <InputError :message="form.errors.servings" />
            </div>

            <div class="col-span-12 space-y-1 sm:col-span-6">
              <Label for="difficulty" :value="$t('recipes.form.difficulty')" />
              <select
                v-model="form.difficulty"
                class="block w-full rounded-md border-gray-300 shadow-sm transition duration-150 ease-in-out focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
              >
                <option value="easy">{{ $t('recipes.easy') }}</option>
                <option value="average">{{ $t('recipes.average') }}</option>
                <option value="difficult">{{ $t('recipes.difficult') }}</option>
              </select>
              <InputError :message="form.errors.difficulty" />
            </div>

            <div class="col-span-12 space-y-1 self-end sm:col-span-6">
              <Label for="preparation_minutes" :value="$t('recipes.form.preparation_time')" />
              <Input v-model="form.preparation_minutes" class="block w-full" min="1" type="number" />
              <InputError :message="form.errors.preparation_minutes" />
            </div>

            <div class="col-span-12 space-y-1 self-end sm:col-span-6">
              <Label for="cooking_minutes" :value="$t('recipes.form.cooking_time')" />
              <Input v-model="form.cooking_minutes" class="block w-full" min="1" type="number" />
              <InputError :message="form.errors.cooking_minutes" />
            </div>

            <div class="col-span-12 grid grid-cols-12 gap-6">
              <div class="col-span-12 space-y-1">
                <Label>{{ $t('recipes.form.ingredients') }}</Label>
                <Textarea v-model="form.ingredients" rows="10" class="block w-full" required />
                <InputError :message="form.errors.ingredients" />
                <p class="!my-3 text-xs text-gray-500">{{ $t('recipes.form.ingredients_help') }}</p>
                <ul class="list-outside pl-4 text-xs text-gray-500" v-html="$t('recipes.form.ingredients_help_text')" />
              </div>
            </div>
          </div>
        </div>

        <div class="space-y-2 bg-white shadow sm:rounded-md">
          <div class="grid grid-cols-12 gap-6 px-4 py-5 sm:p-6">
            <div class="col-span-12 space-y-1">
              <Label for="instructions" :value="$t('recipes.form.instructions')" />
              <TipTapEditor
                v-model="form.instructions"
                :placeholder="$t('recipes.form.instructions_placeholder')"
                :rows="10"
                :toolbar="['orderedList', 'bulletList', '|', 'bold', 'italic', 'underline', '|', 'heading']"
              />
              <InputError :message="form.errors.instructions" />
            </div>

            <div class="col-span-12 space-y-1">
              <Label for="source_label" :value="$t('recipes.form.source_name')" />
              <Input v-model="form.source_label" class="block w-full" type="text" />
              <InputError :message="form.errors.source_label" />
            </div>

            <div class="col-span-12 space-y-1">
              <Label for="source_link" :value="$t('recipes.form.source_link')" />
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

            <div class="col-span-12 space-y-1">
              <label class="flex items-center">
                <input v-model="form.no_index" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                <span class="ml-2 text-sm text-gray-600">{{ $t('recipes.form.no_index') }}</span>
              </label>
              <InputError :message="form.errors.no_index" />
            </div>
          </div>
        </div>
        <div class="sticky bottom-0 w-screen ml-[50%] -translate-x-1/2 border-y border-gray-200 bg-gray-50 px-4 py-3 sm:px-6">
          <div class="mx-auto flex max-w-3xl space-x-2 sm:px-6">
            <Button
              :disabled="form.processing"
              class="text-xs"
              type="submit"
              @click="form.return_to_import_page = false"
            >
              {{ $t('import.form.save') }}
            </Button>

            <Button
              :disabled="form.processing"
              class="text-xs"
              type="submit"
              button-style="secondary"
              @click="form.return_to_import_page = true"
            >
              {{ $t('import.form.save_and_new') }}
            </Button>
          </div>
        </div>
      </form>
    </Transition>
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

/* we will explain what these classes do next! */
.v-enter-active,
.v-leave-active {
  transition: opacity 0.5s ease;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}
</style>
