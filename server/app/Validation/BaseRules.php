<?php

declare(strict_types=1);

namespace App\Validation;

use App\Api\OAuthClientsApi;
use App\Api\OAuthRedirectUrisApi;
use App\Api\OAuthScopesApi;
use App\Api\OAuthTokensApi;
use App\Api\RolesApi;
use App\Api\UsersApi;
use App\Data\Models\OAuthClient;
use App\Json\Schemas\OAuthClientSchema;
use App\Json\Schemas\OAuthRedirectUriSchema;
use App\Json\Schemas\OAuthScopeSchema;
use App\Json\Schemas\OAuthTokenSchema;
use App\Json\Schemas\RoleSchema;
use App\Json\Schemas\UserSchema;
use App\Validation\L10n\Messages;
use Whoa\Doctrine\Json\Date;
use Whoa\Doctrine\Json\DateTime;
use Whoa\Doctrine\Json\Time;
use Whoa\Flute\Validation\Rules\ApiRulesTrait;
use Whoa\Flute\Validation\Rules\DatabaseRulesTrait;
use Whoa\Flute\Validation\Rules\RelationshipRulesTrait;
use Whoa\Flute\Validation\Rules\UuidRulesTrait;
use Whoa\Passport\Contracts\Models\ClientModelInterface;
use Whoa\Passport\Contracts\Models\ScopeModelInterface;
use Whoa\Validation\Contracts\Errors\ErrorCodes;
use Whoa\Validation\Contracts\Rules\RuleInterface;
use Whoa\Validation\Rules;

/**
 * @package App
 */
class BaseRules extends Rules
{
    use ApiRulesTrait;
    use DatabaseRulesTrait;
    use RelationshipRulesTrait;
    use UuidRulesTrait;

    /**
     * @param RuleInterface|null $next
     * @return RuleInterface
     */
    public static function oauthClientId(RuleInterface $next = null): RuleInterface
    {
        return self::stringToInt(self::readable(OAuthClientsApi::class), $next);
    }

    /**
     * @param RuleInterface|null $next
     * @return RuleInterface
     */
    public static function oauthClientRelationship(RuleInterface $next = null): RuleInterface
    {
        return self::toOneRelationship(OAuthClientSchema::TYPE, static::oauthClientId($next));
    }

    /**
     * @param RuleInterface|null $next
     * @return RuleInterface
     */
    public static function oauthClientsRelationship(RuleInterface $next = null): RuleInterface
    {
        $readableAll = static::stringArrayToIntArray(static::readableAll(OAuthClientsApi::class, $next));

        return self::toManyRelationship(OAuthClientSchema::TYPE, $readableAll);
    }

    /**
     * @param RuleInterface|null $next
     * @return RuleInterface
     */
    public static function oauthRedirectUriId(RuleInterface $next = null): RuleInterface
    {
        return self::stringToInt(self::readable(OAuthRedirectUrisApi::class), $next);
    }

    /**
     * @param RuleInterface|null $next
     * @return RuleInterface
     */
    public static function oauthRedirectUriRelationship(RuleInterface $next = null): RuleInterface
    {
        return self::toOneRelationship(OAuthClientSchema::TYPE, static::oauthRedirectUriId($next));
    }

    /**
     * @param RuleInterface|null $next
     * @return RuleInterface
     */
    public static function oauthRedirectUrisRelationship(RuleInterface $next = null): RuleInterface
    {
        $readableAll = static::stringArrayToIntArray(static::readableAll(OAuthRedirectUrisApi::class, $next));

        return self::toManyRelationship(OAuthRedirectUriSchema::TYPE, $readableAll);
    }

    /**
     * @param RuleInterface|null $next
     * @return RuleInterface
     */
    public static function oauthScopeId(RuleInterface $next = null): RuleInterface
    {
        return self::stringToInt(self::readable(OAuthScopesApi::class), $next);
    }

    /**
     * @param RuleInterface|null $next
     * @return RuleInterface
     */
    public static function oauthScopeRelationship(RuleInterface $next = null): RuleInterface
    {
        return self::toOneRelationship(OAuthScopeSchema::TYPE, static::oauthScopeId($next));
    }

    /**
     * @param RuleInterface|null $next
     * @return RuleInterface
     */
    public static function oauthScopesRelationship(RuleInterface $next = null): RuleInterface
    {
        $readableAll = static::stringArrayToIntArray(static::readableAll(OAuthScopesApi::class, $next));

        return self::toManyRelationship(OAuthScopeSchema::TYPE, $readableAll);
    }

