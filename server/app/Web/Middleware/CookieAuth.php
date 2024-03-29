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
use Whoa\Contracts\Passport\PassportAccountManagerInterface;
use Whoa\Passport\Exceptions\AuthenticationException;
use Whoa\Passport\Exceptions\RepositoryException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/**
 * @package App
 */
final class CookieAuth implements MiddlewareInterface
{
    public const COOKIE_NAME = 'auth_token';

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
        // if auth cookie given ...
        $cookies = $request->getCookieParams();
        if (array_key_exists(static::COOKIE_NAME, $cookies) === true &&
            is_string($tokenValue = $cookies[static::COOKIE_NAME]) === true &&
            empty($tokenValue) === false
        ) {
            // ... and user hasn't been authenticated before ...
            /** @var PassportAccountManagerInterface $accountManager */
            $accountManager = $container->get(PassportAccountManagerInterface::class);
            if ($accountManager->getAccount() === null) {
                // ... then auth with the cookie
                try {
                    $accountManager->setAccountWithTokenValue($tokenValue);
                } catch (AuthenticationException $exception) {
                    // ignore if auth with the token fails or add the accident to log (could be taken from container)
                    /** @var LoggerInterface $logger */
                    $logger = $container->get(LoggerInterface::class);
                    $logger->warning(
                        'Auth cookie received with request however authentication failed due to its invalid value.',
                        ['exception' => $exception]
                    );
                } catch (RepositoryException $exception) {
                    // ignore if auth with the token fails or add the accident to log (could be taken from container)
                    /** @var LoggerInterface $logger */
                    $logger = $container->get(LoggerInterface::class);
                    $logger->warning(
                        'Auth cookie received with request however authentication failed due to database issue(s).',
                        ['exception' => $exception]
                    );
                }
            }
        }

        return $next($request);
    }
}
