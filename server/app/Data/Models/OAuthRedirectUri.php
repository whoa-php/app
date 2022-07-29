<?php

/**
 * Copyright 2021-2022 info@whoaphp.com
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
use Whoa\Passport\Contracts\Models\ClientModelInterface;
use Whoa\Passport\Contracts\Models\RedirectUriModelInterface;

/**
 * @package App
 */
class OAuthRedirectUri implements RedirectUriModelInterface, ModelInterface, CommonFields
{
    /**
     * @inheritDoc
     */
    public static function getTableName(): string
    {
        return self::TABLE_NAME;
    }

    /**
     * @inheritDoc
     */
    public static function getPrimaryKeyName(): string
    {
        return self::FIELD_ID;
    }

    /**
     * @inheritDoc
     */
    public static function getAttributeTypes(): array
    {
        return [
            self::FIELD_ID => Types::INTEGER,
            self::FIELD_UUID => UuidType::NAME,
            self::FIELD_ID_CLIENT => OAuthClient::getAttributeTypes()[ClientModelInterface::FIELD_ID],
            self::FIELD_VALUE => Types::STRING,
            self::FIELD_CREATED_AT => DateTimeType::NAME,
            self::FIELD_UPDATED_AT => DateTimeType::NAME,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getAttributeLengths(): array
    {
        return [
            self::FIELD_VALUE => 255,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getRelationships(): array
    {
        return [
            RelationshipTypes::BELONGS_TO => [
                self::REL_CLIENT => [
                    OAuthClient::class,
                    self::FIELD_ID_CLIENT,
                    ClientModelInterface::REL_REDIRECT_URIS,
                ]
            ]
        ];
    }

    /**
     * @inheritDoc
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
}
