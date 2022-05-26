<?php

declare(strict_types=1);

namespace App\Json\Controllers;

use App\Api\OAuthClientsApi as Api;
use App\Json\Schemas\OAuthClientSchema as Schema;
use App\Validation\OAuthClient\OAuthClientCreateJson as CreateJson;
use App\Validation\OAuthClient\OAuthClientsReadQuery as ReadQuery;
use App\Validation\OAuthClient\OAuthClientUpdateJson as UpdateJson;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Whoa\Flute\Validation\JsonApi\Rules\DefaultQueryValidationRules;
use Whoa\Passport\Contracts\Models\ClientModelInterface as ModelInterface;

/**
 * @package App
 */
class OAuthClientsController extends BaseController
{
    /** @var string API class */
    public const API_CLASS = Api::class;

    /** @var string Schema class */
    public const SCHEMA_CLASS = Schema::class;

    /** @var string Validation class */
    public const ON_CREATE_DATA_VALIDATION_RULES_CLASS = CreateJson::class;

    /** @var string Validation class */
    public const ON_READ_QUERY_VALIDATION_RULES_CLASS = ReadQuery::class;

    /** @var string Validation class */
    public const ON_UPDATE_DATA_VALIDATION_RULES_CLASS = UpdateJson::class;

    /** @var string Validation class */
    public const ON_INDEX_QUERY_VALIDATION_RULES_CLASS = ReadQuery::class;

    /**
     * @param array $routeParams
     * @param ContainerInterface $container
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function readOAuthRedirectUris(
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        return static::readRelationship(
            $routeParams[static::ROUTE_KEY_INDEX],
            ModelInterface::REL_REDIRECT_URIS,
            DefaultQueryValidationRules::class,
            $container,
            $request
        );
    }

    /**
     * @param array $routeParams
     * @param ContainerInterface $container
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function readOAuthTokens(
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        return static::readRelationship(
            $routeParams[static::ROUTE_KEY_INDEX],
            ModelInterface::REL_TOKENS,
            DefaultQueryValidationRules::class,
            $container,
            $request
        );
    }

    /**
     * @param array $routeParams
     * @param ContainerInterface $container
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function readOAuthScopes(
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        return static::readRelationship(
            $routeParams[static::ROUTE_KEY_INDEX],
            ModelInterface::REL_SCOPES,
            DefaultQueryValidationRules::class,
            $container,
            $request
        );
    }

}
