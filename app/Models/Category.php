<?php
// app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'type',
        'color',
        'description',
    ];

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
