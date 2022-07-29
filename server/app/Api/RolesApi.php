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

namespace App\Api;

use App\Authorization\RoleRules as Rules;
use App\Data\Models\Role as Model;
use App\Json\Schemas\RoleSchema as Schema;
use Doctrine\DBAL\Exception as DBALException;
use Whoa\Contracts\Exceptions\AuthorizationExceptionInterface;
use Whoa\Flute\Contracts\Models\PaginatedDataInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @package App
 */
class RolesApi extends BaseApi
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
     * @throws NotFoundExceptionInterface
     * @throws DBALException
     */
    public function create(?string $index, iterable $attributes, iterable $toMany): string
    {
        $this->authorize(Rules::ACTION_CREATE_ROLE, Schema::TYPE, $index);

        return parent::create($index, (array)$attributes, (array)$toMany);
    }

    /**
     * @inheritdoc
     * @param string $index
     * @param iterable $attributes
     * @param iterable $toMany
     * @return int
     * @throws AuthorizationExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws DBALException
     */
    public function update(string $index, iterable $attributes, iterable $toMany): int
    {
        $this->authorize(Rules::ACTION_EDIT_ROLE, Schema::TYPE, $index);

        return parent::update($index, (array)$attributes, (array)$toMany);
    }

    /**
     * @inheritdoc
     * @param string $index
     * @return bool
     * @throws AuthorizationExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws DBALException
     */
    public function remove(string $index): bool
    {
        $this->authorize(Rules::ACTION_EDIT_ROLE, Schema::TYPE, $index);

        return parent::remove($index);
    }

    /**
     * @inheritdoc
     * @return PaginatedDataInterface
     * @throws AuthorizationExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws DBALException
     */
    public function index(): PaginatedDataInterface
    {
        $this->authorize(Rules::ACTION_VIEW_ROLES, Schema::TYPE);

        return parent::index();
    }

    /**
     * @inheritdoc
     * @param string $index
     * @return mixed|null
     * @throws AuthorizationExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws DBALException
     */
    public function read(string $index)
    {
        $this->authorize(Rules::ACTION_VIEW_ROLES, Schema::TYPE, $index);

        return parent::read($index);
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
    public function readUsers(
        $index,
        iterable $relationshipFilters = null,
        iterable $relationshipSorts = null
    ): PaginatedDataInterface {
        $this->authorize(Rules::ACTION_VIEW_USERS, Schema::TYPE, $index);

        return $this->readRelationshipInt($index, Model::REL_USERS, $relationshipFilters, $relationshipSorts);
    }
}
