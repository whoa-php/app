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

namespace App\Validation\OAuthToken;

use App\Json\Schemas\OAuthTokenSchema as Schema;
use App\Validation\OAuthRedirectUri\OAuthRedirectUriRules as r;
use Whoa\Flute\Contracts\Validation\JsonApiDataRulesInterface;
use Whoa\Validation\Contracts\Rules\RuleInterface;

/**
 * @package App
 */
final class OAuthTokenCreateJson implements JsonApiDataRulesInterface
{
    /**
     * @inheritdoc
     */
    public static function getTypeRule(): RuleInterface
    {
        return r::schemaType();
    }

    /**
     * @inheritdoc
     */
    public static function getIdRule(): RuleInterface
    {
        return r::equals(null);
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeRules(): array
    {
        return [
            Schema::ATTR_IS_SCOPE_MODIFIED => r::stringToBool(),
            Schema::ATTR_IS_ENABLED => r::stringToBool(),
            Schema::ATTR_REDIRECT_URI => r::nullable(r::asSanitizedUrl()),
            Schema::ATTR_CODE => r::nullable(r::asSanitizedString()),
            Schema::ATTR_VALUE => r::nullable(r::asSanitizedString()),
            Schema::ATTR_TYPE => r::nullable(r::asSanitizedString()),
            Schema::ATTR_REFRESH => r::nullable(r::asSanitizedString()),
            Schema::ATTR_CODE_CREATED_AT => r::nullable(r::asJsonApiDateTime()),
            Schema::ATTR_VALUE_CREATED_AT => r::nullable(r::asJsonApiDateTime()),
            Schema::ATTR_REFRESH_CREATED_AT => r::nullable(r::asJsonApiDateTime()),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getToOneRelationshipRules(): array
    {
        return [
            Schema::REL_USER => r::nullable(r::userRelationship()),
            Schema::REL_CLIENT => r::required(r::oauthClientRelationship()),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getToManyRelationshipRules(): array
    {
        return [
            Schema::REL_SCOPES => r::nullable(r::oauthScopesRelationship()),
        ];
    }
}
