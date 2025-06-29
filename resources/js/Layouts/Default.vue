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
    <div class="min-h-screen bg-gray-100">
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

                <Dropdown align="right" width="48">
                  <template #trigger>
                    <span class="inline-flex rounded-md">
                      <Button button-style="ghost" aria-label="Open het gebruikersmenu" class="!p-2.5 text-sm">
                        {{ $page.props.auth.user.name }}

                        <FontAwesomeIcon :icon="faChevronDown" class="-mt-0.5 ml-2 h-3" />
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

        <!-- Responsive Menus -->
        <div v-if="$page.props.auth.user" class="sm:hidden">
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

      <FlashMessage
        v-if="$page.props.flash.error"
        type="error"
        :message="$page.props.flash.error"
      />

      <FlashMessage
        v-if="$page.props.flash.success"
        type="success"
        :message="$page.props.flash.success"
      />

      <FlashMessage
        v-if="$page.props.flash.warning"
        type="warning"
        :message="$page.props.flash.warning"
      />

      <!-- Page Content -->
      <main>
        <div class="py-6 sm:py-12">
          <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <slot />
          </div>
        </div>
      </main>

      <footer>
        <div class="px-4 pb-4">
          <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <a :href="route('privacy')" class="text-gray-900 hover:text-gray-500">Privacy</a>
          </div>
        </div>
      </footer>
    </div>
  </div>
</template>
