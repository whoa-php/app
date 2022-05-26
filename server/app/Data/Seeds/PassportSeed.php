<?php

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

    /** @var string Scope identifier */
    public const SCOPE_IDENTIFIER = 'identifier';

    /** @var string Scope identifier */
    public const SCOPE_IDENTIFIER_ADMIN_OAUTH = 'manage_oauth';

    /** @var string Scope identifier */
    public const SCOPE_IDENTIFIER_ADMIN_ROLES = 'manage_roles';

    /** @var string Scope identifier */
    public const SCOPE_IDENTIFIER_ADMIN_USERS = 'manage_users';

    /** @var string Scope identifier */
    public const SCOPE_IDENTIFIER_VIEW_ROLES = 'view_roles';

    /** @var string Scope identifier */
    public const SCOPE_IDENTIFIER_VIEW_USERS = 'view_users';

    /** @var string Scope name */
    public const SCOPE_NAME = 'name';

    /** @var string Scope name */
    public const SCOPE_NAME_ADMIN_OAUTH = 'OAuth management';

    /** @var string Scope name */
    public const SCOPE_NAME_ADMIN_ROLES = 'Roles management';

    /** @var string Scope name */
    public const SCOPE_NAME_ADMIN_USERS = 'Users management';

    /** @var string Scope name */
    public const SCOPE_NAME_VIEW_ROLES = 'Roles access';

    /** @var string Scope name */
    public const SCOPE_NAME_VIEW_USERS = 'Users access';

    /** @var string Scope description */
    public const SCOPE_DESCRIPTION = 'description';

    /** @var string Scope description */
    public const SCOPE_DESCRIPTION_ADMIN_OAUTH = 'Can create, update and delete OAuth clients, redirect URIs and scopes.';

    /** @var string Scope description */
    public const SCOPE_DESCRIPTION_ADMIN_ROLES = 'Can create, update and delete roles.';

    /** @var string Scope description */
    public const SCOPE_DESCRIPTION_ADMIN_USERS = 'Can create, update and delete users.';

    /** @var string Scope description */
    public const SCOPE_DESCRIPTION_VIEW_ROLES = 'Can view roles.';

    /** @var string Scope description */
    public const SCOPE_DESCRIPTION_VIEW_USERS = 'Can view users.';

    /** @var array Default scopes */
    public const DEFAULT_SCOPES = [
        [
            self::SCOPE_IDENTIFIER => self::SCOPE_IDENTIFIER_ADMIN_OAUTH,
            self::SCOPE_NAME => self::SCOPE_NAME_ADMIN_OAUTH,
            self::SCOPE_DESCRIPTION => self::SCOPE_DESCRIPTION_ADMIN_OAUTH,
        ],
        [
            self::SCOPE_IDENTIFIER => self::SCOPE_IDENTIFIER_ADMIN_ROLES,
            self::SCOPE_NAME => self::SCOPE_NAME_ADMIN_ROLES,
            self::SCOPE_DESCRIPTION => self::SCOPE_DESCRIPTION_ADMIN_ROLES,
        ],
        [
            self::SCOPE_IDENTIFIER => self::SCOPE_IDENTIFIER_ADMIN_USERS,
            self::SCOPE_NAME => self::SCOPE_NAME_ADMIN_USERS,
            self::SCOPE_DESCRIPTION => self::SCOPE_DESCRIPTION_ADMIN_USERS,
        ],
        [
            self::SCOPE_IDENTIFIER => self::SCOPE_IDENTIFIER_VIEW_ROLES,
            self::SCOPE_NAME => self::SCOPE_NAME_VIEW_ROLES,
            self::SCOPE_DESCRIPTION => self::SCOPE_DESCRIPTION_VIEW_ROLES,
        ],
        [
            self::SCOPE_IDENTIFIER => self::SCOPE_IDENTIFIER_VIEW_USERS,
            self::SCOPE_NAME => self::SCOPE_NAME_VIEW_USERS,
            self::SCOPE_DESCRIPTION => self::SCOPE_DESCRIPTION_VIEW_USERS,
        ],
    ];

    /**
     * @inheritdoc
     *
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
            $scopes[static::SCOPE_IDENTIFIER_ADMIN_OAUTH],
            $scopes[static::SCOPE_IDENTIFIER_ADMIN_ROLES],
            $scopes[static::SCOPE_IDENTIFIER_ADMIN_USERS],
            $scopes[static::SCOPE_IDENTIFIER_VIEW_ROLES],
            $scopes[static::SCOPE_IDENTIFIER_VIEW_USERS],
        ]);

        $this->assignScopes(RolesSeed::ID_MODERATORS, [
            $scopes[static::SCOPE_IDENTIFIER_ADMIN_ROLES],
            $scopes[static::SCOPE_IDENTIFIER_ADMIN_USERS],
            $scopes[static::SCOPE_IDENTIFIER_VIEW_ROLES],
            $scopes[static::SCOPE_IDENTIFIER_VIEW_USERS],
        ]);

        $this->assignScopes(RolesSeed::ID_USERS, [
            $scopes[static::SCOPE_IDENTIFIER_VIEW_ROLES],
            $scopes[static::SCOPE_IDENTIFIER_VIEW_USERS],
        ]);
    }

    /**
     * @param int $roleId
     * @param int[] $scopeIds
     *
     * @return void
     *
     * @throws DBALException
     * @throws Exception
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
