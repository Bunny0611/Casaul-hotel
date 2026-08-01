@extends('employee.layout')

@section('content')
<div class="flex items-center justify-center">
    <div class="w-full max-w-6xl rounded-3xl border border-gray-200 bg-white p-8 shadow-lg">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
            <div class="card-hover rounded-2xl border-l-4 border-orange-500 bg-white p-6 shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm leading-relaxed text-gray-500">Today's<br>arrivals</p>
                        <h4 class="mt-2 text-3xl font-bold text-gray-800">18</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-orange-500"><i class="fas fa-user-plus text-xl"></i></div>
                </div>
            </div>

            <div class="card-hover rounded-2xl border-l-4 border-red-500 bg-white p-6 shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm leading-relaxed text-gray-500">Today's<br>departures</p>
                        <h4 class="mt-2 text-3xl font-bold text-gray-800">12</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-100 text-red-500"><i class="fas fa-sign-out-alt text-xl"></i></div>
                </div>
            </div>

            <div class="card-hover rounded-2xl border-l-4 border-emerald-500 bg-white p-6 shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm leading-relaxed text-gray-500">Available<br>rooms</p>
                        <h4 class="mt-2 text-3xl font-bold text-gray-800">26</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-500"><i class="fas fa-bed text-xl"></i></div>
                </div>
            </div>

            <div class="card-hover rounded-2xl border-l-4 border-yellow-500 bg-white p-6 shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm leading-relaxed text-gray-500">Pending<br>requests</p>
                        <h4 class="mt-2 text-3xl font-bold text-gray-800">09</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-yellow-100 text-yellow-500"><i class="fas fa-bell text-xl"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
