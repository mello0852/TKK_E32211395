<?php

namespace App\Providers;

use App\Models\LokasiMonitoring;
use App\Policies\LokasiPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        LokasiMonitoring::class => LokasiPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}