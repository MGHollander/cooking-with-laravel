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

  <div class="uk-grid-collapse" data-uk-grid>
    <div class="uk-width-1-2@m uk-padding-large uk-flex uk-flex-middle uk-flex-center" data-uk-height-viewport>
      <div class="uk-width-3-4@s">
        <div class="uk-text-center uk-margin-bottom">
          <a class="uk-logo uk-text-primary uk-text-bold" href="/">Kocina</a>
        </div>
        <div class="uk-text-center uk-margin-medium-bottom">
          <h1 class="uk-h2 uk-letter-spacing-small">{{ $t('auth.register') }}</h1>
        </div>

        <div data-uk-grid class="uk-child-width-auto uk-grid-small uk-flex uk-flex-center uk-margin">
          <div>
            <a href="#" data-uk-icon="icon: facebook" class="uk-icon-button uk-icon-button-large facebook"></a>
          </div>
          <div>
            <a href="#" data-uk-icon="icon: google-plus" class="uk-icon-button uk-icon-button-large google-plus"></a>
          </div>
          <div>
            <a href="#" data-uk-icon="icon: linkedin" class="uk-icon-button uk-icon-button-large linkedin"></a>
          </div>
        </div>

        <div class="uk-text-center uk-margin">
          <p class="uk-margin-remove">{{ $t('auth.register_with_email') }}</p>
        </div>

        <ValidationErrors class="uk-margin-bottom" />

        <form class="uk-text-center" @submit.prevent="submit">
          <div class="uk-width-1-1 uk-margin">
            <label class="uk-form-label" for="name">{{ $t('auth.name') }}</label>
            <input
              id="name"
              v-model="form.name"
              class="uk-input uk-form-large uk-border-pill uk-text-center"
              type="text"
              required
              autofocus
              autocomplete="name"
            />
          </div>
          <div class="uk-width-1-1 uk-margin">
            <label class="uk-form-label" for="email">{{ $t('auth.email') }}</label>
            <input
              id="email"
              v-model="form.email"
              class="uk-input uk-form-large uk-border-pill uk-text-center"
              type="email"
              required
              autocomplete="username"
            />
          </div>
          <div class="uk-width-1-1 uk-margin">
            <label class="uk-form-label" for="password">{{ $t('auth.password') }}</label>
            <input
              id="password"
              v-model="form.password"
              class="uk-input uk-form-large uk-border-pill uk-text-center"
              type="password"
              required
              autocomplete="new-password"
            />
          </div>
          <div class="uk-width-1-1 uk-margin">
            <label class="uk-form-label" for="password_confirmation">{{ $t('auth.confirm_password') }}</label>
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              class="uk-input uk-form-large uk-border-pill uk-text-center"
              type="password"
              required
              autocomplete="new-password"
            />
          </div>
          <div class="uk-width-1-1 uk-text-center">
            <button class="uk-button uk-button-primary uk-button-large" :disabled="form.processing">
              {{ $t('auth.register') }}
            </button>
          </div>
          <div class="uk-width-1-1 uk-margin uk-text-center">
            <p class="uk-text-small uk-margin-remove">
              {{ $t('auth.agree_terms') }}
              <a class="uk-link-border" href="#">{{ $t('auth.terms') }}</a>.
            </p>
          </div>
        </form>
      </div>
    </div>
    <div class="uk-width-1-2@m uk-padding-large uk-flex uk-flex-middle uk-flex-center uk-light" data-uk-height-viewport>
      <div
        class="uk-background-cover uk-background-norepeat uk-background-blend-overlay uk-background-overlay uk-border-rounded-large uk-width-1-1 uk-height-xlarge uk-flex uk-flex-middle uk-flex-center"
        style="background-image: url(https://images.unsplash.com/photo-1490645935967-10de6ba17061?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1453&q=80);"
      >
        <div class="uk-padding-large">
          <div class="uk-text-center">
            <h2 class="uk-letter-spacing-small">{{ $t('auth.welcome_back') }}</h2>
          </div>
          <div class="uk-margin-top uk-margin-medium-bottom uk-text-center">
            <p>{{ $t('auth.already_registered_text') }}</p>
          </div>
          <div class="uk-width-1-1 uk-text-center">
            <Link :href="route(`login.${attrs.locale}`)" class="uk-button uk-button-primary uk-button-large">
              {{ $t('auth.login.title') }}
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
