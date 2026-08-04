@extends('housekeeping.layout')

@section('content')



<!-- CONTENT -->

<main class="flex-1 overflow-y-auto p-4 md:p-6">


<div class="animate-fade-in">


<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">


<h2 class="text-3xl font-bold text-gray-800">

Maintenance Report

</h2>


<button
onclick="openReportModal()"
class="btn-primary text-white px-5 py-3 rounded-lg">

<i class="fas fa-plus mr-2"></i>

Create Report

</button>


</div>
<!-- SUMMARY CARDS -->

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">


<!-- TOTAL REPORTS -->

<div class="bg-white rounded-xl shadow-lg p-6">

<div class="flex justify-between items-center">

<div>

<p class="text-gray-500 text-sm">
Total Reports
</p>

<h2 class="text-3xl font-bold text-gray-800">
2
</h2>

</div>


<div class="bg-blue-100 p-4 rounded-full">

<i class="fas fa-file-alt text-blue-600 text-xl"></i>

</div>


</div>

</div>





<!-- PENDING -->

<div class="bg-white rounded-xl shadow-lg p-6">

<div class="flex justify-between items-center">

<div>

<p class="text-gray-500 text-sm">
Pending Issues
</p>

<h2 class="text-3xl font-bold text-gray-800">
1
</h2>

</div>


<div class="bg-yellow-100 p-4 rounded-full">

<i class="fas fa-clock text-yellow-600 text-xl"></i>

</div>


</div>

</div>





<!-- REPAIR -->

<div class="bg-white rounded-xl shadow-lg p-6">

<div class="flex justify-between items-center">

<div>

<p class="text-gray-500 text-sm">
Under Repair
</p>

<h2 class="text-3xl font-bold text-gray-800">
1
</h2>

</div>


<div class="bg-orange-100 p-4 rounded-full">

<i class="fas fa-wrench text-orange-600 text-xl"></i>

</div>


</div>

</div>





<!-- COMPLETED -->

<div class="bg-white rounded-xl shadow-lg p-6">

<div class="flex justify-between items-center">

<div>

<p class="text-gray-500 text-sm">
Completed
</p>

<h2 class="text-3xl font-bold text-gray-800">
0
</h2>

</div>


<div class="bg-green-100 p-4 rounded-full">

<i class="fas fa-check-circle text-green-600 text-xl"></i>

</div>


</div>

</div>



</div>






<!-- MAINTENANCE TABLE -->


<div class="bg-white rounded-xl shadow-lg p-6">


<div class="overflow-x-auto">


<table class="w-full min-w-[900px]">


<thead>


<tr class="bg-gray-50">


<th class="px-6 py-3 text-left text-xs uppercase text-gray-500">
Room
</th>


<th class="px-6 py-3 text-left text-xs uppercase text-gray-500">
Problem
</th>


<th class="px-6 py-3 text-left text-xs uppercase text-gray-500">
Priority
</th>


<th class="px-6 py-3 text-left text-xs uppercase text-gray-500">
Date Reported
</th>


<th class="px-6 py-3 text-left text-xs uppercase text-gray-500">
Status
</th>


<th class="px-6 py-3 text-left text-xs uppercase text-gray-500">
Action
</th>


</tr>


</thead>





<tbody class="divide-y">



<tr class="hover:bg-gray-50 transition">


<td class="px-6 py-4 font-semibold">

Room 101

</td>



<td class="px-6 py-4">


<p class="font-medium">

Air Conditioner

</p>


<p class="text-xs text-gray-500">

Not cooling properly

</p>


</td>




<td class="px-6 py-4">


<span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs">

High

</span>


</td>





<td class="px-6 py-4 text-gray-500">

July 31, 2026

</td>





<td class="px-6 py-4">


<span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs">

Pending

</span>


</td>





<td class="px-6 py-4">


<button class="text-blue-600 hover:text-blue-800">

View

</button>


</td>



</tr>







<tr class="hover:bg-gray-50 transition">


<td class="px-6 py-4 font-semibold">

Room 205

</td>




<td class="px-6 py-4">


<p class="font-medium">

Bathroom Faucet

</p>


<p class="text-xs text-gray-500">

Water leakage

</p>


</td>





<td class="px-6 py-4">


<span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs">

Medium

</span>


</td>





<td class="px-6 py-4 text-gray-500">

July 30, 2026

</td>





<td class="px-6 py-4">


<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs">

Repairing

</span>


</td>





<td class="px-6 py-4">


<button class="text-blue-600 hover:text-blue-800">

View

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







