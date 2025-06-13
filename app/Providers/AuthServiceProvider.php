<?php

namespace App\Providers;

use App\Models\UserRequest;
use App\Policies\UserRequestPolicy;
use App\Models\LaporanType;
use App\Policies\LaporanTypePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        UserRequest::class => UserRequestPolicy::class,
        LaporanType::class => LaporanTypePolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
