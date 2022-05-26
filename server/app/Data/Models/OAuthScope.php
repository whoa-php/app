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
use Whoa\Passport\Contracts\Models\ScopeModelInterface;

/**
 * @package App
 */
class OAuthScope implements ScopeModelInterface, ModelInterface, CommonFields
{
    /** @var string Relationship name */
    public const REL_ROLES = 'roles';

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
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getRelationships(): array
    {
        return [
            RelationshipTypes::BELONGS_TO_MANY => [
                self::REL_CLIENTS => [
                    OAuthClient::class,
                    ClientScopeModelInterface::TABLE_NAME,
                    ClientScopeModelInterface::FIELD_ID_SCOPE,
                    ClientScopeModelInterface::FIELD_ID_CLIENT,
                    ClientModelInterface::REL_SCOPES,
                ],
                self::REL_ROLES => [
                    Role::class,
                    RoleOAuthScope::TABLE_NAME,
                    RoleOAuthScope::FIELD_ID_SCOPE,
                    RoleOAuthScope::FIELD_ID_ROLE,
                    Role::REL_SCOPES,
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
