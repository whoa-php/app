<?php

declare(strict_types=1);

namespace App\Data\Models;

use Doctrine\DBAL\Types\Types;
use Whoa\Contracts\Application\ModelInterface;
use Whoa\Doctrine\Types\DateTimeType;
use Whoa\Passport\Contracts\Models\ScopeModelInterface;
use Whoa\Passport\Entities\Scope;

/**
 * @package App
 */
class RoleOAuthScope implements ModelInterface, CommonFields
{
    /** @var string Table name */
    public const TABLE_NAME = 'roles_oauth_scopes';

    /** @var string Primary key */
    public const FIELD_ID = 'id_role_scope';

    /** @var string Foreign key */
    public const FIELD_ID_ROLE = Role::FIELD_ID;

    /** @var string Foreign key */
    public const FIELD_ID_SCOPE = Scope::FIELD_ID;

    /**
     * @inheritdoc
     */
    public static function getTableName(): string
    {
        return static::TABLE_NAME;
    }

    /**
     * @inheritdoc
     */
    public static function getPrimaryKeyName(): string
    {
        return static::FIELD_ID;
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeTypes(): array
    {
        return [
            self::FIELD_ID => Types::INTEGER,
            self::FIELD_ID_ROLE => Role::getAttributeTypes()[Role::FIELD_ID],
            self::FIELD_ID_SCOPE => OAuthScope::getAttributeTypes()[ScopeModelInterface::FIELD_ID],
            self::FIELD_CREATED_AT => DateTimeType::NAME,
            self::FIELD_UPDATED_AT => DateTimeType::NAME,
            self::FIELD_DELETED_AT => DateTimeType::NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeLengths(): array
    {
        return [];
    }

    /**
     * @inheritdoc
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

    /**
     * @inheritdoc
     */
    public static function getRelationships(): array
    {
        return [];
    }
}
