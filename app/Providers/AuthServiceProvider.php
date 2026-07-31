<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Business;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Gate: User can access business
        Gate::define('access-business', function (User $user, Business $business) {
            return $user->hasBusinessAccess($business->id);
        });

        // Gate: User can manage business
        Gate::define('manage-business', function (User $user, Business $business) {
            return $user->id === $business->user_id;
        });
    }
}
