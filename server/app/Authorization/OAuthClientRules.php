<?php

/**
 * Copyright 2021-2022 info@whoaphp.com
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace App\Authorization;

use App\Data\Seeds\PassportSeed;
use App\Json\Schemas\OAuthClientSchema as Schema;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Whoa\Application\Contracts\Authorization\ResourceAuthorizationRulesInterface;
use Whoa\Auth\Contracts\Authorization\PolicyInformation\ContextInterface;

/**
 * @package App
 */
class OAuthClientRules implements ResourceAuthorizationRulesInterface
{
    use RulesTrait;

    /** @var string Action name */
    public const ACTION_CREATE_OAUTH_CLIENT = 'canCreateOAuthClient';

    /** @var string Action name */
    public const ACTION_READ_OAUTH_CLIENTS = 'canReadOAuthClients';

    /** @var string Action name */
    public const ACTION_UPDATE_OAUTH_CLIENT = 'canUpdateOAuthClient';

    /** @var string Action name */
    public const ACTION_DELETE_OAUTH_CLIENT = 'canDeleteOAuthClient';

    /** @var string Action name */
    public const ACTION_READ_OAUTH_REDIRECT_URIS = 'canReadOAuthRedirectUris';

    /** @var string Action name */
    public const ACTION_READ_OAUTH_TOKENS = 'canReadOAuthTokens';

    /** @var string Action name */
    public const ACTION_READ_OAUTH_SCOPES = 'canReadOAuthScopes';

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
    public static function canCreateOAuthClient(ContextInterface $context): bool
    {
        return self::hasScope($context, PassportSeed::SCOPE_IDENTIFIER_OAUTH_WRITE) === true;
    }

    /**
     * @param ContextInterface $context
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function canReadOAuthClients(ContextInterface $context): bool
    {
        return self::hasScope($context, PassportSeed::SCOPE_IDENTIFIER_OAUTH_READ) === true;
    }

    /**
     * @param ContextInterface $context
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function canUpdateOAuthClient(ContextInterface $context): bool
    {
        return self::hasScope($context, PassportSeed::SCOPE_IDENTIFIER_OAUTH_WRITE) === true;
    }

    /**
     * @param ContextInterface $context
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function canDeleteOAuthClient(ContextInterface $context): bool
    {
        return self::hasScope($context, PassportSeed::SCOPE_IDENTIFIER_OAUTH_WRITE) === true;
    }

    /**
     * @param ContextInterface $context
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function canReadOAuthRedirectUris(ContextInterface $context): bool
    {
        return self::canReadOAuthClients($context) === true &&
            OAuthRedirectUriRules::canReadOAuthRedirectUris($context) === true;
    }

    /**
     * @param ContextInterface $context
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function canReadOAuthTokens(ContextInterface $context): bool
    {
        return self::canReadOAuthClients($context) === true &&
            OAuthTokenRules::canReadOAuthTokens($context) === true;
    }

    /**
     * @param ContextInterface $context
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function canReadOAuthScopes(ContextInterface $context): bool
    {
        return self::canReadOAuthClients($context) === true &&
            OAuthScopeRules::canReadOAuthScopes($context) === true;
    }
}
