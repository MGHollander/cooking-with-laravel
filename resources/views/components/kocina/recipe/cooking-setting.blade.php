@props([
    'recipe'
])

<template x-if="isWakeLockAvailable">
    <div {{ $attributes->class(['recipe-cooking-setting']) }}>
        <label><input type="checkbox" @click="toggleWakeLock" /> {{ __('recipes.show.cooking_mode') }}</label>
        <p>{{ __('recipes.show.cooking_mode_description') }}</p>
    </div>
</template>
