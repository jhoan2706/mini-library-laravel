<?php

namespace App\Providers;

use App\Models\Loan;
use App\Models\User;
use App\Policies\BookPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Loan::class => BookPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
