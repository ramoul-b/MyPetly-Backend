<?php

// app/Models/Provider.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class Provider extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'description',
        'photo',
        'birth_year',
        'specialization',
        'education',
        'experience',
        'personal_info',
        'rating',
    ];
    public $translatable = ['name', 'description','specialization'];

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function reviews()
    {
        return $this->hasManyThrough(Review::class, Service::class);
    }

    public function bookings()
    {
        return $this->hasManyThrough(Booking::class, Service::class);
    }
}
