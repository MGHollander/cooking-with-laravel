@extends("inertia-minimal.layout")

@section("head")
  @routes
  @vite("resources/js/InertiaMinimal/app.js")
  @inertiaHead
@endsection

@section("content")
  @inertia
@endsection
