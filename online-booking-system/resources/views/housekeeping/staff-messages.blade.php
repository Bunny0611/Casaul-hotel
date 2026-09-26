@extends('housekeeping.layout')

@section('content')
<main class="h-full overflow-y-auto p-6">
    <div class="mx-auto max-w-7xl">
        <h1 class="mb-1 text-2xl font-semibold text-gray-800">Staff Messages</h1>
        <p class="mb-5 text-sm text-gray-600">Coordinate with employees and administration.</p>
        @include('shared.staff-messages')
    </div>
</main>
@endsection