<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Store short names in notes.notable_type ('event' / 'venue')
        // instead of full class names like 'App\Models\Event'.
        Relation::morphMap([
            'event' => Event::class,
            'venue' => Venue::class,
        ]);

        // Throw an error on lazy loading, except in production.
        Model::preventLazyLoading(! app()->isProduction());
    }
}