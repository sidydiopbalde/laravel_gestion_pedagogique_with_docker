<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ClientPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut accéder aux routes des clients ou des boutiquiers.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response
     */
    public function accessClientOrBoutiquierRoutes(User $user): Response
    {
        // Autorise l'accès si le rôle de l'utilisateur est 'Client' ou 'Boutiquier'
        return $user->role && in_array($user->role->libelle, ['Client', 'Boutiquier'])
            ? Response::allow()
            : Response::deny('Vous devez être un Client ou un Boutiquier pour accéder à cette ressource.');
    }
}
