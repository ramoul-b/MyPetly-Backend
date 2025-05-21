<?php

// app/Models/Booking.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'provider_id',      // nouveau
        'user_id',
        'appointment_date',
        'time',             // nouveau
        'payment_intent',   // nouveau
        'currency',         // nouveau
        'status', // pending, confirmed, cancelled
        'notes',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

