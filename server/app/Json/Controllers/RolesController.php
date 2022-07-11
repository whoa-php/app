<?php

declare(strict_types=1);

namespace App\Json\Controllers;

use App\Api\RolesApi as Api;
use App\Data\Models\Role as Model;
use App\Json\Schemas\RoleSchema as Schema;
use App\Validation\Role\RoleCreateJson as CreateJson;
use App\Validation\Role\RolesReadQuery as ReadQuery;
use App\Validation\Role\RoleUpdateJson as UpdateJson;
use Whoa\Flute\Validation\JsonApi\Rules\DefaultQueryValidationRules;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package App
 */
class RolesController extends BaseController
{
    /** @inheritdoc */
    public const API_CLASS = Api::class;

    /** @inheritdoc */
    public const SCHEMA_CLASS = Schema::class;

    /** @inheritdoc */
    public const ON_CREATE_DATA_VALIDATION_RULES_CLASS = CreateJson::class;

    /** @inheritdoc */
    public const ON_UPDATE_DATA_VALIDATION_RULES_CLASS = UpdateJson::class;

    /** @inheritdoc */
    public const ON_INDEX_QUERY_VALIDATION_RULES_CLASS = ReadQuery::class;

    /** @inheritdoc */
    public const ON_READ_QUERY_VALIDATION_RULES_CLASS = ReadQuery::class;

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
    public static function readUsers(
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        return static::readRelationship(
            $routeParams[static::ROUTE_KEY_INDEX],
            Model::REL_USERS,
            DefaultQueryValidationRules::class,
            $container,
            $request
        );
    }
}
