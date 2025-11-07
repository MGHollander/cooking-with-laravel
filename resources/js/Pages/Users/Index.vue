<script setup>
import { faEdit, faTrash } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { Head, router } from "@inertiajs/vue3";
import debounce from "lodash/debounce";
import { ref, watch } from "vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import Pagination from "@/Components/Pagination.vue";
import DefaultLayout from "@/Layouts/Default.vue";
import { trans } from "laravel-vue-i18n/*";

let props = defineProps({
  users: Object,
  filters: Object,
});

let search = ref(props.filters.search);

watch(
  search,
  debounce((value) => {
    router.get(
      route("users"),
      { search: value },
      {
        preserveState: true,
        replace: true,
      }
    );
  }, 300)
);

function confirmDeletion(user_id) {
  if (confirm(trans('users.index.confirm_delete'))) {
    router.delete(route("users.destroy", user_id), {
      method: "delete",
    });
  }
}
</script>

<template>
  <Head :title="$t('users.index.title')" />

  <DefaultLayout>
    <template #header>{{ $t('users.index.title') }}</template>

    <div class="mb-4 flex items-center justify-between px-2 sm:px-0">
      <Button
        :href="route('users.create')"
        class="bg-indigo-600 px-2 text-sm no-underline hover:bg-indigo-800 hover:text-white"
      >
        {{ $t('users.index.add_user') }}
      </Button>

      <Input v-model="search" :placeholder="$t('users.index.search')" class="w-full p-2 sm:w-60" />
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

              <td class="space-x-2 whitespace-nowrap px-4 py-5 text-right text-sm font-medium sm:px-6">
                <Button
                  :href="route('users.edit', user.id)"
                  class="text-indigo-600 no-underline hover:text-indigo-900 hover:underline"
                  :aria-label="$t('users.index.edit')"
                >
                  <FontAwesomeIcon :icon="faEdit" />
                </Button>

                <Button button-style="danger" :aria-label="$t('users.index.delete')" @click="confirmDeletion(user.id)">
                  <FontAwesomeIcon :icon="faTrash" />
                </Button>
              </td>
            </tr>
          </table>

          <p v-else class="px-4 py-5 sm:px-6">
            {{ $t('users.index.no_users_found', { search: filters.search }) }}
          </p>
        </div>
      </div>
    </div>

    <div class="mt-6 px-4 py-5 sm:px-6">
      <Pagination :links="users.links" class="flex flex-auto flex-wrap justify-center" />
    </div>
  </DefaultLayout>
</template>
