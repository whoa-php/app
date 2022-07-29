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

use App\Web\Controllers\ControllerTrait;
use App\Web\Views;
use Closure;
use Laminas\Diactoros\Response\HtmlResponse;
use Whoa\Contracts\Application\MiddlewareInterface;
use Whoa\Contracts\Exceptions\AuthorizationExceptionInterface;
use Whoa\Contracts\Http\ThrowableResponseInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package App
 */
class CustomErrorResponsesMiddleware implements MiddlewareInterface
{
    use ControllerTrait;

    /**
     * Middleware handler.
     */
    public const CALLABLE_HANDLER = [self::class, self::MIDDLEWARE_METHOD_NAME];

    /**
     * @inheritdoc
     * @param ServerRequestInterface $request
     * @param Closure $next
     * @param ContainerInterface $container
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function handle(
        ServerRequestInterface $request,
        Closure $next,
        ContainerInterface $container
    ): ResponseInterface {
        /** @var ResponseInterface $response */
        $response = $next($request);

        // is it an error response?
        if ($response instanceof ThrowableResponseInterface) {
            if ($response->getThrowable() instanceof AuthorizationExceptionInterface) {
                return static::createResponseFromTemplate($container, Views::NOT_FORBIDDEN_PAGE, 403);
            }
        }

        // error responses might have just HTTP 4xx code as well
        switch ($response->getStatusCode()) {
            case 404:
                return static::createResponseFromTemplate($container, Views::NOT_FOUND_PAGE, 404);
            default:
                return $response;
        }
    }

    /**
     * @param ContainerInterface $container
     * @param int $templateId
     * @param int $httpCode
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private static function createResponseFromTemplate(
        ContainerInterface $container,
        int $templateId,
        int $httpCode
    ): ResponseInterface {
        $body = static::view($container, $templateId);

        return new HtmlResponse($body, $httpCode);
    }
}
