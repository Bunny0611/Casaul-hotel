@extends('employee.layout')

@section('pageTitle', 'Refund History')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 rounded-2xl bg-white p-6 shadow-sm sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Refund History</h2>
            <p class="mt-1 text-sm text-gray-500">Review refund amounts without changing the original payment history.</p>
        </div>
        <a href="{{ route('employee.reservation') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Back to Reservations</a>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @foreach(['Reservation', 'Guest', 'Original Total', 'Final Total', 'Total Paid', 'Refund Amount', 'Reason', 'Refund Date', 'Status', 'Processed By', ''] as $heading)
                        <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ $heading }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($refunds as $refund)
                    <tr>
                        <td class="whitespace-nowrap px-4 py-4 text-sm font-semibold text-gray-900">RES-{{ $refund->reservationable_id }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $refund->guest_name }}</td>
                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">₱{{ number_format($refund->original_total, 2) }}</td>
                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">₱{{ number_format($refund->final_total, 2) }}</td>
                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">₱{{ number_format($refund->total_paid, 2) }}</td>
                        <td class="whitespace-nowrap px-4 py-4 text-sm font-semibold text-orange-600">₱{{ number_format($refund->refund_amount, 2) }}</td>
                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">{{ $refund->reason }}</td>
                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">{{ $refund->refund_date?->format('F j, Y') }}</td>
                        <td class="px-4 py-4"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $refund->status === 'Refunded' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">{{ $refund->status }}</span></td>
                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">{{ $refund->processedBy?->name ?? 'N/A' }}</td>
                        <td class="px-4 py-4">
                            @if($refund->status === 'Pending')
                                <form method="POST" action="{{ route('employee.refunds.mark-refunded', $refund) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="whitespace-nowrap rounded-lg bg-green-600 px-3 py-2 text-xs font-semibold text-white hover:bg-green-700">Mark Refunded</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="11" class="px-6 py-16 text-center text-gray-500">No refunds have been recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
