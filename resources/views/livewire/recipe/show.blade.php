<x-slot name="title">
  {{ $recipe->title }}
</x-slot>

<x-slot name="meta">
  <link rel="canonical" href="{{ route("recipes.show", ["slug" => $recipe->slug]) }}" />
</x-slot>

<div>
  <p class="mb-4"><a href="{{ route("livewire.home") }}" wire:navigate>Terug naar overzicht</a></p>

  <div class="space-y-6 bg-white p-6 sm:rounded-lg sm:shadow-lg md:space-y-10 lg:p-10" x-data="recipe">
    <h1 class="mb-4 text-2xl font-bold md:text-3xl">{{ $recipe->title }}</h1>

    @if ($tags->count() > 0)
      <div class="!mt-4 flex flex-wrap text-sm">
        @foreach ($tags as $tag_id => $tag_name)
          <div class="mb-2 mr-2 rounded bg-gray-200 px-2" wire:key="{{ $tag_id }}">
            {{ $tag_name }}
          </div>
        @endforeach
      </div>
    @endif

    {{-- TODO strip unsave tags --}}
    <div class="md:text-lg">{!! $recipe->summary !!}</div>

    <div @class(["grid items-center space-y-6 md:space-y-0", "md:grid-cols-2" => $image])>
      @if ($image)
        <img
          src="{{ $image }}"
          class="mx-auto aspect-[4/3] w-full rounded-lg object-cover md:mx-0"
          alt="{{ $recipe->title }}"
        />
      @endif

      <div @class(["grid grid-cols-2 gap-4 text-center", "md:grid-cols-4" => ! $image])>
        <div>
          <div class="mx-auto w-16 fill-orange-600">
            <x-icon.plate />
          </div>
          <strong>Aantal porties</strong>
          <br />
          <span x-text="servingsText" />
        </div>

        <div>
          <div class="mx-auto w-16 fill-orange-600">
            <x-icon.gauge />
          </div>
          <strong>Moeilijkheid</strong>
          <br />
          {{ $recipe->difficulty }}
        </div>

        @if ($recipe->preparation_minutes)
          <div>
            <div class="mx-auto w-16 fill-emerald-700">
              <x-icon.knife />
            </div>
            <strong>Voorbereidingstijd</strong>
            <br />
            {{ $recipe->preparation_minutes }} {{ $recipe->preparation_minutes === 1 ? "minuut" : "minuten" }}
          </div>
        @endif

        @if ($recipe->cooking_minutes)
          <div>
            <div class="mx-auto w-16 fill-emerald-700">
              <x-icon.cooking-timer />
            </div>

            <strong>Bereidingstijd</strong>
            <br />
            {{ $recipe->cooking_minutes }} {{ $recipe->cooking_minutes === 1 ? "minuut" : "minuten" }}
          </div>
        @endif
      </div>
    </div>

    <div class="space-y-6 md:flex md:items-start md:space-x-8 md:space-y-0">
      <div class="-mx-6 bg-gray-100 p-6 sm:mx-0 sm:rounded-lg md:w-1/3">
        <h2 class="mb-2 text-xl font-bold md:text-2xl">Ingrediënten</h2>

        <div class="-mx-2 mb-4 flex items-center justify-between rounded bg-gray-200 p-2">
          <p x-text="servingsText" />

          <div class="space-x-2">
            <button
              class="inline-block w-8 rounded border-2 border-gray-500 text-lg font-bold text-gray-600 transition-all hover:bg-gray-500 hover:text-white"
              aria-label="Verhoog het aantal porties"
              x-on:click="increment"
            >
              +
            </button>

            <button
              :disabled="$wire.servings <= 1"
              class="inline-block w-8 rounded border-2 border-gray-500 text-lg font-bold text-gray-600 hover:bg-gray-500 hover:text-white disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-transparent disabled:hover:text-gray-600"
              aria-label="Verminder het aantal porties"
              x-on:click="decrement"
            >
              -
            </button>
          </div>
        </div>
        <template x-for="list in $wire.ingredients">
          <div>
            <h3 x-text="list.title" class="mb-2 mt-8 text-lg font-bold"></h3>

            <ul class="m-0 space-y-1">
              <template x-for="ingredient in list.ingredients">
                <li class="flex flex-auto">
                  <template x-if="ingredient.amount">
                    <span x-text="Math.round(ingredient.amount * 100) / 100 + '&nbsp;'"></span>
                  </template>
                  <template x-if="ingredient.unit">
                    <span x-text="ingredient.unit + '&nbsp;'"></span>
                  </template>
                  <template x-if="ingredient.name_plural && ingredient.amount > 1">
                    <span x-text="ingredient.name_plural"></span>
                  </template>
                  <template x-if="! ingredient.name_plural || (ingredient.amount > 0 && ingredient.amount <= 1)">
                    <span x-text="ingredient.name"></span>
                  </template>
                  <template x-if="ingredient.info">
                    <span x-text="ingredient.info"></span>
                  </template>
                </li>
              </template>
            </ul>
          </div>
        </template>
      </div>

      <div class="space-y-4 sm:px-6 md:w-2/3 md:px-0">
        <h2 class="mb-4 text-xl font-bold md:mt-6 md:text-2xl">Instructies</h2>

        <div class="recipe-instructions">
          {!! $recipe->instructions !!}
        </div>

        @if ($recipe->source_label || $recipe->source_link)
          <p>
            <strong>Bron:</strong>
            @if ($recipe->source_link)
              <a href="{{ $recipe->source_link }}" target="_blank">
                {{ $recipe->source_label ?? $recipe->source_link }}
              </a>
            @elseif ($recipe->source_label)
              {{ $recipe->source_label }}
            @endif
          </p>
        @endif
      </div>
    </div>
  </div>
</div>

@script
  <script>
    Alpine.data('recipe', () => {
      return {
        servingsText() {
          return $wire.servings + ' ' + ($wire.servings === 1 ? 'portie' : 'porties');
        },
        increment() {
          for (let listKey in $wire.ingredients) {
            for (let key in $wire.ingredients[listKey].ingredients) {
              let amount = parseFloat($wire.ingredients[listKey].ingredients[key].amount);
              // round amount to 2 decimals
              $wire.ingredients[listKey].ingredients[key].amount = amount + amount / parseFloat($wire.servings);
            }
          }
          $wire.servings++;
        },

        decrement() {
          if ($wire.servings <= 1) return;
          for (let listKey in $wire.ingredients) {
            for (let key in $wire.ingredients[listKey].ingredients) {
              let amount = parseFloat($wire.ingredients[listKey].ingredients[key].amount);
              $wire.ingredients[listKey].ingredients[key].amount = amount - amount / parseFloat($wire.servings);
            }
          }
          $wire.servings--;
        },
      };
    });
  </script>
@endscript