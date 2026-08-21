@extends('employee.layout')

@section('pageTitle', 'Guest Requests Management')

@section('content')
<div class="rounded-2xl bg-white p-6 shadow-md">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Guest Service Requests</h3>
            <p class="text-sm text-gray-500">Manage room service and guest assistance requests</p>
        </div>
        <div class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-medium text-emerald-700">
            {{ collect($requests)->where('status', 'Resolved')->count() }} resolved
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-4">
        @forelse($requests as $request)
            <div class="rounded-xl border border-gray-200 p-4">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $request['title'] }}</p>
                        <p class="text-sm text-gray-500">Requested {{ $request['requested_at'] }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="rounded-full px-2 py-1 text-xs font-medium text-white {{ $request['status'] === 'Resolved' ? 'bg-emerald-500' : ($request['status'] === 'Approved' ? 'bg-blue-500' : 'bg-amber-500') }}">
                            {{ $request['status'] }}
                        </span>
                        @if($request['status'] !== 'Resolved')
                            <form method="POST" action="{{ route('employee.guest-requests.resolve', ['id' => $request['id']]) }}">
                                @csrf
                                <button type="submit" class="rounded-lg bg-emerald-500 px-3 py-2 text-sm font-medium text-white transition hover:bg-emerald-600">
                                    Resolve
                                </button>
                            </form>
                        @else
                            <span class="text-sm text-gray-500">Completed</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-6 text-center text-sm text-gray-500">
                No guest requests found. Refresh the page after new requests arrive.
            </div>
        @endforelse
    </div>
</div>
@endsection
