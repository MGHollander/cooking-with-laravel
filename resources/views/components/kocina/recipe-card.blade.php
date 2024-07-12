@props([
  "recipe",
])

<a
    href="{{ route("recipes.show", ["slug" => $recipe["slug"]]) }}"
    class="cursor-pointer rounded-lg bg-white no-underline shadow-lg hover:scale-105"
>
    @if ($recipe["image"])
        <div class="overflow-hidden rounded-t-lg">
            <img
                src="{{ $recipe["image"] }}"
                alt="{{ $recipe["title"] }}"
                class="h-40 w-full object-cover transition-all"
            />
        </div>
    @endif

    <h2 class="mb-2 px-6 py-3 text-xl font-medium text-gray-900">{{ $recipe["title"] }}</h2>
</a>
