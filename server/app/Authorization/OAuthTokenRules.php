<?php

declare(strict_types=1);

namespace App\Authorization;

use App\Data\Seeds\PassportSeed;
use App\Json\Schemas\OAuthTokenSchema as Schema;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Whoa\Application\Contracts\Authorization\ResourceAuthorizationRulesInterface;
use Whoa\Auth\Contracts\Authorization\PolicyInformation\ContextInterface;

/**
 * @package App
 */
class OAuthTokenRules implements ResourceAuthorizationRulesInterface
{
    use RulesTrait;

    /** @var string Action name */
    public const ACTION_VIEW_OAUTH_TOKENS = 'canViewOAuthTokens';

    /** @var string Action name */
    public const ACTION_CREATE_OAUTH_TOKEN = 'canCreateOAuthToken';

    /** @var string Action name */
    public const ACTION_EDIT_OAUTH_TOKEN = 'canEditOAuthToken';

    /** @var string Action name */
    public const ACTION_VIEW_OAUTH_USER = 'canViewOAuthUser';

    /** @var string Action name */
    public const ACTION_VIEW_OAUTH_CLIENT = 'canViewOAuthClient';

    /** @var string Action name */
    public const ACTION_VIEW_OAUTH_SCOPES = 'canViewOAuthScopes';

    /**
     * @inheritDoc
     */
    public static function getResourcesType(): string
    {
        return Schema::TYPE;
    }

    /**
     * @param ContextInterface $context
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function canViewOAuthTokens(ContextInterface $context): bool
    {
        return self::hasScope($context, PassportSeed::SCOPE_IDENTIFIER_ADMIN_OAUTH);
    }

    /**
     * @param ContextInterface $context
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function canCreateOAuthToken(ContextInterface $context): bool
    {
        return self::hasScope($context, PassportSeed::SCOPE_IDENTIFIER_ADMIN_OAUTH);
    }

    /**
     * @param ContextInterface $context
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function canEditOAuthToken(ContextInterface $context): bool
    {
        return self::hasScope($context, PassportSeed::SCOPE_IDENTIFIER_ADMIN_OAUTH);
    }

    /**
     * @param ContextInterface $context
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function canViewOAuthUser(ContextInterface $context): bool
    {
        return UserRules::canViewUsers($context);
    }

    /**
     * @param ContextInterface $context
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function canViewOAuthClient(ContextInterface $context): bool
    {
        return OAuthClientRules::canViewOAuthClients($context);
    }

    /**
     * @param ContextInterface $context
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function canViewOAuthScopes(ContextInterface $context): bool
    {
        return OAuthScopeRules::canViewOAuthScopes($context);
    }
}
