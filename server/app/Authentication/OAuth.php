<?php

declare(strict_types=1);

namespace App\Authentication;

use App\Api\UsersApi;
use App\Data\Models\User;
use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder as DBALQueryBuilder;
use Whoa\Contracts\Data\TimestampFields;
use Whoa\Contracts\Data\UuidFields;
use Whoa\Crypt\Contracts\HasherInterface;
use Whoa\Flute\Contracts\FactoryInterface;
use Whoa\Passport\Contracts\Entities\TokenInterface;
use PDO;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @package App
 */
final class OAuth
{
    /** @var callable */
    public const USER_VALIDATOR = [self::class, 'validateUser'];

    /** @var callable */
    public const SCOPE_VALIDATOR = [self::class, 'validateScope'];

    /** @var callable */
    public const TOKEN_CUSTOM_PROPERTIES_PROVIDER = [self::class, 'getTokenCustomProperties'];

    /**
     * @param ContainerInterface $container
     * @param string $userName
     * @param string|null $password
     *
     * @return int|null
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function validateUser(
        ContainerInterface $container,
        string $userName,
        ?string $password = null
    ): ?int {
        try {
            /** @var DBALConnection $connection */
            $connection = $container->get(DBALConnection::class);
            $query = $connection->createQueryBuilder();
            $query
                ->select([User::FIELD_ID, User::FIELD_EMAIL, User::FIELD_PASSWORD_HASH])
                ->from(User::TABLE_NAME)
                ->where($query->expr()->eq(User::FIELD_EMAIL, $query->createPositionalParameter($userName)))
                ->setMaxResults(1);

            $user = $query->execute()->fetchAssociative();
            if ($user === false) {
                return null;
            }

            if (isset($password) === true) {
                /** @var HasherInterface $hasher */
                $hasher = $container->get(HasherInterface::class);
                if ($hasher->verify($password, $user[User::FIELD_PASSWORD_HASH]) === false) {
                    return null;
                }
            }

            return (int)$user[User::FIELD_ID];
        } catch (DBALDriverException|DBALException $exception) {
            return null;
        }
    }

    /**
     * @param ContainerInterface $container
     * @param int $userId
     * @param array|null $scope
     *
     * @return null|array
     *
     * @throws ContainerExceptionInterface
     * @throws DBALDriverException
     * @throws DBALException
     * @throws NotFoundExceptionInterface
     */
    public static function validateScope(ContainerInterface $container, int $userId, array $scope = null): ?array
    {
        // Here is the place you can implement your scope limitation for users. Such as
        // limiting scopes that could be assigned for the user token.
        // It could be role based system or any other system that suits your application.
        //
        // Possible return values:
        // - `null` means no scope changes for the user.
        // - `array` with scope identities. Token issued for the user will be limited to this scope.
        // - authorization exception if you want to stop token issuing process and notify the user
        //   do not have enough rights to issue requested scopes.

        $result = null;
        if ($scope !== null) {
            /** @var UsersApi $usersApi */
            /** @var FactoryInterface $factory */
            $factory = $container->get(FactoryInterface::class);
            $usersApi = $factory->createApi(UsersApi::class);

            $userScopes = $usersApi->noAuthReadScopes($userId);
            $adjustedScope = array_intersect($userScopes, $scope);
            if (count($adjustedScope) !== count($scope)) {
                $result = $adjustedScope;
            }
        }

        return $result;
    }

    /**
     * @param ContainerInterface $container
     * @param TokenInterface $token
     *
     * @return array
     *
     * @throws ContainerExceptionInterface
     * @throws DBALDriverException
     * @throws DBALException
     * @throws NotFoundExceptionInterface
     */
    public static function getTokenCustomProperties(ContainerInterface $container, TokenInterface $token): array
    {
        $userId = (string)$token->getUserIdentifier();

        /** @var DBALConnection $connection */
        /** @var DBALQueryBuilder $query */
        $connection = $container->get(DBALConnection::class);
        $query = $connection->createQueryBuilder();
        $users = 'u';
        $uUserId = User::FIELD_ID;
        $query
            ->select([
                $users . '.' . UuidFields::FIELD_UUID . ' AS `' . User::FIELD_ID . '`',
                $users . '.' . User::FIELD_EMAIL,
            ])
            ->from(User::TABLE_NAME, $users)
            ->where(
                $users . '.' . User::FIELD_ID . '=' . $query->createPositionalParameter($userId, ParameterType::STRING)
            )
            ->orderBy($users . '.' . TimestampFields::FIELD_CREATED_AT, 'DESC')
            ->setMaxResults(1);

        return $query->execute()->fetchAssociative();
    }
}
