@extends('admin.layout')

@section('content')
<div class="animate-fade-in">
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">Settings</h2>
            <p class="mt-2 text-sm text-slate-500">Manage your account details, application preferences, and security settings.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 shadow-sm">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[1.4fr_1fr]">
        <section class="rounded-[32px] bg-white p-8 shadow-xl border border-slate-200">
            <div class="mb-8 flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-3xl bg-orange-500 text-white shadow-sm">
                    <i class="fas fa-user-cog"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-semibold text-slate-900">Account Settings</h3>
                    <p class="text-sm text-slate-500">Open your account panel to update profile details and password.</p>
                </div>
            </div>

            <button id="openAccountSettingsModal" type="button" class="inline-flex items-center gap-2 rounded-3xl bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-500/20 transition hover:from-orange-600 hover:to-orange-700">
                <i class="fas fa-user-edit"></i> Open Account Settings
            </button>
        </section>

        <div class="space-y-6">
            <aside class="space-y-6">
                <div class="rounded-[32px] bg-white p-6 shadow-xl border border-slate-200">
                    <div class="mb-5 flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">General Settings</h3>
                            <p class="text-sm text-slate-500">Application preferences and notifications.</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="rounded-3xl bg-slate-50 p-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm font-medium text-slate-900">Maintenance Mode</p>
                                    <p class="text-sm text-slate-500">Temporarily disable public access.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="h-7 w-14 rounded-full bg-slate-200 transition peer-checked:bg-orange-500"></div>
                                    <span class="absolute left-1 top-1 h-5 w-5 rounded-full bg-white shadow transition peer-checked:translate-x-7"></span>
                                </label>
                            </div>
                        </div>
                        <div class="rounded-3xl bg-slate-50 p-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm font-medium text-slate-900">Email Notifications</p>
                                    <p class="text-sm text-slate-500">Receive reservation alerts and updates.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div class="h-7 w-14 rounded-full bg-slate-200 transition peer-checked:bg-orange-500"></div>
                                    <span class="absolute left-1 top-1 h-5 w-5 rounded-full bg-white shadow transition peer-checked:translate-x-7"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-[32px] bg-white p-6 shadow-xl border border-slate-200">
                    <div class="mb-5">
                        <h3 class="text-lg font-semibold text-slate-900">Appearance & Security</h3>
                        <p class="text-sm text-slate-500">Customize layout and security behavior.</p>
                    </div>
                    <div class="grid gap-4">
                        <div class="rounded-3xl bg-slate-50 p-4">
                            <p class="text-sm font-medium text-slate-900">Theme</p>
                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                <button class="rounded-3xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 text-left hover:border-orange-300">Light Mode</button>
                                <button class="rounded-3xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 text-left hover:border-orange-300">Dark Mode</button>
                            </div>
                        </div>
                        <div class="rounded-3xl bg-slate-50 p-4">
                            <p class="text-sm font-medium text-slate-900">Security</p>
                            <div class="mt-4 space-y-3">
                                <div class="flex items-center justify-between rounded-3xl bg-white p-4 border border-slate-200">
                                    <div>
                                        <p class="text-sm font-medium text-slate-900">Two-factor Authentication</p>
                                        <p class="text-xs text-slate-500">Protect your admin login.</p>
                                    </div>
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Enabled</span>
                                </div>
                                <div class="flex items-center justify-between rounded-3xl bg-white p-4 border border-slate-200">
                                    <div>
                                        <p class="text-sm font-medium text-slate-900">Auto Logout</p>
                                        <p class="text-xs text-slate-500">Automatically sign out idle sessions.</p>
                                    </div>
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">30 min</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>

<div id="accountSettingsModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 px-4 py-8">
    <div class="w-full max-w-3xl rounded-[32px] bg-white p-8 shadow-2xl border border-slate-200">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-semibold text-slate-900">Account Settings</h3>
                <p class="text-sm text-slate-500">Update your profile details and password.</p>
            </div>
            <button id="closeAccountSettingsModal" type="button" class="rounded-full bg-slate-100 p-3 text-slate-600 transition hover:bg-slate-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.settings.account') }}" class="space-y-6">
            @csrf
            <div class="grid gap-6 lg:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Admin Name</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                        class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200">
                    @error('name')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                        class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200">
                    @error('email')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Current Password</label>
                    <input type="password" name="current_password" required
                        class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200" placeholder="Enter your current password">
                    @error('current_password')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">New Password</label>
                    <input type="password" name="password"
                        class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200" placeholder="Leave blank to keep current">
                    @error('password')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Confirm New Password</label>
                <input type="password" name="password_confirmation"
                    class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200" placeholder="Repeat new password">
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <button id="cancelAccountSettingsModal" type="button" class="rounded-3xl border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Cancel</button>
                <button type="submit" class="inline-flex items-center justify-center rounded-3xl bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-500/20 transition hover:from-orange-600 hover:to-orange-700">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('accountSettingsModal');
    const openButton = document.getElementById('openAccountSettingsModal');
    const closeButton = document.getElementById('closeAccountSettingsModal');
    const cancelButton = document.getElementById('cancelAccountSettingsModal');

    openButton.addEventListener('click', () => modal.classList.remove('hidden'));
    closeButton.addEventListener('click', () => modal.classList.add('hidden'));
    cancelButton.addEventListener('click', () => modal.classList.add('hidden'));
</script>
</div>
@endsection

