<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'currency',
        'timezone',
        'locale',
        'inventory_tracking',
        'notifications_enabled',
        'low_stock_threshold',
        'metadata',
    ];

    protected $casts = [
        'inventory_tracking'    => 'boolean',
        'notifications_enabled' => 'boolean',
        'metadata'              => 'array',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
