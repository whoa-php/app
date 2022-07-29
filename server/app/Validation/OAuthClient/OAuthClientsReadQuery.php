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

namespace App\Validation\OAuthClient;

use App\Json\Schemas\BaseSchema;
use App\Json\Schemas\OAuthClientSchema;
use App\Json\Schemas\OAuthClientSchema as Schema;
use App\Json\Schemas\OAuthScopeSchema;
use App\Validation\OAuthClient\OAuthClientRules as r;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Whoa\Flute\Contracts\Schema\SchemaInterface;
use Whoa\Flute\Contracts\Validation\JsonApiQueryRulesInterface;
use Whoa\Flute\Validation\JsonApi\Rules\DefaultQueryValidationRules;
use Whoa\Validation\Contracts\Rules\RuleInterface;
use Settings\ApplicationApi;

/**
 * @package App
 */
class OAuthClientsReadQuery implements JsonApiQueryRulesInterface
{
    /**
     * @inheritdoc
     */
    public static function getIdentityRule(): ?RuleInterface
    {
        return r::asSanitizedString();
    }

    /**
     * @inheritdoc
     */
    public static function getFilterRules(): ?array
    {
        return [
            SchemaInterface::RESOURCE_ID => static::getIdentityRule(),
            BaseSchema::ATTR_UUID => r::asSanitizedUuid(),
            Schema::ATTR_IDENTIFIER => r::asSanitizedString(),
            Schema::ATTR_NAME => r::asSanitizedString(),
            Schema::ATTR_DESCRIPTION => r::asSanitizedString(),
            Schema::ATTR_CREDENTIALS => r::stringToBool(),
            Schema::ATTR_IS_CONFIDENTIAL => r::stringToBool(),
            Schema::ATTR_IS_SCOPE_EXCESS_ALLOWED => r::stringToBool(),
            Schema::ATTR_IS_USE_DEFAULT_SCOPE => r::stringToBool(),
            Schema::ATTR_IS_CODE_GRANT_ENABLED => r::stringToBool(),
            Schema::ATTR_IS_IMPLICIT_GRANT_ENABLED => r::stringToBool(),
            Schema::ATTR_IS_PASSWORD_GRANT_ENABLED => r::stringToBool(),
            Schema::ATTR_IS_CLIENT_GRANT_ENABLED => r::stringToBool(),
            Schema::ATTR_IS_REFRESH_GRANT_ENABLED => r::stringToBool(),
            BaseSchema::ATTR_CREATED_AT => r::asJsonApiDateTime(),
            BaseSchema::ATTR_UPDATED_AT => r::asJsonApiDateTime(),
            Schema::REL_SCOPES => r::asSanitizedString(),
            Schema::REL_SCOPES . '.' . SchemaInterface::RESOURCE_ID => r::asSanitizedString(),
            Schema::REL_SCOPES . '.' . BaseSchema::ATTR_UUID => r::asSanitizedUuid(),
            Schema::REL_SCOPES . '.' . OAuthScopeSchema::ATTR_IDENTIFIER => r::asSanitizedString(),
            Schema::REL_SCOPES . '.' . OAuthScopeSchema::ATTR_NAME => r::asSanitizedString(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getFieldSetRules(): ?array
    {
        return [
            Schema::TYPE => r::inValues([
                SchemaInterface::RESOURCE_ID,
                BaseSchema::ATTR_UUID,
                Schema::ATTR_IDENTIFIER,
                Schema::REL_SCOPES,
                Schema::REL_SCOPES . '.' . SchemaInterface::RESOURCE_ID,
                Schema::REL_SCOPES . '.' . BaseSchema::ATTR_UUID,
                Schema::REL_SCOPES . '.' . OAuthScopeSchema::ATTR_IDENTIFIER,
                Schema::REL_SCOPES . '.' . OAuthScopeSchema::ATTR_NAME,
            ]),
            OAuthScopeSchema::TYPE => r::success(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getSortsRule(): ?RuleInterface
    {
        return r::isString(
            r::inValues([
                SchemaInterface::RESOURCE_ID,
                BaseSchema::ATTR_UUID,
                Schema::ATTR_IDENTIFIER,
                Schema::ATTR_NAME,
                Schema::ATTR_DESCRIPTION,
                BaseSchema::ATTR_CREATED_AT,
                BaseSchema::ATTR_UPDATED_AT,
                Schema::REL_SCOPES,
                Schema::REL_SCOPES . '.' . SchemaInterface::RESOURCE_ID,
                Schema::REL_SCOPES . '.' . BaseSchema::ATTR_UUID,
                Schema::REL_SCOPES . '.' . OAuthScopeSchema::ATTR_IDENTIFIER,
                Schema::REL_SCOPES . '.' . OAuthScopeSchema::ATTR_NAME,
            ])
        );
    }

    /**
     * @inheritdoc
     */
    public static function getIncludesRule(): ?RuleInterface
    {
        return r::isString(
            r::inValues([
                Schema::REL_TOKENS,
                Schema::REL_SCOPES,
                Schema::REL_REDIRECT_URIS,
            ])
        );
    }

    /**
     * @inheritdoc
     */
    public static function getPageOffsetRule(): ?RuleInterface
    {
        // defaults are fine
        return DefaultQueryValidationRules::getPageOffsetRule();
    }

    /**
     * @inheritdoc
     */
    public static function getPageLimitRule(): ?RuleInterface
    {
        // defaults are fine
        return DefaultQueryValidationRules::getPageLimitRuleForDefaultAndMaxSizes(
            ApplicationApi::DEFAULT_PAGE_SIZE,
            ApplicationApi::DEFAULT_MAX_PAGE_SIZE
        );
    }
}
