<?php

namespace App\Providers;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('checkout', function (User $user) {
            return $user->hasRole(['admin', 'librarian', 'member']);
        });

        Gate::define('checkin', function (User $user, Loan $loan) {
            if ($user->hasRole(['admin', 'librarian'])) {
                return true;
            }
            return $user->id === $loan->user_id;
        });

        Gate::define('admin-only', function (User $user) {
            return $user->hasRole('admin');
        });
    }
}