<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import ValidationErrors from "@/Components/ValidationErrors.vue";
import { useAttrs } from "vue";

const attrs = useAttrs();

const form = useForm({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  terms: false,
});

const submit = () => {
  form.post(route(`register.${attrs.locale}`), {
    onFinish: () => form.reset("password", "password_confirmation"),
  });
};
</script>

<template>
  <Head :title="$t('auth.register')" />

  <div class="grid grid-cols-1 md:grid-cols-2">
    <div class="p-8 md:p-12 lg:p-16 flex items-center justify-center min-h-screen">
      <div class="w-full sm:w-3/4 max-w-md">
        <div class="text-center mb-8">
          <a class="text-3xl text-[#eb4a36] font-bold tracking-tight" href="/">Kocina</a>
        </div>
        <div class="text-center mb-10">
          <h1 class="text-3xl font-bold tracking-tight text-gray-900">{{ $t("auth.register") }}</h1>
        </div>

        <div class="flex flex-wrap justify-center gap-3 mb-8">
          <div>
            <a
              href="#"
              class="w-12 h-12 rounded-full flex items-center justify-center bg-[#3b5998] text-white hover:opacity-90 transition-opacity"
            >
              <span class="sr-only">Facebook</span>
              <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                <path
                  d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"
                />
              </svg>
            </a>
          </div>
          <div>
            <a
              href="#"
              class="w-12 h-12 rounded-full flex items-center justify-center bg-[#db4437] text-white hover:opacity-90 transition-opacity"
            >
              <span class="sr-only">Google Plus</span>
              <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                <path
                  d="M7 11v2.4h3.97c-.16 1.029-1.2 3.02-3.97 3.02-2.39 0-4.34-1.979-4.34-4.42 0-2.44 1.95-4.42 4.34-4.42 1.36 0 2.27.58 2.79 1.08l1.9-1.83c-1.22-1.14-2.8-1.83-4.69-1.83-3.87 0-7 3.13-7 7s3.13 7 7 7c4.04 0 6.721-2.84 6.721-6.84 0-.46-.051-.81-.111-1.16h-6.61zm17 0h-2v-2h-2v2h-2v2h2v2h2v-2h2v-2z"
                />
              </svg>
            </a>
          </div>
          <div>
            <a
              href="#"
              class="w-12 h-12 rounded-full flex items-center justify-center bg-[#0077b5] text-white hover:opacity-90 transition-opacity"
            >
              <span class="sr-only">LinkedIn</span>
              <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                <path
                  d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"
                />
              </svg>
            </a>
          </div>
        </div>

        <div class="text-center mb-8">
          <p class="text-gray-600">{{ $t("auth.register_with_email") }}</p>
        </div>

        <ValidationErrors class="mb-6" />

        <form class="space-y-6" @submit.prevent="submit">
          <div>
            <label class="block text-sm text-center font-medium text-gray-700 mb-2" for="name">{{ $t("auth.name") }}</label>
            <input
              id="name"
              v-model="form.name"
              class="w-full px-4 py-3 rounded-full border border-gray-300 focus:ring-2 focus:ring-[#eb4a36] focus:border-[#eb4a36] text-center transition-all"
              type="text"
              required
              autofocus
              autocomplete="name"
            />
          </div>
          <div>
            <label class="block text-sm text-center font-medium text-gray-700 mb-2" for="email">{{ $t("auth.email") }}</label>
            <input
              id="email"
              v-model="form.email"
              class="w-full px-4 py-3 rounded-full border border-gray-300 focus:ring-2 focus:ring-[#eb4a36] focus:border-[#eb4a36] text-center transition-all"
              type="email"
              required
              autocomplete="username"
            />
          </div>
          <div>
            <label class="block text-sm text-center font-medium text-gray-700 mb-2" for="password">{{ $t("auth.password") }}</label>
            <input
              id="password"
              v-model="form.password"
              class="w-full px-4 py-3 rounded-full border border-gray-300 focus:ring-2 focus:ring-[#eb4a36] focus:border-[#eb4a36] text-center transition-all"
              type="password"
              required
              autocomplete="new-password"
            />
          </div>
          <div>
            <label class="block text-sm text-center font-medium text-gray-700 mb-2" for="password_confirmation">{{
              $t("auth.confirm_password")
            }}</label>
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              class="w-full px-4 py-3 rounded-full border border-gray-300 focus:ring-2 focus:ring-[#eb4a36] focus:border-[#eb4a36] text-center transition-all"
              type="password"
              required
              autocomplete="new-password"
            />
          </div>
          <div>
            <button
              class="w-full bg-[#eb4a36] text-white py-4 px-8 rounded-full font-bold hover:bg-black transition-colors duration-300 disabled:opacity-50"
              :disabled="form.processing"
            >
              {{ $t("auth.register") }}
            </button>
          </div>
          <div class="text-center">
            <p class="text-sm text-gray-600">
              {{ $t("auth.agree_terms") }}
              <a class="text-[#eb4a36] hover:underline" href="#">{{ $t("auth.terms") }}</a
              >.
            </p>
          </div>
        </form>
      </div>
    </div>
    <div class="hidden md:flex p-8 lg:p-12 items-center justify-center min-h-screen text-white">
      <div
        class="bg-cover bg-no-repeat bg-center bg-black/40 bg-blend-overlay rounded-3xl w-full h-full max-h-[700px] flex items-center justify-center overflow-hidden"
        style="
          background-image: url(https://images.unsplash.com/photo-1490645935967-10de6ba17061?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1453&q=80);
        "
      >
        <div class="p-8 md:p-12 text-center">
          <h2 class="text-4xl font-bold mb-6 tracking-tight">{{ $t("auth.welcome_back") }}</h2>
          <p class="text-lg mb-10 text-gray-100 max-w-sm mx-auto">{{ $t("auth.already_registered_text") }}</p>
          <div>
            <Link
              :href="route(`login.${attrs.locale}`)"
              class="inline-block bg-[#eb4a36] text-white py-4 px-12 rounded-full font-bold hover:bg-white hover:text-black transition-colors duration-300"
            >
              {{ $t("auth.login.title") }}
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
