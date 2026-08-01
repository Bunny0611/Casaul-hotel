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


<p class="text-sm text-gray-300 mt-1">

Housekeeping

</p>


</div>







<nav class="mt-6">



<a href="{{ route('housekeeping.dashboard') }}"
class="nav-item flex items-center px-6 py-3">


<i class="fas fa-home w-6"></i>

<span>
Dashboard
</span>


</a>







<a href="{{ route('housekeeping.assigned-rooms') }}"
class="nav-item flex items-center px-6 py-3">


<i class="fas fa-bed w-6"></i>

<span>
Assigned Rooms
</span>


</a>








<a href="{{ route('housekeeping.room-status-update') }}"
class="nav-item flex items-center px-6 py-3">


<i class="fas fa-sync-alt w-6"></i>

<span>
Room Status Update
</span>


</a>








<a href="{{ route('housekeeping.guest-requests') }}"
class="nav-item flex items-center px-6 py-3">


<i class="fas fa-bell w-6"></i>

<span>
Guest Requests
</span>


</a>








<a href="{{ route('housekeeping.maintenance-report') }}"
class="nav-item flex items-center px-6 py-3">


<i class="fas fa-tools w-6"></i>

<span>
Maintenance Report
</span>


</a>








<a href="{{ route('housekeeping.cleaning-history') }}"
class="nav-item active flex items-center px-6 py-3">


<i class="fas fa-history w-6"></i>

<span>
Cleaning History
</span>


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

Welcome to CASAUL Housekeeping

</h2>


</div>







<div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">






<div class="relative w-full sm:w-auto">


<input 
type="text"
placeholder="Search..."
class="bg-white/20 text-white placeholder-gray-200 px-4 py-2 rounded-lg focus:outline-none w-full sm:w-64">


<i class="fas fa-search absolute right-3 top-3 text-gray-200"></i>


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






<div class="flex justify-between items-center mb-6">


<h2 class="text-3xl font-bold text-gray-800">

Cleaning History

</h2>



</div>
<!-- STATISTICS CARDS -->


<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">





<!-- COMPLETED TASKS -->


<div class="bg-white rounded-xl shadow-lg p-6">


<div class="flex justify-between items-center">


<div>


<p class="text-gray-500 text-sm">

Completed Tasks

</p>



<h2 class="text-3xl font-bold text-gray-800">

45

</h2>



<p class="text-xs text-green-500 mt-2">


<i class="fas fa-check-circle mr-1"></i>


Successfully cleaned rooms


</p>



</div>





<div class="bg-green-100 p-4 rounded-full">


<i class="fas fa-broom text-green-600 text-xl"></i>


</div>





</div>


</div>









<!-- THIS WEEK -->


<div class="bg-white rounded-xl shadow-lg p-6">


<div class="flex justify-between items-center">


<div>



<p class="text-gray-500 text-sm">

This Week

</p>




<h2 class="text-3xl font-bold text-gray-800">

18

</h2>




<p class="text-xs text-blue-500 mt-2">


<i class="fas fa-calendar mr-1"></i>


Completed cleaning


</p>



</div>





<div class="bg-blue-100 p-4 rounded-full">


<i class="fas fa-calendar-check text-blue-600 text-xl"></i>


</div>





</div>


</div>









<!-- AVERAGE TIME -->


<div class="bg-white rounded-xl shadow-lg p-6">


<div class="flex justify-between items-center">


<div>



<p class="text-gray-500 text-sm">

Average Cleaning Time

</p>




<h2 class="text-3xl font-bold text-gray-800">

35 mins

</h2>




<p class="text-xs text-purple-500 mt-2">


<i class="fas fa-clock mr-1"></i>


Per room


</p>



</div>





<div class="bg-purple-100 p-4 rounded-full">


<i class="fas fa-stopwatch text-purple-600 text-xl"></i>


</div>





</div>


</div>






</div>









<!-- SEARCH AND FILTER -->


<div class="bg-white rounded-xl shadow-lg p-6 mb-8">



<h3 class="text-lg font-semibold text-gray-800 mb-5">

Search Cleaning Records

</h3>







<div class="grid grid-cols-1 md:grid-cols-4 gap-5">






<div>


<label class="text-sm text-gray-600">

Search Room

</label>




