<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

// use Laravel\Passport\Client;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes();
        // Passport::loadKeysFrom(__DIR__.'/../storage/oauth');
        // Passport::hashClientSecrets();

        // Passport::tokensExpireIn(now()->addMinutes(59));
        Passport::tokensExpireIn(now()->addDays(365));
        Passport::refreshTokensExpireIn(now()->addDays(365));
        Passport::personalAccessTokensExpireIn(now()->addMonths(365));

        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        //
    }
}
