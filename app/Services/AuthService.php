<?php
namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AuthService
{
    /**
     * Authentifie l’utilisateur et renvoie token + infos enrichies
     */
    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Identifiants invalides'],
            ]);
        }

        // Génère/Supprime ancien token au choix
        $token = $user->createToken('api-token')->plainTextToken;

        // Charge rôles + permissions en une requête
        $user->load(['roles', 'permissions']);

        return [
            'access_token' => $token,
            'user'         => $user,      // UserResource formaté plus bas
        ];
    }
}
