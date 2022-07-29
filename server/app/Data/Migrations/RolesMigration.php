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

namespace App\Data\Migrations;

use App\Data\Models\Role as Model;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Whoa\Contracts\Data\MigrationInterface;
use Whoa\Contracts\Data\UuidFields;
use Whoa\Data\Migrations\MigrationTrait;

/**
 * @package App
 */
class RolesMigration implements MigrationInterface
{
    use MigrationTrait;

    /**
     * @inheritdoc
     * @throws DBALException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function migrate(): void
    {
        $this->createTable(Model::class, [
            $this->primaryInt(Model::FIELD_ID),
            $this->defaultUuid(),
            $this->string(Model::FIELD_NAME),
            $this->nullableText(Model::FIELD_DESCRIPTION),
            $this->timestamps(),
            $this->unique([UuidFields::FIELD_UUID]),
            $this->unique([Model::FIELD_NAME]),
            $this->unique([UuidFields::FIELD_UUID, Model::FIELD_NAME]),
        ]);
    }

    /**
     * @inheritdoc
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function rollback(): void
    {
        $this->dropTableIfExists(Model::class);
    }
}
