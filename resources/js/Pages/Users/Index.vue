<script setup>
import { router } from "@inertiajs/vue3";
import { Head, Link } from "@inertiajs/vue3";
import debounce from "lodash/debounce";
import { ref, watch } from "vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import Pagination from "@/Components/Pagination.vue";
import DefaultLayout from "@/Layouts/Default.vue";

let props = defineProps({
  users: Object,
  filters: Object,
});

let search = ref(props.filters.search);

watch(
  search,
  debounce((value) => {
    Inertia.get(
      route("users"),
      { search: value },
      {
        preserveState: true,
        replace: true,
      }
    );
  }, 300)
);
</script>

<template>
  <Head title="Gebruikers beheren" />

  <DefaultLayout>
    <template #header> Gebruikers beheren </template>

    <div class="mb-4 flex items-center justify-between px-2 sm:px-0">
      <Button
        :href="route('users.create')"
        class="bg-indigo-600 px-2 text-sm text-sm no-underline hover:bg-indigo-800 hover:text-white"
      >
        Gebruiker toevoegen
      </Button>

      <Input v-model="search" placeholder="Search..." class="w-full p-2 sm:w-60" />
    </div>

    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
      <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
        <div class="overflow-hidden border-b border-gray-200 bg-white shadow sm:rounded-md">
          <table v-if="users.data.length > 0" class="min-w-full divide-y divide-gray-200">
            <tr v-for="user in users.data" :key="user.id">
              <td class="whitespace-nowrap px-4 py-5 leading-tight sm:px-6">
                {{ user.name }}<br />
                <span class="text-sm text-gray-500">{{ user.email }}</span>
              </td>

              <td class="whitespace-nowrap px-4 py-5 text-right text-sm font-medium sm:px-6">
                <Link
                  :href="route('users.edit', user.id)"
                  class="text-indigo-600 no-underline hover:text-indigo-900 hover:underline"
                >
                  Bewerk
                </Link>
              </td>
            </tr>
          </table>

          <p v-else class="px-4 py-5 sm:px-6">
            Geen gebruiker gevonden met <strong>{{ filters.search }}</strong> in de naam.
          </p>
        </div>
      </div>
    </div>

    <div class="mt-6 px-4 py-5 sm:px-6">
      <Pagination :links="users.links" class="flex flex-auto flex-wrap justify-center" />
    </div>
  </DefaultLayout>
</template>
