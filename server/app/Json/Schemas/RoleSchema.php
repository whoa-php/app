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

namespace App\Json\Schemas;

use App\Data\Models\Role as Model;
use Whoa\Contracts\Data\TimestampFields;
use Whoa\Contracts\Data\UuidFields;

/**
 * @package App
 */
class RoleSchema extends BaseSchema
{
    /** @var string Type */
    public const TYPE = 'roles';

    /** @var string Model class name */
    public const MODEL = Model::class;

    /** @var string Attribute name */
    public const ATTR_NAME = Model::FIELD_NAME;

    /** @var string Attribute name */
    public const ATTR_DESCRIPTION = Model::FIELD_DESCRIPTION;

    /** @var string Relationship name */
    public const REL_USERS = Model::REL_USERS;

    /** @var string Relationship name */
    public const REL_SCOPES = 'oauth-scopes';

    /**
     * @inheritdoc
     */
    public static function getMappings(): array
    {
        return [
            self::SCHEMA_ATTRIBUTES => [
                self::RESOURCE_ID => Model::FIELD_ID,
                self::ATTR_UUID => UuidFields::FIELD_UUID,
                self::ATTR_NAME => Model::FIELD_NAME,
                self::ATTR_DESCRIPTION => Model::FIELD_DESCRIPTION,
                self::ATTR_CREATED_AT => TimestampFields::FIELD_CREATED_AT,
                self::ATTR_UPDATED_AT => TimestampFields::FIELD_UPDATED_AT,
            ],
            self::SCHEMA_RELATIONSHIPS => [
                self::REL_USERS => Model::REL_USERS,
            ],
        ];
    }
}
