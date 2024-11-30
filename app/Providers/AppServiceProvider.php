<?php

namespace App\Providers;

use App\Models\User;
use App\Services\ApiService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ApiService::class, function ($app) {
            return new ApiService('http://gsmd.336699bet.com'); // Replace with your API base URL
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(PermissionRegistrar $permissionRegistrar): void
    {
        Permission::get()->each(function ($permission) {
            Log::info('Permission Loaded: ' . $permission->name);
        });

    }
}
