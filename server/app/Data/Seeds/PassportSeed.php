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

namespace App\Data\Seeds;

use App\Data\Models\RoleOAuthScope;
use Doctrine\DBAL\Exception as DBALException;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Whoa\Contracts\Data\SeedInterface;
use Whoa\Contracts\Data\TimestampFields;
use Whoa\Contracts\Settings\Packages\PassportSettingsInterface;
use Whoa\Contracts\Settings\SettingsProviderInterface;
use Whoa\Data\Seeds\SeedTrait;
use Whoa\Passport\Adaptors\Generic\Client;
use Whoa\Passport\Adaptors\Generic\Scope;
use Whoa\Passport\Contracts\PassportServerIntegrationInterface;
use Whoa\Passport\Traits\PassportSeedTrait;
use Settings\Passport as S;

/**
 * @package App
 */
class PassportSeed implements SeedInterface
{
    use PassportSeedTrait;
    use SeedTrait;

    //region Scope fields

    /** @var string Scope name */
    public const SCOPE_NAME = 'name';

    /** @var string Scope identifier */
    public const SCOPE_IDENTIFIER = 'identifier';

    /** @var string Scope description */
    public const SCOPE_DESCRIPTION = 'description';
    //endregion

    //region OAuth scopes

    //region Administrative scope

    /** @var string Scope name */
    public const SCOPE_NAME_OAUTH_WRITE = 'OAuth administrative scope';

    /** @var string Scope identifier */
    public const SCOPE_IDENTIFIER_OAUTH_WRITE = 'OAuth.Write';

    /** @var string Scope description */
    public const SCOPE_DESCRIPTION_OAUTH_WRITE = 'Can create, update and delete OAuth client, redirect URI and scope.';
    //endregion

    //region Read scope

    /** @var string Scope name */
    public const SCOPE_NAME_OAUTH_READ = 'OAuth read scope';

    /** @var string Scope identifier */
    public const SCOPE_IDENTIFIER_OAUTH_READ = 'OAuth.Read';

    /** @var string Scope description */
    public const SCOPE_DESCRIPTION_OAUTH_READ = 'Can read OAuth clients, redirect URIs and scopes.';
    //endregion
    //endregion

    //region Role scopes

    //region Administrative scope

    /** @var string Scope name */
    public const SCOPE_NAME_ROLE_WRITE = 'Role administrative scope';

    /** @var string Scope identifier */
    public const SCOPE_IDENTIFIER_ROLE_WRITE = 'Role.Write';

    /** @var string Scope description */
    public const SCOPE_DESCRIPTION_ROLE_WRITE = 'Can create, update and delete role.';
    //endregion

    //region Read scope

    /** @var string Scope name */
    public const SCOPE_NAME_ROLE_READ = 'Role read scope';

    /** @var string Scope identifier */
    public const SCOPE_IDENTIFIER_ROLE_READ = 'Role.Read';

    /** @var string Scope description */
    public const SCOPE_DESCRIPTION_ROLE_READ = 'Can read roles.';
    //endregion
    //endregion

    //region User scopes

    //region Administrative scope

    /** @var string Scope name */
    public const SCOPE_NAME_USER_WRITE = 'User administrative scope';

    /** @var string Scope identifier */
    public const SCOPE_IDENTIFIER_USER_WRITE = 'User.Write';

    /** @var string Scope description */
    public const SCOPE_DESCRIPTION_USER_WRITE = 'Can create, update and delete user.';
    //endregion

    //region Read scope

    /** @var string Scope name */
    public const SCOPE_NAME_USER_READ = 'User read scope';

    /** @var string Scope identifier */
    public const SCOPE_IDENTIFIER_USER_READ = 'User.Read';

    /** @var string Scope description */
    public const SCOPE_DESCRIPTION_USER_READ = 'Can read users.';
    //endregion
    //endregion

