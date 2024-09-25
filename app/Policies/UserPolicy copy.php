<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can access admin routes.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    // public function accessAdminRoutes(User $user)
    // {
    //     return $user->role->libelle === 'Admin';
    // }

    public function accessAdminRoutes(User $user): Response
    {
        // Autorise l'accès si le rôle de l'utilisateur est 'Boutiquier'
        return $user->role && $user->role->libelle === 'Admin'
            ? Response::allow()
            : Response::deny('Vous devez être un Administrateur pour accéder à cette ressource.');
    }
}

