<?php

declare(strict_types=1);

namespace App\Json\Controllers;

use App\Api\OAuthScopesApi as Api;
use App\Json\Schemas\OAuthScopeSchema as Schema;
use App\Validation\OAuthScope\OAuthScopeCreateJson as CreateJson;
use App\Validation\OAuthScope\OAuthScopesReadQuery as ReadQuery;
use App\Validation\OAuthScope\OAuthScopeUpdateJson as UpdateJson;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Whoa\Flute\Validation\JsonApi\Rules\DefaultQueryValidationRules;
use Whoa\Passport\Contracts\Models\ScopeModelInterface as ModelInterface;

/**
 * @package App
 */
class OAuthScopesController extends BaseController
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
    public static function readOAuthClients(
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        return static::readRelationship(
            $routeParams[static::ROUTE_KEY_INDEX],
            ModelInterface::REL_CLIENTS,
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
}
