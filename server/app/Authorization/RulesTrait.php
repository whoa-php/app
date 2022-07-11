<?php

declare(strict_types=1);

namespace App\Authorization;

use Whoa\Application\Authorization\AuthorizationRulesTrait;
use Whoa\Auth\Contracts\Authorization\PolicyInformation\ContextInterface;
use Whoa\Contracts\Passport\PassportAccountInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @package App
 */
trait RulesTrait
{
    use AuthorizationRulesTrait;

    /**
     * @param ContextInterface $context
     * @param string $scope
     *
     * @return bool
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected static function hasScope(ContextInterface $context, string $scope): bool
    {
        $result = false;

        if (static::ctxHasCurrentAccount($context) === true) {
            /** @var PassportAccountInterface $account */
            $account = self::ctxGetCurrentAccount($context);
            $result = $account->hasScope($scope);
        }

        return $result;
    }

    /**
     * @param ContextInterface $context
     *
     * @return int|string|null
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected static function getCurrentUserIdentity(ContextInterface $context)
    {
        $userId = null;

        /** @var PassportAccountInterface $account */
        if (self::ctxHasCurrentAccount($context) === true &&
            ($account = self::ctxGetCurrentAccount($context)) !== null &&
            $account->hasUserIdentity() === true
        ) {
            $userId = $account->getUserIdentity();
        }

        return $userId;
    }
}
