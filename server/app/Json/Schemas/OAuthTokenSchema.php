<?php

declare(strict_types=1);

namespace App\Json\Schemas;

use App\Data\Models\OAuthToken as Model;
use Whoa\Contracts\Data\TimestampFields;
use Whoa\Contracts\Data\UuidFields;
use Whoa\Passport\Contracts\Models\TokenModelInterface;

/**
 * @package App
 */
class OAuthTokenSchema extends BaseSchema
{
    /** @var string Type */
    public const TYPE = 'oauth-tokens';

    /** @var string Model class name */
    public const MODEL = Model::class;

    /** @var string Attribute name */
    public const ATTR_IS_SCOPE_MODIFIED = 'is-scope-modified';

    /** @var string Attribute name */
    public const ATTR_IS_ENABLED = 'is-enabled';

    /** @var string Attribute name */
    public const ATTR_REDIRECT_URI = 'redirect-uri';

    /** @var string Attribute name */
    public const ATTR_CODE = TokenModelInterface::FIELD_CODE;

    /** @var string Attribute name */
    public const ATTR_VALUE = TokenModelInterface::FIELD_VALUE;

    /** @var string Attribute name */
    public const ATTR_TYPE = TokenModelInterface::FIELD_TYPE;

    /** @var string Attribute name */
    public const ATTR_REFRESH = TokenModelInterface::FIELD_REFRESH;

    /** @var string Attribute name */
    public const ATTR_CODE_CREATED_AT = 'code-created-at';

    /** @var string Attribute name */
    public const ATTR_VALUE_CREATED_AT = 'value-created-at';

    /** @var string Attribute name */
    public const ATTR_REFRESH_CREATED_AT = 'refresh-created-at';

    /** @var string Relationship name */
    public const REL_USER = TokenModelInterface::REL_USER;

    /** @var string Relationship name */
    public const REL_CLIENT = 'oauth-client';

    /** @var string Relationship name */
    public const REL_SCOPES = 'oauth-scopes';

    /**
     * @inheritDoc
     */
    public static function getMappings(): array
    {
        return [
            self::SCHEMA_ATTRIBUTES => [
                self::RESOURCE_ID => TokenModelInterface::FIELD_ID,
                self::ATTR_UUID => UuidFields::FIELD_UUID,
                self::ATTR_IS_SCOPE_MODIFIED => TokenModelInterface::FIELD_IS_SCOPE_MODIFIED,
                self::ATTR_IS_ENABLED => TokenModelInterface::FIELD_IS_ENABLED,
                self::ATTR_REDIRECT_URI => TokenModelInterface::FIELD_REDIRECT_URI,
                self::ATTR_CODE => TokenModelInterface::FIELD_CODE,
                self::ATTR_VALUE => TokenModelInterface::FIELD_VALUE,
                self::ATTR_TYPE => TokenModelInterface::FIELD_TYPE,
                self::ATTR_REFRESH => TokenModelInterface::FIELD_REFRESH,
                self::ATTR_CODE_CREATED_AT => TokenModelInterface::FIELD_CODE_CREATED_AT,
                self::ATTR_VALUE_CREATED_AT => TokenModelInterface::FIELD_VALUE_CREATED_AT,
                self::ATTR_REFRESH_CREATED_AT => TokenModelInterface::FIELD_REFRESH_CREATED_AT,
                self::ATTR_CREATED_AT => TimestampFields::FIELD_CREATED_AT,
                self::ATTR_UPDATED_AT => TimestampFields::FIELD_UPDATED_AT,
            ],
            self::SCHEMA_RELATIONSHIPS => [
                self::REL_USER => TokenModelInterface::REL_USER,
                self::REL_CLIENT => TokenModelInterface::REL_CLIENT,
                self::REL_SCOPES => TokenModelInterface::REL_SCOPES,
            ],
        ];
    }
}
