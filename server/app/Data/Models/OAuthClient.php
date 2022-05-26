<?php

declare(strict_types=1);

namespace App\Data\Models;

use Doctrine\DBAL\Types\Types;
use Whoa\Contracts\Application\ModelInterface;
use Whoa\Contracts\Data\RelationshipTypes;
use Whoa\Doctrine\Types\DateTimeType;
use Whoa\Doctrine\Types\UuidType;
use Whoa\Passport\Contracts\Models\ClientModelInterface;
use Whoa\Passport\Contracts\Models\ClientScopeModelInterface;
use Whoa\Passport\Contracts\Models\RedirectUriModelInterface;
use Whoa\Passport\Contracts\Models\ScopeModelInterface;
use Whoa\Passport\Contracts\Models\TokenModelInterface;

/**
 * @package App
 */
class OAuthClient implements ClientModelInterface, ModelInterface, CommonFields
{
    /**
     * @inheritDoc
     */
    public static function getTableName(): string
    {
        return self::TABLE_NAME;
    }

    /**
     * @inheritDoc
     */
    public static function getPrimaryKeyName(): string
    {
        return self::FIELD_ID;
    }

    /**
     * @inheritDoc
     */
    public static function getAttributeTypes(): array
    {
        return [
            self::FIELD_ID => Types::INTEGER,
            self::FIELD_UUID => UuidType::NAME,
            self::FIELD_IDENTIFIER => Types::STRING,
            self::FIELD_NAME => Types::STRING,
            self::FIELD_DESCRIPTION => Types::TEXT,
            self::FIELD_CREDENTIALS => Types::STRING,
            self::FIELD_IS_CONFIDENTIAL => Types::BOOLEAN,
            self::FIELD_IS_SCOPE_EXCESS_ALLOWED => Types::BOOLEAN,
            self::FIELD_IS_USE_DEFAULT_SCOPE => Types::BOOLEAN,
            self::FIELD_IS_CODE_GRANT_ENABLED => Types::BOOLEAN,
            self::FIELD_IS_IMPLICIT_GRANT_ENABLED => Types::BOOLEAN,
            self::FIELD_IS_PASSWORD_GRANT_ENABLED => Types::BOOLEAN,
            self::FIELD_IS_CLIENT_GRANT_ENABLED => Types::BOOLEAN,
            self::FIELD_IS_REFRESH_GRANT_ENABLED => Types::BOOLEAN,
            self::FIELD_CREATED_AT => DateTimeType::NAME,
            self::FIELD_UPDATED_AT => DateTimeType::NAME,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getAttributeLengths(): array
    {
        return [
            self::FIELD_IDENTIFIER => 255,
            self::FIELD_NAME => 255,
            self::FIELD_CREDENTIALS => 255,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getRelationships(): array
    {
        return [
            RelationshipTypes::HAS_MANY => [
                self::REL_REDIRECT_URIS => [
                    OAuthRedirectUri::class,
                    RedirectUriModelInterface::FIELD_ID_CLIENT,
                    RedirectUriModelInterface::REL_CLIENT,
                ],
                self::REL_TOKENS => [
                    OAuthToken::class,
                    TokenModelInterface::FIELD_ID_CLIENT,
                    TokenModelInterface::REL_CLIENT,
                ],
            ],
            RelationshipTypes::BELONGS_TO_MANY => [
                self::REL_SCOPES => [
                    OAuthScope::class,
                    ClientScopeModelInterface::TABLE_NAME,
                    ClientScopeModelInterface::FIELD_ID_CLIENT,
                    ClientScopeModelInterface::FIELD_ID_SCOPE,
                    ScopeModelInterface::REL_CLIENTS,
                ]
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getRawAttributes(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public static function getVirtualAttributes(): array
    {
        return [];
    }
}
