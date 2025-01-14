@props([
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Recipe[] */
    'paginator'
])

<div class="pagination">
    {{ $paginator->onEachSide(2)->links() }}
</div>
