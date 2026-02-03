<script setup>
import {faChevronDown} from "@fortawesome/free-solid-svg-icons";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {ref} from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Button from "@/Components/Button.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import FlashMessage from "@/Components/FlashMessage.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { useAttrs } from "vue";

const attrs = useAttrs();

const showNav = ref(null);

const toggleNav = (nav) => {
  if (nav === showNav.value) {
    showNav.value = null;
  } else {
    showNav.value = nav;
  }
};
</script>

<template>
  <div>
    <div class="min-h-screen bg-gray-100 relative">
      <nav class="border-b border-gray-100 bg-white">
        <!-- Primary Navigation Menu -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div class="flex h-16 justify-between">
            <div class="flex">
              <!-- Logo -->
              <div class="flex shrink-0 items-center">
                <a :href="route('home')">
                  <ApplicationLogo class="block h-9 w-auto" />
                </a>
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
            <div v-if="attrs.auth.user" class="hidden sm:ml-6 sm:flex sm:items-center">
              <div class="relative ml-3 flex items-center space-x-1">

                <Dropdown align="left" width="48">
                  <template #trigger>
                    <span class="inline-flex rounded-md">
                      <Button button-style="ghost" :aria-label="$t('app.languages.switch_language')" class="!p-2.5">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg" stroke="currentColor" stroke-width="5.12"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M222.35693,161.11682a99.99106,99.99106,0,0,0-.02246-66.2959,3.99577,3.99577,0,0,0-.16308-.46191A100.00019,100.00019,0,0,0,33.83105,94.3512a4.01515,4.01515,0,0,0-.17773.50415,99.99136,99.99136,0,0,0,.03125,66.37927,4.14511,4.14511,0,0,0,.13965.3949,100,100,0,0,0,188.34228.02624A3.96321,3.96321,0,0,0,222.35693,161.11682ZM128,216.03064c-14.43311-13.53882-25.105-31.73706-30.93994-52.03027h61.87988C153.105,184.29358,142.43311,202.49182,128,216.03064ZM95.02979,156.00037a130.90714,130.90714,0,0,1-.00049-56h65.9414a130.90714,130.90714,0,0,1-.00049,56ZM36,128.00037a91.65778,91.65778,0,0,1,4.36182-28H86.76123a143.33386,143.33386,0,0,0,.00049,56H40.36182A91.65787,91.65787,0,0,1,36,128.00037Zm92-88.03028c14.43506,13.53858,25.10693,31.73633,30.94092,52.03028H97.05908C102.89307,71.70642,113.56494,53.50867,128,39.97009Zm41.23877,60.03028h46.39941a92.05165,92.05165,0,0,1,0,56h-46.3999a143.33386,143.33386,0,0,0,.00049-56Zm43.42187-8H167.36426c-5.70655-21.50928-16.53174-40.7439-31.61377-55.67041A92.20548,92.20548,0,0,1,212.66064,92.00037ZM120.24951,36.33c-15.082,14.92651-25.90722,34.16113-31.61377,55.67041H43.33936A92.20548,92.20548,0,0,1,120.24951,36.33ZM43.33936,164.00037H88.63574c5.707,21.50879,16.53174,40.74353,31.61377,55.67041A92.20529,92.20529,0,0,1,43.33936,164.00037Zm92.41113,55.67041c15.082-14.92688,25.90674-34.16162,31.61377-55.67041h45.29638A92.20529,92.20529,0,0,1,135.75049,219.67078Z"></path> </g></svg>                      
                      </Button>
                    </span>
                  </template>

                  <template #content>
                    <DropdownLink :href="route('locale.update')" method="post" :data="{locale: 'en'}" :active="attrs.locale === 'en'" class="flex items-center gap-1" as="button">
                      <svg class="h-4 w-4 mr-2" viewBox="0 -4 28 28" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_503_2952)"><rect width="28" height="20" rx="2" fill="white"/><mask id="mask0_503_2952" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="28" height="20"><rect width="28" height="20" rx="2" fill="white"/></mask><g mask="url(#mask0_503_2952)"><rect width="28" height="20" fill="#0A17A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M-1.28244 -1.91644L10.6667 6.14335V-1.33333H17.3334V6.14335L29.2825 -1.91644L30.7737 0.294324L21.3263 6.66667H28V13.3333H21.3263L30.7737 19.7057L29.2825 21.9165L17.3334 13.8567V21.3333H10.6667V13.8567L-1.28244 21.9165L-2.77362 19.7057L6.67377 13.3333H2.95639e-05V6.66667H6.67377L-2.77362 0.294324L-1.28244 -1.91644Z" fill="white"/><path d="M18.668 6.33219L31.3333 -2" stroke="#DB1F35" stroke-width="0.666667" stroke-linecap="round"/><path d="M20.0128 13.6975L31.3666 21.3503" stroke="#DB1F35" stroke-width="0.666667" stroke-linecap="round"/><path d="M8.00555 6.31046L-3.83746 -1.67099" stroke="#DB1F35" stroke-width="0.666667" stroke-linecap="round"/><path d="M9.29006 13.6049L-3.83746 22.3105" stroke="#DB1F35" stroke-width="0.666667" stroke-linecap="round"/><path fill-rule="evenodd" clip-rule="evenodd" d="M0 12H12V20H16V12H28V8H16V0H12V8H0V12Z" fill="#E6273E"/></g></g><defs><clipPath id="clip0_503_2952"><rect width="28" height="20" rx="2" fill="white"/></clipPath></defs></svg>
                      {{ $t('app.languages.en') }}
                    </DropdownLink>

                    <DropdownLink :href="route('locale.update')" method="post" :data="{locale: 'nl'}" :active="attrs.locale === 'nl'" class="flex items-center gap-1" as="button">
                      <svg class="h-4 w-4 mr-2" viewBox="0 -4 28 28" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_503_2742)"><rect x="0.25" y="0.25" width="27.5" height="19.5" rx="1.75" fill="white" stroke="#F5F5F5" stroke-width="0.5"/><mask id="mask0_503_2742" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="28" height="20"><rect x="0.25" y="0.25" width="27.5" height="19.5" rx="1.75" fill="white" stroke="white" stroke-width="0.5"/></mask><g mask="url(#mask0_503_2742)"><path fill-rule="evenodd" clip-rule="evenodd" d="M0 6.66667H28V0H0V6.66667Z" fill="#CA2B39"/><path fill-rule="evenodd" clip-rule="evenodd" d="M0 20H28V13.3333H0V20Z" fill="#2C56A2"/></g></g><defs><clipPath id="clip0_503_2742"><rect width="28" height="20" rx="2" fill="white"/></clipPath></defs></svg>
                      {{ $t('app.languages.nl') }}
                    </DropdownLink>
                  </template>
                </Dropdown>

                <Dropdown align="left" width="48">
                  <template #trigger>
                    <span class="inline-flex rounded-md">
                      <Button button-style="ghost" :aria-label="$t('nav.add_menu')" class="!p-2.5">
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
                      {{ $t('nav.add_recipe') }}
                    </DropdownLink>

                    <DropdownLink :href="route('import.index')" :active="route().current('import.index')">
                      {{ $t('nav.import_recipe') }}
                    </DropdownLink>
                  </template>
                </Dropdown>

                <Dropdown align="right" width="48">
                  <template #trigger>
                    <span class="inline-flex rounded-md">
                      <Button button-style="ghost" :aria-label="$t('nav.user_menu')" class="!p-2.5 text-sm">
                        {{ attrs.auth.user.name }}

                        <FontAwesomeIcon :icon="faChevronDown" class="-mt-0.5 ml-2 h-3" />
                      </Button>
                    </span>
                  </template>

                  <template #content>
                    <DropdownLink
                      :href="route(`users.edit.${attrs.locale}`, attrs.auth.user.id)"
                      :active="route().current(`users.edit.${attrs.locale}`, { id: attrs.auth.user.id })"
                    >
                      {{ $t('nav.edit_profile') }}
                    </DropdownLink>

                    <DropdownLink
                      :href="route(`users.password.edit.${attrs.locale}`)"
                      :active="route().current(`users.password.edit.${attrs.locale}`)"
                    >
                      {{ $t('nav.change_password') }}
                    </DropdownLink>

                    <DropdownLink
                      :href="route(`users.index.${attrs.locale}`)"
                      :active="route().current(`users.index.${attrs.locale}`)"
                    >
                      {{ $t('nav.manage_users') }}
                    </DropdownLink>

                    <DropdownLink :href="route(`logout.${attrs.locale}`)" method="post" as="button">{{ $t('nav.logout') }}</DropdownLink>
                  </template>
                </Dropdown>
              </div>
            </div>

            <!-- Menu for guests -->
            <div v-else class="flex space-x-1">
              <NavLink :href="route(`login.${attrs.locale}`)" :active="route().current('login')">{{ $t('nav.login') }}</NavLink>
            </div>

            <!-- Responsive Menu -->
            <div v-if="attrs.auth.user" class="-mr-2 flex items-center space-x-1 sm:hidden">
              <button
                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500"
                :aria-label="$t('app.languages.switch_language')"
                @click="toggleNav('language')"
              >
                  <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg" stroke="currentColor" stroke-width="5.12"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M222.35693,161.11682a99.99106,99.99106,0,0,0-.02246-66.2959,3.99577,3.99577,0,0,0-.16308-.46191A100.00019,100.00019,0,0,0,33.83105,94.3512a4.01515,4.01515,0,0,0-.17773.50415,99.99136,99.99136,0,0,0,.03125,66.37927,4.14511,4.14511,0,0,0,.13965.3949,100,100,0,0,0,188.34228.02624A3.96321,3.96321,0,0,0,222.35693,161.11682ZM128,216.03064c-14.43311-13.53882-25.105-31.73706-30.93994-52.03027h61.87988C153.105,184.29358,142.43311,202.49182,128,216.03064ZM95.02979,156.00037a130.90714,130.90714,0,0,1-.00049-56h65.9414a130.90714,130.90714,0,0,1-.00049,56ZM36,128.00037a91.65778,91.65778,0,0,1,4.36182-28H86.76123a143.33386,143.33386,0,0,0,.00049,56H40.36182A91.65787,91.65787,0,0,1,36,128.00037Zm92-88.03028c14.43506,13.53858,25.10693,31.73633,30.94092,52.03028H97.05908C102.89307,71.70642,113.56494,53.50867,128,39.97009Zm41.23877,60.03028h46.39941a92.05165,92.05165,0,0,1,0,56h-46.3999a143.33386,143.33386,0,0,0,.00049-56Zm43.42187-8H167.36426c-5.70655-21.50928-16.53174-40.7439-31.61377-55.67041A92.20548,92.20548,0,0,1,212.66064,92.00037ZM120.24951,36.33c-15.082,14.92651-25.90722,34.16113-31.61377,55.67041H43.33936A92.20548,92.20548,0,0,1,120.24951,36.33ZM43.33936,164.00037H88.63574c5.707,21.50879,16.53174,40.74353,31.61377,55.67041A92.20529,92.20529,0,0,1,43.33936,164.00037Zm92.41113,55.67041c15.082-14.92688,25.90674-34.16162,31.61377-55.67041h45.29638A92.20529,92.20529,0,0,1,135.75049,219.67078Z"></path> </g></svg>                      
              </button>

              <button
                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500"
                :aria-label="$t('nav.add_recipe_mobile')"
                @click="toggleNav('create')"
              >
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12L20 12 M12 4l0 16" />
                </svg>
              </button>

              <button
                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500"
                :aria-label="$t('nav.user_menu_mobile')"
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

        <!-- Responsive Menus -->
        <div v-if="attrs.auth.user" class="sm:hidden">
          <!--
            <div class="pt-2 pb-3 space-y-1">
                <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                    Dashboard
                </ResponsiveNavLink>

                <ResponsiveNavLink :href="route('users')" :active="route().current('users')">
                    Users
                </ResponsiveNavLink>
            </div>
          -->

          <!-- Responsive Language Menu -->
          <div v-if="showNav === 'language'" class="border-t border-gray-200 py-1">
            <div class="space-y-1">
              <ResponsiveNavLink :href="route('locale.update')" method="post" :data="{locale: 'en'}" :active="attrs.locale === 'en'" as="button" class="flex items-center gap-1 w-full">
                <svg class="h-4 w-4 mr-2" viewBox="0 -4 28 28" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_503_2952)"><rect width="28" height="20" rx="2" fill="white"/><mask id="mask0_503_2952" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="28" height="20"><rect width="28" height="20" rx="2" fill="white"/></mask><g mask="url(#mask0_503_2952)"><rect width="28" height="20" fill="#0A17A7"/><path fill-rule="evenodd" clip-rule="evenodd" d="M-1.28244 -1.91644L10.6667 6.14335V-1.33333H17.3334V6.14335L29.2825 -1.91644L30.7737 0.294324L21.3263 6.66667H28V13.3333H21.3263L30.7737 19.7057L29.2825 21.9165L17.3334 13.8567V21.3333H10.6667V13.8567L-1.28244 21.9165L-2.77362 19.7057L6.67377 13.3333H2.95639e-05V6.66667H6.67377L-2.77362 0.294324L-1.28244 -1.91644Z" fill="white"/><path d="M18.668 6.33219L31.3333 -2" stroke="#DB1F35" stroke-width="0.666667" stroke-linecap="round"/><path d="M20.0128 13.6975L31.3666 21.3503" stroke="#DB1F35" stroke-width="0.666667" stroke-linecap="round"/><path d="M8.00555 6.31046L-3.83746 -1.67099" stroke="#DB1F35" stroke-width="0.666667" stroke-linecap="round"/><path d="M9.29006 13.6049L-3.83746 22.3105" stroke="#DB1F35" stroke-width="0.666667" stroke-linecap="round"/><path fill-rule="evenodd" clip-rule="evenodd" d="M0 12H12V20H16V12H28V8H16V0H12V8H0V12Z" fill="#E6273E"/></g></g><defs><clipPath id="clip0_503_2952"><rect width="28" height="20" rx="2" fill="white"/></clipPath></defs></svg>
                {{ $t('app.languages.en') }}
              </ResponsiveNavLink>

              <ResponsiveNavLink :href="route('locale.update')" method="post" :data="{locale: 'nl'}" :active="attrs.locale === 'nl'" as="button" class="flex items-center gap-1 w-full">
                <svg class="h-4 w-4 mr-2" viewBox="0 -4 28 28" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_503_2742)"><rect x="0.25" y="0.25" width="27.5" height="19.5" rx="1.75" fill="white" stroke="#F5F5F5" stroke-width="0.5"/><mask id="mask0_503_2742" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="28" height="20"><rect x="0.25" y="0.25" width="27.5" height="19.5" rx="1.75" fill="white" stroke="white" stroke-width="0.5"/></mask><g mask="url(#mask0_503_2742)"><path fill-rule="evenodd" clip-rule="evenodd" d="M0 6.66667H28V0H0V6.66667Z" fill="#CA2B39"/><path fill-rule="evenodd" clip-rule="evenodd" d="M0 20H28V13.3333H0V20Z" fill="#2C56A2"/></g></g><defs><clipPath id="clip0_503_2742"><rect width="28" height="20" rx="2" fill="white"/></clipPath></defs></svg>
                {{ $t('app.languages.nl') }}
              </ResponsiveNavLink>
            </div>
          </div>

          <!-- Responsive Create Menu -->
          <div v-if="showNav === 'create'" class="border-t border-gray-200 py-1">
            <div class="space-y-1">
              <ResponsiveNavLink :href="route('recipes.create')" :active="route().current('recipes.create')">
                {{ $t('nav.add_recipe') }}
              </ResponsiveNavLink>

              <ResponsiveNavLink :href="route('import.index')" :active="route().current('import.index')">
                {{ $t('nav.import_recipe') }}
              </ResponsiveNavLink>
            </div>
          </div>

          <!-- Responsive User Menu -->
          <div v-if="showNav === 'user'">
            <div class="border-t border-gray-200 p-4">
              <div class="text-base font-medium text-gray-800">{{ attrs.auth.user.name }}</div>
              <div class="text-sm font-medium text-gray-500">{{ attrs.auth.user.email }}</div>
            </div>

            <div class="space-y-1 border-t border-gray-200 py-1">
              <ResponsiveNavLink
                :href="route(`users.edit.${attrs.locale}`, attrs.auth.user.id)"
                :active="route().current(`users.edit.${attrs.locale}`, { id: attrs.auth.user.id })"
              >
                {{ $t('nav.edit_profile') }}
              </ResponsiveNavLink>

              <ResponsiveNavLink
                :href="route(`users.password.edit.${attrs.locale}`)"
                :active="route().current(`users.password.edit.${attrs.locale}`)"
              >
                {{ $t('nav.change_password') }}
              </ResponsiveNavLink>

              <ResponsiveNavLink
                :href="route(`users.index.${attrs.locale}`)"
                :active="route().current(`users.index.${attrs.locale}`)"
              >
                {{ $t('nav.manage_users') }}
              </ResponsiveNavLink>

              <ResponsiveNavLink :href="route(`logout.${attrs.locale}`)" method="post" as="button" class="w-full text-left">
                {{ $t('nav.logout') }}
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

      <FlashMessage
        v-if="attrs.flash.error"
        type="error"
        :message="attrs.flash.error"
      />

      <FlashMessage
        v-if="attrs.flash.success"
        type="success"
        :message="attrs.flash.success"
      />

      <FlashMessage
        v-if="attrs.flash.warning"
        type="warning"
        :message="attrs.flash.warning"
      />

      <!-- Page Content -->
      <main>
        <div class="pt-6 pb-20 sm:pt-12">
          <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <slot />
          </div>
        </div>
      </main>

      <footer class="absolute bottom-0 w-full">
        <div class="px-4 pb-4">
          <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex items-center justify-between text-gray-600">
              <p v-html="$t('app.copyright')"/>
              <a :href="route('privacy')">{{ $t('nav.privacy') }}</a>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </div>
</template>
