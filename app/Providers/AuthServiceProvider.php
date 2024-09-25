<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\PromoFirebaseModel;
use App\Models\User;
use App\Policies\PromotionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => PromotionPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
        // Passport::tokensCan([
        //     'view-user-data' => 'Voir les données utilisateur',
        //     'admin' => 'Accès administrateur',
        // ]);
        //     // Vous pouvez également définir des gates ici si nécessaire
        //     Gate::define('access-articles', [ArticlePolicy::class, 'access']);
    }
}
