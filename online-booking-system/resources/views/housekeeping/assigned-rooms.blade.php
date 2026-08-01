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

    <aside id="sidebar" class="sidebar fixed lg:static -left-64 lg:left-0 top-0 z-50 w-64 h-screen text-white flex-shrink-0 transition-all duration-300">

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
        <a href="{{ route('housekeeping.dashboard') }}" class="nav-item flex items-center px-6 py-3">
            <i class="fas fa-home w-6"></i>
                <span>
                Dashboard
                </span>
        </a>

        <a href="{{ route('housekeeping.assigned-rooms') }}" class="nav-item active flex items-center px-6 py-3">
            <i class="fas fa-bed w-6"></i>
                <span>
                Assigned Rooms
                </span>
         </a>

        <a href="{{ route('housekeeping.room-status-update') }}" class="nav-item flex items-center px-6 py-3">
            <i class="fas fa-sync-alt w-6"></i>

                <span>
                Room Status Update
                </span>
        </a>

        <a href="{{ route('housekeeping.guest-requests') }}" class="nav-item flex items-center px-6 py-3">
            <i class="fas fa-bell w-6"></i>
                <span>
                Guest Requests
                </span>
        </a>

        <a href="{{ route('housekeeping.maintenance-report') }}" class="nav-item flex items-center px-6 py-3">
            <i class="fas fa-tools w-6"></i>
                <span>
                Maintenance Report
                </span>
        </a>

        <a href="{{ route('housekeeping.cleaning-history') }}" class="nav-item flex items-center px-6 py-3">
            <i class="fas fa-history w-6"></i>
                <span>
                    Cleaning History
                </span>
        </a>

    </nav>

    <div class="absolute bottom-0 w-64 p-6">
        <a href="{{ route('logout') }}" class="flex items-center px-6 py-3 text-gray-300 hover:text-white">
            <i class="fas fa-sign-out-alt w-6"></i>
                Logout
        </a>
    </div>

</aside>

<div class="flex-1 flex flex-col overflow-hidden">


<header class="header text-white px-4 md:px-6 py-4 shadow-lg">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center">
            <button id="menuBtn" class="lg:hidden text-2xl mr-4">
                <i class="fas fa-bars"></i>
            </button>
            <h2 class="text-xl font-semibold">
                Welcome to CASAUL Housekeeping
            </h2>
        </div>

     <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
        <div class="relative w-full sm:w-64">
            <input type="text" placeholder="Search..." class="bg-white/20 text-white placeholder-gray-200 px-4 py-2 rounded-lg focus:outline-none w-full">
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

<main class="flex-1 overflow-y-auto p-4 md:p-6">
        <div class="animate-fade-in">
            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-6">
                <h2 class="text-3xl font-bold text-gray-800">
                    Assigned Rooms
                </h2>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <select class="border rounded-lg px-4 py-2 h-11">

                    <option>
                    All Priority
                    </option>

                    <option>
                    High
                    </option>

                    <option>
                    Medium
                    </option>

                    <option>
                    Low
                    </option>

                </select>

            <button onclick="openModal()" class="h-11 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-5 rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-300 shadow-lg flex items-center justify-center">
                <i class="fas fa-plus mr-2"></i>
                    Assign Room Task
            </button>
        </div>
    </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-5">
                <h3 class="text-lg font-semibold text-gray-800">
                    Assigned Cleaning Tasks
                </h3>



<span class="text-sm text-gray-500">

Total: 4 Rooms

</span>


</div>





<div class="overflow-x-auto">


<table class="w-full min-w-[700px]">



<thead>


<tr class="bg-gray-50">


<th class="px-6 py-3 text-left text-xs uppercase text-gray-500">

Room

</th>


<th class="px-6 py-3 text-left text-xs uppercase text-gray-500">

Task

</th>


<th class="px-6 py-3 text-left text-xs uppercase text-gray-500">

Priority

</th>


<th class="px-6 py-3 text-left text-xs uppercase text-gray-500">

Status

</th>


<th class="px-6 py-3 text-left text-xs uppercase text-gray-500">

Action

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


<span class="px-3 py-1 rounded-full text-xs bg-red-100 text-red-700">

High

</span>


</td>





<td class="px-6 py-4">


<span class="px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">

Pending

</span>


</td>





<td class="px-6 py-4">


<button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">


Start Cleaning


</button>


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

Change Linens

</td>





<td class="px-6 py-4">


<span class="px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">

Medium

</span>


</td>





<td class="px-6 py-4">


<span class="px-3 py-1 rounded-full text-xs bg-blue-100 text-blue-700">

Cleaning

</span>


</td>





<td class="px-6 py-4">


<button class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">

Complete

</button>


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


<span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-700">

Low

</span>


</td>





<td class="px-6 py-4">


<span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-700">

Completed

</span>


</td>





<td class="px-6 py-4">


<span class="text-green-600 text-sm font-medium">

Finished

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

