<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class Store extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'provider_id',
        'name',
        'description',
        'address',
        'phone',
        'email',
    ];

    public $translatable = ['name', 'description'];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
