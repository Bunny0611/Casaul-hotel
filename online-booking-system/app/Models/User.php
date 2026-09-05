<?php

namespace App\Models;

class User extends Staff
{
    // Compatibility model for older code/tests that still reference App\Models\User.
    // The application now stores staff users in staff_users, so this model intentionally
    // behaves like the staff user model.
}
