@extends('employee.layout')

@section('content')
<div class="flex flex-col items-center justify-center rounded-3xl border border-gray-200 bg-white p-10 text-center shadow-lg">
    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-orange-100 text-orange-600">
        <i class="fas fa-user-shield text-2xl"></i>
    </div>
    <h2 class="text-3xl font-bold text-gray-800">Employee Portal Ready</h2>
    <p class="mt-3 max-w-2xl text-lg text-gray-600">Use the navigation to manage reservations, room status, check-ins, guest requests, and messages from one place.</p>
    <div class="mt-8 flex flex-wrap justify-center gap-3">
        <a href="{{ route('employee.dashboard') }}" class="rounded-lg bg-orange-500 px-5 py-3 font-semibold text-white transition hover:bg-orange-600">Go to Dashboard</a>
        <a href="{{ route('employee.reservation') }}" class="rounded-lg border border-gray-300 px-5 py-3 font-semibold text-gray-700 transition hover:bg-gray-100">View Reservations</a>
    </div>
</div>
@endsection
