<script setup>
import { Head, router, useForm } from "@inertiajs/vue3";
import { computed, useAttrs } from "vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import DefaultLayout from "@/Layouts/Default.vue";
import { trans } from 'laravel-vue-i18n';

let props = defineProps({
  id: Number,
  name: String,
  email: String,
});

const attrs = useAttrs();

let form = useForm({
  name: props.name,
  email: props.email,
});

let submit = () => {
  form.patch(route("users.update", props.id));
};

const title = computed(() =>
  props.id === attrs.auth.user.id
    ? trans('users.edit.title_self')
    : trans('users.edit.title', { name: props.name })
);

function confirmDeletion() {
  if (confirm(trans('users.edit.confirm_delete'))) {
    router.delete(route("users.destroy", props.id), {
      method: "delete",
    });
  }
}
</script>

<template>
  <Head :title="title" />

  <DefaultLayout>
    <template #header>
      {{ title }}
    </template>

    <form class="mx-auto max-w-2xl" @submit.prevent="submit">
      <div class="space-y-2 bg-white px-4 py-5 shadow sm:rounded-tl-md sm:rounded-tr-md sm:p-6">
        <div class="grid grid-cols-6 gap-6">
          <div class="col-span-6 space-y-1 sm:col-span-4">
            <Label for="name" :value="$t('users.edit.name')" />
            <Input v-model="form.name" type="text" required class="block w-full" autocomplete="name" />
            <InputError :message="form.errors.name" />
          </div>

          <div class="col-span-6 space-y-1 sm:col-span-4">
            <Label for="email" :value="$t('users.edit.email')" />
            <Input v-model="form.email" type="email" required class="block w-full" autocomplete="email" />
            <InputError :message="form.errors.email" />
          </div>
        </div>
      </div>

      <div
        class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-4 py-3 text-right shadow sm:rounded-bl-md sm:rounded-br-md sm:px-6"
      >
        <Button class="text-xs" type="submit" :disabled="form.processing">{{ $t('users.edit.save') }}</Button>

        <Button class="text-xs" button-style="danger" :disabled="form.processing" @click="confirmDeletion()">
          {{ $t('users.edit.delete') }}
        </Button>
      </div>
    </form>
  </DefaultLayout>
</template>
