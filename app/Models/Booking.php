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
        'provider_id',      
        'user_id',
        'animal_id',
        'appointment_date',
        'time',             
        'payment_intent',   
        'currency',         
        'status', 
        'notes',
    ];
protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'appointment_date' => 'date',
];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function provider()
{
    return $this->belongsTo(Provider::class);
}

public function animal()
{
    return $this->belongsTo(Animal::class);
}


}

