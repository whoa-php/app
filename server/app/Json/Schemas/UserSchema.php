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

use App\Data\Models\User as Model;
use Whoa\Contracts\Data\TimestampFields;
use Whoa\Contracts\Data\UuidFields;

/**
 * @package App
 */
class UserSchema extends BaseSchema
{
    /** @var string Type */
    public const TYPE = 'users';

    /** @var string Model class name */
    public const MODEL = Model::class;

    /** @var string Attribute name */
    public const ATTR_EMAIL = Model::FIELD_EMAIL;

    /** @var string Virtual attribute name */
    public const V_ATTR_PASSWORD = 'password';

    /** @var string Capture name */
    public const CAPTURE_NAME_PASSWORD = self::V_ATTR_PASSWORD;

    /** @var string Virtual attribute name */
    public const V_ATTR_PASSWORD_CONFIRMATION = 'password-confirmation';

    /** @var string Capture name */
    public const CAPTURE_NAME_PASSWORD_CONFIRMATION = self::V_ATTR_PASSWORD_CONFIRMATION;

    /** @var string Attribute name */
    public const ATTR_DESCRIPTION = Model::FIELD_DESCRIPTION;

    /** @var string Relationship name */
    public const REL_ROLE = Model::REL_ROLE;

    /** @var string Relationship name */
    public const REL_OAUTH_TOKENS = 'oauth-tokens';

    /**
     * @inheritdoc
     */
    public static function getMappings(): array
    {
        return [
            self::SCHEMA_ATTRIBUTES => [
                self::RESOURCE_ID => Model::FIELD_ID,
                self::ATTR_UUID => UuidFields::FIELD_UUID,
                self::ATTR_EMAIL => Model::FIELD_EMAIL,
                self::V_ATTR_PASSWORD => self::CAPTURE_NAME_PASSWORD,
                self::V_ATTR_PASSWORD_CONFIRMATION => self::CAPTURE_NAME_PASSWORD_CONFIRMATION,
                self::ATTR_DESCRIPTION => Model::FIELD_DESCRIPTION,
                self::ATTR_CREATED_AT => TimestampFields::FIELD_CREATED_AT,
                self::ATTR_UPDATED_AT => TimestampFields::FIELD_UPDATED_AT,
            ],
            self::SCHEMA_RELATIONSHIPS => [
                self::REL_ROLE => Model::REL_ROLE,
                self::REL_OAUTH_TOKENS => Model::REL_OAUTH_TOKENS,
            ],
        ];
    }
}
