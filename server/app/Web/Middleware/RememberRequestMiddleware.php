<?php

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
        $response = $next($request);

        return $response;
    }
}
