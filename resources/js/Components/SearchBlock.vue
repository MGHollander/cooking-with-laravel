<script setup>
import { useForm, usePage } from "@inertiajs/vue3";

const page = usePage();

const props = defineProps({
  q: String,
  size: String,
});

const form = useForm({
  q: props.q ?? "",
});
</script>

<template>
  <div class="relative flex w-full items-center rounded-lg shadow-lg">
    <img
      class="absolute inset-0 h-full w-full rounded-lg object-cover shadow-lg"
      src="https://picsum.photos/id/999/1000"
    />

    <form
      class="relative flex w-full"
      :class="{ 'p-4 sm:px-16 sm:py-8 md:px-32': size === 'small', 'px-4 py-8 sm:p-16 md:p-32': !size }"
      @submit.prevent="form.get(route(`search.${page.props.locale}`))"
    >
      <input
        v-model="form.q"
        class="w-full rounded-l-md border-2 border-gray-300 bg-white p-2 text-sm sm:px-6 sm:py-4 md:text-base lg:text-lg"
        placeholder="Waar heb je zin in?"
        type="search"
      />
      <button
        type="submit"
        :disabled="form.processing"
        class="rounded-r-md border-2 border-l-0 border-emerald-700 bg-emerald-700 p-2 text-sm text-white sm:px-6 sm:py-4 md:text-base lg:text-lg"
      >
        Zoeken
      </button>
    </form>
  </div>
</template>
