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

namespace App\Web\Middleware;

use Closure;
use Whoa\Contracts\Application\MiddlewareInterface;
use Whoa\Contracts\Http\RequestStorageInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package App\Web\Middleware
 */
class RememberRequestMiddleware implements MiddlewareInterface
{
    /**
     * Middleware handler.
     */
    public const CALLABLE_HANDLER = [self::class, self::MIDDLEWARE_METHOD_NAME];

    /**
     * @inheritdoc
     */
    public static function handle(
        ServerRequestInterface $request,
        Closure $next,
        ContainerInterface $container
    ): ResponseInterface {
        if ($container->has(RequestStorageInterface::class) === true) {
            /** @var RequestStorageInterface $requestStorage */
            $requestStorage = $container->get(RequestStorageInterface::class);
            $requestStorage->set($request);
        }

        /** @var ResponseInterface $response */
        return $next($request);
    }
}