<!-- CREATE MAINTENANCE REPORT MODAL -->


<div id="reportModal"
class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">


<div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">



<!-- MODAL HEADER -->

<div class="flex justify-between items-center px-6 py-4 border-b">


<h2 class="text-2xl font-bold text-gray-800">

Create Maintenance Report

</h2>


<button onclick="closeReportModal()"
class="text-3xl text-gray-500 hover:text-red-600">

&times;

</button>


</div>







<form class="p-6 space-y-6">





<!-- ROOM INFORMATION -->


<h3 class="font-semibold text-gray-700">

Room Information

</h3>



<div class="grid grid-cols-1 md:grid-cols-3 gap-5">



<div>

<label class="text-sm text-gray-600">

Room Number

</label>


<select class="w-full mt-2 border rounded-lg px-4 py-2">

<option>
Room 101
</option>

<option>
Room 205
</option>

<option>
Room 302
</option>

</select>


</div>





<div>

<label class="text-sm text-gray-600">

Room Type

</label>


<select class="w-full mt-2 border rounded-lg px-4 py-2">


<option>
Deluxe Room
</option>


<option>
Suite Room
</option>


<option>
Standard Room
</option>


</select>


</div>





<div>

<label class="text-sm text-gray-600">

Reported By

</label>


<input
type="text"
placeholder="Housekeeper Name"
class="w-full mt-2 border rounded-lg px-4 py-2">


</div>


</div>








<!-- ISSUE DETAILS -->


<h3 class="font-semibold text-gray-700">

Issue Details

</h3>



<div class="grid grid-cols-1 md:grid-cols-2 gap-5">



<div>


<label class="text-sm text-gray-600">

Maintenance Category

</label>


<select class="w-full mt-2 border rounded-lg px-4 py-2">


<option>
Air Conditioning
</option>


<option>
Electrical
</option>


<option>
Plumbing
</option>


<option>
Furniture
</option>


<option>
Appliance
</option>


<option>
Others
</option>


</select>


</div>





<div>


<label class="text-sm text-gray-600">

Priority Level

</label>


<select class="w-full mt-2 border rounded-lg px-4 py-2">


<option>
Low
</option>


<option>
Medium
</option>


<option>
High
</option>


<option>
Urgent
</option>


</select>


</div>


</div>







<div>


<label class="text-sm text-gray-600">

Problem Description

</label>


<textarea
rows="4"
placeholder="Example: Air conditioner is not cooling properly."
class="w-full mt-2 border rounded-lg px-4 py-3"></textarea>


</div>









<!-- REPAIR SCHEDULE -->


<h3 class="font-semibold text-gray-700">

Repair Schedule

</h3>



<div class="grid grid-cols-1 md:grid-cols-3 gap-5">



<div>


<label class="text-sm text-gray-600">

Date Reported

</label>


<input
type="date"
class="w-full mt-2 border rounded-lg px-4 py-2">


</div>





<div>


<label class="text-sm text-gray-600">

Expected Repair Date

</label>


<input
type="date"
class="w-full mt-2 border rounded-lg px-4 py-2">


</div>





<div>


<label class="text-sm text-gray-600">

Assigned Technician

</label>


<input
type="text"
placeholder="Technician Name"
class="w-full mt-2 border rounded-lg px-4 py-2">


</div>


</div>









<!-- STATUS -->


<div>


<label class="text-sm text-gray-600">

Current Status

</label>


<select class="w-full mt-2 border rounded-lg px-4 py-2">


<option>
Pending
</option>


<option>
In Progress
</option>


<option>
Completed
</option>


</select>


</div>








<!-- BUTTONS -->


<div class="flex flex-col sm:flex-row justify-end gap-3">


<button
type="button"
onclick="closeReportModal()"
class="px-6 py-3 border rounded-lg">


Cancel


</button>





<button
type="submit"
class="bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700">


<i class="fas fa-save mr-2"></i>


Submit Report


</button>


</div>



</form>


</div>


</div>







<script>


function openReportModal(){


document.getElementById("reportModal")
.classList.remove("hidden");


document.getElementById("reportModal")
.classList.add("flex");


}



function closeReportModal(){


document.getElementById("reportModal")
.classList.remove("flex");


document.getElementById("reportModal")
.classList.add("hidden");


}




// MOBILE SIDEBAR TOGGLE

document.getElementById("menuBtn")
?.addEventListener("click",function(){


document.getElementById("sidebar")
.classList.toggle("-left-64");


document.getElementById("sidebar")
.classList.toggle("left-0");


});



</script>



@endsection