    /** @var array Default scopes */
    public const DEFAULT_SCOPES = [
        //region OAuth scopes

        [
            self::SCOPE_NAME => self::SCOPE_NAME_OAUTH_WRITE,
            self::SCOPE_IDENTIFIER => self::SCOPE_IDENTIFIER_OAUTH_WRITE,
            self::SCOPE_DESCRIPTION => self::SCOPE_DESCRIPTION_OAUTH_WRITE,
        ],
        [
            self::SCOPE_NAME => self::SCOPE_NAME_OAUTH_READ,
            self::SCOPE_IDENTIFIER => self::SCOPE_IDENTIFIER_OAUTH_READ,
            self::SCOPE_DESCRIPTION => self::SCOPE_DESCRIPTION_OAUTH_READ,
        ],
        //endregion
        //region Role scopes

        [
            self::SCOPE_NAME => self::SCOPE_NAME_ROLE_WRITE,
            self::SCOPE_IDENTIFIER => self::SCOPE_IDENTIFIER_ROLE_WRITE,
            self::SCOPE_DESCRIPTION => self::SCOPE_DESCRIPTION_ROLE_WRITE,
        ],
        [
            self::SCOPE_NAME => self::SCOPE_NAME_ROLE_READ,
            self::SCOPE_IDENTIFIER => self::SCOPE_IDENTIFIER_ROLE_READ,
            self::SCOPE_DESCRIPTION => self::SCOPE_DESCRIPTION_ROLE_READ,
        ],
        //endregion
        //region User scopes

        [
            self::SCOPE_NAME => self::SCOPE_NAME_USER_WRITE,
            self::SCOPE_IDENTIFIER => self::SCOPE_IDENTIFIER_USER_WRITE,
            self::SCOPE_DESCRIPTION => self::SCOPE_DESCRIPTION_USER_WRITE,
        ],
        [
            self::SCOPE_NAME => self::SCOPE_NAME_USER_READ,
            self::SCOPE_IDENTIFIER => self::SCOPE_IDENTIFIER_USER_READ,
            self::SCOPE_DESCRIPTION => self::SCOPE_DESCRIPTION_USER_READ,
        ],
        //endregion
    ];

    /**
     * @inheritdoc
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws DBALException
     */
    public function run(): void
    {
        /** @var PassportServerIntegrationInterface $integration */
        $container = $this->getContainer();
        $settings = $container->get(SettingsProviderInterface::class)->get(S::class);
        $integration = $container->get(PassportServerIntegrationInterface::class);

        // create OAuth scopes

        // scope identifier => description (don't hesitate to add required for your application)
        $scopeRepo = $integration->getScopeRepository();
        $scopes = [];
        foreach (static::DEFAULT_SCOPES as $defaultScope) {
            $scope = $scopeRepo->create(
                (new Scope())
                    ->setIdentifier($defaultScope[static::SCOPE_IDENTIFIER])
                    ->setName($defaultScope[static::SCOPE_NAME])
                    ->setDescription($defaultScope[static::SCOPE_DESCRIPTION])
            );
            $scopes[$defaultScope[static::SCOPE_IDENTIFIER]] = $scope->getIdentity();
        }

        // create OAuth clients

        $client = (new Client())
            ->setIdentifier($settings[PassportSettingsInterface::KEY_DEFAULT_CLIENT_ID])
            ->setName($settings[S::KEY_DEFAULT_CLIENT_NAME])
            ->setPublic()
            ->useDefaultScopesOnEmptyRequest()
            ->disableScopeExcess()
            ->enablePasswordGrant()
            ->disableCodeGrant()
            ->disableImplicitGrant()
            ->disableClientGrant()
            ->enableRefreshGrant()
            ->setScopeIdentifiers(array_keys($scopes));

        $this->seedClient($integration, $client, [], $settings[S::KEY_DEFAULT_CLIENT_REDIRECT_URIS] ?? []);

        // assign scopes to roles
        $this->assignScopes(RolesSeed::ID_ADMINISTRATORS, [
            $scopes[static::SCOPE_IDENTIFIER_OAUTH_WRITE],
            $scopes[static::SCOPE_IDENTIFIER_OAUTH_READ],
            $scopes[static::SCOPE_IDENTIFIER_ROLE_WRITE],
            $scopes[static::SCOPE_IDENTIFIER_ROLE_READ],
            $scopes[static::SCOPE_IDENTIFIER_USER_WRITE],
            $scopes[static::SCOPE_IDENTIFIER_USER_READ],
        ]);

        $this->assignScopes(RolesSeed::ID_MODERATORS, [
            $scopes[static::SCOPE_IDENTIFIER_ROLE_WRITE],
            $scopes[static::SCOPE_IDENTIFIER_ROLE_READ],
            $scopes[static::SCOPE_IDENTIFIER_USER_WRITE],
            $scopes[static::SCOPE_IDENTIFIER_USER_READ],
        ]);

        $this->assignScopes(RolesSeed::ID_USERS, [
            $scopes[static::SCOPE_IDENTIFIER_ROLE_READ],
            $scopes[static::SCOPE_IDENTIFIER_USER_READ],
        ]);
    }

    /**
     * @param int $roleId
     * @param int[] $scopeIds
     * @return void
     * @throws ContainerExceptionInterface
     * @throws DBALException
     * @throws NotFoundExceptionInterface
     */
    private function assignScopes(int $roleId, array $scopeIds)
    {
        foreach ($scopeIds as $scopeId) {
            $this->seedRowData(RoleOAuthScope::TABLE_NAME, [
                RoleOAuthScope::FIELD_ID_ROLE => $roleId,
                RoleOAuthScope::FIELD_ID_SCOPE => $scopeId,
                TimestampFields::FIELD_CREATED_AT => $this->now(),
            ]);
        }
    }
}
