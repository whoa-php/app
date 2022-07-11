<?php

declare(strict_types=1);

namespace App\Commands\Middleware;

use App\Api\UsersApi;
use Closure;
use Whoa\Application\Commands\BaseImpersonationMiddleware;
use Whoa\Flute\Contracts\FactoryInterface;
use Psr\Container\ContainerInterface;

/**
 * @package App
 */
class CliAuthenticationMiddleware extends BaseImpersonationMiddleware
{
    /** Middleware handler */
    public const CALLABLE_HANDLER = [self::class, self::MIDDLEWARE_METHOD_NAME];

    /**
     * @inheritdoc
     */
    protected static function createReadScopesClosure(ContainerInterface $container): Closure
    {
        return function (?int $userId) use ($container): array {
            if ($userId !== null) {
                /** @var FactoryInterface $factory */
                $factory = $container->get(FactoryInterface::class);
                /** @var UsersApi $userApi */
                $userApi = $factory->createApi(UsersApi::class);

                $scopes = $userApi->noAuthReadScopes($userId);
            } else {
                $scopes = [];
            }

            return $scopes;
        };
    }
}
