@extends('housekeeping.layout')

@section('content')


<main class="flex-1 overflow-y-auto p-6">


<div class="animate-fade-in">



<div class="flex justify-between items-center mb-6">


<h2 class="text-3xl font-bold text-gray-800">
Guest Requests
</h2>


<button
onclick="openRequestModal()"
class="btn-primary text-white px-5 py-3 rounded-lg">

<i class="fas fa-plus mr-2"></i>
Add Request

</button>


</div>





<!-- SUMMARY CARDS -->

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">



<div class="bg-white rounded-xl shadow-lg p-6 card-hover">

<p class="text-gray-500">
Pending Requests
</p>

<h2 class="text-3xl font-bold">
8
</h2>

</div>



<div class="bg-white rounded-xl shadow-lg p-6 card-hover">

<p class="text-gray-500">
Completed Requests
</p>

<h2 class="text-3xl font-bold">
24
</h2>

</div>



<div class="bg-white rounded-xl shadow-lg p-6 card-hover">

<p class="text-gray-500">
Total Requests
</p>

<h2 class="text-3xl font-bold">
32
</h2>

</div>


</div>





<!-- COMMON REQUESTS -->


<div class="bg-white rounded-xl shadow-lg p-6 mb-8">


<h3 class="text-lg font-semibold mb-5">
Common Guest Requests
</h3>



<div class="grid grid-cols-2 md:grid-cols-4 gap-5">


<div class="border rounded-xl p-5 text-center">

<i class="fas fa-bed text-blue-500 text-3xl"></i>

<p class="mt-3">
Extra Pillows
</p>

</div>


<div class="border rounded-xl p-5 text-center">

<i class="fas fa-tshirt text-green-500 text-3xl"></i>

<p class="mt-3">
Towels
</p>

</div>


<div class="border rounded-xl p-5 text-center">

<i class="fas fa-box text-orange-500 text-3xl"></i>

<p class="mt-3">
Amenities
</p>

</div>


<div class="border rounded-xl p-5 text-center">

<i class="fas fa-water text-purple-500 text-3xl"></i>

<p class="mt-3">
Drinking Water
</p>

</div>


</div>


</div>





<!-- REQUEST TABLE -->


<div class="bg-white rounded-xl shadow-lg p-6">


<h3 class="text-lg font-semibold mb-5">
Request Tracking
</h3>



<div class="overflow-x-auto">


<table class="w-full min-w-[900px]">


<thead>

<tr class="bg-gray-50">

<th class="px-6 py-3 text-left">
Room
</th>

<th class="px-6 py-3 text-left">
Guest
</th>

<th class="px-6 py-3 text-left">
Request
</th>

<th class="px-6 py-3 text-left">
Time
</th>

<th class="px-6 py-3 text-left">
Status
</th>

<th class="px-6 py-3 text-left">
Action
</th>

</tr>

</thead>



<tbody>


<tr class="border-b">


<td class="px-6 py-4">
Room 101
</td>

<td class="px-6 py-4">
John Smith
</td>

<td class="px-6 py-4">
Extra Pillows
</td>

<td class="px-6 py-4">
10:30 AM
</td>

<td class="px-6 py-4">

<span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full">
Pending
</span>

</td>

<td class="px-6 py-4">

<button class="bg-green-600 text-white px-4 py-2 rounded-lg">
Complete
</button>

</td>


</tr>



</tbody>


</table>


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
Add Guest Request
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
placeholder="Additional notes..."
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

Save Request

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