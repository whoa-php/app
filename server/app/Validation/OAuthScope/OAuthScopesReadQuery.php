<?php

declare(strict_types=1);

namespace App\Validation\OAuthScope;

use App\Json\Schemas\BaseSchema;
use App\Json\Schemas\OAuthClientSchema;
use App\Json\Schemas\OAuthScopeSchema as Schema;
use App\Validation\OAuthScope\OAuthScopeRules as r;
use Whoa\Flute\Contracts\Schema\SchemaInterface;
use Whoa\Flute\Contracts\Validation\JsonApiQueryRulesInterface;
use Whoa\Flute\Validation\JsonApi\Rules\DefaultQueryValidationRules;
use Whoa\Validation\Contracts\Rules\RuleInterface;
use Settings\ApplicationApi;

/**
 * @package App
 */
class OAuthScopesReadQuery implements JsonApiQueryRulesInterface
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
            Schema::ATTR_DESCRIPTION => r::asSanitizedString(),
            BaseSchema::ATTR_CREATED_AT => r::asJsonApiDateTime(),
            BaseSchema::ATTR_UPDATED_AT => r::asJsonApiDateTime(),
            Schema::REL_CLIENTS => r::asSanitizedString(),
            Schema::REL_CLIENTS . '.' . SchemaInterface::RESOURCE_ID => r::asSanitizedString(),
            Schema::REL_CLIENTS . '.' . BaseSchema::ATTR_UUID => r::asSanitizedUuid(),
            Schema::REL_CLIENTS . '.' . OAuthClientSchema::ATTR_IDENTIFIER => r::asSanitizedString(),
            Schema::REL_CLIENTS . '.' . OAuthClientSchema::ATTR_NAME => r::asSanitizedString(),
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
                Schema::REL_CLIENTS,
                Schema::REL_CLIENTS . '.' . SchemaInterface::RESOURCE_ID,
                Schema::REL_CLIENTS . '.' . BaseSchema::ATTR_UUID,
                Schema::REL_CLIENTS . '.' . OAuthClientSchema::ATTR_IDENTIFIER,
                Schema::REL_CLIENTS . '.' . OAuthClientSchema::ATTR_NAME,
            ]),
            OAuthClientSchema::TYPE => r::success(),
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
                Schema::ATTR_DESCRIPTION,
                Schema::REL_CLIENTS,
                Schema::REL_CLIENTS . '.' . SchemaInterface::RESOURCE_ID,
                Schema::REL_CLIENTS . '.' . BaseSchema::ATTR_UUID,
                Schema::REL_CLIENTS . '.' . OAuthClientSchema::ATTR_IDENTIFIER,
                Schema::REL_CLIENTS . '.' . OAuthClientSchema::ATTR_NAME,
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
                Schema::REL_CLIENTS,
                Schema::REL_TOKENS,
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
