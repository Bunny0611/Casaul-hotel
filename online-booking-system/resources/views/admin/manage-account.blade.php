@extends('admin.layout')

@section('content')
<div class="animate-fade-in">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Manage Account</h2>
            <p class="mt-1 text-sm text-gray-500">Create and manage accounts for admins, employees, and housekeeping staff.</p>
        </div>
        <button type="button" onclick="openModal('createModal')"
            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-500/20 transition hover:from-orange-600 hover:to-orange-700">
            <i class="fas fa-plus"></i> Add Account
        </button>
    </div>

    @if($errors->any())
        <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-800">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-3xl bg-white p-5 shadow-sm border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="rounded-2xl bg-orange-100 p-3 text-orange-600">
                    <i class="fas fa-users text-lg"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Total Accounts</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $totalUsers }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-3xl bg-white p-5 shadow-sm border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="rounded-2xl bg-blue-100 p-3 text-blue-600">
                    <i class="fas fa-user-shield text-lg"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Admins</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $totalAdmins }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-3xl bg-white p-5 shadow-sm border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="rounded-2xl bg-purple-100 p-3 text-purple-600">
                    <i class="fas fa-user-tie text-lg"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Employees</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $totalEmployees }}</p>
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
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $totalHousekeeping }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-3xl bg-white p-5 shadow-sm border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="rounded-2xl bg-teal-100 p-3 text-teal-600">
                    <i class="fas fa-user-check text-lg"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Active</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $activeUsers }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 rounded-3xl bg-white p-6 shadow-lg border border-slate-200">
        <div class="grid gap-4 lg:grid-cols-3">
            <div>
                <label class="text-sm font-medium text-slate-700">Filter by Role</label>
                <select id="filterRole" onchange="filterTable()"
                    class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="employee">Employee</option>
                    <option value="housekeeping">Housekeeping</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-medium text-slate-700">Filter by Status</label>
                <select id="filterStatus" onchange="filterTable()"
                    class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-medium text-slate-700">Search</label>
                <div class="mt-2 flex gap-2">
                    <input id="searchInput" type="text" placeholder="Search by name or email..."
                        oninput="filterTable()"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20" />
                    <button type="button" onclick="resetFilters()" class="rounded-2xl bg-slate-800 px-4 py-3 text-white transition hover:bg-slate-900">Reset</button>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 rounded-3xl bg-white p-6 shadow-lg border border-slate-200">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-slate-900">Accounts List</h3>
                <p class="text-sm text-slate-500">Accounts created by an admin can log in on any device using their email and password.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <span id="selectedAccountCount" class="self-center text-sm font-medium text-slate-500">0 selected</span>
                <button type="button" onclick="confirmBulkAccountDelete()"
                    class="inline-flex items-center gap-2 rounded-2xl bg-red-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-red-700">
                    <i class="fas fa-trash"></i> Delete Selected
                </button>
                <button type="button" onclick="window.location.reload()"
                    class="inline-flex items-center gap-2 rounded-2xl bg-cyan-500 px-4 py-3 text-sm font-semibold text-white transition hover:bg-cyan-600">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full border-separate border-spacing-y-3 text-left">
                <thead>
                    <tr class="text-sm font-semibold text-slate-600">
                            <th class="px-4 py-3"><input id="selectAllAccounts" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500" onclick="toggleAllAccounts(this)" aria-label="Select all accounts"></th>
                            <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Phone</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Created By</th>
                        <th class="px-4 py-3">Created</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    @forelse($users as $user)
                        <tr class="bg-slate-50 rounded-3xl user-row"
                            data-role="{{ $user->role }}"
                            data-status="{{ $user->is_active ? 'active' : 'inactive' }}"
                            data-search="{{ strtolower($user->name . ' ' . $user->email) }}">
                                <td class="px-4 py-4"><input type="checkbox" class="account-checkbox h-4 w-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500" value="{{ $user->id }}" onclick="updateAccountSelection()" aria-label="Select {{ $user->name }}" @disabled($user->id === auth()->id())></td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-orange-500 to-orange-600 text-sm font-bold text-white">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $user->middle_initial ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-slate-800">{{ $user->email }}</td>
                            <td class="px-4 py-4">
                                @php
                                    $roleColors = [
                                        'admin' => 'bg-blue-100 text-blue-700',
                                        'employee' => 'bg-purple-100 text-purple-700',
                                        'housekeeping' => 'bg-emerald-100 text-emerald-700',
                                        'guest' => 'bg-slate-100 text-slate-600',
                                    ];
                                @endphp
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $roleColors[$user->role] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm text-slate-800">{{ $user->contact_no ?? '—' }}</td>
                            <td class="px-4 py-4">
                                @if($user->is_active)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        <i class="fas fa-circle text-[8px]"></i> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                        <i class="fas fa-circle text-[8px]"></i> Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-sm text-slate-600">{{ $user->creator ? $user->creator->name : '—' }}</td>
                            <td class="px-4 py-4 text-sm text-slate-600">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="openEditModal(this)"
                                        data-id="{{ $user->id }}"
                                        data-first="{{ $user->first_name }}"
                                        data-last="{{ $user->last_name }}"
                                        data-middle="{{ $user->middle_initial }}"
                                        data-email="{{ $user->email }}"
                                        data-contact="{{ $user->contact_no }}"
                                        data-role="{{ $user->role }}"
                                        class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-200"
                                        title="Edit account">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>

                                    <form method="POST" action="{{ route('admin.manage-account.status', $user->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="is_active" value="{{ $user->is_active ? 0 : 1 }}">
                                        @if($user->id !== auth()->id())
                                            <button type="submit"
                                                class="rounded-xl px-3 py-2 text-xs font-semibold transition {{ $user->is_active ? 'bg-amber-100 text-amber-700 hover:bg-amber-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}"
                                                title="{{ $user->is_active ? 'Deactivate account' : 'Activate account' }}">
                                                <i class="fas {{ $user->is_active ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        @else
                                            <span class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-400" title="Your own account">You</span>
                                        @endif
                                    </form>

                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.manage-account.destroy', $user->id) }}"
                                            onsubmit="return confirm('Delete the account for {{ addslashes($user->name) }}? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-xl bg-red-100 px-3 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-200" title="Delete account">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="bg-slate-50 rounded-3xl">
                            <td class="px-4 py-4 text-sm text-slate-800" colspan="9">No accounts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $users->links('pagination.admin-rooms') }}
    </div>
