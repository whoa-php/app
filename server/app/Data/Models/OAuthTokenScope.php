<?php

namespace App\Data\Models;

use Doctrine\DBAL\Types\Types;
use Whoa\Contracts\Application\ModelInterface;
use Whoa\Doctrine\Types\DateTimeType;
use Whoa\Passport\Contracts\Models\ScopeModelInterface;
use Whoa\Passport\Contracts\Models\TokenModelInterface;
use Whoa\Passport\Contracts\Models\TokenScopeModelInterface;

/**
 * @package App
 */
class OAuthTokenScope implements TokenScopeModelInterface, ModelInterface, CommonFields
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
            self::FIELD_ID_TOKEN => OAuthToken::getAttributeTypes()[TokenModelInterface::FIELD_ID],
            self::FIELD_ID_SCOPE => OAuthScope::getAttributeTypes()[ScopeModelInterface::FIELD_ID],
            self::FIELD_CREATED_AT => DateTimeType::NAME,
            self::FIELD_UPDATED_AT => DateTimeType::NAME,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getAttributeLengths(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public static function getRelationships(): array
    {
        return [];
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
