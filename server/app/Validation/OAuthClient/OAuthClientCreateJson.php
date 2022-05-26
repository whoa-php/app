<?php

declare(strict_types=1);

namespace App\Validation\OAuthClient;

use App\Json\Schemas\OAuthClientSchema as Schema;
use App\Validation\OAuthClient\OAuthClientRules as r;
use Whoa\Flute\Contracts\Validation\JsonApiDataRulesInterface;
use Whoa\Validation\Contracts\Rules\RuleInterface;

/**
 * @package App
 */
final class OAuthClientCreateJson implements JsonApiDataRulesInterface
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
            Schema::ATTR_CREDENTIALS => r::nullable(r::credentials()),
            Schema::ATTR_IS_CONFIDENTIAL => r::stringToBool(),
            Schema::ATTR_IS_SCOPE_EXCESS_ALLOWED => r::stringToBool(),
            Schema::ATTR_IS_USE_DEFAULT_SCOPE => r::stringToBool(),
            Schema::ATTR_IS_CODE_GRANT_ENABLED => r::stringToBool(),
            Schema::ATTR_IS_IMPLICIT_GRANT_ENABLED => r::stringToBool(),
            Schema::ATTR_IS_PASSWORD_GRANT_ENABLED => r::stringToBool(),
            Schema::ATTR_IS_CLIENT_GRANT_ENABLED => r::stringToBool(),
            Schema::ATTR_IS_REFRESH_GRANT_ENABLED => r::stringToBool(),
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
            Schema::REL_TOKENS => r::nullable(r::oauthTokensRelationship()),
            Schema::REL_SCOPES => r::nullable(r::oauthScopesRelationship()),
            Schema::REL_REDIRECT_URIS => r::nullable(r::oauthRedirectUrisRelationship()),
        ];
    }
}
