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

namespace App\Validation\Role;

use App\Json\Schemas\BaseSchema;
use App\Json\Schemas\RoleSchema as Schema;
use App\Validation\Role\RoleRules as r;
use Whoa\Flute\Contracts\Schema\SchemaInterface;
use Whoa\Flute\Contracts\Validation\JsonApiQueryRulesInterface;
use Whoa\Flute\Validation\JsonApi\Rules\DefaultQueryValidationRules;
use Whoa\Validation\Contracts\Rules\RuleInterface;
use Settings\ApplicationApi;

/**
 * @package App
 */
class RolesReadQuery implements JsonApiQueryRulesInterface
{
    /**
     * @inheritdoc
     */
    public static function getIdentityRule(): ?RuleInterface
    {
        return r::stringToInt(r::moreThan(0));
    }

    /**
     * @inheritdoc
     */
    public static function getFilterRules(): ?array
    {
        return [
            SchemaInterface::RESOURCE_ID => static::getIdentityRule(),
            BaseSchema::ATTR_UUID => r::asSanitizedUuid(),
            Schema::ATTR_NAME => r::asSanitizedString(),
            Schema::ATTR_DESCRIPTION => r::asSanitizedString(),
            BaseSchema::ATTR_CREATED_AT => r::asJsonApiDateTime(),
            BaseSchema::ATTR_UPDATED_AT => r::asJsonApiDateTime(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getFieldSetRules(): ?array
    {
        // no field sets are allowed
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getSortsRule(): ?RuleInterface
    {
        return r::isString(
            r::inValues([
                SchemaInterface::RESOURCE_ID,
                Schema::ATTR_NAME,
                Schema::ATTR_DESCRIPTION,
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
                Schema::REL_USERS,
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
