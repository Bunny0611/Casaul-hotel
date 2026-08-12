@extends('housekeeping.layout')

@section('content')

@php
$stats = [
    'pending' => 0,
    'resolved' => 0,
    'total' => 0,
];
@endphp

<main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-slate-50">

<div class="animate-fade-in max-w-7xl mx-auto space-y-6">

<div class="rounded-3xl bg-gradient-to-r from-slate-800 via-slate-700 to-slate-600 text-white p-5 sm:p-8 shadow-xl">
<div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
<div>
<p class="text-xs sm:text-sm uppercase tracking-[0.2em] text-slate-300">Housekeeping Desk</p>
<h2 class="text-2xl sm:text-3xl font-bold mt-2">Guest Requests</h2>
<p class="text-sm sm:text-base text-slate-200 mt-2">A clear view of guest messages and service needs for the day.</p>
</div>
<div class="bg-white/10 backdrop-blur-sm rounded-2xl px-4 py-3 border border-white/10 min-w-[140px] text-center">
<p class="text-sm text-slate-300">Active requests</p>
<p class="text-2xl font-semibold">{{ $stats['pending'] }}</p>
</div>
</div>
</div>





<!-- SUMMARY CARDS -->

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">



<div class="bg-gradient-to-br from-amber-50 to-white rounded-2xl shadow-lg p-5 sm:p-6 card-hover border border-amber-100">

<p class="text-gray-500 text-sm uppercase tracking-wide">
Pending Messages
</p>

<h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mt-2">
{{ $stats['pending'] }}
</h2>

</div>



<div class="bg-gradient-to-br from-emerald-50 to-white rounded-2xl shadow-lg p-5 sm:p-6 card-hover border border-emerald-100">

<p class="text-gray-500 text-sm uppercase tracking-wide">
Resolved Messages
</p>

<h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mt-2">
{{ $stats['resolved'] }}
</h2>

</div>



<div class="bg-gradient-to-br from-sky-50 to-white rounded-2xl shadow-lg p-5 sm:p-6 card-hover border border-sky-100">

<p class="text-gray-500 text-sm uppercase tracking-wide">
Total Messages
</p>

<h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mt-2">
{{ $stats['total'] }}
</h2>

</div>


</div>


<div class="grid grid-cols-1 xl:grid-cols-[1.2fr_0.8fr] gap-4 sm:gap-6">

<div class="bg-white rounded-3xl shadow-lg p-5 sm:p-6 border border-gray-100">
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-5">
<h3 class="text-lg font-semibold text-gray-800">Guest Message Box</h3>
<span class="text-sm text-gray-500">Today</span>
</div>

<div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-6 sm:p-8 text-center">
<p class="text-base sm:text-lg font-semibold text-gray-700">No guest messages yet</p>
<p class="text-sm text-gray-500 mt-2">Requests from guests will appear here once they arrive.</p>
</div>
</div>

<div class="bg-gradient-to-br from-slate-50 to-white rounded-3xl shadow-lg p-5 sm:p-6 border border-gray-100">
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-5">
<h3 class="text-lg font-semibold text-gray-800">Notification Center</h3>
<span class="text-sm text-gray-500">Live</span>
</div>

<div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-6 sm:p-8 text-center">
<p class="text-base sm:text-lg font-semibold text-gray-700">No notifications yet</p>
<p class="text-sm text-gray-500 mt-2">Housekeeping notifications will appear here when needed.</p>
</div>
</div>

</div>





</div>


</main>


</div>


</div>

<!-- ADD REQUEST MODAL -->

<div id="requestModal"
class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-6">


<div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">


<div class="flex justify-between items-center px-6 py-4 border-b">


<h2 class="text-2xl font-bold">
Send New Guest Message
</h2>


<button onclick="closeRequestModal()"
class="text-3xl text-gray-500 hover:text-red-600">

&times;

</button>


</div>


<form class="p-6 space-y-5">


<div class="grid grid-cols-1 md:grid-cols-2 gap-5">


<input 
type="text"
placeholder="Guest Name"
class="border rounded-lg px-4 py-2">


<input
type="text"
placeholder="Room Number"
class="border rounded-lg px-4 py-2">


</div>



<select class="w-full border rounded-lg px-4 py-2">

<option>Extra Pillows</option>
<option>Towels</option>
<option>Amenities</option>
<option>Drinking Water</option>

</select>




<textarea
rows="4"
placeholder="Write the guest's message here..."
class="w-full border rounded-lg px-4 py-3"></textarea>




<div class="flex justify-end gap-3">


<button
type="button"
onclick="closeRequestModal()"
class="border px-6 py-2 rounded-lg">

Cancel

</button>



<button
class="btn-primary text-white px-6 py-2 rounded-lg">

<i class="fas fa-save mr-2"></i>

Send Message

</button>


</div>


</form>


</div>


</div>



<script>

function openRequestModal(){

document.getElementById("requestModal")
.classList.remove("hidden");

document.getElementById("requestModal")
.classList.add("flex");

}


function closeRequestModal(){

document.getElementById("requestModal")
.classList.remove("flex");

document.getElementById("requestModal")
.classList.add("hidden");

}

</script>


@endsection