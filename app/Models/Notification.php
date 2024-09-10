<?php

namespace App\Models;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use Notifiable;
    protected $fillable=['data','type','notifiable','read_at'];
}
