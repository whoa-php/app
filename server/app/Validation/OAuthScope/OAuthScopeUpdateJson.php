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
final class OAuthScopeUpdateJson implements JsonApiDataRulesInterface
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
        return r::oauthScopeId();
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeRules(): array
    {
        return [
            Schema::ATTR_IDENTIFIER => r::identifier(true),
            Schema::ATTR_NAME => r::name(true),
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
