<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Gate::before(function ($user, $ability) {
            return $user->hasRole('Administrador') ? true : null;
        });

        Builder::macro('toCsv', function ($name = null) {
            $query = $this;

            return response()->streamDownload(function () use ($query) {
                $results = $query->get();

                if ($results->count() < 1) return;

                $titles = implode(',', array_keys((array) $results->first()->getAttributes()));

                $values = $results->map(function ($result) {
                    return implode(',', collect($result->getAttributes())->map(function ($thing) {
                        return '"'.$thing.'"';
                    })->toArray());
                });

                $values->prepend($titles);

                echo $values->implode("\n");
            }, $name ?? 'export.csv');
        });
    }
}
