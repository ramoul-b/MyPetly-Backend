<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'phone',
        'address',
        'status', 
        'email_verification_token', 
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_token', // Masquer le token pour des raisons de sécurité
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Define a relationship with the Session model.
     */
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    /**
     * Vérifie si l'utilisateur est actif.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Vérifie si l'email est déjà vérifié.
     */
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Marque l'email comme vérifié.
     */
    public function markEmailAsVerified()
    {
        $this->update(['email_verified_at' => now()]);
    }
    public function getUserById(int $id)
    {
        // Vous pouvez utiliser une méthode Eloquent comme find() ou findOrFail()
        return User::find($id);
    }
    public function animals()
{
    return $this->hasMany(Animal::class);
}

}
