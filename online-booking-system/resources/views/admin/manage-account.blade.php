@extends('admin.layout')

@section('content')
<div class="animate-fade-in">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Manage Account</h2>

    @if(session('success'))
        <div class="mb-6 rounded-xl bg-green-50 border border-green-200 p-4 text-sm text-green-800">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Manage Users</h3>
                        <p class="mt-1 text-sm text-gray-500">Create and manage teacher and parent accounts.</p>
                    </div>
                    <button type="button" class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-500/20 transition hover:from-orange-600 hover:to-orange-700">
                        <i class="fas fa-plus"></i> Add User
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-3xl bg-white p-5 shadow-sm border border-slate-200">
                    <div class="flex items-center gap-3">
                        <div class="rounded-2xl bg-orange-100 p-3 text-orange-600">
                            <i class="fas fa-users text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Total Users</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-900">0</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-3xl bg-white p-5 shadow-sm border border-slate-200">
                    <div class="flex items-center gap-3">
                        <div class="rounded-2xl bg-blue-100 p-3 text-blue-600">
                            <i class="fas fa-user-shield text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Admin</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-900">0</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-3xl bg-white p-5 shadow-sm border border-slate-200">
                    <div class="flex items-center gap-3">
                        <div class="rounded-2xl bg-purple-100 p-3 text-purple-600">
                            <i class="fas fa-user-tie text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Employee</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-900">0</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-3xl bg-white p-5 shadow-sm border border-slate-200">
                    <div class="flex items-center gap-3">
                        <div class="rounded-2xl bg-emerald-100 p-3 text-emerald-600">
                            <i class="fas fa-broom text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Housekeeping</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-900">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl bg-white p-6 shadow-lg border border-slate-200">
                <div class="grid gap-4 lg:grid-cols-3">
                    <div>
                        <label class="text-sm font-medium text-slate-700">Filter by Role</label>
                        <select class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                            <option>All Roles</option>
                            <option>Teacher</option>
                            <option>Parent</option>
                            <option>Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Filter by Status</label>
                        <select class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                            <option>All Status</option>
                            <option>Active</option>
                            <option>Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Search</label>
                        <div class="mt-2 flex gap-2">
                            <input type="text" placeholder="Search by name, email, or username..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20" />
                            <button class="rounded-2xl bg-slate-800 px-4 py-3 text-white transition hover:bg-slate-900">Reset</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl bg-white p-6 shadow-lg border border-slate-200">
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Users List</h3>
                        <p class="text-sm text-slate-500">All users and account roles in one place.</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <button class="inline-flex items-center gap-2 rounded-2xl bg-emerald-500 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-600">
                            <i class="fas fa-download"></i> Export
                        </button>
                        <button class="inline-flex items-center gap-2 rounded-2xl bg-cyan-500 px-4 py-3 text-sm font-semibold text-white transition hover:bg-cyan-600">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full border-separate border-spacing-y-3 text-left">
                        <thead>
                            <tr class="text-sm font-semibold text-slate-600">
                                <th class="px-4 py-3">Name</th>
                                <th class="px-4 py-3">Username</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Role</th>
                                <th class="px-4 py-3">Phone</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Created</th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-slate-50 rounded-3xl">
                                <td class="px-4 py-4 text-sm text-slate-800">No users available</td>
                                <td class="px-4 py-4" colspan="7"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
