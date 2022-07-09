<?php

namespace App\Providers;

use Illuminate\Database\Schema\Builder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\Passport;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use League\OAuth2\Server\AuthorizationServer;
use App\Grants\FacebookGrant;
use App\Grants\FacebookUserRepository;
use Illuminate\Support\Facades\Gate;


class AppServiceProvider extends ServiceProvider
{



    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Builder::defaultStringLength(191); // Update defaultStringLength
        //Model::preventLazyLoading();
      //\Illuminate\Support\Facades\URL::forceScheme('https');
        error_reporting(0);

        app(AuthorizationServer::class)->enableGrantType(
            $this->makeFacebookGrant(), Passport::tokensExpireIn()
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }



    protected function makeFacebookGrant()
    {
        $grant = new FacebookGrant(
            $this->app->make(FacebookUserRepository::class),
            $this->app->make(RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }
}
