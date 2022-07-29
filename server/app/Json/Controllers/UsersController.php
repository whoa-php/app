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

namespace App\Json\Controllers;

use App\Api\UsersApi as Api;
use App\Data\Models\User as Model;
use App\Json\Schemas\UserSchema as Schema;
use App\Validation\User\UserCreateJson as CreateJson;
use App\Validation\User\UsersReadQuery as ReadQuery;
use App\Validation\User\UserUpdateJson as UpdateJson;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Whoa\Flute\Validation\JsonApi\Rules\DefaultQueryValidationRules;

/**
 * @package App
 */
class UsersController extends BaseController
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
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function readRole(
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        return static::readRelationship(
            $routeParams[static::ROUTE_KEY_INDEX],
            Model::REL_ROLE,
            DefaultQueryValidationRules::class,
            $container,
            $request
        );
    }

    /**
     * @param array $routeParams
     * @param ContainerInterface $container
     * @param ServerRequestInterface $request
     * @return ResponseInterface
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
            Model::REL_OAUTH_TOKENS,
            DefaultQueryValidationRules::class,
            $container,
            $request
        );
    }
}
