<?php

namespace App\Grants;

use RuntimeException;
use Illuminate\Contracts\Hashing\Hasher;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Laravel\Passport\Bridge\UserRepository;
use Laravel\Passport\Bridge\User;

class FacebookUserRepository extends UserRepository
{
    /**
     * {@inheritdoc}
     */
    public function getUserEntityByUserCredentials($token, $password, $grantType, ClientEntityInterface $clientEntity)
    {
        $provider = config('auth.guards.api.provider');

        if (is_null($model = config('auth.providers.'.$provider.'.model'))) {
            throw new RuntimeException('Unable to determine authentication model from configuration.');
        }

        $user = (new $model)->findFacebookUserForPassport($token);

        if (! $user)
            return;

        return new User($user->getAuthIdentifier());
    }
}
