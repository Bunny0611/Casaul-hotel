@extends('admin.layout')

@section('content')
<div class="animate-fade-in">
    <div class="mb-6 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Guest Management</h2>
            <p class="mt-1 text-sm text-gray-500">Review guest records and add new guest details from one place.</p>
        </div>
        <button type="button" onclick="openAddGuestModal()" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-3 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:from-orange-600 hover:to-orange-700">
            <i class="fas fa-user-plus mr-2"></i>Add Guest
        </button>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Guest Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Phone</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($guests as $guest)
                    <tr class="transition-colors hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $guest->guest_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $guest->guest_email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $guest->guest_phone }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2">
                                <button type="button" class="rounded-lg p-2 text-blue-600 transition hover:bg-blue-50 hover:text-blue-800" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="rounded-lg p-2 text-green-600 transition hover:bg-green-50 hover:text-green-800" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-users mb-4 text-4xl text-gray-300"></i>
                            <p>No guests registered yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="addGuestModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-4 shadow-2xl sm:p-6">
        <button type="button" onclick="closeAddGuestModal()" class="absolute right-4 top-4 text-gray-500 transition hover:text-gray-700">
            <i class="fas fa-times text-xl"></i>
        </button>

        <div class="mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Add New Guest</h3>
            <p class="mt-1 text-sm text-gray-500">Fill in the guest details below.</p>
        </div>

        <form action="#" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Guest Name</label>
                    <input type="text" name="guest_name" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Guest Email</label>
                    <input type="email" name="guest_email" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Guest Phone</label>
                    <input type="text" name="guest_phone" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
            </div>
            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                <button type="button" onclick="closeAddGuestModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition hover:bg-gray-100">Cancel</button>
                <button type="submit" class="rounded-lg bg-orange-500 px-4 py-2 font-medium text-white transition hover:bg-orange-600">Save Guest</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddGuestModal() {
        document.getElementById('addGuestModal').classList.remove('hidden');
        document.getElementById('addGuestModal').classList.add('flex');
    }

    function closeAddGuestModal() {
        document.getElementById('addGuestModal').classList.add('hidden');
        document.getElementById('addGuestModal').classList.remove('flex');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('addGuestModal');
        if (modal) {
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeAddGuestModal();
                }
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeAddGuestModal();
            }
        });
    });
</script>
@endsection
