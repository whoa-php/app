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

namespace App\Data\Models;

use Doctrine\DBAL\Types\Types;
use Whoa\Contracts\Application\ModelInterface;
use Whoa\Contracts\Data\RelationshipTypes;
use Whoa\Doctrine\Types\DateTimeType;
use Whoa\Doctrine\Types\UuidType;

/**
 * @package App
 */
class Role implements ModelInterface, CommonFields
{
    /** @var string Table name */
    public const TABLE_NAME = 'roles';

    /** @var string Primary key */
    public const FIELD_ID = 'id_role';

    /** @var string Field name */
    public const FIELD_NAME = 'name';

    /** @var string Field name */
    public const FIELD_DESCRIPTION = 'description';

    /** @var string Relationship name */
    public const REL_USERS = 'users';

    /** @var string Relationship name */
    public const REL_SCOPES = 'scopes';

    /**
     * @inheritdoc
     */
    public static function getTableName(): string
    {
        return static::TABLE_NAME;
    }

    /**
     * @inheritdoc
     */
    public static function getPrimaryKeyName(): string
    {
        return static::FIELD_ID;
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeTypes(): array
    {
        return [
            self::FIELD_ID => Types::INTEGER,
            self::FIELD_UUID => UuidType::NAME,
            self::FIELD_NAME => Types::STRING,
            self::FIELD_DESCRIPTION => Types::TEXT,
            self::FIELD_CREATED_AT => DateTimeType::NAME,
            self::FIELD_UPDATED_AT => DateTimeType::NAME,
            self::FIELD_DELETED_AT => DateTimeType::NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeLengths(): array
    {
        return [
            self::FIELD_NAME => 255,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getRawAttributes(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public static function getVirtualAttributes(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getRelationships(): array
    {
        return [
            RelationshipTypes::HAS_MANY => [
                self::REL_USERS => [
                    User::class,
                    User::FIELD_ID_ROLE,
                    User::REL_ROLE
                ],
            ],
            RelationshipTypes::BELONGS_TO_MANY => [
                self::REL_SCOPES => [
                    OAuthScope::class,
                    RoleOAuthScope::TABLE_NAME,
                    RoleOAuthScope::FIELD_ID_ROLE,
                    RoleOAuthScope::FIELD_ID_SCOPE,
                    OAuthScope::REL_ROLES,
                ]
            ],
        ];
    }
}
