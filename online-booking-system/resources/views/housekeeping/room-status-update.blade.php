@extends('housekeeping.layout')

@section('content')


<style>

@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');


*{
    font-family:'Poppins',sans-serif;
}


.sidebar{

background:linear-gradient(
180deg,
#800000 0%,
#5c0000 100%
);

}



.header{

background:linear-gradient(
90deg,
#ff6b35 0%,
#ff8c42 100%
);

}



.nav-item:hover{

background:rgba(255,255,255,0.1);

}



.nav-item.active{

background:rgba(255,255,255,0.2);

border-left:4px solid #ff6b35;

}



.animate-fade-in{

animation:fade .5s ease-in-out;

}



@keyframes fade{

from{

opacity:0;
transform:translateY(10px);

}

to{

opacity:1;
transform:translateY(0);

}

}


</style>





<div class="flex h-screen overflow-hidden bg-gray-100">





<!-- SIDEBAR -->

<aside id="sidebar"
class="sidebar fixed lg:static -left-64 lg:left-0 top-0 z-50 w-64 h-screen text-white flex-shrink-0 transition-all duration-300">





<div class="p-6">


<h1 class="text-2xl font-bold tracking-wider">

<i class="fas fa-hotel mr-2"></i>

CASAUL

</h1>


<p class="text-sm text-gray-300">

Housekeeping

</p>


</div>






<nav class="mt-6">



<a href="{{ route('housekeeping.dashboard') }}"
class="nav-item flex items-center px-6 py-3">


<i class="fas fa-home w-6"></i>

Dashboard


</a>






<a href="{{ route('housekeeping.assigned-rooms') }}"
class="nav-item flex items-center px-6 py-3">


<i class="fas fa-bed w-6"></i>

Assigned Rooms


</a>






<a href="{{ route('housekeeping.room-status-update') }}"
class="nav-item active flex items-center px-6 py-3">


<i class="fas fa-sync-alt w-6"></i>

Room Status Update


</a>






<a href="{{ route('housekeeping.guest-requests') }}"
class="nav-item flex items-center px-6 py-3">


<i class="fas fa-bell w-6"></i>

Guest Requests


</a>






<a href="{{ route('housekeeping.maintenance-report') }}"
class="nav-item flex items-center px-6 py-3">


<i class="fas fa-tools w-6"></i>

Maintenance Report


</a>






<a href="{{ route('housekeeping.cleaning-history') }}"
class="nav-item flex items-center px-6 py-3">


<i class="fas fa-history w-6"></i>

Cleaning History


</a>



</nav>







<div class="absolute bottom-0 w-64 p-6">


<a href="{{ route('logout') }}"
class="flex items-center px-6 py-3 text-gray-300 hover:text-white">


<i class="fas fa-sign-out-alt w-6"></i>

Logout


</a>


</div>






</aside>









<!-- MAIN AREA -->


<div class="flex-1 flex flex-col overflow-hidden">







<!-- HEADER -->


<header class="header text-white px-4 md:px-6 py-4 shadow-lg">


<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">





<div class="flex items-center">



<button id="menuBtn"
class="lg:hidden text-2xl mr-4">

<i class="fas fa-bars"></i>

</button>





<h2 class="text-xl font-semibold">

Room Status Update

</h2>



</div>








<div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">





<div class="relative w-full sm:w-auto">


<input 
type="text"
placeholder="Search..."
class="bg-white/20 placeholder-gray-200 px-4 py-2 rounded-lg focus:outline-none w-full sm:w-64">



<i class="fas fa-search absolute right-3 top-3"></i>


</div>







<div class="flex items-center gap-2 bg-white/20 px-4 py-2 rounded-lg">


<i class="fas fa-user-circle text-2xl"></i>


<span>

Housekeeper

</span>


</div>






</div>






</div>


</header>









<!-- CONTENT -->


<main class="flex-1 overflow-y-auto p-4 md:p-6">



<div class="animate-fade-in">





<h2 class="text-3xl font-bold text-gray-800 mb-6">

Room Status Update

</h2>
<!-- WORKFLOW -->

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