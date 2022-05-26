<?php

declare(strict_types=1);

namespace App\Data\Models;

use Doctrine\DBAL\Types\Types;
use Whoa\Contracts\Application\ModelInterface;
use Whoa\Contracts\Data\RelationshipTypes;
use Whoa\Doctrine\Types\DateTimeType;
use Whoa\Doctrine\Types\UuidType;
use Whoa\Passport\Contracts\Models\ClientModelInterface;
use Whoa\Passport\Contracts\Models\ScopeModelInterface;
use Whoa\Passport\Contracts\Models\TokenModelInterface;
use Whoa\Passport\Contracts\Models\TokenScopeModelInterface;

/**
 * @package App
 */
class OAuthToken implements TokenModelInterface, ModelInterface, CommonFields
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
            self::FIELD_ID_CLIENT => OAuthClient::getAttributeTypes()[ClientModelInterface::FIELD_ID],
            self::FIELD_ID_USER => User::getAttributeTypes()[User::FIELD_ID],
            self::FIELD_IS_SCOPE_MODIFIED => Types::BOOLEAN,
            self::FIELD_IS_ENABLED => Types::BOOLEAN,
            self::FIELD_REDIRECT_URI => Types::STRING,
            self::FIELD_CODE => Types::STRING,
            self::FIELD_VALUE => Types::STRING,
            self::FIELD_TYPE => Types::STRING,
            self::FIELD_REFRESH => Types::STRING,
            self::FIELD_CODE_CREATED_AT => DateTimeType::NAME,
            self::FIELD_VALUE_CREATED_AT => DateTimeType::NAME,
            self::FIELD_REFRESH_CREATED_AT => DateTimeType::NAME,
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
            self::FIELD_REDIRECT_URI => 255,
            self::FIELD_CODE => 255,
            self::FIELD_VALUE => 255,
            self::FIELD_TYPE => 255,
            self::FIELD_REFRESH => 255,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getRelationships(): array
    {
        return [
            RelationshipTypes::BELONGS_TO => [
                self::REL_CLIENT => [
                    OAuthClient::class,
                    self::FIELD_ID_CLIENT,
                    ClientModelInterface::REL_TOKENS,
                ],
                self::REL_USER => [
                    User::class,
                    self::FIELD_ID_USER,
                    User::REL_OAUTH_TOKENS,
                ],
            ],
            RelationshipTypes::BELONGS_TO_MANY => [
                self::REL_SCOPES => [
                    OAuthScope::class,
                    TokenScopeModelInterface::TABLE_NAME,
                    TokenScopeModelInterface::FIELD_ID_TOKEN,
                    TokenScopeModelInterface::FIELD_ID_SCOPE,
                    ScopeModelInterface::REL_TOKENS,
                ],
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
