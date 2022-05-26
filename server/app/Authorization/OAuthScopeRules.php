<?php

declare(strict_types=1);

namespace App\Authorization;

use App\Data\Seeds\PassportSeed;
use App\Json\Schemas\OAuthScopeSchema as Schema;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Whoa\Application\Contracts\Authorization\ResourceAuthorizationRulesInterface;
use Whoa\Auth\Contracts\Authorization\PolicyInformation\ContextInterface;

/**
 * @package App
 */
class OAuthScopeRules implements ResourceAuthorizationRulesInterface
{
    use RulesTrait;

    /** @var string Action name */
    public const ACTION_VIEW_OAUTH_SCOPES = 'canViewOAuthScopes';

    /** @var string Action name */
    public const ACTION_CREATE_OAUTH_SCOPE = 'canCreateOAuthScope';

    /** @var string Action name */
    public const ACTION_EDIT_OAUTH_SCOPE = 'canEditOAuthScope';

    /** @var string Action name */
    public const ACTION_VIEW_OAUTH_CLIENTS = 'canViewOAuthClients';

    /** @var string Action name */
    public const ACTION_VIEW_OAUTH_TOKENS = 'canViewOAuthTokens';

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
    public static function canViewOAuthScopes(ContextInterface $context): bool
    {
        return self::hasScope($context, PassportSeed::SCOPE_IDENTIFIER_ADMIN_OAUTH);
    }

    /**
     * @param ContextInterface $context
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function canCreateOAuthScope(ContextInterface $context): bool
    {
        return self::hasScope($context, PassportSeed::SCOPE_IDENTIFIER_ADMIN_OAUTH);
    }

    /**
     * @param ContextInterface $context
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function canEditOAuthScope(ContextInterface $context): bool
    {
        return self::hasScope($context, PassportSeed::SCOPE_IDENTIFIER_ADMIN_OAUTH);
    }

    /**
     * @param ContextInterface $context
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function canViewOAuthClients(ContextInterface $context): bool
    {
        return OAuthClientRules::canViewOAuthClients($context);
    }

    /**
     * @param ContextInterface $context
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function canViewOAuthTokens(ContextInterface $context): bool
    {
        return OAuthTokenRules::canViewOAuthTokens($context);
    }
}
