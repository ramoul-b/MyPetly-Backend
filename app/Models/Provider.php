<?php

// app/Models/Provider.php

namespace App\Models;

use App\Enums\ProviderStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Provider extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'tax_code',
        'address',
        'description',
        'photo',
        'birth_year',
        'specialization',
        'education',
        'experience',
        'personal_info',
        'rating',
        'status',
        'validated_at',
    ];

    public $translatable = ['name', 'description','specialization'];

    protected $casts = [
        'status' => ProviderStatusEnum::class,
        'validated_at' => 'datetime',
    ];


    public function services()
    {
        return $this->belongsToMany(Service::class, 'provider_services', 'provider_id', 'service_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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
