@extends('admin.layout')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold">Printable Report</h2>
        <p class="text-sm text-gray-600">From: {{ $from ?? 'All' }} — To: {{ $to ?? 'All' }}</p>
    </div>

    <div class="mb-6">
        <h3 class="text-lg font-semibold">Transactions</h3>
        <table class="w-full mt-4 border-collapse">
            <thead>
                <tr>
                    <th class="border px-3 py-2 text-left">Guest</th>
                    <th class="border px-3 py-2 text-left">Room</th>
                    <th class="border px-3 py-2 text-left">Check In</th>
                    <th class="border px-3 py-2 text-left">Check Out</th>
                    <th class="border px-3 py-2 text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reservations as $r)
                <tr>
                    <td class="border px-3 py-2">{{ $r->guest_name }}</td>
                    <td class="border px-3 py-2">{{ $r->room?->room_number ?? 'N/A' }}</td>
                    <td class="border px-3 py-2">{{ $r->check_in }}</td>
                    <td class="border px-3 py-2">{{ $r->check_out }}</td>
                    <td class="border px-3 py-2 text-right">₱{{ number_format($r->total_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
    window.addEventListener('load', function() {
        window.print();
    });
</script>
@endsection
