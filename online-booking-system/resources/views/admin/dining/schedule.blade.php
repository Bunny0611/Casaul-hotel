@extends('admin.layout')

@section('content')
<div class="space-y-6 p-4 lg:p-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Dining Schedule</h2>
            <p class="mt-1 text-sm text-gray-500">Manage meal periods and guest capacity settings.</p>
        </div>
        <a href="{{ route('admin.rooms', ['tab' => 'dining']) }}" class="inline-flex items-center justify-center rounded-lg border border-orange-300 bg-white px-4 py-2.5 text-sm font-semibold text-orange-600 transition hover:bg-orange-50">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dining
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-5 py-4">
            <div>
                <h3 class="text-xl font-semibold text-gray-800">Dining Schedule</h3>
            </div>
            <button type="button" class="inline-flex items-center justify-center rounded-lg border border-orange-300 bg-white px-3 py-2 text-sm font-medium text-orange-600 transition hover:bg-orange-50">
                <i class="fas fa-plus mr-2"></i>Add Schedule
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Meal Period</th>
                        <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Time</th>
                        <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Max Guests</th>
                        <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($schedules as $schedule)
                        <tr class="bg-white">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $schedule['period'] }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $schedule['time'] }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $schedule['max_guests'] }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full border border-green-200 bg-green-100 px-2.5 py-1 text-[11px] font-medium text-green-700">{{ $schedule['status'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" class="rounded-md border border-blue-200 bg-blue-50 p-2 text-blue-700"><i class="fas fa-edit text-xs"></i></button>
                                    <button type="button" class="rounded-md border border-red-200 bg-red-50 p-2 text-red-700"><i class="fas fa-trash text-xs"></i></button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
