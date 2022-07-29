<?php

/**
 * Copyright 2015-2019 info@neomerx.com
 * Modification Copyright 2021-2022 info@whoaphp.com
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
use App\Json\Schemas\UserSchema as Schema;
use Whoa\Application\Contracts\Authorization\ResourceAuthorizationRulesInterface;
use Whoa\Auth\Contracts\Authorization\PolicyInformation\ContextInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @package App
 */
class UserRules implements ResourceAuthorizationRulesInterface
{
    use RulesTrait;

    /** @var string Action name */
    public const ACTION_VIEW_USERS = 'canViewUsers';

    /** @var string Action name */
    public const ACTION_CREATE_USER = 'canCreateUser';

    /** @var string Action name */
    public const ACTION_EDIT_USER = 'canEditUser';

    /** @var string Action name */
    public const ACTION_VIEW_ROLE = 'canViewRole';

    /** @var string Action name */
    public const ACTION_VIEW_OAUTH_TOKENS = 'canViewOAuthTokens';

    /**
     * @inheritdoc
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
    public static function canViewUsers(ContextInterface $context): bool
    {
        return self::hasScope($context, PassportSeed::SCOPE_IDENTIFIER_VIEW_USERS);
    }

    /**
     * @param ContextInterface $context
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function canCreateUser(ContextInterface $context): bool
    {
        return self::hasScope($context, PassportSeed::SCOPE_IDENTIFIER_ADMIN_USERS);
    }

    /**
     * @param ContextInterface $context
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function canEditUser(ContextInterface $context): bool
    {
        return self::hasScope($context, PassportSeed::SCOPE_IDENTIFIER_ADMIN_USERS);
    }

    /**
     * @param ContextInterface $context
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function canViewRole(ContextInterface $context): bool
    {
        return RoleRules::canViewRoles($context) === true &&
            self::canViewUsers($context) === true;
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
}
