@props([
    'recipe'
])

<template x-if="isWakeLockAvailable">
    <div {{ $attributes->class(['recipe-cooking-setting']) }}>
        <label><input type="checkbox" @click="toggleWakeLock" /> Kookstand</label>
        <p>Als je de kookstand inschakelt, dan gaat je scherm niet in slaapstand tijdens het koken.</p>
    </div>
</template>
