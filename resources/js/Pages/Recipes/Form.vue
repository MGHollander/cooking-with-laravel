<script setup>
import {Head, useForm} from '@inertiajs/vue3';
import {ClassicEditor} from '@ckeditor/ckeditor5-editor-classic';
import {Essentials} from '@ckeditor/ckeditor5-essentials';
import {Bold, Italic, Strikethrough, Underline} from '@ckeditor/ckeditor5-basic-styles';
import {Link} from '@ckeditor/ckeditor5-link';
import {List} from '@ckeditor/ckeditor5-list';
import {Paragraph} from '@ckeditor/ckeditor5-paragraph';
import '../../../css/ckeditor.css';
import DefaultLayout from '@/Layouts/Default.vue';
import Button from '@/Components/Button.vue';
import Input from '@/Components/Input.vue';
import InputError from '@/Components/InputError.vue';
import Label from '@/Components/Label.vue';
import {ref} from "vue";

const props = defineProps(['recipe'])
const edit = route().current('recipes.edit') ?? false;

const form = useForm({
    _method: edit ? 'PATCH' : 'POST',
    title: edit ? props.recipe.title : '',
    image: '',
    destroy_image: false,
    preparation_minutes: edit ? props.recipe.preparation_minutes : '',
    cooking_minutes: edit ? props.recipe.cooking_minutes : '',
    servings: edit ? props.recipe.servings : '',
    difficulty: edit ? props.recipe.difficulty : 'easy',
    ingredients: edit ? props.recipe.ingredients : '',
    summary: edit ? props.recipe.summary : '',
    instructions: edit ? props.recipe.instructions : '',
    source_label: edit ? props.recipe.source_label : '',
    source_link: edit ? props.recipe.source_link : '',
})

const imageInput = ref(null);
const imagePreview = ref(props.recipe?.image ?? null);

const updateImagePreview = (event) => {
    form.image = event.target.files[0]
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
}

const submit = () => {
    if (edit) {
        form.post(route('recipes.update', props.recipe.id))
    } else {
        form.post(route('recipes.store'));
    }
}

const title = edit ? 'Update Recipe "' + form.title + '"' : 'Add a recipe'

const editor = ClassicEditor;
const summaryEditorConfig = {
    plugins: [
        Essentials,
        Bold,
        Italic,
        Link,
        Paragraph,
        Strikethrough, Underline
    ],

    toolbar: {
        items: [
            'bold',
            'italic',
            'underline',
            'strikeThrough',
            '|',
            'link',
        ]
    }
}

const instructionsEditorConfig = {
    plugins: [
        Essentials,
        Bold,
        Italic,
        Link,
        List,
        Paragraph,
        Strikethrough, Underline,
    ],

    toolbar: {
        items: [
            'bold',
            'italic',
            'underline',
            'strikeThrough',
            '|',
            'bulletedList',
            'numberedList',
            '|',
            'link',
        ]
    }
}
</script>

