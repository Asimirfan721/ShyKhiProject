<?php

// In app/Models/User.php or app/User.php depending on your Laravel version
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable; // Add this line

    // Other model methods and properties...
}
