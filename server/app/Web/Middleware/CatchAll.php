<?php

namespace App\Web\Middleware;

use App\Web\Controllers\ControllerTrait;
use App\Web\Views;
use Closure;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Whoa\Contracts\Application\MiddlewareInterface;

/**
 * @package App
 */
class CatchAll implements MiddlewareInterface
{
    use ControllerTrait;

    /** @var callable */
    public const CALLABLE_HANDLER = [self::class, self::MIDDLEWARE_METHOD_NAME];

    /**
     * @inheritDoc
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

        switch ($response->getStatusCode()) {
            case 404:
                return static::createResponseFromTemplate($container, Views::CATCH_ALL_PAGE, 200);
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
