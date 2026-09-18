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

    <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @foreach(['Reservation', 'Guest', 'Original Total', 'Final Total', 'Total Paid', 'Refund Amount', 'Refund Date', 'Status', ''] as $heading)
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
                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">{{ $refund->refund_date?->format('F j, Y') }}</td>
                        <td class="px-4 py-4"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $refund->status === 'Refunded' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">{{ $refund->status }}</span></td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="showRefundDetails(@js([
                                    'id' => $refund->id,
                                    'reservation' => 'RES-' . $refund->reservationable_id,
                                    'guest' => $refund->guest_name,
                                    'original_total' => '₱' . number_format($refund->original_total, 2),
                                    'final_total' => '₱' . number_format($refund->final_total, 2),
                                    'total_paid' => '₱' . number_format($refund->total_paid, 2),
                                    'refund_amount' => '₱' . number_format($refund->refund_amount, 2),
                                    'reason' => $refund->reason,
                                    'refund_date' => $refund->refund_date?->format('F j, Y') ?? 'N/A',
                                    'status' => $refund->status,
                                    'processed_by' => $refund->processedBy?->name ?? 'N/A',
                                    'payment_method' => $refund->refund_payment_method,
                                    'reference_number' => $refund->refund_reference_number,
                                    'receipt' => $refund->refund_receipt ? asset('storage/' . $refund->refund_receipt) : null,
                                ]))" class="rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200">View</button>
                                <form method="POST" action="{{ route('employee.refunds.destroy', $refund) }}" onsubmit="return confirm('Delete this refund record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg p-2 text-red-600 hover:bg-red-50" title="Delete refund" aria-label="Delete refund"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="px-6 py-16 text-center text-gray-500">No refunds have been recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="refundDetailsModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4" role="dialog" aria-modal="true" aria-labelledby="refundDetailsTitle">
    <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
        <div class="mb-5 flex items-center justify-between">
            <div><h3 id="refundDetailsTitle" class="text-xl font-bold text-gray-800">Refund Details</h3><p id="refundDetailsReservation" class="mt-1 text-sm text-gray-500"></p></div>
            <button type="button" onclick="closeRefundDetails()" class="text-gray-500 hover:text-gray-800" aria-label="Close"><i class="fas fa-times"></i></button>
        </div>
        <div id="refundDetailsBody" class="grid gap-3 sm:grid-cols-2"></div>
        <div id="refundRecordSection" class="mt-5 hidden border-t border-gray-200 pt-5">
            <h4 class="text-base font-semibold text-gray-800">Record Refund</h4>
            <form id="refundRecordAction" method="POST" enctype="multipart/form-data" class="mt-3 space-y-3">
                @csrf
                @method('PATCH')
                <label class="block text-sm font-semibold text-gray-700">Refund Payment Method
                    <select name="refund_payment_method" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" onchange="toggleRefundReceiptFields(this.value)" required>
                        <option value="Cash">Cash</option>
                        <option value="GCash">GCash</option>
                        <option value="Maya">Maya</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Credit/Debit Card">Credit/Debit Card</option>
                    </select>
                </label>
                <label id="refundReferenceLabel" class="hidden block text-sm font-semibold text-gray-700">Reference Number
                    <input id="refundReferenceField" name="refund_reference_number" type="text" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                </label>
                <label id="refundReceiptLabel" class="hidden block text-sm font-semibold text-gray-700">Refund Receipt
                    <input id="refundReceiptField" name="refund_receipt" type="file" accept="image/*" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                </label>
                <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">Record Refund</button>
            </form>
        </div>
    </div>
</div>

<script>
    function showRefundDetails(refund) {
        document.getElementById('refundDetailsReservation').textContent = refund.reservation;
        document.getElementById('refundDetailsBody').innerHTML = Object.entries({
            'Guest': refund.guest,
            'Original Total': refund.original_total,
            'Final Total': refund.final_total,
            'Total Paid': refund.total_paid,
            'Refund Amount': refund.refund_amount,
            'Reason': refund.reason,
            'Refund Date': refund.refund_date,
            'Processed By': refund.processed_by,
            'Payment Method': refund.payment_method || 'Not recorded',
            'Reference Number': refund.reference_number || 'N/A',
            'Receipt': refund.receipt ? `<img src="${refund.receipt}" alt="Refund receipt" class="max-h-64 w-full rounded-lg border border-gray-200 bg-white object-contain p-2">` : 'N/A',
            'Status': refund.status,
        }).map(([label, value]) => `<div class="rounded-xl border border-gray-200 bg-gray-50 p-4"><div class="text-xs font-semibold uppercase tracking-wider text-gray-500">${label}</div><div class="mt-1 text-sm font-semibold text-gray-900">${value}</div></div>`).join('');
        document.getElementById('refundRecordAction').action = `{{ url('/employee/refunds') }}/${refund.id}/mark-refunded`;
        document.getElementById('refundRecordSection').classList.toggle('hidden', refund.status !== 'Pending');
        document.getElementById('refundDetailsModal').classList.remove('hidden');
        document.getElementById('refundDetailsModal').classList.add('flex');
        toggleRefundReceiptFields('Cash');
    }

    function closeRefundDetails() {
        document.getElementById('refundDetailsModal').classList.add('hidden');
        document.getElementById('refundDetailsModal').classList.remove('flex');
    }

    function toggleRefundReceiptFields(method) {
        const required = method !== 'Cash';
        document.getElementById('refundReferenceField').required = required;
        document.getElementById('refundReceiptField').required = required;
        document.getElementById('refundReferenceLabel').classList.toggle('hidden', !required);
        document.getElementById('refundReceiptLabel').classList.toggle('hidden', !required);
    }
</script>
@endsection
