<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\Shop;
use App\Policies\PagePolicy;
use App\Policies\ShopPolicy;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Shop::class => ShopPolicy::class,
        Page::class => PagePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('isAdmin', fn(User $u) => (bool) $u->is_admin);
    }
}

