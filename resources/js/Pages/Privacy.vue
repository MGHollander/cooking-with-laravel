<script setup>
import { Head } from "@inertiajs/vue3";
import DefaultLayout from "@/Layouts/Default.vue";
</script>

<template>
  <Head title="Privacy" />

  <DefaultLayout>
    <div class="overflow-hidden bg-white sm:rounded-lg sm:shadow-lg">
      <div class="m-6 space-y-6 md:space-y-10 lg:m-10">
        <div id="ppms_cm_privacy_settings" data-editor-centralize="true" data-main-container="true" data-root="true">
          <div data-disable-select="true">
            <h1 id="ppms_cm_privacy_settings_form_link_header_id" class="mb-4 text-xl font-bold md:text-3xl">
              Privacy
            </h1>
            <p id="ppms_cm_privacy_settings_form_link_text_id" class="mb-4">
              Er worden gegevens verzameld om te analyseren hoe de website wordt gebruikt. Er wordt altijd toestemming
              gevraagd om dit te doen. Je kunt uw privacy-instellingen hier wijzigen.
            </p>
            <button
              id="ppms_cm_privacy_settings_button"
              class="lg:text-md rounded-md border-emerald-700 bg-emerald-700 p-2 text-sm text-white hover:bg-emerald-900 sm:px-4 sm:py-3 md:text-base"
            >
              Instellingen beheren
            </button>
          </div>
        </div>
      </div>
    </div>
  </DefaultLayout>
</template>
