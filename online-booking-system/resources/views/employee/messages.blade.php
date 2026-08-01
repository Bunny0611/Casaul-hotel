@extends('employee.layout')

@section('content')
<div class="rounded-2xl bg-white p-6 shadow-md">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Inbox</h3>
            <p class="text-sm text-gray-500">Team and guest communication updates</p>
        </div>
        <button class="rounded-lg bg-orange-500 px-4 py-2 text-white transition hover:bg-orange-600">New Message</button>
    </div>

    <div class="space-y-4">
        <div class="flex items-start gap-3 border-b border-gray-200 pb-4">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-gray-600"><i class="fas fa-user"></i></div>
            <div class="flex-1">
                <div class="flex justify-between">
                    <h6 class="font-semibold text-gray-800">Housekeeping Team</h6>
                    <span class="text-xs text-gray-500">2 min ago</span>
                </div>
                <p class="text-sm text-gray-600">Room 412 is ready for reassignment.</p>
            </div>
        </div>
        <div class="flex items-start gap-3 border-b border-gray-200 pb-4">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-gray-600"><i class="fas fa-user"></i></div>
            <div class="flex-1">
                <div class="flex justify-between">
                    <h6 class="font-semibold text-gray-800">Front Desk</h6>
                    <span class="text-xs text-gray-500">15 min ago</span>
                </div>
                <p class="text-sm text-gray-600">Guest request for airport transfer has been approved.</p>
            </div>
        </div>
        <div class="flex items-start gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-gray-600"><i class="fas fa-user"></i></div>
            <div class="flex-1">
                <div class="flex justify-between">
                    <h6 class="font-semibold text-gray-800">Maintenance</h6>
                    <span class="text-xs text-gray-500">1 hr ago</span>
                </div>
                <p class="text-sm text-gray-600">Air conditioning inspection scheduled for Room 215.</p>
            </div>
        </div>
    </div>
</div>
@endsection
