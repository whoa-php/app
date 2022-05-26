<?php

declare(strict_types=1);

namespace App\Api;

use App\Authorization\UserRules as Rules;
use App\Data\Models\RoleOAuthScope;
use App\Data\Models\User as Model;
use App\Json\Schemas\UserSchema as Schema;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Doctrine\DBAL\Exception as DBALException;
use Whoa\Contracts\Exceptions\AuthorizationExceptionInterface;
use Whoa\Crypt\Contracts\HasherInterface;
use Whoa\Flute\Contracts\Models\PaginatedDataInterface;
use PDO;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Whoa\Passport\Contracts\Models\ScopeModelInterface;

/**
 * @package App
 */
class UsersApi extends BaseApi
{
    /**
     * @param ContainerInterface $container
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container, Model::class);
    }

    /**
     * @inheritdoc
     * @param string|null $index
     * @param iterable $attributes
     * @param iterable $toMany
     * @return string
     * @throws AuthorizationExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws DBALException
     * @throws NotFoundExceptionInterface
     */
    public function create(?string $index, iterable $attributes, iterable $toMany): string
    {
        $this->authorize(Rules::ACTION_CREATE_USER, Schema::TYPE);

        return parent::create($index, $this->getReplacePasswordWithHash((array)$attributes), (array)$toMany);
    }

    /**
     * @inheritdoc
     * @param string $index
     * @param array $attributes
     * @param array $toMany
     * @return int
     * @throws AuthorizationExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws DBALException
     * @throws NotFoundExceptionInterface
     */
    public function update(string $index, array $attributes, array $toMany): int
    {
        $this->authorize(Rules::ACTION_EDIT_USER, Schema::TYPE, $index);

        return parent::update($index, $this->getReplacePasswordWithHash((array)$attributes), $toMany);
    }

    /**
     * @inheritdoc
     * @param string $index
     * @return bool
     * @throws AuthorizationExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws DBALException
     * @throws NotFoundExceptionInterface
     */
    public function remove(string $index): bool
    {
        $this->authorize(Rules::ACTION_EDIT_USER, Schema::TYPE, $index);

        return parent::remove($index);
    }

    /**
     * @inheritdoc
     * @return PaginatedDataInterface
     * @throws AuthorizationExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws DBALException
     * @throws NotFoundExceptionInterface
     */
    public function index(): PaginatedDataInterface
    {
        $this->authorize(Rules::ACTION_VIEW_USERS, Schema::TYPE);

        return parent::index();
    }

    /**
     * @inheritdoc
     * @param string $index
     * @return mixed|null
     * @throws AuthorizationExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws DBALException
     * @throws NotFoundExceptionInterface
     */
    public function read(string $index)
    {
        $this->authorize(Rules::ACTION_VIEW_USERS, Schema::TYPE, $index);

        return parent::read($index);
    }

    /**
     * @param int $userId
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws DBALException
     * @throws DBALDriverException
     */
    public function noAuthReadScopes(int $userId): array
    {
        /** @var Connection $connection */
        $connection = $this->getContainer()->get(Connection::class);
        $query = $connection->createQueryBuilder();
        $users = 'u';
        $uUserId = Model::FIELD_ID;
        $uRoleId = Model::FIELD_ID_ROLE;
        $rolesScopes = 'rs';
        $rsRoleId = RoleOAuthScope::FIELD_ID_ROLE;
        $rsScopeId = RoleOAuthScope::FIELD_ID_SCOPE;
        $scopes = 's';
        $sScopeId = ScopeModelInterface::FIELD_ID;
        $sScopeIdentifier = ScopeModelInterface::FIELD_IDENTIFIER;
        $query
            ->select("$scopes.$sScopeIdentifier")
            ->from(Model::TABLE_NAME, $users)
            ->leftJoin(
                $users,
                RoleOAuthScope::TABLE_NAME,
                $rolesScopes,
                "$users.$uRoleId = $rolesScopes.$rsRoleId"
            )
            ->leftJoin(
                $rolesScopes,
                ScopeModelInterface::TABLE_NAME,
                $scopes,
                "$rolesScopes.$rsScopeId = $scopes.$sScopeId"
            )
            ->where("$users.$uUserId = {$query->createPositionalParameter($userId, PDO::PARAM_INT)}");
        
        return array_column($query->execute()->fetchAllAssociative(), ScopeModelInterface::FIELD_IDENTIFIER);
    }

    /**
     * @param string $email
     * @return int|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws DBALException
     * @throws DBALDriverException
     */
    public function noAuthReadUserIdByEmail(string $email): ?int
    {
        /** @var Connection $connection */
        $connection = $this->getContainer()->get(Connection::class);
        $query = $connection->createQueryBuilder();
        $query
            ->select(Model::FIELD_ID)
            ->from(Model::TABLE_NAME)
            ->where(Model::FIELD_EMAIL . '=' . $query->createPositionalParameter($email))
            ->setMaxResults(1);
        $statement = $query->execute();
        $idOrFalse = $statement->fetchOne();

        return $idOrFalse === false ? null : (int)$idOrFalse;
    }

    /**
     * @param int $userId
     * @param string $newPassword
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Doctrine\DBAL\DBALException
     */
    public function noAuthResetPassword(int $userId, string $newPassword): bool
    {
        $hash = $this->createHasher()->hash($newPassword);

        try {
            $changed = parent::update((string)$userId, [Model::FIELD_PASSWORD_HASH => $hash], []);

            return $changed > 0;
        } catch (DBALException $exception) {
            return false;
        }
    }

    /**
     * @param array $attributes
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getReplacePasswordWithHash(array $attributes): array
    {
        // in attributes were captured validated input password we need to convert it into password hash
        if (array_key_exists(Schema::CAPTURE_NAME_PASSWORD, $attributes) === true) {
            $attributes[Model::FIELD_PASSWORD_HASH] =
                $this->createHasher()->hash($attributes[Schema::CAPTURE_NAME_PASSWORD]);
        }

        return $attributes;
    }

    /**
     * @return HasherInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function createHasher(): HasherInterface
    {
        return $this->getContainer()->get(HasherInterface::class);
    }

    /**
     * @param string|int $index
     * @param iterable|null $relationshipFilters
     * @param iterable|null $relationshipSorts
     * @return PaginatedDataInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws AuthorizationExceptionInterface
     */
    public function readRole(
        $index,
        iterable $relationshipFilters = null,
        iterable $relationshipSorts = null
    ): PaginatedDataInterface {
        $this->authorize(Rules::ACTION_VIEW_ROLE, Schema::TYPE, $index);

        return $this->readRelationshipInt($index, Model::REL_ROLE, $relationshipFilters, $relationshipSorts);
    }

    /**
     * @param string|int $index
     * @param iterable|null $relationshipFilters
     * @param iterable|null $relationshipSorts
     * @return PaginatedDataInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws AuthorizationExceptionInterface
     */
    public function readOAuthTokens(
        $index,
        iterable $relationshipFilters = null,
        iterable $relationshipSorts = null
    ): PaginatedDataInterface {
        $this->authorize(Rules::ACTION_VIEW_OAUTH_TOKENS, Schema::TYPE, $index);

        return $this->readRelationshipInt(
            $index,
            Model::REL_OAUTH_TOKENS,
            $relationshipFilters,
            $relationshipSorts
        );
    }
}
