<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProviderService extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'service_id',
        'price',
        'description',
        'duration',
        'available',
    ];

    public function provider() {
        return $this->belongsTo(Provider::class);
    }

    public function service() {
        return $this->belongsTo(Service::class);
    }
}