<input 
type="text"
placeholder="Room number"
class="w-full mt-2 border rounded-lg px-4 py-2">



</div>









<div>



<label class="text-sm text-gray-600">

Date

</label>





<input 
type="date"
class="w-full mt-2 border rounded-lg px-4 py-2">



</div>









<div>



<label class="text-sm text-gray-600">

Staff

</label>






<select class="w-full mt-2 border rounded-lg px-4 py-2">


<option>

All Staff

</option>


<option>

Maria Santos

</option>


<option>

John Cruz

</option>


<option>

Anna Reyes

</option>


</select>






</div>









<div class="flex items-end">



<button 
class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white px-5 py-2 rounded-lg hover:opacity-90">



<i class="fas fa-search mr-2"></i>


Filter



</button>




</div>






</div>




</div>
<!-- CLEANING HISTORY TABLE -->


<div class="bg-white rounded-xl shadow-lg p-6">





<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-5 gap-3">


<h3 class="text-lg font-semibold text-gray-800">

Completed Cleaning Records

</h3>




<span class="text-sm text-gray-500">

Total Records: 45

</span>



</div>







<div class="overflow-x-auto">



<table class="w-full min-w-[850px]">





<thead>


<tr class="bg-gray-50">





<th class="px-6 py-3 text-left text-xs uppercase text-gray-500">

Room

</th>





<th class="px-6 py-3 text-left text-xs uppercase text-gray-500">

Task

</th>





<th class="px-6 py-3 text-left text-xs uppercase text-gray-500">

Assigned Staff

</th>





<th class="px-6 py-3 text-left text-xs uppercase text-gray-500">

Date

</th>





<th class="px-6 py-3 text-left text-xs uppercase text-gray-500">

Time

</th>





<th class="px-6 py-3 text-left text-xs uppercase text-gray-500">

Status

</th>





</tr>


</thead>








<tbody class="divide-y divide-gray-200">







<!-- ROOM 101 -->


<tr class="hover:bg-gray-50 transition">





<td class="px-6 py-4">



<div class="font-semibold text-gray-800">

Room 101

</div>



<div class="text-xs text-gray-500">

Deluxe Room

</div>



</td>






<td class="px-6 py-4">

Deep Cleaning

</td>






<td class="px-6 py-4">



<div class="flex items-center gap-2">



<div class="bg-gray-100 rounded-full p-2">


<i class="fas fa-user text-gray-500"></i>


</div>



Maria Santos



</div>



</td>







<td class="px-6 py-4 text-gray-500">

Jul 31, 2026

</td>







<td class="px-6 py-4 text-gray-500">

09:30 AM

</td>








<td class="px-6 py-4">



<span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-700">

Completed

</span>



</td>






</tr>









<!-- ROOM 205 -->


<tr class="hover:bg-gray-50 transition">





<td class="px-6 py-4">



<div class="font-semibold text-gray-800">

Room 205

</div>



<div class="text-xs text-gray-500">

Suite Room

</div>



</td>






<td class="px-6 py-4">

Linen Replacement

</td>






<td class="px-6 py-4">



<div class="flex items-center gap-2">



<div class="bg-gray-100 rounded-full p-2">


<i class="fas fa-user text-gray-500"></i>


</div>



John Cruz



</div>



</td>







<td class="px-6 py-4 text-gray-500">

Jul 30, 2026

</td>







<td class="px-6 py-4 text-gray-500">

02:15 PM

</td>








<td class="px-6 py-4">



<span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-700">

Completed

</span>



</td>






</tr>









<!-- ROOM 302 -->


<tr class="hover:bg-gray-50 transition">





<td class="px-6 py-4">



<div class="font-semibold text-gray-800">

Room 302

</div>



<div class="text-xs text-gray-500">

Standard Room

</div>



</td>






<td class="px-6 py-4">

General Cleaning

</td>






<td class="px-6 py-4">



<div class="flex items-center gap-2">



<div class="bg-gray-100 rounded-full p-2">


<i class="fas fa-user text-gray-500"></i>


</div>



Anna Reyes



</div>



</td>







<td class="px-6 py-4 text-gray-500">

Jul 29, 2026

</td>







<td class="px-6 py-4 text-gray-500">

10:00 AM

</td>








<td class="px-6 py-4">



<span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-700">

Completed

</span>



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