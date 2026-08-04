@extends('housekeeping.layout')

@section('content')




<main class="flex-1 overflow-y-auto p-4 md:p-6">



<div class="animate-fade-in">





<h2 class="text-3xl font-bold text-gray-800 mb-6">

Room Status Update

</h2>


<div class="bg-white rounded-xl shadow-lg p-6 mb-8">


<h3 class="text-lg font-semibold text-gray-800 mb-6">

Room Cleaning Workflow

</h3>





<div class="flex flex-col md:flex-row items-center justify-between gap-5">





<div class="flex flex-col items-center">


<div class="bg-red-100 p-4 rounded-full">


<i class="fas fa-trash text-red-600 text-xl"></i>


</div>


<p class="mt-2 font-semibold">

Dirty

</p>


</div>







<i class="fas fa-arrow-right text-gray-400 hidden md:block"></i>







<div class="flex flex-col items-center">


<div class="bg-yellow-100 p-4 rounded-full">


<i class="fas fa-broom text-yellow-600 text-xl"></i>


</div>


<p class="mt-2 font-semibold">

Cleaning

</p>


</div>







<i class="fas fa-arrow-right text-gray-400 hidden md:block"></i>







<div class="flex flex-col items-center">


<div class="bg-green-100 p-4 rounded-full">


<i class="fas fa-check text-green-600 text-xl"></i>


</div>


<p class="mt-2 font-semibold">

Cleaned

</p>


</div>







<i class="fas fa-arrow-right text-gray-400 hidden md:block"></i>







<div class="flex flex-col items-center">


<div class="bg-blue-100 p-4 rounded-full">


<i class="fas fa-user-check text-blue-600 text-xl"></i>


</div>


<p class="mt-2 font-semibold">

Inspected

</p>


</div>







<i class="fas fa-arrow-right text-gray-400 hidden md:block"></i>







<div class="flex flex-col items-center">


<div class="bg-purple-100 p-4 rounded-full">


<i class="fas fa-door-open text-purple-600 text-xl"></i>


</div>


<p class="mt-2 font-semibold">

Available

</p>


</div>






</div>


</div>









<!-- UPDATE STATUS FORM -->


<div class="bg-white rounded-xl shadow-lg p-6 mb-8">



<h3 class="text-lg font-semibold text-gray-800 mb-5">

Update Room Status

</h3>






<form>





<div class="grid grid-cols-1 md:grid-cols-3 gap-6">





<div>


<label class="text-sm text-gray-600">

Select Room

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

Current Status

</label>




<select class="w-full mt-2 border rounded-lg px-4 py-2">



<option>
Dirty
</option>



<option>
Cleaning
</option>



<option>
Cleaned
</option>



<option>
Inspected
</option>



<option>
Available
</option>



</select>



</div>







<div>



<label class="text-sm text-gray-600">

Update Status

</label>




<select class="w-full mt-2 border rounded-lg px-4 py-2">



<option>
Dirty
</option>



<option>
Cleaning
</option>



<option>
Cleaned
</option>



<option>
Inspected
</option>



<option>
Available
</option>



</select>



</div>






</div>








<button
class="mt-6 header text-white px-6 py-3 rounded-lg hover:opacity-90">



<i class="fas fa-sync mr-2"></i>


Update Status



</button>







</form>






</div>
<!-- ROOM STATUS MONITORING -->


<div class="bg-white rounded-xl shadow-lg p-6">



<h3 class="text-lg font-semibold text-gray-800 mb-5">

Room Status Monitoring

</h3>






<div class="overflow-x-auto">



<table class="w-full min-w-[700px]">





<thead>


<tr class="bg-gray-50">





<th class="px-6 py-3 text-left text-xs uppercase text-gray-500">

Room

</th>





<th class="px-6 py-3 text-left text-xs uppercase text-gray-500">

Room Type

</th>






<th class="px-6 py-3 text-left text-xs uppercase text-gray-500">

Status

</th>







<th class="px-6 py-3 text-left text-xs uppercase text-gray-500">

Last Updated

</th>





</tr>



</thead>









<tbody class="divide-y divide-gray-200">







<tr class="hover:bg-gray-50">


<td class="px-6 py-4 font-semibold">

101

</td>





<td class="px-6 py-4">

Deluxe Room

</td>





<td class="px-6 py-4">


<span class="px-3 py-1 rounded-full text-xs bg-red-100 text-red-700">

Dirty

</span>


</td>





<td class="px-6 py-4 text-gray-500">

Jul 31, 2026 09:30 AM

</td>



</tr>









<tr class="hover:bg-gray-50">


<td class="px-6 py-4 font-semibold">

205

</td>





<td class="px-6 py-4">

Suite Room

</td>





<td class="px-6 py-4">


<span class="px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">

Cleaning

</span>


</td>





<td class="px-6 py-4 text-gray-500">

Jul 31, 2026 10:15 AM

</td>



</tr>









<tr class="hover:bg-gray-50">


<td class="px-6 py-4 font-semibold">

302

</td>





<td class="px-6 py-4">

Standard Room

</td>





<td class="px-6 py-4">


<span class="px-3 py-1 rounded-full text-xs bg-purple-100 text-purple-700">

Available

</span>


</td>





<td class="px-6 py-4 text-gray-500">

Jul 31, 2026 11:00 AM

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







<!-- MOBILE SIDEBAR SCRIPT -->

<script>


document.addEventListener("DOMContentLoaded", function(){



const menuBtn = document.getElementById("menuBtn");

const sidebar = document.getElementById("sidebar");



if(menuBtn){


menuBtn.addEventListener("click", function(){



if(sidebar.classList.contains("-left-64")){


sidebar.classList.remove("-left-64");

sidebar.classList.add("left-0");


}else{


sidebar.classList.remove("left-0");

sidebar.classList.add("-left-64");


}



});


}



});



</script>





@endsection