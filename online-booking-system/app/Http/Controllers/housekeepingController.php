<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HousekeepingController extends Controller
{

    public function dashboard()
    {
        return view('housekeeping.dashboard');
    }


    public function assignedRooms()
    {
        return view('housekeeping.assigned-rooms');
    }


    public function roomStatusUpdate()
    {
        return view('housekeeping.room-status-update');
    }


    public function guestRequests()
    {
        return view('housekeeping.guest-requests');
    }


    public function maintenanceReport()
    {
        return view('housekeeping.maintenance-report');
    }


    public function cleaningHistory()
    {
        return view('housekeeping.cleaning-history');
    }

}