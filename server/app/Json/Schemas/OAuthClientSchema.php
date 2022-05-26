<?php

declare(strict_types=1);

namespace App\Json\Schemas;

use App\Data\Models\OAuthClient as Model;
use Whoa\Contracts\Data\TimestampFields;
use Whoa\Contracts\Data\UuidFields;
use Whoa\Passport\Contracts\Models\ClientModelInterface as ModelInterface;

/**
 * @package App
 */
class OAuthClientSchema extends BaseSchema
{
    /** @var string Type */
    public const TYPE = 'oauth-clients';

    /** @var string Model class name */
    public const MODEL = Model::class;

    /** @var string Attribute name */
    public const ATTR_IDENTIFIER = ModelInterface::FIELD_IDENTIFIER;

    /** @var string Attribute name */
    public const ATTR_NAME = ModelInterface::FIELD_NAME;

    /** @var string Attribute name */
    public const ATTR_DESCRIPTION = ModelInterface::FIELD_DESCRIPTION;

    /** @var string Attribute name */
    public const ATTR_CREDENTIALS = ModelInterface::FIELD_CREDENTIALS;

    /** @var string Attribute name */
    public const ATTR_IS_CONFIDENTIAL = 'is-confidential';

    /** @var string Attribute name */
    public const ATTR_IS_SCOPE_EXCESS_ALLOWED = 'is-scope-excess-allowed';

    /** @var string Attribute name */
    public const ATTR_IS_USE_DEFAULT_SCOPE = 'is_use-default-scope';

    /** @var string Attribute name */
    public const ATTR_IS_CODE_GRANT_ENABLED = 'is-code_grant-enabled';

    /** @var string Attribute name */
    public const ATTR_IS_IMPLICIT_GRANT_ENABLED = 'is-implicit-grant-enabled';

    /** @var string Attribute name */
    public const ATTR_IS_PASSWORD_GRANT_ENABLED = 'is-password_grant-enabled';

    /** @var string Attribute name */
    public const ATTR_IS_CLIENT_GRANT_ENABLED = 'is-client-grant-enabled';

    /** @var string Attribute name */
    public const ATTR_IS_REFRESH_GRANT_ENABLED = 'is-refresh-grant-enabled';

    /** @var string Relationship name */
    public const REL_REDIRECT_URIS = 'oauth-redirect-uris';

    /** @var string Relationship name */
    public const REL_SCOPES = 'oauth-scopes';

    /** @var string Relationship name */
    public const REL_TOKENS = 'oauth-tokens';

    /**
     * @inheritDoc
     */
    public static function getMappings(): array
    {
        return [
            self::SCHEMA_ATTRIBUTES => [
                self::RESOURCE_ID => ModelInterface::FIELD_ID,
                self::ATTR_UUID => UuidFields::FIELD_UUID,
                self::ATTR_IDENTIFIER => ModelInterface::FIELD_IDENTIFIER,
                self::ATTR_NAME => ModelInterface::FIELD_NAME,
                self::ATTR_DESCRIPTION => ModelInterface::FIELD_DESCRIPTION,
                self::ATTR_CREDENTIALS => ModelInterface::FIELD_CREDENTIALS,
                self::ATTR_IS_CONFIDENTIAL => ModelInterface::FIELD_IS_CONFIDENTIAL,
                self::ATTR_IS_SCOPE_EXCESS_ALLOWED => ModelInterface::FIELD_IS_SCOPE_EXCESS_ALLOWED,
                self::ATTR_IS_USE_DEFAULT_SCOPE => ModelInterface::FIELD_IS_USE_DEFAULT_SCOPE,
                self::ATTR_IS_CODE_GRANT_ENABLED => ModelInterface::FIELD_IS_CODE_GRANT_ENABLED,
                self::ATTR_IS_IMPLICIT_GRANT_ENABLED => ModelInterface::FIELD_IS_IMPLICIT_GRANT_ENABLED,
                self::ATTR_IS_PASSWORD_GRANT_ENABLED => ModelInterface::FIELD_IS_PASSWORD_GRANT_ENABLED,
                self::ATTR_IS_CLIENT_GRANT_ENABLED => ModelInterface::FIELD_IS_CLIENT_GRANT_ENABLED,
                self::ATTR_IS_REFRESH_GRANT_ENABLED => ModelInterface::FIELD_IS_REFRESH_GRANT_ENABLED,
                self::ATTR_CREATED_AT => TimestampFields::FIELD_CREATED_AT,
                self::ATTR_UPDATED_AT => TimestampFields::FIELD_UPDATED_AT,
            ],
            self::SCHEMA_RELATIONSHIPS => [
                self::REL_REDIRECT_URIS => ModelInterface::REL_REDIRECT_URIS,
                self::REL_TOKENS => ModelInterface::REL_TOKENS,
                self::REL_SCOPES => ModelInterface::REL_SCOPES,
            ],
        ];
    }
}
