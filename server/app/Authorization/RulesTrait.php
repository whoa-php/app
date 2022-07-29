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
     * @return bool
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
     * @return int|string|null
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
