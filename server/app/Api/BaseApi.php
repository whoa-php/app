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

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Query\QueryBuilder;
use Exception;
use Whoa\Contracts\Authentication\AccountManagerInterface;
use Whoa\Contracts\Authorization\AuthorizationManagerInterface;
use Whoa\Contracts\Data\TimestampFields;
use Whoa\Contracts\Data\UuidFields;
use Whoa\Contracts\Exceptions\AuthorizationExceptionInterface;
use Whoa\Contracts\Passport\PassportAccountInterface;
use Whoa\Doctrine\Traits\UuidTypeTrait;
use Whoa\Flute\Adapters\ModelQueryBuilder;
use Whoa\Flute\Api\Crud;
use Whoa\Flute\Contracts\Models\PaginatedDataInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @package App
 * Here you can put common CRUD code.
 */
abstract class BaseApi extends Crud
{
    use UuidTypeTrait;

    /**
     * @param $index
     * @param string $name
     * @param iterable|null $relationshipFilters
     * @param iterable|null $relationshipSorts
     * @return PaginatedDataInterface
     */
    protected function readRelationshipInt(
        $index,
        string $name,
        iterable $relationshipFilters = null,
        iterable $relationshipSorts = null
    ): PaginatedDataInterface {
        return parent::readRelationship($index, $name, $relationshipFilters, $relationshipSorts);
    }

    /**
     * Authorize action for current user.
     * @param string $action
     * @param string|null $resourceType
     * @param string|int|null $resourceIdentity
     * @return void
     * @throws AuthorizationExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function authorize(string $action, string $resourceType = null, $resourceIdentity = null): void
    {
        /** @var AuthorizationManagerInterface $manager */
        $manager = $this->getContainer()->get(AuthorizationManagerInterface::class);
        $manager->authorize($action, $resourceType, $resourceIdentity);
    }

    /**
     * @inheritdoc
     * @throws DBALException
     */
    protected function builderSaveResourceOnCreate(ModelQueryBuilder $builder): ModelQueryBuilder
    {
        $addCreatedAt = $this->addCreatedAt(parent::builderSaveResourceOnCreate($builder));
        return $this->addUuid($addCreatedAt);
    }

    /**
     * @inheritdoc
     * @throws DBALException
     */
    protected function builderSaveResourceOnUpdate(ModelQueryBuilder $builder): ModelQueryBuilder
    {
        return $this->addUpdatedAt(parent::builderSaveResourceOnUpdate($builder));
    }

    /**
     * @inheritdoc
     * @throws DBALException
     */
    protected function builderSaveRelationshipOnCreate($relationshipName, ModelQueryBuilder $builder): ModelQueryBuilder
    {
        return $this->addCreatedAt(parent::builderSaveRelationshipOnCreate($relationshipName, $builder));
    }

    /**
     * @inheritdoc
     * @throws DBALException
     */
    protected function builderSaveRelationshipOnUpdate($relationshipName, ModelQueryBuilder $builder): ModelQueryBuilder
    {
        return $this->addCreatedAt(parent::builderSaveRelationshipOnUpdate($relationshipName, $builder));
    }

    /**
     * @param ModelQueryBuilder $builder
     * @return ModelQueryBuilder
     * @throws DBALException
     * @throws Exception
     */
    protected function addCreatedAt(ModelQueryBuilder $builder): ModelQueryBuilder
    {
        // `Doctrine` specifics: `setValue` works for inserts and `set` for updates
        $timestamp = $this->convertDateTimeToDbValue($builder, new DateTimeImmutable());
        $builder->setValue(TimestampFields::FIELD_CREATED_AT, $builder->createNamedParameter($timestamp));

        return $builder;
    }

    /**
     * @param ModelQueryBuilder $builder
     * @return ModelQueryBuilder
     * @throws DBALException
     * @throws Exception
     */
    protected function addUpdatedAt(ModelQueryBuilder $builder): ModelQueryBuilder
    {
        // `Doctrine` specifics: `setValue` works for inserts and `set` for updates
        $timestamp = $this->convertDateTimeToDbValue($builder, new DateTimeImmutable());
        $builder->set(TimestampFields::FIELD_UPDATED_AT, $builder->createNamedParameter($timestamp));

        return $builder;
    }

    /**
     * @param ModelQueryBuilder $builder
     * @return ModelQueryBuilder
     */
    protected function addUuid(ModelQueryBuilder $builder): ModelQueryBuilder
    {
        $builder->setValue(UuidFields::FIELD_UUID, $builder->createNamedParameter($this->uuid()));

        return $builder;
    }

    /**
     * @param QueryBuilder $builder
     * @param DateTimeInterface $dateTime
     * @return string
     * @throws DBALException
     */
    protected function convertDateTimeToDbValue(QueryBuilder $builder, DateTimeInterface $dateTime): string
    {
        $dbDateTimeFormat = $builder->getConnection()->getDatabasePlatform()->getDateTimeFormatString();
        return $dateTime->format($dbDateTimeFormat);
    }

    /**
     * The method assumes an account is signed in and therefore has less checks.
     * @return int|string|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getCurrentUserIdentity()
    {
        /** @var AccountManagerInterface $manager */
        /** @var PassportAccountInterface $account */
        $manager = $this->getContainer()->get(AccountManagerInterface::class);
        $account = $manager->getAccount();
        return $account->getUserIdentity();
    }

    /**
     * @param iterable $first
     * @param iterable $second
     * @return iterable
     */
    protected function addIterable(iterable $first, iterable $second): iterable
    {
        foreach ($first as $key => $value) {
            yield $key => $value;
        }
        foreach ($second as $key => $value) {
            yield $key => $value;
        }
    }
}
