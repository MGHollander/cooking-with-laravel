<script setup>
import { Link, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Button from "@/Components/Button.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import Input from "@/Components/Input.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";

const showNav = ref(null);
const toggleNav = (nav) => {
  if (nav === showNav.value) {
    showNav.value = null;
  } else {
    showNav.value = nav;
  }
};

const form = useForm({
  q: "",
});
</script>

<template>
  <div>
    <div class="min-h-screen bg-gray-100">
      <nav class="border-b border-gray-100 bg-white">
        <!-- Primary Navigation Menu -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div class="flex h-16 justify-between">
            <div class="flex">
              <!-- Logo -->
              <div class="flex shrink-0 items-center">
                <Link :href="route('home')">
                  <ApplicationLogo class="block h-9 w-auto" />
                </Link>
              </div>

              <!-- Navigation Links -->
              <!-- Hidden for future use
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    Dashboard
                                </NavLink>

                                <NavLink :href="route('users')" :active="route().current('users')">
                                    Users
                                </NavLink>
                            </div>
                            -->
            </div>

            <!-- Menu for users -->
            <div v-if="$page.props.auth.user" class="hidden sm:ml-6 sm:flex sm:items-center">
              <div class="relative ml-3 flex items-center space-x-1">
                <Dropdown align="left" width="48">
                  <template #trigger>
                    <span class="inline-flex rounded-md">
                      <Button button-style="ghost" aria-label="Uitklapmenu om recepten toe te voegen" class="!p-2.5">
                        <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 12L20 12 M12 4l0 16"
                          />
                        </svg>
                      </Button>
                    </span>
                  </template>

                  <template #content>
                    <DropdownLink :href="route('recipes.create')" :active="route().current('recipes.create')">
                      Voeg een recept toe
                    </DropdownLink>

                    <DropdownLink :href="route('import.index')" :active="route().current('import.index')">
                      Importeer een recept
                    </DropdownLink>
                  </template>
                </Dropdown>

                <Button button-style="ghost" aria-label="Zoek een recept" class="!p-2.5" @click="toggleNav('search')">
                  <svg class="h-5 w-5" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <path
                        d="M5.40705882,9.76579186 L1.13049774,14.0423529 L1.95800905,14.8691403 L6.23384615,10.5925792 C5.93199325,10.3445547 5.65508327,10.0676448 5.40705882,9.76579186 L5.40705882,9.76579186 Z M10.0180995,1.21013575 C7.38262671,1.21013575 5.24615385,3.34660861 5.24615385,5.98208145 C5.24615385,8.61755429 7.38262671,10.7540271 10.0180995,10.7540271 C12.6534724,10.7540271 14.7898643,8.61763532 14.7898643,5.98226244 C14.7898643,3.34688957 12.6534724,1.21049774 10.0180995,1.21049774 L10.0180995,1.21013575 Z M6.91113122,5.46932127 C6.65303181,5.46932127 6.4438009,5.26009036 6.4438009,5.00199095 C6.4438009,4.74389154 6.65303181,4.53466063 6.91113122,4.53466063 C7.16923063,4.53466063 7.37846154,4.74389154 7.37846154,5.00199095 C7.37846154,5.12593466 7.32922509,5.24480195 7.24158366,5.33244339 C7.15394222,5.42008482 7.03507493,5.46932127 6.91113122,5.46932127 Z M7.94027149,4.33628959 C7.48843702,4.33549024 7.12272582,3.96869932 7.12325849,3.51686445 C7.12379115,3.06502958 7.49036615,2.69910196 7.94220126,2.69936794 C8.39403636,2.69963392 8.76018029,3.06599287 8.760181,3.51782805 C8.76018134,3.73514866 8.67375208,3.94354729 8.51994746,4.09708029 C8.36614283,4.2506133 8.15759176,4.33667406 7.94027149,4.33628959 Z M9.59276018,3.21158371 C9.3345954,3.21018605 9.12631234,3.00001615 9.12724286,2.74184926 C9.12817338,2.48368237 9.33796605,2.27501936 9.59613419,2.27548273 C9.85430234,2.27594609 10.0633446,2.48536085 10.0633484,2.74352941 C10.0633502,2.86810541 10.0136898,2.98754248 9.92536382,3.07539289 C9.83703781,3.16324331 9.71733436,3.21225814 9.59276018,3.21158371 L9.59276018,3.21158371 Z"
                      />
                      <path
                        d="M10.0180995,0.0170135747 C7.91074463,0.0175014928 5.95997609,1.12971952 4.88615078,2.94296087 C3.81232547,4.75620223 3.7747616,7.00144515 4.78733032,8.84959276 C4.63937697,8.9190674 4.50463812,9.01375207 4.38914027,9.12941176 L0.430769231,13.0888688 C-0.12045472,13.6401793 -0.12045472,14.5339384 0.430769231,15.0852489 L0.914751131,15.5692308 C1.1795314,15.8341616 1.53874087,15.9830108 1.91330317,15.9830108 C2.28786547,15.9830108 2.64707494,15.8341616 2.9118552,15.5692308 L6.86950226,11.6112217 C6.98533647,11.495791 7.08015077,11.361042 7.14968326,11.2130317 C9.3343265,12.4100266 12.0327729,12.1233591 13.9171704,10.4940926 C15.8015678,8.86482619 16.4750474,6.23609712 15.606203,3.90145101 C14.7373587,1.56680491 12.509176,0.0179366079 10.0180995,0.0170135747 L10.0180995,0.0170135747 Z M1.95800905,14.8691403 L1.13049774,14.0423529 L5.40705882,9.76579186 C5.65508327,10.0676448 5.93199325,10.3445547 6.23384615,10.5925792 L1.95800905,14.8691403 Z M10.0180995,10.7536652 C7.38272668,10.7538651 5.2461728,8.61763532 5.24597288,5.98226245 C5.24577296,3.34688959 7.38200271,1.2103357 10.0173756,1.21013577 C12.6527484,1.20993585 14.7893023,3.3461656 14.7895023,5.98153846 C14.7897904,7.24736947 14.2870686,8.46143806 13.3919909,9.35651577 C12.4969132,10.2515935 11.2828446,10.7543153 10.0170136,10.7540271 L10.0180995,10.7536652 Z"
                        fill="currentColor"
                        fill-rule="nonzero"
                      />
                      <circle fill="currentColor" fill-rule="nonzero" cx="7.94027149" cy="3.51782805" r="1" />
                      <circle fill="currentColor" fill-rule="nonzero" cx="9.59457014" cy="2.74352941" r="1" />
                      <circle fill="currentColor" fill-rule="nonzero" cx="6.91113122" cy="5.00199095" r="1" />
                    </g>
                  </svg>
                </Button>

                <Dropdown align="right" width="48">
                  <template #trigger>
                    <span class="inline-flex rounded-md">
                      <Button button-style="ghost" aria-label="Open het gebruikersmenu" class="!p-2.5 text-sm">
                        {{ $page.props.auth.user.name }}

                        <svg
                          class="-mr-0.5 ml-2 h-4 w-4"
                          xmlns="http://www.w3.org/2000/svg"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                          />
                        </svg>
                      </Button>
                    </span>
                  </template>

                  <template #content>
                    <DropdownLink
                      :href="route('users.edit', $page.props.auth.user.id)"
                      :active="route().current('users.edit', { id: $page.props.auth.user.id })"
                    >
                      Bewerk je profiel
                    </DropdownLink>

                    <DropdownLink :href="route('users.password.edit')" :active="route().current('users.password.edit')">
                      Wijzig je wachtwoord
                    </DropdownLink>

                    <DropdownLink :href="route('users.index')" :active="route().current('users.index')">
                      Beheer gebruikers
                    </DropdownLink>

                    <DropdownLink :href="route('logout')" method="post" as="button"> Uitloggen</DropdownLink>
                  </template>
                </Dropdown>
              </div>
            </div>

            <!-- Menu for guests -->
            <div v-else class="flex space-x-1">
              <NavLink :href="route('login')" :active="route().current('login')"> Inloggen</NavLink>

              <div class="flex items-center">
                <button
                  class="inline-flex items-center justify-center rounded-md p-2.5 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500"
                  aria-label="Zoek een recept"
                  @click="toggleNav('search')"
                >
                  <svg class="h-5 w-5" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <path
                        d="M5.40705882,9.76579186 L1.13049774,14.0423529 L1.95800905,14.8691403 L6.23384615,10.5925792 C5.93199325,10.3445547 5.65508327,10.0676448 5.40705882,9.76579186 L5.40705882,9.76579186 Z M10.0180995,1.21013575 C7.38262671,1.21013575 5.24615385,3.34660861 5.24615385,5.98208145 C5.24615385,8.61755429 7.38262671,10.7540271 10.0180995,10.7540271 C12.6534724,10.7540271 14.7898643,8.61763532 14.7898643,5.98226244 C14.7898643,3.34688957 12.6534724,1.21049774 10.0180995,1.21049774 L10.0180995,1.21013575 Z M6.91113122,5.46932127 C6.65303181,5.46932127 6.4438009,5.26009036 6.4438009,5.00199095 C6.4438009,4.74389154 6.65303181,4.53466063 6.91113122,4.53466063 C7.16923063,4.53466063 7.37846154,4.74389154 7.37846154,5.00199095 C7.37846154,5.12593466 7.32922509,5.24480195 7.24158366,5.33244339 C7.15394222,5.42008482 7.03507493,5.46932127 6.91113122,5.46932127 Z M7.94027149,4.33628959 C7.48843702,4.33549024 7.12272582,3.96869932 7.12325849,3.51686445 C7.12379115,3.06502958 7.49036615,2.69910196 7.94220126,2.69936794 C8.39403636,2.69963392 8.76018029,3.06599287 8.760181,3.51782805 C8.76018134,3.73514866 8.67375208,3.94354729 8.51994746,4.09708029 C8.36614283,4.2506133 8.15759176,4.33667406 7.94027149,4.33628959 Z M9.59276018,3.21158371 C9.3345954,3.21018605 9.12631234,3.00001615 9.12724286,2.74184926 C9.12817338,2.48368237 9.33796605,2.27501936 9.59613419,2.27548273 C9.85430234,2.27594609 10.0633446,2.48536085 10.0633484,2.74352941 C10.0633502,2.86810541 10.0136898,2.98754248 9.92536382,3.07539289 C9.83703781,3.16324331 9.71733436,3.21225814 9.59276018,3.21158371 L9.59276018,3.21158371 Z"
                      ></path>
                      <path
                        d="M10.0180995,0.0170135747 C7.91074463,0.0175014928 5.95997609,1.12971952 4.88615078,2.94296087 C3.81232547,4.75620223 3.7747616,7.00144515 4.78733032,8.84959276 C4.63937697,8.9190674 4.50463812,9.01375207 4.38914027,9.12941176 L0.430769231,13.0888688 C-0.12045472,13.6401793 -0.12045472,14.5339384 0.430769231,15.0852489 L0.914751131,15.5692308 C1.1795314,15.8341616 1.53874087,15.9830108 1.91330317,15.9830108 C2.28786547,15.9830108 2.64707494,15.8341616 2.9118552,15.5692308 L6.86950226,11.6112217 C6.98533647,11.495791 7.08015077,11.361042 7.14968326,11.2130317 C9.3343265,12.4100266 12.0327729,12.1233591 13.9171704,10.4940926 C15.8015678,8.86482619 16.4750474,6.23609712 15.606203,3.90145101 C14.7373587,1.56680491 12.509176,0.0179366079 10.0180995,0.0170135747 L10.0180995,0.0170135747 Z M1.95800905,14.8691403 L1.13049774,14.0423529 L5.40705882,9.76579186 C5.65508327,10.0676448 5.93199325,10.3445547 6.23384615,10.5925792 L1.95800905,14.8691403 Z M10.0180995,10.7536652 C7.38272668,10.7538651 5.2461728,8.61763532 5.24597288,5.98226245 C5.24577296,3.34688959 7.38200271,1.2103357 10.0173756,1.21013577 C12.6527484,1.20993585 14.7893023,3.3461656 14.7895023,5.98153846 C14.7897904,7.24736947 14.2870686,8.46143806 13.3919909,9.35651577 C12.4969132,10.2515935 11.2828446,10.7543153 10.0170136,10.7540271 L10.0180995,10.7536652 Z"
                        fill="currentColor"
                        fill-rule="nonzero"
                      ></path>
                      <circle fill="currentColor" fill-rule="nonzero" cx="7.94027149" cy="3.51782805" r="1"></circle>
                      <circle fill="currentColor" fill-rule="nonzero" cx="9.59457014" cy="2.74352941" r="1"></circle>
                      <circle fill="currentColor" fill-rule="nonzero" cx="6.91113122" cy="5.00199095" r="1"></circle>
                    </g>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Responsive Menu -->
            <div v-if="$page.props.auth.user" class="-mr-2 flex items-center space-x-1 sm:hidden">
              <button
                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500"
                aria-label="Voeg een recept toe"
                @click="toggleNav('create')"
              >
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12L20 12 M12 4l0 16" />
                </svg>
              </button>

              <button
                class="inline-flex items-center justify-center rounded-md p-2.5 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500"
                aria-label="Zoek een recept"
                @click="toggleNav('search')"
              >
                <svg class="h-5 w-5" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <path
                      d="M5.40705882,9.76579186 L1.13049774,14.0423529 L1.95800905,14.8691403 L6.23384615,10.5925792 C5.93199325,10.3445547 5.65508327,10.0676448 5.40705882,9.76579186 L5.40705882,9.76579186 Z M10.0180995,1.21013575 C7.38262671,1.21013575 5.24615385,3.34660861 5.24615385,5.98208145 C5.24615385,8.61755429 7.38262671,10.7540271 10.0180995,10.7540271 C12.6534724,10.7540271 14.7898643,8.61763532 14.7898643,5.98226244 C14.7898643,3.34688957 12.6534724,1.21049774 10.0180995,1.21049774 L10.0180995,1.21013575 Z M6.91113122,5.46932127 C6.65303181,5.46932127 6.4438009,5.26009036 6.4438009,5.00199095 C6.4438009,4.74389154 6.65303181,4.53466063 6.91113122,4.53466063 C7.16923063,4.53466063 7.37846154,4.74389154 7.37846154,5.00199095 C7.37846154,5.12593466 7.32922509,5.24480195 7.24158366,5.33244339 C7.15394222,5.42008482 7.03507493,5.46932127 6.91113122,5.46932127 Z M7.94027149,4.33628959 C7.48843702,4.33549024 7.12272582,3.96869932 7.12325849,3.51686445 C7.12379115,3.06502958 7.49036615,2.69910196 7.94220126,2.69936794 C8.39403636,2.69963392 8.76018029,3.06599287 8.760181,3.51782805 C8.76018134,3.73514866 8.67375208,3.94354729 8.51994746,4.09708029 C8.36614283,4.2506133 8.15759176,4.33667406 7.94027149,4.33628959 Z M9.59276018,3.21158371 C9.3345954,3.21018605 9.12631234,3.00001615 9.12724286,2.74184926 C9.12817338,2.48368237 9.33796605,2.27501936 9.59613419,2.27548273 C9.85430234,2.27594609 10.0633446,2.48536085 10.0633484,2.74352941 C10.0633502,2.86810541 10.0136898,2.98754248 9.92536382,3.07539289 C9.83703781,3.16324331 9.71733436,3.21225814 9.59276018,3.21158371 L9.59276018,3.21158371 Z"
                    ></path>
                    <path
                      d="M10.0180995,0.0170135747 C7.91074463,0.0175014928 5.95997609,1.12971952 4.88615078,2.94296087 C3.81232547,4.75620223 3.7747616,7.00144515 4.78733032,8.84959276 C4.63937697,8.9190674 4.50463812,9.01375207 4.38914027,9.12941176 L0.430769231,13.0888688 C-0.12045472,13.6401793 -0.12045472,14.5339384 0.430769231,15.0852489 L0.914751131,15.5692308 C1.1795314,15.8341616 1.53874087,15.9830108 1.91330317,15.9830108 C2.28786547,15.9830108 2.64707494,15.8341616 2.9118552,15.5692308 L6.86950226,11.6112217 C6.98533647,11.495791 7.08015077,11.361042 7.14968326,11.2130317 C9.3343265,12.4100266 12.0327729,12.1233591 13.9171704,10.4940926 C15.8015678,8.86482619 16.4750474,6.23609712 15.606203,3.90145101 C14.7373587,1.56680491 12.509176,0.0179366079 10.0180995,0.0170135747 L10.0180995,0.0170135747 Z M1.95800905,14.8691403 L1.13049774,14.0423529 L5.40705882,9.76579186 C5.65508327,10.0676448 5.93199325,10.3445547 6.23384615,10.5925792 L1.95800905,14.8691403 Z M10.0180995,10.7536652 C7.38272668,10.7538651 5.2461728,8.61763532 5.24597288,5.98226245 C5.24577296,3.34688959 7.38200271,1.2103357 10.0173756,1.21013577 C12.6527484,1.20993585 14.7893023,3.3461656 14.7895023,5.98153846 C14.7897904,7.24736947 14.2870686,8.46143806 13.3919909,9.35651577 C12.4969132,10.2515935 11.2828446,10.7543153 10.0170136,10.7540271 L10.0180995,10.7536652 Z"
                      fill="currentColor"
                      fill-rule="nonzero"
                    ></path>
                    <circle fill="currentColor" fill-rule="nonzero" cx="7.94027149" cy="3.51782805" r="1"></circle>
                    <circle fill="currentColor" fill-rule="nonzero" cx="9.59457014" cy="2.74352941" r="1"></circle>
                    <circle fill="currentColor" fill-rule="nonzero" cx="6.91113122" cy="5.00199095" r="1"></circle>
                  </g>
                </svg>
              </button>

              <button
                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500"
                aria-label="Gebruikersmenu"
                @click="toggleNav('user')"
              >
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                  <path
                    v-if="showNav !== 'user'"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16 M4 12h16 M4 18h16"
                  />
                  <path
                    v-if="showNav === 'user'"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Search bar -->
        <form
          v-if="showNav === 'search'"
          class="flex w-full border-t border-gray-200 p-4"
          @submit.prevent="form.get(route('search'))"
        >
          <input
            v-model="form.q"
            class="w-full rounded-l-md border-2 border-gray-300 bg-white p-2 text-sm sm:px-3 sm:py-2 md:text-base"
            placeholder="Waar heb je zin in?"
            type="search"
          />
          <button
            type="submit"
            :disabled="form.processing"
            class="rounded-r-md border-2 border-l-0 border-emerald-700 bg-emerald-700 p-2 text-sm text-white sm:px-3 sm:py-2 md:text-base"
          >
            Zoeken
          </button>
        </form>

        <!-- Responsive Menus -->
        <div v-if="$page.props.auth.user" class="sm:hidden">
          <!-- <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </ResponsiveNavLink>

                        <ResponsiveNavLink :href="route('users')" :active="route().current('users')">
                            Users
                        </ResponsiveNavLink>
                    </div> -->

          <!-- Responsive Create Menu -->
          <div v-if="showNav === 'create'" class="border-t border-gray-200 py-1">
            <div class="space-y-1">
              <ResponsiveNavLink :href="route('recipes.create')" :active="route().current('recipes.create')">
                Recept toevoegen
              </ResponsiveNavLink>

              <ResponsiveNavLink :href="route('import.index')" :active="route().current('import.index')">
                Recept importeren
              </ResponsiveNavLink>
            </div>
          </div>

          <!-- Responsive User Menu -->
          <div v-if="showNav === 'user'">
            <div class="border-t border-gray-200 p-4">
              <div class="text-base font-medium text-gray-800">{{ $page.props.auth.user.name }}</div>
              <div class="text-sm font-medium text-gray-500">{{ $page.props.auth.user.email }}</div>
            </div>

            <div class="space-y-1 border-t border-gray-200 py-1">
              <ResponsiveNavLink
                :href="route('users.edit', $page.props.auth.user.id)"
                :active="route().current('users.edit', { id: $page.props.auth.user.id })"
              >
                Bewerk je profiel
              </ResponsiveNavLink>

              <ResponsiveNavLink :href="route('users.password.edit')" :active="route().current('users.password.edit')">
                Wijzig je wachtwoord
              </ResponsiveNavLink>

              <ResponsiveNavLink :href="route('users.index')" :active="route().current('users.index')">
                Beheer gebruikers
              </ResponsiveNavLink>

              <ResponsiveNavLink :href="route('logout')" method="post" as="button" class="w-full text-left">
                Uitloggen
              </ResponsiveNavLink>
            </div>
          </div>
        </div>
      </nav>

      <!-- Page Heading -->
      <header v-if="$slots.header" class="bg-white shadow">
        <div class="mx-auto flex max-w-7xl justify-between px-4 py-6 sm:px-6 lg:px-8">
          <h2 class="text-xl font-semibold leading-tight text-gray-800">
            <slot name="header" />
          </h2>
        </div>
      </header>

      <div v-if="$page.props.flash.error" class="flex justify-between rounded-lg bg-red-100 py-4 text-red-700">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
          <p v-html="$page.props.flash.error" />
        </div>
      </div>

      <div
        v-if="$page.props.flash.success"
        class="flex justify-between rounded-lg bg-emerald-100 py-4 text-emerald-800"
      >
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
          <p v-html="$page.props.flash.success" />
        </div>
      </div>

      <div v-if="$page.props.flash.warning" class="flex justify-between rounded-lg bg-yellow-100 py-4 text-yellow-700">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
          <p v-html="$page.props.flash.warning" />
        </div>
      </div>

      <!-- Page Content -->
      <main>
        <div class="py-6 sm:py-12">
          <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <slot />
          </div>
        </div>
      </main>
    </div>
  </div>
</template>
