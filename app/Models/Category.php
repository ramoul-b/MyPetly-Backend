<?php
// app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'name',
        'icon',
        'type',
        'color',
        'description',
    ];
    public $translatable = ['name'];

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
