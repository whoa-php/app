<?php

declare(strict_types=1);

namespace App\Json\Schemas;

use App\Data\Models\OAuthRedirectUri as Model;
use Whoa\Contracts\Data\TimestampFields;
use Whoa\Contracts\Data\UuidFields;
use Whoa\Passport\Contracts\Models\RedirectUriModelInterface as ModelInterface;

/**
 * @package App
 */
class OAuthRedirectUriSchema extends BaseSchema
{
    /** @var string Type */
    public const TYPE = 'oauth-redirect-uris';

    /** @var string Model class name */
    public const MODEL = Model::class;

    /** @var string Attribute name */
    public const ATTR_VALUE = ModelInterface::FIELD_VALUE;

    /** @var string Relationship name */
    public const REL_CLIENT = 'oauth-client';

    /**
     * @inheritDoc
     */
    public static function getMappings(): array
    {
        return [
            self::SCHEMA_ATTRIBUTES => [
                self::RESOURCE_ID => ModelInterface::FIELD_ID,
                self::ATTR_UUID => UuidFields::FIELD_UUID,
                self::ATTR_VALUE => ModelInterface::FIELD_VALUE,
                self::ATTR_CREATED_AT => TimestampFields::FIELD_CREATED_AT,
                self::ATTR_UPDATED_AT => TimestampFields::FIELD_UPDATED_AT,
            ],
            self::SCHEMA_RELATIONSHIPS => [
                self::REL_CLIENT => ModelInterface::REL_CLIENT,
            ],
        ];
    }
}
