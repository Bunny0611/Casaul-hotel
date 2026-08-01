@extends('employee.layout')

@section('content')
<div class="rounded-2xl bg-white p-6 shadow-md">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Guest Service Requests</h3>
            <p class="text-sm text-gray-500">Manage room service and guest assistance requests</p>
        </div>
        <button class="rounded-lg bg-emerald-500 px-4 py-2 text-white transition hover:bg-emerald-600">Resolve</button>
    </div>

    <div class="space-y-4">
        <div class="rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-semibold text-gray-800">Room 305 - Extra Towels</p>
                    <p class="text-sm text-gray-500">Requested 10:15 AM</p>
                </div>
                <span class="rounded-full bg-amber-500 px-2 py-1 text-xs text-white">Pending</span>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-semibold text-gray-800">Room 208 - Late Checkout</p>
                    <p class="text-sm text-gray-500">Requested 9:40 AM</p>
                </div>
                <span class="rounded-full bg-emerald-500 px-2 py-1 text-xs text-white">Approved</span>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-semibold text-gray-800">Room 101 - Wake-up Call</p>
                    <p class="text-sm text-gray-500">Requested 7:30 AM</p>
                </div>
                <span class="rounded-full bg-blue-500 px-2 py-1 text-xs text-white">Done</span>
            </div>
        </div>
    </div>
</div>
@endsection