<template>
    <Head :title="title"/>

    <DefaultLayout>
        <template #header>
            {{ title }}
        </template>

        <form class="max-w-3xl mx-auto mb-12 space-y-8" @submit.prevent="submit">
            <div class="px-4 py-5 sm:p-6 sm:pb-8 bg-white shadow sm:rounded-md space-y-2">
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-12 space-y-1">
                        <Label for="title" value="Title"/>
                        <Input v-model="form.title" autocomplete="title" class="block w-full" required type="text"/>
                        <InputError :message="form.errors.title"/>
                    </div>

                    <div v-if="edit" class="col-span-12 space-y-1">
                        <Label for="slug" value="Slug"/>
                        <blockquote class="font-mono text-sm">/{{ props.recipe.slug }}</blockquote>
                    </div>


                    <div class="col-span-12 space-y-1">
                        <Label for="image" value="Image (optional)"/>
                        <input ref="imageInput" accept="image/jpeg,image/png" type="file" @change="updateImagePreview"
                               class="hidden"/>

                        <Button v-if="!imagePreview" class="text-xs" @click="imageInput.click()">
                            Upload image
                        </Button>

                        <div v-else class="col-span-12 space-y-1">
                            <img
                                alt="Image preview"
                                class="block rounded-md max-w-full max-h-32 bg-contain bg-no-repeat bg-center"
                                :src="imagePreview"
                            />

                            <Button class="text-xs mr-1" button-style="secondary" @click="imageInput.click()">
                                Replace image
                            </Button>

                            <Button class="text-xs" button-style="danger" @click="clearImageField">
                                Remove image
                            </Button>
                        </div>

                        <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                            {{ form.progress.percentage }}%
                        </progress>

                        <InputError :message="form.errors.image"/>
                    </div>

                    <div class="col-span-12 space-y-1">
                        <Label for="summary" value="Summary (optional)"/>
                        <ckeditor :editor="editor" v-model="form.summary" :config="summaryEditorConfig"/>
                        <InputError :message="form.errors.summary"/>
                    </div>
                </div>
            </div>

            <div class="px-4 py-5 sm:p-6 bg-white shadow sm:rounded-md space-y-2">
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-12 sm:col-span-4 space-y-1">
                        <Label for="servings" value="Servings"/>
                        <Input v-model="form.servings" class="block w-full" min="1" required type="number"/>
                        <InputError :message="form.errors.servings"/>
                    </div>

                    <div class="col-span-12 sm:col-span-4 space-y-1">
                        <Label for="preparation_minutes" value="Preparation in minutes (optional)"/>
                        <Input v-model="form.preparation_minutes" class="block w-full" min="1" type="number"/>
                        <InputError :message="form.errors.preparation_minutes"/>
                    </div>

                    <div class="col-span-12 sm:col-span-4 space-y-1">
                        <Label for="cooking_minutes" value="Cooking in minutes (optional)"/>
                        <Input v-model="form.cooking_minutes" class="block w-full" min="1" type="number"/>
                        <InputError :message="form.errors.cooking_minutes"/>
                    </div>

                    <div class="col-span-12 sm:col-span-12 space-y-1">
                        <Label for="difficulty" value="Difficulty"/>
                        <select
                            v-model="form.difficulty"
                            class="
                                block w-full
                                border-gray-300 rounded-md
                                focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                                shadow-sm
                                transition ease-in-out duration-150
                            "
                        >
                            <option value="easy">Easy</option>
                            <option value="moderate">Moderate</option>
                            <option value="difficult">Difficult</option>
                        </select>
                        <InputError :message="form.errors.difficulty"/>
                    </div>

                    <div class="col-span-12 grid grid-cols-12 gap-6">
                        <div class="col-span-12 space-y-1">
                            <Label>
                                Ingredients
                            </Label>
                            <textarea
                                v-model="form.ingredients"
                                class="
                                    block w-full
                                    border-gray-300 rounded-md
                                    focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                                    shadow-sm
                                    transition ease-in-out duration-150
                                "
                                required
                                rows="15"
                            />
                            <InputError :message="form.errors.ingredients"/>
                            <p class="text-xs text-gray-500">
                                One ingredient per line. Start a line with a # to create a title and a new section. Add
                                an empty line to create a new section without a title.
                            </p>
                            <p class="text-xs text-gray-500">
                                Ingredients are automatically split in amount (a number), unit (eg. grams, tablespoons,
                                etc.), name (carrot) and info (chopped in dices).
                            </p>
                            <p class="text-xs text-gray-500">
                                You can add a plural name by adding a | after the name and adding the plural name. For
                                example: carrot|carrots.
                            </p>
                            <p class="text-xs text-gray-500">
                                Place info inside parentheses. For example: carrot (chopped in dices).
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-4 py-5 sm:p-6 bg-white shadow sm:rounded-md space-y-2">
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-12 space-y-1">
                        <Label for="instructions" value="Instructions"/>
                        <ckeditor :editor="editor" v-model="form.instructions" :config="instructionsEditorConfig"/>
                        <InputError :message="form.errors.instructions"/>
                    </div>

                    <div class="col-span-12 space-y-1">
                        <Label for="source_label" value="Source label (optional)"/>
                        <Input v-model="form.source_label" class="block w-full" type="text"/>
                        <InputError :message="form.errors.source_label"/>
                    </div>

                    <div class="col-span-12 space-y-1">
                        <Label for="source_link" value="Source link (optional)"/>
                        <Input v-model="form.source_link" class="block w-full" type="text"/>
                        <InputError :message="form.errors.source_link"/>
                    </div>
                </div>
            </div>

            <div class="fixed bottom-0 left-0 w-full px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6">
                <div class="max-w-3xl mx-auto sm:px-6">
                    <Button :disabled="form.processing" class="text-xs" type="submit">
                        Save
                    </Button>
                </div>
            </div>
        </form>
    </DefaultLayout>
</template>