<div id="assignModal"
class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">


    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-y-auto">


        <!-- Header -->

        <div class="flex justify-between items-center px-6 py-4 border-b">


            <h2 class="text-2xl font-bold text-gray-800">

                Assign Cleaning Task

            </h2>


            <button onclick="closeModal()"
            class="text-2xl text-gray-500 hover:text-red-600">

                &times;

            </button>


        </div>





        <form class="p-6">


            <!-- Guest Information -->


            <h3 class="font-semibold text-gray-700 mb-4">

                Guest Information

            </h3>


            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">


                <div>

                    <label>Guest Name</label>

                    <input type="text"
                    placeholder="John Smith"
                    class="w-full border rounded-lg px-4 py-2 mt-2">

                </div>



                <div>

                    <label>Contact Number</label>

                    <input type="text"
                    placeholder="09123456789"
                    class="w-full border rounded-lg px-4 py-2 mt-2">

                </div>



                <div>

                    <label>Booking Reference No.</label>

                    <input type="text"
                    placeholder="BK-20260731-001"
                    class="w-full border rounded-lg px-4 py-2 mt-2">

                </div>


            </div>






            <!-- Room Assignment -->


            <h3 class="font-semibold text-gray-700 mb-4">

                Room Assignment

            </h3>



            <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">


                <input
                type="text"
                placeholder="Room Number"
                class="border rounded-lg px-4 py-2">



                <select class="border rounded-lg px-4 py-2">

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



                <select class="border rounded-lg px-4 py-2">


                    <option>
                        1st Floor
                    </option>

                    <option>
                        2nd Floor
                    </option>

                    <option>
                        3rd Floor
                    </option>


                </select>




                <input
                type="text"
                placeholder="Occupancy"
                class="border rounded-lg px-4 py-2">


            </div>






            <!-- Cleaning Type -->


            <h3 class="font-semibold text-gray-700 mb-4">

                Cleaning Type

            </h3>


            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">


                <label>
                    <input type="checkbox">
                    Check-out Cleaning
                </label>


                <label>
                    <input type="checkbox">
                    Daily Cleaning
                </label>


                <label>
                    <input type="checkbox">
                    Deep Cleaning
                </label>


                <label>
                    <input type="checkbox">
                    Room Inspection
                </label>


                <label>
                    <input type="checkbox">
                    Linen Replacement
                </label>


            </div>






            <!-- Priority -->


            <h3 class="font-semibold text-gray-700 mb-4">

                Priority Level

            </h3>



            <div class="flex flex-wrap gap-8 mb-8">


                <label>
                    <input type="radio" name="priority">
                    Low
                </label>


                <label>
                    <input type="radio" name="priority">
                    Medium
                </label>


                <label>
                    <input type="radio" name="priority">
                    High
                </label>


                <label>
                    <input type="radio" name="priority">
                    Urgent
                </label>


            </div>






            <!-- Schedule -->


            <h3 class="font-semibold text-gray-700 mb-4">

                Schedule

            </h3>



            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">


                <input type="date"
                class="border rounded-lg px-4 py-2">



                <input type="time"
                class="border rounded-lg px-4 py-2">



                <input type="text"
                placeholder="45 Minutes"
                class="border rounded-lg px-4 py-2">


            </div>






            <!-- Staff -->


            <h3 class="font-semibold text-gray-700 mb-4">

                Assigned Staff

            </h3>



            <input
            type="text"
            placeholder="Maria Santos"
            class="border rounded-lg px-4 py-2 w-full mb-8">






            <!-- Notes -->


            <h3 class="font-semibold text-gray-700 mb-4">

                Special Instructions

            </h3>



            <textarea rows="4"
            placeholder="Guest requested extra pillows and towels. Check minibar before arrival."
            class="w-full border rounded-lg px-4 py-3 mb-8"></textarea>






            <div class="flex justify-end flex-wrap gap-3">


                <button
                type="button"
                onclick="closeModal()"
                class="px-6 py-3 rounded-lg border">


                    Cancel


                </button>





                <button
                type="submit"
                class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-3 rounded-lg">


                    <i class="fas fa-check mr-2"></i>


                    Assign Task


                </button>


            </div>



        </form>


    </div>


</div>







<script>


// OPEN MODAL

function openModal(){

    document
    .getElementById("assignModal")
    .classList.remove("hidden");


    document
    .getElementById("assignModal")
    .classList.add("flex");

}





// CLOSE MODAL

function closeModal(){

    document
    .getElementById("assignModal")
    .classList.remove("flex");


    document
    .getElementById("assignModal")
    .classList.add("hidden");

}






// MOBILE SIDEBAR

document.addEventListener("DOMContentLoaded", function(){


    const menuBtn = document.getElementById("menuBtn");

    const sidebar = document.getElementById("sidebar");



    menuBtn.addEventListener("click", function(){


        if(sidebar.classList.contains("-left-64")){


            sidebar.classList.remove("-left-64");

            sidebar.classList.add("left-0");


        }else{


            sidebar.classList.remove("left-0");

            sidebar.classList.add("-left-64");


        }


    });



});



</script>


@endsection