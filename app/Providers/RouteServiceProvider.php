<?php

namespace App\Providers;

use App\MovieGenre;
use App\MovieVideo;
use App\animeGenre;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */

    protected $namespace = 'App\Http\Controllers';



    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {


            Route::prefix('tv')
            ->middleware('tv')
            ->namespace($this->namespace)
            ->group(base_path('routes/tvapi.php'));

            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

              Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

                Route::prefix('email')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/email.php'));

                Route::prefix('share')
                ->middleware('share')
                ->namespace($this->namespace)
                ->group(base_path('routes/share.php'));
        });
    }

    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
