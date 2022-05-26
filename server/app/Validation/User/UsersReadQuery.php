<?php

declare(strict_types=1);

namespace App\Validation\User;

use App\Json\Schemas\BaseSchema;
use App\Json\Schemas\RoleSchema;
use App\Json\Schemas\UserSchema as Schema;
use App\Validation\User\UserRules as r;
use Whoa\Flute\Contracts\Schema\SchemaInterface;
use Whoa\Flute\Contracts\Validation\JsonApiQueryRulesInterface;
use Whoa\Flute\Validation\JsonApi\Rules\DefaultQueryValidationRules;
use Whoa\Validation\Contracts\Rules\RuleInterface;
use Settings\ApplicationApi;

/**
 * @package App
 */
class UsersReadQuery implements JsonApiQueryRulesInterface
{
    /**
     * @inheritdoc
     */
    public static function getIdentityRule(): ?RuleInterface
    {
        return r::stringToInt(r::moreThan(0));
    }

    /**
     * @return RuleInterface[]|null
     */
    public static function getFilterRules(): ?array
    {
        return [
            SchemaInterface::RESOURCE_ID => static::getIdentityRule(),
            BaseSchema::ATTR_UUID => r::asSanitizedUuid(),
            Schema::ATTR_EMAIL => r::asSanitizedEmail(),
            Schema::ATTR_DESCRIPTION => r::asSanitizedString(),
            BaseSchema::ATTR_CREATED_AT => r::asJsonApiDateTime(),
            Schema::REL_ROLE => r::asSanitizedString(),
            Schema::REL_ROLE . '.' . RoleSchema::ATTR_NAME => r::asSanitizedString(),
            Schema::REL_ROLE . '.' . RoleSchema::ATTR_DESCRIPTION => r::asSanitizedString(),
        ];
    }

    /**
     * @return RuleInterface[]|null
     */
    public static function getFieldSetRules(): ?array
    {
        return [
            // if fields sets are given only the following fields are OK
            Schema::TYPE => r::inValues([
                SchemaInterface::RESOURCE_ID,
                Schema::ATTR_EMAIL,
                Schema::ATTR_DESCRIPTION,
                Schema::REL_ROLE,
            ]),
            // roles field sets could be any
            RoleSchema::TYPE => r::success(),
        ];
    }

    /**
     * @return RuleInterface|null
     */
    public static function getSortsRule(): ?RuleInterface
    {
        return r::isString(
            r::inValues([
                SchemaInterface::RESOURCE_ID,
                Schema::ATTR_EMAIL,
                Schema::ATTR_DESCRIPTION,
                Schema::REL_ROLE,
            ])
        );
    }

    /**
     * @return RuleInterface|null
     */
    public static function getIncludesRule(): ?RuleInterface
    {
        return r::isString(
            r::inValues([
                Schema::REL_ROLE,
                Schema::REL_OAUTH_TOKENS,
            ])
        );
    }

    /**
     * @return RuleInterface|null
     */
    public static function getPageOffsetRule(): ?RuleInterface
    {
        // defaults are fine
        return DefaultQueryValidationRules::getPageOffsetRule();
    }

    /**
     * @return RuleInterface|null
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
