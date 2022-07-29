<?php

/**
 * Copyright 2015-2019 info@neomerx.com
 * Modification Copyright 2021-2022 info@whoaphp.com
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

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
