<script setup>
import {Head, useForm} from '@inertiajs/vue3';

import DefaultLayout from '@/Layouts/Default.vue';
import Button from '@/Components/Button.vue';
import Input from '@/Components/Input.vue';
import InputError from '@/Components/InputError.vue';
import Label from '@/Components/Label.vue';

const props = defineProps(['recipe'])
const edit = route().current('recipes.edit') ?? false;

let form = useForm({
    _method: edit ? 'PATCH' : 'POST',
    title: edit ? props.recipe.title : '',
    slug: edit ? props.recipe.slug : '',
    image: '',
    preparation_minutes: edit ? props.recipe.preparation_minutes : '',
    cooking_minutes: edit ? props.recipe.cooking_minutes : '',
    servings: edit ? props.recipe.servings : '',
    difficulty: edit ? props.recipe.difficulty : 'easy',
    summary: edit ? props.recipe.summary : '',
    instructions: edit ? props.recipe.instructions : '',
    source_label: edit ? props.recipe.source_label : '',
    source_link: edit ? props.recipe.source_link : '',
    ingredients_lists: edit ? props.recipe.ingredients_lists : [{
        title: '',
        ingredients: [{
            name: '',
            amount: '',
            unit: '',
        }],
    }],
})

let submit = () => {
    if (edit) {
        form.post(route('recipes.update', props.recipe.id))
    } else {
        form.post(route('recipes.store'));
    }
}

const addIngredientsList = () => {
    form.ingredients_lists.push({
        title: '',
        ingredients: [{
            name: '',
            amount: '',
            unit: '',
        }],
    })
}

const addIngredient = (list) => {
    list.push({
        name: '',
        amount: '',
        unit: '',
    })
}

const removeIngredientsList = (key) => form.ingredients_lists.splice(key, 1)
const removeIngredient = (list, key) => list.splice(key, 1)
const title = edit ? 'Update Recipe "' + form.title + '"' : 'Create Recipe'
</script>

<template>
    <Head :title="title"/>

    <DefaultLayout>
        <template #header>
            {{ title }}
        </template>

        <form class="max-w-3xl mx-auto mb-12" @submit.prevent="submit">
            <div class="mb-8 px-4 py-5 sm:p-6 sm:pb-8 bg-white shadow sm:rounded-md space-y-2">
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-12 space-y-1">
                        <Label for="title" value="Title"/>
                        <Input v-model="form.title" autocomplete="title" class="block w-full" required type="text"/>
                        <InputError :message="form.errors.title"/>
                    </div>

                    <div class="col-span-12 space-y-1">
                        <Label for="slug" value="Slug"/>
                        <Input v-model="form.slug" class="block w-full" required type="text"/>
                        <InputError :message="form.errors.slug"/>
                    </div>


                    <div class="col-span-12 space-y-1">
                        Show image if there is one and add posibillity to remove it.

                        <Label for="image" value="Image"/>
                        <input accept="image/jpeg,image/png" type="file" @input="form.image = $event.target.files[0]"/>
                        <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                            {{ form.progress.percentage }}%
                        </progress>
                        <InputError :message="form.errors.image"/>
                    </div>

                    <div class="col-span-12 space-y-1">
                        <Label for="summary" value="Summary"/>
                        <textarea
                            v-model="form.summary"
                            class="
                                block w-full
                                border-gray-300 rounded-md
                                focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                                shadow-sm
                                transition ease-in-out duration-150
                            "
                            rows="5"
                        />
                        <InputError :message="form.errors.summary"/>
                    </div>

                    <div class="col-span-12 sm:col-span-4 space-y-1">
                        <Label for="servings" value="Servings"/>
                        <Input v-model="form.servings" class="block w-full" min="1" requireds type="number"/>
                        <InputError :message="form.errors.servings"/>
                    </div>

                    <div class="col-span-12 sm:col-span-4 space-y-1">
                        <Label for="preparation_minutes" value="Preparation in minutes"/>
                        <Input v-model="form.preparation_minutes" class="block w-full" min="1" type="number"/>
                        <InputError :message="form.errors.preparation_minutes"/>
                    </div>

                    <div class="col-span-12 sm:col-span-4 space-y-1">
                        <Label for="cooking_minutes" value="Cooking in minutes"/>
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
                </div>
            </div>

            <div v-for="(list, listKey) in form.ingredients_lists"
                 class="mb-8 px-4 py-5 sm:p-6 sm:pb-8 bg-white shadow sm:rounded-md space-y-6">
                <div class="grid grid-cols-12 gap-3">
                    <div class="col-span-12 space-y-1 mb-3">
                        <Label value="Ingredient list title (optional)"/>
                        <Input v-model="list.title" class="block w-full" type="text"/>
                    </div>

                    <div v-for="(ingredient, ingredientKey) in list.ingredients"
                         class="col-span-12 grid grid-cols-12 gap-6">
                        <div class="col-span-12 sm:col-span-5 space-y-1">
                            <Label value="Ingredient"/>
                            <Input v-model="ingredient.name" class="block w-full" required type="text"/>
                            <InputError
                                :message="form.errors['ingredients_lists.' + listKey + '.ingredients.' + ingredientKey + '.name']"/>
                        </div>

                        <div class="col-span-4 sm:col-span-3 space-y-1">
                            <Label value="Amount"/>
                            <Input v-model="ingredient.amount" class="block w-full" min="0.01" required step="0.01"
                                   type="number"/>
                            <InputError
                                :message="form.errors['ingredients_lists.' + listKey + '.ingredients.' + ingredientKey + '.amount']"/>
                        </div>

                        <div class="col-span-4 sm:col-span-3 space-y-1">
                            <Label>
                                Unit
                                (<span class="sm:hidden">opt</span><span class="hidden sm:inline">optional</span>)
                            </Label>
                            <Input v-model="ingredient.unit" class="block w-full" type="text"/>
                        </div>

                        <div class="col-span-2 sm:col-span-1 pt-6">
                            <Button
                                v-if="list.ingredients.length > 1"
                                class="bg-red-700 border-transparent hover:bg-red-800"
                                type="button"
                                @click="removeIngredient(list.ingredients, ingredientKey)"
                            >
                                -
                            </Button>
                        </div>

                    </div>

                    <div class="col-span-12 space-x-1">
                        <Button
                            class="mt-3 bg-gray-500 hover:bg-gray-700 text-xs"
                            type="button"
                            @click="addIngredient(list.ingredients)"
                        >
                            Add ingredient
                        </Button>
                    </div>
                </div>

                <div class="col-span-12 space-x-1">
                    <Button
                        v-if="listKey === (form.ingredients_lists.length - 1)"
                        class="bg-gray-500 hover:bg-gray-700 text-xs"
                        type="button"
                        @click="addIngredientsList"
                    >
                        Add ingredient list
                    </Button>

                    <Button
                        v-if="form.ingredients_lists.length > 1"
                        class="bg-red-700 hover:bg-red-800 text-xs"
                        type="button"
                        @click="removeIngredientsList(listKey)"
                    >
                        Remove ingredient list
                    </Button>
                </div>
            </div>

            <div class="px-4 py-5 sm:p-6 bg-white shadow sm:rounded-md space-y-2">
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-12 space-y-1">
                        <Label for="instructions" value="Instructions"/>
                        <textarea
                            v-model="form.instructions"
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
