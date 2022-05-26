<?php

declare(strict_types=1);

namespace App\Validation\OAuthScope;

use App\Json\Schemas\OAuthScopeSchema as Schema;
use App\Validation\OAuthScope\OAuthScopeRules as r;
use Whoa\Flute\Contracts\Validation\JsonApiDataRulesInterface;
use Whoa\Validation\Contracts\Rules\RuleInterface;

/**
 * @package App
 */
final class OAuthScopeCreateJson implements JsonApiDataRulesInterface
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
            Schema::ATTR_IDENTIFIER => r::required(r::identifier()),
            Schema::ATTR_NAME => r::required(r::name()),
            Schema::ATTR_DESCRIPTION => r::nullable(r::asSanitizedString()),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getToOneRelationshipRules(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getToManyRelationshipRules(): array
    {
        return [
            Schema::REL_CLIENTS => r::nullable(r::oauthClientsRelationship()),
            Schema::REL_TOKENS => r::nullable(r::oauthTokensRelationship()),
        ];
    }
}
