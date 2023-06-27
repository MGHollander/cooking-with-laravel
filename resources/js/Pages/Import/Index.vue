<script setup>
import {ref} from "vue";
import {Head, useForm} from '@inertiajs/vue3';
import DefaultLayout from '@/Layouts/Default.vue';
import Button from '@/Components/Button.vue';
import Input from '@/Components/Input.vue';
import InputError from '@/Components/InputError.vue';
import Label from '@/Components/Label.vue';

const form = useForm({
    url: '',
});

const title = 'Recept importeren';
let showHelp = ref(false);

const testRecipes = [
    'https://koken.maruc.nl/recepten/gigli-met-kikkererwten-en-zaatar',
    'https://www.ah.nl/allerhande/recept/R-R1197009/pastasalade-met-geroosterde-paprikasaus',
    'https://www.leukerecepten.nl/recepten/noedel-bowl-zalm/',
    'https://www.okokorecepten.nl/recept/groenten/doperwten/doperwten-munt',
    'https://koken.maruc.nl/microdata.html',
    'https://koken.maruc.nl/rdfa.html',
];

// Temporary function for fast testing
function test(event) {
    form.url = event.target.innerText;
    form.get(route('import.create'));
}
</script>

<template>
    <Head :title="title"/>

    <DefaultLayout>
        <template #header>
            {{ title }}
        </template>

        <div class="max-w-3xl mx-auto space-y-8">
            <form class="mb-12 space-y-8" @submit.prevent="form.get(route('import.create'));">
                <div class="bg-white shadow sm:rounded-md">
                    <div class="px-4 py-5 sm:p-6 grid grid-cols-12 gap-6">
                        <div class="col-span-12 space-y-1">
                            <Label for="url" value="URL"/>
                            <Input v-model="form.url" autocomplete="url" class="block w-full" required type="url"/>
                            <InputError :message="form.errors.url"/>
                        </div>

                        <ul class="col-span-12 my-0 font-mono list-none text-sm">
                            <li v-for="url in testRecipes" @click="test"
                                class="cursor-pointer hover:underline overflow-hidden whitespace-nowrap overflow-ellipsis">
                                {{ url }}
                            </li>
                        </ul>
                    </div>

                    <div
                        class="px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6"
                        :class="{'sm:rounded-b': !showHelp}"
                    >
                        <div class="max-w-3xl mx-auto space-x-2 md:space-x-4">
                            <Button :disabled="form.processing" class="text-xs" type="submit">
                                Importeren
                            </Button>

                            <a
                                class="text-sm text-gray-600 hover:text-gray-900 cursor-pointer"
                                @click="showHelp = !showHelp"
                            >
                                Hoe werkt dit?
                            </a>
                        </div>
                    </div>

                    <p v-if="showHelp" class="p-4 border-t border-gray-200 md:rounded-b bg-sky-100 text-sky-700">
                        Bij het importeren wordt er op een webpagina gezocht naar een recept dat is gedefineerd in het
                        <a href="https://schema.org/Recipe" class="text-sky-900">schema.org/Recipe</a> formaat.
                        Er wordt gezocht naar Microdata, RDFa en JSON-LD markups.<br>
                        Als er geen recept wordt gevonden dan wordt er een foutmelding weergegeven.
                    </p>
                </div>
            </form>
        </div>
    </DefaultLayout>
</template>