    /**
     * @param RuleInterface|null $next
     * @return RuleInterface
     */
    public static function oauthTokenId(RuleInterface $next = null): RuleInterface
    {
        return self::stringToInt(self::readable(OAuthTokensApi::class), $next);
    }

    /**
     * @param RuleInterface|null $next
     * @return RuleInterface
     */
    public static function oauthTokenRelationship(RuleInterface $next = null): RuleInterface
    {
        return self::toOneRelationship(OAuthTokenSchema::TYPE, static::oauthTokenId($next));
    }

    /**
     * @param RuleInterface|null $next
     * @return RuleInterface
     */
    public static function oauthTokensRelationship(RuleInterface $next = null): RuleInterface
    {
        $readableAll = static::stringArrayToIntArray(static::readableAll(OAuthTokensApi::class, $next));

        return self::toManyRelationship(OAuthTokenSchema::TYPE, $readableAll);
    }

    /**
     * @param RuleInterface|null $next
     *
     * @return RuleInterface
     */
    public static function roleId(RuleInterface $next = null): RuleInterface
    {
        return self::stringToInt(self::readable(RolesApi::class, $next));
    }

    /**
     * @param RuleInterface|null $next
     *
     * @return RuleInterface
     */
    public static function roleRelationship(RuleInterface $next = null): RuleInterface
    {
        return self::toOneRelationship(RoleSchema::TYPE, static::roleId($next));
    }

    /**
     * @param RuleInterface|null $next
     * @return RuleInterface
     */
    public static function rolesRelationship(RuleInterface $next = null): RuleInterface
    {
        $readableAll = static::stringArrayToIntArray(static::readableAll(RolesApi::class, $next));

        return self::toManyRelationship(RoleSchema::TYPE, $readableAll);
    }

    /**
     * @param RuleInterface|null $next
     *
     * @return RuleInterface
     */
    public static function userId(RuleInterface $next = null): RuleInterface
    {
        return self::stringToInt(self::readable(UsersApi::class, $next));
    }

    /**
     * @param RuleInterface|null $next
     *
     * @return RuleInterface
     */
    public static function userRelationship(RuleInterface $next = null): RuleInterface
    {
        return self::toOneRelationship(UserSchema::TYPE, static::userId($next));
    }

    /**
     * @param RuleInterface|null $next
     *
     * @return RuleInterface
     */
    public static function usersRelationship(RuleInterface $next = null): RuleInterface
    {
        $readableAll = static::stringArrayToIntArray(static::readableAll(UsersApi::class, $next));

        return self::toManyRelationship(UserSchema::TYPE, $readableAll);
    }

    /**
     * @param RuleInterface|null $next
     *
     * @return RuleInterface
     */
    public static function asSanitizedString(RuleInterface $next = null): RuleInterface
    {
        return self::isString(
            self::filter(
                FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                null,
                ErrorCodes::INVALID_VALUE,
                Messages::INVALID_VALUE,
                $next
            )
        );
    }

    /**
     * @param RuleInterface|null $next
     *
     * @return RuleInterface
     */
    public static function asSanitizedEmail(RuleInterface $next = null): RuleInterface
    {
        return self::isString(
            self::filter(
                FILTER_SANITIZE_EMAIL,
                null,
                ErrorCodes::INVALID_VALUE,
                Messages::INVALID_VALUE,
                $next
            )
        );
    }

    /**
     * @param RuleInterface|null $next
     *
     * @return RuleInterface
     */
    public static function asSanitizedUrl(RuleInterface $next = null): RuleInterface
    {
        return self::isString(
            self::filter(
                FILTER_SANITIZE_URL,
                null,
                ErrorCodes::INVALID_VALUE,
                Messages::INVALID_VALUE,
                $next
            )
        );
    }

    /**
     * @param RuleInterface|null $next
     * @return RuleInterface
     */
    public static function asSanitizedUuid(RuleInterface $next = null): RuleInterface
    {
        return self::isUuid();
    }

    /**
     * @param RuleInterface|null $next
     *
     * @return RuleInterface
     */
    public static function asJsonApiDateTime(RuleInterface $next = null): RuleInterface
    {
        return self::stringToDateTime(DateTime::JSON_API_FORMAT, $next);
    }

    /**
     * @param RuleInterface|null $next
     *
     * @return RuleInterface
     */
    public static function asJsonApiDate(RuleInterface $next = null): RuleInterface
    {
        return self::stringToDateTime(Date::JSON_API_FORMAT, $next);
    }

    /**
     * @param RuleInterface|null $next
     *
     * @return RuleInterface
     */
    public static function asJsonApiTime(RuleInterface $next = null): RuleInterface
    {
        return self::stringToDateTime(Time::JSON_API_FORMAT, $next);
    }
}