</div>

<form id="bulkAccountDeleteForm" method="POST" action="{{ route('admin.manage-account.bulkDestroy') }}">
    @csrf
    @method('DELETE')
    <input type="hidden" name="user_ids" id="bulkUserIds">
</form>

<!-- ===== Create Account Modal ===== -->
<div id="createModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 p-4">
    <div class="fixed inset-0" onclick="closeModal('createModal')"></div>
    <div class="relative mx-auto my-6 max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-3xl bg-white p-6 shadow-2xl sm:p-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-bold text-slate-900">Create New Account</h3>
                <p class="mt-1 text-sm text-slate-500">The user will log in with this email and password.</p>
            </div>
            <button type="button" onclick="closeModal('createModal')" class="rounded-xl bg-slate-100 px-3 py-2 text-slate-600 transition hover:bg-slate-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.manage-account.store') }}">
            @csrf
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="text-sm font-medium text-slate-700">First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required maxlength="255"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                    @error('first_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Middle Initial</label>
                    <input type="text" name="middle_initial" value="{{ old('middle_initial') }}" maxlength="3"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                    @error('middle_initial')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required maxlength="255"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                    @error('last_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-slate-700">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required maxlength="255"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                    @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Contact Number</label>
                    <input type="text" name="contact_no" value="{{ old('contact_no') }}" maxlength="25"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                    @error('contact_no')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="text-sm font-medium text-slate-700">Role</label>
                    <select name="role" required
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                        <option value="" disabled selected>Select Role</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Employee</option>
                        <option value="housekeeping" {{ old('role') == 'housekeeping' ? 'selected' : '' }}>Housekeeping</option>
                    </select>
                    @error('role')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Password</label>
                    <input type="password" name="password" required minlength="6"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                    @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="closeModal('createModal')" class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">Cancel</button>
                <button type="submit" class="rounded-2xl bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-500/20 transition hover:from-orange-600 hover:to-orange-700">
                    <i class="fas fa-user-plus mr-1"></i> Create Account
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ===== Edit Account Modal ===== -->
<div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 p-4">
    <div class="fixed inset-0" onclick="closeModal('editModal')"></div>
    <div class="relative mx-auto my-6 max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-3xl bg-white p-6 shadow-2xl sm:p-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-bold text-slate-900">Edit Account</h3>
                <p class="mt-1 text-sm text-slate-500">Update details or reset the password.</p>
            </div>
            <button type="button" onclick="closeModal('editModal')" class="rounded-xl bg-slate-100 px-3 py-2 text-slate-600 transition hover:bg-slate-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" action="" id="editForm">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="text-sm font-medium text-slate-700">First Name</label>
                    <input type="text" name="first_name" id="editFirstName" required maxlength="255"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Middle Initial</label>
                    <input type="text" name="middle_initial" id="editMiddleInitial" maxlength="3"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Last Name</label>
                    <input type="text" name="last_name" id="editLastName" required maxlength="255"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-slate-700">Email Address</label>
                    <input type="email" name="email" id="editEmail" required maxlength="255"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Contact Number</label>
                    <input type="text" name="contact_no" id="editContact" maxlength="25"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="text-sm font-medium text-slate-700">Role</label>
                    <select name="role" id="editRole" required
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                        <option value="admin">Admin</option>
                        <option value="employee">Employee</option>
                        <option value="housekeeping">Housekeeping</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">New Password <span class="text-xs text-slate-400">(optional)</span></label>
                    <input type="password" name="password" id="editPassword" minlength="6"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Confirm New Password</label>
                    <input type="password" name="password_confirmation"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="closeModal('editModal')" class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">Cancel</button>
                <button type="submit" class="rounded-2xl bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-500/20 transition hover:from-orange-600 hover:to-orange-700">
                    <i class="fas fa-save mr-1"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function updateAccountSelection() {
        const checkboxes = document.querySelectorAll('.account-checkbox');
        const selected = document.querySelectorAll('.account-checkbox:checked');
        const selectAll = document.getElementById('selectAllAccounts');
        const count = document.getElementById('selectedAccountCount');

        if (selectAll) {
            selectAll.checked = checkboxes.length > 0 && selected.length === checkboxes.length;
        }
        if (count) {
            count.textContent = selected.length + ' selected';
        }
    }

    function toggleAllAccounts(source) {
        document.querySelectorAll('.account-checkbox').forEach(function (checkbox) {
            checkbox.checked = source.checked;
        });
        updateAccountSelection();
    }

    function confirmBulkAccountDelete() {
        const selectedIds = Array.from(document.querySelectorAll('.account-checkbox:checked')).map(function (checkbox) {
            return checkbox.value;
        });

        if (!selectedIds.length) {
            alert('Please select at least one account to delete.');
            return;
        }

        if (!confirm('Are you sure you want to delete the selected ' + selectedIds.length + ' account(s)?')) {
            return;
        }

        document.getElementById('bulkUserIds').value = selectedIds.join(',');
        document.getElementById('bulkAccountDeleteForm').submit();
    }

    function openEditModal(btn) {
        document.getElementById('editForm').action = '{{ url('admin/manage-account') }}/' + btn.dataset.id;
        document.getElementById('editFirstName').value = btn.dataset.first || '';
        document.getElementById('editMiddleInitial').value = btn.dataset.middle || '';
        document.getElementById('editLastName').value = btn.dataset.last || '';
        document.getElementById('editEmail').value = btn.dataset.email || '';
        document.getElementById('editContact').value = btn.dataset.contact || '';
        document.getElementById('editRole').value = btn.dataset.role || 'employee';
        document.getElementById('editPassword').value = '';
        openModal('editModal');
    }

    function filterTable() {
        const role = document.getElementById('filterRole').value.toLowerCase();
        const status = document.getElementById('filterStatus').value;
        const search = document.getElementById('searchInput').value.trim().toLowerCase();

        document.querySelectorAll('#usersTableBody .user-row').forEach(function (row) {
            const roleMatch = !role || row.dataset.role === role;
            const statusMatch = !status || row.dataset.status === status;
            const searchMatch = !search || row.dataset.search.includes(search);
            row.style.display = (roleMatch && statusMatch && searchMatch) ? '' : 'none';
        });
    }

    function resetFilters() {
        document.getElementById('filterRole').value = '';
        document.getElementById('filterStatus').value = '';
        document.getElementById('searchInput').value = '';
        filterTable();
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeModal('createModal');
            closeModal('editModal');
        }
    });
</script>
@endsection
